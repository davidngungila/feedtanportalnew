@extends('layouts.app')

@section('title', $status === 'matured' ? 'Matured Investments' : ($status === 'active' ? 'Active Investments' : 'Investments'))

@section('content')
    <div class="view-head">
        <div><h2>{{ $status === 'matured' ? 'Matured Investments' : ($status === 'active' ? 'Active Investments' : 'All Investments') }}</h2><p class="sub">Member investment plans, expected returns and maturities — individual pages, no popups.</p></div>
        <div class="view-actions">@if($status === 'matured')<a href="{{ route('investments.matured.import') }}" class="btn btn-ghost">⇪ Import Excel</a>@endif<a href="{{ route('investments.create') }}" class="btn btn-primary">+ New investment</a></div>
    </div>

    <div class="stat-grid">
        <div class="stat-card" style="--stat-tint:var(--acacia-100);--stat-fg:var(--acacia-600);"><div class="stat-top"><div class="stat-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 17l9-9 5 5 4-4"></path></svg></div></div><div class="stat-value">@money($totalActive)</div><div class="stat-label">Active investments</div></div>
    </div>

    <form method="GET" action="{{ request()->url() }}">
        <div class="table-card">
            <div class="table-toolbar">
                <div class="chip-filters">
                    <a href="{{ route('investments.index', ['q'=>$q]) }}" class="chip {{ $status==='all'?'active':'' }}">All</a>
                    <a href="{{ route('investments.active', ['q'=>$q]) }}" class="chip {{ $status==='active'?'active':'' }}">Active</a>
                    <a href="{{ route('investments.matured', ['q'=>$q]) }}" class="chip {{ $status==='matured'?'active':'' }}">Matured</a>
                    <a href="{{ route('investments.index', ['status'=>'withdrawn','q'=>$q]) }}" class="chip {{ $status==='withdrawn'?'active':'' }}">Withdrawn</a>
                </div>
                <div class="table-search"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"></circle></svg><input name="q" value="{{ $q }}" placeholder="Search…" onchange="this.form.submit()"></div>
            </div>
            <div class="table-scroll"><table>
                <thead><tr><th>No</th><th>Member</th><th>Product</th><th>Amount</th><th>Return</th><th>Period</th><th>Status</th><th style="text-align:right;">Actions</th></tr></thead>
                <tbody>
                    @forelse($investments as $i)
                    <tr><td><div class="cell-title"><a href="{{ route('investments.show', $i) }}">{{ $i->investment_no }}</a></div><div class="cell-sub">{{ $i->plan ?? 'Plan' }}</div></td>
                    <td>@if($i->member)<a href="{{ route('investments.member', $i->member) }}">{{ $i->member->name }}</a>@else — @endif</td><td>{{ $i->product->name ?? '—' }}</td><td class="cell-title">@money($i->amount)</td><td>@money($i->expected_return) <span class="cell-sub">({{ $i->expected_return_rate }}%)</span></td>
                    <td><div class="cell-sub">{{ $i->start_date?->format('d M Y') }} → {{ $i->maturity_date?->format('d M Y') ?? '—' }}</div></td>
                    <td><span class="tag {{ status_badge($i->status) }}">{{ ucfirst($i->status) }}</span></td>
                    <td><div class="row-actions"><a href="{{ route('investments.show', $i) }}"><button type="button">↗</button></a><a href="{{ route('investments.edit', $i) }}"><button type="button">Edit</button></a></div></td></tr>
                    @empty<tr><td colspan="8" class="empty-state">No investments. <a href="{{ route('investments.create') }}">Record one</a>.</td></tr>@endforelse
                </tbody>
            </table></div>
            <div class="table-pager"><span class="pager-info">{{ $investments->total() }} investments</span><div class="pager-pages">{{ $investments->links('pagination.pager') }}</div></div>
        </div>
    </form>
@endsection
