<?php

namespace App\Services;

use App\Enums\Role as EnumRole;
use App\Models\AdminNotification;
use App\Models\User;
use Illuminate\Support\Collection;

class AdminNotificationService
{
    /**
     * Create a notification for all admins.
     */
    public function createForAllAdmins(string $type, array $data): void
    {
        $adminIds = $this->adminIds();

        if ($adminIds->isEmpty()) {
            return;
        }

        foreach ($adminIds as $adminId) {
            $this->createForAdmin($adminId, $type, $data);
        }
    }

    /**
     * Create a notification for a single admin.
     */
    public function createForAdmin(int $adminId, string $type, array $data): void
    {
        AdminNotification::create([
            'admin_id' => $adminId,
            'type' => $type,
            'data' => $data,
            'read_at' => null,
        ]);
    }

    /**
     * Get admin user ids.
     */
    private function adminIds(): Collection
    {
        return User::query()
            ->whereHas('roles', function ($query) {
                $query->where('id', EnumRole::ADMIN);
            })
            ->pluck('id');
    }
}
