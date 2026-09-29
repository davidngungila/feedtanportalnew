@extends('errors.layout')
@section('title', '403 · Forbidden')
@section('code', '403')
@section('content')
<div class="error-wrap">
    <div class="error-content" style="--err-tint:var(--gold-100);--err-fg:#8a6418;">
        <span class="error-tag">Error 403</span>
        <div class="error-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path><line x1="4.93" y1="4.93" x2="19.07" y2="19.07"></line></svg>
        </div>
        <div class="error-code">403</div>
        <h1>Access forbidden</h1>
        @if(config('app.debug') && isset($exception) && $exception->getMessage())
            <div class="error-meta"><code>{{ $exception->getMessage() }}</code></div>
        @endif
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
    </div>
</div>
@endsection
