<?php

namespace App\Http\Controllers\Auth;

use App\Enums\Ask;
use App\Enums\Role as EnumRole;
use App\Enums\Status;
use App\Http\Controllers\Controller;
use App\Http\Resources\MenuResource;
use App\Http\Resources\PermissionResource;
use App\Http\Resources\RoleResource;
use App\Http\Resources\UserResource;
use App\Libraries\AppLibrary;
use App\Models\User;
use App\Services\DefaultAccessService;
use App\Services\MenuService;
use App\Services\PermissionService;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Smartisan\Settings\Facades\Settings;

class GoogleLoginController extends Controller
{
    public PermissionService $permissionService;
    public MenuService $menuService;
    public DefaultAccessService $defaultAccessService;

    public function __construct(
        PermissionService $permissionService,
        MenuService $menuService,
        DefaultAccessService $defaultAccessService
    ) {
        $this->permissionService    = $permissionService;
        $this->menuService          = $menuService;
        $this->defaultAccessService = $defaultAccessService;
    }

    private function configureGoogle()
    {
        $settings = Settings::group('google_auth')->all();

        Log::info('Google Auth Settings fetched from DB:', [
            'client_id' => $settings['client_id'] ?? 'MISSING',
            'client_secret' => isset($settings['client_secret']) ? '*** (set)' : 'MISSING',
            'redirect_url' => $settings['redirect_url'] ?? 'MISSING',
        ]);

        Config::set('services.google.client_id', $settings['client_id'] ?? '');
        Config::set('services.google.client_secret', $settings['client_secret'] ?? '');
        Config::set('services.google.redirect', $settings['redirect_url'] ?? '');
    }

    public function redirectToGoogle()
    {
        $this->configureGoogle();

        return Socialite::driver('google')->stateless()->redirect();
    }

    public function handleGoogleCallback(Request $request)
    {
        // If there's no 'code', this is our own redirect back to the Vue SPA (with ?data=token).
        // Serve the SPA blade so Vue Router can read the ?data= param and log the user in.
        if (! $request->has('code')) {
            return app(\App\Http\Controllers\Frontend\RootController::class)->index($request);
        }

        try {
            $this->configureGoogle();
            $googleUser = Socialite::driver('google')->stateless()->user();

            $user = User::where('email', $googleUser->email)->first();

            if ($user) {
                if (! $user->google_id) {
                    $user->update(['google_id' => $googleUser->id]);
                }
            } else {
                $user = User::create([
                    'name' => $googleUser->name,
                    'username' => Str::slug($googleUser->name).'-'.Str::random(5),
                    'email' => $googleUser->email,
                    'google_id' => $googleUser->id,
                    'password' => Hash::make(Str::random(16)),
                    'is_guest' => Ask::NO,
                    'status' => Status::ACTIVE,
                    'email_verified_at' => Carbon::now()->getTimestamp(),
                ]);
                $user->assignRole(EnumRole::CUSTOMER);
            }

            // Generate Sanctum token
            $token = $user->createToken('auth_token')->plainTextToken;

            // Populate Auth::user() for the current request so DefaultAccessService
            // can safely read Auth::user()->branch_id inside storeOrUpdate().
            Auth::setUser($user);

            // Resolve branch_id (same logic as LoginController)
            $branchId = $user->branch_id;
            if ($user->branch_id == 0) {
                $branchId = Settings::group('site')->get('site_default_branch');
            }
            $this->defaultAccessService->storeOrUpdate(['branch_id' => $branchId]);

            // Build full permission/menu payload — same as LoginController
            $permission        = PermissionResource::collection($this->permissionService->permission($user->roles[0]));
            $menus             = MenuResource::collection(collect($this->menuService->menu($user->roles[0])));
            $defaultPermission = AppLibrary::defaultPermission($permission);
            $defaultMenu       = (object) AppLibrary::defaultMenu($this->menuService->menu($user->roles[0]), $defaultPermission);
            $userRole          = RoleResource::make($user->roles[0]);

            // Encode full payload — matches authLogin Vuex mutation exactly
            $encodedToken = base64_encode(json_encode([
                'token'             => $token,
                'branch_id'         => (int) $user->branch_id,
                'user'              => new UserResource($user),
                'menu'              => $menus,
                'role'              => $userRole,
                'permission'        => $permission,
                'defaultPermission' => $defaultPermission,
                'defaultMenu'       => $defaultMenu,
            ]));

            Log::info('Google Auth Success for user: '.$user->email);

            return redirect(env('APP_URL').'/auth/google/callback?data='.urlencode($encodedToken));

        } catch (Exception $e) {
            Log::error('Google Auth Error: '.$e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect(env('APP_URL').'/login?error=google_auth_failed');
        }
    }
}
