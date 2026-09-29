@extends('layouts.app')

@section('title', $title ?? 'SWF')

@section('content')
    <div class="view-head">
        <div><h2>{{ $title ?? 'SWF' }}</h2><p class="sub">Filtered SWF ledger — individual pages, no popups.</p></div>
        <div class="view-actions"><a href="{{ route('swf.create') }}" class="btn btn-primary">+ New entry</a></div>
    </div>
    <div class="table-card">
        <div class="table-scroll"><table>
            <thead><tr><th>Receipt</th><th>Member</th><th>Type</th><th>Amount</th><th>Date</th><th>Reason</th><th style="text-align:right;">Actions</th></tr></thead>
            <tbody>
                @forelse($entries as $e)
                <tr><td class="cell-title">{{ $e->receipt_no }}</td><td>{{ $e->member->name ?? '—' }}</td>
                <td><span class="tag {{ status_badge($e->type) }}">{{ ucfirst($e->type) }}</span></td>
                <td class="cell-title">@money($e->amount)</td><td>{{ $e->transacted_at?->format('d M Y') }}</td><td>{{ $e->reason ?? '—' }}</td>
                <td><div class="row-actions"><a href="{{ route('swf.edit', $e) }}"><button type="button">Edit</button></a><form method="POST" action="{{ route('swf.destroy',$e) }}" onsubmit="return confirm('Remove?')">@csrf @method('DELETE')<button class="danger" type="submit">✕</button></form></div></td></tr>
                @empty<tr><td colspan="7" class="empty-state">No entries in this view.</td></tr>@endforelse
            </tbody>
        </table></div>
        <div class="table-pager"><span class="pager-info">{{ $entries->total() }} entries</span><div class="pager-pages">{{ $entries->links('pagination.pager') }}</div></div>
    </div>
@endsection
