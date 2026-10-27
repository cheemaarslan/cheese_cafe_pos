import axios from "axios";
import appService from "../../services/appService";

export const inventoryWastage = {
    namespaced: true,
    state: {
        lists: [],
        page: {},
        pagination: [],
        // Active inventory items to choose from
        items: [],
    },
    getters: {
        lists: function (state) {
            return state.lists;
        },
        page: function (state) {
            return state.page;
        },
        pagination: function (state) {
            return state.pagination;
        },
        items: function (state) {
            return state.items;
        },
    },
    actions: {
        lists: function (context, payload = {}) {
            return axios.get("admin/inventory-wastage" + appService.requestHandler(payload)).then((res) => {
                context.commit("lists", res.data);
                return res;
            });
        },
        items: function (context) {
            return axios.get("admin/inventory-wastage/items").then((res) => {
                context.commit("items", res.data.data);
                return res;
            });
        },
        save: function (context, payload) {
            return axios.post("/admin/inventory-wastage", payload.form).then((res) => {
                context.dispatch("lists", payload.search);
                context.dispatch("items");
                return res;
            });
        },
        destroy: function (context, payload) {
            return axios.delete(`admin/inventory-wastage/${payload.id}`).then((res) => {
                context.dispatch("lists", payload.search);
                context.dispatch("items");
                return res;
            });
        },
    },
    mutations: {
        lists: function (state, payload) {
            state.lists = payload.data;
            state.pagination = payload;
            state.page = payload.meta ? { from: payload.meta.from, to: payload.meta.to, total: payload.meta.total } : {};
        },
        items: function (state, payload) {
            state.items = payload;
        },
    },
};
