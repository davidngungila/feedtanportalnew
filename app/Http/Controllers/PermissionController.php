<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
    public static function catalog(): array
    {
        return [
            'members' => ['members.view' => 'View members', 'members.create' => 'Register members', 'members.edit' => 'Edit members', 'members.delete' => 'Delete members'],
            'loans' => ['loans.view' => 'View loans', 'loans.create' => 'Disburse loans', 'loans.repay' => 'Record repayments', 'loans.products' => 'Manage loan products'],
            'deposits' => ['deposits.view' => 'View deposits', 'deposits.create' => 'Record deposits', 'deposits.products' => 'Manage deposit products'],
            'investments' => ['investments.view' => 'View investments', 'investments.create' => 'Record investments', 'investments.returns' => 'Pay investment returns'],
            'swf' => ['swf.view' => 'View SWF', 'swf.create' => 'Record SWF entries', 'swf.claims' => 'Handle SWF claims'],
            'finance' => ['finance.view' => 'View finance books', 'finance.post' => 'Post journals & entries', 'finance.statements' => 'View financial statements'],
            'reports' => ['reports.view' => 'View reports'],
            'admin' => ['users.manage' => 'Manage users & roles', 'settings.manage' => 'Manage settings'],
        ];
    }

    public function index()
    {
        self::ensureCatalog();

        $roles = Role::with('permissions')->orderBy('name')->get();
        $permissions = Permission::orderBy('module')->orderBy('name')->get()->groupBy('module');

        return view('permissions.index', compact('roles', 'permissions'));
    }

    public static function ensureCatalog(): void
    {
        foreach (self::catalog() as $module => $perms) {
            foreach ($perms as $slug => $name) {
                Permission::firstOrCreate(['slug' => $slug], ['name' => $name, 'module' => $module]);
            }
        }
    }

    public function update(Request $request)
    {
        self::ensureCatalog();

        $data = $request->validate([
            'matrix' => ['nullable', 'array'],
            'matrix.*' => ['array'],
            'matrix.*.*' => ['in:1'],
        ]);

        $matrix = $data['matrix'] ?? [];
        $all = Permission::pluck('id', 'slug');

        foreach (Role::all() as $role) {
            $slugs = array_keys($matrix[$role->slug] ?? []);
            $role->permissions()->sync($all->only($slugs)->values());
        }

        return redirect()->route('permissions.index')->with('status', 'Permissions saved. One user can hold 2+ officer roles — access combines.');
    }
}
