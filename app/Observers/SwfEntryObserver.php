<?php

namespace App\Observers;

use App\Models\SwfEntry;
use App\Services\FinancePosting;

class SwfEntryObserver
{
    public function created(SwfEntry $entry): void
    {
        if (FinancePosting::$suspended) {
            return;
        }
        FinancePosting::postSwf($entry);
    }

    public function updated(SwfEntry $entry): void
    {
        if (FinancePosting::$suspended) {
            return;
        }
        if ($entry->wasChanged(['amount', 'type', 'transacted_at', 'method'])) {
            FinancePosting::reverseSource(SwfEntry::class, $entry->id);
            FinancePosting::postSwf($entry);
        }
    }

    public function deleted(SwfEntry $entry): void
    {
        FinancePosting::reverseSource(SwfEntry::class, $entry->id);
    }
}
