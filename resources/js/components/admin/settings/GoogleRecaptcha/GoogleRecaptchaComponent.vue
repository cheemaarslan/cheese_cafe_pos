<template>
    <LoadingComponent :props="loading" />

    <div id="googleRecaptcha" class="db-card db-tab-div active">
        <div class="db-card-header">
            <h3 class="db-card-title">Google reCAPTCHA Settings</h3>
        </div>
        <div class="db-card-body">
            <form @submit.prevent="save">
                <div class="form-row">
                    <div class="form-col-12 sm:form-col-12">
                        <label class="db-field-title required" for="active">Enable Google reCAPTCHA</label>
                        <div class="db-field-radio-group">
                            <div class="db-field-radio">
                                <div class="custom-radio">
                                    <input :value="true" v-model="form.is_google_recaptcha_enable" id="enable_recaptcha"
                                        type="radio" class="custom-radio-field" />
                                    <span class="custom-radio-span"></span>
                                </div>
                                <label for="enable_recaptcha" class="db-field-label">Enable</label>
                            </div>
                            <div class="db-field-radio">
                                <div class="custom-radio">
                                    <input :value="false" v-model="form.is_google_recaptcha_enable" type="radio"
                                        id="disable_recaptcha" class="custom-radio-field" />
                                    <span class="custom-radio-span"></span>
                                </div>
                                <label for="disable_recaptcha" class="db-field-label">Disable</label>
                            </div>
                        </div>
                        <small class="db-field-alert" v-if="errors.is_google_recaptcha_enable">{{
                            errors.is_google_recaptcha_enable[0]
                        }}</small>
                    </div>

                    <div class="form-col-12 sm:form-col-12">
                        <label for="site_key" class="db-field-title">Site Key</label>
                        <input v-model="form.site_key" v-bind:class="errors.site_key ? 'invalid' : ''" type="text"
                            id="site_key" class="db-field-control" />
                        <small class="db-field-alert" v-if="errors.site_key">{{ errors.site_key[0] }}</small>
                    </div>

                    <div class="form-col-12 sm:form-col-12">
                        <label for="private_key" class="db-field-title">Private Key (Secret Key)</label>
                        <input v-model="form.private_key" v-bind:class="errors.private_key ? 'invalid' : ''" type="text"
                            id="private_key" class="db-field-control" />
                        <small class="db-field-alert" v-if="errors.private_key">{{ errors.private_key[0] }}</small>
                    </div>

                    <div class="form-col-12 sm:form-col-12">
                        <label for="version" class="db-field-title">Version</label>
                        <select v-model="form.version" id="version" class="db-field-control" v-bind:class="errors.version ? 'invalid' : ''">
                            <option value="">Select Version</option>
                            <option value="v2">v2</option>
                            <option value="v3">v3</option>
                        </select>
                        <small class="db-field-alert" v-if="errors.version">{{ errors.version[0] }}</small>
                    </div>

                    <div class="form-col-12">
                        <button type="submit" class="db-btn text-white bg-primary">
                            <i class="lab lab-save"></i>
                            <span>{{ $t("button.save") }}</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</template>

<script>
import LoadingComponent from "../../components/LoadingComponent";
import alertService from "../../../../services/alertService";

export default {
    name: "GoogleRecaptchaComponent",
    components: { LoadingComponent },
    data() {
        return {
            loading: {
                isActive: false,
            },
            form: {
                is_google_recaptcha_enable: false,
                site_key: "",
                private_key: "",
                version: "",
            },
            errors: {},
        };
    },
    mounted() {
        try {
            this.loading.isActive = true;
            this.$store
                .dispatch("googleRecaptcha/lists")
                .then((res) => {
                    this.form = {
                        is_google_recaptcha_enable: res.data.data.is_google_recaptcha_enable === "true" || res.data.data.is_google_recaptcha_enable === true || res.data.data.is_google_recaptcha_enable === "1" || res.data.data.is_google_recaptcha_enable === 1,
                        site_key: res.data.data.site_key ?? "",
                        private_key: res.data.data.private_key ?? "",
                        version: res.data.data.version ?? "",
                    };
                    this.loading.isActive = false;
                })
                .catch((err) => {
                    this.loading.isActive = false;
                });
        } catch (err) {
            this.loading.isActive = false;
        }
    },
    methods: {
        save: function () {
            try {
                this.loading.isActive = true;
                this.$store.dispatch("googleRecaptcha/save", this.form).then((res) => {
                    this.loading.isActive = false;
                    alertService.successFlip(res.config.method === "put" ? 1 : 0, "Google reCAPTCHA Settings");
                    this.errors = {};
                }).catch((err) => {
                    this.loading.isActive = false;
                    if(err && err.response && err.response.data && err.response.data.errors) {
                        this.errors = err.response.data.errors;
                    }
                });
            } catch (err) {
                this.loading.isActive = false;
                alertService.error(err);
            }
        },
    },
};
</script>
