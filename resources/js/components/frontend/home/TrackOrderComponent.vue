<template>
    <LoadingComponent :props="loading" />

    <div v-if="logged && activeOrders.length > 0" class="fixed bottom-16 left-0 right-0 z-40 w-full rounded-t-2xl bg-primary shadow-[0_-4px_20px_rgba(0,0,0,0.1)] 
           md:static md:bottom-auto md:rounded-none md:shadow-none">
        <div class="relative mx-auto flex w-full max-w-[930px] items-center gap-4 px-5 py-3 md:px-10">

            <!-- Icon -->
            <i class="lab lab-routing lab-font-size-24 text-white"></i>

            <!-- Text Content -->
            <div class="flex-auto z-10">
                <h3 class="mb-0.5 text-sm font-bold capitalize text-white md:text-base">
                    {{ $t('message.order_process_stage') }}
                </h3>

                <!-- Status Messages -->
                <p v-if="Object.keys(currentOrder).length > 0 && parseInt(currentOrder.status) === parseInt(enums.orderStatusEnum.ACCEPT)"
                    class="text-xs font-medium text-white/90 md:text-sm">
                    {{ $t('message.order_confirmed') }}
                </p>

                <p v-if="Object.keys(currentOrder).length > 0 && parseInt(currentOrder.status) === parseInt(enums.orderStatusEnum.PREPARING)"
                    class="text-xs font-medium text-white/90 md:text-sm">
                    {{ $t('message.order_processed') }}
                </p>

                <p v-if="Object.keys(currentOrder).length > 0 && parseInt(currentOrder.status) === parseInt(enums.orderStatusEnum.OUT_FOR_DELIVERY)"
                    class="text-xs font-medium text-white/90 md:text-sm">
                    {{ $t('message.order_out_for_delivery') }}
                </p>
            </div>

            <!-- Decorative Image (Hidden on very small mobile to save space, visible on slightly larger screens) -->
            <!-- Adjusted positioning to not overlap text -->
            <img v-if="settings.image_order_track" class="hidden sm:block absolute bottom-0 left-1/2 -translate-x-1/2 h-[120%] object-contain opacity-20 pointer-events-none 
               md:static md:h-auto md:w-auto md:opacity-100 md:translate-x-0 md:mx-auto"
                :src="settings.image_order_track" alt="tracking-bg">

            <!-- Action Button -->
            <router-link v-if="Object.keys(currentOrder).length > 0"
                :to="{ name: 'frontend.myOrder.details', params: { id: currentOrder.id } }"
                class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-full bg-white text-primary shadow-sm transition-transform hover:scale-105">
                <i class="lab lab-arrow-right-long lab-font-size-24"></i>
            </router-link>

        </div>
    </div>
</template>

<script>
import LoadingComponent from "../../frontend/components/LoadingComponent";
import orderStatusEnum from "../../../enums/modules/orderStatusEnum";

export default {
    name: "TrackOrderComponent",
    components: { LoadingComponent },
    data() {
        return {
            loading: {
                isActive: false,
            },
            enums: {
                orderStatusEnum: orderStatusEnum,
            },
            currentOrder: {},
            activeProps: {
                excepts: orderStatusEnum.DELIVERED + "|" + orderStatusEnum.CANCELED + "|" + orderStatusEnum.REJECTED + "|" + orderStatusEnum.RETURNED + "|" + orderStatusEnum.PENDING,
                order_column: "id",
                order_by: "asc"
            },
        }
    },
    computed: {
        logged: function () {
            return this.$store.getters.authStatus;
        },
        activeOrders: function () {
            return this.$store.getters['frontendOrder/activeOrder'];
        },
        settings: function () {
            return this.$store.getters['frontendSetting/lists'];
        }
    },
    mounted() {
        try {
            if (this.$store.getters.authStatus) {
                this.$store.dispatch('frontendOrder/activeOrder', this.activeProps).then(res => {
                    if (res.data.data.length > 0) {
                        this.currentOrder = res.data.data[0];
                    }
                }).catch((err) => {
                });
            }
        } catch (err) {
        }
    },
    watch: {
        activeOrders: {
            deep: true,
            handler(activeOrders) {
                if (activeOrders.length > 0) {
                    this.currentOrder = activeOrders[0];
                }
            }
        }
    }
}
</script>