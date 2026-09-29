<?php

namespace App\Http\Controllers;

use App\Models\FinancialPeriod;
use Illuminate\Http\Request;

class FinancialPeriodController extends Controller
{
    public function index()
    {
        $periods = FinancialPeriod::orderBy('starts_at', 'desc')->paginate(15);

        return view('finance.periods', compact('periods'));
    }

    public function create()
    {
        return view('finance.period-create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:financial_periods,name'],
            'starts_at' => ['required', 'date'],
            'ends_at' => ['required', 'date', 'after_or_equal:starts_at'],
            'status' => ['required', 'in:open,closed'],
        ]);

        FinancialPeriod::create($data);

        return redirect()->route('finance.periods')->with('status', 'Period created.');
    }

    public function toggle(FinancialPeriod $period)
    {
        $period->update(['status' => $period->status === 'open' ? 'closed' : 'open']);

        return back()->with('status', 'Period '.$period->status.'.');
    }

    public function destroy(FinancialPeriod $period)
    {
        $period->delete();

        return back()->with('status', 'Period removed.');
    }
}
