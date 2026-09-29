@extends('layouts.app')
@section('title', 'Coupon Payment Verification')
@section('content')
    <div class="view-head">
        <div><h2>Coupon Payment Verification</h2><p class="sub">Excel/CSV columns: Name, Amount Earned, Loan installment, SWF deduction, Fines deduction, T-shirt deduction, Capital FeedTan CMG, Net cash, Phone of their payment. Phone is required for SMS.</p></div>
        <div class="view-actions">
            <form method="POST" action="{{ route('coupon.sms.bulk') }}" onsubmit="return confirm('Send verification SMS to {{ $pendingSms }} member(s)?')" style="display:inline;">@csrf<button class="btn btn-primary" type="submit">Send bulk SMS ({{ $pendingSms }})</button></form>
            <a href="{{ route('coupon.export') }}" class="btn btn-ghost">Export Excel</a>
            <a href="{{ route('coupon.template') }}" class="btn btn-ghost">Download template</a><a href="{{ route('investments.index') }}" class="btn btn-ghost">Back</a>
        </div>
    </div>

    @if(session()->has('import_failed') && count(session('import_failed')))
    <div class="table-card"><div class="table-toolbar"><strong>Skipped rows</strong></div>
        <div class="panel-body">@foreach(session('import_failed') as $f)<div class="cell-sub">• {{ $f }}</div>@endforeach</div>
    </div>
    @endif

    <div class="form-layout">
        <div class="settings-panel"><h3>Upload sheet</h3>
            <form method="POST" action="{{ route('coupon.store') }}" enctype="multipart/form-data">@csrf
                <div class="field"><label>Excel or CSV file * (.xlsx, .xls, .csv — max 5MB)</label><input type="file" name="sheet" accept=".xlsx,.xls,.csv,.txt" required></div>
                <div class="receipt"><div class="receipt-row"><span>Matching</span><b>By phone if present, else by name; missing members are created</b></div><div class="receipt-row"><span>SMS</span><b>Admin triggers the verification link to each phone number</b></div><div class="receipt-row"><span>Codes</span><b>Each coupon gets a unique SMS code like SHDG63</b></div></div>
                <div style="margin-top:14px;"><button class="btn btn-primary" type="submit">Import coupons</button></div>
            </form>
        </div>
        <div class="settings-panel"><h3>How it works</h3>
            <div class="detail-grid">
                <div class="detail-item"><div class="dk">Step 1</div><div class="dv">Import the sheet — coupons stay pending.</div></div>
                <div class="detail-item"><div class="dk">Step 2</div><div class="dv">Admin triggers SMS with each member's verification link.</div></div>
                <div class="detail-item"><div class="dk">Step 3</div><div class="dv">Member opens the link and confirms the payment details.</div></div>
                <div class="detail-item"><div class="dk">Step 4</div><div class="dv">Pay verified coupons — deductions post automatically.</div></div>
            </div>
        </div>
    </div>

    @php $previewPayout = $payouts->firstWhere('status', 'pending'); @endphp
    <div class="panel" style="margin-bottom:24px;">
        <div class="panel-head"><h3>Message ya kutuma (bulk SMS)</h3><span class="link">{{ $pendingSms }} waiting</span></div>
        <div class="panel-body">
            <form method="POST" action="{{ route('coupon.sms.bulk') }}" onsubmit="return confirm('Send to {{ $pendingSms }} member(s)?')">@csrf
                <div class="field"><label>Message text — placeholders: {name} {amount} {link} {code} {phone}</label>
                    <textarea name="message" id="bulkMsg" rows="3">{{ \App\Services\SmsService::defaultCouponTemplate() }}</textarea>
                </div>
                <div class="receipt"><div class="receipt-row"><span>Preview{{ $previewPayout ? ' — '.$previewPayout->member->name : '' }}</span><b id="bulkPreview" style="font-weight:600;"></b></div></div>
                <div style="margin-top:12px;display:flex;gap:10px;align-items:center;">
                    <button class="btn btn-primary btn-sm" type="submit">Send bulk SMS ({{ $pendingSms }})</button>
                    <span class="cell-sub">Leave the text as-is to use the saved template.</span>
                </div>
            </form>
        </div>
    </div>

    <div class="table-card">
        <div class="table-toolbar"><strong>SMS links (latest {{ $payouts->count() }})</strong><span class="cell-sub">Send each member: their link + amount</span>
            <form id="bulkDeleteForm" method="POST" action="{{ route('coupon.bulk.destroy') }}" style="margin-left:auto;display:flex;gap:8px;align-items:center;">@csrf @method('DELETE')<span class="cell-sub" id="bulkCount"></span><button class="btn btn-danger btn-sm" type="submit">Delete selected</button></form>
        </div>
        <div class="table-scroll"><table>
                <thead><tr><th style="width:36px;"><input type="checkbox" id="checkAll" title="Select all" style="width:16px;height:16px;accent-color:var(--terracotta-600);"></th><th>Member</th><th>Phone</th><th>SMS link</th><th>Net cash</th><th>Status</th><th>Decision</th><th>SMS</th><th style="text-align:right;">Pay</th></tr></thead>
            <tbody>
                @forelse($payouts as $p)
                <tr>
                    <td><input type="checkbox" class="payout-check" value="{{ eid($p->id) }}" style="width:16px;height:16px;accent-color:var(--terracotta-600);"></td>
                    <td><div class="cell-title"><a href="{{ route('coupon.show', $p) }}">{{ $p->member->name ?? '—' }}</a></div><div class="cell-sub"><a href="{{ route('coupon.show', $p) }}">{{ $p->verify_code }}</a></div></td>
                    <td><div class="cell-title">{{ $p->phone }}</div><div class="cell-sub">{{ $p->intlPhone() }}</div></td>
                    <td><div style="display:flex;gap:6px;align-items:center;"><input value="{{ $p->shortUrl() }}" readonly onclick="this.select()" style="width:220px;padding:7px 10px;border:1.5px solid var(--line);border-radius:8px;font-size:12px;"><button type="button" class="btn btn-ghost btn-sm" onclick="navigator.clipboard.writeText('{{ $p->shortUrl() }}');toast('Link copied.', 'success');">Copy</button></div></td>
                    <td class="cell-title">@money($p->net_cash)</td>
                    <td><span class="tag {{ status_badge($p->status) }}">{{ ucfirst($p->status) }}</span></td>
                    <td><div class="cell-sub">{{ $p->decisionLabel() }}</div></td>
                    <td>@if($p->sms_sent_at)<span class="cell-sub" title="{{ $p->sms_sent_at->format('d M Y H:i') }}">Sent ✓</span>
                        <form method="POST" action="{{ route('coupon.sms.single', $p) }}" style="display:inline;">@csrf<button class="btn btn-ghost btn-sm" type="submit" title="Resend">↻</button></form>
                        @elseif($p->status === 'pending')
                        <form method="POST" action="{{ route('coupon.sms.single', $p) }}">@csrf<button class="btn btn-ghost btn-sm" type="submit">Send</button></form>
                        @else<span class="cell-sub">—</span>@endif
                    </td>
                    <td><div style="display:flex;gap:6px;justify-content:flex-end;flex-wrap:wrap;">
                        <a href="{{ route('coupon.show', $p) }}" class="btn btn-ghost btn-sm">View</a>
                        @if($p->status === 'verified')
                        <form method="POST" action="{{ route('coupon.pay', $p) }}" onsubmit="return confirm('Pay @money($p->net_cash) and apply deductions?')">@csrf<button class="btn btn-primary btn-sm" type="submit">Pay</button></form>
                        @elseif($p->status === 'paid')<span class="cell-sub">{{ $p->paid_at?->format('d M Y') }}</span>
                        @else<span class="cell-sub">Awaiting SMS verify</span>@endif
                    </div></td>
                </tr>
                @empty<tr><td colspan="9" class="empty-state">No coupon payments imported yet.</td></tr>@endforelse
            </tbody>
        </table></div>
    <div class="modal-backdrop" id="bulkDeleteModal">
        <div class="popup" style="padding:22px;text-align:center;">
            <div style="width:52px;height:52px;border-radius:50%;background:var(--danger-100);color:var(--danger);display:flex;align-items:center;justify-content:center;margin:0 auto 12px;font-size:22px;font-weight:700;">!</div>
            <h3 style="font-size:17px;margin:0 0 6px;">Delete <span id="bulkDeleteCount">0</span> coupon(s)?</h3>
            <p class="sub" style="font-size:13px;color:var(--ink-soft);">Paid ones are skipped. This cannot be undone.</p>
            <div style="display:flex;gap:10px;margin-top:16px;">
                <button type="button" class="btn btn-ghost" style="flex:1;" onclick="closeModal('bulkDeleteModal')">Cancel</button>
                <button type="button" class="btn btn-danger" id="bulkDeleteConfirm" style="flex:1;">Delete</button>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
(function(){
    const msgBox = document.getElementById('bulkMsg');
    const preview = document.getElementById('bulkPreview');
    @if($previewPayout)
    const sample = {
        '{name}': @json($previewPayout->member->name ?? 'mwanachama'),
        '{amount}': @json(number_format((float) $previewPayout->net_cash, 0)),
        '{link}': @json($previewPayout->shortUrl()),
        '{code}': @json($previewPayout->verify_code),
        '{phone}': @json($previewPayout->phone),
    };
    function renderPreview(){
        let text = msgBox.value;
        for (const [k, v] of Object.entries(sample)) text = text.split(k).join(v);
        preview.textContent = text;
    }
    if (msgBox && preview) { msgBox.addEventListener('input', renderPreview); renderPreview(); }
    @endif
    const checkAll = document.getElementById('checkAll');
    const boxes = () => Array.from(document.querySelectorAll('.payout-check'));
    const countEl = document.getElementById('bulkCount');
    const form = document.getElementById('bulkDeleteForm');
    function refresh(){
        const n = boxes().filter(b => b.checked).length;
        countEl.textContent = n ? n + ' selected' : '';
        if (checkAll) checkAll.checked = boxes().length > 0 && boxes().every(b => b.checked);
    }
    if (checkAll) checkAll.addEventListener('change', () => boxes().forEach(b => { b.checked = checkAll.checked; }));
    boxes().forEach(b => b.addEventListener('change', refresh));
    refresh();
    form.addEventListener('submit', function(e){
        e.preventDefault();
        const checked = boxes().filter(b => b.checked);
        if (!checked.length) { toast('Select at least one coupon.', 'error'); return; }
        document.getElementById('bulkDeleteCount').textContent = checked.length;
        openModal('bulkDeleteModal');
    });
    document.getElementById('bulkDeleteConfirm').addEventListener('click', function(){
        const checked = boxes().filter(b => b.checked);
        checked.forEach(b => {
            const h = document.createElement('input');
            h.type = 'hidden'; h.name = 'ids[]'; h.value = b.value;
            form.appendChild(h);
        });
        closeModal('bulkDeleteModal');
        showPageLoader();
        form.submit();
    });
})();
</script>
@endsection
