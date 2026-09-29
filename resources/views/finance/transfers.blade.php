@extends('layouts.app')
@section('title', 'Transfers')
@section('content')
    <div class="view-head"><div><h2>Transfers</h2><p class="sub">Move money between accounts — each transfer posts a journal.</p></div><div class="view-actions"><a href="{{ route('finance.transfers.create') }}" class="btn btn-primary">+ New transfer</a></div></div>
    <div class="table-card"><div class="table-scroll"><table>
        <thead><tr><th>Reference</th><th>From → To</th><th>Amount</th><th>Date</th><th style="text-align:right;">Remove</th></tr></thead>
        <tbody>@forelse($transfers as $t)<tr><td><div class="cell-title">{{ $t->reference }}</div><div class="cell-sub">{{ $t->notes ?? '—' }}</div></td>
        <td>{{ $t->fromAccount->name ?? '—' }} → {{ $t->toAccount->name ?? '—' }}</td><td class="cell-title">@money($t->amount)</td><td>{{ $t->transferred_at?->format('d M Y') }}</td>
        <td><form method="POST" action="{{ route('finance.transfers.destroy', $t) }}" onsubmit="return confirm('Remove and reverse journal?')">@csrf @method('DELETE')<div class="row-actions"><button type="submit" class="danger">✕</button></div></form></td></tr>
        @empty<tr><td colspan="5" class="empty-state">No transfers.</td></tr>@endforelse</tbody>
    </table></div><div class="table-pager"><span class="pager-info">{{ $transfers->total() }} transfers</span><div class="pager-pages">{{ $transfers->links('pagination.pager') }}</div></div></div>
@endsection
