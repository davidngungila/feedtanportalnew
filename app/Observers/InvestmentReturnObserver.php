<?php

namespace App\Observers;

use App\Models\InvestmentReturn;
use App\Services\FinancePosting;

class InvestmentReturnObserver
{
    public function created(InvestmentReturn $ret): void
    {
        if (FinancePosting::$suspended) {
            return;
        }
        FinancePosting::postInvestmentReturn($ret);
    }

    public function deleted(InvestmentReturn $ret): void
    {
        FinancePosting::reverseSource(InvestmentReturn::class, $ret->id);
    }
}
