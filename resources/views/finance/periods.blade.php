@extends('layouts.app')
@section('title', 'Financial Periods')
@section('content')
    <div class="view-head"><div><h2>Financial Periods</h2><p class="sub">Closed periods reject new postings dated inside them.</p></div><div class="view-actions"><a href="{{ route('finance.periods.create') }}" class="btn btn-primary">+ New period</a></div></div>
    <div class="table-card"><div class="table-scroll"><table>
        <thead><tr><th>Name</th><th>From</th><th>To</th><th>Status</th><th style="text-align:right;">Actions</th></tr></thead>
        <tbody>@forelse($periods as $p)<tr><td class="cell-title">{{ $p->name }}</td><td>{{ $p->starts_at?->format('d M Y') }}</td><td>{{ $p->ends_at?->format('d M Y') }}</td>
        <td><span class="tag {{ $p->status === 'open' ? 'tag-green' : 'tag-grey' }}">{{ ucfirst($p->status) }}</span></td>
        <td><div class="row-actions" style="justify-content:flex-end;">
            <form method="POST" action="{{ route('finance.periods.toggle', $p) }}">@csrf<button class="btn btn-ghost btn-sm" type="submit">{{ $p->status === 'open' ? 'Close' : 'Reopen' }}</button></form>
            <form method="POST" action="{{ route('finance.periods.destroy', $p) }}" onsubmit="return confirm('Remove?')">@csrf @method('DELETE')<button type="submit" class="danger">✕</button></form>
        </div></td></tr>
        @empty<tr><td colspan="5" class="empty-state">No periods.</td></tr>@endforelse</tbody>
    </table></div><div class="table-pager"><span class="pager-info">{{ $periods->total() }} periods</span><div class="pager-pages">{{ $periods->links('pagination.pager') }}</div></div></div>
@endsection
