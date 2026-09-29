<?php

namespace Database\Seeders;

use App\Http\Controllers\PermissionController;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        PermissionController::ensureCatalog();
        Role::where('slug', 'administrator')->first()
            ?->permissions()->sync(Permission::pluck('id'));

        // Sensible defaults per role (added, never removed — custom matrix stays intact).
        $defaults = [
            'chairperson' => ['members.view', 'members.create', 'members.edit', 'members.delete', 'loans.view', 'loans.create', 'loans.repay', 'loans.products', 'deposits.view', 'deposits.create', 'deposits.products', 'investments.view', 'investments.create', 'investments.returns', 'swf.view', 'swf.create', 'swf.claims', 'finance.view', 'finance.post', 'finance.statements', 'reports.view', 'users.manage', 'settings.manage'],
            'secretary' => ['members.view', 'members.create', 'members.edit', 'loans.view', 'loans.create', 'loans.repay', 'deposits.view', 'deposits.create', 'investments.view', 'investments.create', 'investments.returns', 'swf.view', 'swf.create', 'swf.claims', 'finance.view', 'finance.post', 'finance.statements', 'reports.view'],
            'accountant' => ['members.view', 'members.create', 'members.edit', 'loans.view', 'loans.create', 'loans.repay', 'deposits.view', 'deposits.create', 'investments.view', 'investments.create', 'investments.returns', 'swf.view', 'swf.create', 'swf.claims', 'finance.view', 'finance.post', 'finance.statements', 'reports.view'],
            'loan_officer' => ['members.view', 'loans.view', 'loans.create', 'loans.repay'],
            'deposit_officer' => ['members.view', 'deposits.view', 'deposits.create'],
            'investment_officer' => ['members.view', 'investments.view', 'investments.create', 'investments.returns'],
            'swf_officer' => ['members.view', 'swf.view', 'swf.create', 'swf.claims'],
            'member' => [],
            'applicant' => [],
        ];

        $ids = Permission::pluck('id', 'slug');
        foreach ($defaults as $slug => $permSlugs) {
            $role = Role::where('slug', $slug)->first();
            if ($role && $permSlugs) {
                $role->permissions()->syncWithoutDetaching($ids->only($permSlugs)->values());
            }
        }
    }
}
