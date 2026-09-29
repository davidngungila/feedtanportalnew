@extends('layouts.app')
@section('title', 'Edit Investment')
@section('content')
    <div class="view-head"><div><h2>Edit Investment</h2><p class="sub">{{ $investment->investment_no }}</p></div><div class="view-actions"><a href="{{ route('investments.show', $investment) }}" class="btn btn-ghost">Back</a></div></div>
    <div class="settings-panel"><h3>Investment details</h3>
        <form method="POST" action="{{ route('investments.update', $investment) }}">@csrf @method('PUT')
            <div class="field"><label>Status *</label><select name="status">@foreach(['active','matured','withdrawn'] as $s)<option value="{{ $s }}" {{ $investment->status === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>@endforeach</select></div>
            <div class="field"><label>Maturity date</label><input type="date" name="maturity_date" value="{{ $investment->maturity_date?->format('Y-m-d') }}"></div>
            <div class="field"><label>Notes</label><textarea name="notes" rows="3">{{ $investment->notes }}</textarea></div>
            <button class="btn btn-primary" type="submit">Save changes</button>
        </form>
    </div>
@endsection
