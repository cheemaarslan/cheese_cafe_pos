<template>
    <LoadingComponent :props="loading" />
    <section class="min-h-[80vh] bg-gray-50/50 pt-10 pb-20">
        <div class="container max-w-5xl mx-auto px-4">
            <div class="mb-8">
                <router-link :to="{ name: 'frontend.home' }" 
                    class="group inline-flex items-center gap-2 px-5 py-2.5 rounded-full bg-white text-gray-600 font-medium text-sm shadow-sm border border-gray-100 transition-all hover:shadow-md hover:text-primary hover:border-primary/20">
                    <i class="lab lab-undo transition-transform group-hover:-translate-x-1"></i>
                    <span>{{ $t('label.back_to_home') }}</span>
                </router-link>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
                
                <div class="lg:col-span-8 space-y-6">
                    <div class="relative bg-white rounded-3xl shadow-xl shadow-gray-200/60 overflow-hidden border border-gray-100">
                        <div class="h-32 bg-gradient-to-r from-primary/90 to-primary/70 relative overflow-hidden">
                            <div class="absolute -bottom-10 -left-10 w-40 h-40 bg-white/10 rounded-full blur-2xl"></div>
                            <div class="absolute top-0 right-0 w-60 h-60 bg-black/5 rounded-full blur-3xl transform translate-x-1/2 -translate-y-1/2"></div>
                        </div>

                        <div class="px-8 pb-8 relative">
                            <div class="flex flex-col sm:flex-row items-center sm:items-end -mt-12 mb-8 gap-6">
                                <div class="relative group">
                                    <div class="w-32 h-32 rounded-full p-1.5 bg-white shadow-lg ring-1 ring-gray-100">
                                        <img :src="user.image" alt="Profile Image"
                                            class="w-full h-full object-cover rounded-full" 
                                            @error="handleImageError" />
                                    </div>
                                    <div class="absolute bottom-3 right-3 w-5 h-5 bg-green-500 border-4 border-white rounded-full"></div>
                                </div>
                                
                                <div class="text-center sm:text-left mb-2 flex-1">
                                    <h2 class="text-3xl font-bold text-gray-800 tracking-tight">
                                        {{ user.first_name }} {{ user.last_name }}
                                    </h2>
                                    <p class="text-gray-500 font-medium flex items-center justify-center sm:justify-start gap-2 mt-1">
                                        <i class="lab lab-mail text-primary"></i>
                                        {{ user.email }}
                                    </p>
                                </div>

                                <div class="hidden sm:flex gap-3">
                                    <router-link :to="{ name: 'frontend.editProfile' }"
                                        class="flex items-center justify-center w-10 h-10 rounded-full bg-primary/10 text-primary hover:bg-primary hover:text-white transition-all duration-300">
                                        <i class="lab lab-edit text-xl"></i>
                                    </router-link>
                                </div>
                            </div>

                            <div class="h-px bg-gray-100 w-full mb-8"></div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">
                                <div class="group p-4 rounded-2xl bg-gray-50 border border-gray-100 hover:border-primary/30 transition-colors">
                                    <label class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1 block">
                                        {{ $t('label.first_name') }}
                                    </label>
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-lg bg-white flex items-center justify-center text-primary shadow-sm">
                                            <i class="fa-regular fa-user"></i>
                                        </div>
                                        <p class="font-semibold text-gray-700">{{ user.first_name }}</p>
                                    </div>
                                </div>

                                <div class="group p-4 rounded-2xl bg-gray-50 border border-gray-100 hover:border-primary/30 transition-colors">
                                    <label class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1 block">
                                        {{ $t('label.last_name') }}
                                    </label>
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-lg bg-white flex items-center justify-center text-primary shadow-sm">
                                            <i class="fa-regular fa-user"></i>
                                        </div>
                                        <p class="font-semibold text-gray-700">{{ user.last_name }}</p>
                                    </div>
                                </div>

                                <div class="group p-4 rounded-2xl bg-gray-50 border border-gray-100 hover:border-primary/30 transition-colors">
                                    <label class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1 block">
                                        {{ $t('label.email') }}
                                    </label>
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-lg bg-white flex items-center justify-center text-primary shadow-sm">
                                            <i class="lab lab-mail"></i>
                                        </div>
                                        <p class="font-semibold text-gray-700 truncate" :title="user.email">{{ user.email }}</p>
                                    </div>
                                </div>

                                <div class="group p-4 rounded-2xl bg-gray-50 border border-gray-100 hover:border-primary/30 transition-colors">
                                    <label class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1 block">
                                        {{ $t('label.phone') }}
                                    </label>
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-lg bg-white flex items-center justify-center text-primary shadow-sm">
                                            <i class="lab lab-call-center"></i>
                                        </div>
                                        <p class="font-semibold text-gray-700">
                                            {{ user.phone ? (countryCode.calling_code + user.phone) : 'N/A' }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="flex sm:hidden flex-wrap gap-3 mt-8 pt-6 border-t border-gray-100">
                                <router-link :to="{ name: 'frontend.editProfile' }"
                                    class="flex-1 inline-flex justify-center items-center gap-2 px-6 py-3 bg-primary text-white text-sm font-bold rounded-xl shadow-lg shadow-primary/30 active:scale-95 transition-all">
                                    <i class="lab lab-edit"></i>
                                    {{ $t('menu.edit_profile') }}
                                </router-link>
                            </div>
                        </div>
                    </div>

                    <!-- Saved Addresses Section -->
                    <div class="bg-white rounded-3xl shadow-xl shadow-gray-200/60 p-8 border border-gray-100">
                        <div class="flex justify-between items-center mb-6">
                            <h3 class="text-xl font-bold text-gray-800">{{ $t('label.address') }}</h3>
                            <AddressCreateComponent :getLocation="updateAddress" :props="addressProps" />
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div v-for="address in addresses" :key="address.id" class="p-4 rounded-2xl bg-gray-50 border border-gray-100 flex items-start gap-3">
                                <div class="w-10 h-10 rounded-full bg-primary/10 flex-shrink-0 flex items-center justify-center text-primary">
                                    <i class="lab lab-home" v-if="address.label == 'Home'"></i>
                                    <i class="lab lab-briefcase" v-else-if="address.label == 'Work'"></i>
                                    <i class="lab lab-more-square" v-else></i>
                                </div>
                                <div class="flex-1 w-full">
                                    <div class="flex items-start justify-between">
                                        <h4 class="font-bold text-gray-800 capitalize">{{ address.label }}</h4>
                                        <div class="flex items-center gap-2">
                                            <button data-modal="#address" type="button" @click="edit(address)"
                                                class="w-7 h-7 rounded-full bg-primary/10 text-primary hover:bg-primary hover:text-white flex items-center justify-center transition-colors">
                                                <i class="lab lab-edit-2 text-sm"></i>
                                            </button>
                                            <button type="button" @click="destroy(address.id)"
                                                class="w-7 h-7 rounded-full bg-red-50 text-red-500 hover:bg-red-500 hover:text-white flex items-center justify-center transition-colors">
                                                <i class="lab lab-delete-bold text-sm"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <p class="text-sm text-gray-500 mt-1 line-clamp-2">
                                        {{ address.apartment ? address.apartment + ', ' : '' }}{{ address.address }}
                                    </p>
                                </div>
                            </div>
                            <div v-if="addresses && addresses.length === 0" class="col-span-full text-center py-6">
                                <p class="text-gray-500">{{ $t('message.no_address_found') }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="lg:col-span-4 space-y-6">
                    
                    <div class="relative w-full overflow-hidden rounded-3xl bg-primary text-white shadow-2xl shadow-primary/40 group">
                        <div class="absolute top-0 right-0 -mr-16 -mt-16 w-64 h-64 rounded-full bg-white/10 blur-3xl group-hover:bg-white/20 transition-all duration-700"></div>
                        <div class="absolute bottom-0 left-0 -ml-16 -mb-16 w-48 h-48 rounded-full bg-black/10 blur-2xl"></div>
                        
                        <div v-if="customerType === 'authorized'" class="relative p-8 flex flex-col h-full min-h-[240px] justify-between z-10">
                            <div class="flex justify-between items-start">
                                <div>
                                    <p class="text-white/80 text-sm font-semibold tracking-wider uppercase mb-1">{{ $t('label.loyalty_points') }}</p>
                                    <h3 class="text-2xl text-white font-bold tracking-tight">Member Card</h3>
                                </div>
                                <div class="w-12 h-12 rounded-full bg-white/20 backdrop-blur-md flex items-center justify-center border border-white/30">
                                    <i class="fa-solid fa-award text-2xl text-white"></i>
                                </div>
                            </div>

                            <div class="my-6">
                                <span class="text-6xl font-black tracking-tighter drop-shadow-lg">
                                    {{ totalPoints }}
                                </span>
                                <span class="text-lg font-medium text-white/70 ml-1">pts</span>
                            </div>

                            <div class="pt-6 border-t border-white/10 flex justify-between items-end">
                                <div>
                                    <p class="text-xs text-white/60 mb-1">Card Holder</p>
                                    <p class="font-medium tracking-wide">{{ user.first_name }} {{ user.last_name }}</p>
                                </div>
                                <router-link :to="{ name: 'frontend.loyaltyPoints' }" 
                                    class="px-4 py-2 bg-white text-primary text-xs font-bold rounded-lg shadow hover:bg-gray-50 transition-colors">
                                    {{ $t('label.view_history') }}
                                </router-link>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-3xl shadow-lg shadow-gray-200/50 p-6 border border-gray-100">
                        <h3 class="text-lg font-bold text-heading mb-4 px-2">{{ $t('menu.account_settings') }}</h3>
                        <div class="space-y-3">
                            <router-link :to="{ name: 'frontend.changePassword' }"
                                class="flex items-center justify-between p-4 rounded-xl bg-gray-50 border border-gray-100 hover:bg-white hover:shadow-md hover:border-primary/20 hover:-translate-y-0.5 transition-all duration-300 group">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full bg-red-50 flex items-center justify-center text-red-500 group-hover:bg-red-500 group-hover:text-white transition-colors">
                                        <i class="lab lab-lock text-lg"></i>
                                    </div>
                                    <span class="font-medium text-gray-700 group-hover:text-primary transition-colors">{{ $t('menu.change_password') }}</span>
                                </div>
                                <i class="lab lab-angle-right text-gray-400 group-hover:text-primary group-hover:translate-x-1 transition-all"></i>
                            </router-link>

                            </div>
                    </div>
                </div>

            </div>
        </div>
    </section>
</template>

<script>
import alertService from "../../../../services/alertService";
import AddressCreateComponent from "../address/AddressCreateComponent";
import labelEnum from "../../../../enums/modules/labelEnum";
import LoadingComponent from "../../components/LoadingComponent";
import appService from "../../../../services/appService";

export default {
    name: "ProfileComponent",
    components: {
        LoadingComponent,
        AddressCreateComponent,
    },
    data() {
        return {
            loading: {
                isActive: false,
            },
            localAddress: {},
            addressProps: {
                form: {
                    address: "",
                    apartment: "",
                    latitude: "",
                    longitude: "",
                    label: "",
                },
                search: {
                    paginate: 0,
                    order_column: "id",
                    order_type: "asc",
                },
                status: false,
                switchLabel: "",
                isMap: false,
            },
        };
    },
    mounted() {
        this.loadLoyaltyPointsSummary();
        this.$store.dispatch("frontendAddress/lists", {
            search: { paginate: 0, order_column: "id", order_type: "asc" }
        });
    },
    computed: {
        user: function () {
            return this.$store.getters.authInfo;
        },
        countryCode: function () {
            return this.$store.getters['frontendCountryCode/show'];
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
        customerType: function () {
            return this.$store.getters['frontendLoyaltyPoints/customerType'];
        },
        addresses: function () {
            return this.$store.getters["frontendAddress/lists"];
        },
    },
    methods: {
        loadLoyaltyPointsSummary: function () {
            this.loading.isActive = true;
            this.$store.dispatch('frontendLoyaltyPoints/getSummary').then(() => {
                this.loading.isActive = false;
            }).catch((err) => {
                this.loading.isActive = false;
                console.error('Error loading loyalty points:', err);
            });
        },
        formatDate: function (dateString) {
            if (!dateString) return 'N/A';
            const date = new Date(dateString);
            return date.toLocaleDateString('en-US', {
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });
        },
        handleImageError: function (event) {
            // Fallback to default profile image if the current image fails to load
            event.target.src = '/images/default/profile.png';
        },
        updateAddress: function (address) {
            this.localAddress = address;
        },
        edit: function (address) {
            appService.modalShow("#address");
            this.loading.isActive = true;
            this.$store.dispatch("frontendAddress/edit", address.id).then((res) => {
                this.loading.isActive = false;
                this.addressProps.isMap = true;
                this.addressProps.form = {
                    address: address.address,
                    apartment: address.apartment,
                    latitude: address.latitude,
                    longitude: address.longitude,
                    label: address.label,
                };
                if (this.addressProps.form.label === this.$t("label.home")) {
                    this.addressProps.status = false;
                    this.addressProps.switchLabel = labelEnum.HOME;
                } else if (this.addressProps.form.label === this.$t("label.work")) {
                    this.addressProps.status = false;
                    this.addressProps.switchLabel = labelEnum.WORK;
                } else {
                    this.addressProps.status = true;
                    this.addressProps.switchLabel = labelEnum.OTHER;
                }
            }).catch((err) => {
                this.loading.isActive = false;
                alertService.error(err.response.data.message);
            });
        },
        destroy: function (addressId) {
            appService.destroyConfirmation().then((res) => {
                try {
                    this.loading.isActive = true;
                    this.$store.dispatch("frontendAddress/destroy", {
                        id: addressId,
                        search: this.addressProps.search,
                    }).then((res) => {
                        this.loading.isActive = false;
                        alertService.successFlip(null, this.$t("label.address"));
                    }).catch((err) => {
                        this.loading.isActive = false;
                        alertService.error(err.response.data.message);
                    });
                } catch (err) {
                    this.loading.isActive = false;
                    alertService.error(err.response.data.message);
                }
            }).catch((err) => {
                this.loading.isActive = false;
            });
        },
    },
};
</script>