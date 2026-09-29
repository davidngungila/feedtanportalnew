<?php

namespace App\Http\Controllers;

use App\Models\Deposit;
use App\Models\Investment;
use App\Models\Loan;
use App\Models\LoanApplication;
use App\Models\LoanProduct;
use App\Models\Member;
use Illuminate\Http\Request;

class MemberPortalController extends Controller
{
    /**
     * Resolve the member linked to the signed-in user.
     * Falls back to email match for legacy accounts.
     */
    protected function currentMember(): Member
    {
        $user = auth()->user();

        $member = $user?->member;
        if (! $member && $user?->email) {
            $member = Member::where('email', $user->email)->first();
        }

        abort_unless($member, 403, 'No member profile is linked to this login. Please contact the office.');

        return $member;
    }

    public function home()
    {
        $member = $this->currentMember()->load(['memberType', 'groups']);
        $bal = member_balance($member);

        return view('portal.dashboard', [
            'member' => $member,
            'bal' => $bal,
            'loans' => $member->loans()->with('repayments')->latest()->limit(5)->get(),
            'deposits' => $member->deposits()->latest()->limit(5)->get(),
            'investments' => $member->investments()->latest()->limit(5)->get(),
            'swf' => $member->swfEntries()->latest()->limit(5)->get(),
            'applications' => LoanApplication::where('member_id', $member->id)->with('product')->latest()->limit(5)->get(),
        ]);
    }

    public function loans()
    {
        $member = $this->currentMember();
        $loans = $member->loans()->with(['repayments', 'product'])->latest()->paginate(12);

        return view('portal.loans', compact('member', 'loans'));
    }

    public function loanShow(Loan $loan)
    {
        $member = $this->currentMember();
        abort_unless($loan->member_id === $member->id, 403);
        $loan->load(['repayments', 'product', 'member']);

        return view('portal.loan-show', compact('member', 'loan'));
    }

    public function deposits(Request $request)
    {
        $member = $this->currentMember();
        $deposits = $member->deposits()->latest()->paginate(15)->withQueryString();
        $in = (float) $member->deposits()->where('type', 'deposit')->sum('amount');
        $out = (float) $member->deposits()->where('type', 'withdrawal')->sum('amount');

        return view('portal.deposits', [
            'member' => $member,
            'deposits' => $deposits,
            'savings' => $in - $out,
        ]);
    }

    public function investments()
    {
        $member = $this->currentMember();
        $investments = $member->investments()->with('product')->latest()->paginate(12);
        $payouts = $member->payouts()->with('investment')->latest()->limit(10)->get();

        return view('portal.investments', compact('member', 'investments', 'payouts'));
    }

    public function swf()
    {
        $member = $this->currentMember();
        $entries = $member->swfEntries()->latest()->paginate(15)->withQueryString();

        return view('portal.swf', [
            'member' => $member,
            'entries' => $entries,
            'balance' => $member->swfBalance(),
        ]);
    }

    public function statements(Request $request)
    {
        $member = $this->currentMember();
        $from = $request->get('from');
        $to = $request->get('to');
        $kind = $request->get('kind', 'all');

        $rows = collect();

        $inRange = fn ($date) => (! $from || $date >= $from) && (! $to || $date <= $to);

        if (in_array($kind, ['all', 'savings'], true)) {
            foreach ($member->deposits()->orderByDesc('transacted_at')->limit(200)->get() as $d) {
                $date = $d->transacted_at?->format('Y-m-d') ?? $d->created_at->format('Y-m-d');
                if (! $inRange($date)) {
                    continue;
                }
                $rows->push([
                    'date' => $date,
                    'kind' => 'savings',
                    'ref' => $d->receipt_no,
                    'detail' => ucfirst($d->type).($d->method ? ' · '.ucfirst($d->method) : ''),
                    'in' => $d->type === 'deposit' ? (float) $d->amount : 0,
                    'out' => $d->type === 'withdrawal' ? (float) $d->amount : 0,
                ]);
            }
        }

        if (in_array($kind, ['all', 'loans'], true)) {
            foreach ($member->loans()->with('repayments')->orderByDesc('disbursed_at')->limit(100)->get() as $l) {
                $date = $l->disbursed_at?->format('Y-m-d') ?? $l->created_at->format('Y-m-d');
                if ($inRange($date)) {
                    $rows->push([
                        'date' => $date,
                        'kind' => 'loans',
                        'ref' => $l->loan_no,
                        'detail' => 'Loan disbursed · '.ucfirst($l->status),
                        'in' => (float) $l->principal,
                        'out' => 0,
                    ]);
                }
                foreach ($l->repayments as $r) {
                    $rdate = ($r->paid_at ?? $r->created_at)->format('Y-m-d');
                    if (! $inRange($rdate)) {
                        continue;
                    }
                    $rows->push([
                        'date' => $rdate,
                        'kind' => 'loans',
                        'ref' => $l->loan_no,
                        'detail' => 'Repayment'.($r->method ? ' · '.ucfirst($r->method) : ''),
                        'in' => 0,
                        'out' => (float) $r->amount,
                    ]);
                }
            }
        }

        if (in_array($kind, ['all', 'investments'], true)) {
            foreach ($member->investments()->orderByDesc('start_date')->limit(100)->get() as $i) {
                $date = $i->start_date?->format('Y-m-d') ?? $i->created_at->format('Y-m-d');
                if (! $inRange($date)) {
                    continue;
                }
                $rows->push([
                    'date' => $date,
                    'kind' => 'investments',
                    'ref' => $i->investment_no,
                    'detail' => 'Investment · '.ucfirst($i->status),
                    'in' => 0,
                    'out' => (float) $i->amount,
                ]);
            }
        }

        if (in_array($kind, ['all', 'swf'], true)) {
            foreach ($member->swfEntries()->orderByDesc('transacted_at')->limit(200)->get() as $s) {
                $date = $s->transacted_at?->format('Y-m-d') ?? $s->created_at->format('Y-m-d');
                if (! $inRange($date)) {
                    continue;
                }
                $isIn = $s->type === 'contribution';
                $rows->push([
                    'date' => $date,
                    'kind' => 'swf',
                    'ref' => $s->receipt_no,
                    'detail' => 'SWF '.ucfirst($s->type).($s->reason ? ' · '.$s->reason : ''),
                    'in' => $isIn ? (float) $s->amount : 0,
                    'out' => $isIn ? 0 : (float) $s->amount,
                ]);
            }
        }

        $rows = $rows->sortByDesc('date')->values();

        return view('portal.statements', [
            'member' => $member,
            'rows' => $rows->take(150),
            'from' => $from,
            'to' => $to,
            'kind' => $kind,
            'totalIn' => $rows->sum('in'),
            'totalOut' => $rows->sum('out'),
        ]);
    }

    public function profile()
    {
        $member = $this->currentMember()->load(['memberType', 'groups', 'documents']);
        $user = auth()->user();

        return view('portal.profile', compact('member', 'user'));
    }

    public function updateProfile(Request $request)
    {
        $member = $this->currentMember();

        $data = $request->validate([
            'phone' => ['required', 'string', 'max:30', 'unique:members,phone,'.$member->id],
            'email' => ['nullable', 'email', 'unique:members,email,'.$member->id],
            'address' => ['nullable', 'string', 'max:255'],
        ]);

        $member->update($data);

        return redirect()->route('portal.profile')->with('status', 'Contact details updated.');
    }

    public function loanApplications()
    {
        $member = $this->currentMember();
        $applications = LoanApplication::where('member_id', $member->id)
            ->with('product')->latest()->paginate(12);

        return view('portal.loan-applications', compact('member', 'applications'));
    }

    public function createLoanApplication()
    {
        $member = $this->currentMember();
        $products = LoanProduct::where('status', 'active')->orderBy('name')->get();

        return view('portal.loan-application-create', compact('member', 'products'));
    }

    public function storeLoanApplication(Request $request)
    {
        $member = $this->currentMember();

        $data = $request->validate([
            'loan_product_id' => ['nullable', 'exists:loan_products,id'],
            'amount' => ['required', 'numeric', 'min:1000'],
            'purpose' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
        ]);

        LoanApplication::create([
            ...$data,
            'member_id' => $member->id,
            'status' => 'pending',
        ]);

        return redirect()->route('portal.loan-applications')->with('status', 'Loan request sent. We will notify you once reviewed.');
    }

    public function cancelLoanApplication(LoanApplication $loanApplication)
    {
        $member = $this->currentMember();
        abort_unless($loanApplication->member_id === $member->id, 403);
        abort_unless($loanApplication->status === 'pending', 403, 'Only pending requests can be cancelled.');

        $loanApplication->delete();

        return redirect()->route('portal.loan-applications')->with('status', 'Request cancelled.');
    }
}
