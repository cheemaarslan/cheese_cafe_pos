<template>
    <LoadingComponent :props="loading" />
    <div class="col-12">
        <div class="db-card mb-6">
            <div class="db-card-header border-none flex-wrap gap-3">
                <div>
                    <h3 class="db-card-title">{{ $t('menu.profit_loss') }}</h3>
                    <p v-if="report.from_date" class="text-xs mt-1 text-[#6E7191]">
                        {{ $t('message.profit_loss_formula') }}
                    </p>
                    <p v-if="activeFilters.length" class="text-xs mt-1 font-medium text-primary">
                        {{ $t('label.filters') }}: {{ activeFilters.join(', ') }}
                    </p>
                </div>
                <div class="db-card-filter">
                    <div class="w-full sm:w-64">
                        <Datepicker hideInputIcon autoApply :enableTimePicker="false" :utc="false" range
                            :clearable="false" v-model="dateRange" :preset-ranges="presetRanges"
                            @update:modelValue="handleDate" />
                    </div>
                    <FilterComponent @click.prevent="handleSlide('profit-loss-filter')" />
                    <div class="dropdown-group">
                        <ExportComponent />
                        <div
                            class="dropdown-list db-card-filter-dropdown-list transition-all duration-300 scale-y-0 origin-top">
                            <PrintComponent :props="printObj" />
                            <ExcelComponent :method="xls" />
                            <PdfComponent :method="pdf" />
                        </div>
                    </div>
                </div>
            </div>
            <div class="table-filter-div" id="profit-loss-filter">
                <form class="p-4 sm:p-5" @submit.prevent="list">
                    <div class="row">
                        <div class="col-12 sm:col-6 xl:col-3">
                            <label for="group_by" class="db-field-title after:hidden">{{ $t('label.breakdown') }}</label>
                            <vue-select class="db-field-control f-b-custom-select" id="group_by"
                                v-model="search.group_by" :options="groupByOptions" label-by="name" value-by="id"
                                :closeOnSelect="true" :searchable="false" :clearOnClose="true" placeholder="--" />
                        </div>
                        <div class="col-12 sm:col-6 xl:col-3">
                            <label for="order_type" class="db-field-title after:hidden">{{ $t('label.order_type') }}</label>
                            <vue-select class="db-field-control f-b-custom-select" id="order_type"
                                v-model="search.order_type" :options="orderTypeOptions" label-by="name" value-by="id"
                                :closeOnSelect="true" :searchable="false" :clearOnClose="true" placeholder="--" />
                        </div>
                        <div class="col-12 sm:col-6 xl:col-3">
                            <label for="source" class="db-field-title after:hidden">{{ $t('label.source') }}</label>
                            <vue-select class="db-field-control f-b-custom-select" id="source"
                                v-model="search.source" :options="sourceOptions" label-by="name" value-by="id"
                                :closeOnSelect="true" :searchable="false" :clearOnClose="true" placeholder="--" />
                        </div>
                        <div class="col-12 sm:col-6 xl:col-3">
                            <label for="payment_status" class="db-field-title after:hidden">
                                {{ $t('label.payment_status') }}
                            </label>
                            <vue-select class="db-field-control f-b-custom-select" id="payment_status"
                                v-model="search.payment_status" :options="paymentStatusOptions" label-by="name"
                                value-by="id" :closeOnSelect="true" :searchable="false" :clearOnClose="true"
                                placeholder="--" />
                        </div>
                        <div class="col-12">
                            <p class="text-xs text-[#6E7191]">{{ $t('message.profit_loss_filter_hint') }}</p>
                            <div class="flex flex-wrap gap-3 mt-4">
                                <button type="submit" class="db-btn py-2 text-white bg-primary">
                                    <i class="lab lab-search-line lab-font-size-16"></i>
                                    <span>{{ $t('button.search') }}</span>
                                </button>
                                <button type="button" class="db-btn py-2 text-white bg-gray-600" @click="clear">
                                    <i class="lab lab-cross-line-2 lab-font-size-22"></i>
                                    <span>{{ $t('button.clear') }}</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div id="profit-loss-print" class="col-12">
        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 mb-6">
            <div class="db-card p-4 flex items-center gap-4">
                <div class="w-12 h-12 flex-shrink-0 rounded-lg flex items-center justify-center bg-[#E8F8EE]">
                    <i class="fa-solid fa-sack-dollar text-xl text-[#1AB759]"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-xs capitalize text-[#6E7191]">{{ $t('label.income') }}</p>
                    <h4 class="text-lg font-semibold truncate text-heading">{{ summary.income_currency_price }}</h4>
                    <p class="text-[11px] text-[#6E7191]">
                        {{ $t('label.confirmed_orders_count', { count: summary.orders || 0 }) }}
                    </p>
                </div>
            </div>
            <div class="db-card p-4 flex items-center gap-4">
                <div class="w-12 h-12 flex-shrink-0 rounded-lg flex items-center justify-center bg-[#FFF4E5]">
                    <i class="fa-solid fa-boxes-stacked text-xl text-[#F6A609]"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-xs capitalize text-[#6E7191]">{{ $t('label.inventory_purchases') }}</p>
                    <h4 class="text-lg font-semibold truncate text-heading">
                        {{ summary.inventory_expense_currency_price }}
                    </h4>
                </div>
            </div>
            <div class="db-card p-4 flex items-center gap-4">
                <div class="w-12 h-12 flex-shrink-0 rounded-lg flex items-center justify-center bg-[#FFEDED]">
                    <i class="fa-solid fa-money-bill-wave text-xl text-[#FB4E4E]"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-xs capitalize text-[#6E7191]">{{ $t('label.other_expenses') }}</p>
                    <h4 class="text-lg font-semibold truncate text-heading">
                        {{ summary.general_expense_currency_price }}
                    </h4>
                </div>
            </div>
            <div class="db-card p-4 flex items-center gap-4">
                <div class="w-12 h-12 flex-shrink-0 rounded-lg flex items-center justify-center"
                    :class="isProfit ? 'bg-[#E8F8EE]' : 'bg-[#FFEDED]'">
                    <i class="fa-solid fa-scale-balanced text-xl"
                        :class="isProfit ? 'text-[#1AB759]' : 'text-[#FB4E4E]'"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-xs capitalize text-[#6E7191]">
                        {{ isProfit ? $t('label.net_profit') : $t('label.net_loss') }}
                    </p>
                    <h4 class="text-lg font-semibold truncate" :class="isProfit ? 'text-[#1AB759]' : 'text-[#FB4E4E]'">
                        {{ summary.net_profit_currency_price }}
                    </h4>
                    <p class="text-[11px] text-[#6E7191]">
                        {{ $t('label.total_expenses') }}: {{ summary.total_expense_currency_price }}
                    </p>
                </div>
            </div>
        </div>

        <div class="db-card mb-6">
            <div class="db-card-header">
                <h3 class="db-card-title">
                    {{ report.group_by === 'month' ? $t('label.monthly_breakdown') : $t('label.daily_breakdown') }}
                </h3>
            </div>
            <div class="db-table-responsive">
                <table class="db-table stripe">
                    <thead class="db-table-head">
                        <tr class="db-table-head-tr">
                            <th class="db-table-head-th">{{ $t('label.date') }}</th>
                            <th class="db-table-head-th">{{ $t('label.orders') }}</th>
                            <th class="db-table-head-th">{{ $t('label.income') }}</th>
                            <th class="db-table-head-th">{{ $t('label.inventory_purchases') }}</th>
                            <th class="db-table-head-th">{{ $t('label.other_expenses') }}</th>
                            <th class="db-table-head-th">{{ $t('label.net_profit') }}</th>
                        </tr>
                    </thead>
                    <tbody class="db-table-body">
                        <tr v-for="row in periods" :key="row.period" class="db-table-body-tr">
                            <td class="db-table-body-td whitespace-nowrap">{{ row.label }}</td>
                            <td class="db-table-body-td">{{ row.orders }}</td>
                            <td class="db-table-body-td whitespace-nowrap">{{ row.income_currency_price }}</td>
                            <td class="db-table-body-td whitespace-nowrap">{{ row.inventory_expense_currency_price }}</td>
                            <td class="db-table-body-td whitespace-nowrap">{{ row.general_expense_currency_price }}</td>
                            <td class="db-table-body-td whitespace-nowrap font-semibold"
                                :class="row.net_profit < 0 ? 'text-[#FB4E4E]' : 'text-[#1AB759]'">
                                {{ row.net_profit_currency_price }}
                            </td>
                        </tr>
                        <tr class="db-table-body-tr font-semibold bg-[#F7F7FC]">
                            <td class="db-table-body-td">{{ $t('label.total') }}</td>
                            <td class="db-table-body-td">{{ summary.orders }}</td>
                            <td class="db-table-body-td whitespace-nowrap">{{ summary.income_currency_price }}</td>
                            <td class="db-table-body-td whitespace-nowrap">{{ summary.inventory_expense_currency_price }}</td>
                            <td class="db-table-body-td whitespace-nowrap">{{ summary.general_expense_currency_price }}</td>
                            <td class="db-table-body-td whitespace-nowrap"
                                :class="isProfit ? 'text-[#1AB759]' : 'text-[#FB4E4E]'">
                                {{ summary.net_profit_currency_price }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="row">
            <div class="col-12 lg:col-5">
                <div class="db-card mb-6">
                    <div class="db-card-header">
                        <h3 class="db-card-title">{{ $t('label.expenses_by_category') }}</h3>
                    </div>
                    <div class="db-table-responsive">
                        <table class="db-table stripe">
                            <thead class="db-table-head">
                                <tr class="db-table-head-tr">
                                    <th class="db-table-head-th">{{ $t('label.category') }}</th>
                                    <th class="db-table-head-th">{{ $t('label.amount') }}</th>
                                </tr>
                            </thead>
                            <tbody class="db-table-body">
                                <tr v-for="category in expenseCategories" :key="category.category"
                                    class="db-table-body-tr">
                                    <td class="db-table-body-td">{{ category.category || $t('label.uncategorized') }}</td>
                                    <td class="db-table-body-td whitespace-nowrap">{{ category.amount_currency_price }}</td>
                                </tr>
                                <tr v-if="!expenseCategories.length" class="db-table-body-tr">
                                    <td class="db-table-body-td text-center" colspan="2">
                                        {{ $t('message.no_data_available') }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-12 lg:col-7">
                <div class="db-card mb-6">
                    <div class="db-card-header">
                        <h3 class="db-card-title">{{ $t('label.inventory_purchases') }}</h3>
                    </div>
                    <div class="db-table-responsive">
                        <table class="db-table stripe">
                            <thead class="db-table-head">
                                <tr class="db-table-head-tr">
                                    <th class="db-table-head-th">{{ $t('label.date') }}</th>
                                    <th class="db-table-head-th">{{ $t('label.item') }}</th>
                                    <th class="db-table-head-th">{{ $t('label.qty') }}</th>
                                    <th class="db-table-head-th">{{ $t('label.unit_cost') }}</th>
                                    <th class="db-table-head-th">{{ $t('label.total') }}</th>
                                    <th v-if="permissionChecker('inventory_delete')" class="db-table-head-th hidden-print">
                                        {{ $t('label.action') }}
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="db-table-body">
                                <tr v-for="purchase in purchases" :key="purchase.id" class="db-table-body-tr">
                                    <td class="db-table-body-td whitespace-nowrap">{{ purchase.converted_purchase_date }}</td>
                                    <td class="db-table-body-td">{{ purchase.item_name }}</td>
                                    <td class="db-table-body-td whitespace-nowrap">
                                        {{ cleanNumber(purchase.quantity) }} {{ purchase.unit }}
                                    </td>
                                    <td class="db-table-body-td whitespace-nowrap">{{ purchase.unit_cost_currency_price }}</td>
                                    <td class="db-table-body-td whitespace-nowrap">{{ purchase.total_cost_currency_price }}</td>
                                    <td v-if="permissionChecker('inventory_delete')" class="db-table-body-td hidden-print">
                                        <SmIconDeleteComponent @click="destroyPurchase(purchase.id)" />
                                    </td>
                                </tr>
                                <tr v-if="!purchases.length" class="db-table-body-tr">
                                    <td class="db-table-body-td text-center" colspan="6">
                                        {{ $t('message.no_data_available') }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <p class="px-4 py-3 text-[11px] text-[#6E7191]">{{ $t('message.inventory_purchase_hint') }}</p>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import LoadingComponent from "../components/LoadingComponent";
import SmIconDeleteComponent from "../components/buttons/SmIconDeleteComponent";
import FilterComponent from "../components/buttons/collapse/FilterComponent";
import ExportComponent from "../components/buttons/export/ExportComponent";
import PrintComponent from "../components/buttons/export/PrintComponent";
import ExcelComponent from "../components/buttons/export/ExcelComponent";
import PdfComponent from "../components/buttons/export/PdfComponent";
import Datepicker from "@vuepic/vue-datepicker";
import "@vuepic/vue-datepicker/dist/main.css";
import { endOfMonth, endOfYear, startOfMonth, startOfYear, subDays, subMonths, subYears } from "date-fns";
import appService from "../../../services/appService";
import alertService from "../../../services/alertService";
import orderTypeEnum from "../../../enums/modules/orderTypeEnum";
import sourceEnum from "../../../enums/modules/sourceEnum";
import paymentStatusEnum from "../../../enums/modules/paymentStatusEnum";

export default {
    name: 'ProfitLossReportComponent',
    components: {
        LoadingComponent,
        SmIconDeleteComponent,
        FilterComponent,
        ExportComponent,
        PrintComponent,
        ExcelComponent,
        PdfComponent,
        Datepicker
    },
    data() {
        const today = new Date();
        const yesterday = subDays(today, 1);
        const lastYear = subYears(today, 1);
        return {
            loading: {
                isActive: false
            },
            dateRange: [startOfMonth(today), today],
            presetRanges: [
                { label: this.$t('label.preset_today'), range: [today, today] },
                { label: this.$t('label.yesterday'), range: [yesterday, yesterday] },
                { label: this.$t('label.last_7_days'), range: [subDays(today, 6), today] },
                { label: this.$t('label.last_30_days'), range: [subDays(today, 29), today] },
                { label: this.$t('label.this_month'), range: [startOfMonth(today), endOfMonth(today)] },
                {
                    label: this.$t('label.last_month'),
                    range: [startOfMonth(subMonths(today, 1)), endOfMonth(subMonths(today, 1))]
                },
                { label: this.$t('label.this_year'), range: [startOfYear(today), endOfYear(today)] },
                { label: this.$t('label.last_year'), range: [startOfYear(lastYear), endOfYear(lastYear)] }
            ],
            groupByOptions: [
                { id: 'day', name: this.$t('label.daily') },
                { id: 'month', name: this.$t('label.monthly') }
            ],
            orderTypeOptions: [
                { id: orderTypeEnum.DINING_TABLE, name: this.$t('label.dining_table') },
                { id: orderTypeEnum.TAKEAWAY, name: this.$t('label.takeaway') },
                { id: orderTypeEnum.DELIVERY, name: this.$t('label.delivery') }
            ],
            sourceOptions: [
                { id: sourceEnum.POS, name: this.$t('label.pos') },
                { id: sourceEnum.WEB, name: this.$t('label.website') },
                { id: sourceEnum.APP, name: this.$t('label.app') }
            ],
            paymentStatusOptions: [
                { id: paymentStatusEnum.PAID, name: this.$t('label.paid') },
                { id: paymentStatusEnum.UNPAID, name: this.$t('label.unpaid') }
            ],
            printObj: {
                id: 'profit-loss-print',
                popTitle: this.$t('menu.profit_loss')
            },
            search: {
                from_date: startOfMonth(today).toLocaleDateString('en-CA'),
                to_date: today.toLocaleDateString('en-CA'),
                group_by: null,
                order_type: null,
                source: null,
                payment_status: null
            }
        }
    },
    computed: {
        report: function () {
            return this.$store.getters['profitLoss/report'];
        },
        // The order filters the shown report was made with, e.g. ["Source: POS"]
        activeFilters: function () {
            return this.report.filters || [];
        },
        summary: function () {
            return this.report.summary || {};
        },
        periods: function () {
            return this.report.periods || [];
        },
        expenseCategories: function () {
            return this.report.expense_categories || [];
        },
        purchases: function () {
            return this.report.inventory_purchases || [];
        },
        isProfit: function () {
            return (this.summary.net_profit || 0) >= 0;
        }
    },
    mounted() {
        this.list();
    },
    methods: {
        permissionChecker: function (permission) {
            return appService.permissionChecker(permission);
        },
        handleSlide: function (id) {
            return appService.handleSlide(id);
        },
        clear: function () {
            this.search.group_by = null;
            this.search.order_type = null;
            this.search.source = null;
            this.search.payment_status = null;
            this.list();
        },
        xls: function () {
            this.download('profitLoss/export', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', '.xlsx');
        },
        pdf: function () {
            this.download('profitLoss/pdf', 'application/pdf', '.pdf');
        },
        // Saves the report file for the dates and filters on screen
        download: function (action, type, extension) {
            this.loading.isActive = true;
            this.$store.dispatch(action, this.search).then((res) => {
                this.loading.isActive = false;
                const link = document.createElement('a');
                link.href = URL.createObjectURL(new Blob([res.data], { type: type }));
                link.download = this.$t('menu.profit_loss') + ' ' + this.search.from_date + ' to ' + this.search.to_date + extension;
                link.click();
                URL.revokeObjectURL(link.href);
            }).catch((err) => {
                this.loading.isActive = false;
                // A failed file request still comes back as a Blob holding the JSON error
                const data = err.response?.data;
                if (data instanceof Blob) {
                    data.text().then((text) => {
                        let message = err.message;
                        try {
                            message = JSON.parse(text).message || message;
                        } catch (e) {
                            // not JSON: keep the request error
                        }
                        alertService.error(message);
                    });
                } else {
                    alertService.error(data?.message || err.message);
                }
            });
        },
        cleanNumber: function (value) {
            return Number(value).toLocaleString(undefined, { maximumFractionDigits: 3 });
        },
        // The API expects plain YYYY-MM-DD dates (local, not UTC).
        handleDate: function (range) {
            if (!range) {
                return;
            }
            this.search.from_date = range[0].toLocaleDateString('en-CA');
            this.search.to_date = (range[1] || range[0]).toLocaleDateString('en-CA');
            this.list();
        },
        list: function () {
            this.loading.isActive = true;
            this.$store.dispatch('profitLoss/report', this.search).then(() => {
                this.loading.isActive = false;
            }).catch((err) => {
                this.loading.isActive = false;
                alertService.error(err.response?.data?.message || err.message);
            });
        },
        destroyPurchase: function (id) {
            appService.destroyConfirmation().then(() => {
                this.loading.isActive = true;
                this.$store.dispatch('profitLoss/destroyPurchase', { id: id, search: this.search }).then(() => {
                    this.loading.isActive = false;
                    alertService.successFlip(null, this.$t('label.inventory_purchases'));
                }).catch((err) => {
                    this.loading.isActive = false;
                    alertService.error(err.response?.data?.message || err.message);
                });
            }).catch(() => {});
        }
    }
}
</script>

<style scoped>
@media print {
    .hidden-print { display: none !important; }
}
</style>
