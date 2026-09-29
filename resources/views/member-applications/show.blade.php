@extends('layouts.app')
@section('title', 'Application · '.$application->name)
@section('content')
    <div class="view-head">
        <div><h2>{{ $application->name }}</h2><p class="sub">Application #{{ $application->id }} · sent {{ $application->created_at->format('d M Y') }}@if($application->user_id) · online account @endif</p></div>
        <div class="view-actions">
            <a href="{{ route('member-applications.index') }}" class="btn btn-ghost">Back</a>
            @if($application->status === 'pending')
            <form method="POST" action="{{ route('member-applications.update', $application) }}" style="display:inline;">@csrf @method('PUT')<input type="hidden" name="status" value="rejected"><button class="btn btn-ghost" type="submit">Reject</button></form>
            <form method="POST" action="{{ route('member-applications.approve', $application) }}" onsubmit="return confirm('Approve and create member?')" style="display:inline;">@csrf<button class="btn btn-primary" type="submit">Approve → member</button></form>
            @endif
        </div>
    </div>

    <div class="strip-4">
        <div class="balance-box"><div class="bb-label">Status</div><div class="bb-amount" style="font-size:18px;"><span class="tag {{ status_badge($application->status) }}">{{ ucfirst($application->status) }}</span></div></div>
        <div class="balance-box"><div class="bb-label">Type applied</div><div class="bb-amount" style="font-size:18px;">{{ $application->memberType->name ?? '—' }}</div></div>
        <div class="balance-box"><div class="bb-label">Beneficiaries</div><div class="bb-amount" style="font-size:18px;">{{ count($application->beneficiaries ?? []) }}</div></div>
        <div class="balance-box"><div class="bb-label">Files attached</div><div class="bb-amount" style="font-size:18px;">{{ count($application->attachments['slips'] ?? []) + collect($application->attachments ?? [])->except('slips')->filter()->count() }}</div></div>
    </div>

    <div class="panel-grid">
        <div class="panel"><div class="panel-head"><h3>Identity &amp; contact</h3></div>
            <div class="panel-body"><div class="detail-grid">
                <div class="detail-item"><div class="dk">First name</div><div class="dv">{{ $application->first_name ?? $application->name }}</div></div>
                <div class="detail-item"><div class="dk">Second name</div><div class="dv">{{ $application->middle_name ?? '—' }}</div></div>
                <div class="detail-item"><div class="dk">Surname</div><div class="dv">{{ $application->surname ?? '—' }}</div></div>
                <div class="detail-item"><div class="dk">Sex</div><div class="dv">{{ $application->sex ? ucfirst($application->sex) : '—' }}</div></div>
                <div class="detail-item"><div class="dk">Date of birth</div><div class="dv">{{ $application->dob?->format('d M Y') ?? '—' }}</div></div>
                <div class="detail-item"><div class="dk">Marital status</div><div class="dv">{{ $application->marital_status ? ucfirst($application->marital_status) : '—' }}</div></div>
                <div class="detail-item"><div class="dk">Phone</div><div class="dv">{{ $application->phone }}</div></div>
                <div class="detail-item"><div class="dk">NIDA number</div><div class="dv">{{ $application->national_id ?? '—' }}</div></div>
                <div class="detail-item"><div class="dk">Email</div><div class="dv">{{ $application->email ?? '—' }}</div></div>
                <div class="detail-item"><div class="dk">Address</div><div class="dv">{{ $application->address ?? '—' }}</div></div>
                <div class="detail-item"><div class="dk">Job</div><div class="dv">{{ $application->job ?? '—' }}</div></div>
                <div class="detail-item"><div class="dk">Employer</div><div class="dv">{{ $application->employer ?? '—' }}</div></div>
                <div class="detail-item"><div class="dk">Statements via</div><div class="dv">{{ $application->statement_channel ? ucfirst($application->statement_channel) : '—' }}</div></div>
                <div class="detail-item"><div class="dk">Referrer</div><div class="dv">{{ $application->referrer ?? '—' }}</div></div>
            </div>
            @if($application->biography)<div class="receipt"><div class="receipt-row"><span>Bibliography</span><b style="font-weight:600;">{{ $application->biography }}</b></div></div>@endif
            </div>
        </div>
        <div class="panel"><div class="panel-head"><h3>Bank &amp; payments</h3></div>
            <div class="panel-body"><div class="detail-grid">
                <div class="detail-item"><div class="dk">Bank</div><div class="dv">{{ $application->bank_name ?? '—' }}</div></div>
                <div class="detail-item"><div class="dk">Account</div><div class="dv">{{ $application->bank_account ?? '—' }}</div></div>
                <div class="detail-item"><div class="dk">Ordinary track</div><div class="dv">{{ $application->consider_ordinary ? 'Wants consideration' : '—' }}</div></div>
            </div>
            @if($application->notes)<div class="receipt"><div class="receipt-row"><span>Notes</span><b style="font-weight:600;">{{ $application->notes }}</b></div></div>@endif
            </div>
        </div>
    </div>

    <div class="panel-grid">
        <div class="panel"><div class="panel-head"><h3>Beneficiaries ({{ count($application->beneficiaries ?? []) }})</h3></div>
            <div class="table-scroll"><table style="min-width:520px;">
                <thead><tr><th>Name</th><th>Relationship</th><th>%</th><th>Bank</th><th>Contact</th></tr></thead>
                <tbody>
                    @forelse($application->beneficiaries ?? [] as $b)
                    <tr><td class="cell-title">{{ $b['name'] ?? '—' }}</td><td>{{ $b['relationship'] ?? '—' }}</td><td>{{ $b['allocation'] ?? '—' }}</td><td>{{ $b['bank'] ?? '—' }}</td><td>{{ $b['contact'] ?? '—' }}</td></tr>
                    @empty<tr><td colspan="5" class="empty-state">No beneficiaries named.</td></tr>@endforelse
                </tbody>
            </table></div>
        </div>
        <div class="panel"><div class="panel-head"><h3>Goal, group &amp; files</h3></div>
            <div class="panel-body"><div class="detail-grid">
                <div class="detail-item"><div class="dk">Savings goal</div><div class="dv">{{ $application->savings_goal ?? '—' }}</div></div>
                <div class="detail-item"><div class="dk">Target</div><div class="dv">{{ $application->goal_amount ? money($application->goal_amount).' / '.$application->goal_months.' mo from '.($application->goal_start?->format('d M Y') ?? '?') : '—' }}</div></div>
                <div class="detail-item"><div class="dk">Group</div><div class="dv">{{ $application->memberGroup->name ?? ($application->group_name ?: '—') }}</div></div>
                <div class="detail-item"><div class="dk">Govt registered</div><div class="dv">{{ $application->group_name ? ($application->group_registered ? 'Yes' : 'No') : '—' }}</div></div>
                <div class="detail-item"><div class="dk">Leaders</div><div class="dv">{{ $application->group_leaders ?? '—' }}</div></div>
                <div class="detail-item"><div class="dk">Group bank</div><div class="dv">{{ $application->group_bank_account ?? '—' }}</div></div>
                <div class="detail-item"><div class="dk">Group contacts</div><div class="dv">{{ $application->group_contacts ?? '—' }}</div></div>
            </div>
            <div class="receipt">
                @php $files = $application->attachments ?? []; @endphp
                @foreach(['passport' => 'Passport picture', 'application_letter' => 'Application letter'] as $k => $label)
                    @if(! empty($files[$k]))
                    <div class="receipt-row"><span>{{ $label }}</span><b><a href="{{ Storage::disk('public')->url($files[$k]) }}" target="_blank" style="color:var(--terracotta-600);">Open →</a></b></div>
                    @endif
                @endforeach
                @foreach($files['slips'] ?? [] as $i => $slip)
                <div class="receipt-row"><span>Payment slip {{ $i + 1 }}</span><b><a href="{{ Storage::disk('public')->url($slip) }}" target="_blank" style="color:var(--terracotta-600);">Open →</a></b></div>
                @endforeach
                @if(empty(array_filter($files)))
                <div class="receipt-row"><span>Files</span><b>None attached</b></div>
                @endif
            </div>
            </div>
        </div>
    </div>
@endsection
