@extends('layouts.app')
@section('title', 'New Finance Entry')
@section('content')
    <div class="view-head"><div><h2>New Finance Entry</h2><p class="sub">Individual page — posts a balanced journal automatically.</p></div><div class="view-actions"><a href="{{ route('finance.transactions') }}" class="btn btn-ghost">Back</a></div></div>
    <div class="settings-panel"><h3>Entry details</h3>
        <form method="POST" action="{{ route('finance.transactions.store') }}">@csrf
            <div class="form-row"><div class="field"><label>Type *</label><select name="type"><option value="income" {{ $presetType === 'income' ? 'selected' : '' }}>Income</option><option value="expense" {{ $presetType === 'expense' ? 'selected' : '' }}>Expense</option></select></div>
            <div class="field"><label>Category *</label><select name="category">@foreach(['operating','fee','charge','interest','commission','adjustment','other'] as $c)<option value="{{ $c }}" {{ $preset === $c ? 'selected' : '' }}>{{ ucfirst($c) }}</option>@endforeach</select></div></div>
            <div class="form-row"><div class="field"><label>Cash / Bank account *</label><select name="finance_account_id" required><option value="">Select…</option>@foreach($accounts as $a)<option value="{{ $a->id }}">{{ $a->code }} · {{ $a->name }}</option>@endforeach</select></div>
            <div class="field"><label>Member (optional)</label><select name="member_id"><option value="">— None —</option>@foreach($members as $m)<option value="{{ $m->id }}">{{ $m->name }}</option>@endforeach</select></div></div>
            <div class="form-row"><div class="field"><label>Amount (TZS) *</label><input type="number" name="amount" min="100" step="100" required></div><div class="field"><label>Date *</label><input type="date" name="transacted_at" value="{{ now()->toDateString() }}" required></div></div>
            <div class="field"><label>Description</label><input name="description" placeholder="e.g. Monthly service charges"></div>
            <button class="btn btn-primary" type="submit">Save &amp; post</button>
        </form>
    </div>
@endsection
