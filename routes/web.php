<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\ActivityController;
use App\Http\Controllers\BudgetController;
use App\Http\Controllers\CouponPaymentController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DepositController;
use App\Http\Controllers\DepositProductController;
use App\Http\Controllers\FinanceAccountController;
use App\Http\Controllers\FinanceController;
use App\Http\Controllers\FinanceReconciliationController;
use App\Http\Controllers\FinanceStatementController;
use App\Http\Controllers\FinanceTransactionController;
use App\Http\Controllers\FinanceTransferController;
use App\Http\Controllers\FinancialPeriodController;
use App\Http\Controllers\InvestmentController;
use App\Http\Controllers\InvestmentProductController;
use App\Http\Controllers\InvestmentReturnController;
use App\Http\Controllers\JournalController;
use App\Http\Controllers\JoinController;
use App\Http\Controllers\LoanApplicationController;
use App\Http\Controllers\LoanController;
use App\Http\Controllers\LoanProductController;
use App\Http\Controllers\MaturedPayoutController;
use App\Http\Controllers\ReceivableController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\MemberApplicationController;
use App\Http\Controllers\MemberAccessController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\MemberDocumentController;
use App\Http\Controllers\MemberGroupController;
use App\Http\Controllers\MemberPortalController;
use App\Http\Controllers\MemberTypeController;
use App\Http\Controllers\PayoutVerifyController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SavingsPlanController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\SwfController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (! auth()->check()) {
        return redirect()->route('login');
    }

    return redirect()->route(home_route_for(auth()->user()));
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::middleware(['auth', 'onboarded'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/dashboard', DashboardController::class)->name('dashboard');

    // Member onboarding: stepped registration for new accounts.
    Route::prefix('join')->name('join.')->group(function () {
        Route::get('/', [JoinController::class, 'index'])->name('index');
        Route::get('/step/{n}', [JoinController::class, 'step'])->name('step')->where('n', '[1-4]');
        Route::post('/step/{n}', [JoinController::class, 'saveStep'])->name('save')->where('n', '[1-4]');
        Route::post('/submit', [JoinController::class, 'submit'])->name('submit');
        Route::post('/restart', [JoinController::class, 'restart'])->name('restart');
        Route::get('/status', [JoinController::class, 'status'])->name('status');
    });

    Route::get('/account', [AccountController::class, 'index'])->name('account.index');
    Route::put('/account', [AccountController::class, 'update'])->name('account.update');
    Route::get('/account/security', [AccountController::class, 'security'])->name('account.security');
    Route::put('/account/security', [AccountController::class, 'updateSecurity'])->name('account.security.update');

    // Members — readable by all staff, writes restricted.
    Route::get('/members', [MemberController::class, 'index'])->name('members.index');
    Route::get('/member-documents', [MemberDocumentController::class, 'index'])->name('member-documents.index');

    Route::middleware('role:administrator,chairperson,secretary,accountant')->group(function () {
        Route::get('/members/create', [MemberController::class, 'create'])->name('members.create');
        Route::post('/members', [MemberController::class, 'store'])->name('members.store');
        Route::get('/members/{member}/edit', [MemberController::class, 'edit'])->name('members.edit');
        Route::put('/members/{member}', [MemberController::class, 'update'])->name('members.update');
        Route::delete('/members/{member}', [MemberController::class, 'destroy'])->name('members.destroy');

        Route::get('/member-documents/create', [MemberDocumentController::class, 'create'])->name('member-documents.create');
        Route::post('/member-documents', [MemberDocumentController::class, 'store'])->name('member-documents.store');
        Route::delete('/member-documents/{memberDocument}', [MemberDocumentController::class, 'destroy'])->name('member-documents.destroy');

        Route::get('/member-applications', [MemberApplicationController::class, 'index'])->name('member-applications.index');
        Route::get('/member-applications/create', [MemberApplicationController::class, 'create'])->name('member-applications.create');
        Route::post('/member-applications', [MemberApplicationController::class, 'store'])->name('member-applications.store');
        Route::put('/member-applications/{memberApplication}', [MemberApplicationController::class, 'update'])->name('member-applications.update');
        Route::post('/member-applications/{memberApplication}/approve', [MemberApplicationController::class, 'approve'])->name('member-applications.approve');
        Route::delete('/member-applications/{memberApplication}', [MemberApplicationController::class, 'destroy'])->name('member-applications.destroy');
    });

    Route::get('/members/{member}', [MemberController::class, 'show'])->name('members.show');

    // Admin catalog: types, groups, products, plans.
    Route::middleware('role:administrator,chairperson')->group(function () {
        Route::get('/member-types', [MemberTypeController::class, 'index'])->name('member-types.index');
        Route::get('/member-types/create', [MemberTypeController::class, 'create'])->name('member-types.create');
        Route::post('/member-types', [MemberTypeController::class, 'store'])->name('member-types.store');
        Route::get('/member-types/{memberType}/edit', [MemberTypeController::class, 'edit'])->name('member-types.edit');
        Route::put('/member-types/{memberType}', [MemberTypeController::class, 'update'])->name('member-types.update');
        Route::delete('/member-types/{memberType}', [MemberTypeController::class, 'destroy'])->name('member-types.destroy');

        Route::get('/member-groups', [MemberGroupController::class, 'index'])->name('member-groups.index');
        Route::get('/member-groups/create', [MemberGroupController::class, 'create'])->name('member-groups.create');
        Route::post('/member-groups', [MemberGroupController::class, 'store'])->name('member-groups.store');
        Route::get('/member-groups/{memberGroup}', [MemberGroupController::class, 'show'])->name('member-groups.show');
        Route::get('/member-groups/{memberGroup}/edit', [MemberGroupController::class, 'edit'])->name('member-groups.edit');
        Route::put('/member-groups/{memberGroup}', [MemberGroupController::class, 'update'])->name('member-groups.update');
        Route::delete('/member-groups/{memberGroup}', [MemberGroupController::class, 'destroy'])->name('member-groups.destroy');

        Route::get('/loan-products', [LoanProductController::class, 'index'])->name('loan-products.index');
        Route::get('/loan-products/create', [LoanProductController::class, 'create'])->name('loan-products.create');
        Route::post('/loan-products', [LoanProductController::class, 'store'])->name('loan-products.store');
        Route::get('/loan-products/{loanProduct}/edit', [LoanProductController::class, 'edit'])->name('loan-products.edit');
        Route::put('/loan-products/{loanProduct}', [LoanProductController::class, 'update'])->name('loan-products.update');
        Route::delete('/loan-products/{loanProduct}', [LoanProductController::class, 'destroy'])->name('loan-products.destroy');

        Route::get('/investment-products', [InvestmentProductController::class, 'index'])->name('investment-products.index');
        Route::get('/investment-products/create', [InvestmentProductController::class, 'create'])->name('investment-products.create');
        Route::post('/investment-products', [InvestmentProductController::class, 'store'])->name('investment-products.store');
        Route::get('/investment-products/{investmentProduct}/edit', [InvestmentProductController::class, 'edit'])->name('investment-products.edit');
        Route::put('/investment-products/{investmentProduct}', [InvestmentProductController::class, 'update'])->name('investment-products.update');
        Route::delete('/investment-products/{investmentProduct}', [InvestmentProductController::class, 'destroy'])->name('investment-products.destroy');

        Route::get('/deposit-products', [DepositProductController::class, 'index'])->name('deposit-products.index');
        Route::get('/deposit-products/create', [DepositProductController::class, 'create'])->name('deposit-products.create');
        Route::post('/deposit-products', [DepositProductController::class, 'store'])->name('deposit-products.store');
        Route::get('/deposit-products/{depositProduct}/edit', [DepositProductController::class, 'edit'])->name('deposit-products.edit');
        Route::put('/deposit-products/{depositProduct}', [DepositProductController::class, 'update'])->name('deposit-products.update');
        Route::delete('/deposit-products/{depositProduct}', [DepositProductController::class, 'destroy'])->name('deposit-products.destroy');

        Route::get('/savings-plans', [SavingsPlanController::class, 'index'])->name('savings-plans.index');
        Route::get('/savings-plans/create', [SavingsPlanController::class, 'create'])->name('savings-plans.create');
        Route::post('/savings-plans', [SavingsPlanController::class, 'store'])->name('savings-plans.store');
        Route::get('/savings-plans/{savingsPlan}/edit', [SavingsPlanController::class, 'edit'])->name('savings-plans.edit');
        Route::put('/savings-plans/{savingsPlan}', [SavingsPlanController::class, 'update'])->name('savings-plans.update');
        Route::delete('/savings-plans/{savingsPlan}', [SavingsPlanController::class, 'destroy'])->name('savings-plans.destroy');

        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
        Route::post('/users', [UserController::class, 'store'])->name('users.store');
        Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
        Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
        Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');

        Route::get('/roles', [RoleController::class, 'index'])->name('roles.index');
        Route::get('/roles/create', [RoleController::class, 'create'])->name('roles.create');
        Route::post('/roles', [RoleController::class, 'store'])->name('roles.store');
        Route::get('/roles/{role}/edit', [RoleController::class, 'edit'])->name('roles.edit');
        Route::put('/roles/{role}', [RoleController::class, 'update'])->name('roles.update');
        Route::delete('/roles/{role}', [RoleController::class, 'destroy'])->name('roles.destroy');

        Route::get('/permissions', [PermissionController::class, 'index'])->name('permissions.index');
        Route::put('/permissions', [PermissionController::class, 'update'])->name('permissions.update');

        Route::get('/activity', [ActivityController::class, 'activity'])->name('activity.index');
        Route::get('/access-logs', [ActivityController::class, 'access'])->name('access.index');

        Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
        Route::put('/settings', [SettingController::class, 'update'])->name('settings.update');
        Route::get('/settings/organization', [SettingController::class, 'organization'])->name('settings.organization');
        Route::put('/settings/organization', [SettingController::class, 'updateOrganization'])->name('settings.organization.update');
        Route::get('/settings/payment', [SettingController::class, 'payment'])->name('settings.payment');
        Route::put('/settings/payment', [SettingController::class, 'updatePayment'])->name('settings.payment.update');
        Route::get('/settings/notification', [SettingController::class, 'notification'])->name('settings.notification');
        Route::put('/settings/notification', [SettingController::class, 'updateNotification'])->name('settings.notification.update');
        Route::get('/settings/communication', [SettingController::class, 'communication'])->name('settings.communication');
        Route::put('/settings/communication', [SettingController::class, 'updateCommunication'])->name('settings.communication.update');
        Route::post('/settings/sms-test', [SettingController::class, 'smsTest'])->name('settings.sms.test');
        Route::get('/settings/system', [SettingController::class, 'system'])->name('settings.system');
        Route::put('/settings/system', [SettingController::class, 'updateSystem'])->name('settings.system.update');
    });

    // Loans — loan officer workspace.
    Route::middleware('role:administrator,chairperson,accountant,secretary,loan_officer')->group(function () {
        Route::get('/loans', [LoanController::class, 'index'])->name('loans.index');
        Route::get('/loans/create', [LoanController::class, 'create'])->name('loans.create');
        Route::post('/loans', [LoanController::class, 'store'])->name('loans.store');
        Route::get('/loans/{loan}', [LoanController::class, 'show'])->name('loans.show');
        Route::get('/loans/{loan}/edit', [LoanController::class, 'edit'])->name('loans.edit');
        Route::put('/loans/{loan}', [LoanController::class, 'update'])->name('loans.update');
        Route::post('/loans/{loan}/repay', [LoanController::class, 'repay'])->name('loans.repay');
        Route::delete('/loans/{loan}', [LoanController::class, 'destroy'])->name('loans.destroy');
        Route::delete('/repayments/{repayment}', [LoanController::class, 'destroyRepayment'])->name('repayments.destroy');

        Route::get('/loan-applications', [LoanApplicationController::class, 'index'])->name('loan-applications.index');
        Route::get('/loan-applications/create', [LoanApplicationController::class, 'create'])->name('loan-applications.create');
        Route::post('/loan-applications', [LoanApplicationController::class, 'store'])->name('loan-applications.store');
        Route::put('/loan-applications/{loanApplication}', [LoanApplicationController::class, 'update'])->name('loan-applications.update');
        Route::post('/loan-applications/{loanApplication}/approve', [LoanApplicationController::class, 'approve'])->name('loan-applications.approve');
        Route::delete('/loan-applications/{loanApplication}', [LoanApplicationController::class, 'destroy'])->name('loan-applications.destroy');
    });

    // Deposits — deposit officer workspace.
    Route::middleware('role:administrator,chairperson,accountant,secretary,deposit_officer')->group(function () {
        Route::get('/deposits', [DepositController::class, 'index'])->name('deposits.index');
        Route::get('/deposits/withdrawals', [DepositController::class, 'withdrawals'])->name('deposits.withdrawals');
        Route::get('/deposits/create', [DepositController::class, 'create'])->name('deposits.create');
        Route::post('/deposits', [DepositController::class, 'store'])->name('deposits.store');
        Route::get('/deposits/{deposit}/edit', [DepositController::class, 'edit'])->name('deposits.edit');
        Route::put('/deposits/{deposit}', [DepositController::class, 'update'])->name('deposits.update');
        Route::delete('/deposits/{deposit}', [DepositController::class, 'destroy'])->name('deposits.destroy');
    });

    // Investments — investment officer workspace.
    Route::middleware('role:administrator,chairperson,accountant,secretary,investment_officer')->group(function () {
        Route::get('/investments', [InvestmentController::class, 'index'])->name('investments.index');
        Route::get('/investments/active', [InvestmentController::class, 'active'])->name('investments.active');
        Route::get('/investments/matured', [InvestmentController::class, 'matured'])->name('investments.matured');
        Route::get('/investments/matured/import', [MaturedPayoutController::class, 'importForm'])->name('investments.matured.import');
        Route::get('/investments/matured/import/template', [MaturedPayoutController::class, 'template'])->name('investments.matured.template');
        Route::post('/investments/matured/import', [MaturedPayoutController::class, 'store'])->name('investments.matured.store');
        Route::post('/investments/matured/sms-bulk', [MaturedPayoutController::class, 'sendBulk'])->name('payouts.sms.bulk');
        Route::post('/payouts/{payout}/pay', [MaturedPayoutController::class, 'pay'])->name('payouts.pay');
        Route::delete('/payouts/bulk', [MaturedPayoutController::class, 'bulkDestroy'])->name('payouts.bulk.destroy');
        Route::get('/coupon-payments', [CouponPaymentController::class, 'index'])->name('coupon.index');
        Route::get('/coupon-payments/template', [CouponPaymentController::class, 'template'])->name('coupon.template');
        Route::get('/coupon-payments/export', [CouponPaymentController::class, 'export'])->name('coupon.export');
        Route::post('/coupon-payments', [CouponPaymentController::class, 'store'])->name('coupon.store');
        Route::post('/coupon-payments/sms-bulk', [CouponPaymentController::class, 'sendBulk'])->name('coupon.sms.bulk');
        Route::delete('/coupon-payments/bulk', [CouponPaymentController::class, 'bulkDestroy'])->name('coupon.bulk.destroy');
        Route::get('/coupon-payments/{payout}', [CouponPaymentController::class, 'show'])->name('coupon.show');
        Route::post('/coupon-payments/{payout}/sms', [CouponPaymentController::class, 'sendSms'])->name('coupon.sms.single');
        Route::post('/coupon-payments/{payout}/pay', [CouponPaymentController::class, 'pay'])->name('coupon.pay');
        Route::get('/payouts/{payout}', [MaturedPayoutController::class, 'show'])->name('payouts.show');
        Route::post('/payouts/{payout}/sms', [MaturedPayoutController::class, 'sendSms'])->name('payouts.sms.single');
        Route::get('/investments/create', [InvestmentController::class, 'create'])->name('investments.create');
        Route::post('/investments', [InvestmentController::class, 'store'])->name('investments.store');
        Route::get('/investments/member/{member}', [InvestmentController::class, 'member'])->name('investments.member');
        Route::get('/investments/{investment}', [InvestmentController::class, 'show'])->name('investments.show');
        Route::get('/investments/{investment}/edit', [InvestmentController::class, 'edit'])->name('investments.edit');
        Route::put('/investments/{investment}', [InvestmentController::class, 'update'])->name('investments.update');
        Route::delete('/investments/{investment}', [InvestmentController::class, 'destroy'])->name('investments.destroy');

        Route::get('/investment-returns', [InvestmentReturnController::class, 'index'])->name('investment-returns.index');
        Route::get('/investment-returns/create', [InvestmentReturnController::class, 'create'])->name('investment-returns.create');
        Route::post('/investment-returns', [InvestmentReturnController::class, 'store'])->name('investment-returns.store');
        Route::delete('/investment-returns/{investmentReturn}', [InvestmentReturnController::class, 'destroy'])->name('investment-returns.destroy');
    });

    // Finance — books workspace.
    Route::middleware('role:administrator,chairperson,accountant,secretary')->group(function () {
        Route::get('/finance', [FinanceController::class, 'overview'])->name('finance.overview');
        Route::get('/finance/cash-flow', [FinanceController::class, 'cashflow'])->name('finance.cashflow');

        Route::get('/finance/transactions', [FinanceTransactionController::class, 'transactions'])->name('finance.transactions');
        Route::get('/finance/income', [FinanceTransactionController::class, 'income'])->name('finance.income');
        Route::get('/finance/expenses', [FinanceTransactionController::class, 'expenses'])->name('finance.expenses');
        Route::get('/finance/fees', [FinanceTransactionController::class, 'fees'])->name('finance.fees');
        Route::get('/finance/interest', [FinanceTransactionController::class, 'interest'])->name('finance.interest');
        Route::get('/finance/commissions', [FinanceTransactionController::class, 'commissions'])->name('finance.commissions');
        Route::get('/finance/adjustments', [FinanceTransactionController::class, 'adjustments'])->name('finance.adjustments');
        Route::get('/finance/transactions/create', [FinanceTransactionController::class, 'create'])->name('finance.transactions.create');
        Route::post('/finance/transactions', [FinanceTransactionController::class, 'store'])->name('finance.transactions.store');
        Route::delete('/finance/transactions/{transaction}', [FinanceTransactionController::class, 'destroy'])->name('finance.transactions.destroy');

        Route::get('/finance/accounts', [FinanceAccountController::class, 'accounts'])->name('finance.accounts');
        Route::get('/finance/assets', [FinanceAccountController::class, 'assets'])->name('finance.assets');
        Route::get('/finance/liabilities', [FinanceAccountController::class, 'liabilities'])->name('finance.liabilities');
        Route::get('/finance/equity', [FinanceAccountController::class, 'equity'])->name('finance.equity');
        Route::get('/finance/chart', [FinanceAccountController::class, 'chart'])->name('finance.chart');
        Route::get('/finance/accounts/create', [FinanceAccountController::class, 'create'])->name('finance.accounts.create');
        Route::post('/finance/accounts', [FinanceAccountController::class, 'store'])->name('finance.accounts.store');
        Route::get('/finance/accounts/{account}/edit', [FinanceAccountController::class, 'edit'])->name('finance.accounts.edit');
        Route::put('/finance/accounts/{account}', [FinanceAccountController::class, 'update'])->name('finance.accounts.update');

        Route::get('/finance/transfers', [FinanceTransferController::class, 'index'])->name('finance.transfers');
        Route::get('/finance/transfers/create', [FinanceTransferController::class, 'create'])->name('finance.transfers.create');
        Route::post('/finance/transfers', [FinanceTransferController::class, 'store'])->name('finance.transfers.store');
        Route::delete('/finance/transfers/{transfer}', [FinanceTransferController::class, 'destroy'])->name('finance.transfers.destroy');

        Route::get('/finance/receivables', [ReceivableController::class, 'receivables'])->name('finance.receivables');
        Route::get('/finance/payables', [ReceivableController::class, 'payables'])->name('finance.payables');
        Route::get('/finance/receivables/create', [ReceivableController::class, 'create'])->name('finance.receivables.create');
        Route::post('/finance/receivables', [ReceivableController::class, 'store'])->name('finance.receivables.store');
        Route::post('/finance/receivables/{receivable}/collect', [ReceivableController::class, 'collect'])->name('finance.receivables.collect');
        Route::delete('/finance/receivables/{receivable}', [ReceivableController::class, 'destroy'])->name('finance.receivables.destroy');

        Route::get('/finance/journals', [JournalController::class, 'index'])->name('finance.journals');
        Route::get('/finance/journals/create', [JournalController::class, 'create'])->name('finance.journals.create');
        Route::post('/finance/journals', [JournalController::class, 'store'])->name('finance.journals.store');
        Route::get('/finance/journals/{journal}', [JournalController::class, 'show'])->name('finance.journals.show');
        Route::delete('/finance/journals/{journal}', [JournalController::class, 'destroy'])->name('finance.journals.destroy');
        Route::get('/finance/ledger', [JournalController::class, 'ledger'])->name('finance.ledger');

        Route::get('/finance/budgets', [BudgetController::class, 'index'])->name('finance.budgets');
        Route::get('/finance/budgets/create', [BudgetController::class, 'create'])->name('finance.budgets.create');
        Route::post('/finance/budgets', [BudgetController::class, 'store'])->name('finance.budgets.store');
        Route::delete('/finance/budgets/{budget}', [BudgetController::class, 'destroy'])->name('finance.budgets.destroy');

        Route::get('/finance/periods', [FinancialPeriodController::class, 'index'])->name('finance.periods');
        Route::get('/finance/periods/create', [FinancialPeriodController::class, 'create'])->name('finance.periods.create');
        Route::post('/finance/periods', [FinancialPeriodController::class, 'store'])->name('finance.periods.store');
        Route::post('/finance/periods/{period}/toggle', [FinancialPeriodController::class, 'toggle'])->name('finance.periods.toggle');
        Route::delete('/finance/periods/{period}', [FinancialPeriodController::class, 'destroy'])->name('finance.periods.destroy');

        Route::get('/finance/reconciliation', [FinanceReconciliationController::class, 'index'])->name('finance.reconciliation');
        Route::get('/finance/reconciliation/create', [FinanceReconciliationController::class, 'create'])->name('finance.reconciliation.create');
        Route::post('/finance/reconciliation', [FinanceReconciliationController::class, 'store'])->name('finance.reconciliation.store');
        Route::delete('/finance/reconciliation/{reconciliation}', [FinanceReconciliationController::class, 'destroy'])->name('finance.reconciliation.destroy');

        Route::get('/finance/statements', [FinanceStatementController::class, 'hub'])->name('finance.statements');
        Route::get('/finance/trial-balance', [FinanceStatementController::class, 'trialBalance'])->name('finance.trial-balance');
        Route::get('/finance/income-statement', [FinanceStatementController::class, 'income'])->name('finance.income-statement');
        Route::get('/finance/balance-sheet', [FinanceStatementController::class, 'balance'])->name('finance.balance-sheet');
        Route::get('/finance/cashflow-statement', [FinanceStatementController::class, 'cashflowStatement'])->name('finance.cashflow-statement');
        Route::get('/finance/reports', [FinanceStatementController::class, 'reports'])->name('finance.reports');
    });

    // SWF — swf officer workspace.
    Route::middleware('role:administrator,chairperson,accountant,secretary,swf_officer')->group(function () {
        Route::get('/swf', [SwfController::class, 'index'])->name('swf.index');
        Route::get('/swf/accounts', [SwfController::class, 'accounts'])->name('swf.accounts');
        Route::get('/swf/contributions', [SwfController::class, 'contributions'])->name('swf.contributions');
        Route::get('/swf/deductions', [SwfController::class, 'deductions'])->name('swf.deductions');
        Route::get('/swf/claims', [SwfController::class, 'claims'])->name('swf.claims');
        Route::get('/swf/statements', [SwfController::class, 'statements'])->name('swf.statements');
        Route::get('/swf/create', [SwfController::class, 'create'])->name('swf.create');
        Route::post('/swf', [SwfController::class, 'store'])->name('swf.store');
        Route::get('/swf/{entry}/edit', [SwfController::class, 'edit'])->name('swf.edit');
        Route::put('/swf/{entry}', [SwfController::class, 'update'])->name('swf.update');
        Route::delete('/swf/{entry}', [SwfController::class, 'destroy'])->name('swf.destroy');
    });

    Route::middleware('role:administrator,chairperson,secretary,accountant')->group(function () {
        Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
        Route::get('/reports/members', [ReportController::class, 'members'])->name('reports.members');
        Route::get('/reports/loans', [ReportController::class, 'loans'])->name('reports.loans');
        Route::get('/reports/savings', [ReportController::class, 'savings'])->name('reports.savings');
        Route::get('/reports/transactions', [ReportController::class, 'transactions'])->name('reports.transactions');

        // Member login access (auto-provision + reset).
        Route::post('/members/{member}/provision-login', [MemberAccessController::class, 'provision'])->name('members.provision-login');
        Route::post('/members/{member}/reset-login', [MemberAccessController::class, 'resetPassword'])->name('members.reset-login');
        Route::delete('/members/{member}/login', [MemberAccessController::class, 'destroy'])->name('members.destroy-login');
    });

    // Member self-service portal — own services only.
    Route::middleware('role:member')->prefix('portal')->name('portal.')->group(function () {
        Route::get('/', [MemberPortalController::class, 'home'])->name('home');
        Route::get('/loans', [MemberPortalController::class, 'loans'])->name('loans');
        Route::get('/loans/{loan}', [MemberPortalController::class, 'loanShow'])->name('loans.show');
        Route::post('/loans/{loan}/repay', [MemberPortalController::class, 'repayLoan'])->name('loans.repay');
        Route::get('/deposits', [MemberPortalController::class, 'deposits'])->name('deposits');
        Route::get('/deposits/create', [MemberPortalController::class, 'createDeposit'])->name('deposits.create');
        Route::post('/deposits', [MemberPortalController::class, 'storeDeposit'])->name('deposits.store');
        Route::get('/investments', [MemberPortalController::class, 'investments'])->name('investments');
        Route::get('/investments/create', [MemberPortalController::class, 'createInvestment'])->name('investments.create');
        Route::post('/investments', [MemberPortalController::class, 'storeInvestment'])->name('investments.store');
        Route::get('/swf', [MemberPortalController::class, 'swf'])->name('swf');
        Route::get('/swf/create', [MemberPortalController::class, 'createSwf'])->name('swf.create');
        Route::post('/swf', [MemberPortalController::class, 'storeSwf'])->name('swf.store');
        Route::get('/statements', [MemberPortalController::class, 'statements'])->name('statements');
        Route::get('/profile', [MemberPortalController::class, 'profile'])->name('profile');
        Route::put('/profile', [MemberPortalController::class, 'updateProfile'])->name('profile.update');
        Route::get('/loan-applications', [MemberPortalController::class, 'loanApplications'])->name('loan-applications');
        Route::get('/loan-applications/create', [MemberPortalController::class, 'createLoanApplication'])->name('loan-applications.create');
        Route::post('/loan-applications', [MemberPortalController::class, 'storeLoanApplication'])->name('loan-applications.store');
        Route::delete('/loan-applications/{loanApplication}', [MemberPortalController::class, 'cancelLoanApplication'])->name('loan-applications.cancel');
    });
});

// Public payout verification (no login). Registered last so staff routes win.
Route::get('/verify/{payout}', [PayoutVerifyController::class, 'show'])->name('verify.show');
Route::post('/verify/{payout}', [PayoutVerifyController::class, 'confirm'])->name('verify.confirm');
Route::post('/verify/{payout}/reject', [PayoutVerifyController::class, 'reject'])->name('verify.reject');
Route::get('/{code}', [PayoutVerifyController::class, 'resolve'])->name('verify.short')->where('code', '[A-Za-z0-9]{6}');
