import axios from 'axios';
import appService from '../../services/appService';

export const inventory = {
    namespaced: true,
    state: {
        lists: [],
        page: {},
        pagination: [],
        show: {},
        temp: {
            temp_id: null,
            isEditing: false,
        },
    },
    getters: {
        lists: state => state.lists,
        pagination: state => state.pagination,
        page: state => state.page,
        show: state => state.show,
        temp: state => state.temp,
    },
    actions: {
        lists(context, payload = {}) {
            return axios.get('admin/inventory' + appService.requestHandler(payload)).then(res => {
                if (typeof payload.vuex === 'undefined' || payload.vuex === true) {
                    context.commit('lists', res.data.data);
                    context.commit('page', res.data.meta);
                    context.commit('pagination', res.data);
                }
                return res;
            });
        },
        save(context, payload) {
            const editing = context.state.temp.isEditing;
            const url = editing
                ? `/admin/inventory/${context.state.temp.temp_id}`
                : '/admin/inventory';

            return axios.post(url, payload.form).then(res => {
                context.dispatch('lists', payload.search);
                context.commit('reset');
                return res;
            });
        },
        edit(context, id) {
            context.commit('temp', id);
        },
        destroy(context, payload) {
            return axios.delete(`admin/inventory/${payload.id}`).then(res => {
                context.dispatch('lists', payload.search);
                return res;
            });
        },
        show(context, id) {
            return axios.get(`admin/inventory/show/${id}`).then(res => {
                context.commit('show', res.data.data);
                return res;
            });
        },
        reset(context) {
            context.commit('reset');
        },
    },
    mutations: {
        lists(state, payload) {
            state.lists = payload;
        },
        pagination(state, payload) {
            state.pagination = payload;
        },
        page(state, payload) {
            state.page = payload
                ? { from: payload.from, to: payload.to, total: payload.total }
                : {};
        },
        show(state, payload) {
            state.show = payload;
        },
        temp(state, id) {
            state.temp.temp_id = id;
            state.temp.isEditing = true;
        },
        reset(state) {
            state.temp.temp_id = null;
            state.temp.isEditing = false;
        },
    },
};
