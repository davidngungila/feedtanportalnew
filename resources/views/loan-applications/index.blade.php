@extends('layouts.app')
@section('title', 'Loan Applications')
@section('content')
    <div class="view-head"><div><h2>Loan Applications</h2><p class="sub">Review requests, then disburse into loans.</p></div><div class="view-actions"><a href="{{ route('loan-applications.create') }}" class="btn btn-primary">+ New application</a></div></div>
    <div class="table-card">
        <div class="table-toolbar"><div class="chip-filters">
            @foreach(['all' => 'All', 'pending' => 'Pending', 'approved' => 'Approved', 'rejected' => 'Rejected', 'disbursed' => 'Disbursed'] as $k => $v)
            <a href="{{ route('loan-applications.index', ['status' => $k]) }}" class="chip {{ $status === $k ? 'active' : '' }}">{{ $v }}</a>
            @endforeach
        </div></div>
        <div class="table-scroll"><table>
            <thead><tr><th>Member</th><th>Product</th><th>Amount</th><th>Status</th><th style="text-align:right;">Actions</th></tr></thead>
            <tbody>@forelse($applications as $a)<tr>
                <td><div class="cell-title">{{ $a->member->name ?? '—' }}</div><div class="cell-sub">{{ $a->purpose ?? '' }}</div></td>
                <td>{{ $a->product->name ?? '—' }}</td><td class="cell-title">@money($a->amount)</td>
                <td><span class="tag {{ status_badge($a->status) }}">{{ ucfirst($a->status) }}</span></td>
                <td><div class="row-actions" style="justify-content:flex-end;">
                    @if(in_array($a->status, ['pending', 'approved']))
                    <form method="POST" action="{{ route('loan-applications.approve', $a) }}">@csrf<button class="btn btn-primary btn-sm" type="submit">Disburse</button></form>
                    @endif
                    <form method="POST" action="{{ route('loan-applications.destroy', $a) }}" onsubmit="return confirm('Remove?')">@csrf @method('DELETE')<button type="submit" class="danger">✕</button></form>
                </div></td></tr>
            @empty<tr><td colspan="5" class="empty-state">No applications.</td></tr>@endforelse</tbody>
        </table></div>
        <div class="table-pager"><span class="pager-info">{{ $applications->total() }} applications</span><div class="pager-pages">{{ $applications->links('pagination.pager') }}</div></div>
    </div>
@endsection
