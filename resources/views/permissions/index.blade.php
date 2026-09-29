@extends('layouts.app')
@section('title', 'Permissions')
@section('content')
    <div class="view-head"><div><h2>Permissions</h2><p class="sub">Tick what each role may do. Administrators bypass everything.</p></div></div>
    <form method="POST" action="{{ route('permissions.update') }}">@csrf @method('PUT')
        <div class="table-card"><div class="table-scroll"><table style="min-width:760px;">
            <thead><tr><th>Permission</th>@foreach($roles as $r)<th style="text-align:center;">{{ $r->name }}</th>@endforeach</tr></thead>
            <tbody>
                @foreach($permissions as $module => $perms)
                <tr><td colspan="{{ 1 + $roles->count() }}" class="cell-sub" style="padding:10px 18px;background:var(--sand-50);text-transform:uppercase;font-weight:700;">{{ ucfirst($module) }}</td></tr>
                @foreach($perms as $p)
                <tr><td>{{ $p->name }}<div class="cell-sub">{{ $p->slug }}</div></td>
                    @foreach($roles as $r)
                    <td style="text-align:center;"><input type="checkbox" name="matrix[{{ $r->slug }}][{{ $p->slug }}]" value="1" {{ $r->permissions->contains('id', $p->id) ? 'checked' : '' }} style="width:17px;height:17px;accent-color:var(--terracotta-600);"></td>
                    @endforeach
                </tr>
                @endforeach
                @endforeach
            </tbody>
        </table></div>
        <div class="table-pager"><span class="pager-info">{{ $roles->count() }} roles · {{ $permissions->flatten()->count() }} permissions</span><button class="btn btn-primary" type="submit">Save matrix</button></div>
        </div>
    </form>
@endsection
