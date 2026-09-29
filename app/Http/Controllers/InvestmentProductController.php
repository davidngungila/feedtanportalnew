<?php

namespace App\Http\Controllers;

use App\Models\InvestmentProduct;
use Illuminate\Http\Request;

class InvestmentProductController extends Controller
{
    public function index()
    {
        $products = InvestmentProduct::withCount('investments')->latest()->paginate(15);

        return view('investment-products.index', compact('products'));
    }

    public function create()
    {
        return view('investment-products.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:investment_products,name'],
            'return_rate' => ['required', 'numeric', 'min:0', 'max:100'],
            'min_amount' => ['required', 'numeric', 'min:0'],
            'duration_months' => ['required', 'integer', 'min:1', 'max:120'],
            'description' => ['nullable', 'string'],
            'status' => ['required', 'in:active,inactive'],
        ]);

        InvestmentProduct::create($data);

        return redirect()->route('investment-products.index')->with('status', 'Investment product created.');
    }

    public function edit(InvestmentProduct $investmentProduct)
    {
        return view('investment-products.edit', ['product' => $investmentProduct]);
    }

    public function update(Request $request, InvestmentProduct $investmentProduct)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:investment_products,name,'.$investmentProduct->id],
            'return_rate' => ['required', 'numeric', 'min:0', 'max:100'],
            'min_amount' => ['required', 'numeric', 'min:0'],
            'duration_months' => ['required', 'integer', 'min:1', 'max:120'],
            'description' => ['nullable', 'string'],
            'status' => ['required', 'in:active,inactive'],
        ]);

        $investmentProduct->update($data);

        return redirect()->route('investment-products.index')->with('status', 'Investment product updated.');
    }

    public function destroy(InvestmentProduct $investmentProduct)
    {
        $investmentProduct->delete();

        return redirect()->route('investment-products.index')->with('status', 'Investment product removed.');
    }
}
