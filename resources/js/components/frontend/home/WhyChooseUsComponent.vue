<template>
    <div class="relative overflow-hidden">
        <LoadingComponent :props="loading" />
        
        <!-- Background Decorations (Subtle System Color Accents) -->
        <div class="absolute top-0 right-0 -mr-20 -mt-20 w-80 h-80 rounded-full bg-primary/10 blur-3xl opacity-50 pointer-events-none"></div>
        <div class="absolute bottom-1/4 left-0 -ml-20 w-64 h-64 rounded-full bg-primary/5 blur-3xl opacity-50 pointer-events-none"></div>

        <!-- Reduced padding (py-12) to make section compact -->
        <section class="py-12 bg-gray-50 relative z-10 min-h-[400px]" v-show="features.length > 0 || loading">
            <div class="container mx-auto px-4" v-if="features.length > 0">
                
                <!-- Reduced margin (mb-10) and gap to fit 100vh -->
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-center mb-10">
                    <!-- Left Side: Text Content -->
                    <div class="lg:col-span-4 text-left">
                        <h4 class="text-primary font-bold tracking-widest uppercase text-xs mb-2 pl-1">
                            Our Promise
                        </h4>
                        <!-- Smaller text sizes -->
                        <h2 class="text-3xl md:text-4xl font-extrabold text-gray-800 font-serif mb-4 leading-tight">
                            Why <span class="text-primary">Choose Us</span>
                        </h2>
                        
                        <!-- Decorative Underline -->
                        <div class="w-16 h-1.5 bg-primary rounded-full mb-6"></div>
                        
                        <p class="text-gray-500 leading-relaxed mb-6 text-base font-light">
                            We don't just cook food; we craft culinary experiences. Discover the excellence that keeps our guests returning.
                        </p>
                        
                        <button class="group bg-primary text-white px-6 py-3 rounded-full font-bold shadow-lg hover:shadow-primary/40 hover:-translate-y-1 transition-all duration-300 flex items-center gap-2 text-sm">
                            <span>Learn More</span>
                            <i class="fas fa-arrow-right transform group-hover:translate-x-1 transition-transform"></i>
                        </button>
                    </div>

                    <!-- Right Side: Feature Cards Grid -->
                    <div class="lg:col-span-8 grid grid-cols-1 md:grid-cols-2 gap-4 pl-0 lg:pl-10">
                        <div 
                            v-for="(feature, index) in features" 
                            :key="index"
                            class="group bg-white p-6 rounded-[1.5rem] shadow-sm hover:shadow-xl transition-all duration-500 border border-gray-100 hover:border-primary/20 transform hover:-translate-y-1"
                        >
                            <div class="mb-4 inline-flex items-center justify-center w-12 h-12 rounded-xl bg-primary/10 text-primary group-hover:bg-primary group-hover:text-white transition-all duration-500 rotate-0 group-hover:rotate-0">
                                <i :class="feature.icon || 'fas fa-star'" class="text-xl"></i>
                            </div>
                            
                            <h3 class="text-lg font-bold text-gray-800 mb-2 group-hover:text-primary transition-colors font-serif">
                                {{ feature.title }}
                            </h3>
                            
                            <p class="text-gray-500 leading-relaxed text-xs">
                                {{ feature.description }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</template>

<script>
import LoadingComponent from "../components/LoadingComponent";

export default {
    name: "WhyChooseUsComponent",
    components: {
        LoadingComponent
    },
    props: {},
    data() {
        return {
            loading: {
                isActive: false,
            },
            // Stats state for animation
            animatedStats: {
                years: 0,
                awards: 0,
                locations: 0
            },
            hasAnimated: false,
            observer: null,
            
            // Fallback data
            localFallbackFeatures: [
                {
                    title: "Fresh Ingredients",
                    description: "We source our produce daily from local organic farms to ensure the crispest taste in every bite.",
                    icon: "fas fa-leaf" 
                },
                {
                    title: "Expert Chefs",
                    description: "Our chefs bring decades of skill and passion to every dish we serve.",
                    icon: "fas fa-utensils"
                },
                {
                    title: "Variety of menu",
                    description: "We offer a rich and diverse menu so every guest finds exactly what they are looking for, every single time.",
                    icon: "fas fa-book-open"
                },
                {
                    title: "Quality Service",
                    description: "Our staff is trained to provide the best possible service, ensuring a memorable dining experience.",
                    icon: "fas fa-concierge-bell"
                }
            ]
        };
    },
    mounted() {
        this.setupIntersectionObserver();
        
        // Data fetching logic (Simulated for this component structure)
        // try {
        //     this.loading.isActive = true;
        //     this.$store.dispatch("frontendPage/whyChooseUs", { ... }).then(...)
        // } ...
    },
    beforeDestroy() {
        if (this.observer) {
            this.observer.disconnect();
        }
    },
    computed: {
        features: function () {
            const storedFeatures = this.$store.getters["frontendPage/whyChooseUs"];
            if (storedFeatures && storedFeatures.length > 0) {
                return storedFeatures;
            }
            return this.localFallbackFeatures;
        },
    },
    methods: {
        setupIntersectionObserver() {
            // Options for the observer (trigger when 50% visible)
            const options = {
                threshold: 0.5,
                rootMargin: "0px"
            };

            this.observer = new IntersectionObserver((entries) => {
                entries.forEach((entry) => {
                    if (entry.isIntersecting && !this.hasAnimated) {
                        this.animateStats();
                        this.hasAnimated = true; // Ensure it only runs once
                        this.observer.disconnect(); // Stop observing after triggering
                    }
                });
            }, options);

            // Observe the stats section
            if (this.$refs.statsSection) {
                this.observer.observe(this.$refs.statsSection);
            }
        },
        animateStats() {
            const duration = 1500; // Animation duration in ms
            const interval = 20; // Update interval in ms
            const steps = duration / interval;

            const targets = {
                years: 5,
                awards: 7,
                locations: 10
            };

            const increments = {
                years: targets.years / steps,
                awards: targets.awards / steps,
                locations: targets.locations / steps
            };

            let currentStep = 0;

            const timer = setInterval(() => {
                currentStep++;
                
                // Update numbers
                if (this.animatedStats.years < targets.years) {
                    this.animatedStats.years = Math.min(Math.ceil(increments.years * currentStep), targets.years);
                }
                if (this.animatedStats.awards < targets.awards) {
                    this.animatedStats.awards = Math.min(Math.ceil(increments.awards * currentStep), targets.awards);
                }
                if (this.animatedStats.locations < targets.locations) {
                    this.animatedStats.locations = Math.min(Math.ceil(increments.locations * currentStep), targets.locations);
                }

                if (currentStep >= steps) {
                    clearInterval(timer);
                    // Ensure final values are exact
                    this.animatedStats = { ...targets };
                }
            }, interval);
        }
    },
};
</script>

<style scoped>
/* Slow bounce animation for the floating icons */
.animate-bounce-slow {
    animation: bounce 3s infinite;
}

@keyframes bounce {
    0%, 100% {
        transform: translateY(-50%) translateX(var(--tw-translate-x)) translateY(-10%);
        animation-timing-function: cubic-bezier(0.8, 0, 1, 1);
    }
    50% {
        transform: translateY(-50%) translateX(var(--tw-translate-x)) translateY(10%);
        animation-timing-function: cubic-bezier(0, 0, 0.2, 1);
    }
}
</style>