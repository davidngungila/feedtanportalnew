@extends('errors.layout')
@section('title', '422 · Unprocessable Entity')
@section('code', '422')
@section('content')
<div class="error-wrap">
    <div class="error-card" style="--err-tint:var(--terracotta-100);--err-fg:var(--terracotta-600);">
        <span class="error-tag">Error 422</span>
        <div class="error-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>
        </div>
        <div class="error-code">422</div>
        <h1>Could not process</h1>
        <p class="lede">The data you sent could not be processed. Something failed validation — go back, review the highlighted fields, and submit again.</p>
        @if(config('app.debug') && isset($exception) && $exception->getMessage())
            <div class="error-meta"><code>{{ $exception->getMessage() }}</code></div>
        @endif
        <div class="error-actions">
            <button type="button" class="btn btn-ghost" onclick="goBack()">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
                Go back &amp; fix
            </button>
            <a href="{{ url('/dashboard') }}" class="btn btn-primary">Dashboard</a>
        </div>
        <div class="error-foot">Feedtan Portal · Validation failed.</div>
    </div>
</div>
@endsection
