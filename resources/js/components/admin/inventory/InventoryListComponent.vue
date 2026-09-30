<template>
    <LoadingComponent :props="loading" />

    <div class="col-12">
        <div class="db-card">
            <div class="db-card-header border-none">
                <h3 class="db-card-title">{{ $t('menu.inventory') }}</h3>
                <div class="db-card-filter">
                    <TableLimitComponent :method="list" :search="props.search" :page="paginationPage" />
                    <FilterComponent @click.prevent="handleSlide('inventory-filter')" />
                    <div class="dropdown-group">
                        <ExportComponent />
                        <div class="dropdown-list db-card-filter-dropdown-list transition-all duration-300 scale-y-0 origin-top">
                            <PrintComponent :props="printObj" />
                        </div>
                    </div>
                    <InventoryCreateComponent v-if="permissionChecker('inventory_create')" :props="props" />
                </div>
            </div>

            <div id="inventory-filter" class="table-filter-div">
                <form class="p-4 sm:p-5 mb-5" @submit.prevent="search">
                    <div class="row">
                        <div class="col-12 sm:col-6 md:col-4 xl:col-3">
                            <label for="inventory-search-name" class="db-field-title after:hidden">{{ $t('label.name') }}</label>
                            <input id="inventory-search-name" v-model="props.search.name" type="text" class="db-field-control">
                        </div>
                        <div class="col-12 sm:col-6 md:col-4 xl:col-3">
                            <label for="inventory-search-sku" class="db-field-title after:hidden">{{ $t('label.sku') }}</label>
                            <input id="inventory-search-sku" v-model="props.search.sku" type="text" class="db-field-control">
                        </div>
                        <div class="col-12 sm:col-6 md:col-4 xl:col-3">
                            <label for="inventory-search-category" class="db-field-title after:hidden">{{ $t('label.category') }}</label>
                            <input id="inventory-search-category" v-model="props.search.category" type="text" class="db-field-control">
                        </div>
                        <div class="col-12 sm:col-6 md:col-4 xl:col-3">
                            <label for="inventory-search-status" class="db-field-title after:hidden">{{ $t('label.status') }}</label>
                            <vue-select id="inventory-search-status" v-model="props.search.status"
                                class="db-field-control f-b-custom-select"
                                :options="statusOptions" label-by="name" value-by="id"
                                :closeOnSelect="true" :searchable="false" :clearOnClose="true"
                                placeholder="--" />
                        </div>
                        <div class="col-12 sm:col-6 md:col-4 xl:col-3">
                            <label for="inventory-low-stock" class="db-field-title after:hidden">{{ $t('label.stock_level') }}</label>
                            <vue-select id="inventory-low-stock" v-model="props.search.low_stock"
                                class="db-field-control f-b-custom-select"
                                :options="stockOptions" label-by="name" value-by="id"
                                :closeOnSelect="true" :searchable="false" :clearOnClose="true"
                                placeholder="--" />
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
                            <th class="db-table-head-th">{{ $t('label.name') }}</th>
                            <th class="db-table-head-th">{{ $t('label.sku') }}</th>
                            <th class="db-table-head-th">{{ $t('label.category') }}</th>
                            <th class="db-table-head-th">{{ $t('label.current_stock') }}</th>
                            <th class="db-table-head-th">{{ $t('label.minimum_stock') }}</th>
                            <th class="db-table-head-th">{{ $t('label.unit_cost') }}</th>
                            <th class="db-table-head-th">{{ $t('label.status') }}</th>
                            <th v-if="hasActions" class="db-table-head-th hidden-print">{{ $t('label.action') }}</th>
                        </tr>
                    </thead>
                    <tbody v-if="inventoryItems.length" class="db-table-body">
                        <tr v-for="item in inventoryItems" :key="item.id" class="db-table-body-tr">
                            <td class="db-table-body-td">
                                <div class="font-medium">{{ item.name }}</div>
                                <span v-if="item.is_low_stock" class="db-table-badge text-red-600 bg-red-100 mt-1">
                                    {{ $t('label.low_stock') }}
                                </span>
                            </td>
                            <td class="db-table-body-td">{{ item.sku || '-' }}</td>
                            <td class="db-table-body-td">{{ item.category || '-' }}</td>
                            <td class="db-table-body-td">
                                <span :class="item.is_low_stock ? 'font-semibold text-red-600' : 'font-semibold text-green-600'">
                                    {{ cleanNumber(item.quantity) }} {{ item.unit }}
                                </span>
                            </td>
                            <td class="db-table-body-td">{{ cleanNumber(item.minimum_stock) }} {{ item.unit }}</td>
                            <td class="db-table-body-td">{{ item.unit_cost === null ? '-' : cleanNumber(item.unit_cost) }}</td>
                            <td class="db-table-body-td">
                                <span :class="statusClass(item.status)">{{ statusLabel(item.status) }}</span>
                            </td>
                            <td v-if="hasActions" class="db-table-body-td hidden-print">
                                <div class="flex justify-start items-center gap-1.5">
                                    <SmIconViewComponent v-if="permissionChecker('inventory_show')" link="admin.inventory.show" :id="item.id" />
                                    <SmIconSidebarModalEditComponent v-if="permissionChecker('inventory_edit')" @click="edit(item)" />
                                    <SmIconDeleteComponent v-if="permissionChecker('inventory_delete')" @click="destroy(item.id)" />
                                </div>
                            </td>
                        </tr>
                    </tbody>
                    <tbody v-else class="db-table-body">
                        <tr class="db-table-body-tr">
                            <td class="db-table-body-td text-center" colspan="8">
                                <div class="p-4">
                                    <div class="max-w-[300px] mx-auto mt-2">
                                        <img class="w-full h-full" :src="ENV.API_URL + '/images/default/not-found.png'" alt="Not Found">
                                    </div>
                                    <span class="d-block mt-3 text-lg">{{ $t('message.no_data_available') }}</span>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div v-if="inventoryItems.length" class="flex items-center justify-between border-t border-gray-200 bg-white px-4 py-6">
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
import InventoryCreateComponent from './InventoryCreateComponent';
import LoadingComponent from '../components/LoadingComponent';
import TableLimitComponent from '../components/TableLimitComponent';
import FilterComponent from '../components/buttons/collapse/FilterComponent';
import ExportComponent from '../components/buttons/export/ExportComponent';
import PrintComponent from '../components/buttons/export/PrintComponent';
import PaginationTextComponent from '../components/pagination/PaginationTextComponent';
import PaginationBox from '../components/pagination/PaginationBox';
import PaginationSMBox from '../components/pagination/PaginationSMBox';
import SmIconViewComponent from '../components/buttons/SmIconViewComponent';
import SmIconSidebarModalEditComponent from '../components/buttons/SmIconSidebarModalEditComponent';
import SmIconDeleteComponent from '../components/buttons/SmIconDeleteComponent';
import appService from '../../../services/appService';
import alertService from '../../../services/alertService';
import statusEnum from '../../../enums/modules/statusEnum';
import ENV from '../../../config/env';

export default {
    name: 'InventoryListComponent',
    components: {
        InventoryCreateComponent,
        LoadingComponent,
        TableLimitComponent,
        FilterComponent,
        ExportComponent,
        PrintComponent,
        PaginationTextComponent,
        PaginationBox,
        PaginationSMBox,
        SmIconViewComponent,
        SmIconSidebarModalEditComponent,
        SmIconDeleteComponent,
    },
    data() {
        return {
            loading: { isActive: false },
            ENV,
            printObj: { id: 'print', popTitle: this.$t('menu.inventory') },
            props: {
                form: this.emptyForm(),
                search: {
                    paginate: 1,
                    page: 1,
                    per_page: 10,
                    order_column: 'id',
                    order_type: 'desc',
                    name: '',
                    sku: '',
                    category: '',
                    status: null,
                    low_stock: null,
                },
            },
        };
    },
    computed: {
        inventoryItems() {
            return this.$store.getters['inventory/lists'];
        },
        pagination() {
            return this.$store.getters['inventory/pagination'];
        },
        paginationPage() {
            return this.$store.getters['inventory/page'];
        },
        statusOptions() {
            return [
                { id: statusEnum.ACTIVE, name: this.$t('label.active') },
                { id: statusEnum.INACTIVE, name: this.$t('label.inactive') },
            ];
        },
        stockOptions() {
            return [{ id: 1, name: this.$t('label.low_stock_only') }];
        },
        hasActions() {
            return this.permissionChecker('inventory_show')
                || this.permissionChecker('inventory_edit')
                || this.permissionChecker('inventory_delete');
        },
    },
    mounted() {
        this.list();
    },
    methods: {
        emptyForm() {
            return {
                name: '', sku: '', category: '', quantity: 0, unit: '', minimum_stock: 0,
                unit_cost: '', supplier: '', notes: '', status: statusEnum.ACTIVE,
            };
        },
        permissionChecker(permission) {
            return appService.permissionChecker(permission);
        },
        statusClass(status) {
            return appService.statusClass(status);
        },
        statusLabel(status) {
            return status === statusEnum.ACTIVE ? this.$t('label.active') : this.$t('label.inactive');
        },
        cleanNumber(value) {
            if (value === null || value === '') return '-';
            return Number(value).toLocaleString(undefined, { maximumFractionDigits: 6 });
        },
        handleSlide(id) {
            return appService.handleSlide(id);
        },
        list(page = 1) {
            this.loading.isActive = true;
            this.props.search.page = page;
            this.$store.dispatch('inventory/lists', this.props.search).then(() => {
                this.loading.isActive = false;
            }).catch(err => {
                this.loading.isActive = false;
                alertService.error(err.response?.data?.message || err.message);
            });
        },
        search() {
            this.list(1);
        },
        clear() {
            Object.assign(this.props.search, {
                page: 1, name: '', sku: '', category: '', status: null, low_stock: null,
            });
            this.list(1);
        },
        edit(item) {
            appService.sideDrawerShow();
            this.$store.dispatch('inventory/edit', item.id);
            this.props.form = {
                name: item.name,
                sku: item.sku,
                category: item.category,
                quantity: item.quantity,
                unit: item.unit,
                minimum_stock: item.minimum_stock,
                unit_cost: item.unit_cost === null ? '' : item.unit_cost,
                supplier: item.supplier,
                notes: item.notes,
                status: item.status,
            };
        },
        destroy(id) {
            appService.destroyConfirmation().then(() => {
                this.loading.isActive = true;
                this.$store.dispatch('inventory/destroy', { id, search: this.props.search }).then(() => {
                    this.loading.isActive = false;
                    alertService.successFlip(null, this.$t('menu.inventory'));
                }).catch(err => {
                    this.loading.isActive = false;
                    alertService.error(err.response?.data?.message || err.message);
                });
            });
        },
    },
};
</script>

<style scoped>
@media print {
    .hidden-print { display: none !important; }
}
</style>
