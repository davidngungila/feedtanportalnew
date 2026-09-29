@extends('layouts.app')

@section('title', 'SWF')

@section('content')
    <div class="view-head">
        <div><h2>SWF — Social Welfare Fund</h2><p class="sub">All entries. Use the sidebar for Accounts, Contributions, Deductions, Claims, Statements.</p></div>
        <div class="view-actions"><a href="{{ route('swf.create') }}" class="btn btn-primary">+ New entry</a></div>
    </div>

    <div class="stat-grid">
        <div class="stat-card" style="--stat-tint:var(--acacia-100);--stat-fg:var(--acacia-600);"><div class="stat-top"><div class="stat-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg></div></div><div class="stat-value">@money($totalIn)</div><div class="stat-label">Contributions</div></div>
        <div class="stat-card" style="--stat-tint:var(--gold-100);--stat-fg:#8a6418;"><div class="stat-top"><div class="stat-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 11 12 4l9 7"></path></svg></div></div><div class="stat-value">@money($totalDeductions + $totalPayouts + $totalClaims)</div><div class="stat-label">Deductions + payouts + claims</div></div>
        <div class="stat-card" style="--stat-tint:var(--terracotta-100);--stat-fg:var(--terracotta-600);"><div class="stat-top"><div class="stat-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 17l9-9 5 5 4-4"></path></svg></div></div><div class="stat-value">@money($totalIn - $totalDeductions - $totalPayouts - $totalClaims)</div><div class="stat-label">SWF balance</div></div>
    </div>

    <form method="GET" action="{{ route('swf.index') }}">
        <div class="table-card">
            <div class="table-toolbar">
                <div class="chip-filters">
                    <a href="{{ route('swf.index', ['q'=>$q]) }}" class="chip {{ $type==='all'?'active':'' }}">All</a>
                    <a href="{{ route('swf.contributions', ['q'=>$q]) }}" class="chip {{ $type==='contribution'?'active':'' }}">Contributions</a>
                    <a href="{{ route('swf.deductions', ['q'=>$q]) }}" class="chip {{ $type==='deduction'?'active':'' }}">Deductions</a>
                    <a href="{{ route('swf.claims', ['q'=>$q]) }}" class="chip {{ in_array($type, ['claim','payout']) ?'active':'' }}">Claims</a>
                </div>
                <div class="table-search"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"></circle></svg><input name="q" value="{{ $q }}" placeholder="Search…" onchange="this.form.submit()"></div>
            </div>
            <div class="table-scroll"><table>
                <thead><tr><th>Receipt</th><th>Member</th><th>Type</th><th>Amount</th><th>Date</th><th>Reason</th><th style="text-align:right;">Actions</th></tr></thead>
                <tbody>
                    @forelse($entries as $e)
                    <tr><td class="cell-title">{{ $e->receipt_no }}</td><td>{{ $e->member->name ?? '—' }}</td>
                    <td><span class="tag {{ status_badge($e->type) }}">{{ ucfirst($e->type) }}</span></td>
                    <td class="cell-title">@money($e->amount)</td><td>{{ $e->transacted_at?->format('d M Y') }}</td><td>{{ $e->reason ?? '—' }}</td>
                    <td><div class="row-actions"><a href="{{ route('swf.edit', $e) }}"><button type="button">Edit</button></a><form method="POST" action="{{ route('swf.destroy',$e) }}" onsubmit="return confirm('Remove?')">@csrf @method('DELETE')<button class="danger" type="submit">✕</button></form></div></td></tr>
                    @empty<tr><td colspan="7" class="empty-state">No SWF entries.</td></tr>@endforelse
                </tbody>
            </table></div>
            <div class="table-pager"><span class="pager-info">{{ $entries->total() }} entries</span><div class="pager-pages">{{ $entries->links('pagination.pager') }}</div></div>
        </div>
    </form>
@endsection
