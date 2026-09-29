@extends('errors.layout')
@section('title', '401 · Unauthorized')
@section('code', '401')
@section('content')
<div class="error-wrap">
    <div class="error-content" style="--err-tint:var(--gold-100);--err-fg:#8a6418;">
        <span class="error-tag">Error 401</span>
        <div class="error-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
        </div>
        <div class="error-code">401</div>
        <h1>Sign in required</h1>
        <p class="lede">You need to be signed in to view this page. Your session may have expired — please sign in again to continue working in Feedtan Portal.</p>
        <div class="error-actions">
            <button type="button" class="btn btn-ghost" onclick="goBack()">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
                Go back
            </button>
            <a href="{{ url('/login') }}" class="btn btn-primary">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"></path><polyline points="10 17 15 12 10 7"></polyline><line x1="15" y1="12" x2="3" y2="12"></line></svg>
                Sign in
            </a>
        </div>
        <div class="error-foot">Feedtan Portal · Protected workspace page.</div>
    </div>
</div>
@endsection
