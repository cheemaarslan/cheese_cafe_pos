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
                </div>
                <div class="flex flex-wrap items-center gap-3">
                    <div class="w-full sm:w-64">
                        <Datepicker hideInputIcon autoApply :enableTimePicker="false" utc="false" range
                            :clearable="false" v-model="dateRange" :preset-ranges="presetRanges"
                            @update:modelValue="handleDate" />
                    </div>
                    <button type="button" v-print="printObj"
                        class="flex items-center justify-center gap-2 px-4 h-[38px] rounded shadow-db-card bg-primary">
                        <i class="lab lab-printer-line lab-font-size-16 text-white"></i>
                        <span class="text-sm capitalize text-white">{{ $t('button.print') }}</span>
                    </button>
                </div>
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
import Datepicker from "@vuepic/vue-datepicker";
import "@vuepic/vue-datepicker/dist/main.css";
import { endOfMonth, endOfYear, startOfMonth, startOfYear, subMonths } from "date-fns";
import print from "vue3-print-nb";
import appService from "../../../services/appService";
import alertService from "../../../services/alertService";

export default {
    name: 'ProfitLossReportComponent',
    components: {
        LoadingComponent,
        SmIconDeleteComponent,
        Datepicker
    },
    directives: {
        print
    },
    data() {
        return {
            loading: {
                isActive: false
            },
            dateRange: [startOfMonth(new Date()), new Date()],
            presetRanges: [
                { label: this.$t('label.preset_today'), range: [new Date(), new Date()] },
                { label: this.$t('label.this_month'), range: [startOfMonth(new Date()), endOfMonth(new Date())] },
                {
                    label: this.$t('label.last_month'),
                    range: [startOfMonth(subMonths(new Date(), 1)), endOfMonth(subMonths(new Date(), 1))]
                },
                { label: this.$t('label.this_year'), range: [startOfYear(new Date()), endOfYear(new Date())] }
            ],
            printObj: {
                id: 'profit-loss-print',
                popTitle: this.$t('menu.profit_loss')
            },
            search: {
                from_date: startOfMonth(new Date()).toLocaleDateString('en-CA'),
                to_date: new Date().toLocaleDateString('en-CA')
            }
        }
    },
    computed: {
        report: function () {
            return this.$store.getters['profitLoss/report'];
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
