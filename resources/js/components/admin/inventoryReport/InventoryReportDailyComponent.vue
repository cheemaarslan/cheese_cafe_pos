<template>
    <LoadingComponent :props="loading" />
    <div class="col-12">
        <div class="db-card mb-6">
            <div class="db-card-header border-none flex-wrap gap-3">
                <div>
                    <h3 class="db-card-title">
                        {{ item.name }}
                        <span class="text-[#6E7191] font-normal">· {{ $t('label.daily_breakdown') }}</span>
                        <span v-if="item.deleted" class="db-table-badge text-red-600 bg-red-100 ml-1">
                            {{ $t('label.deleted') }}
                        </span>
                    </h3>
                    <p class="text-xs mt-1 text-[#6E7191]">{{ $t('message.inventory_daily_hint') }}</p>
                </div>
                <div class="flex flex-wrap items-center gap-3">
                    <label class="flex items-center gap-2 text-sm cursor-pointer">
                        <input v-model="onlyEntries" type="checkbox" class="w-4 h-4">
                        <span>{{ $t('label.only_days_with_entries') }}</span>
                    </label>
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
                    <router-link :to="backLink" class="db-btn h-[38px] text-white bg-primary">
                        <i class="lab lab-arrow-left"></i>
                        <span>{{ $t('button.back') }}</span>
                    </router-link>
                </div>
            </div>
        </div>
    </div>

    <div id="inventory-daily-print" class="col-12">
        <div class="db-card">
            <div class="db-table-responsive">
                <table class="db-table stripe">
                    <thead class="db-table-head">
                        <tr class="db-table-head-tr">
                            <th class="db-table-head-th">{{ $t('label.date') }}</th>
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
                        <tr v-for="row in rows" :key="row.date" class="db-table-body-tr"
                            :class="row.has_entries ? '' : 'text-[#A0A3BD]'">
                            <td class="db-table-body-td whitespace-nowrap font-medium">{{ row.converted_date }}</td>
                            <td class="db-table-body-td whitespace-nowrap">{{ stock(row.opening) }}</td>
                            <td class="db-table-body-td whitespace-nowrap">{{ quantity(row.purchased) }}</td>
                            <td class="db-table-body-td whitespace-nowrap">{{ quantity(row.used) }}</td>
                            <td class="db-table-body-td whitespace-nowrap">{{ quantity(row.wasted) }}</td>
                            <td class="db-table-body-td whitespace-nowrap" :class="signClass(row.counted)">
                                {{ quantity(row.counted, true) }}
                            </td>
                            <td class="db-table-body-td whitespace-nowrap" :class="signClass(row.adjusted)">
                                {{ quantity(row.adjusted, true) }}
                            </td>
                            <td class="db-table-body-td whitespace-nowrap font-semibold"
                                :class="row.closing < 0 ? 'text-red-600' : ''">
                                {{ stock(row.closing) }}
                            </td>
                            <td class="db-table-body-td whitespace-nowrap">{{ row.used_value ? row.used_value_currency_price : '-' }}</td>
                            <td class="db-table-body-td whitespace-nowrap">{{ row.wasted_value ? row.wasted_value_currency_price : '-' }}</td>
                        </tr>
                        <tr v-if="!rows.length" class="db-table-body-tr">
                            <td class="db-table-body-td text-center" colspan="10">{{ $t('message.no_data_available') }}</td>
                        </tr>
                        <tr v-if="daily.totals" class="db-table-body-tr font-semibold border-t-2 border-[#EFF0F6]">
                            <td class="db-table-body-td whitespace-nowrap">{{ $t('label.total') }}</td>
                            <td class="db-table-body-td whitespace-nowrap">{{ stock(totals.opening) }}</td>
                            <td class="db-table-body-td whitespace-nowrap">{{ quantity(totals.purchased) }}</td>
                            <td class="db-table-body-td whitespace-nowrap">{{ quantity(totals.used) }}</td>
                            <td class="db-table-body-td whitespace-nowrap">{{ quantity(totals.wasted) }}</td>
                            <td class="db-table-body-td whitespace-nowrap" :class="signClass(totals.counted)">
                                {{ quantity(totals.counted, true) }}
                            </td>
                            <td class="db-table-body-td whitespace-nowrap" :class="signClass(totals.adjusted)">
                                {{ quantity(totals.adjusted, true) }}
                            </td>
                            <td class="db-table-body-td whitespace-nowrap" :class="totals.closing < 0 ? 'text-red-600' : ''">
                                {{ stock(totals.closing) }}
                            </td>
                            <td class="db-table-body-td whitespace-nowrap">{{ totals.used_value ? totals.used_value_currency_price : '-' }}</td>
                            <td class="db-table-body-td whitespace-nowrap">{{ totals.wasted_value ? totals.wasted_value_currency_price : '-' }}</td>
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
    name: 'InventoryReportDailyComponent',
    components: {
        LoadingComponent,
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
            onlyEntries: false,
            dateRange: [startOfMonth(new Date()), new Date()],
            presetRanges: [
                { label: this.$t('label.preset_today'), range: [new Date(), new Date()] },
                { label: this.$t('label.this_month'), range: [startOfMonth(new Date()), endOfMonth(new Date())] },
                {
                    label: this.$t('label.last_month'),
                    range: [startOfMonth(subMonths(new Date(), 1)), endOfMonth(subMonths(new Date(), 1))]
                }
            ],
            printObj: {
                id: 'inventory-daily-print',
                popTitle: this.$t('label.daily_breakdown')
            },
            search: {
                from_date: '',
                to_date: ''
            }
        }
    },
    watch: {
        // Another item or dates put in the address while this page is open reuse the page
        '$route.fullPath': function () {
            if (this.$route.name === 'admin.inventory-report.daily') {
                this.open();
            }
        }
    },
    computed: {
        daily: function () {
            return this.$store.getters['inventoryReport/daily'];
        },
        item: function () {
            return this.daily.item || {};
        },
        rows: function () {
            const rows = this.daily.rows || [];
            return this.onlyEntries ? rows.filter((row) => row.has_entries) : rows;
        },
        totals: function () {
            return this.daily.totals || {};
        },
        // Back to the report on the same dates
        backLink: function () {
            return {
                name: 'admin.inventory-report.list',
                query: { from: this.search.from_date, to: this.search.to_date }
            };
        }
    },
    mounted() {
        this.open();
    },
    methods: {
        // The item and dates in the address
        open: function () {
            const from = parseDate(this.$route.query.from) || startOfMonth(new Date());
            const to = parseDate(this.$route.query.to) || new Date();
            this.dateRange = [from, to];
            this.search.from_date = from.toLocaleDateString('en-CA');
            this.search.to_date = to.toLocaleDateString('en-CA');
            // Don't show another item's days while this one loads
            this.$store.commit('inventoryReport/daily', {});
            this.list();
        },
        stock: function (value) {
            return Number(value || 0).toLocaleString(undefined, { maximumFractionDigits: 3 }) + ' ' + (this.item.unit || '');
        },
        // Zero shows as a dash; signed columns (count difference, adjusted) show + for stock added
        quantity: function (value, signed = false) {
            if (!Number(value)) {
                return '-';
            }
            const text = this.stock(value);
            return signed && value > 0 ? '+' + text : text;
        },
        signClass: function (value) {
            return value < 0 ? 'text-red-600' : (value > 0 ? 'text-green-600' : '');
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
            this.$store.dispatch('inventoryReport/daily', { id: this.$route.params.id, search: this.search }).then(() => {
                this.printObj.popTitle = this.item.name + ' - ' + this.$t('label.daily_breakdown');
                this.loading.isActive = false;
            }).catch((err) => {
                this.loading.isActive = false;
                alertService.error(err.response?.data?.message || err.message);
            });
        }
    }
}
</script>
