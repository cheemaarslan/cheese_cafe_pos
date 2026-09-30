<template>
    <LoadingComponent :props="loading" />
    <section class="pt-8 pb-16">
        <div class="container max-w-[360px] py-6 p-4 mb-6 sm:px-6 shadow-xs rounded-2xl bg-white text-center">
            <h2 class="capitalize mb-6 text-center text-[22px] font-semibold leading-[34px] text-heading">
                Authenticating...
            </h2>
            <p v-if="error" class="text-red-500 mb-4">{{ error }}</p>
            <p v-else class="text-sm text-[#6E7191]">Please wait while we log you in securely.</p>
            
            <div v-if="error" class="mt-4">
                <router-link :to="{ name: 'auth.login' }" class="text-primary hover:underline">
                    Back to Login
                </router-link>
            </div>
        </div>
    </section>
</template>

<script>
import router from "../../../router";
import LoadingComponent from "../components/LoadingComponent";
import alertService from "../../../services/alertService";
import appService from "../../../services/appService";
import { routes } from "../../../router";

export default {
    name: "GoogleCallbackComponent",
    components: { LoadingComponent },
    data() {
        return {
            loading: {
                isActive: true,
            },
            error: null,
        }
    },
    computed: {
        carts: function () {
            return this.$store.getters['frontendCart/lists'];
        },
        permission: function () {
            return this.$store.getters.authPermission;
        },
    },
    mounted() {
        this.processCallback();
    },
    methods: {
        processCallback: function() {
            const urlParams = new URLSearchParams(window.location.search);
            const encodedData = urlParams.get('data');
            
            if (!encodedData) {
                this.error = "Authentication failed. No token provided.";
                this.loading.isActive = false;
                return;
            }
            
            try {
                // Decode base64 and parse JSON
                const decodedData = atob(decodeURIComponent(encodedData));
                const responseData = JSON.parse(decodedData);
                
                if (responseData.token && responseData.user) {
                    // Use the same authLogin mutation as normal login
                    this.$store.commit('authLogin', responseData);
                    
                    alertService.success("Logged in successfully!");
                    
                    this.loading.isActive = false;

                    if (this.carts.length > 0) {
                        router.push({ name: "frontend.checkout" });
                    } else {
                        router.push({ name: "frontend.home" });
                    }
                    
                    setTimeout(() => {
                        appService.recursiveRouter(routes, this.permission);
                    }, 1000);
                } else {
                    throw new Error("Invalid payload structure");
                }
            } catch (err) {
                console.error(err);
                this.error = "Authentication processing failed. Please try again.";
                this.loading.isActive = false;
            }
        }
    }
};
</script>
