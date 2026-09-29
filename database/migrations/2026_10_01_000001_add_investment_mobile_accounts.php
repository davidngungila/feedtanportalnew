<?php

use App\Models\FinanceAccount;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        $accounts = [
            ['1110', 'Mobile Money Float', 'asset', 'Mobile money collections'],
            ['2300', 'Member Investments Liability', 'liability', 'Investments owed to members'],
            ['5300', 'Investment Return Expense', 'expense', 'Returns paid on investments'],
        ];

        foreach ($accounts as [$code, $name, $type, $desc]) {
            FinanceAccount::firstOrCreate(['code' => $code], [
                'name' => $name, 'type' => $type, 'description' => $desc, 'status' => 'active',
            ]);
        }
    }

    public function down(): void
    {
        FinanceAccount::whereIn('code', ['1110', '2300', '5300'])->delete();
    }
};
