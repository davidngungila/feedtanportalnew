@extends('layouts.app')
@section('title', 'Member Groups')
@section('content')
    <div class="view-head"><div><h2>Member Groups</h2><p class="sub">Group members, e.g. by ward, cohort or savings circle.</p></div><div class="view-actions"><a href="{{ route('member-groups.create') }}" class="btn btn-primary">+ New group</a></div></div>
    <div class="table-card"><div class="table-scroll"><table>
        <thead><tr><th>Name</th><th>Description</th><th>Members</th><th>Status</th><th style="text-align:right;">Actions</th></tr></thead>
        <tbody>@forelse($groups as $g)<tr><td class="cell-title"><a href="{{ route('member-groups.show', $g) }}">{{ $g->name }}</a></td><td>{{ $g->description ?? '—' }}</td><td>{{ $g->members_count }}</td><td><span class="tag {{ status_badge($g->status) }}">{{ ucfirst($g->status) }}</span></td>
        <td><div class="row-actions"><a href="{{ route('member-groups.edit', $g) }}"><button type="button">Edit</button></a><form method="POST" action="{{ route('member-groups.destroy', $g) }}" onsubmit="return confirm('Remove?')">@csrf @method('DELETE')<button type="submit" class="danger">✕</button></form></div></td></tr>
        @empty<tr><td colspan="5" class="empty-state">No groups.</td></tr>@endforelse</tbody>
    </table></div><div class="table-pager"><span class="pager-info">{{ $groups->total() }} groups</span><div class="pager-pages">{{ $groups->links('pagination.pager') }}</div></div></div>
@endsection
