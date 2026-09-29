@extends('layouts.app')
@section('title', 'New Applications')
@section('content')
    <div class="view-head"><div><h2>New Applications</h2><p class="sub">Review membership requests, then approve into members.</p></div><div class="view-actions"><a href="{{ route('member-applications.create') }}" class="btn btn-primary">+ New application</a></div></div>
    <div class="table-card">
        <div class="table-toolbar"><div class="chip-filters">
            @foreach(['all' => 'All', 'pending' => 'Pending', 'approved' => 'Approved', 'rejected' => 'Rejected'] as $k => $v)
            <a href="{{ route('member-applications.index', ['status' => $k]) }}" class="chip {{ $status === $k ? 'active' : '' }}">{{ $v }}</a>
            @endforeach
        </div></div>
        <div class="table-scroll"><table>
            <thead><tr><th>Applicant</th><th>Type / Group</th><th>Status</th><th style="text-align:right;">Actions</th></tr></thead>
            <tbody>@forelse($applications as $a)<tr>
                <td><div class="cell-title">{{ $a->name }}</div><div class="cell-sub">{{ $a->phone }} · {{ $a->created_at->format('d M Y') }}</div></td>
                <td>{{ $a->memberType->name ?? '—' }} / {{ $a->memberGroup->name ?? '—' }}</td>
                <td><span class="tag {{ status_badge($a->status) }}">{{ ucfirst($a->status) }}</span></td>
                <td><div class="row-actions" style="justify-content:flex-end;">
                    @if($a->status === 'pending')
                    <form method="POST" action="{{ route('member-applications.approve', $a) }}">@csrf<button class="btn btn-primary btn-sm" type="submit">Approve</button></form>
                    <form method="POST" action="{{ route('member-applications.update', $a) }}">@csrf @method('PUT')<input type="hidden" name="status" value="rejected"><button class="btn btn-ghost btn-sm" type="submit">Reject</button></form>
                    @endif
                    <form method="POST" action="{{ route('member-applications.destroy', $a) }}" onsubmit="return confirm('Remove?')">@csrf @method('DELETE')<button type="submit" class="danger" title="Remove">✕</button></form>
                </div></td></tr>
            @empty<tr><td colspan="4" class="empty-state">No applications.</td></tr>@endforelse</tbody>
        </table></div>
        <div class="table-pager"><span class="pager-info">{{ $applications->total() }} applications</span><div class="pager-pages">{{ $applications->links('pagination.pager') }}</div></div>
    </div>
@endsection
