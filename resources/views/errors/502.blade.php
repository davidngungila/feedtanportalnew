@extends('errors.layout')
@section('title', '502 · Bad Gateway')
@section('code', '502')
@section('content')
<div class="error-wrap">
    <div class="error-card" style="--err-tint:var(--danger-100);--err-fg:var(--danger);">
        <span class="error-tag">Error 502</span>
        <div class="error-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="3" width="20" height="7" rx="2" ry="2"></rect><rect x="2" y="14" width="20" height="7" rx="2" ry="2"></rect><line x1="6" y1="6.5" x2="6.01" y2="6.5"></line><line x1="6" y1="17.5" x2="6.01" y2="17.5"></line></svg>
        </div>
        <div class="error-code">502</div>
        <h1>Bad gateway</h1>
        <p class="lede">The server received an invalid response from upstream. This is usually temporary — wait a few seconds and try again.</p>
        <div class="error-actions">
            <button type="button" class="btn btn-ghost" onclick="window.location.reload()">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="1 4 1 10 7 10"></polyline><path d="M3.51 15a9 9 0 1 0 2.13-9.36L1 10"></path></svg>
                Try again
            </button>
            <a href="{{ url('/dashboard') }}" class="btn btn-primary">Dashboard</a>
        </div>
        <div class="error-foot">Feedtan Portal · Upstream gateway error.</div>
    </div>
</div>
@endsection
