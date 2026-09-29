@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <div class="view-head">
        <div>
            <h2>
                @if($isAdmin) Member Funds Dashboard
                @elseif($canLoans && !$canDeposits && !$canInvest && !$canSwf) Loan Officer Dashboard
                @elseif($canDeposits && !$canLoans && !$canInvest && !$canSwf) Deposit Officer Dashboard
                @elseif($canInvest && !$canLoans && !$canDeposits && !$canSwf) Investment Officer Dashboard
                @elseif($canSwf && !$canLoans && !$canDeposits && !$canInvest) SWF Officer Dashboard
                @else Member Funds Dashboard
                @endif
            </h2>
            <p class="sub">{{ now()->format('l, j F Y') }} · Loans, deposits, investments and SWF at a glance.</p>
        </div>
        <div class="view-actions">
            @if($canMembers)<a href="{{ route('members.create') }}" class="btn btn-primary">+ New member</a>@endif
        </div>
    </div>

    @if($ownMember && !$canLoans && !$canDeposits && !$canInvest && !$canSwf)
        @php $bal = member_balance($ownMember); @endphp
        <div class="balance-strip">
            <div class="balance-box"><div class="bb-label">My savings</div><div class="bb-amount">@money($bal['savings'])</div></div>
            <div class="balance-box"><div class="bb-label">My loans outstanding</div><div class="bb-amount">@money($bal['loan_outstanding'])</div></div>
            <div class="balance-box"><div class="bb-label">My investments</div><div class="bb-amount">@money($bal['invested'])</div></div>
            <div class="balance-box"><div class="bb-label">My SWF</div><div class="bb-amount">@money($bal['swf'])</div></div>
        </div>
        <div class="panel"><div class="panel-head"><h3>My account</h3><a class="link" href="{{ route('members.show', $ownMember) }}">Open profile</a></div>
        <div class="panel-body">Welcome, {{ $ownMember->name }} ({{ $ownMember->member_no }}). Your balances above are live.</div></div>
    @else
    <div class="stat-grid">
        @if($canMembers)
        <div class="stat-card" style="--stat-tint:var(--gold-100);--stat-fg:#8a6418;">
            <div class="stat-top"><div class="stat-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle></svg></div><span class="stat-trend up">{{ $activeMembers }} active</span></div>
            <div class="stat-value">{{ $memberCount }}</div>
            <div class="stat-label">Total members · {{ $pendingMemberApps }} pending applications</div>
        </div>
        @endif
        @if($canDeposits)
        <div class="stat-card" style="--stat-tint:var(--acacia-100);--stat-fg:var(--acacia-600);">
            <div class="stat-top"><div class="stat-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 7l1.5-2.5h17L22 7z"></path><path d="M3 7h18v13H3z"></path></svg></div><span class="stat-trend up">Savings</span></div>
            <div class="stat-value">@money($savingsBalance)</div>
            <div class="stat-label">Net member deposits</div>
        </div>
        @endif
        @if($canLoans)
        <div class="stat-card" style="--stat-tint:var(--terracotta-100);--stat-fg:var(--terracotta-600);">
            <div class="stat-top"><div class="stat-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2v20"></path><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path></svg></div><span class="stat-trend {{ $overdueLoans > 0 ? 'down' : 'up' }}">{{ $activeLoans }} active</span></div>
            <div class="stat-value">@money($loanOutstanding)</div>
            <div class="stat-label">Loans outstanding · {{ $overdueLoans }} overdue · {{ $pendingLoanApps }} applications</div>
        </div>
        @endif
        @if($canInvest)
        <div class="stat-card" style="--stat-tint:var(--sand-200);--stat-fg:var(--coffee-700);">
            <div class="stat-top"><div class="stat-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 17l9-9 5 5 4-4"></path></svg></div><span class="stat-trend up">{{ $activeInvestments }} active</span></div>
            <div class="stat-value">@money($invested)</div>
            <div class="stat-label">Investments · {{ $maturedInvestments }} matured</div>
        </div>
        @endif
        @if($canSwf)
        <div class="stat-card" style="--stat-tint:var(--danger-100);--stat-fg:var(--danger);">
            <div class="stat-top"><div class="stat-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg></div><span class="stat-trend up">SWF</span></div>
            <div class="stat-value">@money($swfBalance)</div>
            <div class="stat-label">Social Welfare Fund</div>
        </div>
        @endif
    </div>

    @if($canFinance)
    <div class="balance-strip">
        <div class="balance-box"><div class="bb-label">Books income</div><div class="bb-amount">@money($finIncome)</div><div class="bb-sub"><a href="{{ route('finance.income') }}">Income →</a></div></div>
        <div class="balance-box"><div class="bb-label">Books expenses</div><div class="bb-amount">@money($finExpenses)</div><div class="bb-sub"><a href="{{ route('finance.expenses') }}">Expenses →</a></div></div>
        <div class="balance-box"><div class="bb-label">Books surplus</div><div class="bb-amount">@money($finIncome - $finExpenses)</div><div class="bb-sub"><a href="{{ route('finance.income-statement') }}">Statement →</a></div></div>
        <div class="balance-box"><div class="bb-label">Books cash</div><div class="bb-amount">@money($finCash)</div><div class="bb-sub"><a href="{{ route('finance.overview') }}">Finance overview →</a></div></div>
    </div>
    @endif

    @if($isAdmin)
    <div class="panel-grid">
        <div class="panel">
            <div class="panel-head"><h3>Monthly movement</h3><span class="link">TZS</span></div>
            <div class="panel-body"><div style="position:relative;height:250px;"><canvas id="monthlyChart"></canvas></div></div>
        </div>
        <div class="panel">
            <div class="panel-head"><h3>Fund split</h3><span class="link">Now</span></div>
            <div class="panel-body">
                <div class="donut-wrap">
                    @php
                        $total = max(1, $savingsBalance + $loanOutstanding + $invested + $swfBalance);
                        $segs = [
                            ['label' => 'Savings', 'v' => max(0,$savingsBalance), 'c' => '#5E6E3F'],
                            ['label' => 'Loans out', 'v' => max(0,$loanOutstanding), 'c' => '#C2592B'],
                            ['label' => 'Investments', 'v' => max(0,$invested), 'c' => '#D4A24C'],
                            ['label' => 'SWF', 'v' => max(0,$swfBalance), 'c' => '#7A5C42'],
                        ];
                        $acc = 0; $parts = [];
                        foreach ($segs as $s) { $pct = $s['v']/$total*100; $parts[] = $s['c'].' '.$acc.'% '.($acc+$pct).'%'; $acc += $pct; }
                    @endphp
                    <div class="donut-chart" style="background:conic-gradient({{ implode(',', $parts) }});">
                        <div class="donut-center" style="background:var(--white);border-radius:50%;inset:14px;position:absolute;">
                            <b>@money($savingsBalance + $invested + $swfBalance)</b><span>Member value</span>
                        </div>
                    </div>
                    <div class="legend">
                        @foreach($segs as $s)
                        <div class="legend-item"><span class="legend-dot" style="background:{{ $s['c'] }};"></span><span>{{ $s['label'] }}</span><b>{{ round($s['v']/$total*100) }}%</b></div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <div class="panel-grid">
        @if($canLoans)
        <div class="panel">
            <div class="panel-head"><h3>Recent loans</h3><a href="{{ route('loans.index') }}" class="link">View all</a></div>
            <div class="table-scroll"><table style="min-width:520px;">
                <thead><tr><th>Loan</th><th>Member</th><th>Amount</th><th>Status</th></tr></thead>
                <tbody>
                    @forelse($recentLoans as $l)
                    <tr><td><div class="cell-title">{{ $l->loan_no }}</div><div class="cell-sub">{{ $l->disbursed_at?->format('d M Y') }}</div></td>
                    <td>{{ $l->member->name ?? '—' }}</td><td class="cell-title">@money($l->principal)</td>
                    <td><span class="tag {{ status_badge($l->status) }}">{{ ucfirst($l->status) }}</span></td></tr>
                    @empty<tr><td colspan="4" class="empty-state">No loans yet.</td></tr>@endforelse
                </tbody>
            </table></div>
        </div>
        @endif
        @if($canDeposits)
        <div class="panel">
            <div class="panel-head"><h3>Recent deposits</h3><a href="{{ route('deposits.index') }}" class="link">View all</a></div>
            <div class="table-scroll"><table style="min-width:520px;">
                <thead><tr><th>Receipt</th><th>Member</th><th>Amount</th><th>Type</th></tr></thead>
                <tbody>
                    @forelse($recentDeposits as $d)
                    <tr><td><div class="cell-title">{{ $d->receipt_no }}</div><div class="cell-sub">{{ $d->transacted_at?->format('d M Y') }}</div></td>
                    <td>{{ $d->member->name ?? '—' }}</td><td class="cell-title">@money($d->amount)</td>
                    <td><span class="tag {{ $d->type === 'deposit' ? 'tag-green' : 'tag-gold' }}">{{ ucfirst($d->type) }}</span></td></tr>
                    @empty<tr><td colspan="4" class="empty-state">No deposits yet.</td></tr>@endforelse
                </tbody>
            </table></div>
        </div>
        @endif
    </div>

    <div class="panel-grid">
        @if($canInvest)
        <div class="panel">
            <div class="panel-head"><h3>Recent investments</h3><a href="{{ route('investments.index') }}" class="link">View all</a></div>
            <div class="table-scroll"><table style="min-width:520px;">
                <thead><tr><th>No</th><th>Member</th><th>Amount</th><th>Status</th></tr></thead>
                <tbody>
                    @forelse($recentInvestments as $i)
                    <tr><td class="cell-title">{{ $i->investment_no }}</td><td>{{ $i->member->name ?? '—' }}</td><td class="cell-title">@money($i->amount)</td><td><span class="tag {{ status_badge($i->status) }}">{{ ucfirst($i->status) }}</span></td></tr>
                    @empty<tr><td colspan="4" class="empty-state">No investments yet.</td></tr>@endforelse
                </tbody>
            </table></div>
        </div>
        @endif
        @if($canSwf)
        <div class="panel">
            <div class="panel-head"><h3>Recent SWF</h3><a href="{{ route('swf.index') }}" class="link">View all</a></div>
            <div class="table-scroll"><table style="min-width:520px;">
                <thead><tr><th>Receipt</th><th>Member</th><th>Amount</th><th>Type</th></tr></thead>
                <tbody>
                    @forelse($recentSwf as $s)
                    <tr><td class="cell-title">{{ $s->receipt_no }}</td><td>{{ $s->member->name ?? '—' }}</td><td class="cell-title">@money($s->amount)</td><td><span class="tag {{ status_badge($s->type) }}">{{ ucfirst($s->type) }}</span></td></tr>
                    @empty<tr><td colspan="4" class="empty-state">No SWF entries yet.</td></tr>@endforelse
                </tbody>
            </table></div>
        </div>
        @endif
    </div>
    @endif
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
(function(){
    const el = document.getElementById('monthlyChart');
    if (!el || typeof Chart === 'undefined') return;
    new Chart(el, {
        type: 'bar',
        data: {
            labels: @json(collect($monthly)->pluck('label')),
            datasets: [
                { label: 'Deposits', data: @json(collect($monthly)->pluck('deposits')), backgroundColor: '#5E6E3F' },
                { label: 'Disbursed', data: @json(collect($monthly)->pluck('disbursed')), backgroundColor: '#C2592B' },
                { label: 'Repaid', data: @json(collect($monthly)->pluck('repaid')), backgroundColor: '#D4A24C' },
            ]
        },
        options: { responsive: true, maintainAspectRatio: false, scales: { y: { beginAtZero: true } } }
    });
})();
</script>
@endsection
