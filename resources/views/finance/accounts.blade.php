@extends('layouts.app')
@section('title', $title)
@section('content')
    <div class="view-head"><div><h2>{{ $title }}</h2><p class="sub">Balances include opening + posted journals. Total: @money($total)</p></div><div class="view-actions"><a href="{{ route('finance.accounts.create') }}" class="btn btn-primary">+ New account</a></div></div>
    <div class="table-card"><div class="table-scroll"><table>
        <thead><tr><th>Code</th><th>Account</th><th>Type</th><th>Balance</th><th style="text-align:right;">Actions</th></tr></thead>
        <tbody>@forelse($accounts as $a)<tr><td class="cell-title">{{ $a->code }}</td><td>{{ $a->name }}</td><td><span class="tag tag-grey">{{ ucfirst($a->type) }}</span></td><td class="cell-title">@money($a->balance())</td>
        <td><div class="row-actions"><a href="{{ route('finance.ledger', ['account_id' => $a->id]) }}"><button type="button" title="Ledger">↗</button></a><a href="{{ route('finance.accounts.edit', $a) }}"><button type="button">Edit</button></a></div></td></tr>
        @empty<tr><td colspan="5" class="empty-state">No accounts.</td></tr>@endforelse</tbody>
    </table></div><div class="table-pager"><span class="pager-info">{{ $accounts->total() }} accounts</span><div class="pager-pages">{{ $accounts->links('pagination.pager') }}</div></div></div>
@endsection
