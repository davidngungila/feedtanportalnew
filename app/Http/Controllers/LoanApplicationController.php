<?php

namespace App\Http\Controllers;

use App\Models\Loan;
use App\Models\LoanApplication;
use App\Models\LoanProduct;
use App\Models\Member;
use Illuminate\Http\Request;

class LoanApplicationController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->get('status', 'all');
        $query = LoanApplication::query()->with(['member', 'product'])->latest();

        if (in_array($status, ['pending', 'approved', 'rejected', 'disbursed'], true)) {
            $query->where('status', $status);
        }

        $applications = $query->paginate(15)->withQueryString();

        return view('loan-applications.index', compact('applications', 'status'));
    }

    public function create()
    {
        $members = Member::where('status', 'active')->orderBy('name')->get();
        $products = LoanProduct::where('status', 'active')->orderBy('name')->get();

        return view('loan-applications.create', compact('members', 'products'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'member_id' => ['required', 'exists:members,id'],
            'loan_product_id' => ['nullable', 'exists:loan_products,id'],
            'amount' => ['required', 'numeric', 'min:1000'],
            'purpose' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
        ]);

        $data['status'] = 'pending';
        LoanApplication::create($data);

        return redirect()->route('loan-applications.index')->with('status', 'Loan application submitted.');
    }

    public function update(Request $request, LoanApplication $loanApplication)
    {
        $data = $request->validate([
            'status' => ['required', 'in:pending,approved,rejected,disbursed'],
        ]);

        $loanApplication->update([...$data, 'reviewed_by' => auth()->id()]);

        return redirect()->route('loan-applications.index')->with('status', 'Loan application updated.');
    }

    public function approve(LoanApplication $loanApplication)
    {
        $rate = (float) ($loanApplication->product->interest_rate ?? 10);
        $interest = round($loanApplication->amount * $rate / 100, 2);

        $loan = \Illuminate\Support\Facades\DB::transaction(function () use ($loanApplication, $rate, $interest) {
            $loan = Loan::create([
                'member_id' => $loanApplication->member_id,
                'loan_product_id' => $loanApplication->loan_product_id,
                'loan_no' => 'LN-'.now()->format('YmdHis').'-'.random_int(100, 999),
                'principal' => $loanApplication->amount,
                'interest_rate' => $rate,
                'interest_amount' => $interest,
                'total_payable' => $loanApplication->amount + $interest,
                'disbursed_at' => now()->toDateString(),
                'due_date' => now()->addMonths((int) ($loanApplication->product->duration_months ?? 12))->toDateString(),
                'status' => 'active',
                'purpose' => $loanApplication->purpose,
                'notes' => 'From application #'.$loanApplication->id,
                'created_by' => auth()->id(),
            ]);

            $loanApplication->update(['status' => 'disbursed', 'reviewed_by' => auth()->id()]);

            return $loan;
        });

        return redirect()->route('loans.show', $loan)->with('status', 'Loan disbursed and posted to the ledger.');
    }

    public function destroy(LoanApplication $loanApplication)
    {
        $loanApplication->delete();

        return redirect()->route('loan-applications.index')->with('status', 'Application removed.');
    }
}
