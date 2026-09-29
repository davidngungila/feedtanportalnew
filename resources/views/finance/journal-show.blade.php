@extends('layouts.app')
@section('title', $journal->reference)
@section('content')
    <div class="view-head"><div><h2>{{ $journal->reference }}</h2><p class="sub">{{ $journal->description }} · {{ $journal->entry_date?->format('d M Y') }}</p></div><div class="view-actions"><a href="{{ route('finance.journals') }}" class="btn btn-ghost">Back</a></div></div>
    <div class="table-card"><div class="table-scroll"><table>
        <thead><tr><th>Account</th><th>Narration</th><th>Debit</th><th>Credit</th></tr></thead>
        <tbody>@foreach($journal->lines as $l)<tr><td class="cell-title">{{ $l->account->code }} · {{ $l->account->name }}</td><td>{{ $l->narration ?? '—' }}</td><td>@money($l->debit)</td><td>@money($l->credit)</td></tr>@endforeach</tbody>
        <tfoot><tr><td colspan="2" class="cell-title" style="padding:14px 18px;">Total</td><td class="cell-title" style="padding:14px 18px;">@money($journal->totalDebit())</td><td class="cell-title" style="padding:14px 18px;">@money($journal->totalCredit())</td></tr></tfoot>
    </table></div></div>
    @if(!$journal->source_type)
    <form method="POST" action="{{ route('finance.journals.destroy', $journal) }}" onsubmit="return confirm('Remove journal?')">@csrf @method('DELETE')<button class="btn btn-danger" type="submit">Delete journal</button></form>
    @else
    <div class="receipt"><div class="receipt-row"><span>Source</span><b>Auto-posted — remove the source record to reverse</b></div></div>
    @endif
@endsection
