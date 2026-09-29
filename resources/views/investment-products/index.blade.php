@extends('layouts.app')
@section('title', 'Investment Products')
@section('content')
    <div class="view-head"><div><h2>Investment Products</h2><p class="sub">Reusable return terms.</p></div><div class="view-actions"><a href="{{ route('investment-products.create') }}" class="btn btn-primary">+ New product</a></div></div>
    <div class="table-card"><div class="table-scroll"><table>
        <thead><tr><th>Name</th><th>Return</th><th>Min amount</th><th>Duration</th><th>Investments</th><th>Status</th><th style="text-align:right;">Actions</th></tr></thead>
        <tbody>@forelse($products as $p)<tr><td class="cell-title">{{ $p->name }}</td><td>{{ $p->return_rate }}%</td><td>@money($p->min_amount)</td><td>{{ $p->duration_months }} mo</td><td>{{ $p->investments_count }}</td><td><span class="tag {{ status_badge($p->status) }}">{{ ucfirst($p->status) }}</span></td>
        <td><div class="row-actions"><a href="{{ route('investment-products.edit', $p) }}"><button type="button">Edit</button></a><form method="POST" action="{{ route('investment-products.destroy', $p) }}" onsubmit="return confirm('Remove?')">@csrf @method('DELETE')<button type="submit" class="danger">✕</button></form></div></td></tr>
        @empty<tr><td colspan="7" class="empty-state">No products.</td></tr>@endforelse</tbody>
    </table></div><div class="table-pager"><span class="pager-info">{{ $products->total() }} products</span><div class="pager-pages">{{ $products->links('pagination.pager') }}</div></div></div>
@endsection
