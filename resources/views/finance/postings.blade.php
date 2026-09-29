@php $canBooks = is_role('administrator', 'chairperson', 'accountant', 'secretary'); @endphp
<div class="table-card">
    <div class="table-toolbar"><span class="chip active">Ledger postings ({{ $journals->count() }})</span>@if($canBooks)<a class="link" href="{{ route('finance.journals') }}">All journals</a>@endif</div>
    <div class="table-scroll"><table>
        <thead><tr><th>Reference</th><th>Date</th><th>Description</th><th>Debit</th><th>Credit</th></tr></thead>
        <tbody>
            @forelse($journals as $j)
            <tr>
                <td class="cell-title">@if($canBooks)<a href="{{ route('finance.journals.show', $j) }}">{{ $j->reference }}</a>@else{{ $j->reference }}@endif</td>
                <td>{{ $j->entry_date?->format('d M Y') }}</td><td>{{ $j->description }}</td>
                <td>@money($j->totalDebit())</td><td>@money($j->totalCredit())</td>
            </tr>
            @empty<tr><td colspan="5" class="empty-state">No ledger postings yet.</td></tr>@endforelse
        </tbody>
    </table></div>
</div>
