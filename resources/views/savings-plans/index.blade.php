@extends('layouts.app')
@section('title', 'Savings Plans')
@section('content')
    <div class="view-head"><div><h2>Savings Plans</h2><p class="sub">Target-based plans linked to deposit products.</p></div><div class="view-actions"><a href="{{ route('savings-plans.create') }}" class="btn btn-primary">+ New plan</a></div></div>
    <div class="table-card"><div class="table-scroll"><table>
        <thead><tr><th>Name</th><th>Target</th><th>Duration</th><th>Product</th><th>Status</th><th style="text-align:right;">Actions</th></tr></thead>
        <tbody>@forelse($plans as $p)<tr><td class="cell-title">{{ $p->name }}</td><td>@money($p->target_amount)</td><td>{{ $p->duration_months }} mo</td><td>{{ $p->product->name ?? '—' }}</td><td><span class="tag {{ status_badge($p->status) }}">{{ ucfirst($p->status) }}</span></td>
        <td><div class="row-actions"><a href="{{ route('savings-plans.edit', $p) }}"><button type="button">Edit</button></a><form method="POST" action="{{ route('savings-plans.destroy', $p) }}" onsubmit="return confirm('Remove?')">@csrf @method('DELETE')<button type="submit" class="danger">✕</button></form></div></td></tr>
        @empty<tr><td colspan="6" class="empty-state">No plans.</td></tr>@endforelse</tbody>
    </table></div><div class="table-pager"><span class="pager-info">{{ $plans->total() }} plans</span><div class="pager-pages">{{ $plans->links('pagination.pager') }}</div></div></div>
@endsection
