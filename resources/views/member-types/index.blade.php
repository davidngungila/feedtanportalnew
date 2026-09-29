@extends('layouts.app')
@section('title', 'Member Types')
@section('content')
    <div class="view-head"><div><h2>Member Types</h2><p class="sub">Classify members, e.g. Ordinary, Associate, Youth.</p></div><div class="view-actions"><a href="{{ route('member-types.create') }}" class="btn btn-primary">+ New type</a></div></div>
    <div class="table-card"><div class="table-scroll"><table>
        <thead><tr><th>Name</th><th>Description</th><th>Members</th><th>Status</th><th style="text-align:right;">Actions</th></tr></thead>
        <tbody>@forelse($types as $t)<tr><td class="cell-title">{{ $t->name }}</td><td>{{ $t->description ?? '—' }}</td><td>{{ $t->members_count }}</td><td><span class="tag {{ status_badge($t->status) }}">{{ ucfirst($t->status) }}</span></td>
        <td><div class="row-actions"><a href="{{ route('member-types.edit', $t) }}"><button type="button">Edit</button></a><form method="POST" action="{{ route('member-types.destroy', $t) }}" onsubmit="return confirm('Remove?')">@csrf @method('DELETE')<button type="submit" class="danger">✕</button></form></div></td></tr>
        @empty<tr><td colspan="5" class="empty-state">No member types. <a href="{{ route('member-types.create') }}">Create one</a>.</td></tr>@endforelse</tbody>
    </table></div><div class="table-pager"><span class="pager-info">{{ $types->total() }} types</span><div class="pager-pages">{{ $types->links('pagination.pager') }}</div></div></div>
@endsection
