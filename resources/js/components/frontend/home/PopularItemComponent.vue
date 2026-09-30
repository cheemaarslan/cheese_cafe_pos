<template>
    <LoadingComponent :props="loading" />
    <section class="py-10 min-h-[400px]" v-show="popularItems.length > 0 || loading">
        <div class="container" v-if="popularItems.length > 0">
            <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-4 mb-10">
                <div class="text-center md:text-left">
                    <h4 class="text-primary/80 font-bold tracking-widest uppercase text-xs md:text-sm mb-2">
                        Popular Items
                    </h4>
                    <h2 class="text-3xl md:text-4xl font-extrabold text-gray-800 font-serif">
                        Our <span class="text-primary">Favorites</span>
                    </h2>
                    <div class="w-20 h-1.5 bg-primary mt-3 rounded-full md:mx-0 mx-auto"></div>
                </div>

                <router-link :to="{ name: 'frontend.menu', query: { s: 'popular' } }" class="rounded-3xl capitalize text-sm leading-6 font-medium py-2 px-4 transition
                       text-primary bg-[#FFEDF4] hover:text-white hover:bg-primary md:self-end self-center">
                    {{ $t("button.view_all") }}
                </router-link>
            </div>
            <ItemComponent :items="popularItems" :type="itemProps.type" :design="itemProps.design" />
        </div>
    </section>
</template>
<script>

import alertService from "../../../services/alertService";
import itemDesignEnum from "../../../enums/modules/itemDesignEnum";
import ItemComponent from "../components/ItemComponent";
import LoadingComponent from "../components/LoadingComponent";

export default {
    name: "PopularItemComponent",
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
                isActive: false
            },
            itemProps: {
                design: itemDesignEnum.LIST,
                type: null,
            },
        };
    },
    mounted() {
        try {
            this.$store.dispatch("frontendItem/popular", {
                order_column: "id",
                order_type: "desc",
            }).then(res => {
            }).catch((err) => {
            });
        } catch (err) {
            
        }
    },
    computed: {
        popularItems: function () {
            return this.$store.getters["frontendItem/popular"];
        }
    }
};
</script>
