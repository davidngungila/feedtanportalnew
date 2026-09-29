<?php

namespace App\Services;

use App\Models\FinanceAccount;
use App\Models\FinancialPeriod;
use App\Models\JournalEntry;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class FinancePosting
{
    public const INCOME_MAP = [
        'fee' => '4100',
        'charge' => '4200',
        'interest' => '4300',
        'commission' => '4400',
        'operating' => '4900',
        'adjustment' => '4900',
        'other' => '4900',
    ];

    public const EXPENSE_CODE = '5100';

    /**
     * When true, model observers skip auto-posting. Used for records created
     * inside payout settlements, which post one compound journal instead.
     */
    public static bool $suspended = false;

    public static function withoutPosting(callable $fn): mixed
    {
        static::$suspended = true;
        try {
            return $fn();
        } finally {
            static::$suspended = false;
        }
    }

    public static function findAccount(string $code): FinanceAccount
    {
        return FinanceAccount::where('code', $code)->firstOrFail();
    }

    /** Cash/bank account implied by a collection method (cash|mobile|bank). */
    public static function cashAccountForMethod(?string $method): FinanceAccount
    {
        $code = match (strtolower((string) $method)) {
            'bank' => '1100',
            'mobile' => '1110',
            default => '1000',
        };

        return FinanceAccount::where('code', $code)->first()
            ?? FinanceAccount::where('code', '1000')->firstOrFail();
    }

    /** First posted journal linked to a source record, if any. Null tag matches any tag. */
    public static function journalFor(string $sourceType, mixed $sourceId, ?string $sourceTag = null): ?JournalEntry
    {
        $q = JournalEntry::where('source_type', $sourceType)->where('source_id', $sourceId);
        if ($sourceTag !== null) {
            $q->where('source_tag', $sourceTag);
        }

        return $q->oldest()->first();
    }

    public static function journalExists(string $sourceType, mixed $sourceId, ?string $sourceTag = null): bool
    {
        $q = JournalEntry::where('source_type', $sourceType)->where('source_id', $sourceId);
        if ($sourceTag !== null) {
            $q->where('source_tag', $sourceTag);
        }

        return $q->exists();
    }

    public static function hasCollectionJournal(string $sourceType, mixed $sourceId): bool
    {
        return JournalEntry::where('source_type', $sourceType)
            ->where('source_id', $sourceId)
            ->where('source_tag', 'like', 'collect%')->exists();
    }

    /** Delete journals linked to a source record (all tags, or one tag when given). */
    public static function reverseSource(string $sourceType, mixed $sourceId, ?string $sourceTag = null): void
    {
        $q = JournalEntry::where('source_type', $sourceType)->where('source_id', $sourceId);
        if ($sourceTag !== null) {
            $q->where('source_tag', $sourceTag);
        }

        foreach ($q->get() as $entry) {
            $entry->lines()->delete();
            $entry->delete();
        }
    }

    protected static function addLine(JournalEntry $entry, string $accountCode, float $debit, float $credit, ?string $narration = null): void
    {
        if (abs($debit) < 0.005 && abs($credit) < 0.005) {
            return;
        }
        $entry->lines()->create([
            'finance_account_id' => self::findAccount($accountCode)->id,
            'debit' => round($debit, 2),
            'credit' => round($credit, 2),
            'narration' => $narration,
        ]);
    }

    /**
     * Post a fully-specified compound journal. Throws unless balanced.
     *
     * @param  array  $legs  [['account' => code, 'debit' => x, 'credit' => y, 'narration' => ?string]]
     */
    public static function postCompound(string $description, string $date, string $sourceType, mixed $sourceId, array $legs, ?string $sourceTag = null): JournalEntry
    {
        return DB::transaction(function () use ($description, $date, $sourceType, $sourceId, $legs, $sourceTag) {
            self::assertOpenPeriod($date);

            $debit = round(collect($legs)->sum('debit'), 2);
            $credit = round(collect($legs)->sum('credit'), 2);

            if ($debit <= 0 || abs($debit - $credit) > 0.01) {
                throw ValidationException::withMessages([
                    'journal' => "Unbalanced compound entry (Dr {$debit} vs Cr {$credit}).",
                ]);
            }

            $entry = self::makeEntry($description, $date, $sourceType, $sourceId, $sourceTag);

            foreach ($legs as $leg) {
                self::addLine($entry, $leg['account'], (float) ($leg['debit'] ?? 0), (float) ($leg['credit'] ?? 0), $leg['narration'] ?? null);
            }

            return $entry;
        });
    }

    /**
     * Loan disbursement: Dr Loans Receivable (principal + interest),
     * Cr Cash (principal), Cr Interest Income (interest).
     * Keeps the 1200 balance equal to the loan outstanding.
     */
    public static function postLoanDisbursement(\App\Models\Loan $loan): JournalEntry
    {
        $loan->loadMissing('member');

        return self::postCompound(
            'Loan disbursed '.$loan->loan_no.' to '.($loan->member->name ?? 'member'),
            $loan->disbursed_at?->toDateString() ?? now()->toDateString(),
            \App\Models\Loan::class,
            $loan->id,
            [
                ['account' => '1200', 'debit' => (float) $loan->total_payable, 'credit' => 0, 'narration' => $loan->loan_no.' receivable'],
                ['account' => '1000', 'debit' => 0, 'credit' => (float) $loan->principal, 'narration' => $loan->loan_no.' cash out'],
                ['account' => '4300', 'debit' => 0, 'credit' => (float) $loan->interest_amount, 'narration' => $loan->loan_no.' interest'],
            ]
        );
    }

    /** Loan repayment: Dr Cash (by method), Cr Loans Receivable. */
    public static function postLoanRepayment(\App\Models\LoanRepayment $repayment): JournalEntry
    {
        $repayment->loadMissing('loan.member');
        $cash = self::cashAccountForMethod($repayment->method);

        return self::postCompound(
            'Loan repayment '.$repayment->receipt_no.' ('.$repayment->loan->loan_no.')',
            $repayment->paid_at?->toDateString() ?? now()->toDateString(),
            \App\Models\LoanRepayment::class,
            $repayment->id,
            [
                ['account' => $cash->code, 'debit' => (float) $repayment->amount, 'credit' => 0, 'narration' => $repayment->receipt_no.' via '.$cash->name],
                ['account' => '1200', 'debit' => 0, 'credit' => (float) $repayment->amount, 'narration' => $repayment->loan->loan_no.' recovery'],
            ]
        );
    }

    /** Deposit: Dr Cash, Cr Member Deposits Liability. Withdrawal mirrors it. */
    public static function postDeposit(\App\Models\Deposit $deposit): JournalEntry
    {
        $deposit->loadMissing('member');
        $cash = self::cashAccountForMethod($deposit->method);
        $isIn = $deposit->type === 'deposit';

        return self::postCompound(
            ($isIn ? 'Deposit ' : 'Withdrawal ').$deposit->receipt_no.' ('.($deposit->member->name ?? 'member').')',
            $deposit->transacted_at?->toDateString() ?? now()->toDateString(),
            \App\Models\Deposit::class,
            $deposit->id,
            $isIn ? [
                ['account' => $cash->code, 'debit' => (float) $deposit->amount, 'credit' => 0, 'narration' => $deposit->receipt_no.' via '.$cash->name],
                ['account' => '2000', 'debit' => 0, 'credit' => (float) $deposit->amount, 'narration' => $deposit->receipt_no.' savings liability'],
            ] : [
                ['account' => '2000', 'debit' => (float) $deposit->amount, 'credit' => 0, 'narration' => $deposit->receipt_no.' savings paid out'],
                ['account' => $cash->code, 'debit' => 0, 'credit' => (float) $deposit->amount, 'narration' => $deposit->receipt_no.' via '.$cash->name],
            ]
        );
    }

    /** Investment placement: Dr Cash, Cr Member Investments Liability. */
    public static function postInvestment(\App\Models\Investment $investment): JournalEntry
    {
        $investment->loadMissing('member');

        return self::postCompound(
            'Investment '.$investment->investment_no.' ('.($investment->member->name ?? 'member').')',
            $investment->start_date?->toDateString() ?? now()->toDateString(),
            \App\Models\Investment::class,
            $investment->id,
            [
                ['account' => '1000', 'debit' => (float) $investment->amount, 'credit' => 0, 'narration' => $investment->investment_no.' cash in'],
                ['account' => '2300', 'debit' => 0, 'credit' => (float) $investment->amount, 'narration' => $investment->investment_no.' owed to member'],
            ]
        );
    }

    /** Investment withdrawal: Dr Investments Liability, Cr Cash. */
    public static function postInvestmentWithdrawal(\App\Models\Investment $investment): JournalEntry
    {
        $investment->loadMissing('member');

        return self::postCompound(
            'Investment withdrawn '.$investment->investment_no.' ('.($investment->member->name ?? 'member').')',
            now()->toDateString(),
            \App\Models\Investment::class,
            $investment->id,
            [
                ['account' => '2300', 'debit' => (float) $investment->amount, 'credit' => 0, 'narration' => $investment->investment_no.' liability settled'],
                ['account' => '1000', 'debit' => 0, 'credit' => (float) $investment->amount, 'narration' => $investment->investment_no.' cash out'],
            ],
            'withdrawn'
        );
    }

    /** Investment return paid: Dr Return Expense, Cr Cash. */
    public static function postInvestmentReturn(\App\Models\InvestmentReturn $ret): JournalEntry
    {
        $ret->loadMissing('investment.member');

        return self::postCompound(
            'Investment return for '.($ret->investment->investment_no ?? '#'.$ret->investment_id),
            $ret->paid_at?->toDateString() ?? now()->toDateString(),
            \App\Models\InvestmentReturn::class,
            $ret->id,
            [
                ['account' => '5300', 'debit' => (float) $ret->amount, 'credit' => 0, 'narration' => $ret->notes],
                ['account' => '1000', 'debit' => 0, 'credit' => (float) $ret->amount, 'narration' => $ret->notes],
            ]
        );
    }

    /**
     * SWF: contribution Dr Cash Cr SWF Liability;
     * deduction/payout/claim Dr SWF Liability Cr Cash.
     */
    public static function postSwf(\App\Models\SwfEntry $entry): JournalEntry
    {
        $entry->loadMissing('member');
        $cash = self::cashAccountForMethod($entry->method);
        $isIn = $entry->type === 'contribution';

        return self::postCompound(
            'SWF '.ucfirst($entry->type).' '.$entry->receipt_no.' ('.($entry->member->name ?? 'member').')',
            $entry->transacted_at?->toDateString() ?? now()->toDateString(),
            \App\Models\SwfEntry::class,
            $entry->id,
            $isIn ? [
                ['account' => $cash->code, 'debit' => (float) $entry->amount, 'credit' => 0, 'narration' => $entry->receipt_no.' via '.$cash->name],
                ['account' => '2100', 'debit' => 0, 'credit' => (float) $entry->amount, 'narration' => $entry->receipt_no.' SWF liability'],
            ] : [
                ['account' => '2100', 'debit' => (float) $entry->amount, 'credit' => 0, 'narration' => $entry->receipt_no.' SWF '.$entry->type],
                ['account' => $cash->code, 'debit' => 0, 'credit' => (float) $entry->amount, 'narration' => $entry->receipt_no.' via '.$cash->name],
            ]
        );
    }

    /**
     * Receivable raised: Dr Receivables Control, Cr Other Income.
     * Payable raised: Dr Operating Expenses, Cr Payables Control.
     */
    public static function postReceivableAccrual(\App\Models\Receivable $row): JournalEntry
    {
        $isReceivable = $row->kind === 'receivable';

        return self::postCompound(
            ucfirst($row->kind).' raised '.$row->reference.' ('.$row->party_name.')',
            now()->toDateString(),
            \App\Models\Receivable::class,
            $row->id,
            $isReceivable ? [
                ['account' => '1300', 'debit' => (float) $row->amount, 'credit' => 0, 'narration' => $row->reference],
                ['account' => '4900', 'debit' => 0, 'credit' => (float) $row->amount, 'narration' => $row->reference],
            ] : [
                ['account' => '5100', 'debit' => (float) $row->amount, 'credit' => 0, 'narration' => $row->reference],
                ['account' => '2200', 'debit' => 0, 'credit' => (float) $row->amount, 'narration' => $row->reference],
            ],
            'accrual'
        );
    }

    /**
     * Receivable collected: Dr Cash, Cr Receivables Control.
     * Payable paid: Dr Payables Control, Cr Cash.
     */
    public static function postReceivableCollection(\App\Models\Receivable $row, float $amount, ?string $method = null, ?string $tag = null): JournalEntry
    {
        $cash = self::cashAccountForMethod($method);
        $isReceivable = $row->kind === 'receivable';

        return self::postCompound(
            ucfirst($row->kind).' '.($isReceivable ? 'collected ' : 'paid ').$row->reference,
            now()->toDateString(),
            \App\Models\Receivable::class,
            $row->id,
            $isReceivable ? [
                ['account' => $cash->code, 'debit' => $amount, 'credit' => 0, 'narration' => $row->reference.' via '.$cash->name],
                ['account' => '1300', 'debit' => 0, 'credit' => $amount, 'narration' => $row->reference],
            ] : [
                ['account' => '2200', 'debit' => $amount, 'credit' => 0, 'narration' => $row->reference],
                ['account' => $cash->code, 'debit' => 0, 'credit' => $amount, 'narration' => $row->reference.' via '.$cash->name],
            ],
            $tag ?? 'collect-'.now()->format('YmdHis').'-'.random_int(100, 999)
        );
    }

    public static function assertOpenPeriod(string $date): void
    {
        $closed = FinancialPeriod::where('status', 'closed')->get();
        foreach ($closed as $period) {
            if ($period->contains($date)) {
                throw ValidationException::withMessages([
                    'date' => "Date falls in closed period {$period->name}.",
                ]);
            }
        }
    }

    public static function reference(string $prefix): string
    {
        return $prefix.'-'.now()->format('YmdHis').'-'.random_int(100, 999);
    }

    protected static function makeEntry(string $description, string $date, ?string $sourceType = null, mixed $sourceId = null, ?string $sourceTag = null): JournalEntry
    {
        return JournalEntry::create([
            'reference' => self::reference('JE'),
            'entry_date' => $date,
            'description' => $description,
            'status' => 'posted',
            'source_type' => $sourceType,
            'source_id' => $sourceId,
            'source_tag' => $sourceTag,
            'created_by' => auth()->id(),
        ]);
    }

    public static function postTransaction(\App\Models\FinanceTransaction $tx): JournalEntry
    {
        return DB::transaction(function () use ($tx) {
            $cash = $tx->account;
            $counterCode = $tx->type === 'income'
                ? (self::INCOME_MAP[$tx->category] ?? '4900')
                : self::EXPENSE_CODE;
            $counter = FinanceAccount::where('code', $counterCode)->firstOrFail();

            $entry = self::makeEntry(
                ($tx->type === 'income' ? 'Income: ' : 'Expense: ').($tx->description ?: $tx->category.' '.$tx->reference),
                $tx->transacted_at->toDateString(),
                FinanceTransaction::class,
                $tx->id
            );

            if ($tx->type === 'income') {
                $entry->lines()->create(['finance_account_id' => $cash->id, 'debit' => $tx->amount, 'credit' => 0, 'narration' => $tx->description]);
                $entry->lines()->create(['finance_account_id' => $counter->id, 'debit' => 0, 'credit' => $tx->amount, 'narration' => $tx->description]);
            } else {
                $entry->lines()->create(['finance_account_id' => $counter->id, 'debit' => $tx->amount, 'credit' => 0, 'narration' => $tx->categoryLabel().' — '.$tx->description]);
                $entry->lines()->create(['finance_account_id' => $cash->id, 'debit' => 0, 'credit' => $tx->amount, 'narration' => $tx->description]);
            }

            $tx->update(['journal_entry_id' => $entry->id]);

            return $entry;
        });
    }

    public static function postTransfer(\App\Models\FinanceTransfer $transfer): JournalEntry
    {
        return DB::transaction(function () use ($transfer) {
            $entry = self::makeEntry(
                'Transfer: '.$transfer->fromAccount->name.' → '.$transfer->toAccount->name,
                $transfer->transferred_at->toDateString(),
                \App\Models\FinanceTransfer::class,
                $transfer->id
            );

            $entry->lines()->create(['finance_account_id' => $transfer->to_account_id, 'debit' => $transfer->amount, 'credit' => 0, 'narration' => $transfer->notes]);
            $entry->lines()->create(['finance_account_id' => $transfer->from_account_id, 'debit' => 0, 'credit' => $transfer->amount, 'narration' => $transfer->notes]);

            $transfer->update(['journal_entry_id' => $entry->id]);

            return $entry;
        });
    }

    /**
     * Settle a coupon/matured payout with one compound journal.
     * Dr Member Investments Liability (gross); Cr loan recovery, SWF,
     * fee/other income, savings liability and cash per the split.
     * Reinvested portions stay inside the liability (no legs).
     */
    public static function postPayoutSettlement(\App\Models\InvestmentPayout $payout, array $parts, ?string $cashMethod = null): JournalEntry
    {
        $gross = round((float) ($parts['gross'] ?? 0), 2);
        $cashCode = match (strtolower((string) $cashMethod)) {
            'bank' => '1100',
            'mpesa', 'tigo', 'mixx', 'mobile' => '1110',
            default => '1000',
        };

        $legs = [
            ['account' => '2300', 'debit' => $gross, 'credit' => 0, 'narration' => 'Payout '.$payout->verify_code.' settled'],
        ];
        $add = function (string $account, float $amount, string $narration) use (&$legs) {
            if ($amount > 0.004) {
                $legs[] = ['account' => $account, 'debit' => 0, 'credit' => round($amount, 2), 'narration' => $narration];
            }
        };

        $add('1200', (float) ($parts['loan'] ?? 0), 'Loan recovery '.$payout->verify_code);
        $add('2100', (float) ($parts['swf'] ?? 0), 'SWF '.$payout->verify_code);
        $add('4100', (float) ($parts['fee'] ?? 0), 'Fee income '.$payout->verify_code);
        $add('4900', (float) ($parts['other'] ?? 0) + (float) ($parts['shares'] ?? 0), 'Other income '.$payout->verify_code);
        $add('2000', (float) ($parts['savings'] ?? 0), 'Savings kept '.$payout->verify_code);

        $credited = round(collect($legs)->sum('credit'), 2);
        $cash = round(((float) ($parts['cash'] ?? 0)) + ($gross - $credited - (float) ($parts['cash'] ?? 0)), 2);
        if (abs($gross - $credited - (float) ($parts['cash'] ?? 0)) >= 1) {
            throw ValidationException::withMessages([
                'payout' => "Payout split does not add up to gross {$gross}.",
            ]);
        }
        $add($cashCode, $cash, 'Cash paid '.$payout->verify_code);

        return self::postCompound(
            'Payout '.$payout->verify_code.' settled ('.$payout->kind.')',
            now()->toDateString(),
            \App\Models\InvestmentPayout::class,
            $payout->id,
            $legs,
            'settlement'
        );
    }

    public static function cashAccountIds(): array
    {
        return FinanceAccount::whereIn('code', ['1000', '1100', '1110'])->where('status', 'active')->pluck('id')->all();
    }

    public static function accountBalance(int $accountId, ?string $to = null): float
    {
        $account = FinanceAccount::findOrFail($accountId);

        return $account->balance(null, $to);
    }
}
