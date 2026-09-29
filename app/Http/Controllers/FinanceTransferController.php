<?php

namespace App\Http\Controllers;

use App\Models\FinanceAccount;
use App\Models\FinanceTransfer;
use App\Services\FinancePosting;
use Illuminate\Http\Request;

class FinanceTransferController extends Controller
{
    public function index()
    {
        $transfers = FinanceTransfer::with(['fromAccount', 'toAccount'])->latest()->paginate(15);

        return view('finance.transfers', compact('transfers'));
    }

    public function create()
    {
        $accounts = FinanceAccount::where('status', 'active')->orderBy('code')->get();

        return view('finance.transfer-create', compact('accounts'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'from_account_id' => ['required', 'exists:finance_accounts,id', 'different:to_account_id'],
            'to_account_id' => ['required', 'exists:finance_accounts,id'],
            'amount' => ['required', 'numeric', 'min:100'],
            'transferred_at' => ['required', 'date'],
            'notes' => ['nullable', 'string', 'max:255'],
        ]);

        FinancePosting::assertOpenPeriod($data['transferred_at']);

        $transfer = FinanceTransfer::create([
            ...$data,
            'reference' => FinancePosting::reference('TR'),
            'created_by' => auth()->id(),
        ]);

        FinancePosting::postTransfer($transfer->fresh());

        return redirect()->route('finance.transfers')->with('status', 'Transfer posted.');
    }

    public function destroy(FinanceTransfer $transfer)
    {
        if ($transfer->journal) {
            $transfer->journal->lines()->delete();
            $transfer->journal->delete();
        }
        $transfer->delete();

        return back()->with('status', 'Transfer removed (journal reversed).');
    }
}
