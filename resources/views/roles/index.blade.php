@extends('layouts.app')
@section('title', 'Roles')
@section('content')
    <div class="view-head"><div><h2>Roles</h2><p class="sub">One user can hold 2+ officer roles — access combines.</p></div><div class="view-actions"><a href="{{ route('roles.create') }}" class="btn btn-primary">+ New role</a></div></div>
    <div class="table-card"><div class="table-scroll"><table>
        <thead><tr><th>Role</th><th>Slug</th><th>Users</th><th>Permissions</th><th style="text-align:right;">Actions</th></tr></thead>
        <tbody>@forelse($roles as $r)<tr><td class="cell-title">{{ $r->name }}</td><td><span class="tag tag-grey">{{ $r->slug }}</span></td><td>{{ $r->users_count }}</td><td>{{ $r->permissions_count }}</td>
        <td><div class="row-actions"><a href="{{ route('roles.edit', $r) }}"><button type="button">Edit</button></a>@if($r->slug !== 'administrator')<form method="POST" action="{{ route('roles.destroy', $r) }}" onsubmit="return confirm('Remove role?')">@csrf @method('DELETE')<button type="submit" class="danger">✕</button></form>@endif</div></td></tr>
        @empty<tr><td colspan="5" class="empty-state">No roles.</td></tr>@endforelse</tbody>
    </table></div><div class="table-pager"><span class="pager-info">{{ $roles->total() }} roles</span><div class="pager-pages">{{ $roles->links('pagination.pager') }}</div></div></div>
@endsection
