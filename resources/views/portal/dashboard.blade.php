@extends('layouts.app')
@section('title', 'My Portal')
@section('content')
    <div class="view-head">
        <div>
            <h2>Karibu, {{ $member->name }}</h2>
            <p class="sub">{{ $member->member_no }} · {{ $member->memberType->name ?? 'Member' }} · Joined {{ $member->join_date?->format('d M Y') }}</p>
        </div>
        <div class="view-actions">
            <a href="{{ route('portal.deposits.create') }}" class="btn btn-ghost">Deposit</a>
            <a href="{{ route('portal.investments.create') }}" class="btn btn-ghost">Invest</a>
            <a href="{{ route('portal.loan-applications.create') }}" class="btn btn-primary">Request loan</a>
        </div>
    </div>

    <div class="balance-strip">
        <div class="balance-box"><div class="bb-label">My savings</div><div class="bb-amount">@money($bal['savings'])</div><div class="bb-sub"><a href="{{ route('portal.deposits') }}">Deposits →</a></div></div>
        <div class="balance-box"><div class="bb-label">Loans outstanding</div><div class="bb-amount">@money($bal['loan_outstanding'])</div><div class="bb-sub"><a href="{{ route('portal.loans') }}">Loans →</a></div></div>
        <div class="balance-box"><div class="bb-label">My investments</div><div class="bb-amount">@money($bal['invested'])</div><div class="bb-sub"><a href="{{ route('portal.investments') }}">Investments →</a></div></div>
        <div class="balance-box"><div class="bb-label">My SWF</div><div class="bb-amount">@money($bal['swf'])</div><div class="bb-sub"><a href="{{ route('portal.swf') }}">SWF →</a></div></div>
    </div>

    <div class="tabs">
        <button class="tab-btn active" onclick="switchPortalTab(this,'loans')">Loans</button>
        <button class="tab-btn" onclick="switchPortalTab(this,'deposits')">Savings</button>
        <button class="tab-btn" onclick="switchPortalTab(this,'invest')">Investments</button>
        <button class="tab-btn" onclick="switchPortalTab(this,'swf')">SWF</button>
        <button class="tab-btn" onclick="switchPortalTab(this,'apps')">My requests</button>
    </div>

    <div class="tab-panel" data-tab="loans">
        <div class="table-card"><div class="table-scroll"><table>
            <thead><tr><th>Loan</th><th>Principal</th><th>Repaid</th><th>Balance</th><th>Status</th></tr></thead>
            <tbody>
                @forelse($loans as $l)
                <tr>
                    <td><a class="cell-title" href="{{ route('portal.loans.show', $l) }}">{{ $l->loan_no }}</a><div class="cell-sub">Due {{ $l->due_date?->format('d M Y') ?? '—' }}</div></td>
                    <td>@money($l->principal)</td><td>@money($l->totalRepaid())</td><td class="cell-title">@money($l->outstanding())</td>
                    <td><span class="tag {{ status_badge($l->status) }}">{{ ucfirst($l->status) }}</span></td>
                </tr>
                @empty<tr><td colspan="5" class="empty-state">No loans yet. <a href="{{ route('portal.loan-applications.create') }}">Request one</a>.</td></tr>@endforelse
            </tbody>
        </table></div></div>
    </div>
    <div class="tab-panel hidden" data-tab="deposits">
        <div class="table-card"><div class="table-scroll"><table>
            <thead><tr><th>Receipt</th><th>Type</th><th>Amount</th><th>Date</th></tr></thead>
            <tbody>
                @forelse($deposits as $d)
                <tr><td class="cell-title">{{ $d->receipt_no }}</td><td><span class="tag {{ $d->type === 'deposit' ? 'tag-green' : 'tag-gold' }}">{{ ucfirst($d->type) }}</span></td><td>@money($d->amount)</td><td>{{ $d->transacted_at?->format('d M Y') }}</td></tr>
                @empty<tr><td colspan="4" class="empty-state">No savings activity yet.</td></tr>@endforelse
            </tbody>
        </table></div></div>
    </div>
    <div class="tab-panel hidden" data-tab="invest">
        <div class="table-card"><div class="table-scroll"><table>
            <thead><tr><th>No</th><th>Amount</th><th>Return</th><th>Status</th></tr></thead>
            <tbody>
                @forelse($investments as $i)
                <tr><td class="cell-title">{{ $i->investment_no }}</td><td>@money($i->amount)</td><td>@money($i->expected_return)</td><td><span class="tag {{ status_badge($i->status) }}">{{ ucfirst($i->status) }}</span></td></tr>
                @empty<tr><td colspan="4" class="empty-state">No investments yet.</td></tr>@endforelse
            </tbody>
        </table></div></div>
    </div>
    <div class="tab-panel hidden" data-tab="swf">
        <div class="table-card"><div class="table-scroll"><table>
            <thead><tr><th>Receipt</th><th>Type</th><th>Amount</th><th>Date</th></tr></thead>
            <tbody>
                @forelse($swf as $s)
                <tr><td class="cell-title">{{ $s->receipt_no }}</td><td><span class="tag {{ status_badge($s->type) }}">{{ ucfirst($s->type) }}</span></td><td>@money($s->amount)</td><td>{{ $s->transacted_at?->format('d M Y') }}</td></tr>
                @empty<tr><td colspan="4" class="empty-state">No SWF entries yet.</td></tr>@endforelse
            </tbody>
        </table></div></div>
    </div>
    <div class="tab-panel hidden" data-tab="apps">
        <div class="table-card"><div class="table-scroll"><table>
            <thead><tr><th>Date</th><th>Product</th><th>Amount</th><th>Status</th></tr></thead>
            <tbody>
                @forelse($applications as $a)
                <tr><td>{{ $a->created_at->format('d M Y') }}</td><td>{{ $a->product->name ?? '—' }}</td><td class="cell-title">@money($a->amount)</td><td><span class="tag {{ status_badge($a->status) }}">{{ ucfirst($a->status) }}</span></td></tr>
                @empty<tr><td colspan="4" class="empty-state">No requests yet.</td></tr>@endforelse
            </tbody>
        </table></div></div>
    </div>
@endsection

@section('scripts')
<script>
function switchPortalTab(btn, tab){
    document.querySelectorAll('.tabs .tab-btn').forEach(b=>b.classList.toggle('active', b===btn));
    document.querySelectorAll('.tab-panel').forEach(p=>p.classList.toggle('hidden', p.dataset.tab!==tab));
}
</script>
@endsection
