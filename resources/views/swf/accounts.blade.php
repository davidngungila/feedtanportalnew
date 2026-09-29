@extends('layouts.app')

@section('title', 'All SWF Accounts')

@section('content')
    <div class="view-head"><div><h2>All SWF Accounts</h2><p class="sub">Per-member SWF balances (contributions minus deductions, payouts and claims).</p></div><div class="view-actions"><a href="{{ route('swf.statements') }}" class="btn btn-ghost">Statements</a><a href="{{ route('swf.create') }}" class="btn btn-primary">+ New entry</a></div></div>
    <form method="GET" action="{{ route('swf.accounts') }}">
        <div class="table-card">
            <div class="table-toolbar"><div class="table-search"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"></circle></svg><input name="q" value="{{ $q }}" placeholder="Search member…" onchange="this.form.submit()"></div></div>
            <div class="table-scroll"><table>
                <thead><tr><th>Member</th><th>Contributed</th><th>Deducted/Paid</th><th>Balance</th><th style="text-align:right;">Statement</th></tr></thead>
                <tbody>@forelse($members as $m)
                    @php
                        $in = (float) $m->swfEntries()->where('type','contribution')->sum('amount');
                        $out = (float) $m->swfEntries()->whereIn('type',['deduction','payout','claim'])->sum('amount');
                    @endphp
                    <tr><td><div class="cell-title"><a href="{{ route('members.show', $m) }}">{{ $m->name }}</a></div><div class="cell-sub">{{ $m->member_no }} · {{ $m->phone }}</div></td>
                    <td>@money($in)</td><td>@money($out)</td><td class="cell-title">@money($in - $out)</td>
                    <td><div class="row-actions"><a href="{{ route('swf.statements', ['member_id' => eid($m->id)]) }}"><button type="button">↗</button></a></div></td></tr>
                @empty<tr><td colspan="5" class="empty-state">No members.</td></tr>@endforelse</tbody>
            </table></div>
            <div class="table-pager"><span class="pager-info">{{ $members->total() }} accounts</span><div class="pager-pages">{{ $members->links('pagination.pager') }}</div></div>
        </div>
    </form>
@endsection
