@extends('layouts.app')
@section('title', 'Application Status')
@section('content')
    <div class="view-head">
        <div><h2>Application {{ ucfirst($app->status) }}</h2><p class="sub">{{ $app->name }} · sent {{ $app->created_at->format('d M Y') }}</p></div>
    </div>

    <div class="panel-grid">
        <div class="panel"><div class="panel-head"><h3>Status</h3><span class="tag {{ status_badge($app->status) }}">{{ ucfirst($app->status) }}</span></div>
            <div class="panel-body">
                @if($app->status === 'pending')
                <p style="font-size:14px;color:var(--ink-soft);line-height:1.7;">Your application is with the office for review. You will get full access to loans, savings, investments and SWF once approved.</p>
                @elseif($app->status === 'approved')
                <p style="font-size:14px;color:var(--ink-soft);line-height:1.7;">Karibu! Your membership was approved@if($member) — {{ $member->member_no }}@endif. Your services menu is now open.</p>
                <div class="view-actions" style="margin-top:14px;"><a href="{{ route('portal.home') }}" class="btn btn-primary">Open my portal</a></div>
                @elseif($app->status === 'rejected')
                <p style="font-size:14px;color:var(--ink-soft);line-height:1.7;">This application was not approved. You can update your details and send it again.</p>
                <form method="POST" action="{{ route('join.restart') }}" style="margin-top:14px;">@csrf<button class="btn btn-primary" type="submit">Start again</button></form>
                @endif
            </div>
        </div>
        <div class="panel"><div class="panel-head"><h3>Your details</h3>@if($app->status === 'draft')<a class="link" href="{{ route('join.step', $app->current_step) }}">Continue →</a>@endif</div>
            <div class="panel-body"><div class="detail-grid">
                <div class="detail-item"><div class="dk">Name</div><div class="dv">{{ $app->name }}</div></div>
                <div class="detail-item"><div class="dk">Phone</div><div class="dv">{{ $app->phone }}</div></div>
                <div class="detail-item"><div class="dk">Email</div><div class="dv">{{ $app->email ?? '—' }}</div></div>
                <div class="detail-item"><div class="dk">Type</div><div class="dv">{{ $app->memberType->name ?? '—' }}</div></div>
            </div></div>
        </div>
    </div>
@endsection
