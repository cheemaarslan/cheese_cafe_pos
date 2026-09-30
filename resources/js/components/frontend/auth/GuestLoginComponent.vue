<template>
    <LoadingComponent :props="loading" />
    <section class="pt-8 pb-16">
        <div class="container max-w-[360px] py-6 p-4 sm:px-6 shadow-xs rounded-2xl bg-white">
            <h2 class="capitalize mb-6 text-center text-[22px] font-semibold leading-[34px] text-heading">
                {{ $t('label.guest_login') }}
            </h2>
            <form @submit.prevent="save">
                <div class="mb-6">
                    <label class="text-sm capitalize mb-1 text-heading">{{ $t('label.mobile_number') }}</label>
                    <div class="relative" ref="countryDropdown">
                        <div class="w-full h-12 rounded-lg border px-4 flex items-center border-[#D9DBE9]">
                            <div class="w-fit flex-shrink-0 dropdown-group">
                                <button type="button" class="flex items-center gap-1" @click="toggleCountryList">
                                    {{ flag }}
                                    <span class="whitespace-nowrap flex-shrink-0 text-sm">{{ props.form.code }}</span>
                                    <i class="lab lab-arrow-down-s-line text-xs text-[#6E7191]"></i>
                                    <input type="hidden" v-model="props.form.code">
                                </button>
                            </div>
                            <input v-model="props.form.phone" v-on:keypress="phoneNumber($event)"
                                v-on:input="enforcePhoneLength" v-on:keyup.enter="save"
                                :maxlength="maxPhoneLength"
                                :class="errors.phone
                                    ? 'invalid' : ''" type="text" id="phone"
                                class="pl-4 text-sm w-full h-full text-heading" />
                        </div>
                        <div v-show="showCountryList"
                            class="absolute z-20 top-full left-0 right-0 mt-2 bg-white rounded-lg border border-[#D9DBE9] shadow-lg overflow-hidden leading-normal">
                            <div class="p-2 border-b border-[#D9DBE9]">
                                <input v-model="countrySearch" type="text" :placeholder="$t('label.search')"
                                    class="w-full h-9 rounded-md border px-3 text-sm border-[#D9DBE9]">
                            </div>
                            <ul class="max-h-60 overflow-y-auto">
                                <li v-for="country in filteredCountries" :key="country.country_code"
                                    @click="selectCountry(country)"
                                    class="flex items-center gap-2 px-3 py-2 text-sm cursor-pointer hover:bg-gray-50 text-heading">
                                    <span>{{ country.flag_emoji }}</span>
                                    <span class="flex-1 truncate text-left rtl:text-right">{{ country.country_name }}</span>
                                    <span class="text-[#6E7191]">{{ country.calling_code }}</span>
                                </li>
                                <li v-if="filteredCountries.length === 0" class="px-3 py-2 text-sm text-[#6E7191]">
                                    {{ $t('message.no_data_available') }}
                                </li>
                            </ul>
                        </div>
                    </div>
                    <small class="db-field-alert" v-if="errors.phone">
                        {{ errors.phone[0] }}
                    </small>
                </div>
                <button type="submit"
                    class="w-full h-12 text-center capitalize font-medium rounded-3xl mb-6 text-white bg-primary">
                    {{ $t('label.next') }}
                </button>
                <div class="flex items-center justify-center gap-2">
                    <span class="text-base text-[#6E7191]">{{ $t('label.already_have_an_account') }}</span>
                    <router-link :to="{ name: 'auth.login' }" class="text-base font-medium text-primary">
                        {{ $t('label.login') }}
                    </router-link>
                </div>
            </form>
        </div>
    </section>
</template>

<script>

import appService from "../../../services/appService";
import activityEnum from "../../../enums/modules/activityEnum"
import alertService from "../../../services/alertService";
import LoadingComponent from "../components/LoadingComponent";

export default {
    name: "GuestLoginComponent",
    components: { LoadingComponent },
    data() {
        return {
            loading: {
                isActive: false,
            },
            props: {
                form: {
                    phone: "",
                    code: "",
                },
            },
            flag: "",
            country_code: "",
            errors: {},
            phone_verification: "",
            countries: [],
            countrySearch: "",
            showCountryList: false,
            maxPhoneLength: 15,
        };
    },
    mounted() {
        this.loading.isActive = true;
        document.addEventListener('click', this.handleClickOutside);
        this.$store.dispatch('frontendSetting/lists').then(res => {
            this.defaultCountryCode = res.data.data.company_country_code;
            this.$store.dispatch('frontendCountryCode/phoneCodes').then(res => {
                this.countries = res.data.data;
                const defaultCountry = this.countries.find(
                    (country) => country.country_code === this.defaultCountryCode
                );
                if (defaultCountry) {
                    this.applyCountry(defaultCountry);
                }
                this.loading.isActive = false;
            }).catch((err) => {
                this.loading.isActive = false;
            });
        }).catch((err) => {
            this.loading.isActive = false;
        });
    },
    beforeUnmount() {
        document.removeEventListener('click', this.handleClickOutside);
    },
    watch: {
        'props.form.phone': function (value) {
            if (this.errors.phone && value.length === this.maxPhoneLength) {
                this.errors = {};
            }
        },
    },
    computed: {
        setting: function () {
            return this.$store.getters['frontendSetting/lists'];
        },
        carts: function () {
            return this.$store.getters['frontendCart/lists'];
        },
        filteredCountries: function () {
            if (!this.countrySearch) {
                return this.countries;
            }
            const search = this.countrySearch.toLowerCase();
            return this.countries.filter((country) =>
                country.country_name.toLowerCase().includes(search) ||
                country.calling_code.includes(search)
            );
        },
    },
    methods: {
        phoneNumber(e) {
            if (this.props.form.phone.length >= this.maxPhoneLength) {
                e.preventDefault();
                return false;
            }
            return appService.phoneNumber(e);
        },
        enforcePhoneLength: function () {
            // Belt-and-braces guard for input that skips the keypress check
            // above (paste, autofill, mobile IME/virtual keyboards) - keeps
            // the number from ever exceeding the selected country's limit.
            if (this.props.form.phone.length > this.maxPhoneLength) {
                this.props.form.phone = this.props.form.phone.slice(0, this.maxPhoneLength);
            }
        },
        toggleCountryList: function () {
            this.showCountryList = !this.showCountryList;
        },
        handleClickOutside: function (e) {
            if (this.$refs.countryDropdown && !this.$refs.countryDropdown.contains(e.target)) {
                this.showCountryList = false;
            }
        },
        applyCountry: function (country) {
            this.props.form.code = country.calling_code;
            this.country_code = country.calling_code;
            this.flag = country.flag_emoji;
            this.maxPhoneLength = country.phone_length;
            if (this.props.form.phone.length > this.maxPhoneLength) {
                this.props.form.phone = this.props.form.phone.slice(0, this.maxPhoneLength);
            }
            this.errors = {};
        },
        selectCountry: function (country) {
            this.applyCountry(country);
            this.showCountryList = false;
            this.countrySearch = "";
        },
        save: function () {
            if (this.props.form.phone.length !== this.maxPhoneLength) {
                this.errors = {
                    phone: [this.$t('message.phone_number_should_be_digits_long', { length: this.maxPhoneLength })],
                };
                return;
            }
            try {
                this.loading.isActive = true;
                this.$store.dispatch("GuestSignup/otp", this.props.form).then((res) => {
                    if (this.setting.site_phone_verification === activityEnum.DISABLE) {
                        this.props.form.token = "1000";
                        this.$store.dispatch("GuestLoginVerify", this.props.form).then((LoginRes) => {
                            this.$store.dispatch("GuestSignup/reset").then().catch();
                            this.loading.isActive = false;
                            this.errors = {};
                            this.props.form = {
                                phone: "",
                                code: this.country_code,
                            };
                            alertService.success(LoginRes.data.message);
                            if (this.carts.length > 0) {
                                this.$router.push({ name: "frontend.checkout" });
                            } else {
                                this.$router.push({ name: "frontend.home" });
                            }
                        }).catch((err) => {
                            this.loading.isActive = false;

                            if (err.response && err.response.data && err.response.data?.message) {
                                alertService.error(err.response.data.message);
                            } else {
                                this.errors = err.response.data.errors;
                            }
                        });
                    } else {
                        this.loading.isActive = false;
                        this.errors = {};
                        this.props.form = {
                            phone: "",
                            code: this.country_code,
                        };
                        alertService.success(res.data.message);
                        this.$router.push({
                            name: "auth.guestLoginVerify",
                        });
                    }
                }).catch((err) => {
                    this.loading.isActive = false;
                    if (err.response && err.response.data && err.response.data?.message) {
                        alertService.error(err.response.data.message);
                    } else {
                        this.errors = err.response.data.errors;
                    }
                });
            } catch (err) {
                this.loading.isActive = false;
                alertService.error(err);
            }
        },
    },
}
</script>
