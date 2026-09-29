@extends('errors.layout')
@section('title', '408 · Request Timeout')
@section('code', '408')
@section('content')
<div class="error-wrap">
    <div class="error-card" style="--err-tint:var(--gold-100);--err-fg:#8a6418;">
        <span class="error-tag">Error 408</span>
        <div class="error-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
        </div>
        <div class="error-code">408</div>
        <h1>Request timed out</h1>
        <p class="lede">The server took too long to respond. Check your connection and try again — your data was not lost.</p>
        <div class="error-actions">
            <button type="button" class="btn btn-ghost" onclick="window.location.reload()">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="1 4 1 10 7 10"></polyline><path d="M3.51 15a9 9 0 1 0 2.13-9.36L1 10"></path></svg>
                Try again
            </button>
            <a href="{{ url('/dashboard') }}" class="btn btn-primary">Dashboard</a>
        </div>
        <div class="error-foot">Feedtan Portal · Slow network or busy server.</div>
    </div>
</div>
@endsection
