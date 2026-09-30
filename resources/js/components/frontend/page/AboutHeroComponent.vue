<template>
    <section
        class="relative w-full overflow-hidden bg-white min-h-[500px] flex items-center"
    >
        <!-- Background Image -->
        <div class="absolute inset-0 md:left-[50%] lg:left-[35%]">
            <img
                src="/images/default/retaurant.webp"
                class="w-full h-full object-cover object-[center_60%]"
            />
        </div>

        <!-- Gradient Overlay to blend image -->
        <div
            class="absolute inset-0 bg-[linear-gradient(90deg,#FFFFFF_0%,#FFFFFF_45%,transparent_100%)] hidden md:block"
        ></div>
        <div
            class="absolute inset-0 bg-[linear-gradient(180deg,#FFFFFF_0%,#FFFFFF_60%,transparent_100%)] md:hidden"
        ></div>

        <div class="container mx-auto px-4 relative z-10 py-12 md:py-16">
            <div class="max-w-2xl">
                <!-- Subtitle -->
                <div class="flex items-center gap-4 mb-4">
                    <div class="w-8 h-[2px] bg-primary"></div>
                    <h4
                        class="text-primary font-bold tracking-widest uppercase text-xs"
                    >
                        ABOUT 3 GUYS GYRO & BURGER
                    </h4>
                    <div class="w-8 h-[2px] bg-primary"></div>
                </div>

                <!-- Main Title -->
                <h2
                    class="text-4xl md:text-5xl lg:text-6xl font-extrabold text-gray-800 font-serif mb-1 leading-tight"
                >
                    Good Food.
                </h2>
                <h2
                    class="text-4xl md:text-5xl lg:text-6xl font-extrabold text-primary font-serif mb-6 leading-tight"
                >
                    Great Experience.
                </h2>

                <!-- Description -->
                <p
                    class="text-gray-600 leading-relaxed mb-8 text-base md:text-lg font-medium max-w-xl"
                >
                    3 Guys Gyro & Burger was born from a simple passion for
                    great food and warm hospitality. We blend authentic recipes
                    with fresh ingredients to create meals that bring people
                    together.
                </p>

                <!-- Stats Grid -->
                <div class="flex flex-wrap gap-6 md:gap-8 mb-10">
                    <!-- Happy Customers -->
                    <div class="flex items-center gap-3">
                        <div
                            class="w-12 h-12 rounded-xl bg-[#FFEDF4] text-primary flex items-center justify-center flex-shrink-0 text-xl"
                        >
                            <i class="far fa-smile"></i>
                        </div>
                        <div>
                            <p
                                class="text-[13px] font-bold text-gray-800 leading-tight"
                            >
                                Happy<br />Customers
                            </p>
                            <p
                                class="text-lg font-extrabold text-primary mt-0.5"
                            >
                                10K+
                            </p>
                        </div>
                    </div>
                    <!-- Delicious Menu Items -->
                    <div class="flex items-center gap-3">
                        <div
                            class="w-12 h-12 rounded-xl bg-[#FFEDF4] text-primary flex items-center justify-center flex-shrink-0 text-xl"
                        >
                            <i class="fas fa-hamburger"></i>
                        </div>
                        <div>
                            <p
                                class="text-[13px] font-bold text-gray-800 leading-tight"
                            >
                                Delicious<br />Menu Items
                            </p>
                            <p
                                class="text-lg font-extrabold text-primary mt-0.5"
                            >
                                50+
                            </p>
                        </div>
                    </div>
                    <!-- Years of Experience -->
                    <div class="flex items-center gap-3">
                        <div
                            class="w-12 h-12 rounded-xl bg-[#FFEDF4] text-primary flex items-center justify-center flex-shrink-0 text-xl"
                        >
                            <i class="fas fa-award"></i>
                        </div>
                        <div>
                            <p
                                class="text-[13px] font-bold text-gray-800 leading-tight"
                            >
                                Years of<br />Experience
                            </p>
                            <p
                                class="text-lg font-extrabold text-primary mt-0.5"
                            >
                                8+
                            </p>
                        </div>
                    </div>
                </div>

                <!-- CTA Button -->
                <router-link
                    :to="{ name: 'frontend.home' }"
                    class="inline-flex items-center justify-center gap-2 bg-primary text-white px-6 py-3 rounded-full font-bold text-base shadow-lg shadow-primary/30 hover:shadow-primary/50 hover:-translate-y-1 transition-all duration-300"
                >
                    <span>Discover Our Story</span>
                    <i class="fas fa-arrow-right text-sm"></i>
                </router-link>
            </div>
        </div>
    </section>
</template>

<script>
export default {
    name: "AboutHeroComponent",
};
</script>
