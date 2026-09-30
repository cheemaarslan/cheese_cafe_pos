<?php

namespace App\Http\Controllers\Admin;

use App\Http\Resources\MenuResource;
use App\Models\Menu;
use App\Services\MenuService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class MenuController extends AdminController
{
    private MenuService $menuService;

    public function __construct(MenuService $menuService)
    {
        parent::__construct();
        $this->menuService = $menuService;
        $this->middleware(['permission:settings'])->only('changeStatus');
    }

    public function index(Role $role): \Illuminate\Http\Response|JsonResponse|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            $menus = $this->menuService->menu($role);

            return new JsonResponse(['data' => $menus], 201);
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function list(
    ): \Illuminate\Http\Response|\Illuminate\Http\Resources\Json\AnonymousResourceCollection|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory {
        try {
            return MenuResource::collection(collect($this->menuService->list()));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function settingsMenuList(
    ): \Illuminate\Http\Response|\Illuminate\Http\Resources\Json\AnonymousResourceCollection|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory {
        try {
            return MenuResource::collection(collect($this->menuService->settingsMenuList()));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function settingsMenu(
    ): \Illuminate\Http\Response|\Illuminate\Http\Resources\Json\AnonymousResourceCollection|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory {
        try {
            return MenuResource::collection(collect($this->menuService->settingsMenu()));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function changeStatus(Menu $menu, Request $request
    ): \Illuminate\Http\Response|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory {
        try {
            $this->menuService->changeStatus($menu, $request);

            return response('', 202);
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }
}
