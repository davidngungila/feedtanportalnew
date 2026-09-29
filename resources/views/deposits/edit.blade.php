@extends('layouts.app')
@section('title', 'Edit Deposit Entry')
@section('content')
    <div class="view-head"><div><h2>Edit Entry</h2><p class="sub">{{ $deposit->receipt_no }} · {{ $deposit->member->name ?? '' }}</p></div><div class="view-actions"><a href="{{ route('deposits.index') }}" class="btn btn-ghost">Back</a></div></div>
    <div class="settings-panel"><h3>Entry details</h3>
        <form method="POST" action="{{ route('deposits.update', $deposit) }}">@csrf @method('PUT')
            <div class="field"><label>Deposit product</label><select name="deposit_product_id"><option value="">— None —</option>@foreach($products as $p)<option value="{{ $p->id }}" {{ $deposit->deposit_product_id == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>@endforeach</select></div>
            <div class="form-row"><div class="field"><label>Type *</label><select name="type"><option value="deposit" {{ $deposit->type === 'deposit' ? 'selected' : '' }}>Deposit</option><option value="withdrawal" {{ $deposit->type === 'withdrawal' ? 'selected' : '' }}>Withdrawal</option></select></div><div class="field"><label>Amount *</label><input type="number" name="amount" value="{{ $deposit->amount }}" min="100" step="100" required></div></div>
            <div class="form-row"><div class="field"><label>Method *</label><select name="method"><option value="cash" {{ $deposit->method === 'cash' ? 'selected' : '' }}>Cash</option><option value="mobile" {{ $deposit->method === 'mobile' ? 'selected' : '' }}>Mobile</option><option value="bank" {{ $deposit->method === 'bank' ? 'selected' : '' }}>Bank</option></select></div><div class="field"><label>Date *</label><input type="date" name="transacted_at" value="{{ $deposit->transacted_at?->format('Y-m-d') }}" required></div></div>
            <div class="field"><label>Notes</label><textarea name="notes" rows="3">{{ $deposit->notes }}</textarea></div>
            <button class="btn btn-primary" type="submit">Save changes</button>
        </form>
    </div>
@endsection
