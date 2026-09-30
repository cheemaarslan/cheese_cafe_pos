import axios from "axios";

export const frontendBlog = {
    namespaced: true,
    state: {
        lists: [],
        page: {},
        pagination: [],
        show: {},
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
    },
    actions: {
        lists: function (context, payload) {
            return new Promise((resolve, reject) => {
                let url = "frontend/blog";
                // console.log('frontendBlog store: Fetching from URL:', url);
                // console.log('frontendBlog store: Payload:', payload);
                
                // Only add a page query if an explicit page value is provided
                if (payload && typeof payload.page !== 'undefined') {
                    url = url + "?page=" + payload.page;
                }

                // Avoid sending internal flags like `vuex` to the backend
                const params = payload ? Object.assign({}, payload) : {};
                if (params && typeof params.vuex !== 'undefined') {
                    delete params.vuex;
                }

                // console.log('frontendBlog store: Final params:', params);

                axios.get(url, { params }).then((res) => {
                    // console.log('frontendBlog store: Response received:', res);
                    // console.log('frontendBlog store: Response data:', res.data);
                    
                    if (!payload || typeof payload.vuex === "undefined" || payload.vuex === true) {
                        context.commit("lists", res.data.data);
                        context.commit("page", res.data.meta);
                        context.commit("pagination", res.data);
                    }
                    resolve(res);
                }).catch((err) => {
                    console.error('frontendBlog store: Error:', err);
                    console.error('frontendBlog store: Error response:', err.response);
                    reject(err);
                });
            });
        },
        show: function (context, payload) {
            return new Promise((resolve, reject) => {
                axios.get(`frontend/blog/show/${payload}`).then((res) => {
                    context.commit("show", res.data.data);
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                });
            });
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
            state.page = payload;
        },
        show: function (state, payload) {
            state.show = payload;
        },
    },
};
