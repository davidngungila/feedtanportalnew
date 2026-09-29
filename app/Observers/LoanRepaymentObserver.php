<?php

namespace App\Observers;

use App\Models\LoanRepayment;
use App\Services\FinancePosting;

class LoanRepaymentObserver
{
    public function created(LoanRepayment $repayment): void
    {
        if (FinancePosting::$suspended) {
            return;
        }
        FinancePosting::postLoanRepayment($repayment);
    }

    public function deleted(LoanRepayment $repayment): void
    {
        FinancePosting::reverseSource(LoanRepayment::class, $repayment->id);
    }
}
