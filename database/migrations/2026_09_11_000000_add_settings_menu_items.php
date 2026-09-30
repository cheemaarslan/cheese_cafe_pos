<?php

use App\Enums\MenuType;
use App\Enums\Status;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * The Settings section's own left-hand nav (Company, Site, Branches, ...) has always been a
     * hardcoded list in MenuComponent.vue. This seeds it into the `menus` table (type = SETTINGS,
     * kept apart from the main sidebar's BACKEND rows) so it can be shown/hidden the same way the
     * main sidebar can, from the new "Settings Tabs" admin page.
     *
     * Items that were already commented out in MenuComponent.vue are seeded INACTIVE so the
     * visible nav is unchanged after this migration; an admin can opt to re-enable them.
     */
    public function up(): void
    {
        $now = now();

        $items = [
            ['name' => 'Company', 'language' => 'company', 'route' => 'admin.settings.company', 'icon' => 'lab lab-company', 'status' => Status::ACTIVE],
            ['name' => 'Site', 'language' => 'site', 'route' => 'admin.settings.site', 'icon' => 'lab lab-site', 'status' => Status::ACTIVE],
            ['name' => 'Branches', 'language' => 'branches', 'route' => 'admin.settings.branch', 'icon' => 'lab lab-branches', 'status' => Status::ACTIVE],
            ['name' => 'Kiosk Machines', 'language' => 'kiosk_machines', 'route' => 'admin.settings.kioskMachines', 'icon' => 'lab lab-kiosk', 'status' => Status::INACTIVE],
            ['name' => 'Mail', 'language' => 'mail', 'route' => 'admin.settings.mail', 'icon' => 'lab lab-mail', 'status' => Status::INACTIVE],
            ['name' => 'Order Setup', 'language' => 'order_setup', 'route' => 'admin.settings.orderSetup', 'icon' => 'lab lab-order-setup', 'status' => Status::INACTIVE],
            ['name' => 'Otp', 'language' => 'otp', 'route' => 'admin.settings.otp', 'icon' => 'lab lab-otp', 'status' => Status::INACTIVE],
            ['name' => 'Notification', 'language' => 'notification', 'route' => 'admin.settings.notification', 'icon' => 'lab lab-notification', 'status' => Status::ACTIVE],
            ['name' => 'Notification Alert', 'language' => 'notification_alert', 'route' => 'admin.settings.notificationAlert', 'icon' => 'lab lab-license', 'status' => Status::ACTIVE],
            ['name' => 'Loyalty Points', 'language' => 'loyalty_points', 'route' => 'admin.settings.loyaltyPoints', 'icon' => 'fa-solid fa-award', 'status' => Status::ACTIVE],
            ['name' => 'Google Auth', 'language' => 'google_auth', 'route' => 'admin.settings.googleAuth', 'icon' => 'fa-brands fa-google', 'status' => Status::ACTIVE],
            ['name' => 'Google Recaptcha', 'language' => 'google_recaptcha', 'route' => 'admin.settings.googleRecaptcha', 'icon' => 'fa-brands fa-google', 'status' => Status::ACTIVE],
            ['name' => 'Social Media', 'language' => 'social_media', 'route' => 'admin.settings.socialMedia', 'icon' => 'lab lab-social-media', 'status' => Status::INACTIVE],
            ['name' => 'Cookies', 'language' => 'cookies', 'route' => 'admin.settings.cookies', 'icon' => 'lab lab-cookies', 'status' => Status::INACTIVE],
            ['name' => 'Analytics', 'language' => 'analytics', 'route' => 'admin.settings.analytic', 'icon' => 'lab lab-analytics', 'status' => Status::INACTIVE],
            ['name' => 'Theme', 'language' => 'theme', 'route' => 'admin.settings.theme', 'icon' => 'lab lab-theme', 'status' => Status::ACTIVE],
            ['name' => 'Time Slots', 'language' => 'time_slots', 'route' => 'admin.settings.timeSlot', 'icon' => 'lab lab-time-slots', 'status' => Status::ACTIVE],
            ['name' => 'Sliders', 'language' => 'sliders', 'route' => 'admin.settings.slider', 'icon' => 'lab lab-sliders', 'status' => Status::ACTIVE],
            ['name' => 'Currencies', 'language' => 'currencies', 'route' => 'admin.settings.currency', 'icon' => 'lab lab-currencies', 'status' => Status::INACTIVE],
            ['name' => 'Item Categories', 'language' => 'item_categories', 'route' => 'admin.settings.itemCategory', 'icon' => 'lab lab-item-categories', 'status' => Status::ACTIVE],
            ['name' => 'Item Attributes', 'language' => 'item_attributes', 'route' => 'admin.settings.itemAttribute', 'icon' => 'lab lab-item-attributes', 'status' => Status::ACTIVE],
            ['name' => 'Taxes', 'language' => 'taxes', 'route' => 'admin.settings.tax', 'icon' => 'lab lab-taxes', 'status' => Status::INACTIVE],
            ['name' => 'Pages', 'language' => 'pages', 'route' => 'admin.settings.page', 'icon' => 'lab lab-pages', 'status' => Status::ACTIVE],
            ['name' => 'Blogs', 'language' => 'blogs', 'route' => 'admin.settings.blogs', 'icon' => 'fa-solid fa-blog', 'status' => Status::ACTIVE],
            ['name' => 'Role Permissions', 'language' => 'role_permissions', 'route' => 'admin.settings.role', 'icon' => 'lab lab-role-permissions', 'status' => Status::ACTIVE],
            ['name' => 'Languages', 'language' => 'languages', 'route' => 'admin.settings.language', 'icon' => 'lab lab-languages', 'status' => Status::INACTIVE],
            ['name' => 'Sms Gateway', 'language' => 'sms_gateway', 'route' => 'admin.settings.smsGateway', 'icon' => 'lab lab-sms', 'status' => Status::ACTIVE],
            ['name' => 'Payment Gateway', 'language' => 'payment_gateway', 'route' => 'admin.settings.paymentGateway', 'icon' => 'lab lab-payment-gateway', 'status' => Status::ACTIVE],
            ['name' => 'License', 'language' => 'license', 'route' => 'admin.settings.license', 'icon' => 'lab lab-license', 'status' => Status::INACTIVE],
            ['name' => 'Sidebar Menu', 'language' => 'sidebar_menu', 'route' => 'admin.settings.sidebarMenu', 'icon' => 'fa-solid fa-bars', 'status' => Status::ACTIVE],
            ['name' => 'Settings Tabs', 'language' => 'settings_tabs', 'route' => 'admin.settings.settingsMenuItems', 'icon' => 'fa-solid fa-sliders', 'status' => Status::ACTIVE],
        ];

        foreach ($items as $item) {
            if (DB::table('menus')->where('type', MenuType::SETTINGS)->where('language', $item['language'])->exists()) {
                continue;
            }

            DB::table('menus')->insert([
                'name'       => $item['name'],
                'language'   => $item['language'],
                'url'        => $item['route'],
                'icon'       => $item['icon'],
                'status'     => $item['status'],
                'parent'     => 0,
                'type'       => MenuType::SETTINGS,
                'priority'   => 100,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    public function down(): void
    {
        DB::table('menus')->where('type', MenuType::SETTINGS)->delete();
    }
};
