@extends('errors.layout')
@section('title', '504 · Gateway Timeout')
@section('code', '504')
@section('content')
<div class="error-wrap">
    <div class="error-card" style="--err-tint:var(--danger-100);--err-fg:var(--danger);">
        <span class="error-tag">Error 504</span>
        <div class="error-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
        </div>
        <div class="error-code">504</div>
        <h1>Gateway timeout</h1>
        <p class="lede">The upstream server did not respond in time. Check your connection, wait a moment, and try again.</p>
        <div class="error-actions">
            <button type="button" class="btn btn-ghost" onclick="window.location.reload()">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="1 4 1 10 7 10"></polyline><path d="M3.51 15a9 9 0 1 0 2.13-9.36L1 10"></path></svg>
                Try again
            </button>
            <a href="{{ url('/dashboard') }}" class="btn btn-primary">Dashboard</a>
        </div>
        <div class="error-foot">Feedtan Portal · Upstream timeout.</div>
    </div>
</div>
@endsection
