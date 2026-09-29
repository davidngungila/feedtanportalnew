@extends('errors.layout')
@section('title', '419 · Page Expired')
@section('code', '419')
@section('content')
<div class="error-wrap">
    <div class="error-card" style="--err-tint:var(--gold-100);--err-fg:#8a6418;">
        <span class="error-tag">Error 419</span>
        <div class="error-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="1 4 1 10 7 10"></polyline><path d="M3.51 15a9 9 0 1 0 2.13-9.36L1 10"></path></svg>
        </div>
        <div class="error-code">419</div>
        <h1>Page expired</h1>
        <p class="lede">Your session expired while the page was open (usually after being idle). Sign in again, then retry what you were doing.</p>
        <div class="error-actions">
            <button type="button" class="btn btn-ghost" onclick="window.location.reload()">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="1 4 1 10 7 10"></polyline><path d="M3.51 15a9 9 0 1 0 2.13-9.36L1 10"></path></svg>
                Refresh
            </button>
            <a href="{{ url('/login') }}" class="btn btn-primary">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"></path><polyline points="10 17 15 12 10 7"></polyline><line x1="15" y1="12" x2="3" y2="12"></line></svg>
                Sign in again
            </a>
        </div>
        <div class="error-foot">Feedtan Portal · CSRF token / session expired.</div>
    </div>
</div>
@endsection
