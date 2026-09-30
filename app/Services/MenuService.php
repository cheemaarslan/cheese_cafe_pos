<?php

namespace App\Services;

use App\Enums\MenuType;
use App\Enums\Status;
use App\Libraries\AppLibrary;
use App\Libraries\QueryExceptionLibrary;
use App\Models\Menu;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class MenuService
{
    /**
     * @throws Exception
     */
    public function menu(Role $role): array
    {
        try {
            // Exclude only explicitly hidden items (status = INACTIVE) rather than requiring an
            // exact match to ACTIVE, since legacy menu rows may hold other "not disabled" values.
            $menus = Menu::where('type', MenuType::BACKEND)
                ->where('status', '!=', Status::INACTIVE)
                ->orderBy('id')
                ->get()
                ->toArray();
            $permissions = Permission::get();
            $rolePermissions = Permission::join(
                'role_has_permissions',
                'role_has_permissions.permission_id',
                '=',
                'permissions.id'
            )->where('role_has_permissions.role_id', $role->id)->get()->pluck('name', 'id');
            $permissions = AppLibrary::permissionWithAccess($permissions, $rolePermissions);
            $permissions = AppLibrary::pluck($permissions, 'obj', 'url');

            return AppLibrary::numericToAssociativeArrayBuilder(AppLibrary::menu($menus, $permissions));
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * Full sidebar menu tree (every item, regardless of status) for the
     * "show/hide sidebar items" settings page.
     *
     * @throws Exception
     */
    public function list(): array
    {
        try {
            $menus = Menu::where('type', MenuType::BACKEND)->orderBy('id')->get()->toArray();

            return AppLibrary::numericToAssociativeArrayBuilder($menus);
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * Flat list of the Settings section's own left-hand nav items (Company, Site, Branches, ...)
     * for the "show/hide settings tabs" admin page.
     *
     * @throws Exception
     */
    public function settingsMenuList(): array
    {
        try {
            return Menu::where('type', MenuType::SETTINGS)->orderBy('id')->get()->toArray();
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * The Settings section's own left-hand nav (Company, Site, Branches, ...), filtered to what
     * should actually render, for MenuComponent.vue to consume directly.
     *
     * @throws Exception
     */
    public function settingsMenu(): array
    {
        try {
            return Menu::where('type', MenuType::SETTINGS)
                ->where('status', '!=', Status::INACTIVE)
                ->orderBy('id')
                ->get()
                ->toArray();
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function changeStatus(Menu $menu, Request $request): Menu
    {
        try {
            if ($request->filled('status')) {
                $menu->update(['status' => $request->input('status')]);
            }

            return $menu;
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }
}
