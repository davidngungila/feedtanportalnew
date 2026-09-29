@extends('layouts.app')
@section('title', 'My Statements')
@section('content')
    <div class="view-head">
        <div><h2>My statements</h2><p class="sub">{{ $member->name }} · Combined history across savings, loans, investments and SWF.</p></div>
        <div class="view-actions"><a href="{{ route('portal.home') }}" class="btn btn-ghost">Portal</a></div>
    </div>
    <form method="GET" action="{{ route('portal.statements') }}">
        <div class="table-card">
            <div class="table-toolbar">
                <div class="chip-filters">
                    @foreach(['all' => 'All', 'savings' => 'Savings', 'loans' => 'Loans', 'investments' => 'Investments', 'swf' => 'SWF'] as $k => $label)
                    <button type="submit" name="kind" value="{{ $k }}" class="chip {{ $kind === $k ? 'active' : '' }}">{{ $label }}</button>
                    @endforeach
                </div>
                <div class="table-search" style="min-width:140px;"><input type="date" name="from" value="{{ $from }}" onchange="this.form.submit()"></div>
                <div class="table-search" style="min-width:140px;"><input type="date" name="to" value="{{ $to }}" onchange="this.form.submit()"></div>
            </div>
            <div class="table-scroll"><table>
                <thead><tr><th>Date</th><th>Service</th><th>Reference</th><th>Detail</th><th>In</th><th>Out</th></tr></thead>
                <tbody>
                    @forelse($rows as $r)
                    <tr><td>{{ $r['date'] }}</td><td><span class="tag {{ $r['kind'] === 'savings' ? 'tag-green' : ($r['kind'] === 'swf' ? 'tag-gold' : 'tag-terracotta') }}">{{ ucfirst($r['kind']) }}</span></td><td class="cell-title">{{ $r['ref'] }}</td><td>{{ $r['detail'] }}</td><td>@money($r['in'])</td><td>@money($r['out'])</td></tr>
                    @empty<tr><td colspan="6" class="empty-state">No activity for the selected filters.</td></tr>@endforelse
                </tbody>
            </table></div>
            <div class="table-pager"><span class="pager-info">{{ count($rows) }} rows · In @money($totalIn) · Out @money($totalOut)</span></div>
        </div>
    </form>
@endsection
