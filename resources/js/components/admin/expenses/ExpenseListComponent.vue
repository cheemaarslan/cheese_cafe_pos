<template>
    <LoadingComponent :props="loading" />
    <div class="col-12">
        <div class="db-card">
            <div class="db-card-header border-none">
                <h3 class="db-card-title">{{ $t('menu.expenses') }}</h3>
                <div class="db-card-filter">
                    <TableLimitComponent :method="list" :search="props.search" :page="paginationPage" />
                    <FilterComponent @click.prevent="handleSlide('expense-filter')" />
                    <div class="dropdown-group">
                        <ExportComponent />
                        <div
                            class="dropdown-list db-card-filter-dropdown-list transition-all duration-300 scale-y-0 origin-top">
                            <PrintComponent :props="printObj" />
                        </div>
                    </div>
                    <ExpenseCreateComponent v-if="permissionChecker('expenses_create')" :props="props" />
                </div>
            </div>

            <div id="expense-filter" class="table-filter-div">
                <form class="p-4 sm:p-5 mb-5" @submit.prevent="search">
                    <div class="row">
                        <div class="col-12 sm:col-6 md:col-4 xl:col-3">
                            <label for="expense-search-title" class="db-field-title after:hidden">
                                {{ $t('label.title') }}
                            </label>
                            <input id="expense-search-title" v-model="props.search.title" type="text"
                                class="db-field-control" />
                        </div>
                        <div class="col-12 sm:col-6 md:col-4 xl:col-3">
                            <label for="expense-search-category" class="db-field-title after:hidden">
                                {{ $t('label.category') }}
                            </label>
                            <input id="expense-search-category" v-model="props.search.category" type="text"
                                class="db-field-control" />
                        </div>
                        <div class="col-12 sm:col-6 md:col-4 xl:col-3">
                            <label class="db-field-title after:hidden">{{ $t('label.date') }}</label>
                            <Datepicker hideInputIcon autoApply :enableTimePicker="false" :utc="false"
                                @update:modelValue="handleDate" v-model="dateRange" range />
                        </div>

                        <div class="col-12">
                            <div class="flex flex-wrap gap-3 mt-4">
                                <button class="db-btn py-2 text-white bg-primary">
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

            <div class="db-table-responsive">
                <table id="print" class="db-table stripe">
                    <thead class="db-table-head">
                        <tr class="db-table-head-tr">
                            <th class="db-table-head-th">{{ $t('label.date') }}</th>
                            <th class="db-table-head-th">{{ $t('label.title') }}</th>
                            <th class="db-table-head-th">{{ $t('label.category') }}</th>
                            <th class="db-table-head-th">{{ $t('label.amount') }}</th>
                            <th class="db-table-head-th">{{ $t('label.notes') }}</th>
                            <th v-if="hasActions" class="db-table-head-th hidden-print">{{ $t('label.action') }}</th>
                        </tr>
                    </thead>
                    <tbody v-if="expenses.length" class="db-table-body">
                        <tr v-for="expense in expenses" :key="expense.id" class="db-table-body-tr">
                            <td class="db-table-body-td whitespace-nowrap">{{ expense.converted_expense_date }}</td>
                            <td class="db-table-body-td font-medium">{{ expense.title }}</td>
                            <td class="db-table-body-td">{{ expense.category || '-' }}</td>
                            <td class="db-table-body-td font-semibold text-[#FB4E4E] whitespace-nowrap">
                                {{ expense.amount_currency_price }}
                            </td>
                            <td class="db-table-body-td max-w-[280px] truncate" :title="expense.notes">
                                {{ expense.notes || '-' }}
                            </td>
                            <td v-if="hasActions" class="db-table-body-td hidden-print">
                                <div class="flex justify-start items-center gap-1.5">
                                    <SmIconSidebarModalEditComponent v-if="permissionChecker('expenses_edit')"
                                        @click="edit(expense)" />
                                    <SmIconDeleteComponent v-if="permissionChecker('expenses_delete')"
                                        @click="destroy(expense.id)" />
                                </div>
                            </td>
                        </tr>
                    </tbody>
                    <tbody v-else class="db-table-body">
                        <tr class="db-table-body-tr">
                            <td class="db-table-body-td text-center" colspan="6">
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

            <div v-if="expenses.length"
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
import ExpenseCreateComponent from "./ExpenseCreateComponent";
import LoadingComponent from "../components/LoadingComponent";
import TableLimitComponent from "../components/TableLimitComponent";
import FilterComponent from "../components/buttons/collapse/FilterComponent";
import ExportComponent from "../components/buttons/export/ExportComponent";
import PrintComponent from "../components/buttons/export/PrintComponent";
import PaginationTextComponent from "../components/pagination/PaginationTextComponent";
import PaginationBox from "../components/pagination/PaginationBox";
import PaginationSMBox from "../components/pagination/PaginationSMBox";
import SmIconSidebarModalEditComponent from "../components/buttons/SmIconSidebarModalEditComponent";
import SmIconDeleteComponent from "../components/buttons/SmIconDeleteComponent";
import Datepicker from "@vuepic/vue-datepicker";
import "@vuepic/vue-datepicker/dist/main.css";
import appService from "../../../services/appService";
import alertService from "../../../services/alertService";
import ENV from "../../../config/env";

export default {
    name: 'ExpenseListComponent',
    components: {
        ExpenseCreateComponent,
        LoadingComponent,
        TableLimitComponent,
        FilterComponent,
        ExportComponent,
        PrintComponent,
        PaginationTextComponent,
        PaginationBox,
        PaginationSMBox,
        SmIconSidebarModalEditComponent,
        SmIconDeleteComponent,
        Datepicker
    },
    data() {
        return {
            loading: {
                isActive: false
            },
            ENV: ENV,
            dateRange: null,
            printObj: {
                id: 'print',
                popTitle: this.$t('menu.expenses')
            },
            props: {
                form: {
                    title: '',
                    category: '',
                    amount: '',
                    expense_date: new Date().toLocaleDateString('en-CA'),
                    notes: ''
                },
                search: {
                    paginate: 1,
                    page: 1,
                    per_page: 10,
                    order_column: 'expense_date',
                    order_type: 'desc',
                    title: '',
                    category: '',
                    from_date: '',
                    to_date: ''
                }
            }
        }
    },
    computed: {
        expenses: function () {
            return this.$store.getters['expense/lists'];
        },
        pagination: function () {
            return this.$store.getters['expense/pagination'];
        },
        paginationPage: function () {
            return this.$store.getters['expense/page'];
        },
        hasActions: function () {
            return this.permissionChecker('expenses_edit') || this.permissionChecker('expenses_delete');
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
        // The API expects plain YYYY-MM-DD dates (local, not UTC).
        handleDate: function (range) {
            this.props.search.from_date = range ? range[0].toLocaleDateString('en-CA') : '';
            this.props.search.to_date = range && range[1] ? range[1].toLocaleDateString('en-CA') : this.props.search.from_date;
        },
        list: function (page = 1) {
            this.loading.isActive = true;
            this.props.search.page = page;
            this.$store.dispatch('expense/lists', this.props.search).then(() => {
                this.loading.isActive = false;
            }).catch((err) => {
                this.loading.isActive = false;
                alertService.error(err.response?.data?.message || err.message);
            });
        },
        search: function () {
            this.list(1);
        },
        clear: function () {
            this.dateRange = null;
            Object.assign(this.props.search, {
                page: 1,
                title: '',
                category: '',
                from_date: '',
                to_date: ''
            });
            this.list(1);
        },
        edit: function (expense) {
            appService.sideDrawerShow();
            this.$store.dispatch('expense/edit', expense.id);
            this.props.form = {
                title: expense.title,
                category: expense.category,
                amount: expense.amount,
                expense_date: expense.expense_date,
                notes: expense.notes
            };
        },
        destroy: function (id) {
            appService.destroyConfirmation().then(() => {
                this.loading.isActive = true;
                this.$store.dispatch('expense/destroy', { id: id, search: this.props.search }).then(() => {
                    this.loading.isActive = false;
                    alertService.successFlip(null, this.$t('menu.expenses'));
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
