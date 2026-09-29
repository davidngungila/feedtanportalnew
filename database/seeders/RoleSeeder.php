<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            'administrator' => 'Administrator',
            'chairperson' => 'Chairperson',
            'secretary' => 'Secretary',
            'accountant' => 'Accountant',
            'swf_officer' => 'SWF Officer',
            'deposit_officer' => 'Deposit Officer',
            'investment_officer' => 'Investment Officer',
            'loan_officer' => 'Loan Officer',
            'member' => 'Member',
            'applicant' => 'Applicant',
        ];

        foreach ($roles as $slug => $name) {
            Role::firstOrCreate(['slug' => $slug], ['name' => $name]);
        }
    }
}
