import axios from "axios";
import appService from "../../services/appService";

export const profitLoss = {
    namespaced: true,
    state: {
        report: {},
    },
    getters: {
        report: function (state) {
            return state.report;
        },
    },
    actions: {
        report: function (context, payload = {}) {
            return axios.get("admin/profit-loss" + appService.requestHandler(payload)).then((res) => {
                context.commit("report", res.data.data);
                return res;
            });
        },
        export: function (context, payload = {}) {
            return axios.get("admin/profit-loss/export" + appService.requestHandler(payload), { responseType: "blob" });
        },
        pdf: function (context, payload = {}) {
            return axios.get("admin/profit-loss/pdf" + appService.requestHandler(payload), { responseType: "blob" });
        },
        // Removes a wrongly entered stock purchase from the expenses (stock quantity is not touched).
        destroyPurchase: function (context, payload) {
            return axios.delete(`admin/profit-loss/inventory-purchase/${payload.id}`).then((res) => {
                context.dispatch("report", payload.search);
                return res;
            });
        },
    },
    mutations: {
        report: function (state, payload) {
            state.report = payload;
        },
    },
};
