<?php

namespace App\Http\Controllers;

use App\Models\FinanceAccount;
use Illuminate\Http\Request;

class FinanceAccountController extends Controller
{
    private function listByType(?string $type, string $title)
    {
        $query = FinanceAccount::query()->latest('code');
        if ($type) {
            $query->where('type', $type);
        }
        $accounts = $query->paginate(20);
        $total = FinanceAccount::when($type, fn ($q) => $q->where('type', $type))->get()->sum(fn ($a) => $a->balance());

        return view('finance.accounts', compact('accounts', 'title', 'total', 'type'));
    }

    public function accounts()
    {
        return $this->listByType(null, 'Accounts');
    }

    public function assets()
    {
        return $this->listByType('asset', 'Assets');
    }

    public function liabilities()
    {
        return $this->listByType('liability', 'Liabilities');
    }

    public function equity()
    {
        return $this->listByType('equity', 'Equity');
    }

    public function chart()
    {
        $grouped = FinanceAccount::orderBy('code')->get()->groupBy('type');

        return view('finance.chart', compact('grouped'));
    }

    public function create()
    {
        return view('finance.account-create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'code' => ['required', 'string', 'max:20', 'unique:finance_accounts,code'],
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'in:asset,liability,equity,income,expense'],
            'opening_balance' => ['nullable', 'numeric', 'min:0'],
            'description' => ['nullable', 'string'],
            'status' => ['required', 'in:active,inactive'],
        ]);

        $data['opening_balance'] ??= 0;
        FinanceAccount::create($data);

        return redirect()->route('finance.chart')->with('status', 'Account created.');
    }

    public function edit(FinanceAccount $account)
    {
        return view('finance.account-edit', compact('account'));
    }

    public function update(Request $request, FinanceAccount $account)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'opening_balance' => ['nullable', 'numeric', 'min:0'],
            'description' => ['nullable', 'string'],
            'status' => ['required', 'in:active,inactive'],
        ]);

        $account->update($data);

        return redirect()->route('finance.chart')->with('status', 'Account updated.');
    }
}
