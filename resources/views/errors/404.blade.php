@extends('errors.layout')
@section('title', '404 · Page Not Found')
@section('code', '404')
@section('content')
<div class="error-wrap">
    <div class="error-card" style="--err-tint:var(--terracotta-100);--err-fg:var(--terracotta-600);">
        <span class="error-tag">Error 404</span>
        <div class="error-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line><line x1="11" y1="8" x2="11" y2="14"></line><line x1="8" y1="11" x2="14" y2="11"></line></svg>
        </div>
        <div class="error-code">404</div>
        <h1>Page not found</h1>
        <p class="lede">Sorry — the page you are looking for does not exist or was moved. Check the address, or use the workspace navigation to find members, loans, deposits or reports.</p>
        <div class="error-actions">
            <button type="button" class="btn btn-ghost" onclick="goBack()">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
                Go back
            </button>
            <a href="{{ url('/dashboard') }}" class="btn btn-primary">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>
                Dashboard
            </a>
        </div>
        <div class="error-links">Looking for something? <a href="{{ url('/members') }}">Members</a> · <a href="{{ url('/loans') }}">Loans</a> · <a href="{{ url('/dashboard') }}">Home</a></div>
        <div class="error-foot">Feedtan Portal · Error 404 — missing route.</div>
    </div>
</div>
@endsection
