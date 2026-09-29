@extends('layouts.app')
@section('title', $group->name)
@section('content')
    <div class="view-head"><div><h2>{{ $group->name }}</h2><p class="sub">{{ $group->members->count() }} members</p></div><div class="view-actions"><a href="{{ route('member-groups.index') }}" class="btn btn-ghost">Back</a><a href="{{ route('member-groups.edit', $group) }}" class="btn btn-primary">Edit group</a></div></div>
    <div class="table-card"><div class="table-scroll"><table>
        <thead><tr><th>Member</th><th>Phone</th><th>Status</th></tr></thead>
        <tbody>@forelse($group->members as $m)<tr><td><a href="{{ route('members.show', $m) }}" class="cell-title">{{ $m->name }}</a></td><td>{{ $m->phone }}</td><td><span class="tag {{ status_badge($m->status) }}">{{ ucfirst($m->status) }}</span></td></tr>@empty<tr><td colspan="3" class="empty-state">No members in this group yet. Assign groups on the member registration/edit page.</td></tr>@endforelse</tbody>
    </table></div></div>
@endsection
