import axios from "axios";

export const sidebarMenu = {
    namespaced: true,
    state: {
        lists: [],
    },
    getters: {
        lists: function (state) {
            return state.lists;
        },
    },
    actions: {
        lists: function (context) {
            return new Promise((resolve, reject) => {
                axios
                    .get("admin/setting/menu")
                    .then((res) => {
                        console.log("[SidebarMenu DEBUG] GET admin/setting/menu ->", res.data.data);
                        context.commit("lists", res.data.data);
                        resolve(res);
                    })
                    .catch((err) => {
                        console.log("[SidebarMenu DEBUG] GET admin/setting/menu FAILED ->", err?.response?.status, err?.response?.data);
                        reject(err);
                    });
            });
        },
        changeStatus: function (context, payload) {
            return new Promise((resolve, reject) => {
                console.log("[SidebarMenu DEBUG] POST change-status ->", payload);
                axios
                    .post(
                        `admin/setting/menu/change-status/${payload.id}`,
                        payload.form,
                    )
                    .then((res) => {
                        console.log("[SidebarMenu DEBUG] change-status OK, status:", res.status, "- refetching list");
                        context.dispatch("lists").then().catch();
                        resolve(res);
                    })
                    .catch((err) => {
                        console.log("[SidebarMenu DEBUG] change-status FAILED ->", err?.response?.status, err?.response?.data);
                        reject(err);
                    });
            });
        },
    },
    mutations: {
        lists: function (state, payload) {
            state.lists = payload;
        },
    },
};
