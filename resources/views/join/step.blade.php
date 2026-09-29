@extends('layouts.app')
@section('title', 'Membership · Step '.$step)
@section('content')
    <div class="view-head">
        <div><h2>Become a member — step {{ $step }} of 5</h2><p class="sub">{{ $steps[$step] }} · your progress saves automatically after each step.</p></div>
        <div class="view-actions"><span class="tag tag-gold">Step {{ $step }} of 5</span><a href="{{ route('join.status') }}" class="btn btn-ghost">Application status</a></div>
    </div>

    <div class="settings-panel">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:18px;flex-wrap:wrap;gap:10px;">
            <h3 style="margin:0;">
                @if($step === 1)
                Personal details
                @endif
                @if($step === 2)
                Contact &amp; work
                @endif
                @if($step === 3)
                Bank &amp; payments
                @endif
                @if($step === 4)
                Membership &amp; people
                @endif
                @if($step === 5)
                Review &amp; submit
                @endif
            </h3>
            <span class="cell-sub">Application ref #{{ $app->id }} · started {{ $app->created_at->format('d M Y') }}</span>
        </div>

        @if($step === 1)
        <p style="font-size:14px;color:var(--ink-soft);margin-bottom:20px;line-height:1.7;max-width:1000px;">Tell us who you are. Your name must match your national ID — the office verifies this before approval. Your phone receives SMS notifications about approvals, loans, repayments and payouts.</p>
        <form method="POST" action="{{ route('join.save', eid(1)) }}" enctype="multipart/form-data">@csrf
            <div class="form-row-3">
                <div class="field"><label>First name *</label><input name="first_name" value="{{ old('first_name', $app->first_name) }}" placeholder="As shown on your national ID" required></div>
                <div class="field"><label>Second name</label><input name="middle_name" value="{{ old('middle_name', $app->middle_name) }}" placeholder="Middle name (if any)"></div>
                <div class="field"><label>Surname *</label><input name="surname" value="{{ old('surname', $app->surname) }}" placeholder="Family name" required></div>
            </div>
            <div class="form-row-3">
                <div class="field"><label>Sex</label><select name="sex"><option value="">— Select —</option><option value="male" {{ old('sex', $app->sex) === 'male' ? 'selected' : '' }}>Male</option><option value="female" {{ old('sex', $app->sex) === 'female' ? 'selected' : '' }}>Female</option></select></div>
                <div class="field"><label>Date of birth</label><input type="date" id="dobInput" name="dob" value="{{ old('dob', $app->dob?->format('Y-m-d')) }}" max="{{ now()->toDateString() }}" onchange="showAge()"><div class="cell-sub" id="ageHint" style="margin-top:6px;">@if($app->age !== null){{ $app->age }} years old @endif</div></div>
                <div class="field"><label>Marital status</label><select name="marital_status"><option value="">— Select —</option>@foreach(['single' => 'Single', 'married' => 'Married', 'divorced' => 'Divorced', 'widowed' => 'Widowed'] as $v => $l)<option value="{{ $v }}" {{ old('marital_status', $app->marital_status) === $v ? 'selected' : '' }}>{{ $l }}</option>@endforeach</select></div>
            </div>
            <div class="form-row">
                <div class="field"><label>Mobile number *</label><input name="phone" value="{{ old('phone', $app->phone) }}" placeholder="07…" required></div>
                <div class="field"><label>NIDA ID number</label><input name="national_id" value="{{ old('national_id', $app->national_id) }}" placeholder="e.g. 19900101-00000-00001-01"></div>
            </div>
            <div class="field"><label>Passport-size picture (jpg/png — resized to 500px automatically)</label><input type="file" name="passport_picture" accept=".jpg,.jpeg,.png">@if(! empty($app->attachments['passport']))<div class="cell-sub" style="margin-top:6px;">Uploaded ✓ <a href="{{ Storage::disk('public')->url($app->attachments['passport']) }}" target="_blank" style="color:var(--terracotta-600);font-weight:700;">view</a> · re-upload to replace</div>@endif</div>
            <button class="btn btn-primary" type="submit">Save &amp; continue →</button>
        </form>
        <script>
        function showAge(){
            const el = document.getElementById('dobInput');
            const hint = document.getElementById('ageHint');
            if (! el || ! hint) return;
            if (! el.value) { hint.textContent = ''; return; }
            const dob = new Date(el.value + 'T00:00:00');
            const now = new Date();
            let age = now.getFullYear() - dob.getFullYear();
            const hadBirthday = (now.getMonth() > dob.getMonth()) || (now.getMonth() === dob.getMonth() && now.getDate() >= dob.getDate());
            if (! hadBirthday) age--;
            hint.textContent = age >= 0 ? age + (age === 1 ? ' year old' : ' years old') : '';
        }
        document.addEventListener('DOMContentLoaded', showAge);
        </script>
        <div class="card-grid" style="margin-top:22px;">
            <div class="mini-card"><div class="mc-top"><span class="mc-name">Why your name?</span></div><div class="mc-label">It appears on your member number, statements and payout verifications. It must match your ID or approval is delayed.</div></div>
            <div class="mini-card"><div class="mc-top"><span class="mc-name">Why your phone?</span></div><div class="mc-label">One-time codes, payout confirmations and office notices arrive by SMS on this number.</div></div>
            <div class="mini-card"><div class="mc-top"><span class="mc-name">Why your ID?</span></div><div class="mc-label">Proof of identity for the office review. It is kept private and never shown to other members.</div></div>
        </div>
        @endif

        @if($step === 2)
        <p style="font-size:14px;color:var(--ink-soft);margin-bottom:20px;line-height:1.7;max-width:1000px;">Where do you live and work? Approval decisions, verification codes and monthly statements go to these contacts. Attach a current passport-size picture for the group documents.</p>
        <form method="POST" action="{{ route('join.save', eid(2)) }}" enctype="multipart/form-data">@csrf
            <div class="form-row">
                <div class="field"><label>Email address</label><input type="email" name="email" value="{{ old('email', $app->email) }}" placeholder="you@example.com"></div>
                <div class="field"><label>Current address</label><input name="address" value="{{ old('address', $app->address) }}" placeholder="Street, ward, district"></div>
            </div>
            <div class="form-row">
                <div class="field"><label>Job / occupation</label><input name="job" value="{{ old('job', $app->job) }}" placeholder="e.g. Teacher, trader"></div>
                <div class="field"><label>Employer / self employment</label><input name="employer" value="{{ old('employer', $app->employer) }}" placeholder="e.g. Self, ACME Ltd"></div>
            </div>
            <div style="display:flex;gap:10px;"><a href="{{ route('join.step', eid(1)) }}" class="btn btn-ghost">← Back</a><button class="btn btn-primary" type="submit">Save &amp; continue →</button></div>
        </form>
        <div class="card-grid" style="margin-top:22px;">
            <div class="mini-card"><div class="mc-top"><span class="mc-name">Already given</span></div><div class="mc-label">{{ $app->name ?: '—' }} · {{ $app->phone ?: '—' }} · ID {{ $app->national_id ?: '—' }}</div></div>
            <div class="mini-card"><div class="mc-top"><span class="mc-name">Passport picture</span></div><div class="mc-label">Used in official group documents. Clear front-facing photo, plain background.</div></div>
            <div class="mini-card"><div class="mc-top"><span class="mc-name">Statements</span></div><div class="mc-label">Choose how your monthly statement reaches you most conveniently.</div></div>
        </div>
        @endif

        @if($step === 3)
        <p style="font-size:14px;color:var(--ink-soft);margin-bottom:20px;line-height:1.7;max-width:1000px;">Where do your payments come from? Give the bank account you pay from so the office can match your contributions.</p>
        <form method="POST" action="{{ route('join.save', eid(3)) }}" enctype="multipart/form-data">@csrf
            <div class="form-row">
                <div class="field"><label>Bank and branch name</label><input name="bank_name" value="{{ old('bank_name', $app->bank_name) }}" placeholder="e.g. CRDB Mwanza"></div>
                <div class="field"><label>Bank account number</label><input name="bank_account" value="{{ old('bank_account', $app->bank_account) }}" placeholder="Account number"></div>
            </div>
            <div style="display:flex;gap:10px;"><a href="{{ route('join.step', eid(2)) }}" class="btn btn-ghost">← Back</a><button class="btn btn-primary" type="submit">Save &amp; continue →</button></div>
        </form>
        @endif

        @if($step === 4)
        <p style="font-size:14px;color:var(--ink-soft);margin-bottom:20px;line-height:1.7;max-width:1000px;">Who is joining, and who benefits. Choose your membership type, tell us who introduced you, and name your beneficiaries with their % allocation (must add up to 100%).</p>
        <form method="POST" action="{{ route('join.save', eid(4)) }}" enctype="multipart/form-data">@csrf
            <div class="form-row">
                <div class="field"><label>Type of membership applied</label><select name="member_type_id"><option value="">— Select —</option>@foreach($types as $t)<option value="{{ $t->id }}" {{ (string)old('member_type_id', $app->member_type_id) === (string)$t->id ? 'selected' : '' }}>{{ $t->name }}</option>@endforeach</select></div>
                <div class="field"><label>Who introduced / guarantees you?</label><input name="referrer" value="{{ old('referrer', $app->referrer) }}" placeholder="Name of person, or how you heard of FeedTan"></div>
            </div>

            <h3 style="margin:22px 0 10px;">Beneficiaries <span class="cell-sub">(in case of unfortunate event of death)</span></h3>
            <div id="benList">
                @php $bens = old('beneficiaries', $app->beneficiaries ?? [['name' => '', 'relationship' => '', 'allocation' => '', 'bank' => '', 'contact' => '']]); @endphp
                @foreach($bens as $i => $b)
                <div class="ben-row ben-grid">
                    <div class="field" style="margin:0;"><label>Name *</label><input name="beneficiaries[{{ $i }}][name]" value="{{ $b['name'] ?? '' }}"></div>
                    <div class="field" style="margin:0;"><label>Relationship</label><input name="beneficiaries[{{ $i }}][relationship]" value="{{ $b['relationship'] ?? '' }}" placeholder="e.g. Spouse"></div>
                    <div class="field" style="margin:0;"><label>% allocation</label><input type="number" name="beneficiaries[{{ $i }}][allocation]" value="{{ $b['allocation'] ?? '' }}" min="0" max="100" step="0.01"></div>
                    <div class="field" style="margin:0;"><label>Bank details</label><input name="beneficiaries[{{ $i }}][bank]" value="{{ $b['bank'] ?? '' }}"></div>
                    <div class="field" style="margin:0;"><label>Contact</label><input name="beneficiaries[{{ $i }}][contact]" value="{{ $b['contact'] ?? '' }}" placeholder="Mobile / email"></div>
                    <div><button type="button" class="btn btn-ghost btn-sm" onclick="this.closest('.ben-row').remove()">✕</button></div>
                </div>
                @endforeach
            </div>
            <button type="button" class="btn btn-ghost btn-sm" onclick="addBenRow()">+ Add beneficiary</button>

            <h3 style="margin:22px 0 10px;">Savings goal (optional)</h3>
            <div class="field"><label>Specific goal</label><input name="savings_goal" value="{{ old('savings_goal', $app->savings_goal) }}" placeholder="What is the money for?"></div>
            <div class="form-row-3">
                <div class="field"><label>Amount (TZS)</label><input type="number" name="goal_amount" value="{{ old('goal_amount', $app->goal_amount) }}" min="0" step="1000" placeholder="Figures only"></div>
                <div class="field"><label>Months</label><input type="number" name="goal_months" value="{{ old('goal_months', $app->goal_months) }}" min="1" max="600"></div>
                <div class="field"><label>Start saving</label><input type="date" name="goal_start" value="{{ old('goal_start', $app->goal_start?->format('Y-m-d')) }}"></div>
            </div>

            <div style="display:flex;gap:10px;"><a href="{{ route('join.step', eid(3)) }}" class="btn btn-ghost">← Back</a><button class="btn btn-primary" type="submit">Save &amp; review →</button></div>
        </form>
        <script>
        let benIndex = {{ count($bens) }};
        function addBenRow(){
            const host = document.getElementById('benList');
            const div = document.createElement('div');
            div.className = 'ben-row ben-grid';
            div.innerHTML =
                '<div class="field" style="margin:0;"><label>Name *</label><input name="beneficiaries[' + benIndex + '][name]"></div>' +
                '<div class="field" style="margin:0;"><label>Relationship</label><input name="beneficiaries[' + benIndex + '][relationship]"></div>' +
                '<div class="field" style="margin:0;"><label>% allocation</label><input type="number" name="beneficiaries[' + benIndex + '][allocation]" min="0" max="100" step="0.01"></div>' +
                '<div class="field" style="margin:0;"><label>Bank details</label><input name="beneficiaries[' + benIndex + '][bank]"></div>' +
                '<div class="field" style="margin:0;"><label>Contact</label><input name="beneficiaries[' + benIndex + '][contact]"></div>' +
                '<div><button type="button" class="btn btn-ghost btn-sm" onclick="this.closest(\'.ben-row\').remove()">✕</button></div>';
            host.appendChild(div);
            benIndex++;
        }
        </script>
        @if($types->isNotEmpty() || $groups->isNotEmpty())
        <h3 style="margin:24px 0 12px;">Available options in full</h3>
        <div class="card-grid">
            @foreach($types as $t)
            <div class="mini-card"><div class="mc-top"><span class="mc-name">{{ $t->name }}</span><span class="tag tag-gold">Type</span></div><div class="mc-label">{{ $t->description ?? 'Standard member type with full access after approval.' }}</div></div>
            @endforeach
            @foreach($groups as $g)
            <div class="mini-card"><div class="mc-top"><span class="mc-name">{{ $g->name }}</span><span class="tag tag-green">Group</span></div><div class="mc-label">{{ $g->description ?? 'Member group that saves or borrows together.' }}</div></div>
            @endforeach
        </div>
        @endif
        @endif

        @if($step === 5)
        <p style="font-size:14px;color:var(--ink-soft);margin-bottom:20px;line-height:1.7;max-width:1100px;">Check everything once more. You can jump back to any step to fix it — nothing is sent until you press submit.</p>
        <h3 style="margin:0 0 12px;">Identity &amp; contact <a href="{{ route('join.step', eid(1)) }}" style="color:var(--terracotta-600);font-size:13px;">Edit</a></h3>
        <div class="detail-grid-4" style="margin-bottom:20px;">
            <div class="detail-item"><div class="dk">First name</div><div class="dv">{{ $app->first_name }}</div></div>
            <div class="detail-item"><div class="dk">Second name</div><div class="dv">{{ $app->middle_name ?? '—' }}</div></div>
            <div class="detail-item"><div class="dk">Surname</div><div class="dv">{{ $app->surname }}</div></div>
            <div class="detail-item"><div class="dk">Sex</div><div class="dv">{{ $app->sex ? ucfirst($app->sex) : '—' }}</div></div>
            <div class="detail-item"><div class="dk">Date of birth</div><div class="dv">{{ $app->dob?->format('d M Y') ?? '—' }}@if($app->age !== null) · {{ $app->age }} yrs @endif</div></div>
            <div class="detail-item"><div class="dk">Marital status</div><div class="dv">{{ $app->marital_status ? ucfirst($app->marital_status) : '—' }}</div></div>
            <div class="detail-item"><div class="dk">Phone</div><div class="dv">{{ $app->phone }}</div></div>
            <div class="detail-item"><div class="dk">NIDA number</div><div class="dv">{{ $app->national_id ?? '—' }}</div></div>
            <div class="detail-item"><div class="dk">Email</div><div class="dv">{{ $app->email ?? '—' }}</div></div>
            <div class="detail-item"><div class="dk">Address</div><div class="dv">{{ $app->address ?? '—' }}</div></div>
            <div class="detail-item"><div class="dk">Job</div><div class="dv">{{ $app->job ?? '—' }}</div></div>
            <div class="detail-item"><div class="dk">Employer</div><div class="dv">{{ $app->employer ?? '—' }}</div></div>
            <div class="detail-item"><div class="dk">Statements via</div><div class="dv">{{ $app->statement_channel ? ucfirst($app->statement_channel) : '—' }}</div></div>
            <div class="detail-item"><div class="dk">Referrer</div><div class="dv">{{ $app->referrer ?? '—' }}</div></div>
        </div>
        <h3 style="margin:0 0 12px;">Bank &amp; payments <a href="{{ route('join.step', eid(3)) }}" style="color:var(--terracotta-600);font-size:13px;">Edit</a></h3>
        <div class="detail-grid-4" style="margin-bottom:20px;">
            <div class="detail-item"><div class="dk">Bank</div><div class="dv">{{ $app->bank_name ?? '—' }}</div></div>
            <div class="detail-item"><div class="dk">Account</div><div class="dv">{{ $app->bank_account ?? '—' }}</div></div>
        </div>
        <h3 style="margin:0 0 12px;">Membership &amp; people <a href="{{ route('join.step', eid(4)) }}" style="color:var(--terracotta-600);font-size:13px;">Edit</a></h3>
        <div class="detail-grid-4" style="margin-bottom:20px;">
            <div class="detail-item"><div class="dk">Type applied</div><div class="dv">{{ $app->memberType->name ?? '—' }}</div></div>
            <div class="detail-item"><div class="dk">Referrer</div><div class="dv">{{ $app->referrer ?? '—' }}</div></div>
        </div>
        @if(! empty($app->beneficiaries))
        <h3 style="margin:0 0 12px;">Beneficiaries</h3>
        <div class="table-card"><div class="table-scroll"><table>
            <thead><tr><th>Name</th><th>Relationship</th><th>%</th><th>Bank</th><th>Contact</th></tr></thead>
            <tbody>
                @foreach($app->beneficiaries as $b)
                <tr><td class="cell-title">{{ $b['name'] ?? '—' }}</td><td>{{ $b['relationship'] ?? '—' }}</td><td>{{ $b['allocation'] ?? '—' }}</td><td>{{ $b['bank'] ?? '—' }}</td><td>{{ $b['contact'] ?? '—' }}</td></tr>
                @endforeach
            </tbody>
        </table></div></div>
        @endif
        @if($app->savings_goal || $app->goal_amount)
        <h3 style="margin:20px 0 12px;">Savings goal</h3>
        <div class="detail-grid-4" style="margin-bottom:20px;">
            <div class="detail-item"><div class="dk">Goal</div><div class="dv">{{ $app->savings_goal ?? '—' }}</div></div>
            <div class="detail-item"><div class="dk">Amount</div><div class="dv">{{ $app->goal_amount ? money($app->goal_amount) : '—' }}</div></div>
            <div class="detail-item"><div class="dk">Duration</div><div class="dv">{{ $app->goal_months ? $app->goal_months.' months' : '—' }}</div></div>
            <div class="detail-item"><div class="dk">Start</div><div class="dv">{{ $app->goal_start?->format('d M Y') ?? '—' }}</div></div>
        </div>
        @endif
        <form method="POST" action="{{ route('join.submit') }}" style="display:flex;gap:10px;margin-top:18px;">@csrf
            <a href="{{ route('join.step', eid(4)) }}" class="btn btn-ghost">← Back</a>
            <button class="btn btn-primary" type="submit">Submit application</button>
        </form>
        <div class="card-grid" style="margin-top:22px;">
            <div class="mini-card"><div class="mc-top"><span class="mc-name">After submit</span></div><div class="mc-label">Your application moves to office review. Track it any time under Application status.</div></div>
            <div class="mini-card"><div class="mc-top"><span class="mc-name">On approval</span></div><div class="mc-label">You get a member number and the services menu opens: loans, savings, investments, SWF.</div></div>
            <div class="mini-card"><div class="mc-top"><span class="mc-name">If rejected</span></div><div class="mc-label">Your answers are kept — update them and send again from the status page.</div></div>
        </div>
        @endif
    </div>
@endsection
