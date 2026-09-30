<template>
    <section :class="`relative py-10 min-h-[400px] bg-${bg} overflow-hidden`" v-show="offers.length > 0 || loading.isActive">
        <LoadingComponent :props="loading" />

        <!-- Background Decorations -->
        <div class="absolute top-0 left-0 -ml-16 -mt-16 w-64 h-64 bg-amber-50 rounded-full blur-3xl opacity-50 pointer-events-none"></div>
        <div class="absolute bottom-0 right-0 -mr-16 -mb-16 w-64 h-64 bg-orange-50 rounded-full blur-3xl opacity-50 pointer-events-none"></div>

        <div class="container mx-auto px-4 relative z-10">
            <!-- Section Header -->
            <div class="text-center mb-12">
                <h4 class="text-primary/80 font-bold tracking-widest uppercase text-xs md:text-sm mb-2">
                    Don't Miss Out
                </h4>
                <h2 class="text-3xl md:text-4xl font-extrabold text-gray-800 font-serif mb-4">
                    Exclusive <span class="text-primary">Offers</span>
                </h2>
                <div class="w-20 h-1.5 bg-primary mx-auto rounded-full"></div>
            </div>

            <!-- Offers Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <template v-if="offers.length > 0">
                    <router-link 
                        v-for="(offer, index) in offers"
                        :key="offer.id || index"
                        :to="{ name: 'frontend.offers.item', params: { slug: offer.slug } }" 
                        class="group relative block rounded-3xl overflow-hidden shadow-lg hover:shadow-2xl transition-all duration-500 transform hover:-translate-y-1"
                    >
                        <!-- Image Container with Zoom Effect -->
                        <div class="aspect-w-16 aspect-h-9 md:aspect-h-7 overflow-hidden">
                            <img 
                                class="w-full h-full object-cover transition-transform duration-700 ease-in-out group-hover:scale-110" 
                                :src="offer.image" 
                                :alt="offer.name || 'Special Offer'" 
                            />
                        </div>

                        <!-- Overlay & Action Button (Appears on Hover) -->
                        <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-center justify-center backdrop-blur-[2px]">
                            <span class="bg-primary text-white px-8 py-3 rounded-full font-bold uppercase tracking-wide transform translate-y-4 group-hover:translate-y-0 transition-transform duration-300 shadow-lg flex items-center gap-2">
                                View Deal <i class="fas fa-arrow-right"></i>
                            </span>
                        </div>

                        <!-- Optional: Offer Name Tag (Always Visible or on Hover) -->
                        <div class="absolute bottom-0 left-0 w-full p-6 bg-gradient-to-t from-black/80 to-transparent pointer-events-none">
                            <h3 class="text-white text-xl font-bold font-serif translate-y-2 group-hover:translate-y-0 transition-transform duration-300">
                                {{ offer.name }}
                            </h3>
                        </div>
                    </router-link>
                </template>
                <template v-else-if="loading.isActive">
                    <div v-for="n in limit" :key="n" class="relative block rounded-3xl overflow-hidden shadow-lg bg-gray-200 animate-pulse">
                        <div class="aspect-w-16 aspect-h-9 md:aspect-h-7"></div>
                    </div>
                </template>
            </div>
        </div>
    </section>
</template>

<script>
import statusEnum from "../../../enums/modules/statusEnum";
import LoadingComponent from "../components/LoadingComponent"; // Assuming this is needed based on previous files

export default {
    name: "OfferComponent",
    components: {
        LoadingComponent
    },
    props: {
        limit: Number,
        bg : {
            type: String,
            default: 'bg-transparent'
        }   
    },
    data() {
        return {
            loading: {
                isActive: false,
            },
        };
    },
    mounted() {
        try {
            this.loading.isActive = true;
            this.$store.dispatch("frontendOffer/lists", {
                order_column: "id",
                order_type: "desc",
                limit: this.limit,
                status: statusEnum.ACTIVE,
            }).then(() => {
                 this.loading.isActive = false;
            }).catch(() => {
                 this.loading.isActive = false;
            });
        } catch (err) {
            this.loading.isActive = false;
        }
    },
    computed: {
        offers: function () {
            return this.$store.getters["frontendOffer/lists"];
        },
    },
    methods: {},
};
</script>