@extends('errors.layout')
@section('title', $exception->getStatusCode() . ' · Request Error')
@section('code', (string) $exception->getStatusCode())
@section('content')
<div class="error-wrap">
    <div class="error-content" style="--err-tint:var(--terracotta-100);--err-fg:var(--terracotta-600);">
        <span class="error-tag">Error {{ $exception->getStatusCode() }}</span>
        <div class="error-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
        </div>
        <div class="error-code">{{ $exception->getStatusCode() }}</div>
        <h1>Something needs attention</h1>
        <div class="error-actions">
            <button type="button" class="btn btn-ghost" onclick="goBack()">Go back</button>
            <a href="{{ url('/dashboard') }}" class="btn btn-primary">Dashboard</a>
        </div>
    </div>
</div>
@endsection
