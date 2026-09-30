<template>
    <div class="w-full relative overflow-hidden group">

        <!-- Premium Gradient Masks (Fade edges)
        <div
            class="absolute left-0 top-0 bottom-0 w-12 z-10 bg-gradient-to-r from-white to-transparent pointer-events-none">
        </div>
        <div
            class="absolute right-0 top-0 bottom-0 w-12 z-10 bg-gradient-to-l from-white to-transparent pointer-events-none">
        </div> -->

        <!-- Scroll Track -->
        <div class="flex w-max animate-marquee group-hover:paused">

            <!-- We render the list twice to create the seamless infinite scroll effect -->
            <div v-for="i in 2" :key="i" :class="['flex gap-4 px-2', padding]"  :aria-hidden="i === 2">
                <div v-for="category in categories" :key="category.id || category.slug" class="flex-shrink-0">

                    <!-- Design 1 -->
                    <router-link v-if="design === categoryDesignEnum.FIRST"
                        :tabindex="i === 2 ? -1 : 0"
                        :to="{ name: 'frontend.menu', query: { s: category.slug } }" :class="[
                            'block flex flex-col items-center text-center gap-4 p-3 min-h-[13rem] rounded-2xl border-b-2 transition-all duration-300',
                            'w-36 sm:w-48 lg:w-48',
                            checkIsQueryAndRouteQuerySame(category.slug)
                                ? 'bg-white border-pink-500 shadow-xl scale-105 z-10 ring-1 ring-pink-100'
                                : 'border-transparent bg-[#F7F7FC] hover:bg-[#FFEDF4] hover:scale-105'
                        ]">
                        <img class="h-24 sm:h-28 lg:h-32 rounded-md drop-shadow-category object-cover"
                            :src="category.thumb" alt="category" width="160" height="160">
                        <h3 class="text-sm font-medium line-clamp-2">{{ category.name }}</h3>
                    </router-link>

                    <!-- Design 2 -->
                    <router-link v-else-if="design === categoryDesignEnum.SECOND"
                        :tabindex="i === 2 ? -1 : 0"
                        :to="{ name: 'frontend.menu', query: { s: category.slug } }" :class="[
                            'block flex flex-col items-center text-center gap-4 p-3 min-h-[12rem] rounded-2xl border-b-2 transition-all duration-300',
                            'w-36 sm:w-48 lg:w-60',
                            checkIsQueryAndRouteQuerySame(category.slug)
                                ? 'bg-white border-pink-500 shadow-xl scale-105 z-10 ring-1 ring-pink-100'
                                : 'border-transparent hover:bg-[#FFEDF4] hover:scale-105'
                        ]">
                        <img class="h-20 sm:h-24 lg:h-28 drop-shadow-category object-contain" :src="category.thumb"
                            alt="category">
                        <h3 class="text-sm font-medium line-clamp-2">{{ category.name }}</h3>
                    </router-link>

                </div>
            </div>

        </div>
    </div>
</template>

<script>
import categoryDesignEnum from "../../../enums/modules/categoryDesignEnum";

export default {
    name: "CategoryComponent",
    props: {
        categories: {
            type: [Array, Object],
            default: () => []
        },
        design: {
            type: Number,
            default: 1
        },
        padding: {
            type: String,
            default: 'pb-0'
        }
    },
    data() {
        return {
            currentCategory: "",
            categoryDesignEnum: categoryDesignEnum,
        }
    },
    mounted() {
        if (this.$route.query.s && this.$route.query.s !== "undefined") {
            this.currentCategory = this.$route.query.s;
        }
    },
    methods: {
        checkIsQueryAndRouteQuerySame(query) {
            return this.currentCategory === query;
        },
    },
    watch: {
        $route(to, from) {
            if (to.query.s && to.query.s !== "undefined") {
                this.currentCategory = to.query.s;
            }
        }
    }
}
</script>

<style scoped>
.animate-marquee {
    animation: scroll 20s linear infinite;
}

.paused {
    animation-play-state: paused;
}

@keyframes scroll {
    0% {
        transform: translateX(0);
    }

    100% {
        /* Move exactly 50% because we doubled the content. 
           This creates the seamless loop illusion. */
        transform: translateX(-50%);
    }
}
</style>