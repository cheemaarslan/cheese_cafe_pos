import axios from "axios";
import appService from "../../services/appService";

export const inventory = {
    namespaced: true,
    state: {
        lists: [],
        page: {},
        pagination: [],
        show: {},
        transactions: [],
        transactionPage: {},
        transactionPagination: [],
        temp: {
            temp_id: null,
            isEditing: false,
        },
    },
    getters: {
        lists: function (state) {
            return state.lists;
        },
        pagination: function (state) {
            return state.pagination;
        },
        page: function (state) {
            return state.page;
        },
        show: function (state) {
            return state.show;
        },
        transactions: function (state) {
            return state.transactions;
        },
        transactionPage: function (state) {
            return state.transactionPage;
        },
        transactionPagination: function (state) {
            return state.transactionPagination;
        },
        temp: function (state) {
            return state.temp;
        },
    },
    actions: {
        lists: function (context, payload = {}) {
            return axios.get("admin/inventory" + appService.requestHandler(payload)).then((res) => {
                if (typeof payload.vuex === "undefined" || payload.vuex === true) {
                    context.commit("lists", res.data.data);
                    context.commit("page", res.data.meta);
                    context.commit("pagination", res.data);
                }
                return res;
            });
        },
        save: function (context, payload) {
            const url = context.state.temp.isEditing
                ? `/admin/inventory/${context.state.temp.temp_id}`
                : "/admin/inventory";

            return axios.post(url, payload.form).then((res) => {
                context.dispatch("lists", payload.search);
                context.commit("reset");
                return res;
            });
        },
        edit: function (context, payload) {
            context.commit("temp", payload);
        },
        destroy: function (context, payload) {
            return axios.delete(`admin/inventory/${payload.id}`).then((res) => {
                context.dispatch("lists", payload.search);
                return res;
            });
        },
        show: function (context, payload) {
            return axios.get(`admin/inventory/show/${payload}`).then((res) => {
                context.commit("show", res.data.data);
                return res;
            });
        },
        transactions: function (context, payload) {
            return axios.get(`admin/inventory/${payload.id}/transactions` + appService.requestHandler(payload.search)).then((res) => {
                context.commit("transactions", res.data);
                return res;
            });
        },
        reset: function (context) {
            context.commit("reset");
        },
    },
    mutations: {
        lists: function (state, payload) {
            state.lists = payload;
        },
        pagination: function (state, payload) {
            state.pagination = payload;
        },
        page: function (state, payload) {
            state.page = payload ? { from: payload.from, to: payload.to, total: payload.total } : {};
        },
        show: function (state, payload) {
            state.show = payload;
        },
        transactions: function (state, payload) {
            state.transactions = payload.data;
            state.transactionPagination = payload;
            state.transactionPage = payload.meta
                ? { from: payload.meta.from, to: payload.meta.to, total: payload.meta.total }
                : {};
        },
        temp: function (state, payload) {
            state.temp.temp_id = payload;
            state.temp.isEditing = true;
        },
        reset: function (state) {
            state.temp.temp_id = null;
            state.temp.isEditing = false;
        },
    },
};
