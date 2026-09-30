<template>
    <!-- Unviewed Orders Alert Modal -->
    <transition name="notif-toast">
        <div
            v-if="unviewedAlertVisible"
            class="fixed inset-0 z-[9998] flex items-center justify-center bg-black/40"
            @click.self="dismissUnviewedAlert"
        >
            <div
                class="relative w-full max-w-[380px] mx-4 bg-white rounded-2xl shadow-2xl overflow-hidden"
            >
                <!-- Top accent bar -->
                <div
                    class="h-1.5 w-full bg-gradient-to-r from-primary to-orange-400"
                ></div>

                <!-- Content -->
                <div class="p-6 text-center">
                    <!-- Animated bell icon -->
                    <div
                        class="w-16 h-16 rounded-full bg-[#FFEDF4] flex items-center justify-center mx-auto mb-4"
                    >
                        <i
                            class="fa-solid fa-bell text-primary text-2xl blinking"
                        ></i>
                    </div>

                    <h3 class="text-lg font-bold text-heading mb-1">
                        {{ unviewedCount }} Unviewed Order{{
                            unviewedCount !== 1 ? "s" : ""
                        }}
                    </h3>
                    <p class="text-sm text-gray-500 mb-5">
                        You have pending orders waiting for your attention.
                    </p>

                    <div class="flex items-center gap-3">
                        <button
                            @click="dismissUnviewedAlert"
                            class="flex-1 h-[38px] rounded-lg border border-gray-200 text-sm text-gray-600 hover:bg-gray-50 transition"
                        >
                            Dismiss
                        </button>
                        <router-link
                            :to="
                                unviewedCount === 1 && unviewedSingleOrderId
                                    ? (unviewedSingleOrderType === enums.orderTypeEnum.DINING_TABLE ? '/admin/table-orders/show/' + unviewedSingleOrderId : '/admin/online-orders/show/' + unviewedSingleOrderId)
                                    : '/admin/online-orders'
                            "
                            @click="dismissUnviewedAlert"
                            class="flex-1 h-[38px] flex items-center justify-center rounded-lg bg-primary text-white text-sm font-medium shadow-[0px_4px_10px_rgba(255,_0,_107,_0.3)] hover:opacity-90 transition"
                        >
                            {{ unviewedCount === 1 ? 'View Order' : 'View Orders' }}
                        </router-link>
                    </div>
                </div>

                <!-- Close button -->
                <button
                    @click="dismissUnviewedAlert"
                    class="absolute top-3 right-3 w-7 h-7 flex items-center justify-center rounded-full bg-gray-100 hover:bg-gray-200 transition text-gray-500"
                >
                    <i class="fa-solid fa-xmark text-xs"></i>
                </button>
            </div>
        </div>
    </transition>

    <!-- Chef Unviewed Orders Alert Modal -->
    <transition name="notif-toast">
        <div
            v-if="chefUnviewedAlertVisible"
            class="fixed inset-0 z-[9998] flex items-center justify-center bg-black/40"
            @click.self="dismissChefUnviewedAlert"
        >
            <div
                class="relative w-full max-w-[380px] mx-4 bg-white rounded-2xl shadow-2xl overflow-hidden"
            >
                <!-- Top accent bar -->
                <div
                    class="h-1.5 w-full bg-gradient-to-r from-primary to-orange-400"
                ></div>

                <!-- Content -->
                <div class="p-6 text-center">
                    <!-- Animated bell icon -->
                    <div
                        class="w-16 h-16 rounded-full bg-[#FFEDF4] flex items-center justify-center mx-auto mb-4"
                    >
                        <i
                            class="fa-solid fa-bell text-primary text-2xl blinking"
                        ></i>
                    </div>

                    <template v-if="chefUnviewedCount === 1 && chefUnviewedSingleOrder">
                        <h3 class="text-lg font-bold text-heading mb-1">
                            New Order: #{{ chefUnviewedSingleOrder.order_serial_no }}
                        </h3>
                        <div class="text-sm text-gray-500 mb-5 bg-gray-50 rounded-lg p-3 text-left border border-gray-100">
                            <p class="mb-1"><span class="font-medium">Type:</span> <span class="capitalize">{{ chefUnviewedSingleOrder.order_type_name }}</span></p>
                            <p class="mb-1" v-if="chefUnviewedSingleOrder.delivery_time"><span class="font-medium">Time:</span> {{ chefUnviewedSingleOrder.delivery_time }}</p>
                            <p class="mb-0" v-if="chefUnviewedSingleOrder.token"><span class="font-medium">Token:</span> {{ chefUnviewedSingleOrder.token }}</p>
                        </div>
                    </template>
                    <template v-else>
                        <h3 class="text-lg font-bold text-heading mb-1">
                            {{ chefUnviewedCount }} New Order{{
                                chefUnviewedCount !== 1 ? "s" : ""
                            }} for Kitchen
                        </h3>
                        <p class="text-sm text-gray-500 mb-5">
                            There are new accepted orders waiting to be prepared.
                        </p>
                    </template>

                    <div class="flex items-center gap-3">
                        <button
                            @click="dismissChefUnviewedAlert"
                            class="flex-1 h-[38px] rounded-lg border border-gray-200 text-sm text-gray-600 hover:bg-gray-50 transition"
                        >
                            Dismiss
                        </button>
                        <button
                            v-if="chefUnviewedCount === 1 && chefUnviewedSingleOrder"
                            @click="markAndRedirectToKds(chefUnviewedSingleOrder.id)"
                            class="flex-1 h-[38px] flex items-center justify-center rounded-lg bg-primary text-white text-sm font-medium shadow-[0px_4px_10px_rgba(255,_0,_107,_0.3)] hover:opacity-90 transition"
                        >
                            Mark as Seen
                        </button>
                        <router-link
                            v-else
                            to="/admin/kitchen-display-system"
                            @click="dismissChefUnviewedAlert"
                            class="flex-1 h-[38px] flex items-center justify-center rounded-lg bg-primary text-white text-sm font-medium shadow-[0px_4px_10px_rgba(255,_0,_107,_0.3)] hover:opacity-90 transition"
                        >
                            View Orders
                        </router-link>
                    </div>
                </div>

                <!-- Close button -->
                <button
                    @click="dismissChefUnviewedAlert"
                    class="absolute top-3 right-3 w-7 h-7 flex items-center justify-center rounded-full bg-gray-100 hover:bg-gray-200 transition text-gray-500"
                >
                    <i class="fa-solid fa-xmark text-xs"></i>
                </button>
            </div>
        </div>
    </transition>

    <!-- Notification Toast Popup -->
    <transition name="notif-toast">
        <div
            v-if="toastVisible && toastNotif"
            class="notif-toast-wrap fixed top-5 ltr:right-5 rtl:left-5 z-[9999] w-[320px] rounded-xl shadow-xl bg-white border border-[#EFF0F6] overflow-hidden"
        >
            <!-- Accent bar -->
            <div class="h-1 w-full bg-primary"></div>
            <div class="flex items-start gap-3 p-4">
                <!-- Bell icon -->
                <div
                    class="flex-shrink-0 w-9 h-9 rounded-full bg-[#FFEDF4] flex items-center justify-center mt-0.5"
                >
                    <i class="fa-solid fa-bell text-primary text-sm"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-semibold text-heading mb-0.5">
                        {{ $t("label.notifications") }}
                    </p>
                    <p class="text-xs text-gray-600 leading-5 line-clamp-3">
                        {{
                            toastNotif.data && toastNotif.data.message
                                ? toastNotif.data.message
                                : "New notification"
                        }}
                    </p>
                    <p class="text-[11px] text-gray-400 mt-1">
                        {{ $t("label.just_now") || "just now" }}
                    </p>
                </div>
                <button
                    @click="dismissToast"
                    class="flex-shrink-0 text-gray-400 hover:text-gray-600 transition ml-1"
                >
                    <i class="fa-solid fa-xmark text-xs"></i>
                </button>
            </div>
            <!-- Progress bar -->
            <div
                class="notif-toast-progress bg-primary h-0.5"
                :style="{ animationDuration: toastDuration + 'ms' }"
            ></div>
        </div>
    </transition>
</template>

<script>
import axios from "axios";
import orderTypeEnum from "../../../enums/modules/orderTypeEnum";

export default {
    name: "BackendPopupsComponent",
    data() {
        return {
            enums: {
                orderTypeEnum: orderTypeEnum,
            },
            unviewedAlertVisible: false,
            unviewedCount: 0,
            unviewedSingleOrderId: null,
            unviewedSingleOrderType: null,
            unviewedAlertInterval: null,

            chefUnviewedAlertVisible: false,
            chefUnviewedCount: 0,
            chefUnviewedSingleOrderId: null,
            chefUnviewedSingleOrder: null,
            chefUnviewedAlertInterval: null,

            toastNotif: null,
            toastVisible: false,
            toastTimer: null,
            toastDuration: 5000,
            audioLoopInterval: null,
            audioInstance: null,
        };
    },
    computed: {
        setting() {
            return this.$store.getters["frontendSetting/lists"];
        },
        authInfo() {
            return this.$store.getters.authInfo;
        },
    },
    mounted() {
        // Check for unviewed orders immediately then every 30 seconds (admin only)
        if (this.authInfo?.role_id === 1) {
            this.fetchUnviewedOrders();
            this.unviewedAlertInterval = setInterval(() => {
                this.fetchUnviewedOrders();
            }, 30000);
        }

        // Check for chef unviewed orders after 30 seconds then every 30 seconds (chef only)
        if (this.authInfo?.role_id === 5) {
            this.chefUnviewedAlertInterval = setInterval(() => {
                this.fetchChefUnviewedOrders();
            }, 30000);
        }
    },
    beforeUnmount() {
        if (this.unviewedAlertInterval) clearInterval(this.unviewedAlertInterval);
        if (this.chefUnviewedAlertInterval) clearInterval(this.chefUnviewedAlertInterval);
        if (this.toastTimer) clearTimeout(this.toastTimer);
        this.stopNotificationSoundLoop();
    },
    methods: {
        fetchUnviewedOrders() {
            if (this.authInfo?.role_id !== 1) return;
            axios.get("/admin/notifications/unviewed-count")
                .then((res) => {
                    const count = res.data?.count ?? 0;
                    this.unviewedCount = count;
                    this.unviewedSingleOrderId = res.data?.single_order_id ?? null;
                    this.unviewedSingleOrderType = res.data?.single_order_type ?? null;
                    if (count > 0) {
                        this.unviewedAlertVisible = true;
                        this.playNotificationSound();
                    }
                })
                .catch((err) => console.error("Failed to fetch unviewed count", err));
        },
        fetchChefUnviewedOrders() {
            if (this.authInfo?.role_id !== 5) return;
            axios.get("/admin/notifications/chef-unviewed-count")
                .then((res) => {
                    const count = res.data?.count ?? 0;
                    this.chefUnviewedCount = count;
                    this.chefUnviewedSingleOrderId = res.data?.single_order_id ?? null;
                    this.chefUnviewedSingleOrder = res.data?.single_order ?? null;
                    if (count > 0) {
                        this.chefUnviewedAlertVisible = true;
                        this.playNotificationSound();
                    }
                })
                .catch((err) => console.error("Failed to fetch chef unviewed count", err));
        },
        dismissUnviewedAlert() {
            this.unviewedAlertVisible = false;
            this.stopNotificationSoundLoop();
        },
        dismissChefUnviewedAlert() {
            this.chefUnviewedAlertVisible = false;
            this.stopNotificationSoundLoop();
        },
        markAndRedirectToKds(orderId) {
            this.dismissChefUnviewedAlert();
            axios.post(`/admin/kds-order/mark-viewed/${orderId}`)
                .then(() => {
                    if (this.$route.path.includes('kitchen-display-system')) {
                        window.dispatchEvent(new Event('refresh-kds-orders'));
                    } else {
                        this.$router.push('/admin/kitchen-display-system');
                    }
                })
                .catch((err) => {
                    console.error("Failed to mark order as viewed", err);
                    if (this.$route.path.includes('kitchen-display-system')) {
                        window.dispatchEvent(new Event('refresh-kds-orders'));
                    } else {
                        this.$router.push('/admin/kitchen-display-system');
                    }
                });
        },
        showToast(notif) {
            if (this.toastTimer) clearTimeout(this.toastTimer);
            this.toastNotif = notif;
            this.toastVisible = true;
            this.toastTimer = setTimeout(() => {
                this.dismissToast();
            }, this.toastDuration);
        },
        dismissToast() {
            this.toastVisible = false;
            this.toastNotif = null;
            if (this.toastTimer) {
                clearTimeout(this.toastTimer);
                this.toastTimer = null;
            }
        },
        playNotificationSound() {
            this.stopNotificationSoundLoop();
            try {
                const src = this.setting && this.setting.notification_audio ? this.setting.notification_audio : null;
                if (src) {
                    this.audioInstance = new Audio(src);
                    this.audioInstance.volume = 0.7;
                    this.audioInstance.play().catch(() => {});

                    this.audioLoopInterval = setInterval(() => {
                        if (this.unviewedAlertVisible || this.chefUnviewedAlertVisible) {
                            if (this.audioInstance) {
                                this.audioInstance.currentTime = 0;
                                this.audioInstance.play().catch(() => {});
                            }
                        } else {
                            this.stopNotificationSoundLoop();
                        }
                    }, 3000);
                }
            } catch (e) {}
        },
        stopNotificationSoundLoop() {
            if (this.audioLoopInterval) {
                clearInterval(this.audioLoopInterval);
                this.audioLoopInterval = null;
            }
            if (this.audioInstance) {
                this.audioInstance.pause();
                this.audioInstance = null;
            }
        },
    }
};
</script>

<style scoped>
@keyframes blink {
    0% { opacity: 1; }
    50% { opacity: 0.5; }
    100% { opacity: 1; }
}
.blinking {
    animation: blink 1.5s infinite;
}
.notif-toast-enter-active {
    animation: toastSlideIn 0.35s cubic-bezier(0.34, 1.56, 0.64, 1);
}
.notif-toast-leave-active {
    animation: toastSlideOut 0.25s ease-in forwards;
}
@keyframes toastSlideIn {
    from { opacity: 0; transform: translateX(110%); }
    to { opacity: 1; transform: translateX(0); }
}
@keyframes toastSlideOut {
    from { opacity: 1; transform: translateX(0); }
    to { opacity: 0; transform: translateX(110%); }
}
.notif-toast-progress {
    animation: toastProgress linear forwards;
    transform-origin: left;
}
@keyframes toastProgress {
    from { transform: scaleX(1); }
    to { transform: scaleX(0); }
}
.line-clamp-3 {
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
