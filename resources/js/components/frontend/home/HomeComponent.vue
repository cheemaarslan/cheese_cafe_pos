<template>
    <LoadingComponent :props="loading" />

    <!--========TRACK PART START===========-->
    <TrackOrderComponent />
    <!--========TRACK PART END=============-->


    <!--========BANNER PART START===========-->
    <SliderComponent />
    <!--========BANNER PART END=============-->

    <!--======== Category Section START =========-->
    <section class="mb-2 min-h-[460px]" v-show="categories.length > 0 || loading">
        <div class="container" v-if="categories.length > 0">

            <!-- Header Row -->
            <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-4 mt-12">

                <!-- Text + Title -->
                <div class="text-center md:text-left">
                    <h4 class="text-primary/80 font-bold tracking-widest uppercase text-xs md:text-sm mb-2">
                        Discover Flavors
                    </h4>

                    <h2 class="text-3xl md:text-4xl font-extrabold text-gray-800 font-serif">
                        Our <span class="text-primary">Categories</span>
                    </h2>

                    <div class="w-20 h-1.5 bg-primary mt-3 rounded-full md:mx-0 mx-auto"></div>
                </div>

                <!-- View All Button -->
                <router-link :to="{ name: 'frontend.menu', query: { s: categoryProps.slug } }" class="rounded-3xl capitalize text-sm leading-6 font-medium py-2 px-4 transition
                       text-primary bg-[#FFEDF4] hover:text-white hover:bg-primary md:self-end self-center">
                    {{ $t("button.view_all") }}
                </router-link>

            </div>

            <!-- Slider -->
            <div class="swiper menu-swiper mt-10">
                <CategoryComponent :categories="categories" :design="categoryProps.design" />
            </div>

        </div>
    </section>
    <!--======== Category Section END =========-->


    <!--========FEATURE PART START=========-->
    <FeaturedItemComponent />
    <!--========FEATURE PART END=========-->

    <!--========OFFER PART START=========-->
    <OfferComponent :limit="limit" bg="gray-50" />
    <!--========OFFER PART START=========-->

    <!--========POPULAR PART START=========-->
    <PopularItemComponent />
    <!--========POPULAR PART START=========-->

    <!--========Why Choose Us PART START=========-->
    <WhyChooseUsComponent />
    <!--========Why Choose Us PART END===========-->

    <!--========TESTIMONIAL PART START=========-->
    <TestimonialComponent />
    <!--========TESTIMONIAL PART END=========-->

    <BlogListComponent :limit="limit" />

    <!--========CONTACT PART START=========-->
    <ContactComponent />
    <!--========CONTACT PART END=========-->
</template>
<!-- change from local  -->
<script>
import { defineAsyncComponent } from "vue";
import categoryDesignEnum from "../../../enums/modules/categoryDesignEnum";
import statusEnum from "../../../enums/modules/statusEnum";
import LoadingComponent from "../components/LoadingComponent";
import SliderComponent from "../../frontend/home/SliderComponent";

const CategoryComponent = defineAsyncComponent(() => import("../components/CategoryComponent"));
const FeaturedItemComponent = defineAsyncComponent(() => import("../home/FeaturedItemComponent"));
const PopularItemComponent = defineAsyncComponent(() => import("../home/PopularItemComponent"));
const OfferComponent = defineAsyncComponent(() => import("../components/OfferComponent"));
const TrackOrderComponent = defineAsyncComponent(() => import("./TrackOrderComponent"));
const WhyChooseUsComponent = defineAsyncComponent(() => import("../home/WhyChooseUsComponent"));
const TestimonialComponent = defineAsyncComponent(() => import("../home/TestimonialComponent"));
const ContactComponent = defineAsyncComponent(() => import("../home/ContactComponent"));
const BlogListComponent = defineAsyncComponent(() => import("../components/BlogListComponent"));

export default {
    name: "HomeComponent",
    components: {
        TrackOrderComponent,
        CategoryComponent,
        SliderComponent,
        FeaturedItemComponent,
        PopularItemComponent,
        OfferComponent,
        LoadingComponent,
        WhyChooseUsComponent,
        TestimonialComponent,
        ContactComponent,
        BlogListComponent,
    },
    data() {
        return {
            loading: {
                isActive: false,
            },
            categoryProps: {
                design: categoryDesignEnum.FIRST,
                slug: '',
            },
            limit: 4,
        };
    },
    computed: {
        categories: function () {
            return this.$store.getters["frontendItemCategory/lists"];
        },
    },
    mounted() {
        this.$store.dispatch("frontendItemCategory/lists", {
            paginate: 0,
            order_column: "sort",
            order_type: "asc",
            status: statusEnum.ACTIVE,
        }).then(res => {
        }).catch((err) => {
        });
    },
    watch: {
        categories: {
            deep: true,
            handler(category) {
                if (category.length > 0) {
                    if (category[0].slug !== "undefined") {
                        this.categoryProps.slug = category[0].slug;
                    }
                }
            },
        },
    },
};
</script>
