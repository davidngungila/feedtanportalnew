@extends('layouts.app')

@section('title', 'SWF Statements')

@section('content')
    <div class="view-head"><div><h2>SWF Statements</h2><p class="sub">Pick a member to see the full SWF statement.</p></div><div class="view-actions"><a href="{{ route('swf.create') }}" class="btn btn-primary">+ New entry</a></div></div>
    <div class="table-card"><div class="table-toolbar">
        <form method="GET" action="{{ route('swf.statements') }}" style="display:flex;gap:10px;align-items:center;">
            <select name="member_id" onchange="this.form.submit()" style="padding:9px 12px;border:1.5px solid var(--line);border-radius:10px;font-weight:600;">
                <option value="">Select member…</option>
                @foreach($members as $m)<option value="{{ eid($m->id) }}" {{ (int)$memberId === $m->id ? 'selected' : '' }}>{{ $m->name }} · {{ $m->phone }}</option>@endforeach
            </select>
        </form>
        @if($member)
            @php
                $in = (float) $member->swfEntries()->where('type','contribution')->sum('amount');
                $out = (float) $member->swfEntries()->whereIn('type',['deduction','payout','claim'])->sum('amount');
            @endphp
            <span class="tag tag-green">Balance: @money($in - $out)</span>
        @endif
    </div>
    @if($member)
        <div class="table-scroll"><table>
            <thead><tr><th>Receipt</th><th>Type</th><th>Amount</th><th>Date</th><th>Reason</th></tr></thead>
            <tbody>@forelse($entries as $e)<tr><td class="cell-title">{{ $e->receipt_no }}</td><td><span class="tag {{ status_badge($e->type) }}">{{ ucfirst($e->type) }}</span></td><td>@money($e->amount)</td><td>{{ $e->transacted_at?->format('d M Y') }}</td><td>{{ $e->reason ?? '—' }}</td></tr>@empty<tr><td colspan="5" class="empty-state">No entries for this member.</td></tr>@endforelse</tbody>
        </table></div>
        <div class="table-pager"><span class="pager-info">{{ $entries->total() }} rows</span><div class="pager-pages">{{ $entries->links('pagination.pager') }}</div></div>
    @else
        <div class="empty-state">Select a member above to load the statement.</div>
    @endif
    </div>
@endsection
