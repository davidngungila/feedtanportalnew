@extends('errors.layout')
@section('title', '429 · Too Many Requests')
@section('code', '429')
@section('content')
<div class="error-wrap">
    <div class="error-content" style="--err-tint:var(--gold-100);--err-fg:#8a6418;">
        <span class="error-tag">Error 429</span>
        <div class="error-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon></svg>
        </div>
        <div class="error-code">429</div>
        <h1>Slow down</h1>
        <p class="lede">You sent too many requests in a short time, so further requests were paused to protect the system. Wait a moment, then try again.</p>
        <div class="error-actions">
            <button type="button" class="btn btn-ghost" onclick="goBack()">Go back</button>
            <button type="button" class="btn btn-primary" onclick="setTimeout(()=>window.location.reload(),800);this.disabled=true;this.textContent='Retrying…'">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="1 4 1 10 7 10"></polyline><path d="M3.51 15a9 9 0 1 0 2.13-9.36L1 10"></path></svg>
                Try again
            </button>
        </div>
        <div class="error-foot">Feedtan Portal · Rate limit reached. Please wait a minute.</div>
    </div>
</div>
@endsection
