@extends('layouts.app')
@section('title', 'Income Statement')
@section('content')
    <div class="view-head"><div><h2>Income Statement</h2><p class="sub">{{ $from }} → {{ $to }} · Surplus: @money($totalRevenue - $totalExpenses)</p></div>
        <div class="view-actions"><form method="GET" action="{{ route('finance.income-statement') }}" style="display:flex;gap:8px;"><input type="date" name="from" value="{{ $from }}" onchange="this.form.submit()" style="padding:9px 12px;border:1.5px solid var(--line);border-radius:10px;font-weight:600;"><input type="date" name="to" value="{{ $to }}" onchange="this.form.submit()" style="padding:9px 12px;border:1.5px solid var(--line);border-radius:10px;font-weight:600;"></form><button class="btn btn-ghost btn-sm" onclick="window.print()">Print</button></div>
    </div>
    <div class="panel-grid">
        <div class="panel"><div class="panel-head"><h3>Revenue</h3><span class="link">@money($totalRevenue)</span></div>
            <div class="table-scroll"><table style="min-width:420px;"><tbody>@forelse($revenues as $r)<tr><td>{{ $r['account']->code }} · {{ $r['account']->name }}</td><td class="cell-title">@money($r['amount'])</td></tr>@empty<tr><td class="empty-state">No revenue in period.</td></tr>@endforelse<tr><td class="cell-title" style="padding:14px 18px;">Total revenue</td><td class="cell-title" style="padding:14px 18px;">@money($totalRevenue)</td></tr></tbody></table></div>
        </div>
        <div class="panel"><div class="panel-head"><h3>Expenses</h3><span class="link">@money($totalExpenses)</span></div>
            <div class="table-scroll"><table style="min-width:420px;"><tbody>@forelse($expenses as $r)<tr><td>{{ $r['account']->code }} · {{ $r['account']->name }}</td><td class="cell-title">@money($r['amount'])</td></tr>@empty<tr><td class="empty-state">No expenses in period.</td></tr>@endforelse<tr><td class="cell-title" style="padding:14px 18px;">Total expenses</td><td class="cell-title" style="padding:14px 18px;">@money($totalExpenses)</td></tr></tbody></table></div>
        </div>
    </div>
    <div class="balance-strip"><div class="balance-box"><div class="bb-label">Net surplus / (deficit)</div><div class="bb-amount">@money($totalRevenue - $totalExpenses)</div></div></div>
@endsection
