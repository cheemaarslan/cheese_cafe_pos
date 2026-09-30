<template>
    <button
        @click="openSettingMenu($event)"
        type="button"
        class="settings-btn w-full md:hidden flex items-center justify-center gap-2 p-2 rounded bg-primary text-white"
    >
        <span class="capitalize">{{ $t("menu.settings_menu") }}</span>
        <i class="icon fa-solid fa-chevron-down text-sm"></i>
    </button>
    <div
        class="h-0 overflow-hidden md:h-auto md:overflow-auto transition-all duration-300"
    >
        <nav class="db-card p-3">
            <router-link
                v-for="menu in menus"
                :key="menu.id"
                :to="{ name: menu.url }"
                class="db-tab-btn"
            >
                <i class="text-sm" :class="menu.icon"></i>
                {{ $t("menu." + menu.language) }}
            </router-link>
        </nav>
    </div>
</template>

<script>
import appService from "../../../services/appService";
export default {
    name: "MenuComponent",
    mounted() {
        this.$store.dispatch("settingsMenu/nav").then().catch();
    },
    computed: {
        menus: function () {
            return this.$store.getters["settingsMenu/nav"];
        },
    },
    methods: {
        openSettingMenu: function (event) {
            return appService.openSettingMenu(event);
        },
    },
};
</script>
