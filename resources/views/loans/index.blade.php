@extends('layouts.app')

@section('title', 'Loans')

@section('content')
    <div class="view-head">
        <div><h2>All Loans</h2><p class="sub">Disburse loans per member, track repayments and outstanding balances.</p></div>
        <div class="view-actions"><a href="{{ route('loans.create') }}" class="btn btn-primary">+ New loan</a></div>
    </div>

    <form method="GET" action="{{ route('loans.index') }}">
        <div class="table-card">
            <div class="table-toolbar">
                <div class="chip-filters">
                    @foreach(['all'=>'All','pending'=>'Pending','active'=>'Active','paid'=>'Paid','overdue'=>'Overdue','defaulted'=>'Defaulted'] as $k=>$v)
                    <a href="{{ route('loans.index', ['status'=>$k,'q'=>$q]) }}" class="chip {{ $status===$k?'active':'' }}">{{ $v }}</a>
                    @endforeach
                </div>
                <div class="table-search"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg><input type="text" name="q" value="{{ $q }}" placeholder="Search loan/member…" onchange="this.form.submit()"></div>
            </div>
            <div class="table-scroll"><table>
                <thead><tr><th>Loan</th><th>Member</th><th>Product</th><th>Principal</th><th>Payable</th><th>Repaid</th><th>Status</th><th style="text-align:right;">Open</th></tr></thead>
                <tbody>
                    @forelse($loans as $l)
                    <tr>
                        <td><div class="cell-title"><a href="{{ route('loans.show', $l) }}">{{ $l->loan_no }}</a></div><div class="cell-sub">{{ $l->disbursed_at?->format('d M Y') }} → {{ $l->due_date?->format('d M Y') ?? '—' }}</div></td>
                        <td><div class="cell-title">{{ $l->member->name ?? '—' }}</div><div class="cell-sub">{{ $l->member->phone ?? '' }}</div></td>
                        <td>{{ $l->product->name ?? '—' }}</td>
                        <td>@money($l->principal)</td><td class="cell-title">@money($l->total_payable)</td>
                        <td>@money($l->repayments->sum('amount'))</td>
                        <td><span class="tag {{ status_badge($l->status) }}">{{ ucfirst($l->status) }}</span></td>
                        <td><div class="row-actions"><a href="{{ route('loans.show',$l) }}"><button type="button">↗</button></a></div></td>
                    </tr>
                    @empty<tr><td colspan="8" class="empty-state">No loans in this view.</td></tr>@endforelse
                </tbody>
            </table></div>
            <div class="table-pager"><span class="pager-info">{{ $loans->total() }} loans</span><div class="pager-pages">{{ $loans->links('pagination.pager') }}</div></div>
        </div>
    </form>
@endsection
