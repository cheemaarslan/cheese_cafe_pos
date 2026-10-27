<template>
    <LoadingComponent :props="loading" />
    <div class="col-12">
        <div class="db-card mb-6">
            <div class="db-card-header border-none flex-wrap gap-3">
                <div>
                    <h3 class="db-card-title">{{ $t('menu.inventory_report') }}</h3>
                    <p class="text-xs mt-1 text-[#6E7191]">{{ $t('message.inventory_report_hint') }}</p>
                </div>
                <div class="flex flex-wrap items-center gap-3">
                    <input v-model="search.name" type="text" class="db-field-control w-full sm:w-48"
                        :placeholder="$t('label.search') + '...'" @keyup.enter="list">
                    <div class="w-full sm:w-64">
                        <Datepicker hideInputIcon autoApply :enableTimePicker="false" :utc="false" range
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

    <div id="inventory-report-print" class="col-12">
        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 mb-6">
            <div v-for="card in cards" :key="card.label" class="db-card p-4 flex items-center gap-4">
                <div class="w-12 h-12 flex-shrink-0 rounded-lg flex items-center justify-center" :class="card.background">
                    <i class="text-xl" :class="[card.icon, card.color]"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-xs capitalize text-[#6E7191]">{{ card.label }}</p>
                    <h4 class="text-lg font-semibold truncate text-heading">{{ card.value }}</h4>
                </div>
            </div>
        </div>

        <div class="db-card">
            <div class="db-table-responsive">
                <table class="db-table stripe">
                    <thead class="db-table-head">
                        <tr class="db-table-head-tr">
                            <th class="db-table-head-th">{{ $t('label.item') }}</th>
                            <th class="db-table-head-th">{{ $t('label.opening_stock') }}</th>
                            <th class="db-table-head-th">{{ $t('label.purchased') }}</th>
                            <th class="db-table-head-th">{{ $t('label.used_in_orders') }}</th>
                            <th class="db-table-head-th">{{ $t('label.wasted') }}</th>
                            <th class="db-table-head-th">{{ $t('label.count_difference') }}</th>
                            <th class="db-table-head-th">{{ $t('label.adjusted') }}</th>
                            <th class="db-table-head-th">{{ $t('label.closing_stock') }}</th>
                            <th class="db-table-head-th">{{ $t('label.used_value') }}</th>
                            <th class="db-table-head-th">{{ $t('label.wasted_value') }}</th>
                        </tr>
                    </thead>
                    <tbody class="db-table-body">
                        <tr v-for="row in rows" :key="row.id" class="db-table-body-tr">
                            <td class="db-table-body-td">
                                <router-link :to="dailyLink(row)" class="block font-medium text-primary hover:underline">
                                    {{ row.name }}
                                </router-link>
                                <span v-if="row.deleted" class="db-table-badge text-red-600 bg-red-100 mt-1">
                                    {{ $t('label.deleted') }}
                                </span>
                                <span v-else-if="row.is_low_stock" class="db-table-badge text-red-600 bg-red-100 mt-1">
                                    {{ $t('label.low_stock') }}
                                </span>
                            </td>
                            <td class="db-table-body-td whitespace-nowrap">{{ quantity(row.opening, row.unit) }}</td>
                            <td class="db-table-body-td whitespace-nowrap">{{ quantity(row.purchased, row.unit) }}</td>
                            <td class="db-table-body-td whitespace-nowrap">{{ quantity(row.used, row.unit) }}</td>
                            <td class="db-table-body-td whitespace-nowrap">{{ quantity(row.wasted, row.unit) }}</td>
                            <td class="db-table-body-td whitespace-nowrap" :class="signClass(row.counted)">
                                {{ quantity(row.counted, row.unit, true) }}
                            </td>
                            <td class="db-table-body-td whitespace-nowrap" :class="signClass(row.adjusted)">
                                {{ quantity(row.adjusted, row.unit, true) }}
                            </td>
                            <td class="db-table-body-td whitespace-nowrap font-semibold"
                                :class="row.closing < 0 ? 'text-red-600' : ''">
                                {{ Number(row.closing).toLocaleString(undefined, { maximumFractionDigits: 3 }) }} {{ row.unit }}
                            </td>
                            <td class="db-table-body-td whitespace-nowrap">{{ row.used_value ? row.used_value_currency_price : '-' }}</td>
                            <td class="db-table-body-td whitespace-nowrap">{{ row.wasted_value ? row.wasted_value_currency_price : '-' }}</td>
                        </tr>
                        <tr v-if="!rows.length" class="db-table-body-tr">
                            <td class="db-table-body-td text-center" colspan="10">{{ $t('message.no_data_available') }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>

<script>
import LoadingComponent from "../components/LoadingComponent";
import Datepicker from "@vuepic/vue-datepicker";
import "@vuepic/vue-datepicker/dist/main.css";
import { endOfMonth, startOfMonth, subMonths } from "date-fns";
import print from "vue3-print-nb";
import alertService from "../../../services/alertService";

// YYYY-MM-DD from the address bar as a local date, or null
const parseDate = function (text) {
    const match = /^(\d{4})-(\d{2})-(\d{2})$/.exec(text || '');
    return match ? new Date(Number(match[1]), Number(match[2]) - 1, Number(match[3])) : null;
};

export default {
    name: 'InventoryReportListComponent',
    components: {
        LoadingComponent,
        Datepicker
    },
    directives: {
        print
    },
    data() {
        // Coming back from an item's day-by-day view keeps its dates
        const from = parseDate(this.$route.query.from) || startOfMonth(new Date());
        const to = parseDate(this.$route.query.to) || new Date();
        return {
            loading: {
                isActive: false
            },
            dateRange: [from, to],
            presetRanges: [
                { label: this.$t('label.preset_today'), range: [new Date(), new Date()] },
                { label: this.$t('label.this_month'), range: [startOfMonth(new Date()), endOfMonth(new Date())] },
                {
                    label: this.$t('label.last_month'),
                    range: [startOfMonth(subMonths(new Date(), 1)), endOfMonth(subMonths(new Date(), 1))]
                }
            ],
            printObj: {
                id: 'inventory-report-print',
                popTitle: this.$t('menu.inventory_report')
            },
            search: {
                from_date: from.toLocaleDateString('en-CA'),
                to_date: to.toLocaleDateString('en-CA'),
                name: ''
            }
        }
    },
    computed: {
        report: function () {
            return this.$store.getters['inventoryReport/report'];
        },
        rows: function () {
            return this.report.rows || [];
        },
        summary: function () {
            return this.report.summary || {};
        },
        cards: function () {
            return [
                {
                    label: this.$t('label.used_value'), value: this.summary.used_value_currency_price,
                    icon: 'fa-solid fa-utensils', color: 'text-[#1AB759]', background: 'bg-[#E8F8EE]'
                },
                {
                    label: this.$t('label.wasted_value'), value: this.summary.wasted_value_currency_price,
                    icon: 'fa-solid fa-trash-can', color: 'text-[#FB4E4E]', background: 'bg-[#FFEDED]'
                },
                {
                    label: this.$t('label.count_difference'), value: this.summary.counted_value_currency_price,
                    icon: 'fa-solid fa-clipboard-check', color: 'text-[#F6A609]', background: 'bg-[#FFF4E5]'
                },
                {
                    label: this.$t('label.closing_stock_value'), value: this.summary.closing_value_currency_price,
                    icon: 'fa-solid fa-boxes-stacked', color: 'text-[#567DFF]', background: 'bg-[#EEF2FF]'
                }
            ];
        }
    },
    mounted() {
        this.list();
    },
    methods: {
        // Zero shows as a dash; signed columns (count difference, adjusted) show + for stock added
        quantity: function (value, unit, signed = false) {
            if (!Number(value)) {
                return '-';
            }
            const text = Number(value).toLocaleString(undefined, { maximumFractionDigits: 3 }) + ' ' + unit;
            return signed && value > 0 ? '+' + text : text;
        },
        signClass: function (value) {
            return value < 0 ? 'text-red-600' : (value > 0 ? 'text-green-600' : '');
        },
        // The item's day-by-day view over the same dates
        dailyLink: function (row) {
            return {
                name: 'admin.inventory-report.daily',
                params: { id: row.id },
                query: { from: this.search.from_date, to: this.search.to_date }
            };
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
            this.$store.dispatch('inventoryReport/report', this.search).then(() => {
                this.loading.isActive = false;
            }).catch((err) => {
                this.loading.isActive = false;
                alertService.error(err.response?.data?.message || err.message);
            });
        }
    }
}
</script>
