@extends('layouts.app')
@section('title', 'Access Logs')
@section('content')
    <div class="view-head"><div><h2>Access Logs</h2><p class="sub">Logins, logouts and failed attempts.</p></div></div>
    <div class="table-card">
        <div class="table-toolbar"><div class="chip-filters">
            <a href="{{ route('access.index') }}" class="chip {{ $event === 'all' ? 'active' : '' }}">All</a>
            <a href="{{ route('access.index', ['event' => 'login']) }}" class="chip {{ $event === 'login' ? 'active' : '' }}">Logins</a>
            <a href="{{ route('access.index', ['event' => 'logout']) }}" class="chip {{ $event === 'logout' ? 'active' : '' }}">Logouts</a>
            <a href="{{ route('access.index', ['event' => 'failed']) }}" class="chip {{ $event === 'failed' ? 'active' : '' }}">Failed</a>
        </div></div>
        <div class="table-scroll"><table>
            <thead><tr><th>When</th><th>User</th><th>Event</th><th>IP</th></tr></thead>
            <tbody>@forelse($logs as $l)<tr><td class="cell-sub">{{ $l->created_at->format('d M Y H:i') }}</td><td class="cell-title">{{ $l->user->name ?? $l->email ?? '—' }}</td>
            <td><span class="tag {{ $l->event === 'login' ? 'tag-green' : ($l->event === 'failed' ? 'tag-red' : 'tag-grey') }}">{{ ucfirst($l->event) }}</span></td>
            <td class="cell-sub">{{ $l->ip_address ?? '—' }}</td></tr>
            @empty<tr><td colspan="4" class="empty-state">No access events yet.</td></tr>@endforelse</tbody>
        </table></div>
        <div class="table-pager"><span class="pager-info">{{ $logs->total() }} events</span><div class="pager-pages">{{ $logs->links('pagination.pager') }}</div></div>
    </div>
@endsection
