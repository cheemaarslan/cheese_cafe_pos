<template>
    <div :dir="direction">
        <div v-if="theme === 'frontend'">
            <FrontendNavbarComponent
                v-if="$route.name !== 'auth.admin.login'"
            />
            <FrontendCartComponent />
            <main style="min-height: 100vh;">
                <router-view></router-view>
            </main>
            <FrontendMobileNavBarComponent />
            <FrontendMobileAccountComponent />
            <FrontendCookiesComponent />
            <FrontendFooterComponent
                v-if="$route.name !== 'auth.admin.login'"
            />
        </div>

        <div v-if="theme === 'backend'">
            <main class="db-main" v-if="logged">
                <BackendNavbarComponent />
                <BackendMenuComponent />
                <router-view></router-view>
            </main>

            <div v-if="!logged">
                <router-view></router-view>
            </div>
        </div>

        <div v-if="theme === 'table'">
            <TableNavbarComponent />
            <TableCartComponent />
            <main>
                <router-view></router-view>
            </main>
            <TableFooterComponent />
        </div>
    </div>
</template>

<script>
import BackendNavbarComponent from "./layouts/backend/BackendNavbarComponent";
import BackendMenuComponent from "./layouts/backend/BackendMenuComponent";
import FrontendNavbarComponent from "./layouts/frontend/FrontendNavBarComponent";
import FrontendFooterComponent from "./layouts/frontend/FrontendFooterComponent";
import FrontendMobileNavBarComponent from "./layouts/frontend/FrontendMobileNavBarComponent";
import FrontendMobileAccountComponent from "./layouts/frontend/FrontendMobileAccountComponent";
import FrontendCartComponent from "./layouts/frontend/FrontendCartComponent";
import FrontendCookiesComponent from "./layouts/frontend/FrontendCookiesComponent";
import TableNavbarComponent from "./layouts/table/TableNavBarComponent.vue";
import TableFooterComponent from "./layouts/table/TableFooterComponent.vue";
import TableCartComponent from "./layouts/table/TableCartComponent.vue";
import displayModeEnum from "../enums/modules/displayModeEnum";
import env from "../config/env";

export default {
    name: "DefaultComponent",
    components: {
        TableCartComponent,
        TableFooterComponent,
        TableNavbarComponent,
        FrontendCartComponent,
        FrontendMobileAccountComponent,
        FrontendMobileNavBarComponent,
        FrontendCookiesComponent,
        FrontendFooterComponent,
        FrontendNavbarComponent,
        BackendNavbarComponent,
        BackendMenuComponent,
    },
    data() {
        return {
            // Derive the initial theme from the resolved route instead of
            // hardcoding "frontend" — the $route watcher below only fires on
            // a route *change*, so a hardcoded default would still flash the
            // storefront layout for one frame on every fresh page load.
            theme: this.themeForRoute(this.$route),
        };
    },
    computed: {
        direction: function () {
            return this.$store.getters["frontendLanguage/show"].display_mode ===
                displayModeEnum.RTL
                ? "rtl"
                : "ltr";
        },
        logged: function () {
            return this.$store.getters.authStatus;
        },
    },
    beforeMount() {
        this.$store
            .dispatch("frontendSetting/lists")
            .then((res) => {
                this.$store.dispatch("globalState/init", {
                    branch_id: res.data.data.site_default_branch,
                    language_id: res.data.data.site_default_language,
                });
            })
            .catch();

        // Pre-fetch the (admin-configurable) Settings tabs nav so it's
        // already loaded by the time the "Settings" menu link is clicked —
        // its route redirect picks the first tab from this list.
        if (this.logged) {
            this.$store.dispatch("settingsMenu/nav").catch();
        }

        if (
            env.DEMO === "true" ||
            env.DEMO === "TRUE" ||
            env.DEMO === true ||
            env.DEMO === "1" ||
            env.DEMO === 1
        ) {
            this.$store
                .dispatch("authcheck")
                .then((res) => {
                    if (
                        res.data.status === false &&
                        (this.theme == "frontend" || this.theme == "backend")
                    ) {
                        this.$router.push({ name: "frontend.home" });
                    }
                })
                .catch();
        }
    },
    methods: {
        themeForRoute(route) {
            if (route.meta.isFrontend === true) {
                return "frontend";
            } else if (route.meta.isTable === true) {
                return "table";
            } else {
                return "backend";
            }
        },
    },
    watch: {
        $route(e) {
            this.theme = this.themeForRoute(e);
        },
    },
};
</script>
