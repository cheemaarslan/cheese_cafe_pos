import axios from "axios";
import appService from "../../services/appService";

export const stockCount = {
    namespaced: true,
    state: {
        lists: [],
        page: {},
        pagination: [],
        show: {},
        // Active inventory items with their current stock, to count
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
        show: function (state) {
            return state.show;
        },
        items: function (state) {
            return state.items;
        },
    },
    actions: {
        lists: function (context, payload = {}) {
            return axios.get("admin/stock-counts" + appService.requestHandler(payload)).then((res) => {
                context.commit("lists", res.data);
                return res;
            });
        },
        items: function (context) {
            return axios.get("admin/stock-counts/items").then((res) => {
                context.commit("items", res.data.data);
                return res;
            });
        },
        show: function (context, payload) {
            return axios.get(`admin/stock-counts/show/${payload}`).then((res) => {
                context.commit("show", res.data.data);
                return res;
            });
        },
        save: function (context, payload) {
            return axios.post("/admin/stock-counts", payload).then((res) => {
                context.commit("show", res.data.data);
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
        show: function (state, payload) {
            state.show = payload;
        },
        items: function (state, payload) {
            state.items = payload;
        },
    },
};
