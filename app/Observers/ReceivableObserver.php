<?php

namespace App\Observers;

use App\Models\Receivable;
use App\Services\FinancePosting;

class ReceivableObserver
{
    public function created(Receivable $row): void
    {
        if (FinancePosting::$suspended) {
            return;
        }
        if (! FinancePosting::journalExists(Receivable::class, $row->id, 'accrual')) {
            FinancePosting::postReceivableAccrual($row);
        }
    }

    public function deleted(Receivable $row): void
    {
        FinancePosting::reverseSource(Receivable::class, $row->id);
    }
}
