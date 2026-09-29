@extends('layouts.app')
@section('title', 'Balance Sheet')
@section('content')
    <div class="view-head"><div><h2>Balance Sheet</h2><p class="sub">As of {{ $asOf }} · Check: @money($totalAssets - $totalLiab - $totalEquity)</p></div>
        <div class="view-actions"><form method="GET" action="{{ route('finance.balance-sheet') }}"><input type="date" name="as_of" value="{{ $asOf }}" onchange="this.form.submit()" style="padding:9px 12px;border:1.5px solid var(--line);border-radius:10px;font-weight:600;"></form><button class="btn btn-ghost btn-sm" onclick="window.print()">Print</button></div>
    </div>
    <div class="panel-grid">
        <div class="panel"><div class="panel-head"><h3>Assets</h3><span class="link">@money($totalAssets)</span></div>
            <div class="table-scroll"><table style="min-width:420px;"><tbody>
                @foreach($assets as $r)<tr><td>{{ $r['account']->code }} · {{ $r['account']->name }}</td><td class="cell-title">@money($r['amount'])</td></tr>@endforeach
                <tr><td>Member loans outstanding (module)</td><td class="cell-title">@money($loanOut)</td></tr>
                <tr><td>Member investments active (module)</td><td class="cell-title">@money($invested)</td></tr>
                <tr><td class="cell-title" style="padding:14px 18px;">Total assets</td><td class="cell-title" style="padding:14px 18px;">@money($totalAssets)</td></tr>
            </tbody></table></div>
        </div>
        <div class="panel"><div class="panel-head"><h3>Liabilities + Equity</h3><span class="link">@money($totalLiab + $totalEquity)</span></div>
            <div class="table-scroll"><table style="min-width:420px;"><tbody>
                @foreach($liabilities as $r)<tr><td>{{ $r['account']->code }} · {{ $r['account']->name }}</td><td class="cell-title">@money($r['amount'])</td></tr>@endforeach
                <tr><td>Member savings net (module)</td><td class="cell-title">@money($savingsNet)</td></tr>
                <tr><td>SWF balance (module)</td><td class="cell-title">@money($swfNet)</td></tr>
                @foreach($equity as $r)<tr><td>{{ $r['account']->code }} · {{ $r['account']->name }}</td><td class="cell-title">@money($r['amount'])</td></tr>@endforeach
                <tr><td class="cell-title" style="padding:14px 18px;">Total L + E</td><td class="cell-title" style="padding:14px 18px;">@money($totalLiab + $totalEquity)</td></tr>
            </tbody></table></div>
        </div>
    </div>
@endsection
