@extends('layouts.app')
@section('title', 'Become a Member')
@section('content')
    <div class="view-head">
        <div><h2>Become a member</h2><p class="sub">Read how joining works, then continue with your registration.</p></div>
        <div class="view-actions"><a href="{{ route('join.status') }}" class="btn btn-ghost">Application status</a></div>
    </div>

    <div class="panel-grid">
        <div class="panel">
            <div class="panel-head"><h3>How it works</h3><span class="link">5 steps</span></div>
            <div class="panel-body">
                <div class="toggle-row"><div class="toggle-text"><strong>Step 1 · Personal details</strong><span>Name, sex, birth date, phone and NIDA ID — verified against your ID. Your progress saves as you go, so you can come back anytime.</span></div></div>
                <div class="toggle-row"><div class="toggle-text"><strong>Step 2 · Contact &amp; work</strong><span>Email, address, job and statement channel — used for approval notices, verification codes and statements.</span></div></div>
                <div class="toggle-row"><div class="toggle-text"><strong>Step 3 · Bank &amp; payments</strong><span>Bank details, contribution ticks, payment references and slip evidence.</span></div></div>
                <div class="toggle-row"><div class="toggle-text"><strong>Step 4 · Membership &amp; people</strong><span>Type, referrer, bibliography, beneficiaries and savings goal.</span></div></div>
                <div class="toggle-row"><div class="toggle-text"><strong>Step 5 · Review &amp; submit</strong><span>Check everything once more, then send. Nothing is sent before you press submit.</span></div></div>
                <div class="toggle-row"><div class="toggle-text"><strong>Office review</strong><span>The office verifies your details, usually within a few days. Approved accounts unlock loans, savings, investments and SWF.</span></div></div>
            </div>
        </div>
        <div class="panel">
            <div class="panel-head"><h3>Your registration</h3><span class="tag tag-gold">Step {{ $app->current_step }} of 5</span></div>
            <div class="panel-body">
                <div class="detail-grid">
                    <div class="detail-item"><div class="dk">Name</div><div class="dv">{{ $app->name ?: '—' }}</div></div>
                    <div class="detail-item"><div class="dk">Phone</div><div class="dv">{{ $app->phone ?: '—' }}</div></div>
                    <div class="detail-item"><div class="dk">Started</div><div class="dv">{{ $app->created_at->format('d M Y') }}</div></div>
                    <div class="detail-item"><div class="dk">Application ref</div><div class="dv">#{{ $app->id }}</div></div>
                </div>
                <div class="receipt">
                    @foreach($steps as $n => $label)
                    <div class="receipt-row">
                        <span>
                            @if($n < $app->current_step) ✓ @endif
                            @if($n === $app->current_step) ● @endif
                            @if($n > $app->current_step) ○ @endif
                            {{ $label }}
                        </span>
                        <b>
                            @if($n < $app->current_step) Done @endif
                            @if($n === $app->current_step) Now @endif
                            @if($n > $app->current_step) Waiting @endif
                        </b>
                    </div>
                    @endforeach
                </div>
                <div class="view-actions" style="margin-top:16px;">
                    @if($app->current_step > 1 || $app->name)
                    <a href="{{ route('join.step', eid($app->current_step)) }}" class="btn btn-primary">Continue step {{ $app->current_step }} →</a>
                    @else
                    <a href="{{ route('join.step', eid($app->current_step)) }}" class="btn btn-primary">Start step 1 →</a>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
