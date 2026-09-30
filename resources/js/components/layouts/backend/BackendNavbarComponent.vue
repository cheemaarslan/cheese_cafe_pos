<template>
    <div class="backdrop"></div>
    <header class="db-header">
        <div class="flex items-center justify-between w-full gap-4">
            <button
                @click.prevent="handleSidebar"
                v-if="
                    !$route.path.includes('kitchen-display-system') &&
                    !$route.path.includes('order-status-screen')
                "
                class="fa-solid db-header-nav w-9 h-9 rounded-lg text-primary bg-primary/5"
                :class="sidebar ? 'fa-align-left' : 'fa-bars'"
            ></button>
            <div class="flex items-center gap-4 transition-all" :class="($route.path.includes('kitchen-display-system') || $route.path.includes('order-status-screen')) ? 'w-full justify-between' : ''">
                <div
                    class="sub-header flex items-center gap-4 transition xh:justify-between xh:fixed xh:left-0 xh:w-full xh:p-4 xh:border-y xh:border-[#EFF0F6] xh:bg-white"
                >
                    <button
                        v-if="$route.path.includes('order-status-screen')"
                        type="button"
                        @click="fullScreen"
                        class="hidden db-header-toggle lg:flex items-center justify-center w-9 h-9 px-3 rounded-lg bg-[#E0FFED]"
                    >
                        <i
                            class="lab lab-maximize lab-font-size-24 text-[#1AB759]"
                        ></i>
                    </button>

                    <!-- Notification Bell -->
                    <div class="relative dropdown-group" ref="notifDropdown">
                        <button
                            class="dropdown-btn relative flex items-center justify-center w-9 h-9 rounded-lg bg-[#FFEDF4]"
                            @click.stop="toggleNotifDropdown"
                        >
                            <i
                                class="fa-solid fa-bell text-primary"
                                :class="{ blinking: unreadCount > 0 }"
                            ></i>
                            <span
                                v-if="unreadCount > 0"
                                class="absolute -top-1.5 -right-1.5 flex items-center justify-center w-5 h-5 rounded-full bg-primary text-white text-[10px] font-medium"
                            >
                                {{ unreadCount > 9 ? "9+" : unreadCount }}
                            </span>
                        </button>

                        <div
                            v-show="notifOpen"
                            class="dropdown-list fixed sm:absolute top-[75px] sm:top-12 z-[60] w-full sm:w-[320px] rounded-xl shadow-paper bg-white border border-[#EFF0F6] overflow-hidden"
                            :class="[{ active: notifOpen }, ($route.path.includes('kitchen-display-system') || $route.path.includes('order-status-screen')) ? 'ltr:left-0 rtl:right-0' : 'ltr:right-0 rtl:left-0']"
                        >
                            <div
                                class="flex items-center justify-between px-4 py-3 border-b border-[#EFF0F6]"
                            >
                                <span
                                    class="text-sm font-medium text-heading"
                                    >{{ $t("label.notifications") }}</span
                                >
                                <button
                                    @click="markAllRead"
                                    :disabled="
                                        notifLoading ||
                                        notifications.length === 0
                                    "
                                    :class="{
                                        'opacity-50 cursor-not-allowed':
                                            notifLoading ||
                                            notifications.length === 0,
                                    }"
                                    class="text-xs text-primary"
                                >
                                    {{
                                        notifLoading
                                            ? $t("button.loading") ||
                                              "Loading..."
                                            : $t("button.mark_all_read")
                                    }}
                                </button>
                            </div>

                            <ul class="max-h-[300px] overflow-y-auto">
                                <li
                                    v-if="notifLoading"
                                    class="px-4 py-8 text-center"
                                >
                                    <div class="inline-block">
                                        <div
                                            class="animate-spin rounded-full h-6 w-6 border-2 border-primary border-t-transparent"
                                        ></div>
                                    </div>
                                </li>
                                <li
                                    v-else-if="notifications.length === 0"
                                    class="px-4 py-8 text-center text-xs text-gray-400"
                                >
                                    {{ $t("message.no_notifications") }}
                                </li>
                                <li
                                    v-for="notif in notifications"
                                    :key="notif.id"
                                    @click="readNotification(notif)"
                                    class="flex items-start gap-3 px-4 py-3 border-b last:border-none border-[#EFF0F6] cursor-pointer transition hover:bg-gray-50"
                                    :class="{
                                        'opacity-60':
                                            readingNotifications[notif.id],
                                        'bg-[#FFF5F9]': !notif.read_at,
                                    }"
                                >
                                    <span
                                        class="mt-1.5 w-2 h-2 rounded-full flex-shrink-0"
                                        :class="
                                            notif.read_at
                                                ? 'bg-gray-200'
                                                : 'bg-primary'
                                        "
                                    ></span>
                                    <div class="flex-1">
                                        <p
                                            class="text-xs text-heading leading-5"
                                        >
                                            {{
                                                notif.data?.message ||
                                                "Notification"
                                            }}
                                        </p>
                                        <p
                                            class="text-[11px] text-gray-400 mt-0.5"
                                        >
                                            {{ timeAgo(notif.created_at) }}
                                        </p>
                                    </div>
                                    <div
                                        v-if="readingNotifications[notif.id]"
                                        class="animate-spin"
                                    >
                                        <i
                                            class="fa-solid fa-circle-notch fa-xs text-gray-400"
                                        ></i>
                                    </div>
                                </li>
                            </ul>

                            <div
                                class="px-4 py-2.5 border-t border-[#EFF0F6] text-center"
                            >
                                <!-- Notifications list page not yet implemented -->
                            </div>
                        </div>
                    </div>

                    <!--
                <div v-if="authBranch === 0" class="relative dropdown-group">
                    <button class="flex items-center text-left gap-2 dropdown-btn">
                        <i class="lab lab-shop lab-font-size-24 font-fill-primary"></i>
                        <h3 class="capitalize text-xs font-medium text-heading">
                            <span class="block font-normal mb-0.5">{{ $t('label.branch') }}</span>
                            <b class="font-semibold whitespace-nowrap">{{ branch.name }}</b>
                        </h3>
                        <i class="lab lab-arrow-down text-xs ml-1.5 lab-font-size-14"></i>
                    </button>
                    <ul v-if="branches.length > 0"
                        class="p-2 w-fit rounded-lg shadow-xl absolute top-14 left-0 z-10 border border-gray-200 bg-white dropdown-list transition-all duration-300 scale-y-0 origin-top">
                        <li v-for="branch in branches"
                            class="flex items-center gap-2 w-full px-2.5 rounded-md transition hover:bg-gray-100">
                            <input @click="changeBranch(branch.id)" v-model="defaultBranch" type="radio"
                                :id="'branch_id_' + branch.id" :value="branch.id" name="branch"
                                class="w-3 cursor-pointer mb-[1px] accent-primary">
                            <label :for="'branch_id_' + branch.id"
                                class="capitalize leading-8 text-sm min-w-[150px] cursor-pointer text-heading">
                                {{ branch.name }}
                            </label>
                        </li>
                    </ul>
                </div>
                -->
                    <div class="flex items-center gap-4">
                        <div
                            class="relative dropdown-group"
                            v-if="
                                $route.path.includes(
                                    'kitchen-display-system',
                                ) || $route.path.includes('order-status-screen')
                            "
                        >
                            <router-link
                                :to="{ path: '/admin/' + defaultMenu?.url }"
                                @click="closeFullScreen"
                                class="flex items-center gap-2 h-9 px-3 rounded-lg bg-[#FFEDF4]"
                            >
                                <i
                                    class="lab-font-size-17 text-primary"
                                    :class="defaultMenu?.icon"
                                ></i>
                                <span
                                    class="md:block hidden whitespace-nowrap text-xs font-medium capitalize text-heading"
                                    >{{
                                        $t("menu." + defaultMenu?.language)
                                    }}</span
                                >
                            </router-link>
                        </div>
                        <div
                            class="flex items-center justify-between md:justify-center gap-4"
                        >
                            <div
                                v-if="
                                    setting.site_language_switch ===
                                    enums.activityEnum.ENABLE
                                "
                                class="dropdown-group relative"
                            >
                                <button
                                    class="dropdown-btn flex items-center gap-2 h-9 px-3 rounded-lg bg-[#FFEDF4]"
                                >
                                    <img
                                        :src="language.image"
                                        alt="flag"
                                        class="w-4 h-4 rounded-full"
                                    />
                                    <span
                                        class="hidden md:block whitespace-nowrap text-xs font-medium capitalize text-heading"
                                    >
                                        {{ language.name }}
                                    </span>
                                </button>
                                <ul
                                    v-if="languages.length > 0"
                                    class="p-2 min-w-[180px] rounded-lg shadow-xl absolute top-14 ltr:right-0 rtl:left-0 z-10 border border-gray-200 bg-white transition-all duration-300 origin-top scale-y-0 dropdown-list"
                                >
                                    <li
                                        @click="
                                            changeLanguage(
                                                language.id,
                                                language.code,
                                            )
                                        "
                                        v-for="language in languages"
                                        class="flex items-center gap-2 py-1.5 px-2.5 rounded-md cursor-pointer hover:bg-gray-100"
                                    >
                                        <img
                                            :src="language.image"
                                            alt="flag"
                                            class="w-4 h-4 rounded-full"
                                        />
                                        <span
                                            class="text-heading capitalize text-sm"
                                            >{{ language.name }}</span
                                        >
                                    </li>
                                </ul>
                            </div>

                            <!-- <router-link
                                v-if="pos.permission && !$route.path.includes('kitchen-display-system') && !$route.path.includes('order-status-screen')"
                                class="w-9 h-9 rounded-lg flex items-center justify-center bg-[#FFEBD8]"
                                :to="{ path: '/admin/' + pos.url }">
                                <i class="lab lab-pos-bold lab-font-size-16 font-fill-pos"></i>
                            </router-link> -->
                        </div>
                    </div>
                </div>
                <button
                    @click.prevent="handleSidebar"
                    v-if="
                        !$route.path.includes('kitchen-display-system') &&
                        !$route.path.includes('order-status-screen')
                    "
                    class="fa-solid db-header-nav w-9 h-9 rounded-lg text-primary bg-primary/5"
                    :class="sidebar ? 'fa-align-left block' : 'fa-bars hidden'"
                ></button>

                <div class="dropdown-group">
                    <button class="dropdown-btn flex items-center gap-2">
                        <img
                            class="flex-shrink-0 w-9 h-9 object-cover rounded-lg"
                            :src="authInfo.image"
                            alt="avatar"
                        />
                        <h3
                            class="whitespace-nowrap text-sm capitalize text-left leading-[17px]"
                        >
                            {{ $t("label.hello") }}
                            <b class="block font-semibold">{{
                                textShortener(authInfo.name, 15)
                            }}</b>
                        </h3>
                        <i
                            class="lab lab-arrow-down text-xs ml-1.5 lab-font-size-14"
                        ></i>
                    </button>
                    <div
                        class="dropdown-list fixed sm:absolute top-[75px] sm:top-12 ltr:right-0 rtl:left-0 z-[60] rounded-xl w-full h-[calc(100vh_-_75px)] overflow-y-auto sm:h-auto sm:w-[360px] p-4 shadow-paper bg-white transition-all duration-300 scale-y-0 origin-top"
                    >
                        <div class="w-fit mx-auto text-center mb-5">
                            <figure
                                class="relative z-10 w-[98px] h-[98px] border-2 border-dashed rounded-full inline-flex items-center justify-center border-white bg-gradient-to-t from-[#FF7A00] to-[#FF016C] before:absolute before:top-1/2 before:left-1/2 before:-translate-x-1/2 before:-translate-y-1/2 before:w-24 before:h-24 before:rounded-full before:-z-10 before:bg-white"
                            >
                                <img
                                    class="w-[90px] h-[90px] rounded-full shadow-avatar"
                                    :src="authInfo.image"
                                    alt="avatar"
                                />
                            </figure>

                            <label
                                for="imageProperty"
                                class="block w-11 h-11 mx-auto -mt-7 mb-3 relative z-10 rounded-full border-2 cursor-pointer bg-heading border-white"
                            >
                                <input
                                    @change="saveImage"
                                    accept="image/png, image/jpeg, image/jpg, image/webp"
                                    ref="imageProperty"
                                    type="file"
                                    id="imageProperty"
                                    class="w-full h-full rounded-full opacity-0 cursor-pointer"
                                />
                                <i
                                    class="lab lab-edit-2 absolute top-1/2 left-1/2 -translate-y-1/2 -translate-x-1/2 -z-10 lab-font-size-24 lab-font-color-1"
                                ></i>
                            </label>

                            <h3
                                class="font-medium text-sm leading-6 capitalize mb-0.5"
                            >
                                {{ textShortener(authInfo.name, 20) }}
                            </h3>
                            <p class="text-xs mb-0.5">{{ authInfo.email }}</p>
                            <p dir="ltr" class="text-xs">
                                {{ authInfo.country_code }}{{ authInfo.phone }}
                            </p>
                            <h3
                                class="font-medium text-sm leading-6 capitalize mb-0.5"
                                v-if="authInfo.currency_balance > 0"
                            >
                                {{ authInfo.currency_balance }}
                            </h3>
                        </div>
                        <nav>
                            <router-link
                                :to="{ name: 'admin.profile.editProfile' }"
                                class="paper-link transition w-full flex items-center gap-3.5 py-3 border-b last:border-none border-[#EFF0F6]"
                            >
                                <i class="lab lab-edit lab-font-size-17"></i>
                                <span class="text-sm leading-6 capitalize">{{
                                    $t("button.edit_profile")
                                }}</span>
                            </router-link>

                            <router-link
                                :to="{ name: 'admin.profile.changePassword' }"
                                class="paper-link transition w-full flex items-center gap-3.5 py-3 border-b last:border-none border-[#EFF0F6]"
                            >
                                <i class="lab lab-key lab-font-size-17"></i>
                                <span class="text-sm leading-6 capitalize">{{
                                    $t("button.change_password")
                                }}</span>
                            </router-link>

                            <button
                                @click="logout()"
                                class="paper-link transition w-full flex items-center gap-3.5 py-3 border-b last:border-none border-[#EFF0F6]"
                            >
                                <i class="lab lab-logout lab-font-size-17"></i>
                                <span class="text-sm leading-6 capitalize">{{
                                    $t("button.logout")
                                }}</span>
                            </button>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <div
        id="order"
        v-if="orderNotificationStatus"
        ref="orderNotificationModal"
        class="modal active ff-modal"
    >
        <div class="modal-dialog max-w-[360px] p-6 text-center relative">
            <button
                @click.prevent="closeOrderNotificationModal"
                class="modal-close absolute top-4 right-4"
            >
                <i class="fa-regular fa-circle-xmark"></i>
            </button>
            <h3 class="text-[18px] font-semibold leading-8 mb-6">
                {{ orderNotificationMessage }}
                <span class="block">{{
                    $t("message.please_check_your_order_list")
                }}</span>
            </h3>
            <router-link
                @click.prevent="closeOrderNotificationModal"
                :to="{ path: '/admin/' + getUrl() }"
                class="db-btn h-[38px] shadow-[0px_6px_10px_rgba(255,_0,_107,_0.24)] bg-primary text-white"
            >
                {{ $t("button.let_me_check") }}
            </router-link>
        </div>
    </div>

    <!-- Extracted Popups -->
    <BackendPopupsComponent ref="backendPopups" />
</template>

<script>
import activityEnum from "../../../enums/modules/activityEnum";
import _ from "lodash";
import alertService from "../../../services/alertService";
import appService from "../../../services/appService";
import axios from "axios";
import BackendPopupsComponent from "../../admin/components/BackendPopupsComponent.vue";

export default {
    name: "BackendNavbarComponent",
    components: {
        BackendPopupsComponent,
    },
    data() {
        return {
            loading: {
                isActive: false,
            },
            enums: {
                activityEnum: activityEnum,
            },
            defaultBranch: null,
            sidebarOpen: true,
            pos: {
                permission: false,
                url: "",
            },
            branchProps: {
                paginate: 0,
                order_column: "id",
                order_type: "asc",
            },
            orderNotificationStatus: false,
            orderNotificationMessage: "",
            orderNotification: {
                tablePermission: false,
                tableUrl: "",
                permission: false,
                url: "",
                orderType: null,
            },

            // Notification bell
            notifOpen: false,
            notifications: [],
            notifLoading: false,
            readingNotifications: {}, // Track which notifications are being marked as read

            // Real-time polling
            prevUnreadCount: 0,
            pollingInterval: null,
        };
    },
    computed: {
        setting: function () {
            return this.$store.getters["frontendSetting/lists"];
        },
        authInfo: function () {
            return this.$store.getters.authInfo;
        },
        authBranch: function () {
            return this.$store.getters.authBranchId;
        },
        branches: function () {
            return this.$store.getters["backendGlobalState/branches"];
        },
        branch: function () {
            return this.$store.getters["backendGlobalState/branchShow"];
        },
        languages: function () {
            return this.$store.getters["frontendLanguage/lists"];
        },
        language: function () {
            return this.$store.getters["frontendLanguage/show"];
        },
        permissions: function () {
            return this.$store.getters.authPermission;
        },
        defaultPermission: function () {
            return this.$store.getters.authDefaultPermission;
        },
        defaultMenu: function () {
            return this.$store.getters.authDefaultMenu;
        },
        sidebar() {
            return this.$store.getters["globalState/lists"].topSidebar;
        },
        unreadCount() {
            return this.notifications.filter((n) => !n.read_at).length;
        },
    },
    mounted() {
        appService.responsiveLoad();
        this.$store.dispatch("globalState/set", {
            topSidebar: this.sidebarOpen,
        });
        this.$store
            .dispatch("defaultAccess/show")
            .then((res) => {
                this.defaultBranch = res.data.data.branch_id;
                this.$store
                    .dispatch(
                        "backendGlobalState/branchShow",
                        res.data.data.branch_id,
                    )
                    .then()
                    .catch();
            })
            .catch();
        this.$store
            .dispatch("backendGlobalState/branches", this.branchProps)
            .then()
            .catch();

        this.orderPermissionCheck();
        this.posPermissionCheck();
        document.addEventListener("click", (e) => {
            if (
                this.$refs.notifDropdown &&
                !this.$refs.notifDropdown.contains(e.target)
            ) {
                this.notifOpen = false;
            }
        });

        // Initial fetch then poll every 15 seconds
        this.fetchNotifications();
        this.pollingInterval = setInterval(() => {
            this.fetchNotifications(true); // silent = true (no loading spinner)
        }, 15000);
    },
    beforeUnmount() {
        if (this.pollingInterval) clearInterval(this.pollingInterval);
    },
    methods: {


        textShortener: function (text, number = 30) {
            return appService.textShortener(text, number);
        },
        logout: function () {
            this.$store
                .dispatch("logout")
                .then((res) => {
                    this.$router.push({ name: "auth.admin.login" });
                })
                .catch();
        },
        handleSidebar: function () {
            this.sidebarOpen = !this.sidebar;
            this.$store.dispatch("globalState/set", {
                topSidebar: this.sidebarOpen,
            });

            if (
                document
                    ?.querySelector(".db-sidebar")
                    ?.classList?.contains("active")
            ) {
                document
                    ?.querySelector(".db-main")
                    ?.classList?.remove("expand");
                document
                    ?.querySelector(".db-sidebar")
                    ?.classList?.remove("active");
            } else {
                document
                    ?.querySelector(".db-sidebar")
                    ?.classList?.add("active");
                document?.querySelector(".db-main")?.classList?.add("expand");
            }
        },
        changeBranch: function (id) {
            this.$store
                .dispatch("defaultAccess/saveOrUpdate", { branch_id: id })
                .then((res) => {
                    this.$store
                        .dispatch("backendGlobalState/branchShow", id)
                        .then((res) => {
                            location.reload();
                        })
                        .catch();
                });
        },
        changeLanguage: function (id, code) {
            this.defaultLanguage = id;
            this.$store
                .dispatch("globalState/set", {
                    language_id: id,
                    language_code: code,
                })
                .then((res) => {
                    this.$store
                        .dispatch("frontendLanguage/show", id)
                        .then((res) => {
                            this.$i18n.locale = res.data.data.code;
                        })
                        .catch();
                })
                .catch();
        },
        posPermissionCheck: function () {
            const permissions = this.$store.getters.authPermission;
            if (permissions.length > 0) {
                _.forEach(permissions, (permission) => {
                    if (permission.name === "pos") {
                        if (permission.access === true) {
                            this.pos.permission = true;
                            this.pos.url = permission.url;
                        }
                    }
                });
            }
        },
        saveImage: function () {
            if (this.$refs.imageProperty.files[0]) {
                try {
                    this.loading.isActive = true;
                    const formData = new FormData();
                    formData.append("image", this.$refs.imageProperty.files[0]);
                    this.$store
                        .dispatch("frontendEditProfile/changeImage", {
                            form: formData,
                        })
                        .then((res) => {
                            this.$store
                                .dispatch("updateAuthInfo", res.data.data)
                                .then((res) => {
                                    this.loading.isActive = false;
                                    alertService.success(
                                        this.$t("message.photo_update"),
                                    );
                                    this.$refs.imageProperty.value = null;
                                })
                                .catch((err) => {
                                    this.loading.isActive = false;
                                    alertService.error(err);
                                });
                        })
                        .catch((err) => {
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
                    if (permission.name === "online-orders") {
                        if (permission.access === true) {
                            this.orderNotification.permission = true;
                            this.orderNotification.url = permission.url;
                        }
                    }

                    if (permission.name === "table-orders") {
                        if (permission.access === true) {
                            this.orderNotification.tablePermission = true;
                            this.orderNotification.tableUrl = permission.url;
                        }
                    }
                });
            }
        },
        getUrl: function () {
            return this.orderNotification.orderType === "table"
                ? this.orderNotification.tableUrl
                : this.orderNotification.url;
        },
        closeOrderNotificationModal: function () {
            const modalTarget = this.$refs.orderNotificationModal;
            modalTarget?.classList?.remove("active");
            document.body.style.overflowY = "auto";
            this.loading.isActive = false;
            this.orderNotificationStatus = false;
            this.orderNotification.orderType = null;
        },

        closeFullScreen: function () {
            if (
                document.fullscreenElement ||
                document.webkitFullscreenElement
            ) {
                const elementDbCustomerMain =
                    document?.querySelector(".db-main-customer");
                const headerDiv = document?.querySelector(".db-header");
                elementDbCustomerMain?.classList.remove(
                    "db-main-customer",
                    "customer-display",
                );
                elementDbCustomerMain?.classList.add("db-main");
                elementDbCustomerMain?.classList.remove("hiddenHeader");
                headerDiv?.classList.remove("active", "hidden");
                document?.exitFullscreen();
            }
            document.removeEventListener("mousemove", handleMouseMove);
        },

        fullScreen: function (event) {
            if (this.$route.path.includes("order-status-screen")) {
                const elementDbMain = document?.querySelector(".db-main");
                const elementDbCustomerMain =
                    document?.querySelector(".db-main-customer");
                const headerDiv = document.querySelector(".db-header");

                if (elementDbMain) {
                    elementDbMain.classList.remove("db-main");
                    elementDbMain.classList.add(
                        "db-main-customer",
                        "customer-display",
                    );
                    elementDbMain.classList.add("hiddenHeader");
                    headerDiv.classList.add("active", "hidden");
                } else {
                    elementDbCustomerMain.classList.remove(
                        "db-main-customer",
                        "customer-display",
                    );
                    elementDbCustomerMain.classList.add("db-main");
                    elementDbCustomerMain.classList.remove("hiddenHeader");
                    headerDiv.classList.remove("active", "hidden");
                }
            }

            this.toggleFullscreen();
        },
        toggleFullscreen: function () {
            let elem = document.documentElement;
            if (!document.fullscreenElement) {
                if (elem.requestFullscreen) {
                    elem.requestFullscreen();
                } else if (elem.webkitRequestFullscreen) {
                    elem.webkitRequestFullscreen();
                } else if (elem.mozRequestFullScreen) {
                    elem.mozRequestFullScreen();
                } else if (elem.msRequestFullscreen) {
                    elem.msRequestFullscreen();
                }
                document.addEventListener("mousemove", handleMouseMove);
            } else {
                if (document.exitFullscreen) {
                    document.exitFullscreen();
                } else if (document.webkitExitFullscreen) {
                    document.webkitExitFullscreen();
                } else if (document.mozCancelFullScreen) {
                    document.mozCancelFullScreen();
                } else if (document.msExitFullscreen) {
                    document.msExitFullscreen();
                }
                document.removeEventListener("mousemove", handleMouseMove);
            }
        },
        toggleNotifDropdown() {
            this.notifOpen = !this.notifOpen;
            if (this.notifOpen) this.fetchNotifications();
        },

        fetchNotifications(silent = false) {
            if (!silent) this.notifLoading = true;
            axios
                .get("/admin/notifications", {
                    params: { page: 1, per_page: 20 },
                })
                .then((res) => {
                    // Handle different response formats
                    let data = [];
                    if (res.data && Array.isArray(res.data.data)) {
                        data = res.data.data;
                    } else if (res.data && Array.isArray(res.data)) {
                        data = res.data;
                    }

                    if (Array.isArray(data)) {
                        const incoming = data.map((notif) =>
                            this.validateNotification(notif),
                        );
                        const newUnreadCount = incoming.filter(
                            (n) => !n.read_at,
                        ).length;

                        // Detect new notifications on silent (background) polls
                        if (
                            silent &&
                            newUnreadCount > this.prevUnreadCount &&
                            incoming.length > 0
                        ) {
                            // Find the newest unread notification to show in toast
                            const newest =
                                incoming.find((n) => !n.read_at) || incoming[0];
                            this.$refs.backendPopups?.showToast(newest);
                        }

                        this.prevUnreadCount = newUnreadCount;
                        this.notifications = incoming;
                    } else {
                        this.notifications = [];
                    }
                })
                .catch((error) => {
                    // Silently ignore background poll errors
                    if (!silent) {
                        console.error("Failed to fetch notifications:", error);
                        this.notifications = [];
                        alertService.error(
                            this.$t("message.failed_to_load_notifications") ||
                                "Failed to load notifications",
                        );
                    }
                })
                .finally(() => {
                    if (!silent) this.notifLoading = false;
                });
        },



        validateNotification(notif) {
            // Ensure all required fields exist
            return {
                id: notif.id || null,
                read_at: notif.read_at || null,
                created_at: notif.created_at || new Date().toISOString(),
                data: {
                    message: notif.data?.message || "No message",
                    url: notif.data?.url || null,
                    ...(notif.data || {}),
                },
            };
        },

        readNotification(notif) {
            // Validate notification
            if (!notif || !notif.id) {
                console.error("Invalid notification");
                return;
            }

            // Close the dropdown
            this.notifOpen = false;

            if (!notif.read_at) {
                // Set loading state for this notification (Vue 3 reactive)
                this.readingNotifications = {
                    ...this.readingNotifications,
                    [notif.id]: true,
                };

                axios
                    .post(`/admin/notifications/${notif.id}/read`)
                    .then(() => {
                        // Update after confirmation (not optimistic)
                        notif.read_at = new Date().toISOString();
                    })
                    .catch((error) => {
                        console.error(
                            "Failed to mark notification as read:",
                            error,
                        );
                        alertService.error(
                            this.$t("message.failed_to_read_notification") ||
                                "Failed to read notification",
                        );
                    })
                    .finally(() => {
                        this.readingNotifications = {
                            ...this.readingNotifications,
                            [notif.id]: false,
                        };
                    });
            }

            // Navigate to order details if notif has a valid url
            if (notif.data?.url && typeof notif.data.url === "string") {
                this.$router.push(notif.data.url);
            }
        },

        markAllRead() {
            if (this.notifications.length === 0) return;

            this.notifLoading = true;
            axios
                .post("/admin/notifications/read-all")
                .then(() => {
                    const now = new Date().toISOString();
                    this.notifications.forEach((n) => {
                        n.read_at = now;
                    });
                    alertService.success(
                        this.$t("message.marked_all_as_read") ||
                            "All notifications marked as read",
                    );
                })
                .catch((error) => {
                    console.error("Failed to mark all as read:", error);
                    alertService.error(
                        this.$t("message.failed_to_mark_all_read") ||
                            "Failed to mark all as read",
                    );
                })
                .finally(() => {
                    this.notifLoading = false;
                });
        },

        timeAgo(dateStr) {
            const diff = Date.now() - new Date(dateStr).getTime();
            const m = Math.floor(diff / 60000);
            if (m < 1) return "just now";
            if (m < 60) return `${m} min ago`;
            const h = Math.floor(m / 60);
            if (h < 24) return `${h} hr ago`;
            return `${Math.floor(h / 24)} d ago`;
        },
    },
};
</script>

<style>
/* CSS logic moved to BackendPopupsComponent.vue */
</style>
