@extends('layouts.app')
@section('title', 'Member Reports')
@section('content')
    <div class="view-head"><div><h2>Member Reports</h2><p class="sub">{{ $newCount }} new members in period · per-member balances across all funds.</p></div>
        <div class="view-actions"><form method="GET" action="{{ route('reports.members') }}" style="display:flex;gap:8px;"><input type="date" name="from" value="{{ $from }}" onchange="this.form.submit()" style="padding:9px 12px;border:1.5px solid var(--line);border-radius:10px;font-weight:600;"><input type="date" name="to" value="{{ $to }}" onchange="this.form.submit()" style="padding:9px 12px;border:1.5px solid var(--line);border-radius:10px;font-weight:600;"></form><button class="btn btn-ghost btn-sm" onclick="window.print()">Print</button></div>
    </div>
    <div class="table-card"><div class="table-scroll"><table>
        <thead><tr><th>Member</th><th>Type</th><th>Savings</th><th>Loans out</th><th>Invested</th><th>SWF</th></tr></thead>
        <tbody>@forelse($members as $r)<tr><td><div class="cell-title"><a href="{{ route('members.show', $r['member']) }}">{{ $r['member']->name }}</a></div><div class="cell-sub">{{ $r['member']->member_no }} · {{ $r['member']->phone }}</div></td><td>{{ $r['member']->memberType->name ?? '—' }}</td><td>@money($r['savings'])</td><td>@money($r['loan_outstanding'])</td><td>@money($r['invested'])</td><td>@money($r['swf'])</td></tr>@empty<tr><td colspan="6" class="empty-state">No members.</td></tr>@endforelse</tbody>
    </table></div></div>
@endsection
