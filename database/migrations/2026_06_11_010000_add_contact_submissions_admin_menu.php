<?php

use App\Enums\Role as EnumRole;
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

        $permission = Permission::firstOrCreate(
            ['name' => 'contact-submissions', 'guard_name' => 'sanctum'],
            [
                'title' => 'Contact Submissions',
                'url'   => 'contact-submissions',
            ]
        );

        $adminRole = Role::find(EnumRole::ADMIN);
        $adminRole?->givePermissionTo($permission);

        $parentId = DB::table('menus')->where('url', '#')->where('language', 'communications')->value('id');
        if ($parentId && !DB::table('menus')->where('url', 'contact-submissions')->exists()) {
            DB::table('menus')->insert([
                'name'       => 'Contact Submissions',
                'language'   => 'contact_submissions',
                'url'        => 'contact-submissions',
                'icon'       => 'lab lab-messages',
                'status'     => 1,
                'parent'     => $parentId,
                'type'       => 1,
                'priority'   => 100,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    public function down(): void
    {
        DB::table('menus')->where('url', 'contact-submissions')->delete();

        $permission = Permission::where('name', 'contact-submissions')->where('guard_name', 'sanctum')->first();
        if ($permission) {
            DB::table('role_has_permissions')->where('permission_id', $permission->id)->delete();
            $permission->delete();
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
};
