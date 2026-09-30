<template>
    <LoadingComponent :props="loading" />
    <section class="py-10 min-h-[400px]" v-show="featuredItems.length > 0 || loading">
        <div class="container" v-if="featuredItems.length > 0">
            <!-- Section Header -->
            <div class="text-center mb-12">
                <h4 class="text-primary/80 font-bold tracking-widest uppercase text-xs md:text-sm mb-2">
                    Our Specials
                </h4>

                <h2 class="text-3xl md:text-4xl font-extrabold text-gray-800 font-serif mb-4">
                    Featured <span class="text-primary">Items</span>
                </h2>
                <div class="w-20 h-1.5 bg-primary mx-auto rounded-full"></div>
            </div>
            <ItemComponent :items="featuredItems" :type="itemProps.type" :design="itemProps.design" />
        </div>
    </section>
</template>
<script>

import alertService from "../../../services/alertService";
import itemDesignEnum from "../../../enums/modules/itemDesignEnum";
import ItemComponent from "../components/ItemComponent";
import LoadingComponent from "../components/LoadingComponent";

export default {
    name: "FeaturedItemComponent",
    components: {
        ItemComponent,
        LoadingComponent
    },
    props: {
        items: Object,
    },
    data() {
        return {
            loading: {
                isActive: false,
            },
            itemProps: {
                design: itemDesignEnum.GRID,
                type: null,
            },
        };
    },
    mounted() {
        try {
            this.$store.dispatch("frontendItem/featured", {
                order_column: "id",
                order_type: "desc"
            }).then(res => {
            }).catch((err) => {
            });
        } catch (err) {
        }
    },
    computed: {
        featuredItems: function () {
            return this.$store.getters["frontendItem/featured"];
        },
    },
    methods: {},
};
</script>
