@extends('layouts.app')
@section('title', 'My Investments')
@section('content')
    <div class="view-head">
        <div><h2>My investments</h2><p class="sub">{{ $member->name }} · Track placements, maturity and payouts.</p></div>
        <div class="view-actions"><a href="{{ route('portal.home') }}" class="btn btn-ghost">Portal</a></div>
    </div>
    <div class="table-card">
        <div class="table-toolbar"><span class="chip active">Placements ({{ $investments->total() }})</span></div>
        <div class="table-scroll"><table>
            <thead><tr><th>No</th><th>Amount</th><th>Expected return</th><th>Start</th><th>Maturity</th><th>Status</th></tr></thead>
            <tbody>
                @forelse($investments as $i)
                <tr><td class="cell-title">{{ $i->investment_no }}</td><td>@money($i->amount)</td><td>@money($i->expected_return)</td><td>{{ $i->start_date?->format('d M Y') }}</td><td>{{ $i->maturity_date?->format('d M Y') ?? '—' }}</td><td><span class="tag {{ status_badge($i->status) }}">{{ ucfirst($i->status) }}</span></td></tr>
                @empty<tr><td colspan="6" class="empty-state">No investments yet.</td></tr>@endforelse
            </tbody>
        </table></div>
        <div class="table-pager"><span class="pager-info">Showing {{ $investments->count() }} of {{ $investments->total() }}</span><div class="pager-pages">{{ $investments->links('pagination.pager') }}</div></div>
    </div>
    <div class="table-card">
        <div class="table-toolbar"><span class="chip active">Recent payouts</span></div>
        <div class="table-scroll"><table>
            <thead><tr><th>Date</th><th>Investment</th><th>Amount</th><th>Status</th></tr></thead>
            <tbody>
                @forelse($payouts as $p)
                <tr><td>{{ ($p->paid_at ?? $p->created_at)->format('d M Y') }}</td><td class="cell-title">{{ $p->investment->investment_no ?? '—' }}</td><td>@money($p->net_cash ?? $p->amount)</td><td><span class="tag {{ status_badge($p->status) }}">{{ ucfirst($p->status) }}</span></td></tr>
                @empty<tr><td colspan="4" class="empty-state">No payouts yet.</td></tr>@endforelse
            </tbody>
        </table></div>
    </div>
@endsection
