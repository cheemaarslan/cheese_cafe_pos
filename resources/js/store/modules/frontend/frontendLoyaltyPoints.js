import axios from 'axios'
import appService from "../../../services/appService";

export const frontendLoyaltyPoints = {
    namespaced: true,
    state: {
        loyaltyPoints: [],
        customerLoyaltyPoints: {},
        totalPoints: 0,
        activePoints: 0,
        expiredPoints: 0,
        customerType: '',
        pagination: [],
        page: {},
    },

    getters: {
        loyaltyPoints: function (state) {
            return state.loyaltyPoints;
        },
        customerLoyaltyPoints: function (state) {
            return state.customerLoyaltyPoints;
        },
        totalPoints: function (state) {
            return state.totalPoints;
        },
        activePoints: function (state) {
            return state.activePoints;
        },
        expiredPoints: function (state) {
            return state.expiredPoints;
        },
        customerType: function (state) {
            return state.customerType;
        },
        pagination: function (state) {
            return state.pagination;
        },
        page: function (state) {
            return state.page;
        },
    },

    actions: {
        // Get all loyalty points for the authenticated customer
        getCustomerLoyaltyPoints: function (context, payload = {}) {
            return new Promise((resolve, reject) => {
                let url = 'frontend/loyalty-points';
                if (payload) {
                    url = url + appService.requestHandler(payload);
                }
                axios.get(url).then((res) => {
                    console.log('API Response:', res.data);
                    if (typeof payload.vuex === "undefined" || payload.vuex === true) {
                        // Extract the loyalty points array from the paginated response
                        const loyaltyPointsData = res.data.data.data || [];
                        // console.log('Loyalty Points Data:', loyaltyPointsData);
                        // context.commit('loyaltyPoints', loyaltyPointsData);
                        // context.commit('totalPoints', parseInt(res.data.total_points) || 0);
                        // context.commit('activePoints', parseInt(res.data.active_points) || 0);
                        // context.commit('expiredPoints', parseInt(res.data.expired_points) || 0);
                        
                        // Handle pagination
                        if (res.data.data) {
                            context.commit("page", {
                                from: res.data.data.from,
                                to: res.data.data.to,
                                total: res.data.data.total,
                            });
                            context.commit("pagination", res.data.data);
                        }
                    }
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                });
            });
        },

        // Get specific loyalty point details by ID
        show: function (context, payload) {
            return new Promise((resolve, reject) => {
                axios.get(`frontend/loyalty-points/show/${payload}`).then((res) => {
                    context.commit("customerLoyaltyPoints", res.data.data);
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                });
            });
        },

        // Get loyalty points summary for the customer
        getSummary: function (context, payload = {}) {
            return new Promise((resolve, reject) => {
                let url = 'frontend/loyalty-points/summary';
                if (payload) {
                    url = url + appService.requestHandler(payload);
                }
                axios.get(url).then((res) => {
                    if (typeof payload.vuex === "undefined" || payload.vuex === true) {
                        context.commit('totalPoints', res.data.total_points || 0);
                        context.commit('activePoints', res.data.active_points || 0);
                        context.commit('expiredPoints', res.data.expired_points || 0);
                        context.commit('customerType', res.data.customer_type || 0);
                    }
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                });
            });
        },
    },

    mutations: {
        loyaltyPoints: function (state, payload) {
            state.loyaltyPoints = payload;
        },
        customerLoyaltyPoints: function (state, payload) {
            state.customerLoyaltyPoints = payload;
        },
        totalPoints: function (state, payload) {
            state.totalPoints = payload;
        },
        activePoints: function (state, payload) {
            state.activePoints = payload;
        },
        expiredPoints: function (state, payload) {
            state.expiredPoints = payload;
        },
         customerType: function (state, payload) {
            state.customerType = payload;
        },
        pagination: function (state, payload) {
            state.pagination = payload;
        },
        page: function (state, payload) {
            if (typeof payload !== "undefined" && payload !== null) {
                state.page = {
                    from: payload.from,
                    to: payload.to,
                    total: payload.total,
                };
            }
        },
    },
}