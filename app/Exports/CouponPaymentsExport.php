<?php

namespace App\Exports;

use App\Models\InvestmentPayout;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class CouponPaymentsExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection(): \Illuminate\Support\Enumerable
    {
        return InvestmentPayout::with('member')
            ->where('kind', 'coupon')
            ->latest()
            ->get();
    }

    public function headings(): array
    {
        return [
            'Verify Code',
            'Name',
            'Phone of their payment',
            'Amount Earned',
            'Loan installment',
            'SWF deduction',
            'Fines deduction',
            'T-shirt deduction',
            'Capital FeedTan CMG',
            'Net cash',
            'Status',
            'Decision',
            'SMS sent at',
            'Verified at',
            'Paid at',
            'Imported at',
        ];
    }

    public function map($payout): array
    {
        return [
            $payout->verify_code,
            $payout->member->name ?? '',
            $payout->phone,
            (float) $payout->amount,
            (float) $payout->loan_installment,
            (float) $payout->swf_deduction,
            (float) $payout->fines_deduction,
            (float) $payout->tshirt_deduction,
            (float) $payout->capital_cmg,
            (float) $payout->net_cash,
            $payout->status,
            $payout->decisionLabel(),
            optional($payout->sms_sent_at)->format('d M Y H:i'),
            optional($payout->verified_at)->format('d M Y H:i'),
            optional($payout->paid_at)->format('d M Y H:i'),
            optional($payout->created_at)->format('d M Y H:i'),
        ];
    }
}
