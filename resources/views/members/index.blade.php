@extends('layouts.app')

@section('title', 'Members')

@section('content')
    <div class="view-head">
        <div><h2>All Members</h2><p class="sub">Register members and track each member's loans, deposits, investments and SWF.</p></div>
        <div class="view-actions"><a href="{{ route('members.create') }}" class="btn btn-primary">+ New member</a></div>
    </div>

    <form method="GET" action="{{ route('members.index') }}">
        <div class="table-card">
            <div class="table-toolbar">
                <div class="chip-filters">
                    <a href="{{ route('members.index', ['q' => $q, 'type_id' => $typeId]) }}" class="chip {{ $status === 'all' ? 'active' : '' }}">All</a>
                    <a href="{{ route('members.index', ['q' => $q, 'status' => 'active', 'type_id' => $typeId]) }}" class="chip {{ $status === 'active' ? 'active' : '' }}">Active</a>
                    <a href="{{ route('members.index', ['q' => $q, 'status' => 'inactive', 'type_id' => $typeId]) }}" class="chip {{ $status === 'inactive' ? 'active' : '' }}">Inactive</a>
                </div>
                <select name="type_id" onchange="this.form.submit()" style="padding:9px 12px;border:1.5px solid var(--line);border-radius:10px;font-size:13px;background:var(--white);font-weight:600;">
                    <option value="all">All types</option>
                    @foreach($types as $t)<option value="{{ $t->id }}" {{ (string)$typeId === (string)$t->id ? 'selected' : '' }}>{{ $t->name }}</option>@endforeach
                </select>
                <div class="table-search">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                    <input type="text" name="q" value="{{ $q }}" placeholder="Search name, no, phone…" onchange="this.form.submit()">
                </div>
            </div>
            <div class="table-scroll"><table>
                <thead><tr><th>Member</th><th>Type</th><th>Contact</th><th>Loans</th><th>Savings</th><th>Status</th><th style="text-align:right;">Open</th></tr></thead>
                <tbody>
                    @forelse($members as $m)
                    <tr>
                        <td><div class="cell-main"><div class="avatar">{{ strtoupper(substr($m->name,0,1)) }}</div><div><div class="cell-title"><a href="{{ route('members.show', $m) }}">{{ $m->name }}</a></div><div class="cell-sub">{{ $m->member_no }} · Joined {{ $m->join_date?->format('d M Y') }}</div></div></div></td>
                        <td>{{ $m->memberType->name ?? '—' }}</td>
                        <td><div class="cell-title">{{ $m->phone }}</div><div class="cell-sub">{{ $m->email ?? '—' }}</div></td>
                        <td>{{ $m->loans_count }}</td>
                        <td class="cell-title">@money($m->savingsBalance())</td>
                        <td><span class="tag {{ status_badge($m->status) }}">{{ ucfirst($m->status) }}</span></td>
                        <td><div class="row-actions"><a href="{{ route('members.show', $m) }}"><button type="button" title="Open">↗</button></a></div></td>
                    </tr>
                    @empty<tr><td colspan="7" class="empty-state">No members found. <a href="{{ route('members.create') }}">Register the first member</a>.</td></tr>@endforelse
                </tbody>
            </table></div>
            <div class="table-pager"><span class="pager-info">Showing {{ $members->count() }} of {{ $members->total() }}</span><div class="pager-pages">{{ $members->links('pagination.pager') }}</div></div>
        </div>
    </form>
@endsection
