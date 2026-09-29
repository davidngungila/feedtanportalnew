@extends('layouts.app')
@section('title', 'My Savings')
@section('content')
    <div class="view-head">
        <div><h2>My savings</h2><p class="sub">{{ $member->name }} · Balance <b>@money($savings)</b> · Deposits minus withdrawals.</p></div>
        <div class="view-actions"><a href="{{ route('portal.home') }}" class="btn btn-ghost">Portal</a><a href="{{ route('portal.statements', ['kind' => 'savings']) }}" class="btn btn-soft">Statement</a></div>
    </div>
    <div class="table-card"><div class="table-scroll"><table>
        <thead><tr><th>Receipt</th><th>Type</th><th>Amount</th><th>Method</th><th>Date</th></tr></thead>
        <tbody>
            @forelse($deposits as $d)
            <tr><td class="cell-title">{{ $d->receipt_no }}</td><td><span class="tag {{ $d->type === 'deposit' ? 'tag-green' : 'tag-gold' }}">{{ ucfirst($d->type) }}</span></td><td>@money($d->amount)</td><td>{{ ucfirst($d->method ?? '—') }}</td><td>{{ $d->transacted_at?->format('d M Y') }}</td></tr>
            @empty<tr><td colspan="5" class="empty-state">No savings activity yet.</td></tr>@endforelse
        </tbody>
    </table></div>
    <div class="table-pager"><span class="pager-info">Showing {{ $deposits->count() }} of {{ $deposits->total() }}</span><div class="pager-pages">{{ $deposits->links('pagination.pager') }}</div></div></div>
@endsection
