<?php

namespace App\Http\Controllers;

use App\Models\LoanProduct;
use Illuminate\Http\Request;

class LoanProductController extends Controller
{
    public function index()
    {
        $products = LoanProduct::withCount('loans')->latest()->paginate(15);

        return view('loan-products.index', compact('products'));
    }

    public function create()
    {
        return view('loan-products.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:loan_products,name'],
            'interest_rate' => ['required', 'numeric', 'min:0', 'max:100'],
            'min_amount' => ['required', 'numeric', 'min:0'],
            'max_amount' => ['required', 'numeric', 'min:0', 'gte:min_amount'],
            'duration_months' => ['required', 'integer', 'min:1', 'max:120'],
            'description' => ['nullable', 'string'],
            'status' => ['required', 'in:active,inactive'],
        ]);

        LoanProduct::create($data);

        return redirect()->route('loan-products.index')->with('status', 'Loan product created.');
    }

    public function edit(LoanProduct $loanProduct)
    {
        return view('loan-products.edit', ['product' => $loanProduct]);
    }

    public function update(Request $request, LoanProduct $loanProduct)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:loan_products,name,'.$loanProduct->id],
            'interest_rate' => ['required', 'numeric', 'min:0', 'max:100'],
            'min_amount' => ['required', 'numeric', 'min:0'],
            'max_amount' => ['required', 'numeric', 'min:0', 'gte:min_amount'],
            'duration_months' => ['required', 'integer', 'min:1', 'max:120'],
            'description' => ['nullable', 'string'],
            'status' => ['required', 'in:active,inactive'],
        ]);

        $loanProduct->update($data);

        return redirect()->route('loan-products.index')->with('status', 'Loan product updated.');
    }

    public function destroy(LoanProduct $loanProduct)
    {
        $loanProduct->delete();

        return redirect()->route('loan-products.index')->with('status', 'Loan product removed.');
    }
}
