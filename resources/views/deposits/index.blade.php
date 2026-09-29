@extends('layouts.app')

@section('title', $pageTitle ?? 'Deposits')

@section('content')
    <div class="view-head">
        <div><h2>{{ $pageTitle ?? 'All Deposits' }}</h2><p class="sub">Member savings deposits and withdrawals — individual pages, no popups.</p></div>
        <div class="view-actions"><a href="{{ route('deposits.create') }}" class="btn btn-primary">+ New entry</a></div>
    </div>

    <div class="stat-grid">
        <div class="stat-card" style="--stat-tint:var(--acacia-100);--stat-fg:var(--acacia-600);"><div class="stat-top"><div class="stat-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2v20"></path><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path></svg></div></div><div class="stat-value">@money($totalIn)</div><div class="stat-label">Total deposits</div></div>
        <div class="stat-card" style="--stat-tint:var(--gold-100);--stat-fg:#8a6418;"><div class="stat-top"><div class="stat-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 11 12 4l9 7"></path></svg></div></div><div class="stat-value">@money($totalOut)</div><div class="stat-label">Total withdrawals</div></div>
        <div class="stat-card" style="--stat-tint:var(--terracotta-100);--stat-fg:var(--terracotta-600);"><div class="stat-top"><div class="stat-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 17l9-9 5 5 4-4"></path></svg></div></div><div class="stat-value">@money($totalIn - $totalOut)</div><div class="stat-label">Net savings</div></div>
    </div>

    <form method="GET" action="{{ ($pageTitle ?? '') === 'Withdrawals' ? route('deposits.withdrawals') : route('deposits.index') }}">
        <div class="table-card">
            <div class="table-toolbar">
                <div class="chip-filters">
                    <a href="{{ route('deposits.index', ['q'=>$q]) }}" class="chip {{ $type==='all'?'active':'' }}">All</a>
                    <a href="{{ route('deposits.index', ['type'=>'deposit','q'=>$q]) }}" class="chip {{ $type==='deposit'?'active':'' }}">Deposits</a>
                    <a href="{{ route('deposits.withdrawals', ['q'=>$q]) }}" class="chip {{ $type==='withdrawal'?'active':'' }}">Withdrawals</a>
                </div>
                <div class="table-search"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"></circle></svg><input type="text" name="q" value="{{ $q }}" placeholder="Search…" onchange="this.form.submit()"></div>
            </div>
            <div class="table-scroll"><table>
                <thead><tr><th>Receipt</th><th>Member</th><th>Product</th><th>Type</th><th>Amount</th><th>Date</th><th style="text-align:right;">Actions</th></tr></thead>
                <tbody>
                    @forelse($deposits as $d)
                    <tr><td><div class="cell-title">{{ $d->receipt_no }}</div><div class="cell-sub">{{ $d->method }}</div></td>
                    <td><div class="cell-title">{{ $d->member->name ?? '—' }}</div><div class="cell-sub">{{ $d->member->phone ?? '' }}</div></td>
                    <td>{{ $d->product->name ?? '—' }}</td>
                    <td><span class="tag {{ $d->type==='deposit'?'tag-green':'tag-gold' }}">{{ ucfirst($d->type) }}</span></td>
                    <td class="cell-title">@money($d->amount)</td><td>{{ $d->transacted_at?->format('d M Y') }}</td>
                    <td><div class="row-actions"><a href="{{ route('deposits.edit', $d) }}"><button type="button">Edit</button></a><form method="POST" action="{{ route('deposits.destroy',$d) }}" onsubmit="return confirm('Remove entry?')">@csrf @method('DELETE')<button type="submit" class="danger" title="Remove">✕</button></form></div></td></tr>
                    @empty<tr><td colspan="7" class="empty-state">No entries.</td></tr>@endforelse
                </tbody>
            </table></div>
            <div class="table-pager"><span class="pager-info">{{ $deposits->total() }} entries</span><div class="pager-pages">{{ $deposits->links('pagination.pager') }}</div></div>
        </div>
    </form>
@endsection
