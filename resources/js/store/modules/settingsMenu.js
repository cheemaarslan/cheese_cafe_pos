import axios from "axios";

export const settingsMenu = {
    namespaced: true,
    state: {
        lists: [],
        nav: [],
    },
    getters: {
        lists: function (state) {
            return state.lists;
        },
        nav: function (state) {
            return state.nav;
        },
    },
    actions: {
        // Full list (any status) for the "Settings Tabs" show/hide admin page.
        lists: function (context) {
            return new Promise((resolve, reject) => {
                axios.get("admin/setting/menu/settings-menu-list").then((res) => {
                    console.log("[SettingsMenu DEBUG] GET settings-menu-list ->", res.data.data);
                    context.commit("lists", res.data.data);
                    resolve(res);
                }).catch((err) => {
                    console.log("[SettingsMenu DEBUG] GET settings-menu-list FAILED ->", err?.response?.status, err?.response?.data);
                    reject(err);
                });
            });
        },
        // Visible-only list for rendering the actual Settings left-hand nav.
        nav: function (context) {
            return new Promise((resolve, reject) => {
                axios.get("admin/setting/menu/settings-menu").then((res) => {
                    console.log("[SettingsMenu DEBUG] GET settings-menu (nav) ->", res.data.data);
                    context.commit("nav", res.data.data);
                    resolve(res);
                }).catch((err) => {
                    console.log("[SettingsMenu DEBUG] GET settings-menu (nav) FAILED ->", err?.response?.status, err?.response?.data);
                    reject(err);
                });
            });
        },
        changeStatus: function (context, payload) {
            return new Promise((resolve, reject) => {
                console.log("[SettingsMenu DEBUG] POST change-status ->", payload);
                axios.post(`admin/setting/menu/change-status/${payload.id}`, payload.form).then((res) => {
                    console.log("[SettingsMenu DEBUG] change-status OK, status:", res.status, "- refetching list");
                    context.dispatch("lists").then().catch();
                    resolve(res);
                }).catch((err) => {
                    console.log("[SettingsMenu DEBUG] change-status FAILED ->", err?.response?.status, err?.response?.data);
                    reject(err);
                });
            });
        },
    },
    mutations: {
        lists: function (state, payload) {
            state.lists = payload;
        },
        nav: function (state, payload) {
            state.nav = payload;
        },
    },
};
