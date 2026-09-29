@extends('errors.layout')
@section('title', $exception->getStatusCode() . ' · Server Error')
@section('code', (string) $exception->getStatusCode())
@section('content')
<div class="error-wrap">
    <div class="error-content" style="--err-tint:var(--danger-100);--err-fg:var(--danger);">
        <span class="error-tag">Error {{ $exception->getStatusCode() }}</span>
        <div class="error-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"></path></svg>
        </div>
        <div class="error-code">{{ $exception->getStatusCode() }}</div>
        <h1>Server issue</h1>
        <p class="lede">{{ $exception->getMessage() ?: 'An unexpected server error occurred. Please try again shortly.' }}</p>
        <div class="error-actions">
            <button type="button" class="btn btn-ghost" onclick="window.location.reload()">Try again</button>
            <a href="{{ url('/dashboard') }}" class="btn btn-primary">Dashboard</a>
        </div>
        <div class="error-foot">Feedtan Portal · Server error.</div>
    </div>
</div>
@endsection
