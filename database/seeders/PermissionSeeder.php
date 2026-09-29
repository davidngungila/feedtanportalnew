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
    }
}
