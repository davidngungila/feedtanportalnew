@extends('layouts.app')

@section('title', $member->name)

@section('content')
    <div class="view-head">
        <div><h2>{{ $member->name }}</h2><p class="sub">{{ $member->member_no }} · {{ $member->phone }} · {{ $member->memberType->name ?? 'No type' }} · Joined {{ $member->join_date?->format('d M Y') }}</p></div>
        <div class="view-actions">
            <a href="{{ route('members.index') }}" class="btn btn-ghost">Back</a>
            <a href="{{ route('members.edit', $member) }}" class="btn btn-primary">Edit member</a>
        </div>
    </div>

    @php $bal = member_balance($member); @endphp
    <div class="balance-strip">
        <div class="balance-box"><div class="bb-label">Savings balance</div><div class="bb-amount">@money($bal['savings'])</div><div class="bb-sub">Deposits minus withdrawals</div></div>
        <div class="balance-box"><div class="bb-label">Loan outstanding</div><div class="bb-amount">@money($bal['loan_outstanding'])</div><div class="bb-sub">{{ $member->loans->count() }} loans</div></div>
        <div class="balance-box"><div class="bb-label">Invested</div><div class="bb-amount">@money($bal['invested'])</div><div class="bb-sub">{{ $member->investments->count() }} investments</div></div>
        <div class="balance-box"><div class="bb-label">SWF balance</div><div class="bb-amount">@money($bal['swf'])</div><div class="bb-sub">{{ $member->swfEntries->count() }} entries</div></div>
    </div>

    <div class="panel-grid">
        <div class="panel"><div class="panel-head"><h3>Profile</h3><span class="tag {{ status_badge($member->status) }}">{{ ucfirst($member->status) }}</span></div>
            <div class="panel-body"><div class="detail-grid">
                <div class="detail-item"><div class="dk">Phone</div><div class="dv">{{ $member->phone }}</div></div>
                <div class="detail-item"><div class="dk">Email</div><div class="dv">{{ $member->email ?? '—' }}</div></div>
                <div class="detail-item"><div class="dk">Type</div><div class="dv">{{ $member->memberType->name ?? '—' }}</div></div>
                <div class="detail-item"><div class="dk">Groups</div><div class="dv">{{ $member->groups->pluck('name')->join(', ') ?: '—' }}</div></div>
                <div class="detail-item"><div class="dk">National ID</div><div class="dv">{{ $member->national_id ?? '—' }}</div></div>
                <div class="detail-item"><div class="dk">Address</div><div class="dv">{{ $member->address ?? '—' }}</div></div>
            </div></div>
        </div>
        <div class="panel"><div class="panel-head"><h3>Documents</h3><a href="{{ route('member-documents.create', ['member_id' => eid($member->id)]) }}" class="link">+ Add</a></div>
            <div class="panel-body"><div class="activity-list">
                @forelse($member->documents as $d)<div class="activity-row"><div class="activity-text"><b>{{ $d->title }}</b><div class="activity-time"><span>{{ $d->doc_type }}</span><span>{{ $d->created_at->format('d M Y') }}</span></div></div></div>
                @empty<div class="empty-state">No documents.</div>@endforelse
            </div></div>
        </div>
    </div>

    @if(is_role('administrator', 'chairperson', 'secretary', 'accountant'))
    @php $loginUser = \App\Models\User::where('member_id', $member->id)->first(); @endphp
    <div class="panel" style="margin-bottom:24px;"><div class="panel-head"><h3>Member login access</h3>@if($loginUser)<span class="tag tag-green">Active</span>@else<span class="tag tag-grey">No login</span>@endif</div>
        <div class="panel-body">
            @if(session('provisioned_password'))
                <div class="receipt" style="margin:0 0 14px;"><div class="receipt-row"><span>Temporary password (copy now — shown once)</span><b><code>{{ session('provisioned_password') }}</code></b></div></div>
            @endif
            @if($loginUser)
                <div class="detail-grid">
                    <div class="detail-item"><div class="dk">Login email</div><div class="dv">{{ $loginUser->email }}</div></div>
                    <div class="detail-item"><div class="dk">Portal</div><div class="dv"><a href="{{ route('portal.home') }}" style="color:var(--terracotta-600);font-weight:700;">Member portal →</a></div></div>
                </div>
                <div style="display:flex;gap:10px;flex-wrap:wrap;margin-top:14px;">
                    <form method="POST" action="{{ route('members.reset-login', $member) }}" onsubmit="return confirm('Generate a new password for this member?')">@csrf<button class="btn btn-soft btn-sm" type="submit">Reset password</button></form>
                    <form method="POST" action="{{ route('members.destroy-login', $member) }}" onsubmit="return confirm('Remove this member login?')">@csrf @method('DELETE')<button class="btn btn-danger btn-sm" type="submit">Remove login</button></form>
                </div>
            @else
                <form method="POST" action="{{ route('members.provision-login', $member) }}" style="display:flex;gap:10px;flex-wrap:wrap;align-items:flex-end;">@csrf
                    <div class="field" style="margin:0;min-width:220px;flex:1;"><label>Login email *</label><input type="email" name="email" required value="{{ old('email', $member->email) }}" placeholder="member@example.com"></div>
                    <div class="field" style="margin:0;min-width:180px;"><label>Password (blank = auto)</label><input type="text" name="password" placeholder="Auto-generate"></div>
                    <button class="btn btn-primary btn-sm" type="submit" style="padding:12px 20px;">Create login</button>
                </form>
                <p style="font-size:12.5px;color:var(--ink-soft);margin-top:10px;">Creates a <b>Member</b>-role user linked to this profile. Member signs in with the same login page and lands in the self-service portal.</p>
            @endif
        </div>
    </div>
    @endif

    <div class="tabs">
        <button class="tab-btn active" onclick="switchTab(this,'loans')">Loans <span class="tab-count">{{ $member->loans->count() }}</span></button>        <button class="tab-btn" onclick="switchTab(this,'deposits')">Deposits <span class="tab-count">{{ $member->deposits->count() }}</span></button>
        <button class="tab-btn" onclick="switchTab(this,'invest')">Investments <span class="tab-count">{{ $member->investments->count() }}</span></button>
        <button class="tab-btn" onclick="switchTab(this,'swf')">SWF <span class="tab-count">{{ $member->swfEntries->count() }}</span></button>
    </div>

    <div class="tab-panel" data-tab="loans">
        <div class="table-card"><div class="table-scroll"><table>
            <thead><tr><th>Loan no</th><th>Principal</th><th>Total payable</th><th>Repaid</th><th>Status</th><th>Due</th></tr></thead>
            <tbody>@forelse($member->loans as $l)<tr><td><a href="{{ route('loans.show',$l) }}" class="cell-title">{{ $l->loan_no }}</a></td><td>@money($l->principal)</td><td>@money($l->total_payable)</td><td>@money($l->totalRepaid())</td><td><span class="tag {{ status_badge($l->status) }}">{{ ucfirst($l->status) }}</span></td><td>{{ $l->due_date?->format('d M Y') ?? '—' }}</td></tr>@empty<tr><td colspan="6" class="empty-state">No loans for this member.</td></tr>@endforelse</tbody>
        </table></div></div>
    </div>
    <div class="tab-panel hidden" data-tab="deposits">
        <div class="table-card"><div class="table-scroll"><table>
            <thead><tr><th>Receipt</th><th>Type</th><th>Amount</th><th>Date</th><th>Method</th></tr></thead>
            <tbody>@forelse($member->deposits as $d)<tr><td class="cell-title">{{ $d->receipt_no }}</td><td><span class="tag {{ $d->type==='deposit'?'tag-green':'tag-gold' }}">{{ ucfirst($d->type) }}</span></td><td>@money($d->amount)</td><td>{{ $d->transacted_at?->format('d M Y') }}</td><td>{{ ucfirst($d->method) }}</td></tr>@empty<tr><td colspan="5" class="empty-state">No deposits.</td></tr>@endforelse</tbody>
        </table></div></div>
    </div>
    <div class="tab-panel hidden" data-tab="invest">
        <div class="table-card"><div class="table-scroll"><table>
            <thead><tr><th>No</th><th>Amount</th><th>Return</th><th>Start</th><th>Maturity</th><th>Status</th></tr></thead>
            <tbody>@forelse($member->investments as $i)<tr><td class="cell-title">{{ $i->investment_no }}</td><td>@money($i->amount)</td><td>@money($i->expected_return)</td><td>{{ $i->start_date?->format('d M Y') }}</td><td>{{ $i->maturity_date?->format('d M Y') ?? '—' }}</td><td><span class="tag {{ status_badge($i->status) }}">{{ ucfirst($i->status) }}</span></td></tr>@empty<tr><td colspan="6" class="empty-state">No investments.</td></tr>@endforelse</tbody>
        </table></div></div>
    </div>
    <div class="tab-panel hidden" data-tab="swf">
        <div class="table-card"><div class="table-scroll"><table>
            <thead><tr><th>Receipt</th><th>Type</th><th>Amount</th><th>Date</th><th>Reason</th></tr></thead>
            <tbody>@forelse($member->swfEntries as $s)<tr><td class="cell-title">{{ $s->receipt_no }}</td><td><span class="tag {{ status_badge($s->type) }}">{{ ucfirst($s->type) }}</span></td><td>@money($s->amount)</td><td>{{ $s->transacted_at?->format('d M Y') }}</td><td>{{ $s->reason ?? '—' }}</td></tr>@empty<tr><td colspan="5" class="empty-state">No SWF entries.</td></tr>@endforelse</tbody>
        </table></div></div>
    </div>
@endsection

@section('scripts')
<script>
function switchTab(btn, tab){
    document.querySelectorAll('.tabs .tab-btn').forEach(b=>b.classList.toggle('active', b===btn));
    document.querySelectorAll('.tab-panel').forEach(p=>p.classList.toggle('hidden', p.dataset.tab!==tab));
}
</script>
@endsection
