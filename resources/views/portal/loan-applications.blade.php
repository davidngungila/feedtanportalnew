@extends('layouts.app')
@section('title', 'My Loan Requests')
@section('content')
    <div class="view-head">
        <div><h2>My loan requests</h2><p class="sub">{{ $member->name }} · Submit a request and track approvals here.</p></div>
        <div class="view-actions"><a href="{{ route('portal.home') }}" class="btn btn-ghost">Portal</a><a href="{{ route('portal.loan-applications.create') }}" class="btn btn-primary">+ New request</a></div>
    </div>
    <div class="table-card"><div class="table-scroll"><table>
        <thead><tr><th>Date</th><th>Product</th><th>Amount</th><th>Purpose</th><th>Status</th><th style="text-align:right;">Action</th></tr></thead>
        <tbody>
            @forelse($applications as $a)
            <tr>
                <td>{{ $a->created_at->format('d M Y') }}</td><td>{{ $a->product->name ?? '—' }}</td><td class="cell-title">@money($a->amount)</td><td>{{ $a->purpose ?? '—' }}</td>
                <td><span class="tag {{ status_badge($a->status) }}">{{ ucfirst($a->status) }}</span></td>
                <td><div class="row-actions">@if($a->status === 'pending')<form method="POST" action="{{ route('portal.loan-applications.cancel', $a) }}" onsubmit="return confirm('Cancel this request?')">@csrf @method('DELETE')<button type="submit" class="danger" title="Cancel">✕</button></form>@endif</div></td>
            </tr>
            @empty<tr><td colspan="6" class="empty-state">No requests yet. <a href="{{ route('portal.loan-applications.create') }}">Send your first request</a>.</td></tr>@endforelse
        </tbody>
    </table></div>
    <div class="table-pager"><span class="pager-info">Showing {{ $applications->count() }} of {{ $applications->total() }}</span><div class="pager-pages">{{ $applications->links('pagination.pager') }}</div></div></div>
@endsection
