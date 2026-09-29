@extends('layouts.app')
@section('title', 'Member Documents')
@section('content')
    <div class="view-head"><div><h2>Member Documents</h2><p class="sub">IDs, contracts, photos and proofs per member.</p></div><div class="view-actions"><a href="{{ route('member-documents.create') }}" class="btn btn-primary">+ New document</a></div></div>
    <form method="GET" action="{{ route('member-documents.index') }}">
        <div class="table-card">
            <div class="table-toolbar"><div class="table-search"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"></circle></svg><input name="q" value="{{ $q }}" placeholder="Search title/member…" onchange="this.form.submit()"></div></div>
            <div class="table-scroll"><table>
                <thead><tr><th>Title</th><th>Member</th><th>Type</th><th>Date</th><th style="text-align:right;">Remove</th></tr></thead>
                <tbody>@forelse($documents as $d)<tr><td class="cell-title">{{ $d->title }}</td><td><a href="{{ route('members.show', $d->member) }}">{{ $d->member->name ?? '—' }}</a></td><td><span class="tag tag-grey">{{ $d->doc_type }}</span></td><td>{{ $d->created_at->format('d M Y') }}</td>
                <td><form method="POST" action="{{ route('member-documents.destroy', $d) }}" onsubmit="return confirm('Remove?')">@csrf @method('DELETE')<div class="row-actions"><button type="submit" class="danger">✕</button></div></form></td></tr>
                @empty<tr><td colspan="5" class="empty-state">No documents.</td></tr>@endforelse</tbody>
            </table></div>
            <div class="table-pager"><span class="pager-info">{{ $documents->total() }} documents</span><div class="pager-pages">{{ $documents->links('pagination.pager') }}</div></div>
        </div>
    </form>
@endsection
