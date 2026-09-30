<template>
    <LoadingComponent :props="loading" />

    <!-- ✅ Header: Premium Dual-Tier Layout -->
    <header
        class="sticky top-0 z-40 bg-white/95 backdrop-blur-md border-b border-gray-100 shadow-[0_2px_15px_-3px_rgba(0,0,0,0.07),0_10px_20px_-2px_rgba(0,0,0,0.04)] transition-all duration-300"
        ref="ffHeader">
        <div class="container mx-auto">

            <!-- ✅ Mobile Layout (Simple & Clean) -->
            <div class="w-full flex items-center justify-between lg:hidden py-3">
                <router-link :to="{ name: 'frontend.home' }" class="flex-shrink-0">
                    <img class="h-16 w-auto object-contain" :src="setting.theme_logo" alt="logo">
                </router-link>

                <button type="button" @click="showMobileSearch = true"
                    class="flex items-center justify-center w-10 h-10 rounded-full bg-gray-50 text-heading border border-gray-200 hover:bg-primary hover:text-white hover:border-primary transition-all duration-300 shadow-sm">
                    <i class="lab lab-search-normal text-xl"></i>
                </button>
            </div>

            <!-- ✅ Desktop Layout (Split: Logo Left | Actions Top, Nav Bottom) -->
            <div class="hidden lg:flex items-center justify-between py-4 gap-8">

                <!-- 1. Logo Section (Tall & Prominent) -->
                <div class="flex-shrink-0 relative z-20">
                    <router-link :to="{ name: 'frontend.home' }" class="block group">
                        <!-- 6rem Height as requested -->
                        <img class="h-[6rem] w-auto object-contain transition-transform duration-500 group-hover:scale-[1.02]"
                            alt="logo" :src="setting.theme_logo">
                    </router-link>
                </div>

                <!-- 2. Right Side Column -->
                <!-- UPDATED: Changed items-end to items-center to center the nav menu -->
                <div class="flex flex-col items-center justify-between h-[6rem] w-full max-w-5xl">

                    <!-- Top Row: Search, Language, Cart, Profile -->
                    <div class="w-full flex items-center justify-end gap-4 pb-3 mb-1">

                        <!-- Search Bar (Sleek Pill) -->
                        <div class="relative w-full max-w-md mr-auto">
                            <form @submit.prevent="search"
                                class="group flex items-center gap-2 px-4 h-10 rounded-full bg-gray-100/50 border border-gray-200 transition-all duration-300 hover:bg-white hover:shadow-md focus-within:bg-white focus-within:ring-2 focus-within:ring-primary/20 focus-within:border-primary">
                                <button type="submit"
                                    class="text-gray-400 group-focus-within:text-primary transition-colors">
                                    <i class="lab lab-search-normal text-lg"></i>
                                </button>
                                <input type="search" v-model="searchItem" :placeholder="$t('button.search')"
                                    @input="handleSearchInput" @focus="showSearchResults = true"
                                    @blur="hideSearchResults"
                                    class="w-full bg-transparent text-sm font-medium text-heading placeholder:text-gray-400 focus:outline-none">
                                <button v-if="searchItem" type="button" @click.prevent="searchReset"
                                    class="text-gray-400 hover:text-red-500 transition-colors">
                                    <i class="lab lab-close-circle-line text-lg"></i>
                                </button>
                            </form>

                            <!-- Search Dropdown -->
                            <div v-if="showSearchResults && (searchResults.length > 0 || searchLoading)"
                                class="absolute top-full left-0 mt-2 bg-white border border-gray-100 rounded-2xl shadow-xl z-50 max-h-96 overflow-y-auto w-full ring-1 ring-black/5">
                                <div v-if="searchLoading" class="p-4 text-center">
                                    <i class="lab lab-loading animate-spin text-primary"></i>
                                </div>
                                <div v-else-if="searchResults.length > 0" class="py-2">
                                    <div v-for="item in searchResults" :key="item.id"
                                        class="flex items-center gap-3 p-2.5 mx-2 rounded-xl hover:bg-gray-50 cursor-pointer transition-colors"
                                        @mousedown.prevent="selectSearchItem(item)">
                                        <img :src="item.thumb" :alt="item.name"
                                            class="w-10 h-10 object-cover rounded-lg border border-gray-100">
                                        <div class="flex-1 min-w-0">
                                            <h4 class="text-xs font-bold text-heading truncate">{{ item.name }}</h4>
                                            <div class="flex items-center gap-2">
                                                <span class="text-xs font-bold text-primary">{{ (item.offer &&
                                                    item.offer.length > 0) ? item.offer[0].currency_price :
                                                    item.currency_price }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div v-else class="p-4 text-center text-xs text-gray-500">{{
                                    $t('message.no_items_found') }}</div>
                            </div>
                        </div>

                        <!-- Language -->
                        <div v-if="setting.site_language_switch === enums.activityEnum.ENABLE"
                            class="relative dropdown-group">
                            <button
                                class="flex items-center gap-2 h-9 px-3 rounded-full border border-gray-200 bg-white hover:bg-gray-50 transition-all dropdown-btn">
                                <img :src="language.image" alt="flag" class="w-5 h-5 rounded-full object-cover">
                                <span class="text-xs font-bold text-gray-700 uppercase tracking-wide">{{ language.code
                                    }}</span>
                            </button>
                            <ul v-if="languages.length > 0"
                                class="p-2 w-40 rounded-xl shadow-lg absolute top-10 right-0 z-50 border border-gray-100 bg-white transition-all scale-y-0 origin-top dropdown-list">
                                <li @click="changeLanguage(language.id, language.code)" v-for="language in languages"
                                    class="flex items-center gap-3 py-2 px-3 rounded-lg cursor-pointer hover:bg-gray-50">
                                    <img :src="language.image" alt="flag" class="w-4 h-4 rounded-full">
                                    <span class="text-xs font-bold text-gray-700">{{ language.name }}</span>
                                </li>
                            </ul>
                        </div>

                        <!-- Cart -->
                        <button @click.prevent="openCanvas('cart')" type="button"
                            class="flex items-center gap-2 h-9 px-4 rounded-full bg-heading text-white hover:bg-black hover:shadow-lg hover:-translate-y-0.5 transition-all duration-300 shadow-md">
                            <i class="lab lab-bag-2 text-base"></i>
                            <span class="text-xs font-bold tracking-wide">
                                {{ currencyFormat(subtotal, setting.site_digit_after_decimal_point,
                                    setting.site_default_currency_symbol, setting.site_currency_position) }}
                            </span>
                        </button>

                        <!-- User Profile / Login -->
                        <router-link v-if="!logged" :to="{ name: 'auth.login' }"
                            class="flex items-center gap-2 h-9 px-5 rounded-full bg-primary text-white font-semibold text-xs hover:bg-primary-dark hover:shadow-lg hover:shadow-primary/30 hover:-translate-y-0.5 transition-all shadow-md no-underline">
                            <i class="lab lab-profile-circle text-base"></i>
                            <span>{{ $t('label.login') }}</span>
                        </router-link>

                        <div v-else class="dropdown-group relative z-50">
                            <button
                                class="flex items-center gap-2 h-9 pl-1 pr-3 rounded-full border border-gray-200 bg-white hover:border-primary hover:ring-1 hover:ring-primary/20 transition-all dropdown-btn">
                                <img :src="profile.image" alt="avatar"
                                    class="w-7 h-7 rounded-full object-cover border border-gray-100">
                                <span class="text-xs font-bold text-heading max-w-[80px] truncate">{{ profile.name
                                    }}</span>
                                <i class="lab lab-arrow-down-2 text-[10px] text-gray-400"></i>
                            </button>

                            <!-- Dropdown List -->
                            <div
                                class="dropdown-list w-64 absolute top-10 right-0 z-50 rounded-2xl shadow-xl bg-white border border-gray-100 scale-y-0 origin-top-right transition-all overflow-hidden">
                                <div class="bg-gray-50 p-4 border-b border-gray-100">
                                    <div class="flex items-center gap-3">
                                        <img class="w-10 h-10 rounded-full object-cover border-2 border-white shadow-sm"
                                            :src="profile.image" alt="avatar">
                                        <div class="overflow-hidden">
                                            <h3 class="text-sm font-bold text-heading truncate">{{ profile.name }}</h3>
                                            <p class="text-[10px] text-gray-500 truncate">{{ profile.email }}</p>
                                        </div>
                                    </div>
                                </div>
                                <nav class="p-1.5">
                                    <router-link
                                        v-if="profile.role_id !== enums.roleEnum.CUSTOMER && Object.keys(authDefaultPermission).length > 0"
                                        :to="{ path: '/admin/' + defaultMenu?.url }"
                                        class="flex items-center gap-3 px-3 py-2 rounded-lg text-xs font-medium text-gray-600 hover:text-primary hover:bg-primary/5">
                                        <i :class="defaultMenu?.icon"></i> {{ $t('menu.' + defaultMenu?.language) }}
                                    </router-link>
                                    <router-link :to="{ name: 'frontend.myOrder' }"
                                        class="flex items-center gap-3 px-3 py-2 rounded-lg text-xs font-medium text-gray-600 hover:text-primary hover:bg-primary/5">
                                        <i class="lab lab-reserve-line"></i> {{ $t('button.my_orders') }}
                                    </router-link>
                                    <router-link :to="{ name: 'frontend.editProfile' }"
                                        class="flex items-center gap-3 px-3 py-2 rounded-lg text-xs font-medium text-gray-600 hover:text-primary hover:bg-primary/5">
                                        <i class="lab lab-edit"></i> {{ $t('button.edit_profile') }}
                                    </router-link>
                                    <router-link :to="{ name: 'frontend.chat' }"
                                        class="flex items-center gap-3 px-3 py-2 rounded-lg text-xs font-medium text-gray-600 hover:text-primary hover:bg-primary/5">
                                        <i class="lab lab-messages-line"></i> {{ $t('button.chat') }}
                                    </router-link>
                                    <router-link :to="{ name: 'frontend.address' }"
                                        class="flex items-center gap-3 px-3 py-2 rounded-lg text-xs font-medium text-gray-600 hover:text-primary hover:bg-primary/5">
                                        <i class="lab lab-map"></i> {{ $t('button.address') }}
                                    </router-link>
                                    <router-link :to="{ name: 'frontend.changePassword' }"
                                        class="flex items-center gap-3 px-3 py-2 rounded-lg text-xs font-medium text-gray-600 hover:text-primary hover:bg-primary/5">
                                        <i class="lab lab-key"></i> {{ $t('button.change_password') }}
                                    </router-link>
                                    <div class="h-px bg-gray-100 my-1"></div>
                                    <button @click="logout()"
                                        class="w-full flex items-center gap-3 px-3 py-2 rounded-lg text-xs font-medium text-red-500 hover:bg-red-50">
                                        <i class="lab lab-logout"></i> {{ $t('button.logout') }}
                                    </button>
                                </nav>
                            </div>
                        </div>
                    </div>

                    <!-- Bottom Row: Navigation Menu -->
                    <nav class="flex items-center gap-8 py-2 mr-[10rem]">

                        <router-link :to="{ name: 'frontend.home' }"
                            :class="checkIsPathAndRoutePathSame('/home') ? 'text-primary font-bold' : 'text-gray-500 font-medium'"
                            class="text-sm tracking-wide hover:text-primary transition-all duration-300 flex items-center justify-center py-2">
                            {{ $t('menu.home') }}
                        </router-link>

                        <router-link :to="{ name: 'frontend.menu', query: { s: categoryProps.slug } }"
                            :class="checkIsPathAndRoutePathSame('/menu') ? 'text-primary font-bold' : 'text-gray-500 font-medium'"
                            class="text-sm tracking-wide hover:text-primary transition-all duration-300 flex items-center justify-center py-2">
                            {{ $t('label.menu') }}
                        </router-link>

                        <router-link :to="{ name: 'frontend.offers' }"
                            :class="checkIsPathAndRoutePathSame('/offers') ? 'text-primary font-bold' : 'text-gray-500 font-medium'"
                            class="text-sm tracking-wide hover:text-primary transition-all duration-300 flex items-center justify-center py-2">
                            {{ $t('label.offers') }}
                        </router-link>

                        <router-link :to="{ name: 'frontend.ourSpecials' }"
                            :class="checkIsPathAndRoutePathSame('/our-specials') ? 'text-primary font-bold' : 'text-gray-500 font-medium'"
                            class="text-sm tracking-wide hover:text-primary transition-all duration-300 flex items-center justify-center py-2">
                            {{ $t('label.ourSpecials') }}
                        </router-link>

                        <router-link :to="{ name: 'frontend.about' }"
                            :class="checkIsPathAndRoutePathSame('/about-us') ? 'text-primary font-bold' : 'text-gray-500 font-medium'"
                            class="text-sm tracking-wide hover:text-primary transition-all duration-300 flex items-center justify-center py-2">
                            About Us
                        </router-link>

                        <router-link :to="{ name: 'frontend.contact' }"
                            :class="checkIsPathAndRoutePathSame('/contact') ? 'text-primary font-bold' : 'text-gray-500 font-medium'"
                            class="text-sm tracking-wide hover:text-primary transition-all duration-300 flex items-center justify-center py-2">
                            Contact
                        </router-link>

                    </nav>

                </div>
            </div>
        </div>
    </header>

    <!-- ✅ Mobile Search Modal -->
    <div v-if="showMobileSearch" class="fixed inset-0 z-[100] bg-gray-900/60 backdrop-blur-sm lg:hidden"
        @click.self="showMobileSearch = false">
        <div class="bg-white w-full p-4 rounded-b-3xl shadow-2xl animate-slide-down">
            <form @submit.prevent="search" class="relative">
                <div class="flex items-center gap-3">
                    <div class="flex-1 relative group">
                        <i
                            class="lab lab-search-normal absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-lg"></i>
                        <input type="search" v-model="searchItem" :placeholder="$t('button.search')" autofocus
                            @input="handleSearchInput"
                            class="w-full h-12 pl-12 pr-4 rounded-2xl bg-gray-50 border border-gray-200 focus:bg-white focus:border-primary focus:ring-2 focus:ring-primary/20 text-heading text-sm font-medium transition-all"
                            @keyup.enter="search">
                    </div>
                    <button type="button" @click="showMobileSearch = false"
                        class="w-12 h-12 flex items-center justify-center rounded-2xl bg-gray-50 text-gray-500 hover:text-red-500 hover:bg-red-50 transition-colors">
                        <i class="lab lab-close-circle-line text-2xl"></i>
                    </button>
                </div>
                <!-- Mobile Results -->
                <div v-if="searchResults.length > 0 || searchLoading" class="mt-4 max-h-[60vh] overflow-y-auto">
                    <div v-if="searchLoading" class="p-8 text-center"><i
                            class="lab lab-loading animate-spin text-3xl text-primary"></i></div>
                    <div v-else-if="searchResults.length > 0" class="space-y-3 pb-4">
                        <div v-for="item in searchResults" :key="item.id"
                            class="flex items-center gap-4 p-3 bg-white border border-gray-100 rounded-2xl shadow-sm"
                            @click="selectSearchItem(item)">
                            <img :src="item.thumb" :alt="item.name" class="w-16 h-16 object-cover rounded-xl">
                            <div class="flex-1 min-w-0">
                                <h4 class="text-sm font-bold text-heading truncate">{{ item.name }}</h4>
                                <span class="text-sm font-bold text-primary">{{ (item.offer && item.offer.length > 0) ?
                                    item.offer[0].currency_price : item.currency_price }}</span>
                            </div>
                        </div>
                    </div>
                    <div v-else class="p-8 text-center text-gray-500 text-sm">{{ $t('message.no_items_found') }}</div>
                </div>
            </form>
        </div>
    </div>

    <!-- ✅ Language Modal (Mobile) -->
    <div id="language" class="modal group">
        <div class="modal-dialog max-w-xs rounded-2xl shadow-2xl p-0 overflow-hidden">
            <div class="modal-header border-b border-gray-100 p-4 flex items-center justify-between bg-gray-50">
                <h3 class="modal-title font-bold text-heading">{{ $t('menu.languages') }}</h3>
                <button class="text-gray-400 hover:text-red-500 transition-colors" @click="hideLanguageModal()">
                    <i class="fa-regular fa-circle-xmark text-xl"></i>
                </button>
            </div>
            <nav class="p-2 bg-white">
                <button @click="changeLanguage(language.id, language.code)" v-for="language in languages" type="button"
                    class="w-full flex items-center gap-3 py-3 px-4 rounded-xl cursor-pointer hover:bg-primary/5 hover:text-primary transition-all">
                    <img :src="language.image" alt="flag" class="w-6 h-6 rounded-full border border-gray-200">
                    <span class="text-sm font-semibold capitalize">{{ language.name }}</span>
                </button>
            </nav>
        </div>
    </div>

    <!-- ✅ Order Notification Modal -->
    <div id="order" v-if="orderNotificationStatus" ref="orderNotificationModal"
        class="modal active ff-modal backdrop-blur-sm bg-black/50">
        <div class="modal-dialog max-w-[400px] p-8 text-center relative rounded-3xl shadow-2xl bg-white">
            <button @click.prevent="closeOrderNotificationModal"
                class="absolute top-4 right-4 text-gray-400 hover:text-red-500 transition-colors">
                <i class="fa-regular fa-circle-xmark text-xl"></i>
            </button>
            <div
                class="w-16 h-16 bg-primary/10 rounded-full flex items-center justify-center mx-auto mb-4 text-primary">
                <i class="lab lab-notification text-3xl"></i>
            </div>
            <h3 class="text-xl font-bold text-heading mb-2">New Update!</h3>
            <p class="text-gray-500 text-sm mb-6">{{ orderNotificationMessage }}<span class="block mt-1">{{
                $t('message.please_check_your_order_list') }}</span></p>
            <router-link :to="{ path: '/admin/' + orderNotification.url }"
                class="inline-flex items-center justify-center w-full h-12 rounded-xl bg-primary text-white font-semibold shadow-lg shadow-primary/30 hover:shadow-primary/50 hover:-translate-y-1 transition-all no-underline">
                {{ $t('button.let_me_check') }}
            </router-link>
        </div>
    </div>
</template>
<style scoped>
/* Remove underline from all router-links */
a {
    text-decoration: none;
}

.no-underline {
    text-decoration: none !important;
}

/* Ensure proper vertical alignment */
.ff-header {
    position: relative;
}

/* Logo container adjustments */
.ff-header img[alt="logo"] {
    object-fit: contain;
}
</style>

<style>
.ff-header .container {
    height: 75px !important;
    padding-top: 0.75rem;
    padding-bottom: 0.75rem;
}

.animate-slide-down {
    animation: slideDown 0.3s ease-out forwards;
}

.left-3\.5 {
    left: 0.375rem !important;
}

@keyframes slideDown {
    from {
        transform: translateY(-100%);
        opacity: 0;
    }

    to {
        transform: translateY(0);
        opacity: 1;
    }
}

@keyframes spin {
    from {
        transform: rotate(0deg);
    }

    to {
        transform: rotate(360deg);
    }
}

.animate-spin {
    animation: spin 1s linear infinite;
}

/* Search results styling */
.search-results-dropdown {
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
}

.search-result-item:hover {
    background-color: #f8f9fa;
}

/* Mobile search results */
.mobile-search-results {
    max-height: 320px;
    overflow-y: auto;
}

.mobile-search-results::-webkit-scrollbar {
    width: 4px;
}

.mobile-search-results::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 2px;
}

.mobile-search-results::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 2px;
}

.mobile-search-results::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8;
}
</style>



<script>
import statusEnum from "../../../enums/modules/statusEnum";
import appService from "../../../services/appService";
import alertService from "../../../services/alertService";
import LoadingComponent from "../../frontend/components/LoadingComponent";
import axios from 'axios'
import { initializeApp } from "firebase/app";
import { getMessaging, getToken, onMessage } from "firebase/messaging";
import activityEnum from "../../../enums/modules/activityEnum";
import roleEnum from "../../../enums/modules/roleEnum";
import _ from "lodash";

export default {
    name: "FrontendNavbarComponent",
    components: { LoadingComponent },
    data() {
        return {
            loading: {
                isActive: false,
            },
            // ADDED: State for mobile search modal
            showMobileSearch: false,

            orderNotificationStatus: false,
            orderNotificationMessage: "",
            currentRoute: "",
            defaultBranch: null,
            defaultLanguage: null,
            defaultCountryCode: null,
            enums: {
                activityEnum: activityEnum,
                roleEnum: roleEnum
            },
            branchProps: {
                paginate: 0,
                order_column: "id",
                order_type: "asc",
                status: statusEnum.ACTIVE
            },
            languageProps: {
                paginate: 0,
                order_column: "id",
                order_type: "asc",
                status: statusEnum.ACTIVE
            },
            categoryProps: {
                search: {
                    paginate: 0,
                    order_column: 'sort',
                    order_type: 'asc',
                    status: statusEnum.ACTIVE
                },
                slug: '',
            },
            orderNotification: {
                permission: false,
                url: ""
            },
            searchItem: "",
            searchResults: [],
            showSearchResults: false,
            searchLoading: false,

        }
    },
    computed: {
        logged: function () {
            return this.$store.getters.authStatus;
        },
        authDefaultPermission: function () {
            return this.$store.getters.authDefaultPermission;
        },
        profile: function () {
            return this.$store.getters.authInfo;
        },
        setting: function () {
            return this.$store.getters['frontendSetting/lists'];
        },
        language: function () {
            return this.$store.getters['frontendLanguage/show'];
        },
        languages: function () {
            return this.$store.getters['frontendLanguage/lists'];
        },
        categories: function () {
            return this.$store.getters['frontendItemCategory/lists'];
        },
        subtotal: function () {
            return this.$store.getters['frontendCart/subtotal'];
        },
        defaultMenu: function () {
            return this.$store.getters.authDefaultMenu;
        }
    },
    mounted() {
        window.addEventListener('scroll', () => {
            const resetRoutes = [
                'frontend.myOrder',
                'frontend.editProfile',
                'frontend.chat',
                'frontend.address',
                'frontend.changePassword',
                'auth.login',
                'auth.signupPhone',
                'auth.signupRegister',
                'auth.forgetPassword',
                'auth.verifyEmail',
                'frontend.page',
                'frontend.offers'
            ];
            if (this.$refs.ffHeader) {
                if (!resetRoutes.includes(this.$route.name)) {
                    if (window.scrollY > 0) {
                        this.$refs.ffHeader.classList.add('active');
                    } else {
                        this.$refs.ffHeader.classList.remove('active');
                    }
                } else {
                    this.$refs.ffHeader.classList.remove('active');
                }
            }
        });

        this.currentRoute = this.$route.path;
        this.loading.isActive = true;
        this.orderPermissionCheck();
        this.$store.dispatch('frontendSetting/lists').then(res => {
            this.defaultBranch = res.data.data.site_default_branch;
            this.defaultLanguage = res.data.data.site_default_language;
            this.defaultCountryCode = res.data.data.company_country_code;

            const globalState = this.$store.getters['globalState/lists'];
            if (globalState.branch_id > 0) {
                this.defaultBranch = globalState.branch_id;
            }

            if (globalState.language_id > 0) {
                this.defaultLanguage = globalState.language_id;
            }
            this.$store.dispatch('frontendLanguage/lists', this.languageProps).then().catch();
            this.$store.dispatch('frontendLanguage/show', this.defaultLanguage).then(res => {
                this.$i18n.locale = res.data.data.code;
                this.$store.dispatch("globalState/init", {
                    language_code: res.data.data.code
                });
            }).catch();
            this.$store.dispatch('frontendItemCategory/lists', this.categoryProps.search).then().catch();
            this.loading.isActive = false;

            window.setTimeout(() => {
                this.$store.dispatch('frontendCountryCode/show', this.defaultCountryCode).then().catch();
                this.$store.dispatch('frontendCart/initOrderType', {
                    order_setup_delivery: res.data.data.order_setup_delivery,
                    order_setup_takeaway: res.data.data.order_setup_takeaway
                });

                if (this.$store.getters.authStatus && res.data.data.notification_fcm_api_key && res.data.data.notification_fcm_auth_domain && res.data.data.notification_fcm_project_id && res.data.data.notification_fcm_storage_bucket && res.data.data.notification_fcm_messaging_sender_id && res.data.data.notification_fcm_app_id && res.data.data.notification_fcm_measurement_id) {
                    initializeApp({
                        apiKey: res.data.data.notification_fcm_api_key,
                        authDomain: res.data.data.notification_fcm_auth_domain,
                        projectId: res.data.data.notification_fcm_project_id,
                        storageBucket: res.data.data.notification_fcm_storage_bucket,
                        messagingSenderId: res.data.data.notification_fcm_messaging_sender_id,
                        appId: res.data.data.notification_fcm_app_id,
                        measurementId: res.data.data.notification_fcm_measurement_id
                    });
                    const messaging = getMessaging();

                    Notification.requestPermission().then((permission) => {
                        if (permission === 'granted') {
                            getToken(messaging, { vapidKey: res.data.data.notification_fcm_public_vapid_key }).then((currentToken) => {
                                if (currentToken) {
                                    axios.post('/frontend/device-token/web', { token: currentToken }).then().catch((error) => {
                                        if (error.response.data.message === 'Unauthenticated.') {
                                            this.$store.dispatch('loginDataReset');
                                        }
                                    });
                                }
                            }).catch();
                        }
                    });

                    onMessage(messaging, (payload) => {
                        const notificationTitle = payload.notification.title;
                        const notificationOptions = {
                            body: payload.notification.body,
                            icon: '/images/default/firebase-logo.png'
                        };
                        new Notification(notificationTitle, notificationOptions);

                        if (payload.data.topicName === 'new-order-found' && this.orderNotification.permission) {
                            this.orderNotificationStatus = true;
                            this.orderNotificationMessage = payload.notification.body;
                            const audio = new Audio(res.data.data.notification_audio);
                            audio.play();
                        }
                    });
                }
            }, 3000);
        }).catch((err) => {
            this.loading.isActive = false;
        });
    },

    methods: {
        textShortener: function (text, number = 30) {
            return appService.textShortener(text, number);
        },
        openCanvas: function (id) {
            return appService.openCanvas(id);
        },
        checkIsPathAndRoutePathSame(path) {
            if (this.currentRoute === path) {
                return true;
            }
        },
        changeBranch: function (id) {
            this.$store.dispatch('frontendBranch/show', id);
            this.$store.dispatch("globalState/set", { branch_id: id });
        },
        changeLanguage: function (id, code) {
            this.hideLanguageModal();
            this.defaultLanguage = id;
            this.$store.dispatch("globalState/set", { language_id: id, language_code: code }).then(res => {
                this.$store.dispatch('frontendLanguage/show', id).then(res => {
                    this.$i18n.locale = res.data.data.code;
                }).catch();
            }).catch();
        },
        hideLanguageModal: function () {
            appService.modalHide('#language');
        },
        logout: function () {
            this.$store.dispatch("logout").then(res => {
                this.$router.push({ name: "frontend.home" });
            }).catch();
        },
        currencyFormat(amount, decimal, currency, position) {
            return appService.currencyFormat(amount, decimal, currency, position);
        },
        search: function () {
            // Trim and check for empty
            const term = (this.searchItem || '').trim();
            if (!term) {
                // Optional: show alert or just close
                this.showMobileSearch = false;
                return;
            }

            // Navigate
            this.$router.push({
                name: "frontend.search",
                query: { s: term }
            }).then(() => {
                this.searchItem = ""; // reset after navigation
                this.showMobileSearch = false; // close modal
            }).catch(err => {
                // handle navigation errors
                console.warn('Search nav error:', err);
            });
        },
        searchReset: function () {
            this.searchItem = "";
            this.searchResults = [];
            this.showSearchResults = false;
        },
        handleSearchInput: _.debounce(function () {
            const term = (this.searchItem || '').trim();
            if (term.length >= 2) {
                this.performSearch(term);
            } else {
                this.searchResults = [];
                this.showSearchResults = false;
            }
        }, 300),
        performSearch: function (term) {
            this.searchLoading = true;
            this.showSearchResults = true;

            const searchProps = {
                paginate: 8, // Limit results for dropdown
                order_column: 'id',
                order_type: 'asc',
                name: term,
                status: statusEnum.ACTIVE,
            };

            this.$store.dispatch("frontendItem/lists", searchProps).then((res) => {
                this.searchResults = res.data.data || [];
                this.searchLoading = false;
            }).catch((err) => {
                this.searchResults = [];
                this.searchLoading = false;
                console.error('Search error:', err);
            });
        },
        hideSearchResults: function () {
            // Delay hiding to allow for click events
            setTimeout(() => {
                this.showSearchResults = false;
            }, 200);
        },
        selectSearchItem: function (item) {
            this.showMobileSearch = false;
            this.showSearchResults = false;
            this.searchItem = "";
            this.searchResults = [];

            // Navigate to search page with the item name
            this.$router.push({
                name: "frontend.search",
                query: { s: item.name }
            });
        },
        quickAddToCart: function (item) {
            // Check if item has variations or extras that require selection
            if (this.itemRequiresVariationModal(item)) {
                // Open variation modal for complex items
                this.openVariationModal(item);
            } else {
                // Direct add to cart for simple items
                this.directAddToCart(item);
            }
        },
        itemRequiresVariationModal: function (item) {
            // This is a simplified check - you might need to fetch full item details
            // For now, assume items with variations need the modal
            return item.has_variation || item.has_extras || item.has_addons;
        },
        openVariationModal: function (item) {
            this.showMobileSearch = false;
            this.showSearchResults = false;

            // Dispatch event to open variation modal
            // This assumes you have a global event system or can access the ItemComponent
            this.$store.dispatch('frontendItem/details', item.id).then((res) => {
                // You might need to emit an event or use a global state to open the modal
                // For now, navigate to search page where user can properly add the item
                this.$router.push({
                    name: "frontend.search",
                    query: { s: item.name }
                });
            }).catch();
        },
        directAddToCart: function (item) {
            const itemArray = [{
                name: item.name || '',
                image: item.thumb || item.image || '',
                item_id: item.id,
                quantity: 1,
                discount: 0,
                currency_price: (item.offer && item.offer.length > 0) ? item.offer[0].currency_price : item.currency_price,
                convert_price: (item.offer && item.offer.length > 0) ? item.offer[0].convert_price : item.convert_price,
                item_variations: {
                    variations: {},
                    names: {}
                },
                item_extras: {
                    extras: [],
                    names: []
                },
                item_variation_total: 0,
                item_extra_total: 0,
                instruction: ""
            }];

            this.$store.dispatch("frontendCart/lists", itemArray).then((res) => {
                this.showMobileSearch = false;
                this.showSearchResults = false;
                this.searchItem = "";
                this.searchResults = [];

                alertService.success(this.$t('message.add_to_cart'));
            }).catch((err) => {
                alertService.error(err.response?.data?.message || 'Error adding to cart');
            });
        },
        saveImage: function () {
            if (this.$refs.imageProperty.files[0]) {
                try {
                    this.loading.isActive = true;
                    const formData = new FormData();
                    formData.append("image", this.$refs.imageProperty.files[0]);
                    this.$store.dispatch("frontendEditProfile/changeImage", { form: formData }).then((res) => {
                        this.$store.dispatch('updateAuthInfo', res.data.data).then(res => {
                            this.loading.isActive = false;
                            alertService.success(this.$t("message.photo_update"));
                            this.$refs.imageProperty.value = null;
                        }).catch((err) => {
                            this.loading.isActive = false;
                            alertService.error(err);
                        });
                    }).catch((err) => {
                        this.loading.isActive = false;
                        this.imageErrors = err.response.data.errors;
                    });
                } catch (err) {
                    this.loading.isActive = false;
                    alertService.error(err.response.data.message);
                }
            }
        },
        orderPermissionCheck: function () {
            const permissions = this.$store.getters.authPermission;
            if (permissions.length > 0) {
                _.forEach(permissions, (permission) => {
                    if (permission.name === 'online-orders') {
                        if (permission.access === true) {
                            this.orderNotification.permission = true;
                            this.orderNotification.url = permission.url;
                        }
                    }
                });
            }
        },
        closeOrderNotificationModal: function () {
            const modalTarget = this.$refs.orderNotificationModal;
            modalTarget?.classList?.remove("active");
            document.body.style.overflowY = "auto";
            this.loading.isActive = false;
            this.orderNotificationStatus = false;
        },
    },

    watch: {
        $route(to, from) {
            this.currentRoute = to.path;
            // Close mobile search when route changes
            this.showMobileSearch = false;
            // Clear search results when navigating
            this.searchResults = [];
            this.showSearchResults = false;
        },
        // ADDED: Prevent background scrolling when search modal is open
        showMobileSearch(val) {
            if (val) {
                document.body.style.overflow = 'hidden';
            } else {
                document.body.style.overflow = 'auto';
                // Clear search results when closing mobile search
                this.searchResults = [];
            }
        },
        // Clear search results when search item is cleared
        searchItem(val) {
            if (!val || val.trim() === '') {
                this.searchResults = [];
                this.showSearchResults = false;
            }
        },
        categories: {
            deep: true,
            handler(category) {
                if (category.length > 0) {
                    if (category[0].slug !== "undefined") {
                        this.categoryProps.slug = category[0].slug;
                    }
                }
            }
        }
    },
}
</script>
