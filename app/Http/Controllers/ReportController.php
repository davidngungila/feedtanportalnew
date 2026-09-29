<?php

namespace App\Http\Controllers;

use App\Models\Deposit;
use App\Models\Investment;
use App\Models\Loan;
use App\Models\LoanRepayment;
use App\Models\Member;
use App\Models\SwfEntry;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->get('tab', 'summary');
        $from = $request->get('from', now()->startOfMonth()->toDateString());
        $to = $request->get('to', now()->toDateString());

        $loans = Loan::with('member')->whereBetween('disbursed_at', [$from, $to])->latest()->limit(200)->get();
        $repayments = LoanRepayment::with('loan.member')->whereBetween('paid_at', [$from, $to])->latest()->limit(200)->get();
        $deposits = Deposit::with('member')->whereBetween('transacted_at', [$from, $to])->latest()->limit(200)->get();
        $investments = Investment::with('member')->whereBetween('start_date', [$from, $to])->latest()->limit(200)->get();
        $swf = SwfEntry::with('member')->whereBetween('transacted_at', [$from, $to])->latest()->limit(200)->get();

        $members = Member::orderBy('name')->get()->map(function (Member $m) {
            $b = member_balance($m);

            return ['member' => $m, ...$b];
        });

        return view('reports.index', compact('tab', 'from', 'to', 'loans', 'repayments', 'deposits', 'investments', 'swf', 'members'));
    }

    private function range(Request $request): array
    {
        return [
            $request->get('from', now()->startOfMonth()->toDateString()),
            $request->get('to', now()->toDateString()),
        ];
    }

    public function members(Request $request)
    {
        [$from, $to] = $this->range($request);
        $members = Member::orderBy('name')->get()->map(fn (Member $m) => ['member' => $m, ...member_balance($m)]);
        $newCount = Member::whereBetween('join_date', [$from, $to])->count();

        return view('reports.members', compact('from', 'to', 'members', 'newCount'));
    }

    public function loans(Request $request)
    {
        [$from, $to] = $this->range($request);
        $loans = Loan::with(['member', 'product'])->whereBetween('disbursed_at', [$from, $to])->latest()->paginate(20)->withQueryString();
        $disbursed = (float) Loan::whereBetween('disbursed_at', [$from, $to])->sum('principal');
        $repaid = (float) LoanRepayment::whereBetween('paid_at', [$from, $to])->sum('amount');
        $outstanding = max(0, (float) Loan::whereIn('status', ['active', 'overdue', 'paid'])->sum('total_payable') - (float) LoanRepayment::sum('amount'));

        return view('reports.loans', compact('from', 'to', 'loans', 'disbursed', 'repaid', 'outstanding'));
    }

    public function savings(Request $request)
    {
        [$from, $to] = $this->range($request);
        $deposits = Deposit::with('member')->whereBetween('transacted_at', [$from, $to])->latest()->paginate(15)->withQueryString();
        $investments = Investment::with('member')->whereBetween('start_date', [$from, $to])->latest()->paginate(15, ['*'], 'invest_page')->withQueryString();
        $depIn = (float) Deposit::where('type', 'deposit')->whereBetween('transacted_at', [$from, $to])->sum('amount');
        $depOut = (float) Deposit::where('type', 'withdrawal')->whereBetween('transacted_at', [$from, $to])->sum('amount');
        $invTotal = (float) Investment::whereBetween('start_date', [$from, $to])->sum('amount');

        return view('reports.savings', compact('from', 'to', 'deposits', 'investments', 'depIn', 'depOut', 'invTotal'));
    }

    public function transactions(Request $request)
    {
        [$from, $to] = $this->range($request);
        $repayments = LoanRepayment::with('loan.member')->whereBetween('paid_at', [$from, $to])->latest()->paginate(15)->withQueryString();
        $deposits = Deposit::with('member')->whereBetween('transacted_at', [$from, $to])->latest()->paginate(15, ['*'], 'dep_page')->withQueryString();
        $swf = SwfEntry::with('member')->whereBetween('transacted_at', [$from, $to])->latest()->paginate(15, ['*'], 'swf_page')->withQueryString();
        $finance = \App\Models\FinanceTransaction::with('account')->whereBetween('transacted_at', [$from, $to])->latest()->paginate(15, ['*'], 'fin_page')->withQueryString();

        return view('reports.transactions', compact('from', 'to', 'repayments', 'deposits', 'swf', 'finance'));
    }
}
