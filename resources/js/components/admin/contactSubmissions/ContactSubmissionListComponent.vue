<template>
    <LoadingComponent :props="loading" />
    <div class="col-12">
        <div class="db-card db-tab-div active">
            <div class="db-card-header border-none">
                <h3 class="db-card-title">{{ $t("menu.contact_submissions") }}</h3>
                <div class="db-card-filter">
                    <TableLimitComponent :method="list" :search="props.search" :page="paginationPage" />
                    <FilterComponent @click.prevent="handleSlide('contact-submission-filter')" />
                    <div class="dropdown-group">
                        <ExportComponent />
                        <div
                            class="dropdown-list db-card-filter-dropdown-list transition-all duration-300 scale-y-0 origin-top">
                            <PrintComponent :props="printObj" />
                        </div>
                    </div>
                </div>
            </div>
            <div class="table-filter-div" id="contact-submission-filter">
                <form class="p-4 sm:p-5 mb-5" @submit.prevent="search">
                    <div class="row">
                        <div class="col-12 sm:col-6 md:col-4 xl:col-3">
                            <label for="searchName" class="db-field-title after:hidden">{{ $t('label.name') }}</label>
                            <input id="searchName" v-model="props.search.name" type="text" class="db-field-control">
                        </div>
                        <div class="col-12 sm:col-6 md:col-4 xl:col-3">
                            <label for="searchEmail" class="db-field-title after:hidden">{{ $t('label.email') }}</label>
                            <input id="searchEmail" v-model="props.search.email" type="text" class="db-field-control">
                        </div>
                        <div class="col-12 sm:col-6 md:col-4 xl:col-3">
                            <label for="searchPhone" class="db-field-title after:hidden">{{ $t('label.phone') }}</label>
                            <input id="searchPhone" v-model="props.search.phone" type="text" class="db-field-control">
                        </div>
                        <div class="col-12 sm:col-6 md:col-4 xl:col-3">
                            <label for="searchStartDate" class="db-field-title after:hidden">{{
                                $t('label.date')
                            }}</label>
                            <Datepicker hideInputIcon autoApply :enableTimePicker="false" utc="false"
                                @update:modelValue="handleDate" v-model="modelValue" range
                                :preset-ranges="presetRanges">
                                <template #yearly="{ label, range, presetDateRange }">
                                    <span @click="presetDateRange(range)">{{ label }}</span>
                                </template>
                            </Datepicker>
                        </div>
                        <div class="col-12">
                            <div class="flex flex-wrap gap-3 mt-4">
                                <button class="db-btn py-2 text-white bg-primary">
                                    <i class="lab lab-search-line lab-font-size-16"></i>
                                    <span>{{ $t('button.search') }}</span>
                                </button>
                                <button class="db-btn py-2 text-white bg-gray-600" @click.prevent="clear">
                                    <i class="lab lab-cross-line-2 lab-font-size-22"></i>
                                    <span>{{ $t('button.clear') }}</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="db-table-responsive">
                <table class="db-table stripe" id="print">
                    <thead class="db-table-head">
                        <tr class="db-table-head-tr">
                            <th class="db-table-head-th">{{ $t("label.name") }}</th>
                            <th class="db-table-head-th">{{ $t("label.email") }}</th>
                            <th class="db-table-head-th">{{ $t("label.phone") }}</th>
                            <th class="db-table-head-th">{{ $t("label.message") }}</th>
                            <th class="db-table-head-th">{{ $t("label.date") }}</th>
                        </tr>
                    </thead>
                    <tbody class="db-table-body" v-if="contactSubmissions.length > 0">
                        <tr class="db-table-body-tr" v-for="submission in contactSubmissions" :key="submission.id">
                            <td class="db-table-body-td">{{ submission.name }}</td>
                            <td class="db-table-body-td">{{ submission.email }}</td>
                            <td class="db-table-body-td">{{ submission.phone || '-' }}</td>
                            <td class="db-table-body-td max-w-[420px] whitespace-normal">{{ submission.message }}</td>
                            <td class="db-table-body-td">{{ submission.date_time }}</td>
                        </tr>
                    </tbody>
                    <tbody class="db-table-body" v-else>
                        <tr class="db-table-body-tr">
                            <td class="db-table-body-td text-center" colspan="5">
                                <div class="p-4">
                                    <div class="max-w-[300px] mx-auto mt-2">
                                        <img class="w-full h-full" :src="ENV.API_URL + '/images/default/not-found.png'"
                                            alt="Not Found">
                                    </div>
                                    <span class="d-block mt-3 text-lg">{{ $t('message.no_data_available') }}</span>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="flex items-center justify-between border-t border-gray-200 bg-white px-4 py-6"
                v-if="contactSubmissions.length > 0">
                <PaginationSMBox :pagination="pagination" :method="list" />
                <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
                    <PaginationTextComponent :props="{ page: paginationPage }" />
                    <PaginationBox :pagination="pagination" :method="list" />
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import LoadingComponent from "../components/LoadingComponent";
import PaginationTextComponent from "../components/pagination/PaginationTextComponent";
import PaginationBox from "../components/pagination/PaginationBox";
import PaginationSMBox from "../components/pagination/PaginationSMBox";
import appService from "../../../services/appService";
import TableLimitComponent from "../components/TableLimitComponent";
import FilterComponent from "../components/buttons/collapse/FilterComponent";
import ExportComponent from "../components/buttons/export/ExportComponent";
import PrintComponent from "../components/buttons/export/PrintComponent";
import Datepicker from "@vuepic/vue-datepicker";
import "@vuepic/vue-datepicker/dist/main.css";
import { ref } from "vue";
import { endOfMonth, endOfYear, startOfMonth, startOfYear, subMonths } from "date-fns";
import ENV from "../../../config/env";

export default {
    name: "ContactSubmissionListComponent",
    components: {
        TableLimitComponent,
        PaginationSMBox,
        PaginationBox,
        PaginationTextComponent,
        LoadingComponent,
        ExportComponent,
        FilterComponent,
        PrintComponent,
        Datepicker,
    },
    setup() {
        const presetRanges = ref([
            { label: "Today", range: [new Date(), new Date()] },
            { label: "This month", range: [startOfMonth(new Date()), endOfMonth(new Date())] },
            {
                label: "Last month",
                range: [startOfMonth(subMonths(new Date(), 1)), endOfMonth(subMonths(new Date(), 1))],
            },
            { label: "This year", range: [startOfYear(new Date()), endOfYear(new Date())] },
            {
                label: "This year (slot)",
                range: [startOfYear(new Date()), endOfYear(new Date())],
                slot: "yearly",
            },
        ]);

        return {
            presetRanges,
        };
    },
    data() {
        return {
            loading: {
                isActive: false,
            },
            printObj: {
                id: "print",
                popTitle: this.$t("menu.contact_submissions"),
            },
            props: {
                search: {
                    paginate: 1,
                    page: 1,
                    per_page: 10,
                    order_column: "id",
                    order_type: "desc",
                    name: "",
                    email: "",
                    phone: "",
                    from_date: "",
                    to_date: "",
                },
            },
            modelValue: null,
            ENV: ENV,
        };
    },
    mounted() {
        this.list();
    },
    computed: {
        contactSubmissions: function () {
            return this.$store.getters["contactSubmission/lists"];
        },
        pagination: function () {
            return this.$store.getters["contactSubmission/pagination"];
        },
        paginationPage: function () {
            return this.$store.getters["contactSubmission/page"];
        },
    },
    methods: {
        handleSlide: function (id) {
            return appService.handleSlide(id);
        },
        search: function () {
            this.list();
        },
        clear: function () {
            this.props.search.paginate = 1;
            this.props.search.page = 1;
            this.props.search.name = "";
            this.props.search.email = "";
            this.props.search.phone = "";
            this.props.search.from_date = "";
            this.props.search.to_date = "";
            this.modelValue = null;
            this.list();
        },
        handleDate: function (e) {
            if (e) {
                this.props.search.from_date = e[0];
                this.props.search.to_date = e[1];
            } else {
                this.props.search.from_date = null;
                this.props.search.to_date = null;
            }
        },
        list: function (page = 1) {
            this.loading.isActive = true;
            this.props.search.page = page;
            this.$store.dispatch("contactSubmission/lists", this.props.search).then(() => {
                this.loading.isActive = false;
            }).catch(() => {
                this.loading.isActive = false;
            });
        },
    },
};
</script>

<style scoped>
@media print {
    .hidden-print {
        display: none !important;
    }
}
</style>
