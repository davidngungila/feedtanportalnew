@extends('layouts.app')
@section('title', 'My SWF')
@section('content')
    <div class="view-head">
        <div><h2>My SWF</h2><p class="sub">{{ $member->name }} · Balance <b>@money($balance)</b> · Contributions minus claims &amp; deductions.</p></div>
        <div class="view-actions"><a href="{{ route('portal.home') }}" class="btn btn-ghost">Portal</a><a href="{{ route('portal.statements', ['kind' => 'swf']) }}" class="btn btn-soft">Statement</a></div>
    </div>
    <div class="table-card"><div class="table-scroll"><table>
        <thead><tr><th>Receipt</th><th>Type</th><th>Amount</th><th>Date</th><th>Reason</th></tr></thead>
        <tbody>
            @forelse($entries as $s)
            <tr><td class="cell-title">{{ $s->receipt_no }}</td><td><span class="tag {{ status_badge($s->type) }}">{{ ucfirst($s->type) }}</span></td><td>@money($s->amount)</td><td>{{ $s->transacted_at?->format('d M Y') }}</td><td>{{ $s->reason ?? '—' }}</td></tr>
            @empty<tr><td colspan="5" class="empty-state">No SWF entries yet.</td></tr>@endforelse
        </tbody>
    </table></div>
    <div class="table-pager"><span class="pager-info">Showing {{ $entries->count() }} of {{ $entries->total() }}</span><div class="pager-pages">{{ $entries->links('pagination.pager') }}</div></div></div>
@endsection
