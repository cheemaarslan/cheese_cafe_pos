<template>
    <aside
        class="db-sidebar"
        :class="
            $route.path.includes('kitchen-display-system') ||
            $route.path.includes('order-status-screen')
                ? 'hidden'
                : ''
        "
    >
        <div class="db-sidebar-header">
            <div class="w-24 cursor-pointer" @click="redirectToDashboard">
                <img :src="setting.theme_logo" alt="logo" />
            </div>
            <button
                @click.prevent="handleSidebar"
                class="fa-solid fa-xmark xmark-btn close-db-menu"
            ></button>
        </div>
        <!--{{ menus }}-->
        <nav class="db-sidebar-nav">
            <div
                class="hidden lg:flex items-center justify-center mb-8 lg:mb-4 sticky top-[-16px] z-20 bg-white"
            >
                <div
                    class="w-[14rem] flex-shrink-0 cursor-pointer"
                    @click="redirectToDashboard"
                >
                    <img class="w-full" :src="setting.theme_logo" alt="logo" />
                </div>
            </div>
            <ul
                class="db-sidebar-nav-list"
                v-if="menus.length > 0"
                v-for="menu in menus"
                :key="menu"
            >
                <li
                    class="db-sidebar-nav-item"
                    v-if="menu.url === '#'"
                    @click.prevent="sidebarActive($event)"
                >
                    <a
                        href="javascript:void(0);"
                        class="db-sidebar-nav-title"
                        id="{{ menu.language }}"
                    >
                        {{ $t("menu." + menu.language) }}
                    </a>
                </li>

                <li
                    class="db-sidebar-nav-item"
                    v-else
                    @click.prevent="sidebarActive($event)"
                >
                    <router-link
                        :to="'/admin/' + menu.url"
                        class="db-sidebar-nav-menu"
                        :id="menu.language"
                    >
                        <i class="text-sm" :class="menu.icon"></i>
                        <span class="text-base flex-auto">{{
                            $t("menu." + menu.language)
                        }}</span>
                    </router-link>
                </li>

                <li
                    class="db-sidebar-nav-item"
                    v-if="menu.children"
                    v-for="children in menu.children"
                    @click.prevent="sidebarActive($event)"
                >
                    <router-link
                        :to="'/admin/' + children.url"
                        class="db-sidebar-nav-menu"
                    >
                        <i class="text-sm" :class="children.icon"></i>
                        <span class="text-base flex-auto">{{
                            $t("menu." + children.language)
                        }}</span>
                    </router-link>
                </li>
            </ul>
        </nav>
    </aside>
</template>

<script>
export default {
    name: "BackendMenuComponent",
    data: function () {
        return {
            activeParentId: 1,
            activeChildId: 0,
            sidebarOpen: false,
        };
    },
    computed: {
        setting: function () {
            return this.$store.getters["frontendSetting/lists"];
        },
        menus: function () {
            // Filter out specific menus (Dining Tables / Table Orders) before rendering
            const raw = this.$store.getters.authMenu || [];
            const excludeUrls = [];
            const excludeLanguages = [];

            // --- DEBUG: menu-toggle diagnostics -----------------------------------
            // authMenu is only populated once, at login (see store/modules/auth.js),
            // and it already comes from the server pre-filtered by MenuService::menu()
            // to exclude status = INACTIVE rows. So:
            //   - if a toggled-off item still appears here, either the toggle never
            //     persisted server-side, or this session logged in before the toggle
            //     happened (authMenu is stale until next login).
            //   - if it's already gone from this log, the backend + toggle are working
            //     and any remaining visual bug is purely in this component's own
            //     render/filter logic below.
            console.groupCollapsed(
                "[SidebarMenu DEBUG] authMenu snapshot @ " +
                    new Date().toLocaleTimeString(),
            );
            console.log("raw authMenu length:", raw.length);
            console.table(
                raw.flatMap((m) => {
                    if (!m) return [];
                    const rows = [
                        {
                            id: m.id,
                            name: m.name,
                            language: m.language,
                            url: m.url,
                            status: m.status,
                            parent: m.parent,
                            childCount: (m.children || []).length,
                        },
                    ];
                    (m.children || []).forEach((c) => {
                        if (!c) return;
                        rows.push({
                            id: c.id,
                            name: "  ↳ " + c.name,
                            language: c.language,
                            url: c.url,
                            status: c.status,
                            parent: c.parent,
                            childCount: 0,
                        });
                    });
                    return rows;
                }),
            );
            console.groupEnd();
            // --- END DEBUG ----------------------------------------------------------

            const filtered = raw
                .map((menu) => {
                    if (!menu) return null;
                    if (
                        excludeUrls.includes(menu.url) ||
                        excludeLanguages.includes(menu.language)
                    ) {
                        return null;
                    }

                    let copy = menu;
                    if (menu.children && Array.isArray(menu.children)) {
                        const kids = menu.children.filter((c) => {
                            if (!c) return false;
                            return (
                                !excludeUrls.includes(c.url) &&
                                !excludeLanguages.includes(c.language)
                            );
                        });
                        // return a shallow copy with filtered children (or no children if empty)
                        copy = Object.assign({}, menu);
                        if (kids.length) {
                            copy.children = kids;
                        } else {
                            delete copy.children;
                        }
                    }

                    if (
                        copy.url === "#" &&
                        (!copy.children || copy.children.length === 0)
                    ) {
                        return null;
                    }

                    return copy;
                })
                .filter(Boolean);

            console.log(
                "[SidebarMenu DEBUG] rendered menu count after component-level filter:",
                filtered.length,
            );

            return filtered;
        },
        sidebar() {
            return this.$store.getters["globalState/lists"].topSidebar;
        },
    },
    mounted() {
        this.defaultSidebarActive();
    },
    methods: {
        sidebarActive: function (e) {
            const activeMenu = document.querySelector(
                ".db-sidebar-nav-item.active",
            );
            if (activeMenu) {
                activeMenu.classList.remove("active");
            }
            e?.currentTarget?.classList?.add("active");
        },
        defaultSidebarActive: function () {
            if (
                document
                    ?.querySelector(".db-sidebar-nav-menu")
                    ?.classList?.contains("active")
            ) {
                document
                    ?.querySelector(".db-sidebar-nav-menu")
                    ?.parentElement?.classList?.add("active");
            } else {
                document
                    ?.querySelector(".router-link-exact-active")
                    ?.parentElement?.classList?.add("active");
            }
        },
        handleSidebar: function () {
            this.sidebarOpen = !this.sidebar;
            this.$store.dispatch("globalState/set", {
                topSidebar: this.sidebarOpen,
            });

            if (
                document
                    ?.querySelector(".db-sidebar")
                    ?.classList?.contains("active")
            ) {
                document
                    ?.querySelector(".db-main")
                    ?.classList?.remove("expand");
                document
                    ?.querySelector(".db-sidebar")
                    ?.classList?.remove("active");
            } else {
                document
                    ?.querySelector(".db-sidebar")
                    ?.classList?.add("active");
                document?.querySelector(".db-main")?.classList?.add("expand");
            }
        },
        closeFullScreen: function () {
            if (
                document.fullscreenElement ||
                document.webkitFullscreenElement
            ) {
                const elementDbCustomerMain =
                    document?.querySelector(".db-main-customer");
                const headerDiv = document?.querySelector(".db-header");
                elementDbCustomerMain?.classList.remove(
                    "db-main-customer",
                    "customer-display",
                );
                elementDbCustomerMain?.classList.add("db-main");
                elementDbCustomerMain?.classList.remove("hiddenHeader");
                headerDiv?.classList.remove("active", "hidden");
                document?.exitFullscreen();
            }
            document.removeEventListener("mousemove", handleMouseMove);
        },
        redirectToDashboard: function () {
            const dashboardMenu = document.getElementById("dashboard");
            if (dashboardMenu) {
                dashboardMenu.click();
            }
        },
    },
};
</script>
