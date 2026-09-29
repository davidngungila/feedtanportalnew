@extends('errors.layout')
@section('title', '503 · Service Unavailable')
@section('code', '503')
@section('content')
<div class="error-wrap">
    <div class="error-content" style="--err-tint:var(--danger-100);--err-fg:var(--danger);">
        <span class="error-tag">Error 503</span>
        <div class="error-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path><path d="m9 12 2 2 4-4"></path></svg>
        </div>
        <div class="error-code">503</div>
        <h1>Service unavailable</h1>
        <p class="lede">Feedtan Portal is temporarily down for maintenance or is overloaded. Please wait a little while, then refresh the page.</p>
        <div class="error-actions">
            <button type="button" class="btn btn-ghost" onclick="window.location.reload()">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="1 4 1 10 7 10"></polyline><path d="M3.51 15a9 9 0 1 0 2.13-9.36L1 10"></path></svg>
                Refresh
            </button>
            <a href="{{ url('/') }}" class="btn btn-primary">Home</a>
        </div>
        <div class="error-foot">Feedtan Portal · Maintenance mode or overloaded.</div>
    </div>
</div>
@endsection
