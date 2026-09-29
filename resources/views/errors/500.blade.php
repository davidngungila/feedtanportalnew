@extends('errors.layout')
@section('title', '500 · Server Error')
@section('code', '500')
@section('content')
<div class="error-wrap">
    <div class="error-content" style="--err-tint:var(--danger-100);--err-fg:var(--danger);">
        <span class="error-tag">Error 500</span>
        <div class="error-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"></path></svg>
        </div>
        <div class="error-code">500</div>
        <h1>Something went wrong</h1>
        @if(config('app.debug') && isset($exception))
            <div class="error-meta"><code>{{ $exception->getMessage() ?: get_class($exception) }}</code><br>File <code>{{ $exception->getFile() }}:{{ $exception->getLine() }}</code></div>
        @endif
        <div class="error-actions">
            <button type="button" class="btn btn-ghost" onclick="window.location.reload()">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="1 4 1 10 7 10"></polyline><path d="M3.51 15a9 9 0 1 0 2.13-9.36L1 10"></path></svg>
                Try again
            </button>
            <a href="{{ url('/dashboard') }}" class="btn btn-primary">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>
                Dashboard
            </a>
        </div>
    </div>
</div>
@endsection
