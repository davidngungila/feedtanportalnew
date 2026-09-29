@extends('layouts.app')

@section('title', 'Edit Loan')

@section('content')
    <div class="view-head"><div><h2>Edit Loan</h2><p class="sub">{{ $loan->loan_no }}</p></div><div class="view-actions"><a href="{{ route('loans.show', $loan) }}" class="btn btn-ghost">Back</a></div></div>
    <div class="settings-panel"><h3>Loan details</h3>
        <form method="POST" action="{{ route('loans.update', $loan) }}">@csrf @method('PUT')
            <div class="field"><label>Status *</label><select name="status">@foreach(['pending','active','paid','overdue','defaulted'] as $s)<option value="{{ $s }}" {{ $loan->status === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>@endforeach</select></div>
            <div class="field"><label>Due date</label><input type="date" name="due_date" value="{{ $loan->due_date?->format('Y-m-d') }}"></div>
            <div class="field"><label>Purpose</label><input name="purpose" value="{{ $loan->purpose }}"></div>
            <div class="field"><label>Notes</label><textarea name="notes" rows="3">{{ $loan->notes }}</textarea></div>
            <button class="btn btn-primary" type="submit">Save changes</button>
        </form>
    </div>
@endsection
