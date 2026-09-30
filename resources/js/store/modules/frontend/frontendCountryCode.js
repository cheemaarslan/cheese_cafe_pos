import axios from "axios";

export const frontendCountryCode = {
    namespaced: true,
    state: {
        show: [],
        phoneCodes: [],
    },

    getters: {
        show: function (state) {
            return state.show;
        },
        phoneCodes: function (state) {
            return state.phoneCodes;
        },
    },

    actions: {
        show: function (context,payload) {
            return new Promise((resolve, reject) => {
                let url = `frontend/country-code/show/${payload}`;
                axios
                    .get(url)
                    .then((res) => {
                        context.commit("show", res.data.data);
                        resolve(res);
                    })
                    .catch((err) => {
                        reject(err);
                    });
            });
        },
        phoneCodes: function (context) {
            return new Promise((resolve, reject) => {
                let url = `frontend/country-code/phone-codes`;
                axios
                    .get(url)
                    .then((res) => {
                        context.commit("phoneCodes", res.data.data);
                        resolve(res);
                    })
                    .catch((err) => {
                        reject(err);
                    });
            });
        },
    },

    mutations: {
        show: function (state, payload) {
            state.show = payload;
        },
        phoneCodes: function (state, payload) {
            state.phoneCodes = payload;
        },
    },
};
