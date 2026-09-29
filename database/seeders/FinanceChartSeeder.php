<?php

namespace Database\Seeders;

use App\Models\FinanceAccount;
use Illuminate\Database\Seeder;

class FinanceChartSeeder extends Seeder
{
    public function run(): void
    {
        $chart = [
            ['1000', 'Cash on Hand', 'asset', 'Liquid cash'],
            ['1100', 'Bank Account', 'asset', 'Bank balance'],
            ['1110', 'Mobile Money Float', 'asset', 'Mobile money collections'],
            ['1200', 'Loans Receivable (Books)', 'asset', 'Manual loan bookings'],
            ['1300', 'Receivables Control', 'asset', 'Outstanding receivables'],
            ['2000', 'Member Deposits Liability', 'liability', 'Savings owed to members'],
            ['2100', 'SWF Liability', 'liability', 'Welfare fund owed'],
            ['2200', 'Payables Control', 'liability', 'Outstanding payables'],
            ['2300', 'Member Investments Liability', 'liability', 'Investments owed to members'],
            ['3000', 'Opening Capital / Equity', 'equity', 'Owner capital'],
            ['3100', 'Retained Surplus', 'equity', 'Accumulated surplus'],
            ['4100', 'Fee Income', 'income', 'Fees earned'],
            ['4200', 'Service Charges', 'income', 'Charges earned'],
            ['4300', 'Interest Income', 'income', 'Interest earned'],
            ['4400', 'Commission Income', 'income', 'Commissions earned'],
            ['4900', 'Other Income', 'income', 'Other revenue'],
            ['5100', 'Operating Expenses', 'expense', 'General expenses'],
            ['5200', 'Salaries & Allowances', 'expense', 'Staff costs'],
            ['5300', 'Investment Return Expense', 'expense', 'Returns paid on investments'],
        ];

        foreach ($chart as [$code, $name, $type, $desc]) {
            FinanceAccount::firstOrCreate(['code' => $code], [
                'name' => $name, 'type' => $type, 'description' => $desc, 'status' => 'active',
            ]);
        }
    }
}
