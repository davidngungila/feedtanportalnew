@extends('layouts.app')
@section('title', 'Trial Balance')
@section('content')
    <div class="view-head">
        <div><h2>Trial Balance</h2><p class="sub">As of {{ $asOf }} · every debit has an equal credit.</p></div>
        <div class="view-actions"><a href="{{ route('finance.statements') }}" class="btn btn-ghost">Statements</a></div>
    </div>
    <form method="GET" action="{{ route('finance.trial-balance') }}">
        <div class="table-card">
            <div class="table-toolbar">
                <span class="chip active">Debits @money($totalDebit) · Credits @money($totalCredit)</span>
                <div class="table-search" style="min-width:140px;"><input type="date" name="as_of" value="{{ $asOf }}" onchange="this.form.submit()"></div>
            </div>
            <div class="table-scroll"><table>
                <thead><tr><th>Code</th><th>Account</th><th>Type</th><th>Debit</th><th>Credit</th></tr></thead>
                <tbody>
                    @forelse($rows as $r)
                    <tr><td class="cell-title">{{ $r['account']->code }}</td><td>{{ $r['account']->name }}</td><td><span class="tag tag-grey">{{ ucfirst($r['account']->type) }}</span></td><td>@money($r['debit'])</td><td>@money($r['credit'])</td></tr>
                    @empty<tr><td colspan="5" class="empty-state">No posted balances yet.</td></tr>@endforelse
                </tbody>
            </table></div>
            <div class="table-pager"><span class="pager-info">Total debit @money($totalDebit) · Total credit @money($totalCredit) · {{ abs($totalDebit - $totalCredit) < 0.01 ? 'Balanced ✓' : 'OUT OF BALANCE' }}</span></div>
        </div>
    </form>
@endsection
