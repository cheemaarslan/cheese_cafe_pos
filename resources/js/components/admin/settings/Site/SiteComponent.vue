<template>
    <LoadingComponent :props="loading" />

    <div id="company" class="db-card db-tab-div active">
        <div class="db-card-header">
            <h3 class="db-card-title">{{ $t('menu.site') }}</h3>
        </div>
        <div class="db-card-body">
            <form @submit.prevent="save">
                <div class="form-row">
                    <div class="form-col-12 sm:form-col-6">
                        <label for="site_date_format" class="db-field-title required">{{
                            $t("label.date_format")
                            }}</label>
                        <vue-select class="db-field-control f-b-custom-select" id="site_date_format"
                            v-bind:class="errors.site_date_format ? 'is-invalid' : ''" v-model="form.site_date_format"
                            :options="enums.dateFormatEnum" label-by="name" value-by="id" :closeOnSelect="true"
                            :searchable="true" :clearOnClose="true" placeholder="--" search-placeholder="--" />
                        <small class="db-field-alert" v-if="errors.site_date_format">{{
                            errors.site_date_format[0]
                            }}</small>
                    </div>
                    <div class="form-col-12 sm:form-col-6">
                        <label for="site_time_format" class="db-field-title required">{{
                            $t("label.time_format")
                            }}</label>

                        <vue-select class="db-field-control f-b-custom-select" id="site_time_format"
                            v-bind:class="errors.site_time_format ? 'is-invalid' : ''" v-model="form.site_time_format"
                            :options="enums.timeFormatEnum" label-by="name" value-by="id" :closeOnSelect="true"
                            :searchable="true" :clearOnClose="true" placeholder="--" search-placeholder="--" />
                        <small class="db-field-alert" v-if="errors.site_time_format">{{
                            errors.site_time_format[0]
                            }}</small>
                    </div>
                    <div class="form-col-12 sm:form-col-6">
                        <label for="site_default_timezone" class="db-field-title required">{{
                            $t("label.default_timezone")
                            }}</label>

                        <vue-select class="db-field-control f-b-custom-select" id="site_default_timezone"
                            v-bind:class="errors.site_default_timezone ? 'is-invalid' : ''"
                            v-model="form.site_default_timezone" :options="timezones" label-by="name" value-by="name"
                            :closeOnSelect="true" :searchable="true" :clearOnClose="true" placeholder="--"
                            search-placeholder="--" />
                        <small class="db-field-alert" v-if="errors.site_default_timezone">{{
                            errors.site_default_timezone[0]
                            }}</small>
                    </div>

                    <div class="form-col-12 sm:form-col-6">
                        <label for="site_default_branch" class="db-field-title required">{{
                            $t("label.default_branch")
                            }}</label>

                        <vue-select class="db-field-control f-b-custom-select" id="site_default_branch"
                            v-bind:class="errors.site_default_branch ? 'invalid' : ''"
                            v-model="form.site_default_branch" :options="branches" label-by="name" value-by="id"
                            :closeOnSelect="true" :searchable="true" :clearOnClose="true" placeholder="--"
                            search-placeholder="--" />
                        <small class="db-field-alert" v-if="errors.site_default_branch">{{
                            errors.site_default_branch[0]
                            }}</small>
                    </div>
                    <div class="form-col-12 sm:form-col-6">
                        <label for="site_default_language" class="db-field-title required">{{
                            $t("label.default_language")
                            }}</label>
                        <vue-select class="db-field-control f-b-custom-select" id="site_default_language"
                            v-bind:class="errors.site_default_language ? 'is-invalid' : ''"
                            v-model="form.site_default_language" :options="languages" label-by="name" value-by="id"
                            :closeOnSelect="true" :searchable="true" :clearOnClose="true" placeholder="--"
                            search-placeholder="--" />
                        <small class="db-field-alert" v-if="errors.site_default_language">{{
                            errors.site_default_language[0]
                            }}</small>
                    </div>

                    <div class="form-col-12 sm:form-col-6">
                        <label for="site_default_sms_gateway" class="db-field-title">{{
                            $t("label.default_sms_gateway")
                            }}</label>

                        <vue-select class="db-field-control f-b-custom-select" id="site_default_sms_gateway"
                            v-bind:class="errors.site_default_sms_gateway ? 'invalid' : ''"
                            v-model="form.site_default_sms_gateway" :options="smsGateways" label-by="name" value-by="id"
                            :closeOnSelect="true" :searchable="true" :clearOnClose="true" placeholder="--"
                            search-placeholder="--" />
                        <small class="db-field-alert" v-if="errors.site_default_sms_gateway">{{
                            errors.site_default_sms_gateway[0]
                            }}</small>
                    </div>

                    <div class="form-col-12 sm:form-col-6">
                        <label for="site_android_app_link" class="db-field-title">
                            {{ $t("label.android_app_link") }}
                        </label>
                        <input v-model="form.site_android_app_link"
                            v-bind:class="errors.site_android_app_link ? 'invalid' : ''" type="text"
                            id="site_android_app_link" class="db-field-control" />
                        <small class="db-field-alert" v-if="errors.site_android_app_link">{{
                            errors.site_android_app_link[0]
                            }}</small>
                    </div>

                    <div class="form-col-12 sm:form-col-6">
                        <label for="site_ios_app_link" class="db-field-title">
                            {{ $t("label.ios_app_link") }}
                        </label>
                        <input v-model="form.site_ios_app_link" v-bind:class="errors.site_ios_app_link ? 'invalid' : ''"
                            type="text" id="site_ios_app_link" class="db-field-control" />
                        <small class="db-field-alert" v-if="errors.site_ios_app_link">{{
                            errors.site_ios_app_link[0]
                            }}</small>
                    </div>

                    <div class="form-col-12 sm:form-col-6">
                        <label for="site_copyright" class="db-field-title required">
                            {{ $t("label.copyright") }}
                        </label>
                        <input v-model="form.site_copyright" v-bind:class="errors.site_copyright ? 'invalid' : ''"
                            type="text" id="site_copyright" class="db-field-control" />
                        <small class="db-field-alert" v-if="errors.site_copyright">{{
                            errors.site_copyright[0]
                            }}</small>
                    </div>

                    <div class="form-col-12 sm:form-col-6">
                        <label for="site_google_map_key" class="db-field-title required">
                            {{ $t("label.google_map_key") }}
                        </label>
                        <input v-model="form.site_google_map_key"
                            v-bind:class="errors.site_google_map_key ? 'invalid' : ''" type="text"
                            id="site_google_map_key" class="db-field-control" />
                        <small class="db-field-alert" v-if="errors.site_google_map_key">{{
                            errors.site_google_map_key[0]
                            }}</small>
                    </div>

                    <div class="form-col-12 sm:form-col-6">
                        <label for="site_digit_after_decimal_point" class="db-field-title required">
                            {{ $t("label.digit_after_decimal_point") }}
                            <span class="text-primary">{{ $t("label.ex") }}</span>
                        </label>
                        <input v-on:keypress="floatNumber($event)" v-model="form.site_digit_after_decimal_point"
                            v-bind:class="errors.site_digit_after_decimal_point ? 'invalid' : ''" type="text"
                            id="site_digit_after_decimal_point" class="db-field-control" />
                        <small class="db-field-alert" v-if="errors.site_digit_after_decimal_point">{{
                            errors.site_digit_after_decimal_point[0]
                            }}</small>
                    </div>

                    <div class="form-col-12 sm:form-col-6">
                        <label for="site_default_currency" class="db-field-title required">{{
                            $t("label.default_currency")
                            }}</label>

                        <vue-select class="db-field-control f-b-custom-select" id="site_default_currency"
                            v-bind:class="errors.site_default_currency ? 'is-invalid' : ''"
                            v-model="form.site_default_currency" :options="currencies" label-by="name_symbol"
                            value-by="id" :closeOnSelect="true" :searchable="true" :clearOnClose="true" placeholder="--"
                            search-placeholder="--" />
                        <small class="db-field-alert" v-if="errors.site_default_currency">{{
                            errors.site_default_currency[0]
                            }}</small>
                    </div>

                    <div class="form-col-12 sm:form-col-6">
                        <label for="site_default_phone_digit_length" class="db-field-title">
                            {{ $t("label.default_phone_digit_length") }}
                        </label>
                        <input v-model="form.site_default_phone_digit_length"
                            v-bind:class="errors.site_default_phone_digit_length ? 'invalid' : ''" type="text"
                            id="site_default_phone_digit_length" class="db-field-control" />
                        <small class="db-field-alert" v-if="errors.site_default_phone_digit_length">{{
                            errors.site_default_phone_digit_length[0]
                            }}</small>
                    </div>

                    <div class="form-col-12 sm:form-col-6">
                        <label for="site_convenience_fee" class="db-field-title">
                            {{ $t("label.convenience_fee") }}
                        </label>
                        <input v-on:keypress="floatNumber($event)" v-model="form.site_convenience_fee"
                            v-bind:class="errors.site_convenience_fee ? 'invalid' : ''" type="text"
                            id="site_convenience_fee" class="db-field-control" />
                        <small class="db-field-alert" v-if="errors.site_convenience_fee">{{
                            errors.site_convenience_fee[0]
                            }}</small>
                    </div>

                    <div class="form-col-12 sm:form-col-6">
                        <label for="site_meta_title" class="db-field-title">
                            {{ $t("label.meta_title") }}
                        </label>
                        <input v-model="form.site_meta_title" v-bind:class="errors.site_meta_title ? 'invalid' : ''"
                            type="text" id="site_meta_title" class="db-field-control" />
                        <small class="db-field-alert" v-if="errors.site_meta_title">{{ errors.site_meta_title[0] }}</small>
                    </div>

                    <div class="form-col-12 sm:form-col-6">
                        <label for="site_og_title" class="db-field-title">
                            {{ $t("label.og_title") }}
                        </label>
                        <input v-model="form.site_og_title" v-bind:class="errors.site_og_title ? 'invalid' : ''"
                            type="text" id="site_og_title" class="db-field-control" />
                        <small class="db-field-alert" v-if="errors.site_og_title">{{ errors.site_og_title[0] }}</small>
                    </div>

                    <div class="form-col-12">
                        <label for="site_meta_description" class="db-field-title">
                            {{ $t("label.meta_description") }}
                        </label>
                        <textarea v-model="form.site_meta_description"
                            v-bind:class="errors.site_meta_description ? 'invalid' : ''" id="site_meta_description"
                            rows="3" class="db-field-control"></textarea>
                        <small class="db-field-alert" v-if="errors.site_meta_description">{{ errors.site_meta_description[0] }}</small>
                    </div>

                    <div class="form-col-12">
                        <label for="site_meta_keywords" class="db-field-title">
                            {{ $t("label.meta_keywords") }}
                        </label>
                        <textarea v-model="form.site_meta_keywords"
                            v-bind:class="errors.site_meta_keywords ? 'invalid' : ''" id="site_meta_keywords"
                            rows="2" class="db-field-control"></textarea>
                        <small class="db-field-alert" v-if="errors.site_meta_keywords">{{ errors.site_meta_keywords[0] }}</small>
                    </div>

                    <div class="form-col-12">
                        <label for="site_og_description" class="db-field-title">
                            {{ $t("label.og_description") }}
                        </label>
                        <textarea v-model="form.site_og_description"
                            v-bind:class="errors.site_og_description ? 'invalid' : ''" id="site_og_description"
                            rows="3" class="db-field-control"></textarea>
                        <small class="db-field-alert" v-if="errors.site_og_description">{{ errors.site_og_description[0] }}</small>
                    </div>

                    <div class="form-col-12 sm:form-col-6">
                        <label for="site_og_image" class="db-field-title">
                            {{ $t("label.og_image") }}
                        </label>
                        <input @change="changeOgImage" v-bind:class="errors.site_og_image ? 'invalid' : ''"
                            id="site_og_image" type="file" class="db-field-control" ref="siteOgImageProperty"
                            accept="image/png, image/jpeg, image/jpg, image/webp" />
                        <small class="db-field-alert" v-if="errors.site_og_image">{{ errors.site_og_image[0] }}</small>
                        <img class="w-[240px] h-[120px] object-cover rounded-lg mt-2" alt="og image"
                            v-if="site_og_image_reader" :src="site_og_image_reader" />
                    </div>

                    <div class="form-col-12 sm:form-col-6">
                        <label class="db-field-title required" for="enable">
                            {{ $t("label.currency_position") }}
                        </label>
                        <div class="db-field-radio-group">
                            <div class="db-field-radio">
                                <div class="custom-radio">
                                    <input :value="enums.currencyPositionEnum.LEFT"
                                        v-model="form.site_currency_position" id="left" type="radio"
                                        class="custom-radio-field" />
                                    <span class="custom-radio-span"></span>
                                </div>
                                <label for="left" class="db-field-label"> ({{ form.site_default_currency_symbol }}) {{
                                    $t("label.left")
                                    }}</label>
                            </div>
                            <div class="db-field-radio">
                                <div class="custom-radio">
                                    <input :value="enums.currencyPositionEnum.RIGHT"
                                        v-model="form.site_currency_position" type="radio" id="right"
                                        class="custom-radio-field" />
                                    <span class="custom-radio-span"></span>
                                </div>
                                <label for="right" class="db-field-label">
                                    {{ $t("label.right") }} ({{ form.site_default_currency_symbol }})
                                </label>
                            </div>
                        </div>
                        <small class="db-field-alert" v-if="errors.site_currency_position">{{
                            errors.site_currency_position[0]
                            }}</small>
                    </div>

                    <div class="form-col-12 sm:form-col-6">
                        <label class="db-field-title required" for="enable">{{ $t("label.online_payment_gateway")
                            }}</label>
                        <div class="db-field-radio-group">
                            <div class="db-field-radio">
                                <div class="custom-radio">
                                    <input :value="enums.activityEnum.ENABLE" v-model="form.site_online_payment_gateway"
                                        id="online_payment_gateway_enable" type="radio" class="custom-radio-field" />
                                    <span class="custom-radio-span"></span>
                                </div>
                                <label for="online_payment_gateway_enable" class="db-field-label">{{
                                    $t("label.enable")
                                    }}</label>
                            </div>
                            <div class="db-field-radio">
                                <div class="custom-radio">
                                    <input :value="enums.activityEnum.DISABLE"
                                        v-model="form.site_online_payment_gateway" type="radio"
                                        id="online_payment_gateway_disable" class="custom-radio-field" />
                                    <span class="custom-radio-span"></span>
                                </div>
                                <label for="online_payment_gateway_disable" class="db-field-label">{{
                                    $t("label.disable")
                                    }}</label>
                            </div>
                        </div>
                        <small class="db-field-alert" v-if="errors.site_online_payment_gateway">{{
                            errors.site_online_payment_gateway[0]
                            }}</small>
                    </div>

                    <div class="form-col-12 sm:form-col-6">
                        <label class="db-field-title required" for="enable">{{ $t("label.language_switch") }}</label>
                        <div class="db-field-radio-group">
                            <div class="db-field-radio">
                                <div class="custom-radio">
                                    <input :value="enums.activityEnum.ENABLE" v-model="form.site_language_switch"
                                        id="language_switch_enable" type="radio" class="custom-radio-field" />
                                    <span class="custom-radio-span"></span>
                                </div>
                                <label for="language_switch_enable" class="db-field-label">{{
                                    $t("label.enable")
                                    }}</label>
                            </div>
                            <div class="db-field-radio">
                                <div class="custom-radio">
                                    <input :value="enums.activityEnum.DISABLE" v-model="form.site_language_switch"
                                        type="radio" id="language_switch_disable" class="custom-radio-field" />
                                    <span class="custom-radio-span"></span>
                                </div>
                                <label for="language_switch_disable" class="db-field-label">{{
                                    $t("label.disable")
                                    }}</label>
                            </div>
                        </div>
                        <small class="db-field-alert" v-if="errors.site_language_switch">{{
                            errors.site_language_switch[0]
                            }}</small>
                    </div>

                    <div class="form-col-12 sm:form-col-6">
                        <label class="db-field-title required" for="enable">{{ $t("label.email_verification") }}</label>
                        <div class="db-field-radio-group">
                            <div class="db-field-radio">
                                <div class="custom-radio">
                                    <input :value="enums.activityEnum.ENABLE" v-model="form.site_email_verification"
                                        id="email_verification_enable" type="radio" class="custom-radio-field" />
                                    <span class="custom-radio-span"></span>
                                </div>
                                <label for="email_verification_enable" class="db-field-label">{{
                                    $t("label.enable")
                                    }}</label>
                            </div>
                            <div class="db-field-radio">
                                <div class="custom-radio">
                                    <input :value="enums.activityEnum.DISABLE" v-model="form.site_email_verification"
                                        type="radio" id="email_verification_disable" class="custom-radio-field" />
                                    <span class="custom-radio-span"></span>
                                </div>
                                <label for="email_verification_disable" class="db-field-label">{{
                                    $t("label.disable")
                                    }}</label>
                            </div>
                        </div>
                        <small class="db-field-alert" v-if="errors.site_email_verification">{{
                            errors.site_email_verification[0]
                            }}</small>
                    </div>

                    <div class="form-col-12 sm:form-col-6">
                        <label class="db-field-title required" for="enable">{{ $t("label.phone_verification") }}</label>
                        <div class="db-field-radio-group">
                            <div class="db-field-radio">
                                <div class="custom-radio">
                                    <input :value="enums.activityEnum.ENABLE" v-model="form.site_phone_verification"
                                        id="phone_verification_enable" type="radio" class="custom-radio-field" />
                                    <span class="custom-radio-span"></span>
                                </div>
                                <label for="phone_verification_enable" class="db-field-label">{{
                                    $t("label.enable")
                                    }}</label>
                            </div>
                            <div class="db-field-radio">
                                <div class="custom-radio">
                                    <input :value="enums.activityEnum.DISABLE" v-model="form.site_phone_verification"
                                        type="radio" id="phone_verification_disable" class="custom-radio-field" />
                                    <span class="custom-radio-span"></span>
                                </div>
                                <label for="phone_verification_disable" class="db-field-label">{{
                                    $t("label.disable")
                                    }}</label>
                            </div>
                        </div>
                        <small class="db-field-alert" v-if="errors.site_phone_verification">{{
                            errors.site_phone_verification[0]
                            }}</small>
                    </div>

                    <div class="form-col-12 sm:form-col-6">
                        <label class="db-field-title required" for="app_debug">{{ $t("label.app_debug") }}</label>
                        <div class="db-field-radio-group">
                            <div class="db-field-radio">
                                <div class="custom-radio">
                                    <input :value="enums.activityEnum.ENABLE" v-model="form.site_app_debug"
                                        id="debug_enable" type="radio" class="custom-radio-field" />
                                    <span class="custom-radio-span"></span>
                                </div>
                                <label for="debug_enable" class="db-field-label">{{ $t("label.enable") }}</label>
                            </div>
                            <div class="db-field-radio">
                                <div class="custom-radio">
                                    <input :value="enums.activityEnum.DISABLE" v-model="form.site_app_debug"
                                        type="radio" id="debug_disable" class="custom-radio-field" />
                                    <span class="custom-radio-span"></span>
                                </div>
                                <label for="debug_disable" class="db-field-label">{{ $t("label.disable") }}</label>
                            </div>
                        </div>
                        <small class="db-field-alert" v-if="errors.site_app_debug">{{
                            errors.site_app_debug[0]
                            }}</small>
                    </div>

                    <div class="form-col-12 sm:form-col-6">
                        <label class="db-field-title required" for="guest_login">{{ $t("label.guest_login") }}</label>
                        <div class="db-field-radio-group">
                            <div class="db-field-radio">
                                <div class="custom-radio">
                                    <input :value="enums.activityEnum.ENABLE" v-model="form.site_guest_login"
                                        id="guest_login_enable" type="radio" class="custom-radio-field" />
                                    <span class="custom-radio-span"></span>
                                </div>
                                <label for="guest_login_enable" class="db-field-label">{{ $t("label.enable") }}</label>
                            </div>
                            <div class="db-field-radio">
                                <div class="custom-radio">
                                    <input :value="enums.activityEnum.DISABLE" v-model="form.site_guest_login"
                                        type="radio" id="guest_login_disable" class="custom-radio-field" />
                                    <span class="custom-radio-span"></span>
                                </div>
                                <label for="guest_login_disable" class="db-field-label">{{
                                    $t("label.disable")
                                    }}</label>
                            </div>
                        </div>
                        <small class="db-field-alert" v-if="errors.site_guest_login">{{
                            errors.site_guest_login[0]
                            }}</small>
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

import dateFormatEnum from "../../../../enums/modules/dateFormatEnum";
import timeFormatEnum from "../../../../enums/modules/timeFormatEnum";
import activityEnum from "../../../../enums/modules/activityEnum";
import currencyPositionEnum from "../../../../enums/modules/currencyPositionEnum";
import statusEnum from "../../../../enums/modules/statusEnum";
import LoadingComponent from "../../components/LoadingComponent";
import alertService from "../../../../services/alertService";
import appService from "../../../../services/appService";

export default {
    name: "SiteComponent",
    components: { LoadingComponent },
    data() {
        return {
            loading: {
                isActive: false
            },
            form: {
                site_date_format: null,
                site_time_format: null,
                site_default_timezone: null,
                site_default_branch: null,
                site_default_currency: null,
                site_default_currency_symbol: null,
                site_default_language: null,
                site_language_switch: null,
                site_app_debug: null,
                site_currency_position: null,
                site_email_verification: null,
                site_phone_verification: null,
                site_digit_after_decimal_point: null,
                site_google_map_key: null,
                site_android_app_link: null,
                site_ios_app_link: null,
                site_copyright: null,
                site_online_payment_gateway: null,
                site_default_sms_gateway: null,
                site_guest_login: null,
                site_default_phone_digit_length: null,
                site_convenience_fee: null,
                site_meta_title: null,
                site_meta_description: null,
                site_meta_keywords: null,
                site_og_title: null,
                site_og_description: null,
            },
            site_og_image: "",
            site_og_image_reader: "",
            enums: {
                dateFormatEnum: dateFormatEnum,
                timeFormatEnum: timeFormatEnum,
                activityEnum: activityEnum,
                currencyPositionEnum: currencyPositionEnum,
            },
            errors: {}
        }
    },
    computed: {
        timezones: function () {
            return this.$store.getters['timezone/lists'];
        },
        branches: function () {
            return this.$store.getters['branch/lists'];
        },
        currencies: function () {
            return this.$store.getters['currency/lists'];
        },
        languages: function () {
            return this.$store.getters['language/lists'];
        },
        smsGateways: function () {
            return this.$store.getters["smsGateway/lists"];
        },
    },
    mounted() {
        this.loadInfo();
    },
    methods: {
        floatNumber(e) {
            return appService.floatNumber(e);
        },
        changeOgImage: function (e) {
            this.site_og_image = e.target.files[0];
            if (this.site_og_image) {
                this.site_og_image_reader = URL.createObjectURL(this.site_og_image);
            }
        },
        loadInfo: async function () {
            try {
                this.loading.isActive = true;
                await this.$store.dispatch("smsGateway/lists", {
                    status: statusEnum.ACTIVE
                });
                await this.$store.dispatch('timezone/lists');
                await this.$store.dispatch('branch/lists', {
                    order_column: 'id',
                    order_type: 'asc',
                    status: statusEnum.ACTIVE
                });
                await this.$store.dispatch('currency/lists', {
                    order_column: 'id',
                    order_type: 'asc',
                    status: statusEnum.ACTIVE
                });
                await this.$store.dispatch('language/lists', {
                    order_column: 'id',
                    order_type: 'asc',
                    status: statusEnum.ACTIVE
                });

                this.list();

            } catch (err) {
                this.loading.isActive = false;
            }
        },
        list: function () {
            this.loading.isActive = true;
            this.$store.dispatch('site/lists').then(res => {
                this.form = {
                    site_date_format: res.data.data.site_date_format,
                    site_time_format: res.data.data.site_time_format,
                    site_default_timezone: res.data.data.site_default_timezone,
                    site_default_branch: res.data.data.site_default_branch,
                    site_default_currency: res.data.data.site_default_currency,
                    site_default_currency_symbol: res.data.data.site_default_currency_symbol,
                    site_default_language: res.data.data.site_default_language,
                    site_language_switch: res.data.data.site_language_switch,
                    site_app_debug: res.data.data.site_app_debug,
                    site_currency_position: res.data.data.site_currency_position,
                    site_email_verification: res.data.data.site_email_verification,
                    site_phone_verification: res.data.data.site_phone_verification,
                    site_digit_after_decimal_point: res.data.data.site_digit_after_decimal_point,
                    site_google_map_key: res.data.data.site_google_map_key,
                    site_android_app_link: res.data.data.site_android_app_link,
                    site_ios_app_link: res.data.data.site_ios_app_link,
                    site_copyright: res.data.data.site_copyright,
                    site_online_payment_gateway: res.data.data.site_online_payment_gateway,
                    site_default_sms_gateway: res.data.data.site_default_sms_gateway === 0 ? null : res.data.data.site_default_sms_gateway,
                    site_guest_login: res.data.data.site_guest_login,
                    site_default_phone_digit_length: res.data.data.site_default_phone_digit_length,
                    site_convenience_fee: res.data.data.site_convenience_fee,
                    site_meta_title: res.data.data.site_meta_title,
                    site_meta_description: res.data.data.site_meta_description,
                    site_meta_keywords: res.data.data.site_meta_keywords,
                    site_og_title: res.data.data.site_og_title,
                    site_og_description: res.data.data.site_og_description,
                }
                this.site_og_image_reader = res.data.data.site_og_image;
                this.loading.isActive = false;
            }).catch((err) => {
                this.loading.isActive = false;
            });

        },
        save: function () {
            try {
                const fd = new FormData();
                fd.append("site_date_format", this.form.site_date_format);
                fd.append("site_time_format", this.form.site_time_format);
                fd.append("site_default_timezone", this.form.site_default_timezone);
                fd.append("site_default_branch", this.form.site_default_branch);
                fd.append("site_default_language", this.form.site_default_language);
                fd.append("site_default_sms_gateway", this.form.site_default_sms_gateway === null ? "" : this.form.site_default_sms_gateway);
                fd.append("site_android_app_link", this.form.site_android_app_link ?? "");
                fd.append("site_ios_app_link", this.form.site_ios_app_link ?? "");
                fd.append("site_copyright", this.form.site_copyright);
                fd.append("site_google_map_key", this.form.site_google_map_key);
                fd.append("site_digit_after_decimal_point", this.form.site_digit_after_decimal_point);
                fd.append("site_default_currency", this.form.site_default_currency);
                fd.append("site_default_phone_digit_length", this.form.site_default_phone_digit_length ?? "");
                fd.append("site_convenience_fee", this.form.site_convenience_fee ?? "");
                fd.append("site_currency_position", this.form.site_currency_position);
                fd.append("site_online_payment_gateway", this.form.site_online_payment_gateway);
                fd.append("site_language_switch", this.form.site_language_switch);
                fd.append("site_email_verification", this.form.site_email_verification);
                fd.append("site_phone_verification", this.form.site_phone_verification);
                fd.append("site_app_debug", this.form.site_app_debug);
                fd.append("site_guest_login", this.form.site_guest_login);
                fd.append("_method", "put");
                fd.append("site_meta_title", this.form.site_meta_title ?? "");
                fd.append("site_meta_description", this.form.site_meta_description ?? "");
                fd.append("site_meta_keywords", this.form.site_meta_keywords ?? "");
                fd.append("site_og_title", this.form.site_og_title ?? "");
                fd.append("site_og_description", this.form.site_og_description ?? "");
                if (this.site_og_image) {
                    fd.append("site_og_image", this.site_og_image);
                }
                this.loading.isActive = true;
                this.$store.dispatch("site/save", { form: fd }).then((res) => {
                    this.loading.isActive = false;
                    alertService.successFlip(1, this.$t("menu.site"));
                    this.list();
                    this.$store.dispatch('frontendSetting/lists').then().catch();
                    this.site_og_image = "";
                    this.$refs.siteOgImageProperty.value = null;
                    this.errors = {};
                }).catch((err) => {
                    this.loading.isActive = false;
                    this.errors = {};
                    if (err.response && err.response.data && err.response.data.errors) {
                        this.errors = err.response.data.errors;
                    } else {
                        alertService.error(err.response.data.message);
                    }
                });
            } catch (err) {
                this.loading.isActive = false;
                alertService.error(err);
            }
        },
    }
}
</script>