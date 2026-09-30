<template>
    <section class="relative bg-gray-50 py-10 overflow-hidden">
        <LoadingComponent :props="loading" />

        <div class="absolute top-0 right-0 -mr-20 -mt-20 w-80 h-80 rounded-full bg-amber-50 opacity-50 blur-3xl"></div>
        <div class="absolute bottom-0 left-0 -ml-20 -mb-20 w-80 h-80 rounded-full bg-orange-50 opacity-50 blur-3xl"></div>

        <div class="container mx-auto px-4 relative z-10">
            <div class="text-center mb-12">
                <h4 class="text-primary/80 font-bold tracking-widest uppercase text-sm mb-2">Get In Touch</h4>
                <h2 class="text-4xl md:text-5xl font-extrabold text-gray-800 font-serif mb-4">
                    Hungry for Answers? <br />
                    <span class="text-primary mt-3">We Deliver.</span>
                </h2>
                <div class="w-20 h-1.5 bg-primary mx-auto rounded-full mt-3"></div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 lg:gap-20 items-start">

                <!-- LEFT SIDE: Location Info -->
                <div class="flex flex-col space-y-8">
                    <div class="bg-white rounded-3xl p-6 shadow-xl border border-gray-100 flex flex-col md:flex-row items-center md:items-start gap-6 relative overflow-hidden group hover:border-primary transition-colors duration-300"
                        style="height: 300px;">
                        <div class="flex-shrink-0 relative z-10">
                            <div class="w-32 h-32 bg-gray-200 rounded-full flex items-center justify-center text-primary text-5xl transform group-hover:scale-110 transition-transform duration-300">
                                <img src="/images/default/cooking.png" class="h-[70px] w-[70px]" alt="">
                            </div>
                        </div>
                        <div class="flex-grow text-center md:text-left">
                            <h3 class="text-2xl font-bold text-gray-800 mb-1 uppercase tracking-tight">{{ branchName }}</h3>
                            <p class="text-gray-500 text-sm mb-4 leading-relaxed">{{ branchAddress }}</p>
                            <h4 class="text-xs font-bold text-gray-600 uppercase tracking-widest mb-2">Hours of Operation</h4>
                            <ul class="text-sm text-gray-600 space-y-1 mb-4">
                                <li class="flex justify-between md:justify-start gap-4">
                                    <span class="font-medium">Sun-Thu:</span>
                                    <span>11:00 AM - 10:00 PM</span>
                                </li>
                                <li class="flex justify-between md:justify-start gap-4">
                                    <span class="font-medium">Fri-Sat:</span>
                                    <span>11:00 AM - 10:00 PM</span>
                                </li>
                            </ul>
                            <a v-if="branchPhone" :href="branchPhoneHref"
                                class="inline-flex items-center text-primary font-bold text-lg hover:text-primary-dark transition-colors">
                                <i class="fas fa-phone-alt mr-2"></i> {{ branchPhone }}
                            </a>
                        </div>
                    </div>

                    <!-- ✅ Map Section - replaced iframe with JS API -->
                    <div ref="mapContainer" class="bg-gray-200 rounded-3xl overflow-hidden shadow-inner relative" style="height: 340px;"></div>
                </div>

                <!-- RIGHT SIDE: Contact Form (unchanged) -->
                <div class="bg-white rounded-[2rem] shadow-2xl p-8 md:p-10 border-t-4 border-primary relative">
                    <h3 class="text-2xl font-bold text-gray-800 mb-6">Drop us a line</h3>
                    <form @submit.prevent="submitForm">
                        <div class="space-y-5">
                            <div>
                                <label for="name" class="block text-sm font-bold text-gray-700 mb-2 uppercase tracking-wide">Your Name</label>
                                <input v-model="form.name" type="text" id="name"
                                    class="block w-full px-4 py-3 text-gray-900 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-all duration-200"
                                    placeholder="e.g. John Doe" required />
                            </div>
                            <div>
                                <label for="email" class="block text-sm font-bold text-gray-700 mb-2 uppercase tracking-wide">Email Address</label>
                                <input v-model="form.email" type="email" id="email"
                                    class="block w-full px-4 py-3 text-gray-900 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-all duration-200"
                                    placeholder="e.g. john@example.com" required />
                            </div>
                            <div>
                                <label for="phone" class="block text-sm font-bold text-gray-700 mb-2 uppercase tracking-wide">
                                    Phone Number <span class="text-gray-600 font-normal lowercase ml-1">(optional)</span>
                                </label>
                                <input v-model="form.phone" type="tel" id="phone"
                                    class="block w-full px-4 py-3 text-gray-900 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-all duration-200"
                                    placeholder="e.g. +1 (555) 000-0000" />
                            </div>
                            <div>
                                <label for="message" class="block text-sm font-bold text-gray-700 mb-2 uppercase tracking-wide">How can we help?</label>
                                <textarea v-model="form.message" id="message" rows="4"
                                    class="block w-full px-4 py-3 text-gray-900 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-all duration-200 resize-none"
                                    placeholder="Tell us about your event or question..." required></textarea>
                            </div>
                        </div>
                        <button type="submit"
                            class="mt-8 w-full bg-primary text-white font-bold py-4 rounded-xl shadow-lg hover:bg-primary/50 hover:shadow-xl hover:-translate-y-1 transition-all duration-300 flex items-center justify-center gap-2">
                            <span>Send Message</span>
                            <i class="fas fa-paper-plane text-sm"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </section>
</template>

<script>
import alertService from "../../../services/alertService";
import LoadingComponent from "../components/LoadingComponent";
import statusEnum from "../../../enums/modules/statusEnum";
import ENV from "../../../config/env";

export default {
    name: "ContactSectionComponent",
    components: { LoadingComponent },
    data() {
        return {
            loading: { isActive: false },
            form: {
                name: "",
                email: "",
                phone: "",
                message: ""
            },
            branchProps: {
                paginate: 0,
                order_column: "id",
                order_type: "asc",
                status: statusEnum.ACTIVE,
            }
        };
    },
    computed: {
        branches: function () {
            return this.$store.getters["frontendBranch/lists"] || [];
        },
        setting: function () {
            return this.$store.getters["frontendSetting/lists"] || {};
        },
        globalState: function () {
            return this.$store.getters["globalState/lists"] || {};
        },
        selectedBranch: function () {
            if (!this.branches.length) return {};
            const selectedBranchId = Number(this.globalState.branch_id || this.setting.site_default_branch || 0);
            return this.branches.find((branch) => Number(branch.id) === selectedBranchId) || this.branches[0];
        },
        branchName: function () {
            return this.selectedBranch.name || "Our Location";
        },
        branchAddress: function () {
            return this.selectedBranch.address || "Address will be available soon.";
        },
        branchPhone: function () {
            return this.selectedBranch.phone || "";
        },
        branchPhoneHref: function () {
            return `tel:${this.branchPhone.replace(/[^\d+]/g, "")}`;
        },

        // ✅ Google Maps redirect link
        branchMapLink: function () {
            const lat = this.selectedBranch.latitude;
            const lng = this.selectedBranch.longitude;
            if (lat && lng) {
                return `https://www.google.com/maps/search/3+Guys+Gyro+%26+Burger/@${lat},${lng},17z`;
            }
            return `https://www.google.com/maps/search/?api=1&query=${encodeURIComponent(this.branchAddress)}`;
        },
    },
    mounted() {
        this.loading.isActive = true;
        Promise.all([
            this.$store.dispatch("frontendSetting/lists"),
            this.$store.dispatch("frontendBranch/lists", this.branchProps),
        ]).then(() => {
            this.loading.isActive = false;
            this.$nextTick(() => {
                this.initMap(); // ✅ init map after data is ready
            });
        }).catch((err) => {
            this.loading.isActive = false;
            alertService.error(err.response?.data?.message || err.message);
        });
    },
    methods: {
        // ✅ Renders real Google Map with clickable marker
        async initMap() {
            const lat = parseFloat(this.selectedBranch.latitude);
            const lng = parseFloat(this.selectedBranch.longitude);

            if (!lat || !lng || !this.$refs.mapContainer) return;

            const { Loader } = await import(/* webpackChunkName: "google-maps" */ "google-maps");
            const loader = new Loader(ENV.GOOGLE_MAP_KEY || this.setting.google_map_key, { libraries: ["places", "geometry", "drawing"] });
            const google = await loader.load();

            const position = { lat, lng };

            const map = new google.maps.Map(this.$refs.mapContainer, {
                zoom: 19,
                center: position,
            });

            // ✅ Marker — click opens Google Maps
            const marker = new google.maps.Marker({
                position: position,
                map: map,
                title: this.branchName,
            });

            marker.addListener("click", () => {
                window.open(this.branchMapLink, "_blank");
            });
        },

        submitForm() {
            this.loading.isActive = true;
            this.$store.dispatch("frontendContact/send", this.form).then(() => {
                this.loading.isActive = false;
                alertService.success(this.$t('message.sent_successfully') || "Message Sent Successfully!");
                this.form = { name: "", email: "", phone: "", message: "" };
            }).catch((err) => {
                this.loading.isActive = false;
                if (err.response?.data?.errors) {
                    const errors = Object.values(err.response.data.errors).flat();
                    alertService.error(errors[0]);
                    return;
                }
                alertService.error(err.response?.data?.message || err.message);
            });
        }
    }
};
</script>