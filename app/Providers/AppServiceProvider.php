<?php

namespace App\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Blade::directive('money', fn (string $expression) => "<?php echo money($expression); ?>");

        \App\Models\Loan::observe(\App\Observers\LoanObserver::class);
        \App\Models\LoanRepayment::observe(\App\Observers\LoanRepaymentObserver::class);
        \App\Models\Deposit::observe(\App\Observers\DepositObserver::class);
        \App\Models\Investment::observe(\App\Observers\InvestmentObserver::class);
        \App\Models\InvestmentReturn::observe(\App\Observers\InvestmentReturnObserver::class);
        \App\Models\SwfEntry::observe(\App\Observers\SwfEntryObserver::class);
        \App\Models\Receivable::observe(\App\Observers\ReceivableObserver::class);
    }
}
