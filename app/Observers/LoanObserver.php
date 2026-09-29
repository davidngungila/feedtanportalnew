<?php

namespace App\Observers;

use App\Models\Loan;
use App\Services\FinancePosting;

class LoanObserver
{
    public const DISBURSED = ['active', 'overdue', 'paid', 'defaulted'];

    public function created(Loan $loan): void
    {
        if (FinancePosting::$suspended) {
            return;
        }
        if (in_array($loan->status, self::DISBURSED, true)) {
            FinancePosting::postLoanDisbursement($loan);
        }
    }

    public function updated(Loan $loan): void
    {
        if (FinancePosting::$suspended) {
            return;
        }
        if (! $loan->wasChanged('status')) {
            return;
        }
        $wasOut = in_array($loan->getOriginal('status'), self::DISBURSED, true);
        $isOut = in_array($loan->status, self::DISBURSED, true);

        if ($isOut && ! $wasOut && ! FinancePosting::journalExists(Loan::class, $loan->id)) {
            FinancePosting::postLoanDisbursement($loan);
        } elseif ($wasOut && ! $isOut) {
            FinancePosting::reverseSource(Loan::class, $loan->id);
        }
    }

    public function deleting(Loan $loan): void
    {
        // Repayments cascade at DB level (no events), so reverse their journals here too.
        foreach ($loan->repayments()->pluck('id') as $repaymentId) {
            FinancePosting::reverseSource(\App\Models\LoanRepayment::class, $repaymentId);
        }
        FinancePosting::reverseSource(Loan::class, $loan->id);
    }
}
