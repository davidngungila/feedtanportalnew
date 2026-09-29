@extends('layouts.app')
@section('title', 'Journal Entries')
@section('content')
    <div class="view-head"><div><h2>Journal Entries</h2><p class="sub">Double-entry book — debits must equal credits.</p></div><div class="view-actions"><a href="{{ route('finance.journals.create') }}" class="btn btn-primary">+ New journal</a></div></div>
    <div class="table-card">
        <div class="table-toolbar"><div class="chip-filters">
            <a href="{{ route('finance.journals') }}" class="chip {{ $status === 'all' ? 'active' : '' }}">All</a>
            <a href="{{ route('finance.journals', ['status' => 'posted']) }}" class="chip {{ $status === 'posted' ? 'active' : '' }}">Posted</a>
            <a href="{{ route('finance.journals', ['status' => 'draft']) }}" class="chip {{ $status === 'draft' ? 'active' : '' }}">Draft</a>
        </div></div>
        <div class="table-scroll"><table>
            <thead><tr><th>Reference</th><th>Description</th><th>Date</th><th>Lines</th><th>Status</th><th style="text-align:right;">Open</th></tr></thead>
            <tbody>@forelse($entries as $e)<tr><td><div class="cell-title"><a href="{{ route('finance.journals.show', $e) }}">{{ $e->reference }}</a></div>@if($e->source_type)<div class="cell-sub">Auto-posted</div>@endif</td>
            <td>{{ $e->description }}</td><td>{{ $e->entry_date?->format('d M Y') }}</td><td>{{ $e->lines_count }}</td>
            <td><span class="tag {{ status_badge($e->status) }}">{{ ucfirst($e->status) }}</span></td>
            <td><div class="row-actions"><a href="{{ route('finance.journals.show', $e) }}"><button type="button">↗</button></a></div></td></tr>
            @empty<tr><td colspan="6" class="empty-state">No journals.</td></tr>@endforelse</tbody>
        </table></div>
        <div class="table-pager"><span class="pager-info">{{ $entries->total() }} entries</span><div class="pager-pages">{{ $entries->links('pagination.pager') }}</div></div>
    </div>
@endsection
