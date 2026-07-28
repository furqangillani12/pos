<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

return new class extends Migration
{
    public function up(): void
    {
        // Client update 24/7 #7: put "Manage Website settings" behind an assignable
        // permission so not every staff member can change the storefront/settings.
        $permission = Permission::firstOrCreate(['name' => 'manage website settings']);

        // Grant to the top-level roles so existing admins keep access after deploy
        // (there is no super-admin Gate bypass in this app — it must be explicit).
        foreach (['admin', 'super_admin'] as $roleName) {
            $role = Role::where('name', $roleName)->first();
            if ($role) {
                $role->givePermissionTo($permission);
            }
        }
    }

    public function down(): void
    {
        Permission::where('name', 'manage website settings')->delete();
    }
};
