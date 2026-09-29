@extends('layouts.app')

@section('title', 'New Loan')

@section('content')
    <div class="view-head"><div><h2>New Loan</h2><p class="sub">Individual page — no popup.</p></div><div class="view-actions"><a href="{{ route('loans.index') }}" class="btn btn-ghost">Back to loans</a></div></div>
    <div class="settings-panel"><h3>Loan details</h3>
        <form method="POST" action="{{ route('loans.store') }}">@csrf
            <div class="form-row"><div class="field"><label>Member *</label><select name="member_id" required><option value="">Select member…</option>@foreach($members as $m)<option value="{{ $m->id }}">{{ $m->name }} · {{ $m->phone }}</option>@endforeach</select></div>
            <div class="field"><label>Loan product</label><select name="loan_product_id"><option value="">— None —</option>@foreach($products as $p)<option value="{{ $p->id }}">{{ $p->name }} · {{ $p->interest_rate }}%</option>@endforeach</select></div></div>
            <div class="form-row"><div class="field"><label>Principal (TZS) *</label><input type="number" name="principal" min="1000" step="100" required></div><div class="field"><label>Interest % *</label><input type="number" name="interest_rate" min="0" max="100" step="0.5" value="10" required></div></div>
            <div class="form-row"><div class="field"><label>Disbursed</label><input type="date" name="disbursed_at" value="{{ now()->toDateString() }}"></div><div class="field"><label>Due date</label><input type="date" name="due_date" value="{{ now()->addMonths(6)->toDateString() }}"></div></div>
            <div class="form-row"><div class="field"><label>Status *</label><select name="status"><option value="pending">Pending</option><option value="active" selected>Active</option><option value="overdue">Overdue</option></select></div><div class="field"><label>Purpose</label><input name="purpose" placeholder="e.g. Business stock"></div></div>
            <div class="field"><label>Notes</label><textarea name="notes" rows="3"></textarea></div>
            <button class="btn btn-primary" type="submit">Save loan</button>
        </form>
    </div>
@endsection
