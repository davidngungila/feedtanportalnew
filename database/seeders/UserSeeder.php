<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::firstOrCreate(
            ['email' => 'admin@feedtan.local'],
            ['name' => 'Administrator', 'password' => Hash::make('password123'), 'role' => 'admin'],
        );

        $admin->roles()->sync(Role::pluck('id'));

        // Demo officers (one user can hold 2 officer roles).
        $demos = [
            ['Demo Loan Officer', 'loan@feedtan.local', ['loan_officer']],
            ['Demo Deposit Officer', 'deposit@feedtan.local', ['deposit_officer']],
            ['Demo Investment Officer', 'investment@feedtan.local', ['investment_officer']],
            ['Demo SWF Officer', 'swf@feedtan.local', ['swf_officer']],
            ['Demo Dual Officer', 'dual@feedtan.local', ['loan_officer', 'deposit_officer']],
        ];

        foreach ($demos as [$name, $email, $slugs]) {
            $u = User::firstOrCreate(
                ['email' => $email],
                ['name' => $name, 'password' => Hash::make('password123'), 'role' => $slugs[0]],
            );
            $u->roles()->sync(Role::whereIn('slug', $slugs)->pluck('id'));
        }
    }
}
