@extends('layouts.app')
@section('title', 'Chart of Accounts')
@section('content')
    <div class="view-head"><div><h2>Chart of Accounts</h2><p class="sub">Full books structure grouped by class.</p></div><div class="view-actions"><a href="{{ route('finance.accounts.create') }}" class="btn btn-primary">+ New account</a></div></div>
    @foreach(['asset' => 'Assets', 'liability' => 'Liabilities', 'equity' => 'Equity', 'income' => 'Income', 'expense' => 'Expenses'] as $type => $label)
    <div class="table-card">
        <div class="table-toolbar"><strong>{{ $label }}</strong><span class="tag tag-green">{{ ($grouped[$type] ?? collect())->count() }} accounts</span></div>
        <div class="table-scroll"><table>
            <thead><tr><th>Code</th><th>Name</th><th>Opening</th><th>Balance</th><th style="text-align:right;">Actions</th></tr></thead>
            <tbody>@forelse($grouped[$type] ?? [] as $a)<tr><td class="cell-title">{{ $a->code }}</td><td>{{ $a->name }}</td><td>@money($a->opening_balance)</td><td class="cell-title">@money($a->balance())</td>
            <td><div class="row-actions"><a href="{{ route('finance.ledger', ['account_id' => $a->id]) }}"><button type="button">↗</button></a><a href="{{ route('finance.accounts.edit', $a) }}"><button type="button">Edit</button></a></div></td></tr>
            @empty<tr><td colspan="5" class="empty-state">None.</td></tr>@endforelse</tbody>
        </table></div>
    </div>
    @endforeach
@endsection
