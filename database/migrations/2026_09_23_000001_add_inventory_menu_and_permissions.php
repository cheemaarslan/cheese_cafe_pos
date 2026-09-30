<?php

use App\Enums\MenuType;
use App\Enums\Role as EnumRole;
use App\Enums\Status;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

return new class extends Migration
{
    public function up(): void
    {
        $now = now();

        $parent = Permission::firstOrCreate(
            ['name' => 'inventory', 'guard_name' => 'sanctum'],
            ['title' => 'Inventory', 'url' => 'inventory', 'parent' => 0]
        );

        $permissions = [
            ['title' => 'Inventory Create', 'name' => 'inventory_create', 'url' => 'inventory/create'],
            ['title' => 'Inventory Edit', 'name' => 'inventory_edit', 'url' => 'inventory/edit'],
            ['title' => 'Inventory Delete', 'name' => 'inventory_delete', 'url' => 'inventory/delete'],
            ['title' => 'Inventory Show', 'name' => 'inventory_show', 'url' => 'inventory/show'],
        ];

        $createdPermissions = collect([$parent]);
        foreach ($permissions as $permissionData) {
            $permission = Permission::firstOrCreate(
                ['name' => $permissionData['name'], 'guard_name' => 'sanctum'],
                [
                    'title' => $permissionData['title'],
                    'url' => $permissionData['url'],
                    'parent' => $parent->id,
                ]
            );
            $createdPermissions->push($permission);
        }

        $adminRole = Role::find(EnumRole::ADMIN);
        if ($adminRole) {
            foreach ($createdPermissions as $permission) {
                $adminRole->givePermissionTo($permission);
            }
        }

        if (!DB::table('menus')->where('url', 'inventory')->where('type', MenuType::BACKEND)->exists()) {
            DB::table('menus')->insert([
                'name' => 'Inventory',
                'language' => 'inventory',
                'url' => 'inventory',
                'icon' => 'fa-solid fa-boxes-stacked',
                'status' => Status::ACTIVE,
                'parent' => 0,
                'type' => MenuType::BACKEND,
                'priority' => 100,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    public function down(): void
    {
        DB::table('menus')->where('url', 'inventory')->where('type', MenuType::BACKEND)->delete();

        $permissionIds = Permission::whereIn('name', [
            'inventory',
            'inventory_create',
            'inventory_edit',
            'inventory_delete',
            'inventory_show',
        ])->where('guard_name', 'sanctum')->pluck('id');

        DB::table('role_has_permissions')->whereIn('permission_id', $permissionIds)->delete();
        Permission::whereIn('id', $permissionIds)->delete();

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
};
