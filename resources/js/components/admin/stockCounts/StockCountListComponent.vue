<template>
    <LoadingComponent :props="loading" />
    <div class="col-12">
        <div class="db-card">
            <div class="db-card-header border-none flex-wrap gap-3">
                <div>
                    <h3 class="db-card-title">{{ $t('menu.stock_counts') }}</h3>
                    <p class="text-xs mt-1 text-[#6E7191]">{{ $t('message.stock_count_hint') }}</p>
                </div>
                <div class="flex flex-wrap items-center gap-3">
                    <div class="w-full sm:w-64">
                        <Datepicker hideInputIcon autoApply :enableTimePicker="false" :utc="false" range
                            v-model="dateRange" :placeholder="$t('label.date')" @update:modelValue="handleDate" />
                    </div>
                    <router-link v-if="permissionChecker('stock_counts_create')" :to="{ name: 'admin.stock-counts.create' }"
                        class="db-btn h-[37px] text-white bg-primary">
                        <i class="lab lab-add-circle-line"></i>
                        <span>{{ $t('button.new_stock_count') }}</span>
                    </router-link>
                </div>
            </div>

            <div class="db-table-responsive">
                <table class="db-table stripe">
                    <thead class="db-table-head">
                        <tr class="db-table-head-tr">
                            <th class="db-table-head-th">#</th>
                            <th class="db-table-head-th">{{ $t('label.date') }}</th>
                            <th class="db-table-head-th">{{ $t('label.items_counted') }}</th>
                            <th class="db-table-head-th">{{ $t('label.items_different') }}</th>
                            <th class="db-table-head-th">{{ $t('label.difference_value') }}</th>
                            <th class="db-table-head-th">{{ $t('label.note') }}</th>
                            <th class="db-table-head-th">{{ $t('label.by') }}</th>
                            <th v-if="permissionChecker('stock_counts_show')" class="db-table-head-th">
                                {{ $t('label.action') }}
                            </th>
                        </tr>
                    </thead>
                    <tbody class="db-table-body">
                        <tr v-for="count in counts" :key="count.id" class="db-table-body-tr">
                            <td class="db-table-body-td">{{ count.id }}</td>
                            <td class="db-table-body-td whitespace-nowrap">{{ count.converted_created_at }}</td>
                            <td class="db-table-body-td">{{ count.items_count }}</td>
                            <td class="db-table-body-td">{{ count.changed_items_count }}</td>
                            <td class="db-table-body-td whitespace-nowrap font-semibold"
                                :class="count.difference_value < 0 ? 'text-red-600' : (count.difference_value > 0 ? 'text-green-600' : '')">
                                {{ count.difference_value_currency_price }}
                            </td>
                            <td class="db-table-body-td">{{ count.note || '-' }}</td>
                            <td class="db-table-body-td">{{ count.created_by_name || '-' }}</td>
                            <td v-if="permissionChecker('stock_counts_show')" class="db-table-body-td">
                                <SmIconViewComponent link="admin.stock-counts.show" :id="count.id" />
                            </td>
                        </tr>
                        <tr v-if="!counts.length" class="db-table-body-tr">
                            <td class="db-table-body-td text-center" colspan="8">
                                {{ $t('message.no_data_available') }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div v-if="counts.length"
                class="flex items-center justify-between border-t border-gray-200 bg-white px-4 py-6">
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
import SmIconViewComponent from "../components/buttons/SmIconViewComponent";
import PaginationTextComponent from "../components/pagination/PaginationTextComponent";
import PaginationBox from "../components/pagination/PaginationBox";
import PaginationSMBox from "../components/pagination/PaginationSMBox";
import Datepicker from "@vuepic/vue-datepicker";
import "@vuepic/vue-datepicker/dist/main.css";
import appService from "../../../services/appService";
import alertService from "../../../services/alertService";

export default {
    name: 'StockCountListComponent',
    components: {
        LoadingComponent,
        SmIconViewComponent,
        PaginationTextComponent,
        PaginationBox,
        PaginationSMBox,
        Datepicker
    },
    data() {
        return {
            loading: {
                isActive: false
            },
            dateRange: null,
            search: {
                paginate: 1,
                page: 1,
                per_page: 10,
                from_date: '',
                to_date: ''
            }
        }
    },
    computed: {
        counts: function () {
            return this.$store.getters['stockCount/lists'];
        },
        pagination: function () {
            return this.$store.getters['stockCount/pagination'];
        },
        paginationPage: function () {
            return this.$store.getters['stockCount/page'];
        }
    },
    mounted() {
        this.list();
    },
    methods: {
        permissionChecker: function (permission) {
            return appService.permissionChecker(permission);
        },
        handleDate: function (range) {
            this.search.from_date = range ? range[0].toLocaleDateString('en-CA') : '';
            this.search.to_date = range ? (range[1] || range[0]).toLocaleDateString('en-CA') : '';
            this.list(1);
        },
        list: function (page = 1) {
            this.loading.isActive = true;
            this.search.page = page;
            this.$store.dispatch('stockCount/lists', this.search).then(() => {
                this.loading.isActive = false;
            }).catch((err) => {
                this.loading.isActive = false;
                alertService.error(err.response?.data?.message || err.message);
            });
        }
    }
}
</script>
