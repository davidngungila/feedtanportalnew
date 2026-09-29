<?php

namespace App\Observers;

use App\Models\Investment;
use App\Services\FinancePosting;

class InvestmentObserver
{
    public function created(Investment $investment): void
    {
        if (FinancePosting::$suspended) {
            return;
        }
        FinancePosting::postInvestment($investment);
    }

    public function updated(Investment $investment): void
    {
        if (FinancePosting::$suspended) {
            return;
        }
        if ($investment->wasChanged('amount')) {
            FinancePosting::reverseSource(Investment::class, $investment->id);
            FinancePosting::postInvestment($investment);
        }
        if ($investment->wasChanged('status')) {
            $wasOut = $investment->getOriginal('status') === 'withdrawn';
            $isOut = $investment->status === 'withdrawn';
            if ($isOut && ! $wasOut && ! FinancePosting::journalExists(Investment::class, $investment->id, 'withdrawn')) {
                FinancePosting::postInvestmentWithdrawal($investment);
            } elseif ($wasOut && ! $isOut) {
                FinancePosting::reverseSource(Investment::class, $investment->id, 'withdrawn');
            }
        }
    }

    public function deleted(Investment $investment): void
    {
        FinancePosting::reverseSource(Investment::class, $investment->id);
    }
}
