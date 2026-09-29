@extends('layouts.app')
@section('title', $title)
@section('content')
    <div class="view-head"><div><h2>{{ $title }}</h2><p class="sub">Outstanding {{ strtolower($title) }}: @money($outstanding)</p></div><div class="view-actions"><a href="{{ route('finance.receivables.create', ['kind' => $kind]) }}" class="btn btn-primary">+ New {{ strtolower(substr($title, 0, -1)) }}</a></div></div>
    <div class="table-card"><div class="table-scroll"><table>
        <thead><tr><th>Reference</th><th>Party</th><th>Amount</th><th>Paid</th><th>Outstanding</th><th>Due</th><th>Status</th><th style="text-align:right;">Actions</th></tr></thead>
        <tbody>@forelse($rows as $r)<tr><td class="cell-title">{{ $r->reference }}</td><td>{{ $r->party_name }}</td><td>@money($r->amount)</td><td>@money($r->paid_amount)</td><td class="cell-title">@money($r->outstanding())</td><td>{{ $r->due_date?->format('d M Y') ?? '—' }}</td>
        <td><span class="tag {{ status_badge($r->status) }}">{{ ucfirst($r->status) }}</span></td>
        <td><div class="row-actions" style="justify-content:flex-end;">
            @if($r->outstanding() > 0)
            <form method="POST" action="{{ route('finance.receivables.collect', $r) }}" style="display:flex;gap:6px;"><input type="number" name="amount" min="100" max="{{ $r->outstanding() }}" step="100" placeholder="Pay" required style="width:110px;padding:7px 10px;border:1.5px solid var(--line);border-radius:8px;">@csrf<button class="btn btn-primary btn-sm" type="submit">Pay</button></form>
            @endif
            <form method="POST" action="{{ route('finance.receivables.destroy', $r) }}" onsubmit="return confirm('Remove?')">@csrf @method('DELETE')<button type="submit" class="danger">✕</button></form>
        </div></td></tr>
        @empty<tr><td colspan="8" class="empty-state">Nothing here.</td></tr>@endforelse</tbody>
    </table></div><div class="table-pager"><span class="pager-info">{{ $rows->total() }} records</span><div class="pager-pages">{{ $rows->links('pagination.pager') }}</div></div></div>
@endsection
