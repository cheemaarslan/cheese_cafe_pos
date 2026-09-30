/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */
import {createApp} from 'vue';
import DefaultComponent from "./components/DefaultComponent";
import router from './router';
import store from './store';
import axios from 'axios';
import i18n from "./i18n";
import Toast from "vue-toastification";
import "vue-toastification/dist/index.css";
import VueSimpleAlert from "vue3-simple-alert";
import VueNextSelect from 'vue-next-select';
import 'vue-next-select/dist/index.css';
import VueApexCharts from "vue3-apexcharts";
import ENV from './config/env';


/* Start tooltip alert code */
const options = {
    timeout: 2000,
    closeOnClick: true,
    pauseOnFocusLoss: true,
    pauseOnHover: true,
    draggable: true,
    draggablePercent: 0.6,
    showCloseButtonOnHover: false,
    hideProgressBar: false,
    closeButton: "button",
    icon: true,
    rtl: false
};
/* End tooltip alert code */


/* Start axios code*/
const API_URL = ENV.API_URL.replace(/\/+$/, '');
const API_KEY = ENV.API_KEY;

axios.defaults.baseURL = `${API_URL}/api`;
axios.interceptors.request.use(
    config => {
        config.headers['x-api-key'] = API_KEY;
        if (localStorage.getItem('vuex')) {
            const vuex = JSON.parse(localStorage.getItem('vuex'));
            const token = vuex.auth.authToken;
            const language = vuex.globalState.lists.language_code;
            config.headers['Authorization'] = token ? `Bearer ${token}` : '';
            config.headers['x-localization'] = language;
        }
        return config;
    },
    error => Promise.reject(error),
);
/* End axios code */

const app = createApp({});
app.component('default-component', DefaultComponent);
app.component('vue-select', VueNextSelect)
app.use(router)
app.use(store)
app.use(VueSimpleAlert)
app.use(VueApexCharts)
app.use(Toast, options)
app.use(i18n)
// Fire critical data fetches immediately — parallel, non-blocking
store.dispatch("frontendSlider/lists", { paginate: 0, order_column: 'id', order_type: 'desc', status: 5 });
store.dispatch("frontendPage/lists", { paginate: 0, order_column: 'id', order_type: 'asc', menu_section_id: 2, status: 5 });
store.dispatch("frontendFooterData/lists").catch(() => {});

// Wait for the router's first navigation to resolve before mounting. Without
// this, the app paints once with its default (storefront) layout and then
// swaps to the real one a moment later — visible as a flash of the frontend
// theme on pages like /admin/login. router.isReady() only waits on our own
// (synchronous) guards, so this adds no real delay.
router.isReady().then(() => {
    app.mount('#app');
});
