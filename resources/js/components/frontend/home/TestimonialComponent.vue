<template>
    <section class="relative bg-gray-50 py-10 overflow-hidden">
        <LoadingComponent :props="loading" />

        <!-- Background Decor (Dots/Circles) -->
        <div
            class="absolute top-10 left-10 w-4 h-4 bg-amber-400 rounded-full opacity-60 animate-bounce"
        ></div>
        <div
            class="absolute bottom-20 right-20 w-6 h-6 bg-orange-300 rounded-full opacity-60 animate-pulse"
        ></div>

        <div
            class="container mx-auto px-4 relative z-10"
            v-if="testimonials.length > 0"
        >
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                <!-- LEFT SIDE: Chef Image & Decorations -->
                <div class="relative hidden lg:block h-[500px]">
                    <!-- Large Orange Circle Background -->
                    <div
                        class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-[400px] h-[400px] bg-amber-500 rounded-full opacity-10"
                    ></div>
                    <div
                        class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-[350px] h-[350px] bg-amber-500 rounded-full opacity-20"
                    ></div>

                    <!-- Delivery Person Image -->
                    <div
                        class="absolute inset-0 flex items-center justify-center"
                    >
                        <img
                            src="/images/default/deliveryman.png"
                            alt="Delivery Person"
                            class="h-full object-contain relative z-10 drop-shadow-2xl hover:scale-105 transition-transform duration-500"
                        />
                    </div>

                    <!-- Floating Badge: Happy Customer (Bottom Left) -->
                    <div
                        class="absolute bottom-10 left-0 bg-white p-3 rounded-2xl shadow-xl z-20 flex items-center gap-3 border border-gray-100 animate-[bounce_3s_infinite]"
                    >
                        <div class="flex -space-x-2">
                            <div
                                class="w-8 h-8 rounded-full border-2 border-white bg-gray-200 overflow-hidden"
                            >
                                <img
                                    src="https://i.pravatar.cc/100?img=1"
                                    alt="User"
                                />
                            </div>
                            <div
                                class="w-8 h-8 rounded-full border-2 border-white bg-gray-200 overflow-hidden"
                            >
                                <img
                                    src="https://i.pravatar.cc/100?img=2"
                                    alt="User"
                                />
                            </div>
                            <div
                                class="w-8 h-8 rounded-full border-2 border-white bg-gray-200 overflow-hidden"
                            >
                                <img
                                    src="https://i.pravatar.cc/100?img=3"
                                    alt="User"
                                />
                            </div>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-gray-800">
                                Happy Customers
                            </p>
                            <div class="flex text-amber-500 text-[10px]">
                                <i class="fas fa-star"></i
                                ><i class="fas fa-star"></i
                                ><i class="fas fa-star"></i
                                ><i class="fas fa-star"></i
                                ><i class="fas fa-star-half-alt"></i>
                                <span class="text-gray-400 ml-1">(4.9)</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- RIGHT SIDE: Content & Slider -->
                <div class="flex flex-col justify-center">
                    <!-- Heading Area -->
                    <div class="text-left mb-8">
                        <h2
                            class="text-3xl md:text-5xl font-extrabold text-gray-800 font-serif mb-4"
                        >
                            Our Happy Client <br />
                            <span class="text-primary mt-3">Says About Us</span>
                        </h2>

                        <!-- Reviewer Badge Group -->
                        <div
                            class="inline-flex items-center bg-white px-4 py-2 rounded-full shadow-md border border-gray-100 mb-6"
                        >
                            <div class="flex -space-x-3 mr-3">
                                <img
                                    class="w-8 h-8 rounded-full border-2 border-white"
                                    src="https://i.pravatar.cc/100?img=5"
                                    alt=""
                                />
                                <img
                                    class="w-8 h-8 rounded-full border-2 border-white"
                                    src="https://i.pravatar.cc/100?img=9"
                                    alt=""
                                />
                                <div
                                    class="w-8 h-8 rounded-full border-2 border-white bg-primary flex items-center justify-center text-[10px] text-white font-bold"
                                >
                                    15k+
                                </div>
                            </div>
                            <span class="text-sm font-bold text-gray-700"
                                >Our Reviewers</span
                            >
                        </div>
                    </div>

                    <!-- TESTIMONIAL SLIDER -->
                    <div class="relative w-full">
                        <!-- Navigation Buttons (Top Right of Slider) -->
                        <div class="absolute -top-16 right-0 flex gap-2 z-20">
                            <button
                                @click="prevSlide"
                                class="w-10 h-10 rounded-full bg-white shadow-md flex items-center justify-center text-gray-600 hover:bg-primary hover:text-white transition-colors duration-300"
                            >
                                <i class="fas fa-chevron-left"></i>
                            </button>
                            <button
                                @click="nextSlide"
                                class="w-10 h-10 rounded-full bg-white shadow-md flex items-center justify-center text-gray-600 hover:bg-primary hover:text-white transition-colors duration-300"
                            >
                                <i class="fas fa-chevron-right"></i>
                            </button>
                        </div>

                        <!-- Card Container -->
                        <div class="overflow-hidden p-2">
                            <!-- Padding for shadow -->
                            <div
                                class="flex transition-transform duration-500 ease-in-out"
                                :style="{
                                    transform: `translateX(-${currentSlide * 100}%)`,
                                }"
                            >
                                <div
                                    v-for="(testimonial, index) in testimonials"
                                    :key="index"
                                    class="w-full flex-shrink-0"
                                >
                                    <div
                                        class="p-8 md:p-10 rounded-[2rem] shadow-sm border border-gray-100 relative mx-1"
                                    >
                                        <!-- Quote Icon -->
                                        <div
                                            class="absolute top-8 right-8 text-6xl text-amber-100 font-serif leading-none"
                                        >
                                            &rdquo;
                                        </div>

                                        <!-- Testimonial Text -->
                                        <p
                                            class="text-gray-600 text-lg leading-relaxed mb-8 italic relative z-10"
                                        >
                                            "{{ testimonial.review }}"
                                        </p>

                                        <!-- User Profile Section -->
                                        <div class="flex items-center gap-4">
                                            <!-- User Image -->
                                            <div class="relative">
                                                <div
                                                    class="w-16 h-16 rounded-full overflow-hidden border-2 border-primary p-0.5"
                                                >
                                                    <img
                                                        :src="testimonial.image"
                                                        class="w-full h-full object-cover rounded-full"
                                                        :alt="testimonial.name"
                                                    />
                                                </div>
                                                <div
                                                    class="absolute -bottom-1 -right-1 bg-primary text-white w-6 h-6 rounded-full flex items-center justify-center text-[10px]"
                                                >
                                                    <i class="fas fa-check"></i>
                                                </div>
                                            </div>

                                            <!-- User Info -->
                                            <div>
                                                <h4
                                                    class="text-xl font-bold text-gray-800"
                                                >
                                                    {{ testimonial.name }}
                                                </h4>
                                                <p
                                                    class="text-sm text-gray-500 mb-1"
                                                >
                                                    {{
                                                        testimonial.designation
                                                    }}
                                                </p>

                                                <!-- Stars -->
                                                <div
                                                    class="flex text-amber-400 text-sm"
                                                >
                                                    <i
                                                        v-for="n in 5"
                                                        :key="n"
                                                        class="fas fa-star"
                                                        :class="{
                                                            'text-gray-300':
                                                                n >
                                                                testimonial.rating,
                                                        }"
                                                    ></i>
                                                    <span
                                                        class="text-gray-400 ml-2 text-xs font-medium"
                                                        >{{
                                                            testimonial.rating
                                                        }}</span
                                                    >
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Pagination Dots -->
                        <div class="flex justify-center mt-6 gap-2">
                            <button
                                v-for="(dot, idx) in testimonials"
                                :key="idx"
                                @click="goToSlide(idx)"
                                class="w-3 h-3 rounded-full transition-all duration-300"
                                :class="
                                    currentSlide === idx
                                        ? 'bg-primary w-6'
                                        : 'bg-gray-300'
                                "
                            ></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>

<script>
import LoadingComponent from "../components/LoadingComponent";

export default {
    name: "TestimonialComponent",
    components: {
        LoadingComponent,
    },
    data() {
        return {
            loading: {
                isActive: false,
            },
            currentSlide: 0,
            autoplayInterval: null,
            // Fallback data
            localTestimonials: [
                {
                    id: 1,
                    name: "Olivia Fernando",
                    designation: "Food Enthusiast",
                    image: "https://i.pravatar.cc/150?img=5",
                    rating: 5,
                    review: "I absolutely love dining here! The food is always delicious, and the service is top-notch. The staff are friendly and attentive, making every visit a delightful experience. I highly recommend trying their signature dishes; they never disappoint.",
                },
                {
                    id: 2,
                    name: "James Carter",
                    designation: "Local Guide",
                    image: "https://i.pravatar.cc/150?img=11",
                    rating: 5,
                    review: "A hidden gem in the city. The ambiance is perfect for a romantic dinner or a casual meet-up. The flavors are authentic and the presentation is art on a plate. Definitely coming back for the weekend brunch specials!",
                },
                {
                    id: 3,
                    name: "Sarah Jenkins",
                    designation: "Regular Customer",
                    image: "https://i.pravatar.cc/150?img=9",
                    rating: 4,
                    review: "Fast delivery and the food arrived piping hot. The packaging was eco-friendly which I appreciate. The taste was consistent with what I have had at the restaurant. Great job keeping the quality high for deliveries.",
                },
            ],
        };
    },
    computed: {
        testimonials() {
            // In a real app, you would fetch this from Vuex
            // const stored = this.$store.getters["frontendPage/testimonials"];
            // return stored && stored.length ? stored : this.localTestimonials;
            return this.localTestimonials;
        },
    },
    mounted() {
        try {
            this.$store
                .dispatch("frontendTestimonial/lists")
                .then((res) => {})
                .catch((err) => {});
        } catch (err) {}
        this.startAutoplay();
    },
    beforeDestroy() {
        this.stopAutoplay();
    },
    methods: {
        nextSlide() {
            this.currentSlide =
                (this.currentSlide + 1) % this.testimonials.length;
            this.resetAutoplay();
        },
        prevSlide() {
            this.currentSlide =
                (this.currentSlide - 1 + this.testimonials.length) %
                this.testimonials.length;
            this.resetAutoplay();
        },
        goToSlide(index) {
            this.currentSlide = index;
            this.resetAutoplay();
        },
        startAutoplay() {
            this.autoplayInterval = setInterval(() => {
                this.nextSlide();
            }, 5000); // Change slide every 5 seconds
        },
        stopAutoplay() {
            if (this.autoplayInterval) {
                clearInterval(this.autoplayInterval);
            }
        },
        resetAutoplay() {
            this.stopAutoplay();
            this.startAutoplay();
        },
    },
};
</script>
