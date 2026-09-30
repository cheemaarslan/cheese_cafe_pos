<template>
    <LoadingComponent :props="loading" />

    <!-- Premium Offer Hero Section -->
    <div v-if="offer.image" class="w-full mt-10 px-4 sm:px-0 relative group">
        <div class="relative w-full max-w-6xl mx-auto overflow-hidden rounded-[2rem] shadow-2xl">
            <!-- Overlay gradient for premium feel -->
            <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-black/10 to-transparent z-10 pointer-events-none"></div>
            
            <!-- Offer Banner -->
            <div v-if="offer.image" class="w-full flex justify-center overflow-hidden rounded-2xl">
                <img :src="offer.image" :alt="offer.name" class="max-w-full max-h-[450px] object-contain rounded-2xl">
            </div>
            
            <!-- Floating Offer Badge inside Image -->
            <div v-if="offer.discount > 0" class="absolute top-6 right-6 z-20 bg-white/90 backdrop-blur-sm px-4 py-2 rounded-full shadow-lg border border-white/20">
                <span class="text-red-600 font-bold tracking-wide">{{ offer.discount }}% OFF</span>
            </div>
            
            <!-- Bottom Left Image Title
            <div class="absolute bottom-8 left-8 z-20">
                <h1 class="text-3xl sm:text-5xl font-extrabold text-white tracking-tight drop-shadow-md">
                    {{ offer.name }}
                </h1>
            </div> -->
        </div>
    </div>

    <!-- Static Value Proposition Content (New) -->
    <section class="max-w-6xl mx-auto mt-12 px-4 sm:px-0">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 py-8 border-y border-gray-100">
            <div class="flex items-center gap-4 p-4 rounded-xl hover:bg-gray-50 transition-colors">
                <div class="w-12 h-12 flex items-center justify-center bg-primary/10 text-primary rounded-full">
                    <i class="fa-solid fa-boxes-packing"></i>
                </div>
                <div>
                    <h4 class="font-semibold text-gray-900">Premium Curated Items</h4>
                    <p class="text-sm text-gray-500">Hand-picked for the best experience</p>
                </div>
            </div>
            <div class="flex items-center gap-4 p-4 rounded-xl hover:bg-gray-50 transition-colors">
                <div class="w-12 h-12 flex items-center justify-center bg-primary/10 text-primary rounded-full">
                    <i class="lab lab-save lab-font-size-24"></i>
                </div>
                <div>
                    <h4 class="font-semibold text-gray-900">Exclusive Discount</h4>
                    <p class="text-sm text-gray-500">Bundle pricing unavailable elsewhere</p>
                </div>
            </div>
            <div class="flex items-center gap-4 p-4 rounded-xl hover:bg-gray-50 transition-colors">
                <div class="w-12 h-12 flex items-center justify-center bg-primary/10 text-primary rounded-full">
                   <i class="fa-solid fa-certificate"></i>
                </div>
                <div>
                    <h4 class="font-semibold text-gray-900">Guaranteed Satisfaction</h4>
                    <p class="text-sm text-gray-500">Highest quality standards met</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Main Content Section -->
    <section class="mb-24 mt-12 max-w-6xl mx-auto px-4 sm:px-0">
        <div class="container">
            <!-- Toolbar -->
            <div class="flex flex-col sm:flex-row items-center justify-between mb-8">
                <h2 class="text-2xl font-bold text-gray-900">
                    Included in this Offer
                </h2>
                <div class="flex items-center gap-2 bg-gray-50 p-1.5 rounded-lg border border-gray-100">
                    <button type="button" 
                        class="p-2 rounded-md transition-all duration-200 flex items-center justify-center"
                        v-on:click="itemProps.design = itemDesignEnum.LIST"
                        :class="itemProps.design === itemDesignEnum.LIST ? 'bg-white shadow-sm text-primary' : 'text-gray-400 hover:text-gray-600'">
                        <i class="lab lab-row-vertical lab-font-size-20"></i>
                    </button>
                    <button type="button" 
                        class="p-2 rounded-md transition-all duration-200 flex items-center justify-center"
                        v-on:click="itemProps.design = itemDesignEnum.GRID"
                        :class="itemProps.design === itemDesignEnum.GRID ? 'bg-white shadow-sm text-primary' : 'text-gray-400 hover:text-gray-600'">
                        <i class="lab lab-element-3 lab-font-size-20"></i>
                    </button>
                </div>
            </div>

            <!-- Premium Add to Cart Action Card -->
            <div v-if="hasItems"
                class="mb-12 relative overflow-hidden bg-white rounded-3xl shadow-[0_8px_30px_rgb(0,0,0,0.06)] border border-gray-100 p-8">
                <!-- Decorative background element -->
                <div class="absolute -right-20 -top-20 w-64 h-64 bg-primary/5 rounded-full blur-3xl pointer-events-none"></div>
                
                <div class="flex flex-col md:flex-row items-center justify-between gap-8 relative z-10">
                    <div class="flex-1 text-center md:text-left">
                        <h3 class="text-2xl font-bold text-gray-900 mb-2">Unlock the Complete Package</h3>
                        <p class="text-base text-gray-500 mb-4 max-w-lg">Get all the items listed below instantly with our exclusive bundle pricing.</p>
                        
                        <div class="flex flex-wrap items-center justify-center md:justify-start gap-4">
                            <span class="text-4xl font-extrabold text-primary tracking-tight">
                                {{ offerDiscountedTotal }}
                            </span>
                            <del v-if="offer.discount > 0" class="text-xl text-gray-400 font-medium decoration-gray-300">
                                {{ offerOriginalTotal }}
                            </del>
                            <span v-if="offer.discount > 0"
                                class="px-3 py-1.5 bg-green-50 text-green-700 border border-green-200 text-sm font-bold uppercase tracking-wider rounded-lg">
                                Save {{ offer.discount }}%
                            </span>
                        </div>
                    </div>
                    <!-- <button @click="goToOnlineOrder()"
                        class="flex items-center gap-1.5 px-3 h-10 rounded-xl bg-primary to-primary-dark text-white font-semibold text-xs hover:shadow-lg hover:shadow-primary/30 transition-all duration-300 whitespace-nowrap">
                        <i class="lab lab-bag-2 transition lab-font-size-14"></i>
                        <span>Online Order</span>
                    </button> -->
                    <button @click.prevent="addOfferToCart"
                        class="group w-full md:w-auto flex items-center justify-center gap-3 px-8 py-4 bg-primary text-white font-bold text-lg rounded-xl hover:bg-primary/90 transition-all duration-300 shadow-[0_4px_14px_0_rgba(var(--color-primary),0.39)] hover:shadow-[0_6px_20px_rgba(var(--color-primary),0.23)] hover:-translate-y-0.5">
                        <i class="lab lab-bag-2 lab-font-size-20 transition-transform group-hover:scale-110"></i>
                        <span>Claim Offer Now</span>
                    </button>
                </div>
            </div>

            <!-- Item Listing -->
            <ItemComponent v-if="hasItems" :items="items.items" :type="itemProps.type" :design="itemProps.design"
                :hasCartButton="false" />

            <!-- Empty State -->
            <div class="mt-20 flex flex-col items-center justify-center" v-else>
                <div class="w-64 h-64 mb-6 opacity-80 mix-blend-multiply grayscale">
                    <img class="w-full h-full object-contain" :src="setting.item_not_found" alt="Not found">
                </div>
                <h3 class="text-xl font-semibold text-gray-800 mb-2">No items found</h3>
                <p class="text-gray-500 text-center max-w-md">This offer doesn't currently have any items attached to it. Please check back later.</p>
            </div>
        </div>
    </section>
</template>

<script>
import ItemComponent from "../components/ItemComponent";
import itemDesignEnum from "../../../enums/modules/itemDesignEnum";
import alertService from "../../../services/alertService";
import LoadingComponent from "../components/LoadingComponent";
import appService from "../../../services/appService"; // Moved import to top for cleaner methods

export default {
    name: "OffersItemComponent",
    components: {
        ItemComponent, LoadingComponent
    },
    data() {
        return {
            loading: {
                isActive: false,
            },
            itemDesignEnum: itemDesignEnum,
            offer: {},
            items: {},
            itemProps: {
                design: itemDesignEnum.LIST,
                type: null,
            },
            route: null,
        };
    },
    computed: {
        setting: function () {
            return this.$store.getters['frontendSetting/lists'];
        },
        hasItems() {
            const list = this.items?.items;
            return Array.isArray(list) ? list.length > 0 : !!(list && Object.keys(list).length);
        },
        offerOriginalTotal() {
            if (!this.items?.items || !Array.isArray(this.items.items)) return this.currencyFormat(0, this.setting.site_digit_after_decimal_point, this.setting.site_default_currency_symbol, this.setting.site_currency_position);

            const total = this.items.items.reduce((sum, item) => {
                return sum + (item.convert_price || 0);
            }, 0);

            return this.currencyFormat(total, this.setting.site_digit_after_decimal_point, this.setting.site_default_currency_symbol, this.setting.site_currency_position);
        },
        offerDiscountedTotal() {
            if (!this.items?.items || !Array.isArray(this.items.items)) return this.currencyFormat(0, this.setting.site_digit_after_decimal_point, this.setting.site_default_currency_symbol, this.setting.site_currency_position);

            const total = this.items.items.reduce((sum, item) => {
                return sum + (item.convert_price || 0);
            }, 0);

            const discount = this.offer.discount || 0;
            const discountedTotal = total - (total * discount / 100);

            return this.currencyFormat(discountedTotal, this.setting.site_digit_after_decimal_point, this.setting.site_default_currency_symbol, this.setting.site_currency_position);
        }
    },
    mounted() {
        if (typeof this.$route.params.slug !== "undefined") {
            this.loading.isActive = true;
            this.$store.dispatch("frontendOffer/offerItems", {
                slug: this.$route.params.slug,
                vuex: false,
            }).then((res) => {
                this.offer = res.data.data;
                this.items = res.data.data;
                this.loading.isActive = false;
            }).catch((err) => {
                this.loading.isActive = false;
            });
        }
    },
    methods: {
        goToOnlineOrder() {
             window.open('/online-orders', '_blank', 'noopener,noreferrer');
        },
        currencyFormat: function (amount, decimal, currency, position) {
            return appService.currencyFormat(amount, decimal, currency, position);
        },
        itemTypeSet: function (e) {
            this.itemProps.type = e;
        },
        itemTypeReset: function () {
            this.itemProps.type = null;
        },
        addOfferToCart: function () {
            if (!this.items?.items || !Array.isArray(this.items.items) || this.items.items.length === 0) {
                alertService.error(this.$t('message.no_items_in_offer') || 'No items in this offer');
                return;
            }

            // Calculate total original price
            const totalOriginalPrice = this.items.items.reduce((sum, item) => {
                return sum + (item.convert_price || 0);
            }, 0);

            // Apply discount
            const discount = this.offer.discount || 0;
            const discountedPrice = totalOriginalPrice - (totalOriginalPrice * discount / 100);
            const discountAmount = totalOriginalPrice - discountedPrice;

            // Prepare offer items data with images and names
            const offerItemsData = this.items.items.map(item => ({
                name: item.name,
                image: item.thumb || item.cover || '',
                price: item.convert_price
            }));

            // Serialize offer items data to JSON
            const offerItemsJson = JSON.stringify(offerItemsData);

            // Create a combined offer item for cart
            const offerCartItem = {
                name: this.offer.name,
                image: this.items.items[0]?.thumb || this.items.items[0]?.cover || '',
                item_id: null,
                itemable_type: 'App\\Models\\Offer',
                itemable_id: this.offer.id,
                offer_id: this.offer.id,
                quantity: 1,
                discount: discountAmount,
                currency_price: this.currencyFormat(discountedPrice, this.setting.site_digit_after_decimal_point, this.setting.site_default_currency_symbol, this.setting.site_currency_position),
                convert_price: discountedPrice,
                item_variations: {
                    variations: {
                        'offer_items_data': 1
                    },
                    names: {
                        'offer_items_data': offerItemsJson 
                    }
                },
                item_extras: {
                    extras: [],
                    names: []
                },
                item_variation_total: 0,
                item_extra_total: 0,
                instruction: ''
            };

            // Add to cart
            this.$store.dispatch('frontendCart/lists', [offerCartItem])
                .then((res) => {
                    alertService.success(this.$t('message.offer_added_to_cart') || 'Offer added to cart successfully!');
                })
                .catch((err) => {
                    alertService.error(this.$t('message.failed_to_add_offer') || 'Failed to add offer to cart');
                });
        },
    },
};
</script>