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

    protected static function makeEntry(string $description, string $date, ?string $sourceType = null, mixed $sourceId = null): JournalEntry
    {
        return JournalEntry::create([
            'reference' => self::reference('JE'),
            'entry_date' => $date,
            'description' => $description,
            'status' => 'posted',
            'source_type' => $sourceType,
            'source_id' => $sourceId,
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

    public static function cashAccountIds(): array
    {
        return FinanceAccount::whereIn('code', ['1000', '1100'])->where('status', 'active')->pluck('id')->all();
    }

    public static function accountBalance(int $accountId, ?string $to = null): float
    {
        $account = FinanceAccount::findOrFail($accountId);

        return $account->balance(null, $to);
    }
}
