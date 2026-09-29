<?php

namespace App\Http\Controllers;

use App\Models\Investment;
use App\Models\InvestmentReturn;
use Illuminate\Http\Request;

class InvestmentReturnController extends Controller
{
    public function index()
    {
        $returns = InvestmentReturn::with('investment.member')->latest()->paginate(15);
        $total = (float) InvestmentReturn::sum('amount');

        return view('investment-returns.index', compact('returns', 'total'));
    }

    public function create(Request $request)
    {
        $investments = Investment::with('member')->where('status', 'active')->latest()->get();
        $selected = null;
        if ($request->filled('investment_id')) {
            try {
                $selected = did($request->get('investment_id'));
            } catch (\Throwable) {
                $selected = null;
            }
        }

        return view('investment-returns.create', compact('investments', 'selected'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'investment_id' => ['required', 'exists:investments,id'],
            'amount' => ['required', 'numeric', 'min:100'],
            'paid_at' => ['required', 'date'],
            'notes' => ['nullable', 'string'],
        ]);

        $data['paid_by'] = auth()->id();
        InvestmentReturn::create($data);

        return redirect()->route('investment-returns.index')->with('status', 'Investment return recorded.');
    }

    public function destroy(InvestmentReturn $investmentReturn)
    {
        $investmentReturn->delete();

        return redirect()->route('investment-returns.index')->with('status', 'Return removed.');
    }
}
