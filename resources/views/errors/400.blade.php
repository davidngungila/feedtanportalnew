@extends('errors.layout')
@section('title', '400 · Bad Request')
@section('code', '400')
@section('content')
<div class="error-wrap">
    <div class="error-content" style="--err-tint:var(--terracotta-100);--err-fg:var(--terracotta-600);">
        <span class="error-tag">Error 400</span>
        <div class="error-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
        </div>
        <div class="error-code">400</div>
        <h1>Bad request</h1>
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
