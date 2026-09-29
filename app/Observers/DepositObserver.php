<?php

namespace App\Observers;

use App\Models\Deposit;
use App\Services\FinancePosting;

class DepositObserver
{
    public function created(Deposit $deposit): void
    {
        if (FinancePosting::$suspended) {
            return;
        }
        FinancePosting::postDeposit($deposit);
    }

    public function updated(Deposit $deposit): void
    {
        if (FinancePosting::$suspended) {
            return;
        }
        if ($deposit->wasChanged(['amount', 'type', 'transacted_at', 'method'])) {
            FinancePosting::reverseSource(Deposit::class, $deposit->id);
            FinancePosting::postDeposit($deposit);
        }
    }

    public function deleted(Deposit $deposit): void
    {
        FinancePosting::reverseSource(Deposit::class, $deposit->id);
    }
}
