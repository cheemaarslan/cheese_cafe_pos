import axios from "axios";

export const itemIngredient = {
    namespaced: true,
    state: {
        lists: [],
        // The item, its variations and extras (parts of the recipe) and the inventory items usable as ingredients
        options: {
            item: {},
            variations: [],
            extras: [],
            inventory_items: [],
        },
        temp: {
            temp_id: null,
            isEditing: false,
        },
    },
    getters: {
        lists: function (state) {
            return state.lists;
        },
        options: function (state) {
            return state.options;
        },
        temp: function (state) {
            return state.temp;
        },
    },
    actions: {
        lists: function (context, payload) {
            return axios.get(`admin/item/recipe/${payload.id}`).then((res) => {
                context.commit("lists", res.data.data);
                context.commit("options", res.data);
                return res;
            });
        },
        save: function (context, payload) {
            const method = context.state.temp.isEditing ? axios.put : axios.post;
            const url = context.state.temp.isEditing
                ? `/admin/item/recipe/${payload.id}/${context.state.temp.temp_id}`
                : `/admin/item/recipe/${payload.id}`;

            return method(url, payload.form).then((res) => {
                context.dispatch("lists", { id: payload.id });
                context.commit("reset");
                return res;
            });
        },
        edit: function (context, payload) {
            context.commit("temp", payload);
        },
        destroy: function (context, payload) {
            return axios.delete(`admin/item/recipe/${payload.item}/${payload.id}`).then((res) => {
                context.dispatch("lists", { id: payload.item });
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
        options: function (state, payload) {
            state.options = {
                item: payload.item || {},
                variations: payload.variations || [],
                extras: payload.extras || [],
                inventory_items: payload.inventory_items || [],
            };
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
