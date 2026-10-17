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

        $expenseParent = Permission::firstOrCreate(
            ['name' => 'expenses', 'guard_name' => 'sanctum'],
            ['title' => 'Expenses', 'url' => 'expenses', 'parent' => 0]
        );

        $expensePermissions = [
            ['title' => 'Expenses Create', 'name' => 'expenses_create', 'url' => 'expenses/create'],
            ['title' => 'Expenses Edit', 'name' => 'expenses_edit', 'url' => 'expenses/edit'],
            ['title' => 'Expenses Delete', 'name' => 'expenses_delete', 'url' => 'expenses/delete'],
        ];

        $createdPermissions = collect([$expenseParent]);
        foreach ($expensePermissions as $permissionData) {
            $createdPermissions->push(Permission::firstOrCreate(
                ['name' => $permissionData['name'], 'guard_name' => 'sanctum'],
                [
                    'title' => $permissionData['title'],
                    'url' => $permissionData['url'],
                    'parent' => $expenseParent->id,
                ]
            ));
        }

        $createdPermissions->push(Permission::firstOrCreate(
            ['name' => 'profit-loss', 'guard_name' => 'sanctum'],
            ['title' => 'Profit & Loss', 'url' => 'profit-loss', 'parent' => 0]
        ));

        $adminRole = Role::find(EnumRole::ADMIN);
        if ($adminRole) {
            foreach ($createdPermissions as $permission) {
                $adminRole->givePermissionTo($permission);
            }
        }

        if (!DB::table('menus')->where('url', 'expenses')->where('type', MenuType::BACKEND)->exists()) {
            DB::table('menus')->insert([
                'name' => 'Expenses',
                'language' => 'expenses',
                'url' => 'expenses',
                'icon' => 'fa-solid fa-money-bill-wave',
                'status' => Status::ACTIVE,
                'parent' => 0,
                'type' => MenuType::BACKEND,
                'priority' => 100,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        if (!DB::table('menus')->where('url', 'profit-loss')->where('type', MenuType::BACKEND)->exists()) {
            $reportsId = DB::table('menus')
                ->where('url', '#')
                ->where('language', 'reports')
                ->where('type', MenuType::BACKEND)
                ->value('id');

            DB::table('menus')->insert([
                'name' => 'Profit & Loss',
                'language' => 'profit_loss',
                'url' => 'profit-loss',
                'icon' => 'fa-solid fa-scale-balanced',
                'status' => Status::ACTIVE,
                'parent' => $reportsId ?? 0,
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
        DB::table('menus')->whereIn('url', ['expenses', 'profit-loss'])->where('type', MenuType::BACKEND)->delete();

        $permissionIds = Permission::whereIn('name', [
            'expenses',
            'expenses_create',
            'expenses_edit',
            'expenses_delete',
            'profit-loss',
        ])->where('guard_name', 'sanctum')->pluck('id');

        DB::table('role_has_permissions')->whereIn('permission_id', $permissionIds)->delete();
        Permission::whereIn('id', $permissionIds)->delete();

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
};
