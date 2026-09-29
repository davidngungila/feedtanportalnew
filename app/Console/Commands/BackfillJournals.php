<?php

namespace App\Console\Commands;

use App\Models\Deposit;
use App\Models\Investment;
use App\Models\InvestmentReturn;
use App\Models\Loan;
use App\Models\LoanRepayment;
use App\Models\Receivable;
use App\Models\SwfEntry;
use App\Services\FinancePosting;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class BackfillJournals extends Command
{
    protected $signature = 'finance:backfill-journals';
    protected $description = 'Post missing double-entry journals for existing operational records (idempotent).';

    public function handle(): int
    {
        $counts = ['loans' => 0, 'repayments' => 0, 'deposits' => 0, 'investments' => 0, 'returns' => 0, 'swf' => 0, 'receivables' => 0, 'skipped' => 0, 'failed' => 0];
        $fail = function (string $label, \Throwable $e) use (&$counts) {
            $counts['failed']++;
            $this->warn($label.': '.$e->getMessage());
        };

        foreach (Loan::all() as $loan) {
            if (! in_array($loan->status, \App\Observers\LoanObserver::DISBURSED, true)
                || FinancePosting::journalExists(Loan::class, $loan->id)) {
                $counts['skipped']++;
                continue;
            }
            try {
                DB::transaction(fn () => FinancePosting::postLoanDisbursement($loan));
                $counts['loans']++;
            } catch (\Throwable $e) {
                $fail('Loan #'.$loan->id, $e);
            }
        }

        foreach (LoanRepayment::all() as $r) {
            if (FinancePosting::journalExists(LoanRepayment::class, $r->id)) {
                $counts['skipped']++;
                continue;
            }
            try {
                DB::transaction(fn () => FinancePosting::postLoanRepayment($r));
                $counts['repayments']++;
            } catch (\Throwable $e) {
                $fail('Repayment #'.$r->id, $e);
            }
        }

        foreach (Deposit::all() as $d) {
            if (FinancePosting::journalExists(Deposit::class, $d->id)) {
                $counts['skipped']++;
                continue;
            }
            try {
                DB::transaction(fn () => FinancePosting::postDeposit($d));
                $counts['deposits']++;
            } catch (\Throwable $e) {
                $fail('Deposit #'.$d->id, $e);
            }
        }

        foreach (Investment::all() as $inv) {
            if (FinancePosting::journalExists(Investment::class, $inv->id)) {
                $counts['skipped']++;
                continue;
            }
            try {
                DB::transaction(fn () => FinancePosting::postInvestment($inv));
                $counts['investments']++;
            } catch (\Throwable $e) {
                $fail('Investment #'.$inv->id, $e);
            }
            if ($inv->status === 'withdrawn' && ! FinancePosting::journalExists(Investment::class, $inv->id, 'withdrawn')) {
                try {
                    DB::transaction(fn () => FinancePosting::postInvestmentWithdrawal($inv));
                    $counts['investments']++;
                } catch (\Throwable $e) {
                    $fail('Investment withdrawal #'.$inv->id, $e);
                }
            }
        }

        foreach (InvestmentReturn::all() as $ret) {
            if (FinancePosting::journalExists(InvestmentReturn::class, $ret->id)) {
                $counts['skipped']++;
                continue;
            }
            try {
                DB::transaction(fn () => FinancePosting::postInvestmentReturn($ret));
                $counts['returns']++;
            } catch (\Throwable $e) {
                $fail('Return #'.$ret->id, $e);
            }
        }

        foreach (SwfEntry::all() as $s) {
            if (FinancePosting::journalExists(SwfEntry::class, $s->id)) {
                $counts['skipped']++;
                continue;
            }
            try {
                DB::transaction(fn () => FinancePosting::postSwf($s));
                $counts['swf']++;
            } catch (\Throwable $e) {
                $fail('SWF #'.$s->id, $e);
            }
        }

        foreach (Receivable::all() as $row) {
            if (! FinancePosting::journalExists(Receivable::class, $row->id, 'accrual')) {
                try {
                    DB::transaction(fn () => FinancePosting::postReceivableAccrual($row));
                    $counts['receivables']++;
                } catch (\Throwable $e) {
                    $fail('Receivable #'.$row->id, $e);
                }
            } else {
                $counts['skipped']++;
            }
            if ((float) $row->paid_amount > 0 && ! FinancePosting::hasCollectionJournal(Receivable::class, $row->id)) {
                try {
                    DB::transaction(fn () => FinancePosting::postReceivableCollection($row, (float) $row->paid_amount, 'cash', 'collect-backfill'));
                    $counts['receivables']++;
                } catch (\Throwable $e) {
                    $fail('Receivable collection #'.$row->id, $e);
                }
            }
        }

        foreach ($counts as $k => $v) {
            $this->info(ucfirst($k).': '.$v);
        }

        $debit = \App\Models\JournalLine::sum('debit');
        $credit = \App\Models\JournalLine::sum('credit');
        $this->info('Ledger totals — Dr: '.$debit.' Cr: '.$credit.' '.(abs($debit - $credit) < 0.01 ? '(BALANCED)' : '(OUT OF BALANCE)'));

        return self::SUCCESS;
    }
}
