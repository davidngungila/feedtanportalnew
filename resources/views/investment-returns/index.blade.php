@extends('layouts.app')
@section('title', 'Investment Returns')
@section('content')
    <div class="view-head"><div><h2>Investment Returns</h2><p class="sub">Actual return payouts to members. Total paid: @money($total)</p></div><div class="view-actions"><a href="{{ route('investment-returns.create') }}" class="btn btn-primary">+ Pay return</a></div></div>
    <div class="table-card"><div class="table-scroll"><table>
        <thead><tr><th>Investment</th><th>Member</th><th>Amount</th><th>Date</th><th style="text-align:right;">Remove</th></tr></thead>
        <tbody>@forelse($returns as $r)<tr><td><a class="cell-title" href="{{ route('investments.show', $r->investment) }}">{{ $r->investment->investment_no ?? '#' . $r->investment_id }}</a></td><td>{{ $r->investment->member->name ?? '—' }}</td><td class="cell-title">@money($r->amount)</td><td>{{ $r->paid_at?->format('d M Y') }}</td>
        <td><form method="POST" action="{{ route('investment-returns.destroy', $r) }}" onsubmit="return confirm('Remove?')">@csrf @method('DELETE')<div class="row-actions"><button type="submit" class="danger">✕</button></div></form></td></tr>
        @empty<tr><td colspan="5" class="empty-state">No returns paid.</td></tr>@endforelse</tbody>
    </table></div><div class="table-pager"><span class="pager-info">{{ $returns->total() }} payments</span><div class="pager-pages">{{ $returns->links('pagination.pager') }}</div></div></div>
@endsection
