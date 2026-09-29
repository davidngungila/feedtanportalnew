<?php

namespace App\Http\Controllers;

use App\Models\FinanceAccount;
use App\Models\FinanceReconciliation;
use App\Services\FinancePosting;
use Illuminate\Http\Request;

class FinanceReconciliationController extends Controller
{
    public function index()
    {
        $rows = FinanceReconciliation::with('account')->latest()->paginate(15);

        return view('finance.reconciliation', compact('rows'));
    }

    public function create()
    {
        $accounts = FinanceAccount::whereIn('type', ['asset', 'liability'])->where('status', 'active')->orderBy('code')->get();

        return view('finance.reconciliation-create', compact('accounts'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'finance_account_id' => ['required', 'exists:finance_accounts,id'],
            'statement_date' => ['required', 'date'],
            'statement_balance' => ['required', 'numeric'],
            'notes' => ['nullable', 'string'],
        ]);

        $system = FinancePosting::accountBalance((int) $data['finance_account_id'], $data['statement_date']);
        $variance = round($data['statement_balance'] - $system, 2);

        FinanceReconciliation::create([
            ...$data,
            'system_balance' => $system,
            'variance' => $variance,
            'status' => abs($variance) < 0.005 ? 'matched' : 'variance',
            'created_by' => auth()->id(),
        ]);

        return redirect()->route('finance.reconciliation')->with('status', abs($variance) < 0.005 ? 'Reconciled — matched.' : 'Saved with variance of '.money($variance).'.');
    }

    public function destroy(FinanceReconciliation $reconciliation)
    {
        $reconciliation->delete();

        return back()->with('status', 'Reconciliation removed.');
    }
}
