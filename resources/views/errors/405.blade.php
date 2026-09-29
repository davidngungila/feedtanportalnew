@extends('errors.layout')
@section('title', '405 · Method Not Allowed')
@section('code', '405')
@section('content')
<div class="error-wrap">
    <div class="error-card" style="--err-tint:var(--terracotta-100);--err-fg:var(--terracotta-600);">
        <span class="error-tag">Error 405</span>
        <div class="error-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="4.93" y1="4.93" x2="19.07" y2="19.07"></line></svg>
        </div>
        <div class="error-code">405</div>
        <h1>Method not allowed</h1>
        <p class="lede">This address does not support the action you tried (for example, refreshing a form submission). Go back and try the action from the workspace instead.</p>
        <div class="error-actions">
            <button type="button" class="btn btn-ghost" onclick="goBack()">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
                Go back
            </button>
            <a href="{{ url('/dashboard') }}" class="btn btn-primary">Dashboard</a>
        </div>
        <div class="error-foot">Feedtan Portal · HTTP verb not supported here.</div>
    </div>
</div>
@endsection
