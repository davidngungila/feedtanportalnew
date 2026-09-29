<?php

namespace App\Http\Controllers;

use App\Models\Deposit;
use App\Models\Investment;
use App\Models\Loan;
use App\Models\LoanApplication;
use App\Models\LoanRepayment;
use App\Models\Member;
use App\Models\MemberApplication;
use App\Models\SwfEntry;

class DashboardController extends Controller
{
    public function __invoke()
    {
        $user = auth()->user();

        if (function_exists('is_applicant_incomplete') && is_applicant_incomplete($user)) {
            return redirect()->route('join.index');
        }
        $roles = $user && method_exists($user, 'roleSlugs') ? $user->roleSlugs() : [$user->role ?? ''];

        $isAdmin = in_array('administrator', $roles, true) || in_array('admin', $roles, true);
        $isChair = $isAdmin || in_array('chairperson', $roles, true);
        $isAccount = $isChair || in_array('accountant', $roles, true) || in_array('secretary', $roles, true);

        $canLoans = $isAccount || in_array('loan_officer', $roles, true);
        $canDeposits = $isAccount || in_array('deposit_officer', $roles, true);
        $canInvest = $isAccount || in_array('investment_officer', $roles, true);
        $canSwf = $isAccount || in_array('swf_officer', $roles, true);
        $canMembers = $isAccount || $canLoans || $canDeposits || $canInvest || $canSwf;
        $canFinance = $isAccount;

        // Member portal: show own balances only.
        $ownMember = null;
        if (in_array('member', $roles, true)) {
            $ownMember = $user->member
                ?? ($user->email ? Member::where('email', $user->email)->first() : null);
        }

        $memberCount = Member::count();
        $activeMembers = Member::where('status', 'active')->count();
        $pendingMemberApps = MemberApplication::where('status', 'pending')->count();

        $totalSavingsIn = (float) Deposit::where('type', 'deposit')->sum('amount');
        $totalSavingsOut = (float) Deposit::where('type', 'withdrawal')->sum('amount');

        $loanDisbursed = (float) Loan::whereIn('status', ['active', 'paid', 'overdue'])->sum('principal');
        $loanRepaid = (float) LoanRepayment::sum('amount');
        $activeLoans = Loan::whereIn('status', ['active', 'overdue'])->count();
        $overdueLoans = Loan::where('status', 'overdue')->count();
        $pendingLoanApps = LoanApplication::where('status', 'pending')->count();

        $invested = (float) Investment::whereIn('status', ['active', 'matured'])->sum('amount');
        $activeInvestments = Investment::where('status', 'active')->count();
        $maturedInvestments = Investment::where('status', 'matured')->count();

        $swfIn = (float) SwfEntry::where('type', 'contribution')->sum('amount');
        $swfOut = (float) SwfEntry::whereIn('type', ['payout', 'claim', 'deduction'])->sum('amount');

        $recentMembers = $canMembers ? Member::latest()->limit(6)->get() : collect();
        $recentLoans = $canLoans ? Loan::with('member')->latest()->limit(6)->get() : collect();
        $recentDeposits = $canDeposits ? Deposit::with('member')->latest()->limit(6)->get() : collect();
        $recentInvestments = $canInvest ? Investment::with('member')->latest()->limit(5)->get() : collect();
        $recentSwf = $canSwf ? SwfEntry::with('member')->latest()->limit(5)->get() : collect();

        $finIncome = $canFinance ? (float) \App\Models\FinanceTransaction::where('type', 'income')->sum('amount') : 0;
        $finExpenses = $canFinance ? (float) \App\Models\FinanceTransaction::where('type', 'expense')->sum('amount') : 0;
        $finCash = 0;
        if ($canFinance) {
            $cashIds = \App\Services\FinancePosting::cashAccountIds();
            $finCash = \App\Models\FinanceAccount::whereIn('id', $cashIds)->get()->sum(fn ($a) => $a->balance());
        }

        $monthly = [];
        for ($i = 5; $i >= 0; $i--) {
            $d = now()->subMonths($i);
            $monthly[] = [
                'label' => $d->format('M'),
                'deposits' => $canDeposits ? (float) Deposit::where('type', 'deposit')->whereYear('transacted_at', $d->year)->whereMonth('transacted_at', $d->month)->sum('amount') : 0,
                'repaid' => $canLoans ? (float) LoanRepayment::whereYear('paid_at', $d->year)->whereMonth('paid_at', $d->month)->sum('amount') : 0,
                'disbursed' => $canLoans ? (float) Loan::whereYear('disbursed_at', $d->year)->whereMonth('disbursed_at', $d->month)->sum('principal') : 0,
            ];
        }

        return view('dashboard.index', [
            'roles' => $roles,
            'isAdmin' => $isAdmin,
            'canMembers' => $canMembers,
            'canLoans' => $canLoans,
            'canDeposits' => $canDeposits,
            'canInvest' => $canInvest,
            'canSwf' => $canSwf,
            'canFinance' => $canFinance,
            'finIncome' => $finIncome,
            'finExpenses' => $finExpenses,
            'finCash' => $finCash,
            'ownMember' => $ownMember,
            'memberCount' => $memberCount,
            'activeMembers' => $activeMembers,
            'pendingMemberApps' => $pendingMemberApps,
            'savingsBalance' => $totalSavingsIn - $totalSavingsOut,
            'loanOutstanding' => max(0, (float) Loan::whereIn('status', ['active', 'overdue', 'paid'])->sum('total_payable') - $loanRepaid),
            'loanDisbursed' => $loanDisbursed,
            'activeLoans' => $activeLoans,
            'overdueLoans' => $overdueLoans,
            'pendingLoanApps' => $pendingLoanApps,
            'invested' => $invested,
            'activeInvestments' => $activeInvestments,
            'maturedInvestments' => $maturedInvestments,
            'swfBalance' => $swfIn - $swfOut,
            'recentMembers' => $recentMembers,
            'recentLoans' => $recentLoans,
            'recentDeposits' => $recentDeposits,
            'recentInvestments' => $recentInvestments,
            'recentSwf' => $recentSwf,
            'monthly' => $monthly,
        ]);
    }
}
