@extends('layouts.app')
@section('title', 'User Activity')
@section('content')
    <div class="view-head"><div><h2>User Activity</h2><p class="sub">Every create / update / delete across the portal, auto-logged.</p></div></div>
    <form method="GET" action="{{ route('activity.index') }}">
        <div class="table-card">
            <div class="table-toolbar"><div class="table-search"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"></circle></svg><input name="q" value="{{ $q }}" placeholder="Search user/action…" onchange="this.form.submit()"></div></div>
            <div class="table-scroll"><table>
                <thead><tr><th>When</th><th>User</th><th>Action</th><th>IP</th></tr></thead>
                <tbody>@forelse($logs as $l)<tr><td><div class="cell-sub">{{ $l->created_at->format('d M Y H:i') }}</div><div class="cell-sub">{{ $l->created_at->diffForHumans() }}</div></td><td class="cell-title">{{ $l->user->name ?? '—' }}</td><td><span class="tag tag-grey">{{ $l->action }}</span><div class="cell-sub">{{ $l->subject ?? '' }}</div></td><td class="cell-sub">{{ $l->ip_address ?? '—' }}</td></tr>
                @empty<tr><td colspan="4" class="empty-state">No activity yet — it starts logging from now.</td></tr>@endforelse</tbody>
            </table></div>
            <div class="table-pager"><span class="pager-info">{{ $logs->total() }} events</span><div class="pager-pages">{{ $logs->links('pagination.pager') }}</div></div>
        </div>
    </form>
@endsection
