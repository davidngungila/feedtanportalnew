<?php

namespace App\Http\Controllers;

use App\Models\Budget;
use App\Models\FinanceAccount;
use App\Models\FinancialPeriod;
use Illuminate\Http\Request;

class BudgetController extends Controller
{
    public function index()
    {
        $budgets = Budget::with(['account', 'period'])->latest()->paginate(15);

        return view('finance.budgets', compact('budgets'));
    }

    public function create()
    {
        $accounts = FinanceAccount::whereIn('type', ['income', 'expense'])->where('status', 'active')->orderBy('code')->get();
        $periods = FinancialPeriod::orderBy('starts_at', 'desc')->get();

        return view('finance.budget-create', compact('accounts', 'periods'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'finance_account_id' => ['required', 'exists:finance_accounts,id'],
            'financial_period_id' => ['nullable', 'exists:financial_periods,id'],
            'budgeted' => ['required', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string'],
        ]);

        Budget::create($data);

        return redirect()->route('finance.budgets')->with('status', 'Budget saved.');
    }

    public function destroy(Budget $budget)
    {
        $budget->delete();

        return back()->with('status', 'Budget removed.');
    }
}
