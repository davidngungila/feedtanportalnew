@extends('layouts.app')
@section('title', 'Transaction Reports')
@section('content')
    <div class="view-head"><div><h2>Transaction Reports</h2><p class="sub">Repayments, deposits, SWF and book entries in the period.</p></div>
        <div class="view-actions"><form method="GET" action="{{ route('reports.transactions') }}" style="display:flex;gap:8px;"><input type="date" name="from" value="{{ $from }}" onchange="this.form.submit()" style="padding:9px 12px;border:1.5px solid var(--line);border-radius:10px;font-weight:600;"><input type="date" name="to" value="{{ $to }}" onchange="this.form.submit()" style="padding:9px 12px;border:1.5px solid var(--line);border-radius:10px;font-weight:600;"></form><button class="btn btn-ghost btn-sm" onclick="window.print()">Print</button></div>
    </div>
    <div class="tabs">
        <button class="tab-btn active" onclick="switchTab(this,'repay')">Repayments</button>
        <button class="tab-btn" onclick="switchTab(this,'dep')">Deposits</button>
        <button class="tab-btn" onclick="switchTab(this,'swf')">SWF</button>
        <button class="tab-btn" onclick="switchTab(this,'fin')">Book entries</button>
    </div>
    <div class="tab-panel" data-tab="repay"><div class="table-card"><div class="table-scroll"><table>
        <thead><tr><th>Receipt</th><th>Member</th><th>Amount</th><th>Date</th></tr></thead>
        <tbody>@forelse($repayments as $r)<tr><td class="cell-title">{{ $r->receipt_no }}</td><td>{{ $r->loan->member->name ?? '—' }}</td><td>@money($r->amount)</td><td>{{ $r->paid_at?->format('d M Y') }}</td></tr>@empty<tr><td colspan="4" class="empty-state">None.</td></tr>@endforelse</tbody>
    </table></div><div class="table-pager"><span class="pager-info">{{ $repayments->total() }}</span><div class="pager-pages">{{ $repayments->links('pagination.pager') }}</div></div></div></div>
    <div class="tab-panel hidden" data-tab="dep"><div class="table-card"><div class="table-scroll"><table>
        <thead><tr><th>Receipt</th><th>Member</th><th>Amount</th><th>Date</th></tr></thead>
        <tbody>@forelse($deposits as $d)<tr><td class="cell-title">{{ $d->receipt_no }}</td><td>{{ $d->member->name ?? '—' }}</td><td>@money($d->amount)</td><td>{{ $d->transacted_at?->format('d M Y') }}</td></tr>@empty<tr><td colspan="4" class="empty-state">None.</td></tr>@endforelse</tbody>
    </table></div><div class="table-pager"><span class="pager-info">{{ $deposits->total() }}</span><div class="pager-pages">{{ $deposits->links('pagination.pager') }}</div></div></div></div>
    <div class="tab-panel hidden" data-tab="swf"><div class="table-card"><div class="table-scroll"><table>
        <thead><tr><th>Receipt</th><th>Member</th><th>Amount</th><th>Date</th></tr></thead>
        <tbody>@forelse($swf as $s)<tr><td class="cell-title">{{ $s->receipt_no }}</td><td>{{ $s->member->name ?? '—' }}</td><td>@money($s->amount)</td><td>{{ $s->transacted_at?->format('d M Y') }}</td></tr>@empty<tr><td colspan="4" class="empty-state">None.</td></tr>@endforelse</tbody>
    </table></div><div class="table-pager"><span class="pager-info">{{ $swf->total() }}</span><div class="pager-pages">{{ $swf->links('pagination.pager') }}</div></div></div></div>
    <div class="tab-panel hidden" data-tab="fin"><div class="table-card"><div class="table-scroll"><table>
        <thead><tr><th>Reference</th><th>Account</th><th>Amount</th><th>Date</th></tr></thead>
        <tbody>@forelse($finance as $f)<tr><td class="cell-title">{{ $f->reference }}</td><td>{{ $f->account->name ?? '—' }}</td><td>@money($f->amount)</td><td>{{ $f->transacted_at?->format('d M Y') }}</td></tr>@empty<tr><td colspan="4" class="empty-state">None.</td></tr>@endforelse</tbody>
    </table></div><div class="table-pager"><span class="pager-info">{{ $finance->total() }}</span><div class="pager-pages">{{ $finance->links('pagination.pager') }}</div></div></div></div>
@endsection

@section('scripts')
<script>
function switchTab(btn, tab){
    document.querySelectorAll('.tabs .tab-btn').forEach(b=>b.classList.toggle('active', b===btn));
    document.querySelectorAll('.tab-panel').forEach(p=>p.classList.toggle('hidden', p.dataset.tab!==tab));
}
</script>
@endsection
