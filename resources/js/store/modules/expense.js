import axios from "axios";
import appService from "../../services/appService";

export const expense = {
    namespaced: true,
    state: {
        lists: [],
        page: {},
        pagination: [],
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
        temp: function (state) {
            return state.temp;
        },
    },
    actions: {
        lists: function (context, payload = {}) {
            return axios.get("admin/expense" + appService.requestHandler(payload)).then((res) => {
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
                ? `/admin/expense/${context.state.temp.temp_id}`
                : "/admin/expense";

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
            return axios.delete(`admin/expense/${payload.id}`).then((res) => {
                context.dispatch("lists", payload.search);
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
