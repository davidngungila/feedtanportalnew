@extends('layouts.app')
@section('title', 'New Document')
@section('content')
    <div class="view-head"><div><h2>New Member Document</h2><p class="sub">Individual page — no popup.</p></div><div class="view-actions"><a href="{{ route('member-documents.index') }}" class="btn btn-ghost">Back</a></div></div>
    <div class="settings-panel"><h3>Document details</h3>
        <form method="POST" action="{{ route('member-documents.store') }}">@csrf
            <div class="field"><label>Member *</label><select name="member_id" required><option value="">Select…</option>@foreach($members as $m)<option value="{{ $m->id }}" {{ (string)$selectedMember === (string)$m->id ? 'selected' : '' }}>{{ $m->name }} · {{ $m->phone }}</option>@endforeach</select></div>
            <div class="form-row"><div class="field"><label>Title *</label><input name="title" required placeholder="e.g. National ID copy"></div><div class="field"><label>Type *</label><select name="doc_type"><option value="id">ID</option><option value="contract">Contract</option><option value="photo">Photo</option><option value="proof">Proof</option><option value="other">Other</option></select></div></div>
            <div class="field"><label>File reference</label><input name="file_path" placeholder="e.g. docs/id-123.pdf (optional)"></div>
            <div class="field"><label>Notes</label><textarea name="notes" rows="3"></textarea></div>
            <button class="btn btn-primary" type="submit">Save document</button>
        </form>
    </div>
@endsection
