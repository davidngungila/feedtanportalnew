@extends('layouts.app')
@section('title', 'New Journal Entry')
@section('content')
    <div class="view-head"><div><h2>New Journal Entry</h2><p class="sub">Individual page — debits must equal credits.</p></div><div class="view-actions"><a href="{{ route('finance.journals') }}" class="btn btn-ghost">Back</a></div></div>
    <div class="settings-panel"><h3>Journal</h3>
        <form method="POST" action="{{ route('finance.journals.store') }}" id="journalForm">@csrf
            <div class="form-row"><div class="field"><label>Date *</label><input type="date" name="entry_date" value="{{ now()->toDateString() }}" required></div><div class="field"><label>Description *</label><input name="description" required placeholder="e.g. Month-end adjustment"></div></div>
            <div class="table-scroll"><table style="min-width:560px;">
                <thead><tr><th>Account</th><th>Debit</th><th>Credit</th><th>Narration</th><th></th></tr></thead>
                <tbody id="journalLines">
                    @for($i = 0; $i < 2; $i++)
                    <tr><td><select name="lines[{{ $i }}][finance_account_id]" required>@foreach($accounts as $a)<option value="{{ $a->id }}">{{ $a->code }} · {{ $a->name }}</option>@endforeach</select></td>
                    <td><input type="number" name="lines[{{ $i }}][debit]" min="0" step="0.01" value="0" class="dr" oninput="balanceCheck()"></td>
                    <td><input type="number" name="lines[{{ $i }}][credit]" min="0" step="0.01" value="0" class="cr" oninput="balanceCheck()"></td>
                    <td><input name="lines[{{ $i }}][narration]" placeholder="Optional"></td><td></td></tr>
                    @endfor
                </tbody>
            </table></div>
            <div class="receipt"><div class="receipt-row"><span>Total debit</span><b id="drTotal">0.00</b></div><div class="receipt-row"><span>Total credit</span><b id="crTotal">0.00</b></div><div class="receipt-row"><span>Balanced</span><b id="balState">—</b></div></div>
            <div style="display:flex;gap:10px;margin:14px 0;"><button type="button" class="btn btn-ghost btn-sm" onclick="addLine()">+ Add line</button></div>
            <button class="btn btn-primary" type="submit">Post journal</button>
        </form>
    </div>
@endsection

@section('scripts')
<script>
let lineIdx = 2;
const accountOptions = @json($accounts->map(fn($a) => ['id' => $a->id, 'label' => $a->code.' · '.$a->name]));
function addLine(){
    const tb = document.getElementById('journalLines');
    const tr = document.createElement('tr');
    tr.innerHTML = `<td><select name="lines[${lineIdx}][finance_account_id]" required>${accountOptions.map(o => `<option value="${o.id}">${o.label}</option>`).join('')}</select></td>
    <td><input type="number" name="lines[${lineIdx}][debit]" min="0" step="0.01" value="0" class="dr" oninput="balanceCheck()"></td>
    <td><input type="number" name="lines[${lineIdx}][credit]" min="0" step="0.01" value="0" class="cr" oninput="balanceCheck()"></td>
    <td><input name="lines[${lineIdx}][narration]" placeholder="Optional"></td>
    <td><button type="button" class="btn btn-ghost btn-sm" onclick="this.closest('tr').remove();balanceCheck();">✕</button></td>`;
    tb.appendChild(tr);
    lineIdx++;
}
function balanceCheck(){
    let dr = 0, cr = 0;
    document.querySelectorAll('#journalLines .dr').forEach(i => dr += parseFloat(i.value) || 0);
    document.querySelectorAll('#journalLines .cr').forEach(i => cr += parseFloat(i.value) || 0);
    document.getElementById('drTotal').textContent = dr.toFixed(2);
    document.getElementById('crTotal').textContent = cr.toFixed(2);
    document.getElementById('balState').textContent = (dr > 0 && Math.abs(dr - cr) < 0.005) ? 'Yes ✓' : 'No';
}
balanceCheck();
</script>
@endsection
