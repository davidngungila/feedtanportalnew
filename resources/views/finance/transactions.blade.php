@extends('layouts.app')
@section('title', $title)
@section('content')
    <div class="view-head">
        <div><h2>{{ $title }}</h2><p class="sub">Every entry auto-posts a balanced journal to the ledger. Total here: @money($total)</p></div>
        <div class="view-actions"><a href="{{ route('finance.transactions.create') }}" class="btn btn-primary">+ New entry</a></div>
    </div>

    <form method="GET" action="{{ url()->current() }}">
        <div class="table-card">
            <div class="table-toolbar">
                <input type="date" name="from" value="{{ $from }}" onchange="this.form.submit()" style="padding:9px 12px;border:1.5px solid var(--line);border-radius:10px;font-size:13px;background:var(--white);font-weight:600;">
                <input type="date" name="to" value="{{ $to }}" onchange="this.form.submit()" style="padding:9px 12px;border:1.5px solid var(--line);border-radius:10px;font-size:13px;background:var(--white);font-weight:600;">
                <div class="table-search"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"></circle></svg><input name="q" value="{{ $q }}" placeholder="Search…" onchange="this.form.submit()"></div>
            </div>
            <div class="table-scroll"><table>
                <thead><tr><th>Reference</th><th>Account</th><th>Member</th><th>Type</th><th>Amount</th><th>Date</th><th style="text-align:right;">Remove</th></tr></thead>
                <tbody>
                    @forelse($transactions as $t)
                    <tr><td><div class="cell-title">{{ $t->reference }}</div><div class="cell-sub">{{ $t->categoryLabel() }} · {{ $t->description ?? '—' }}</div></td>
                    <td>{{ $t->account->name ?? '—' }}</td><td>{{ $t->member->name ?? '—' }}</td>
                    <td><span class="tag {{ $t->type === 'income' ? 'tag-green' : 'tag-red' }}">{{ ucfirst($t->type) }}</span></td>
                    <td class="cell-title">@money($t->amount)</td><td>{{ $t->transacted_at?->format('d M Y') }}</td>
                    <td><form method="POST" action="{{ route('finance.transactions.destroy', $t) }}" onsubmit="return confirm('Remove entry and reverse its journal?')">@csrf @method('DELETE')<div class="row-actions"><button type="submit" class="danger">✕</button></div></form></td></tr>
                    @empty<tr><td colspan="7" class="empty-state">No entries in this view.</td></tr>@endforelse
                </tbody>
            </table></div>
            <div class="table-pager"><span class="pager-info">{{ $transactions->total() }} entries · @money($total)</span><div class="pager-pages">{{ $transactions->links('pagination.pager') }}</div></div>
        </div>
    </form>
@endsection
