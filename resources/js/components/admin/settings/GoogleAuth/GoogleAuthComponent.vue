<template>
    <LoadingComponent :props="loading" />

    <div id="googleAuth" class="db-card db-tab-div active">
        <div class="db-card-header">
            <h3 class="db-card-title">Google Auth Settings</h3>
        </div>
        <div class="db-card-body">
            <form @submit.prevent="save">
                <div class="form-row">
                    <div class="form-col-12 sm:form-col-12">
                        <label class="db-field-title required" for="active">Enable Google Auth</label>
                        <div class="db-field-radio-group">
                            <div class="db-field-radio">
                                <div class="custom-radio">
                                    <input :value="true" v-model="form.is_google_auth_enable" id="enable_auth"
                                        type="radio" class="custom-radio-field" />
                                    <span class="custom-radio-span"></span>
                                </div>
                                <label for="enable_auth" class="db-field-label">Enable</label>
                            </div>
                            <div class="db-field-radio">
                                <div class="custom-radio">
                                    <input :value="false" v-model="form.is_google_auth_enable" type="radio"
                                        id="disable_auth" class="custom-radio-field" />
                                    <span class="custom-radio-span"></span>
                                </div>
                                <label for="disable_auth" class="db-field-label">Disable</label>
                            </div>
                        </div>
                        <small class="db-field-alert" v-if="errors.is_google_auth_enable">{{
                            errors.is_google_auth_enable[0]
                        }}</small>
                    </div>

                    <div class="form-col-12 sm:form-col-12">
                        <label for="client_id" class="db-field-title">Google Client ID</label>
                        <input v-model="form.client_id" v-bind:class="errors.client_id ? 'invalid' : ''" type="text"
                            id="client_id" class="db-field-control" />
                        <small class="db-field-alert" v-if="errors.client_id">{{ errors.client_id[0] }}</small>
                    </div>

                    <div class="form-col-12 sm:form-col-12">
                        <label for="client_secret" class="db-field-title">Google Client Secret</label>
                        <input v-model="form.client_secret" v-bind:class="errors.client_secret ? 'invalid' : ''" type="text"
                            id="client_secret" class="db-field-control" />
                        <small class="db-field-alert" v-if="errors.client_secret">{{ errors.client_secret[0] }}</small>
                    </div>

                    <div class="form-col-12 sm:form-col-12">
                        <label for="redirect_url" class="db-field-title">Google Redirect URL</label>
                        <input v-model="form.redirect_url" v-bind:class="errors.redirect_url ? 'invalid' : ''" type="text"
                            id="redirect_url" class="db-field-control" />
                        <small class="db-field-alert" v-if="errors.redirect_url">{{ errors.redirect_url[0] }}</small>
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
    name: "GoogleAuthComponent",
    components: { LoadingComponent },
    data() {
        return {
            loading: {
                isActive: false,
            },
            form: {
                is_google_auth_enable: false,
                client_id: "",
                client_secret: "",
                redirect_url: "",
            },
            errors: {},
        };
    },
    mounted() {
        try {
            this.loading.isActive = true;
            this.$store
                .dispatch("googleAuth/lists")
                .then((res) => {
                    this.form = {
                        is_google_auth_enable: res.data.data.is_google_auth_enable === "true" || res.data.data.is_google_auth_enable === true || res.data.data.is_google_auth_enable === "1" || res.data.data.is_google_auth_enable === 1,
                        client_id: res.data.data.client_id ?? "",
                        client_secret: res.data.data.client_secret ?? "",
                        redirect_url: res.data.data.redirect_url ?? "",
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
                this.$store.dispatch("googleAuth/save", this.form).then((res) => {
                    this.loading.isActive = false;
                    alertService.successFlip(res.config.method === "put" ? 1 : 0, "Google Auth Settings");
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
