@extends('layouts.app')
@section('title', 'Budgets')
@section('content')
    <div class="view-head"><div><h2>Budgets</h2><p class="sub">Planned vs actual (posted) per account and period.</p></div><div class="view-actions"><a href="{{ route('finance.budgets.create') }}" class="btn btn-primary">+ New budget</a></div></div>
    <div class="table-card"><div class="table-scroll"><table>
        <thead><tr><th>Account</th><th>Period</th><th>Budgeted</th><th>Actual</th><th>Variance</th><th style="text-align:right;">Remove</th></tr></thead>
        <tbody>@forelse($budgets as $b)
            @php $actual = $b->actual(); $var = $b->budgeted - $actual; @endphp
            <tr><td class="cell-title">{{ $b->account->name ?? '—' }}</td><td>{{ $b->period->name ?? 'All time' }}</td><td>@money($b->budgeted)</td><td>@money($actual)</td><td class="cell-title">@money($var)</td>
            <td><form method="POST" action="{{ route('finance.budgets.destroy', $b) }}" onsubmit="return confirm('Remove?')">@csrf @method('DELETE')<div class="row-actions"><button type="submit" class="danger">✕</button></div></form></td></tr>
        @empty<tr><td colspan="6" class="empty-state">No budgets.</td></tr>@endforelse</tbody>
    </table></div><div class="table-pager"><span class="pager-info">{{ $budgets->total() }} budgets</span><div class="pager-pages">{{ $budgets->links('pagination.pager') }}</div></div></div>
@endsection
