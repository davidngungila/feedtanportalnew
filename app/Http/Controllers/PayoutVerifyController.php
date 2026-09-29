<?php

namespace App\Http\Controllers;

use App\Models\InvestmentPayout;
use Illuminate\Http\Request;

class PayoutVerifyController extends Controller
{
    public function resolve(string $code)
    {
        $payout = InvestmentPayout::where('verify_code', strtoupper($code))->firstOrFail();

        return redirect()->route('verify.show', $payout);
    }

    public function show(InvestmentPayout $payout)
    {
        $payout->load('member');

        return view('verify.show', compact('payout'));
    }

    public function confirm(Request $request, InvestmentPayout $payout)
    {
        if ($payout->status === 'paid') {
            return back()->withErrors(['payout' => 'This payout was already paid.']);
        }
        if ($payout->status === 'rejected') {
            return back()->withErrors(['payout' => 'You already rejected this payout.']);
        }
        if ($payout->status === 'verified') {
            return back()->withErrors(['payout' => 'You already verified on '.$payout->verified_at?->format('d M Y').'.']);
        }

        $data = $request->validate([
            'decision_notes' => ['nullable', 'string', 'max:1000'],
            'alloc_cash' => ['nullable', 'numeric', 'min:0'],
            'cash_method' => ['nullable', 'in:mpesa,tigo,mixx,bank'],
            'cash_account' => ['nullable', 'string', 'max:50'],
            'alloc_swf' => ['nullable', 'numeric', 'min:0'],
            'alloc_loan' => ['nullable', 'numeric', 'min:0'],
            'alloc_shares' => ['nullable', 'numeric', 'min:0'],
            'alloc_reinvest' => ['nullable', 'numeric', 'min:0'],
            'reinvest_term' => ['nullable', 'in:2,4,6'],
            'alloc_savings' => ['nullable', 'numeric', 'min:0'],
            'savings_type' => ['nullable', 'in:rda,flex,emergence'],
        ]);

        $num = fn ($v) => max(0, round((float) ($v ?? 0), 2));
        $allocation = [
            'cash' => $num($data['alloc_cash'] ?? 0),
            'cash_method' => $data['cash_method'] ?? null,
            'cash_account' => trim((string) ($data['cash_account'] ?? '')),
            'swf' => $num($data['alloc_swf'] ?? 0),
            'loan' => $num($data['alloc_loan'] ?? 0),
            'shares' => $num($data['alloc_shares'] ?? 0),
            'reinvest' => $num($data['alloc_reinvest'] ?? 0),
            'reinvest_term' => $data['reinvest_term'] ?? null,
            'savings' => $num($data['alloc_savings'] ?? 0),
            'savings_type' => $data['savings_type'] ?? null,
        ];

        if (abs(array_sum([$allocation['cash'], $allocation['swf'], $allocation['loan'], $allocation['shares'], $allocation['reinvest'], $allocation['savings']]) - (float) $payout->net_cash) > 0.5) {
            return back()->withErrors(['allocation' => 'Mgawanyo lazima ujumlishe TZS '.number_format($payout->net_cash, 0).'.'])->withInput();
        }
        if ($allocation['cash'] > 0 && ! in_array($allocation['cash_method'], ['mpesa', 'tigo', 'mixx', 'bank'], true)) {
            return back()->withErrors(['cash_method' => 'Chagua njia ya malipo ya taslimu (M-Pesa, Tigo Pesa, Mixx by Yas au Benki).'])->withInput();
        }
        if ($allocation['cash'] > 0 && $allocation['cash_account'] === '') {
            return back()->withErrors(['cash_account' => 'Weka namba ya kupokea malipo (au akaunti ya benki).'])->withInput();
        }
        if ($allocation['reinvest'] > 0 && ! in_array($allocation['reinvest_term'], ['2', '4', '6'], true)) {
            return back()->withErrors(['reinvest_term' => 'Chagua miaka 2, 4 au 6 kwa kuwekeza tena.'])->withInput();
        }
        if ($allocation['savings'] > 0 && ! in_array($allocation['savings_type'], ['rda', 'flex', 'emergence'], true)) {
            return back()->withErrors(['savings_type' => 'Chagua RDA, Flex au Emergence kwa akiba.'])->withInput();
        }
        if ($allocation['savings'] > 0 && ($allocation['savings_type'] ?? '') === 'rda' && $allocation['savings'] <= 100000) {
            return back()->withErrors(['savings_type' => 'RDA inahitaji zaidi ya TZS 100,000. Chagua Flex au Emergence.'])->withInput();
        }

        $payout->update([
            'status' => 'verified',
            'decision' => 'allocated',
            'decision_notes' => $data['decision_notes'] ?? null,
            'allocation' => $allocation,
            'verified_at' => now(),
        ]);

        $payout = $payout->fresh();
        $summary = $payout->allocationSummary();

        return redirect()->route('verify.show', $payout)->with(
            'status',
            $summary === '—' || str_starts_with($summary, 'No split')
                ? 'Asante! Uthibitisho wako umepokelewa.'
                : 'Asante! Mgawanyo wako umepokelewa: '.$summary.'.'
        );
    }

    public function reject(Request $request, InvestmentPayout $payout)
    {
        if ($payout->status !== 'pending') {
            return back()->withErrors(['payout' => 'Tayari umeshajibu taarifa hizi (status: '.$payout->status.').']);
        }

        $data = $request->validate([
            'rejection_reason' => ['required', 'string', 'min:3', 'max:1000'],
        ]);

        $payout->update([
            'status' => 'rejected',
            'decision' => 'rejected',
            'decision_notes' => $data['rejection_reason'],
        ]);

        return redirect()->route('verify.show', $payout)->with('status', 'Asante! Sababu yako imepokelewa — ofisi itaipitia.');
    }
}
