<?php

namespace App\Http\Controllers;

use App\Models\DepositProduct;
use App\Models\SavingsPlan;
use Illuminate\Http\Request;

class SavingsPlanController extends Controller
{
    public function index()
    {
        $plans = SavingsPlan::with('product')->latest()->paginate(15);

        return view('savings-plans.index', compact('plans'));
    }

    public function create()
    {
        $products = DepositProduct::where('status', 'active')->orderBy('name')->get();

        return view('savings-plans.create', compact('products'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:savings_plans,name'],
            'target_amount' => ['required', 'numeric', 'min:0'],
            'duration_months' => ['required', 'integer', 'min:1', 'max:120'],
            'deposit_product_id' => ['nullable', 'exists:deposit_products,id'],
            'description' => ['nullable', 'string'],
            'status' => ['required', 'in:active,inactive'],
        ]);

        SavingsPlan::create($data);

        return redirect()->route('savings-plans.index')->with('status', 'Savings plan created.');
    }

    public function edit(SavingsPlan $savingsPlan)
    {
        $products = DepositProduct::orderBy('name')->get();

        return view('savings-plans.edit', ['plan' => $savingsPlan, 'products' => $products]);
    }

    public function update(Request $request, SavingsPlan $savingsPlan)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:savings_plans,name,'.$savingsPlan->id],
            'target_amount' => ['required', 'numeric', 'min:0'],
            'duration_months' => ['required', 'integer', 'min:1', 'max:120'],
            'deposit_product_id' => ['nullable', 'exists:deposit_products,id'],
            'description' => ['nullable', 'string'],
            'status' => ['required', 'in:active,inactive'],
        ]);

        $savingsPlan->update($data);

        return redirect()->route('savings-plans.index')->with('status', 'Savings plan updated.');
    }

    public function destroy(SavingsPlan $savingsPlan)
    {
        $savingsPlan->delete();

        return redirect()->route('savings-plans.index')->with('status', 'Savings plan removed.');
    }
}
