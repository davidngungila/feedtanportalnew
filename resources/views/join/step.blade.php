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
        <p style="font-size:14px;color:var(--ink-soft);margin-bottom:20px;line-height:1.7;max-width:1000px;">Tell us who you are. Your name must match your national ID — the office verifies this before approval. Your phone receives SMS notifications about approvals, loans, repayments and payouts. Attach a picture of your NIDA ID if you have it.</p>
        <form method="POST" action="{{ route('join.save', eid(1)) }}" enctype="multipart/form-data">@csrf
            <div class="form-row" style="grid-template-columns:2fr 1fr 1fr 1fr;">
                <div class="field"><label>Full name *</label><input name="name" value="{{ old('name', $app->name) }}" placeholder="As shown on your national ID" required></div>
                <div class="field"><label>Sex</label><select name="sex"><option value="">— Select —</option><option value="male" {{ old('sex', $app->sex) === 'male' ? 'selected' : '' }}>Male</option><option value="female" {{ old('sex', $app->sex) === 'female' ? 'selected' : '' }}>Female</option></select></div>
                <div class="field"><label>Date of birth</label><input type="date" name="dob" value="{{ old('dob', $app->dob?->format('Y-m-d')) }}" max="{{ now()->toDateString() }}"></div>
                <div class="field"><label>Marital status</label><select name="marital_status"><option value="">— Select —</option>@foreach(['single' => 'Single', 'married' => 'Married', 'divorced' => 'Divorced', 'widowed' => 'Widowed'] as $v => $l)<option value="{{ $v }}" {{ old('marital_status', $app->marital_status) === $v ? 'selected' : '' }}>{{ $l }}</option>@endforeach</select></div>
            </div>
            <div class="form-row" style="grid-template-columns:1fr 1fr 2fr;">
                <div class="field"><label>Mobile number *</label><input name="phone" value="{{ old('phone', $app->phone) }}" placeholder="07…" required></div>
                <div class="field"><label>NIDA ID number</label><input name="national_id" value="{{ old('national_id', $app->national_id) }}" placeholder="e.g. 19900101-00000-00001-01"></div>
                <div class="field"><label>Picture of NIDA ID (jpg/png/pdf)</label><input type="file" name="nida_picture" accept=".jpg,.jpeg,.png,.pdf">@if(! empty($app->attachments['nida']))<div class="cell-sub" style="margin-top:6px;">Uploaded ✓ <a href="{{ Storage::disk('public')->url($app->attachments['nida']) }}" target="_blank" style="color:var(--terracotta-600);font-weight:700;">view</a> · re-upload to replace</div>@endif</div>
            </div>
            <button class="btn btn-primary" type="submit">Save &amp; continue →</button>
        </form>
        <div class="card-grid" style="margin-top:22px;">
            <div class="mini-card"><div class="mc-top"><span class="mc-name">Why your name?</span></div><div class="mc-label">It appears on your member number, statements and payout verifications. It must match your ID or approval is delayed.</div></div>
            <div class="mini-card"><div class="mc-top"><span class="mc-name">Why your phone?</span></div><div class="mc-label">One-time codes, payout confirmations and office notices arrive by SMS on this number.</div></div>
            <div class="mini-card"><div class="mc-top"><span class="mc-name">Why your ID?</span></div><div class="mc-label">Proof of identity for the office review. It is kept private and never shown to other members.</div></div>
        </div>
        @endif

        @if($step === 2)
        <p style="font-size:14px;color:var(--ink-soft);margin-bottom:20px;line-height:1.7;max-width:1000px;">Where do you live and work? Approval decisions, verification codes and monthly statements go to these contacts. Attach a current passport-size picture for the group documents.</p>
        <form method="POST" action="{{ route('join.save', eid(2)) }}" enctype="multipart/form-data">@csrf
            <div class="form-row" style="grid-template-columns:1fr 1fr 1fr;">
                <div class="field"><label>Email address</label><input type="email" name="email" value="{{ old('email', $app->email) }}" placeholder="you@example.com"></div>
                <div class="field"><label>Current address</label><input name="address" value="{{ old('address', $app->address) }}" placeholder="Street, ward, district"></div>
                <div class="field"><label>Monthly statement via</label><select name="statement_channel"><option value="">— Select —</option><option value="sms" {{ old('statement_channel', $app->statement_channel) === 'sms' ? 'selected' : '' }}>SMS</option><option value="email" {{ old('statement_channel', $app->statement_channel) === 'email' ? 'selected' : '' }}>Email</option><option value="both" {{ old('statement_channel', $app->statement_channel) === 'both' ? 'selected' : '' }}>Both</option></select></div>
            </div>
            <div class="form-row" style="grid-template-columns:1fr 1fr 2fr;">
                <div class="field"><label>Job / occupation</label><input name="job" value="{{ old('job', $app->job) }}" placeholder="e.g. Teacher, trader"></div>
                <div class="field"><label>Employer / self employment</label><input name="employer" value="{{ old('employer', $app->employer) }}" placeholder="e.g. Self, ACME Ltd"></div>
                <div class="field"><label>Passport-size picture (jpg/png)</label><input type="file" name="passport_picture" accept=".jpg,.jpeg,.png">@if(! empty($app->attachments['passport']))<div class="cell-sub" style="margin-top:6px;">Uploaded ✓ <a href="{{ Storage::disk('public')->url($app->attachments['passport']) }}" target="_blank" style="color:var(--terracotta-600);font-weight:700;">view</a> · re-upload to replace</div>@endif</div>
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
        <p style="font-size:14px;color:var(--ink-soft);margin-bottom:20px;line-height:1.7;max-width:1000px;">Show how you paid. Tick each contribution you made, write the payment reference numbers (Namba za Kumbukumbu), and attach the payment slips as evidence. Phase 2 of TShs 1,800,000 (12 shares) can be lumpsum or installment — attach the bank standing order if you pay in installments. Students attach the annual subscription slip.</p>
        <form method="POST" action="{{ route('join.save', eid(3)) }}" enctype="multipart/form-data">@csrf
            <div class="form-row" style="grid-template-columns:1fr 1fr;">
                <div class="field"><label>Bank and branch name</label><input name="bank_name" value="{{ old('bank_name', $app->bank_name) }}" placeholder="e.g. CRDB Mwanza"></div>
                <div class="field"><label>Bank account number</label><input name="bank_account" value="{{ old('bank_account', $app->bank_account) }}" placeholder="Account number"></div>
            </div>
            <div class="field"><label>Contributions made (tick all that apply)</label>
                <div style="display:flex;gap:8px;flex-wrap:wrap;">
                    <label style="display:flex;gap:6px;align-items:center;background:var(--sand-100);padding:9px 14px;border-radius:20px;font-size:13px;font-weight:600;"><input type="checkbox" name="pay_entrance" value="1" {{ old('pay_entrance', ($app->contributions['entrance_fee'] ?? false)) ? 'checked' : '' }}> Entrance fee</label>
                    <label style="display:flex;gap:6px;align-items:center;background:var(--sand-100);padding:9px 14px;border-radius:20px;font-size:13px;font-weight:600;"><input type="checkbox" name="pay_capital" value="1" {{ old('pay_capital', ($app->contributions['capital_contribution'] ?? false)) ? 'checked' : '' }}> Capital contribution</label>
                    <label style="display:flex;gap:6px;align-items:center;background:var(--sand-100);padding:9px 14px;border-radius:20px;font-size:13px;font-weight:600;"><input type="checkbox" name="pay_phase2" value="1" {{ old('pay_phase2', ($app->contributions['phase2'] ?? false)) ? 'checked' : '' }}> Phase 2 · TShs 1,800,000 (12 shares)</label>
                    <select name="pay_phase2_mode" style="padding:9px 12px;border:1.5px solid var(--line);border-radius:20px;font-size:13px;font-weight:600;background:var(--white);"><option value="">Phase 2 mode…</option><option value="lumpsum" {{ old('pay_phase2_mode', ($app->contributions['phase2_mode'] ?? '')) === 'lumpsum' ? 'selected' : '' }}>Lumpsum</option><option value="installment" {{ old('pay_phase2_mode', ($app->contributions['phase2_mode'] ?? '')) === 'installment' ? 'selected' : '' }}>Installment</option></select>
                </div>
            </div>
            <div class="field"><label>Payment reference numbers (one per line)</label><textarea name="payment_refs" rows="2" placeholder="e.g. Entrance fee — ref 998877">{{ old('payment_refs', ($app->contributions['payment_refs'] ?? '')) }}</textarea></div>
            <div class="form-row" style="grid-template-columns:1fr 1fr 1fr;">
                <div class="field"><label>Payment slips (evidence)</label><input type="file" name="payment_slips[]" multiple accept=".jpg,.jpeg,.png,.pdf">@if(! empty($app->attachments['slips']))<div class="cell-sub" style="margin-top:6px;">{{ count($app->attachments['slips']) }} file(s) attached ✓ · new uploads are added</div>@endif</div>
                <div class="field"><label>Bank standing order (phase 2)</label><input type="file" name="standing_order" accept=".jpg,.jpeg,.png,.pdf">@if(! empty($app->attachments['standing_order']))<div class="cell-sub" style="margin-top:6px;">Uploaded ✓ · re-upload to replace</div>@endif</div>
                <div class="field"><label>Annual subscription slip (students)</label><input type="file" name="subscription_slip" accept=".jpg,.jpeg,.png,.pdf">@if(! empty($app->attachments['subscription_slip']))<div class="cell-sub" style="margin-top:6px;">Uploaded ✓ · re-upload to replace</div>@endif</div>
            </div>
            <div style="display:flex;gap:10px;"><a href="{{ route('join.step', eid(2)) }}" class="btn btn-ghost">← Back</a><button class="btn btn-primary" type="submit">Save &amp; continue →</button></div>
        </form>
        @endif

        @if($step === 4)
        <p style="font-size:14px;color:var(--ink-soft);margin-bottom:20px;line-height:1.7;max-width:1000px;">Who is joining, and who benefits. Choose your membership type, tell us who introduced you, write a short bibliography for the group documents, and name your beneficiaries with their % allocation (must add up to 100%). Groups fill the group section too.</p>
        <form method="POST" action="{{ route('join.save', eid(4)) }}" enctype="multipart/form-data">@csrf
            <div class="form-row" style="grid-template-columns:1fr 1fr 1fr;">
                <div class="field"><label>Type of membership applied</label><select name="member_type_id"><option value="">— Select —</option>@foreach($types as $t)<option value="{{ $t->id }}" {{ (string)old('member_type_id', $app->member_type_id) === (string)$t->id ? 'selected' : '' }}>{{ $t->name }}</option>@endforeach</select></div>
                <div class="field"><label>Group</label><select name="member_group_id"><option value="">— None (individual) —</option>@foreach($groups as $g)<option value="{{ $g->id }}" {{ (string)old('member_group_id', $app->member_group_id) === (string)$g->id ? 'selected' : '' }}>{{ $g->name }}</option>@endforeach</select></div>
                <div class="field"><label>Consider me for ordinary membership</label><select name="consider_ordinary"><option value="">— Select —</option><option value="1" {{ old('consider_ordinary', $app->consider_ordinary) ? 'selected' : '' }}>Yes</option><option value="0" {{ old('consider_ordinary', $app->consider_ordinary) === false && old('consider_ordinary') !== null ? 'selected' : '' }}>No</option></select></div>
            </div>
            <div class="form-row">
                <div class="field"><label>Who introduced / guarantees you?</label><input name="referrer" value="{{ old('referrer', $app->referrer) }}" placeholder="Name of person, or how you heard of FeedTan"></div>
                <div class="field"><label>Membership application letter</label><input type="file" name="application_letter" accept=".jpg,.jpeg,.png,.pdf">@if(! empty($app->attachments['application_letter']))<div class="cell-sub" style="margin-top:6px;">Uploaded ✓ · re-upload to replace</div>@endif</div>
            </div>
            <div class="field"><label>Short bibliography (published in group documents — optional)</label><textarea name="biography" rows="3" placeholder="A few lines about yourself">{{ old('biography', $app->biography) }}</textarea></div>

            <h3 style="margin:22px 0 10px;">Beneficiaries <span class="cell-sub">(in case of unfortunate event of death)</span></h3>
            <div id="benList">
                @php $bens = old('beneficiaries', $app->beneficiaries ?? [['name' => '', 'relationship' => '', 'allocation' => '', 'bank' => '', 'contact' => '']]); @endphp
                @foreach($bens as $i => $b)
                <div class="ben-row" style="display:grid;grid-template-columns:2fr 1fr 1fr 1fr 1fr auto;gap:10px;margin-bottom:10px;align-items:end;">
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
            <div class="form-row" style="grid-template-columns:2fr 1fr 1fr 1fr;">
                <div class="field"><label>Specific goal</label><input name="savings_goal" value="{{ old('savings_goal', $app->savings_goal) }}" placeholder="What is the money for?"></div>
                <div class="field"><label>Amount (TZS)</label><input type="number" name="goal_amount" value="{{ old('goal_amount', $app->goal_amount) }}" min="0" step="1000" placeholder="Figures only"></div>
                <div class="field"><label>Months</label><input type="number" name="goal_months" value="{{ old('goal_months', $app->goal_months) }}" min="1" max="600"></div>
                <div class="field"><label>Start saving</label><input type="date" name="goal_start" value="{{ old('goal_start', $app->goal_start?->format('Y-m-d')) }}"></div>
            </div>

            <h3 style="margin:22px 0 10px;">Group applicants only</h3>
            <div class="form-row" style="grid-template-columns:1fr 1fr;">
                <div class="field"><label>Name of the group</label><input name="group_name" value="{{ old('group_name', $app->group_name) }}"></div>
                <div class="field"><label>Registered with government?</label><select name="group_registered"><option value="">— Select —</option><option value="1" {{ old('group_registered', $app->group_registered) ? 'selected' : '' }}>Yes</option><option value="0" {{ old('group_registered', $app->group_registered) === false && old('group_registered') !== null ? 'selected' : '' }}>No</option></select></div>
            </div>
            <div class="field"><label>Names of leaders</label><input name="group_leaders" value="{{ old('group_leaders', $app->group_leaders) }}"></div>
            <div class="form-row">
                <div class="field"><label>Group bank account</label><input name="group_bank_account" value="{{ old('group_bank_account', $app->group_bank_account) }}"></div>
                <div class="field"><label>Group contacts (email, mobile, address)</label><input name="group_contacts" value="{{ old('group_contacts', $app->group_contacts) }}"></div>
            </div>
            <div class="field"><label>Notes</label><textarea name="notes" rows="2" placeholder="Anything else the office should know">{{ old('notes', $app->notes) }}</textarea></div>
            <div style="display:flex;gap:10px;"><a href="{{ route('join.step', eid(3)) }}" class="btn btn-ghost">← Back</a><button class="btn btn-primary" type="submit">Save &amp; review →</button></div>
        </form>
        <script>
        let benIndex = {{ count($bens) }};
        function addBenRow(){
            const host = document.getElementById('benList');
            const div = document.createElement('div');
            div.className = 'ben-row';
            div.style.cssText = 'display:grid;grid-template-columns:2fr 1fr 1fr 1fr 1fr auto;gap:10px;margin-bottom:10px;align-items:end;';
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
        <div class="detail-grid" style="grid-template-columns:repeat(4,1fr);margin-bottom:20px;">
            <div class="detail-item"><div class="dk">Name</div><div class="dv">{{ $app->name }}</div></div>
            <div class="detail-item"><div class="dk">Sex</div><div class="dv">{{ $app->sex ? ucfirst($app->sex) : '—' }}</div></div>
            <div class="detail-item"><div class="dk">Date of birth</div><div class="dv">{{ $app->dob?->format('d M Y') ?? '—' }}</div></div>
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
        <div class="detail-grid" style="grid-template-columns:repeat(4,1fr);margin-bottom:20px;">
            <div class="detail-item"><div class="dk">Bank</div><div class="dv">{{ $app->bank_name ?? '—' }}</div></div>
            <div class="detail-item"><div class="dk">Account</div><div class="dv">{{ $app->bank_account ?? '—' }}</div></div>
            <div class="detail-item"><div class="dk">Entrance fee</div><div class="dv">{{ ($app->contributions['entrance_fee'] ?? false) ? 'Paid ✓' : '—' }}</div></div>
            <div class="detail-item"><div class="dk">Capital contribution</div><div class="dv">{{ ($app->contributions['capital_contribution'] ?? false) ? 'Paid ✓' : '—' }}</div></div>
            <div class="detail-item"><div class="dk">Phase 2 (12 shares)</div><div class="dv">{{ ($app->contributions['phase2'] ?? false) ? 'Paid ✓ ('.($app->contributions['phase2_mode'] ?? '?').')' : '—' }}</div></div>
            <div class="detail-item"><div class="dk">Payment refs</div><div class="dv">{{ ($app->contributions['payment_refs'] ?? '') ?: '—' }}</div></div>
            <div class="detail-item"><div class="dk">Slips attached</div><div class="dv">{{ count($app->attachments['slips'] ?? []) }}</div></div>
            <div class="detail-item"><div class="dk">Standing order</div><div class="dv">{{ ! empty($app->attachments['standing_order']) ? 'Attached ✓' : '—' }}</div></div>
        </div>
        <h3 style="margin:0 0 12px;">Membership &amp; people <a href="{{ route('join.step', eid(4)) }}" style="color:var(--terracotta-600);font-size:13px;">Edit</a></h3>
        <div class="detail-grid" style="grid-template-columns:repeat(4,1fr);margin-bottom:20px;">
            <div class="detail-item"><div class="dk">Type applied</div><div class="dv">{{ $app->memberType->name ?? '—' }}</div></div>
            <div class="detail-item"><div class="dk">Group</div><div class="dv">{{ $app->memberGroup->name ?? ($app->group_name ?: '—') }}</div></div>
            <div class="detail-item"><div class="dk">Ordinary track</div><div class="dv">{{ $app->consider_ordinary ? 'Yes' : '—' }}</div></div>
            <div class="detail-item"><div class="dk">Biography</div><div class="dv">{{ $app->biography ?? '—' }}</div></div>
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
        <div class="detail-grid" style="grid-template-columns:repeat(4,1fr);margin-bottom:20px;">
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
