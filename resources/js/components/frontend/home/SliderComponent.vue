<template>
    <LoadingComponent :props="loading" />
    <div v-if="sliders.length > 0" class="slider-container container mb-5 mt-5 sm:mt-8 rounded-2xl overflow-hidden">
        <Swiper dir="rtl" :slides-per-view="1" :speed="1000" :loop="true" :navigation="true"
            :autoplay="{ delay: 2500, disableOnInteraction: false }" :pagination="{ clickable: true }"
            :modules="modules" class="banner-swiper">
            <SwiperSlide class="rounded-2xl" v-for="(slider, index) in sliders" :key="index">
                <img class="w-full rounded-2xl" :src="slider.image" alt="banner" width="1120" height="400" style="aspect-ratio: 1120/400; object-fit: cover;" :fetchpriority="index === 0 ? 'high' : 'auto'" :loading="index === 0 ? 'eager' : 'lazy'">
            </SwiperSlide>
        </Swiper>
    </div>
    <div v-else class="slider-skeleton container mb-5 mt-5 sm:mt-8 rounded-2xl overflow-hidden" style="aspect-ratio: 1120/400; background: rgba(0,0,0,0.05); width: 100%;"></div>
</template>

<style>

/* @media (min-width: 1300px) {
    .slider-container {
        max-width: 100% !important;
        /* give margin of 30 rem from left and right */
        /*padding-left: 7rem !important;
        padding-right: 7rem !important;
    }
} */

/* for media 1800 and above  */
@media (min-width: 1800px) {
    .slider-container {
        max-width: 100% !important;
        padding-left: 10rem !important;
        padding-right: 10rem !important;
    }
}

/* for media 1800 and above  */
@media (min-width: 2700px) {
    .slider-container {
        max-width: 100% !important;
        padding-left: 15rem !important;
        padding-right: 15rem !important;
    }
}
</style>

<script>
import alertService from "../../../services/alertService";
import statusEnum from "../../../enums/modules/statusEnum";
import LoadingComponent from "../components/LoadingComponent";
import { Swiper, SwiperSlide } from 'swiper/vue';
import { Navigation, Pagination, Autoplay } from 'swiper/modules';
import 'swiper/css';

export default {
    name: "SliderComponent",
    components: {
        LoadingComponent,
        Swiper,
        SwiperSlide,
    },
    setup() {
        return {
            modules: [Navigation, Pagination, Autoplay],
        }
    },
    data() {
        return {
            loading: {
                isActive: false
            },
            sliderProps: {
                search: {
                    paginate: 0,
                    order_column: 'id',
                    order_type: 'desc',
                    status: statusEnum.ACTIVE
                }
            },
            settings: {
                pauseAutoplayOnHover: true,
                wrapAround: true,
                transition: 1000,
                autoplay: 5000
            },
        }
    },
    computed: {
        sliders: function () {
            return this.$store.getters['frontendSlider/lists'];
        },
    },
    methods: {
        getViewportData() {
            const viewportWidth = window.innerWidth;
            const zoom = window.innerWidth / window.screen.width;
            return { viewportWidth, zoom };
        },

        onResize() {
            this.getViewportData();
        }
    }
    ,
    mounted() {
        // initial log
        this.getViewportData();

        // zoom / resize listener
        window.addEventListener('resize', this.onResize);

        if (this.sliders.length === 0) {
            this.loading.isActive = true;
            this.$store.dispatch("frontendSlider/lists", this.sliderProps.search).then((res) => {
                this.loading.isActive = false;
            }).catch((err) => {
                this.loading.isActive = false;
            });
        }
    },
    beforeUnmount() {
        // remove zoom / resize listener
        window.removeEventListener('resize', this.onResize);
    }
}
</script>
