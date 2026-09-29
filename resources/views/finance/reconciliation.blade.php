@extends('layouts.app')
@section('title', 'Reconciliation')
@section('content')
    <div class="view-head"><div><h2>Reconciliation</h2><p class="sub">Statement balance vs system (ledger) balance.</p></div><div class="view-actions"><a href="{{ route('finance.reconciliation.create') }}" class="btn btn-primary">+ Reconcile</a></div></div>
    <div class="table-card"><div class="table-scroll"><table>
        <thead><tr><th>Account</th><th>Statement date</th><th>Statement</th><th>System</th><th>Variance</th><th>Status</th><th style="text-align:right;">Remove</th></tr></thead>
        <tbody>@forelse($rows as $r)<tr><td class="cell-title">{{ $r->account->name ?? '—' }}</td><td>{{ $r->statement_date?->format('d M Y') }}</td><td>@money($r->statement_balance)</td><td>@money($r->system_balance)</td><td class="cell-title">@money($r->variance)</td>
        <td><span class="tag {{ $r->status === 'matched' ? 'tag-green' : 'tag-red' }}">{{ ucfirst($r->status) }}</span></td>
        <td><form method="POST" action="{{ route('finance.reconciliation.destroy', $r) }}" onsubmit="return confirm('Remove?')">@csrf @method('DELETE')<div class="row-actions"><button type="submit" class="danger">✕</button></div></form></td></tr>
        @empty<tr><td colspan="7" class="empty-state">Nothing reconciled yet.</td></tr>@endforelse</tbody>
    </table></div><div class="table-pager"><span class="pager-info">{{ $rows->total() }} rows</span><div class="pager-pages">{{ $rows->links('pagination.pager') }}</div></div></div>
@endsection
