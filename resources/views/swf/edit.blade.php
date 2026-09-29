@extends('layouts.app')
@section('title', 'Edit SWF Entry')
@section('content')
    <div class="view-head"><div><h2>Edit SWF Entry</h2><p class="sub">{{ $entry->receipt_no }} · {{ $entry->member->name ?? '' }}</p></div><div class="view-actions"><a href="{{ route('swf.index') }}" class="btn btn-ghost">Back</a></div></div>
    <div class="settings-panel"><h3>Entry details</h3>
        <form method="POST" action="{{ route('swf.update', $entry) }}">@csrf @method('PUT')
            <div class="field"><label>Reason</label><input name="reason" value="{{ $entry->reason }}"></div>
            <div class="field"><label>Notes</label><textarea name="notes" rows="3">{{ $entry->notes }}</textarea></div>
            <button class="btn btn-primary" type="submit">Save changes</button>
        </form>
    </div>
@endsection
