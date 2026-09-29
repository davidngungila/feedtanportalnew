@extends('layouts.app')
@section('title', 'Application Status')
@section('content')
    <div class="view-head">
        <div><h2>Application {{ ucfirst($app->status) }}</h2><p class="sub">{{ $app->name }} · ref #{{ $app->id }} · sent {{ $app->created_at->format('d M Y') }}</p></div>
        @if($app->status === 'approved')<div class="view-actions"><a href="{{ route('portal.home') }}" class="btn btn-primary">Open my portal</a></div>@endif
    </div>

    <div class="strip-4">
        <div class="balance-box" style="border-color:var(--acacia-500);"><div class="bb-label">✓ Step 1 · Account</div><div class="bb-amount" style="font-size:15px;">Created</div><div class="bb-sub">{{ $app->created_at->format('d M Y') }}</div></div>
        <div class="balance-box" style="border-color:var(--acacia-500);"><div class="bb-label">✓ Step 2 · Details</div><div class="bb-amount" style="font-size:15px;">Submitted</div><div class="bb-sub">{{ $app->updated_at->format('d M Y') }}</div></div>
        <div class="balance-box" style="@if($app->status === 'pending')border-color:var(--gold-500);box-shadow:0 0 0 3px var(--gold-100);@elseif($app->status === 'approved')border-color:var(--acacia-500);@elseif($app->status === 'rejected')border-color:var(--danger);@endif"><div class="bb-label">@if($app->status === 'pending')● Step 3 · Review @elseif($app->status === 'approved')✓ Step 3 · Review @else Step 3 · Review @endif</div><div class="bb-amount" style="font-size:15px;">{{ ucfirst($app->status) }}</div><div class="bb-sub">@if($app->reviewed_by)By {{ \App\Models\User::find($app->reviewed_by)->name ?? 'office' }}@else Awaiting office @endif</div></div>
        <div class="balance-box" style="@if($app->status === 'approved')border-color:var(--acacia-500);@endif"><div class="bb-label">@if($app->status === 'approved')✓ Step 4 · Services @else ○ Step 4 · Services @endif</div><div class="bb-amount" style="font-size:15px;">@if($app->status === 'approved') Unlocked @else Locked @endif</div><div class="bb-sub">@if($app->status === 'approved' && $member){{ $member->member_no }}@else Loans · savings · investments · SWF @endif</div></div>
    </div>

    <div class="panel-grid">
        <div class="panel"><div class="panel-head"><h3>Status</h3><span class="tag {{ status_badge($app->status) }}">{{ ucfirst($app->status) }}</span></div>
            <div class="panel-body">
                @if($app->status === 'pending')
                <p style="font-size:14px;color:var(--ink-soft);line-height:1.7;">Your application is with the office for review. You will get full access to loans, savings, investments and SWF once approved. Nothing more to do — check back here for the decision.</p>
                @elseif($app->status === 'approved')
                <p style="font-size:14px;color:var(--ink-soft);line-height:1.7;">Karibu! Your membership was approved
                @if($member)
                — member number <b>{{ $member->member_no }}</b>, joined {{ $member->join_date?->format('d M Y') }}
                @endif
                . Your services menu is now open in the sidebar.</p>
                <div class="view-actions" style="margin-top:14px;"><a href="{{ route('portal.home') }}" class="btn btn-primary">Open my portal</a></div>
                @elseif($app->status === 'rejected')
                <p style="font-size:14px;color:var(--ink-soft);line-height:1.7;">This application was not approved. You can update your details and send it again — your previous answers are kept.</p>
                <form method="POST" action="{{ route('join.restart') }}" style="margin-top:14px;">@csrf<button class="btn btn-primary" type="submit">Update &amp; send again</button></form>
                @elseif($app->status === 'draft')
                <p style="font-size:14px;color:var(--ink-soft);line-height:1.7;">You have not sent this application yet — finish the remaining steps first.</p>
                <div class="view-actions" style="margin-top:14px;"><a href="{{ route('join.step', eid($app->current_step)) }}" class="btn btn-primary">Continue step {{ $app->current_step }}</a></div>
                @endif
            </div>
        </div>
        <div class="panel"><div class="panel-head"><h3>Your details</h3>@if($app->status === 'draft')<a class="link" href="{{ route('join.step', eid($app->current_step)) }}">Continue →</a>@endif</div>
            <div class="panel-body"><div class="detail-grid">
                <div class="detail-item"><div class="dk">Name</div><div class="dv">{{ $app->name }}</div></div>
                <div class="detail-item"><div class="dk">Phone</div><div class="dv">{{ $app->phone }}</div></div>
                <div class="detail-item"><div class="dk">National ID</div><div class="dv">{{ $app->national_id ?? '—' }}</div></div>
                <div class="detail-item"><div class="dk">Email</div><div class="dv">{{ $app->email ?? '—' }}</div></div>
                <div class="detail-item"><div class="dk">Address</div><div class="dv">{{ $app->address ?? '—' }}</div></div>
                <div class="detail-item"><div class="dk">Type</div><div class="dv">{{ $app->memberType->name ?? '—' }}</div></div>
            </div></div>
        </div>
    </div>
@endsection
