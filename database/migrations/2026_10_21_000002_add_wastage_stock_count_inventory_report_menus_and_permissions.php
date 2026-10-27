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
    // Parent permission (named after its menu url) => its child permissions
    private const PERMISSIONS = [
        'inventory-wastage' => [
            'title' => 'Wastage',
            'children' => [
                ['title' => 'Wastage Create', 'name' => 'inventory_wastage_create', 'url' => 'inventory-wastage/create'],
                ['title' => 'Wastage Delete', 'name' => 'inventory_wastage_delete', 'url' => 'inventory-wastage/delete'],
            ],
        ],
        'stock-counts' => [
            'title' => 'Stock Count',
            'children' => [
                ['title' => 'Stock Count Create', 'name' => 'stock_counts_create', 'url' => 'stock-counts/create'],
                ['title' => 'Stock Count Show', 'name' => 'stock_counts_show', 'url' => 'stock-counts/show'],
            ],
        ],
        'inventory-report' => [
            'title' => 'Inventory Report',
            'children' => [],
        ],
    ];

    public function up(): void
    {
        $now = now();

        $createdPermissions = collect();
        foreach (self::PERMISSIONS as $url => $permissionGroup) {
            $parent = Permission::firstOrCreate(
                ['name' => $url, 'guard_name' => 'sanctum'],
                ['title' => $permissionGroup['title'], 'url' => $url, 'parent' => 0]
            );
            $createdPermissions->push($parent);

            foreach ($permissionGroup['children'] as $permissionData) {
                $createdPermissions->push(Permission::firstOrCreate(
                    ['name' => $permissionData['name'], 'guard_name' => 'sanctum'],
                    [
                        'title' => $permissionData['title'],
                        'url' => $permissionData['url'],
                        'parent' => $parent->id,
                    ]
                ));
            }
        }

        $adminRole = Role::find(EnumRole::ADMIN);
        if ($adminRole) {
            foreach ($createdPermissions as $permission) {
                $adminRole->givePermissionTo($permission);
            }
        }

        $reportsId = DB::table('menus')
            ->where('url', '#')
            ->where('language', 'reports')
            ->where('type', MenuType::BACKEND)
            ->value('id');

        $menus = [
            ['name' => 'Wastage', 'language' => 'inventory_wastage', 'url' => 'inventory-wastage', 'icon' => 'fa-solid fa-trash-can', 'parent' => 0],
            ['name' => 'Stock Count', 'language' => 'stock_counts', 'url' => 'stock-counts', 'icon' => 'fa-solid fa-clipboard-check', 'parent' => 0],
            ['name' => 'Inventory Report', 'language' => 'inventory_report', 'url' => 'inventory-report', 'icon' => 'fa-solid fa-chart-column', 'parent' => $reportsId ?? 0],
        ];

        foreach ($menus as $menu) {
            if (!DB::table('menus')->where('url', $menu['url'])->where('type', MenuType::BACKEND)->exists()) {
                DB::table('menus')->insert($menu + [
                    'status' => Status::ACTIVE,
                    'type' => MenuType::BACKEND,
                    'priority' => 100,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    public function down(): void
    {
        DB::table('menus')
            ->whereIn('url', array_keys(self::PERMISSIONS))
            ->where('type', MenuType::BACKEND)
            ->delete();

        $names = [];
        foreach (self::PERMISSIONS as $url => $permissionGroup) {
            $names[] = $url;
            foreach ($permissionGroup['children'] as $permissionData) {
                $names[] = $permissionData['name'];
            }
        }

        $permissionIds = Permission::whereIn('name', $names)->where('guard_name', 'sanctum')->pluck('id');

        DB::table('role_has_permissions')->whereIn('permission_id', $permissionIds)->delete();
        Permission::whereIn('id', $permissionIds)->delete();

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
};
