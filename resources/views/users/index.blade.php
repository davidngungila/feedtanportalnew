@extends('layouts.app')

@section('title', 'Users & Roles')

@section('content')
    <div class="view-head"><div><h2>Users &amp; Roles</h2><p class="sub">Administrator, Chairperson, Secretary, Accountant, SWF / Deposit / Investment / Loan officers, Member. One user can hold 2+ officer roles.</p></div><div class="view-actions"><a href="{{ route('users.create') }}" class="btn btn-primary">+ New user</a></div></div>

    <form method="GET" action="{{ route('users.index') }}">
        <div class="table-card">
            <div class="table-toolbar"><div class="table-search"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"></circle></svg><input name="q" value="{{ $q }}" placeholder="Search name/email…" onchange="this.form.submit()"></div></div>
            <div class="table-scroll"><table>
                <thead><tr><th>Name</th><th>Email</th><th>Roles</th><th style="text-align:right;">Actions</th></tr></thead>
                <tbody>
                    @forelse($users as $u)
                    <tr><td><div class="cell-main"><div class="avatar" style="overflow:hidden;">@if($u->avatarUrl())<img src="{{ $u->avatarUrl() }}" alt="">@else{{ strtoupper(substr($u->name,0,1)) }}@endif</div><div class="cell-title">{{ $u->name }}</div></div></td>
                    <td>{{ $u->email }}</td>
                    <td>@foreach($u->roles as $r)<span class="tag {{ $r->slug === 'administrator' ? 'tag-gold' : 'tag-green' }}" style="margin-right:4px;">{{ $r->name }}</span>@endforeach</td>
                    <td><div class="row-actions">
                        <a href="{{ route('users.edit', $u) }}"><button type="button">Edit</button></a>
                        @if($u->id !== auth()->id())
                        <form method="POST" action="{{ route('users.destroy',$u) }}" onsubmit="return confirm('Remove user?')">@csrf @method('DELETE')<button type="submit" class="danger">✕</button></form>
                        @endif
                    </div></td></tr>
                    @empty<tr><td colspan="4" class="empty-state">No users.</td></tr>@endforelse
                </tbody>
            </table></div>
            <div class="table-pager"><span class="pager-info">{{ $users->total() }} users</span><div class="pager-pages">{{ $users->links('pagination.pager') }}</div></div>
        </div>
    </form>
@endsection
