<?php

namespace App\Http\Controllers;

use App\Models\DepositProduct;
use Illuminate\Http\Request;

class DepositProductController extends Controller
{
    public function index()
    {
        $products = DepositProduct::withCount('deposits')->latest()->paginate(15);

        return view('deposit-products.index', compact('products'));
    }

    public function create()
    {
        return view('deposit-products.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:deposit_products,name'],
            'interest_rate' => ['required', 'numeric', 'min:0', 'max:100'],
            'min_amount' => ['required', 'numeric', 'min:0'],
            'description' => ['nullable', 'string'],
            'status' => ['required', 'in:active,inactive'],
        ]);

        DepositProduct::create($data);

        return redirect()->route('deposit-products.index')->with('status', 'Deposit product created.');
    }

    public function edit(DepositProduct $depositProduct)
    {
        return view('deposit-products.edit', ['product' => $depositProduct]);
    }

    public function update(Request $request, DepositProduct $depositProduct)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:deposit_products,name,'.$depositProduct->id],
            'interest_rate' => ['required', 'numeric', 'min:0', 'max:100'],
            'min_amount' => ['required', 'numeric', 'min:0'],
            'description' => ['nullable', 'string'],
            'status' => ['required', 'in:active,inactive'],
        ]);

        $depositProduct->update($data);

        return redirect()->route('deposit-products.index')->with('status', 'Deposit product updated.');
    }

    public function destroy(DepositProduct $depositProduct)
    {
        $depositProduct->delete();

        return redirect()->route('deposit-products.index')->with('status', 'Deposit product removed.');
    }
}
