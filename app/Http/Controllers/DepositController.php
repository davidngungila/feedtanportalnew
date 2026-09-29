<?php

namespace App\Http\Controllers;

use App\Models\Deposit;
use App\Models\DepositProduct;
use App\Models\Member;
use Illuminate\Http\Request;

class DepositController extends Controller
{
    public function index(Request $request)
    {
        $type = $request->get('type', 'all');
        $q = trim((string) $request->get('q', ''));

        $query = Deposit::query()->with(['member', 'product'])->latest();

        if (in_array($type, ['deposit', 'withdrawal'], true)) {
            $query->where('type', $type);
        }
        if ($q !== '') {
            $query->where(fn ($w) => $w->where('receipt_no', 'like', "%{$q}%")
                ->orWhereHas('member', fn ($m) => $m->where('name', 'like', "%{$q}%")->orWhere('phone', 'like', "%{$q}%")));
        }

        $deposits = $query->paginate(15)->withQueryString();

        $in = (float) Deposit::where('type', 'deposit')->sum('amount');
        $out = (float) Deposit::where('type', 'withdrawal')->sum('amount');

        return view('deposits.index', [
            'deposits' => $deposits, 'type' => $type, 'q' => $q,
            'totalIn' => $in, 'totalOut' => $out,
        ]);
    }

    public function withdrawals(Request $request)
    {
        $request->merge(['type' => 'withdrawal']);

        $result = $this->index($request);
        // Reuse same view but force withdrawal filter title via shared var.
        if ($result instanceof \Illuminate\View\View) {
            $result->with('pageTitle', 'Withdrawals');
        }

        return $result;
    }

    public function create()
    {
        $members = Member::where('status', 'active')->orderBy('name')->get();
        $products = DepositProduct::where('status', 'active')->orderBy('name')->get();

        return view('deposits.create', compact('members', 'products'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'member_id' => ['required', 'exists:members,id'],
            'deposit_product_id' => ['nullable', 'exists:deposit_products,id'],
            'type' => ['required', 'in:deposit,withdrawal'],
            'amount' => ['required', 'numeric', 'min:100'],
            'method' => ['required', 'in:cash,mobile,bank'],
            'transacted_at' => ['required', 'date'],
            'notes' => ['nullable', 'string'],
        ]);

        $deposit = \Illuminate\Support\Facades\DB::transaction(function () use ($data) {
            return Deposit::create([
                ...$data,
                'receipt_no' => 'DP-'.now()->format('YmdHis').'-'.random_int(100, 999),
                'received_by' => auth()->id(),
            ]);
        });

        return redirect()->route('deposits.index')->with('status', 'Entry recorded and posted to the ledger.');
    }

    public function edit(Deposit $deposit)
    {
        $members = Member::orderBy('name')->get();
        $products = DepositProduct::orderBy('name')->get();

        return view('deposits.edit', compact('deposit', 'members', 'products'));
    }

    public function update(Request $request, Deposit $deposit)
    {
        $data = $request->validate([
            'deposit_product_id' => ['nullable', 'exists:deposit_products,id'],
            'type' => ['required', 'in:deposit,withdrawal'],
            'amount' => ['required', 'numeric', 'min:100'],
            'method' => ['required', 'in:cash,mobile,bank'],
            'transacted_at' => ['required', 'date'],
            'notes' => ['nullable', 'string'],
        ]);

        \Illuminate\Support\Facades\DB::transaction(function () use ($deposit, $data) {
            $deposit->update($data);
        });

        return redirect()->route('deposits.index')->with('status', 'Entry updated (journal reposted).');
    }

    public function destroy(Deposit $deposit)
    {
        $deposit->delete();

        return redirect()->route('deposits.index')->with('status', 'Entry removed (journal reversed).');
    }
}
