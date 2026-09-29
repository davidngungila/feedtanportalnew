@extends('layouts.app')
@section('title', 'New Loan Application')
@section('content')
    <div class="view-head"><div><h2>New Loan Application</h2><p class="sub">Individual page — no popup.</p></div><div class="view-actions"><a href="{{ route('loan-applications.index') }}" class="btn btn-ghost">Back</a></div></div>
    <div class="settings-panel"><h3>Application details</h3>
        <form method="POST" action="{{ route('loan-applications.store') }}">@csrf
            <div class="form-row"><div class="field"><label>Member *</label><select name="member_id" required><option value="">Select…</option>@foreach($members as $m)<option value="{{ $m->id }}">{{ $m->name }} · {{ $m->phone }}</option>@endforeach</select></div>
            <div class="field"><label>Product</label><select name="loan_product_id"><option value="">— None —</option>@foreach($products as $p)<option value="{{ $p->id }}">{{ $p->name }} · {{ $p->interest_rate }}%</option>@endforeach</select></div></div>
            <div class="form-row"><div class="field"><label>Amount *</label><input type="number" name="amount" min="1000" step="100" required></div><div class="field"><label>Purpose</label><input name="purpose" placeholder="e.g. Stock"></div></div>
            <div class="field"><label>Notes</label><textarea name="notes" rows="3"></textarea></div>
            <button class="btn btn-primary" type="submit">Submit application</button>
        </form>
    </div>
@endsection
