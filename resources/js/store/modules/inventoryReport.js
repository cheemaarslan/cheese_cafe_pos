import axios from "axios";
import appService from "../../services/appService";

export const inventoryReport = {
    namespaced: true,
    state: {
        report: {},
        daily: {},
    },
    getters: {
        report: function (state) {
            return state.report;
        },
        daily: function (state) {
            return state.daily;
        },
    },
    actions: {
        report: function (context, payload = {}) {
            return axios.get("admin/inventory-report" + appService.requestHandler(payload)).then((res) => {
                context.commit("report", res.data.data);
                return res;
            });
        },
        daily: function (context, payload) {
            return axios.get("admin/inventory-report/daily/" + payload.id + appService.requestHandler(payload.search)).then((res) => {
                context.commit("daily", res.data.data);
                return res;
            });
        },
    },
    mutations: {
        report: function (state, payload) {
            state.report = payload;
        },
        daily: function (state, payload) {
            state.daily = payload;
        },
    },
};
