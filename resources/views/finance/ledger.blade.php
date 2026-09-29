@extends('layouts.app')
@section('title', 'General Ledger')
@section('content')
    <div class="view-head"><div><h2>General Ledger</h2><p class="sub">@if($account){{ $account->code }} · {{ $account->name }} · Opening @money($opening) · Closing @money($closing)@else Select an account.@endif</p></div></div>
    <form method="GET" action="{{ route('finance.ledger') }}">
        <div class="table-card">
            <div class="table-toolbar">
                <select name="account_id" onchange="this.form.submit()" style="padding:9px 12px;border:1.5px solid var(--line);border-radius:10px;font-weight:600;">@foreach($accounts as $a)<option value="{{ $a->id }}" {{ (string)$accountId === (string)$a->id ? 'selected' : '' }}>{{ $a->code }} · {{ $a->name }}</option>@endforeach</select>
                <input type="date" name="from" value="{{ $from }}" onchange="this.form.submit()" style="padding:9px 12px;border:1.5px solid var(--line);border-radius:10px;font-weight:600;">
                <input type="date" name="to" value="{{ $to }}" onchange="this.form.submit()" style="padding:9px 12px;border:1.5px solid var(--line);border-radius:10px;font-weight:600;">
            </div>
            @if($account)
            <div class="table-scroll"><table>
                <thead><tr><th>Date</th><th>Reference</th><th>Narration</th><th>Debit</th><th>Credit</th><th>Balance</th></tr></thead>
                <tbody>
                    <tr><td colspan="5" class="cell-sub">Opening balance</td><td class="cell-title">@money($opening)</td></tr>
                    @php $run = $opening; @endphp
                    @forelse($lines as $l)
                        @php
                            $run += in_array($account->type, ['asset', 'expense'], true) ? ($l->debit - $l->credit) : ($l->credit - $l->debit);
                        @endphp
                        <tr><td>{{ $l->entry->entry_date?->format('d M Y') }}</td><td><a href="{{ route('finance.journals.show', $l->entry) }}">{{ $l->entry->reference }}</a></td><td>{{ $l->narration ?? $l->entry->description }}</td><td>@money($l->debit)</td><td>@money($l->credit)</td><td class="cell-title">@money($run)</td></tr>
                    @empty<tr><td colspan="6" class="empty-state">No posted lines in range.</td></tr>@endforelse
                </tbody>
            </table></div>
            <div class="table-pager"><span class="pager-info">Closing: @money($closing)</span><div class="pager-pages">{{ $lines->links('pagination.pager') }}</div></div>
            @endif
        </div>
    </form>
@endsection
