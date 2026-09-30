<template>
    <div id="recaptcha-modal" v-show="isVisible" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm transition-opacity">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-md overflow-hidden relative" @click.stop>
            <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50">
                <h3 class="text-lg font-bold text-gray-800">Security Check</h3>
                <button @click="close" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            
            <div class="p-6 flex flex-col items-center justify-center min-h-[150px]">
                <div v-if="loading" class="flex flex-col items-center">
                    <div class="w-8 h-8 border-4 border-primary border-t-transparent rounded-full animate-spin mb-4"></div>
                    <p class="text-sm text-gray-500">Loading reCAPTCHA...</p>
                </div>
                
                <div v-show="!loading" id="recaptcha-container" class="w-full flex justify-center"></div>
                
                <p v-if="error" class="text-red-500 text-sm mt-4 text-center">{{ error }}</p>
            </div>
            
            <div class="px-6 py-4 bg-gray-50 flex justify-end gap-3 rounded-b-2xl">
                <button @click="close" type="button" class="px-5 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-xl hover:bg-gray-50 transition-colors">
                    Cancel
                </button>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: "RecaptchaModalComponent",
    data() {
        return {
            isVisible: false,
            loading: true,
            error: null,
            widgetId: null,
        };
    },
    computed: {
        setting() {
            return this.$store.getters['frontendSetting/lists'];
        },
        siteKey() {
            return this.setting.google_recaptcha_site_key;
        },
        version() {
            return this.setting.google_recaptcha_version;
        }
    },
    methods: {
        open() {
            this.isVisible = true;
            this.loading = true;
            this.error = null;
            this.loadScript();
        },
        close() {
            this.isVisible = false;
            this.reset();
        },
        loadScript() {
            if (window.grecaptcha && window.grecaptcha.render) {
                this.renderWidget();
                return;
            }

            const scriptId = 'recaptcha-script';
            if (!document.getElementById(scriptId)) {
                const script = document.createElement('script');
                script.id = scriptId;
                script.src = `https://www.google.com/recaptcha/api.js?render=explicit`;
                script.async = true;
                script.defer = true;
                document.head.appendChild(script);
                
                script.onload = () => {
                    this.checkGrecaptcha();
                };
            } else {
                this.checkGrecaptcha();
            }
        },
        checkGrecaptcha() {
            if (window.grecaptcha && window.grecaptcha.render) {
                this.renderWidget();
            } else {
                setTimeout(() => this.checkGrecaptcha(), 100);
            }
        },
        renderWidget() {
            this.loading = false;
            
            // Wait for next tick so DOM is ready
            this.$nextTick(() => {
                const container = document.getElementById('recaptcha-container');
                if (container && this.widgetId === null) {
                    try {
                        this.widgetId = window.grecaptcha.render('recaptcha-container', {
                            'sitekey': this.siteKey,
                            'theme': 'light',
                            'size': this.version === 'v3' ? 'invisible' : 'normal',
                            'callback': this.onVerify,
                            'expired-callback': this.onExpired,
                            'error-callback': this.onError,
                        });
                        
                        if (this.version === 'v3') {
                            window.grecaptcha.execute(this.widgetId);
                        }
                    } catch (e) {
                        this.error = "Failed to load reCAPTCHA. Please try again.";
                    }
                } else if (this.widgetId !== null) {
                    window.grecaptcha.reset(this.widgetId);
                    if (this.version === 'v3') {
                        window.grecaptcha.execute(this.widgetId);
                    }
                }
            });
        },
        onVerify(token) {
            this.$emit('verified', token);
            setTimeout(() => {
                this.close();
            }, 500);
        },
        onExpired() {
            this.error = "reCAPTCHA expired. Please verify again.";
        },
        onError() {
            this.error = "Network error. Please check your connection.";
        },
        reset() {
            if (this.widgetId !== null && window.grecaptcha) {
                window.grecaptcha.reset(this.widgetId);
            }
        }
    },
    beforeUnmount() {
        if (this.widgetId !== null && window.grecaptcha) {
            window.grecaptcha.reset(this.widgetId);
            this.widgetId = null;
        }
    }
};
</script>
