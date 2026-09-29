@extends('layouts.app')
@section('title', 'My Loans')
@section('content')
    <div class="view-head">
        <div><h2>My loans</h2><p class="sub">{{ $member->name }} · {{ $member->member_no }} · Track balances and repayments.</p></div>
        <div class="view-actions"><a href="{{ route('portal.home') }}" class="btn btn-ghost">Portal</a><a href="{{ route('portal.loan-applications.create') }}" class="btn btn-primary">Request loan</a></div>
    </div>
    <div class="table-card"><div class="table-scroll"><table>
        <thead><tr><th>Loan no</th><th>Principal</th><th>Total payable</th><th>Repaid</th><th>Balance</th><th>Status</th><th style="text-align:right;">Open</th></tr></thead>
        <tbody>
            @forelse($loans as $l)
            <tr>
                <td><div class="cell-title">{{ $l->loan_no }}</div><div class="cell-sub">Due {{ $l->due_date?->format('d M Y') ?? '—' }}</div></td>
                <td>@money($l->principal)</td><td>@money($l->total_payable)</td><td>@money($l->totalRepaid())</td><td class="cell-title">@money($l->outstanding())</td>
                <td><span class="tag {{ status_badge($l->status) }}">{{ ucfirst($l->status) }}</span></td>
                <td><div class="row-actions"><a href="{{ route('portal.loans.show', $l) }}"><button type="button" title="Open">↗</button></a></div></td>
            </tr>
            @empty<tr><td colspan="7" class="empty-state">No loans yet.</td></tr>@endforelse
        </tbody>
    </table></div>
    <div class="table-pager"><span class="pager-info">Showing {{ $loans->count() }} of {{ $loans->total() }}</span><div class="pager-pages">{{ $loans->links('pagination.pager') }}</div></div></div>
@endsection
