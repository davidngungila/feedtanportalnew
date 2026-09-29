<?php

namespace App\Http\Controllers;

use App\Models\Loan;
use App\Models\LoanProduct;
use App\Models\LoanRepayment;
use App\Models\Member;
use Illuminate\Http\Request;

class LoanController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->get('status', 'all');
        $q = trim((string) $request->get('q', ''));

        $query = Loan::query()->with(['member', 'repayments', 'product'])->latest();

        if (in_array($status, ['pending', 'active', 'paid', 'overdue', 'defaulted'], true)) {
            $query->where('status', $status);
        }
        if ($q !== '') {
            $query->where(fn ($w) => $w->where('loan_no', 'like', "%{$q}%")
                ->orWhereHas('member', fn ($m) => $m->where('name', 'like', "%{$q}%")->orWhere('phone', 'like', "%{$q}%")));
        }

        $loans = $query->paginate(15)->withQueryString();

        return view('loans.index', compact('loans', 'status', 'q'));
    }

    public function create()
    {
        $members = Member::where('status', 'active')->orderBy('name')->get();
        $products = LoanProduct::where('status', 'active')->orderBy('name')->get();

        return view('loans.create', compact('members', 'products'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'member_id' => ['required', 'exists:members,id'],
            'loan_product_id' => ['nullable', 'exists:loan_products,id'],
            'principal' => ['required', 'numeric', 'min:1000'],
            'interest_rate' => ['required', 'numeric', 'min:0', 'max:100'],
            'disbursed_at' => ['nullable', 'date'],
            'due_date' => ['nullable', 'date', 'after_or_equal:disbursed_at'],
            'status' => ['required', 'in:pending,active,paid,overdue,defaulted'],
            'purpose' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
        ]);

        $interest = round($data['principal'] * $data['interest_rate'] / 100, 2);

        $loan = \Illuminate\Support\Facades\DB::transaction(function () use ($data, $interest) {
            return Loan::create([
                ...$data,
                'loan_no' => 'LN-'.now()->format('YmdHis').'-'.random_int(100, 999),
                'interest_amount' => $interest,
                'total_payable' => $data['principal'] + $interest,
                'disbursed_at' => $data['disbursed_at'] ?? now()->toDateString(),
                'created_by' => auth()->id(),
            ]);
        });

        return redirect()->route('loans.show', $loan)->with('status', 'Loan recorded and posted to the ledger.');
    }

    public function show(Loan $loan)
    {
        $loan->load(['member', 'repayments', 'product']);
        $repaymentIds = $loan->repayments->pluck('id');
        $journals = \App\Models\JournalEntry::where(function ($q) use ($loan, $repaymentIds) {
            $q->where(fn ($w) => $w->where('source_type', \App\Models\Loan::class)->where('source_id', $loan->id))
                ->orWhere(fn ($w) => $w->where('source_type', \App\Models\LoanRepayment::class)->whereIn('source_id', $repaymentIds));
        })->latest('entry_date')->get();

        return view('loans.show', compact('loan', 'journals'));
    }

    public function edit(Loan $loan)
    {
        return view('loans.edit', compact('loan'));
    }

    public function update(Request $request, Loan $loan)
    {
        $data = $request->validate([
            'status' => ['required', 'in:pending,active,paid,overdue,defaulted'],
            'due_date' => ['nullable', 'date'],
            'purpose' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
        ]);

        \Illuminate\Support\Facades\DB::transaction(function () use ($loan, $data) {
            $loan->update($data);
        });

        return redirect()->route('loans.show', $loan)->with('status', 'Loan updated.');
    }

    public function repay(Request $request, Loan $loan)
    {
        $data = $request->validate([
            'amount' => ['required', 'numeric', 'min:100', 'max:'.$loan->outstanding()],
            'paid_at' => ['required', 'date'],
            'method' => ['required', 'in:cash,mobile,bank'],
            'notes' => ['nullable', 'string'],
        ]);

        \Illuminate\Support\Facades\DB::transaction(function () use ($loan, $data) {
            LoanRepayment::create([
                ...$data,
                'loan_id' => $loan->id,
                'receipt_no' => 'LR-'.now()->format('YmdHis').'-'.random_int(100, 999),
                'received_by' => auth()->id(),
            ]);

            if ($loan->fresh()->outstanding() <= 0) {
                $loan->update(['status' => 'paid']);
            } elseif ($loan->status === 'pending') {
                $loan->update(['status' => 'active']);
            }
        });

        return redirect()->route('loans.show', $loan)->with('status', 'Repayment recorded and posted to the ledger.');
    }

    public function destroyRepayment(LoanRepayment $repayment)
    {
        $loanId = $repayment->loan_id;
        $repayment->delete();

        return redirect()->route('loans.show', $loanId)->with('status', 'Repayment removed (journal reversed).');
    }

    public function destroy(Loan $loan)
    {
        $loan->delete();

        return redirect()->route('loans.index')->with('status', 'Loan removed (journals reversed).');
    }
}
