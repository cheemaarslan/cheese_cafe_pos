<template>
    <LoadingComponent :props="loading" />
    <section class="min-h-[80vh] bg-gray-50/50 pt-10 pb-20">
        <div class="container max-w-4xl mx-auto px-4">

            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
                <router-link :to="{ name: 'frontend.home' }"
                    class="group inline-flex items-center gap-2 text-sm font-medium text-gray-500 hover:text-primary transition-colors">
                    <span
                        class="w-8 h-8 rounded-full bg-white border border-gray-200 flex items-center justify-center shadow-sm group-hover:border-primary/30 group-hover:shadow-md transition-all">
                        <i class="lab lab-undo transition-transform group-hover:-translate-x-0.5"></i>
                    </span>
                    <span>{{ $t('label.back_to_home') }}</span>
                </router-link>

                <router-link :to="{ name: 'frontend.profile' }"
                    class="inline-flex items-center gap-2 px-5 py-2.5 bg-white border border-gray-200 text-gray-700 text-sm font-semibold rounded-full shadow-sm hover:bg-gray-50 hover:text-primary hover:border-primary/20 transition-all">
                    <i class="lab lab-arrow-left"></i>
                    {{ $t('label.back_to_profile') }}
                </router-link>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
                <div
                    class="md:col-span-2 relative overflow-hidden rounded-3xl bg-primary text-white shadow-2xl shadow-primary/30 group">
                    <div class="absolute top-0 right-0 -mr-10 -mt-10 w-64 h-64 rounded-full bg-white/10 blur-3xl"></div>
                    <div class="absolute bottom-0 left-0 -ml-10 -mb-10 w-40 h-40 rounded-full bg-black/20 blur-2xl">
                    </div>
                    <div class="absolute inset-0 bg-gradient-to-br from-white/10 to-transparent opacity-50"></div>

                    <div class="relative p-8 h-full flex flex-col justify-between z-10">
                        <div class="flex justify-between items-start">
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-5 rounded flex items-center justify-center bg-yellow-400/80">
                                    <i class="fa-regular fa-credit-card"></i>
                                </div> <span class="text-white/60 text-xs font-mono tracking-widest">LOYALTY CARD</span>
                            </div>
                            <i class="fa-solid fa-award text-3xl text-white/90 drop-shadow-md"></i>
                        </div>

                        <div class="py-4">
                            <span class="block text-white/70 text-sm font-medium mb-1">{{ $t('label.total_balance')
                                }}</span>
                            <div class="flex items-baseline gap-2">
                                <span class="text-5xl font-black tracking-tight drop-shadow-sm">{{ totalPoints }}</span>
                                <span class="text-lg font-medium opacity-80">pts</span>
                            </div>
                        </div>

                        <div class="flex justify-between items-end border-t border-white/10 pt-4 mt-2">
                            <div>
                                <p class="text-[10px] text-white/50 uppercase tracking-wider">MEMBER STATUS</p>
                                <p class="font-bold tracking-wide text-sm">PREMIUM</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex flex-col gap-4">
                    <div
                        class="flex-1 bg-white rounded-2xl p-5 border border-green-100 shadow-lg shadow-green-100/50 flex flex-col justify-center relative overflow-hidden">
                        <div class="absolute -right-4 -top-4 w-16 h-16 bg-green-50 rounded-full blur-xl"></div>
                        <p class="text-sm text-gray-500 font-medium relative z-10">{{ $t('label.active_points') }}</p>
                        <div class="flex items-center gap-2 mt-1 relative z-10">
                            <i class="fa-solid fa-circle-check text-green-500"></i>
                            <span class="text-2xl font-bold text-gray-800">{{ activePoints }}</span>
                        </div>
                    </div>

                    <div
                        class="flex-1 bg-white rounded-2xl p-5 border border-red-100 shadow-lg shadow-red-100/50 flex flex-col justify-center relative overflow-hidden">
                        <div class="absolute -right-4 -top-4 w-16 h-16 bg-red-50 rounded-full blur-xl"></div>
                        <p class="text-sm text-gray-500 font-medium relative z-10">{{ $t('label.expired_points') }}</p>
                        <div class="flex items-center gap-2 mt-1 relative z-10">
                            <i class="fa-solid fa-circle-xmark text-red-400"></i>
                            <span class="text-2xl font-bold text-gray-800">{{ expiredPoints }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-3xl shadow-xl shadow-gray-200/60 border border-gray-100 overflow-hidden">
                <div class="p-6 border-b border-gray-100 bg-gray-50/50 flex justify-between items-center">
                    <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                        <i class="lab lab-history text-primary"></i>
                        {{ $t('label.points_history') }}
                    </h3>
                </div>

                <div v-if="loyaltyPoints && loyaltyPoints.length > 0">
                    <div class="divide-y divide-gray-100">
                        <div v-for="point in loyaltyPoints" :key="point.id"
                            class="group p-5 hover:bg-gray-50 transition-all duration-300 flex flex-col sm:flex-row sm:items-center justify-between gap-4">

                            <div class="flex items-start gap-4">
                                <div class="w-12 h-12 rounded-2xl flex-shrink-0 flex items-center justify-center transition-colors shadow-sm"
                                    :class="point.status === 5 ? 'bg-green-100 text-green-600' : 'bg-red-50 text-red-500'">
                                    <i class="fa-solid"
                                        :class="point.status === 5 ? 'fa-arrow-trend-up' : 'fa-arrow-trend-down'"></i>
                                </div>

                                <div>
                                    <h4 class="font-bold text-gray-800 group-hover:text-primary transition-colors">
                                        {{ $t('label.order') }} #{{ point.order ? point.order.order_serial_no : 'N/A' }}
                                    </h4>
                                    <div class="flex flex-wrap items-center gap-x-3 gap-y-1 mt-1 text-sm">
                                        <span class="text-gray-500 flex items-center gap-1">
                                            <i class="lab lab-calendar-line text-xs"></i>
                                            {{ formatDate(point.earned_at) }}
                                        </span>
                                        <span class="w-1 h-1 rounded-full bg-gray-300 hidden sm:block"></span>
                                        <span class="text-gray-500 font-medium">
                                            Amt: {{ point.order_subtotal }}$
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div
                                class="flex items-center justify-between sm:justify-end gap-4 sm:text-right pl-16 sm:pl-0">
                                <div>
                                    <div class="text-xl font-black tracking-tight"
                                        :class="point.status === 5 ? 'text-green-600' : 'text-red-500'">
                                        {{ point.status === 5 ? ' Total Points: ' : '' }}{{ point.points }}
                                    </div>
                                    <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider shadow-sm"
                                        :class="point.status === 5
                                            ? 'bg-emerald-50 text-emerald-700 ring-1 ring-inset ring-emerald-600/20'
                                            : 'bg-rose-50 text-rose-700 ring-1 ring-inset ring-rose-600/20'">

                                        <span class="w-1.5 h-1.5 rounded-full"
                                            :class="point.status === 5 ? 'bg-emerald-500' : 'bg-rose-500'">
                                        </span>

                                        {{ point.status_text }}
                                    </div>
                                    <div v-if="point.expired_at" class="text-[11px] text-red-400 mt-1 font-medium">
                                        Exp: {{ formatDate(point.expired_at) }}
                                    </div>
                                </div>
                                <div
                                    class="hidden sm:block text-gray-300 group-hover:text-primary group-hover:translate-x-1 transition-all">
                                    <i class="lab lab-angle-right"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div v-else class="flex flex-col items-center justify-center py-16 text-center">
                    <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mb-4 shadow-inner">
                        <i class="lab lab-star text-4xl text-gray-300"></i>
                    </div>
                    <h4 class="text-xl font-bold text-gray-800 mb-2">{{ $t('message.no_loyalty_points') }}</h4>
                    <p class="text-gray-500 max-w-xs">{{ $t('message.start_earning_points') }}</p>
                    <router-link :to="{ name: 'frontend.home' }"
                        class="mt-6 px-6 py-2 bg-primary text-white text-sm font-bold rounded-lg shadow-lg shadow-primary/30 hover:bg-primary-dark transition-colors">
                        Shop Now
                    </router-link>
                </div>

                <div v-if="pagination && pagination.last_page > 1" class="p-6 border-t border-gray-100 bg-gray-50/30">
                    <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                        <div class="text-xs font-semibold text-gray-400 uppercase tracking-wide">
                            {{ $t('label.showing') }} <span class="text-gray-700">{{ pagination.from }}-{{ pagination.to
                                }}</span> {{ $t('label.of') }} <span class="text-gray-700">{{ pagination.total }}</span>
                        </div>
                        <div class="flex gap-1.5">
                            <button v-if="pagination.current_page > 1" @click="loadPage(pagination.current_page - 1)"
                                class="w-9 h-9 flex items-center justify-center rounded-lg border border-gray-200 bg-white text-gray-500 hover:bg-primary hover:text-white hover:border-primary transition-all shadow-sm">
                                <i class="lab lab-angle-left"></i>
                            </button>

                            <button v-for="page in visiblePages" :key="page" @click="loadPage(page)" :class="[
                                'w-9 h-9 flex items-center justify-center rounded-lg text-sm font-bold transition-all shadow-sm',
                                page === pagination.current_page
                                    ? 'bg-primary text-white border border-primary shadow-primary/30'
                                    : 'bg-white text-gray-600 border border-gray-200 hover:bg-gray-50'
                            ]">
                                {{ page }}
                            </button>

                            <button v-if="pagination.current_page < pagination.last_page"
                                @click="loadPage(pagination.current_page + 1)"
                                class="w-9 h-9 flex items-center justify-center rounded-lg border border-gray-200 bg-white text-gray-500 hover:bg-primary hover:text-white hover:border-primary transition-all shadow-sm">
                                <i class="lab lab-angle-right"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>

<script>
import LoadingComponent from "../../components/LoadingComponent";

export default {
    name: "LoyaltyPointsComponent",
    components: {
        LoadingComponent,
    },
    data() {
        return {
            loading: {
                isActive: false,
            },
        };
    },
    mounted() {
        this.loadLoyaltyPoints();
    },
    computed: {
        loyaltyPoints: function () {
            const points = this.$store.getters['frontendLoyaltyPoints/loyaltyPoints'];
            console.log('Loyalty Points:', points);
            return points;
        },
        totalPoints: function () {
            return this.$store.getters['frontendLoyaltyPoints/totalPoints'];
        },
        activePoints: function () {
            return this.$store.getters['frontendLoyaltyPoints/activePoints'];
        },
        expiredPoints: function () {
            return this.$store.getters['frontendLoyaltyPoints/expiredPoints'];
        },
        pagination: function () {
            return this.$store.getters['frontendLoyaltyPoints/pagination'];
        },
        visiblePages: function () {
            if (!this.pagination) return [];
            const current = this.pagination.current_page;
            const total = this.pagination.last_page;
            const pages = [];

            if (total <= 7) {
                for (let i = 1; i <= total; i++) {
                    pages.push(i);
                }
            } else {
                if (current <= 4) {
                    for (let i = 1; i <= 5; i++) {
                        pages.push(i);
                    }
                    pages.push('...');
                    pages.push(total);
                } else if (current >= total - 3) {
                    pages.push(1);
                    pages.push('...');
                    for (let i = total - 4; i <= total; i++) {
                        pages.push(i);
                    }
                } else {
                    pages.push(1);
                    pages.push('...');
                    for (let i = current - 1; i <= current + 1; i++) {
                        pages.push(i);
                    }
                    pages.push('...');
                    pages.push(total);
                }
            }

            return pages.filter(page => page !== '...');
        },
    },
    methods: {
        loadLoyaltyPoints: function (page = 1) {
            this.loading.isActive = true;
            this.$store.dispatch('frontendLoyaltyPoints/getCustomerLoyaltyPoints', {
                page: page,
                per_page: 10,
                vuex: true
            }).then(() => {
                this.loading.isActive = false;
                console.log('Loyalty points loaded successfully');
            }).catch((err) => {
                this.loading.isActive = false;
                console.error('Error loading loyalty points:', err);
            });
        },
        loadPage: function (page) {
            if (page !== '...' && page !== this.pagination.current_page) {
                this.loadLoyaltyPoints(page);
            }
        },
        formatDate: function (dateString) {
            if (!dateString) return 'N/A';
            const date = new Date(dateString);
            return date.toLocaleDateString('en-US', {
                year: 'numeric',
                month: 'short',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
        },
    },
};
</script>