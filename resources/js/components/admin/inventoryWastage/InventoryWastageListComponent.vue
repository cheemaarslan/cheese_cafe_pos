<template>
    <LoadingComponent :props="loading" />
    <div class="col-12">
        <div class="db-card">
            <div class="db-card-header border-none flex-wrap gap-3">
                <h3 class="db-card-title">{{ $t('menu.inventory_wastage') }}</h3>
                <div class="flex flex-wrap items-center gap-3">
                    <div class="w-full sm:w-64">
                        <Datepicker hideInputIcon autoApply :enableTimePicker="false" :utc="false" range
                            v-model="dateRange" :preset-ranges="presetRanges" :placeholder="$t('label.date')"
                            @update:modelValue="handleDate" />
                    </div>
                    <select v-model="search.inventory_item_id" class="db-field-control f-b-custom-select w-full sm:w-52"
                        @change="list(1)">
                        <option :value="null">{{ $t('label.all_items') }}</option>
                        <option v-for="item in items" :key="item.id" :value="item.id">{{ item.name }}</option>
                    </select>
                    <InventoryWastageCreateComponent v-if="permissionChecker('inventory_wastage_create')"
                        :search="search" />
                </div>
            </div>

            <div class="db-table-responsive">
                <table class="db-table stripe">
                    <thead class="db-table-head">
                        <tr class="db-table-head-tr">
                            <th class="db-table-head-th">{{ $t('label.date') }}</th>
                            <th class="db-table-head-th">{{ $t('label.item') }}</th>
                            <th class="db-table-head-th">{{ $t('label.quantity') }}</th>
                            <th class="db-table-head-th">{{ $t('label.reason') }}</th>
                            <th class="db-table-head-th">{{ $t('label.note') }}</th>
                            <th class="db-table-head-th">{{ $t('label.value') }}</th>
                            <th class="db-table-head-th">{{ $t('label.by') }}</th>
                            <th v-if="permissionChecker('inventory_wastage_delete')" class="db-table-head-th">
                                {{ $t('label.action') }}
                            </th>
                        </tr>
                    </thead>
                    <tbody class="db-table-body">
                        <tr v-for="wastage in wastages" :key="wastage.id" class="db-table-body-tr">
                            <td class="db-table-body-td whitespace-nowrap">{{ wastage.converted_created_at }}</td>
                            <td class="db-table-body-td">{{ wastage.item_name }}</td>
                            <td class="db-table-body-td whitespace-nowrap">
                                {{ cleanNumber(wastage.purchase_quantity) }} {{ wastage.purchase_unit_label }}
                                <span v-if="wastage.purchase_unit_label !== wastage.unit" class="text-gray-500">
                                    ({{ cleanNumber(wastage.quantity) }} {{ wastage.unit }})
                                </span>
                            </td>
                            <td class="db-table-body-td whitespace-nowrap">{{ reasonLabel(wastage.reason) }}</td>
                            <td class="db-table-body-td">{{ wastage.note || '-' }}</td>
                            <td class="db-table-body-td whitespace-nowrap">{{ wastage.value_currency_price || '-' }}</td>
                            <td class="db-table-body-td">{{ wastage.created_by_name || '-' }}</td>
                            <td v-if="permissionChecker('inventory_wastage_delete')" class="db-table-body-td">
                                <SmIconDeleteComponent @click="destroy(wastage.id)" />
                            </td>
                        </tr>
                        <tr v-if="!wastages.length" class="db-table-body-tr">
                            <td class="db-table-body-td text-center" colspan="8">
                                {{ $t('message.no_data_available') }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div v-if="wastages.length"
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
import SmIconDeleteComponent from "../components/buttons/SmIconDeleteComponent";
import PaginationTextComponent from "../components/pagination/PaginationTextComponent";
import PaginationBox from "../components/pagination/PaginationBox";
import PaginationSMBox from "../components/pagination/PaginationSMBox";
import InventoryWastageCreateComponent from "./InventoryWastageCreateComponent";
import Datepicker from "@vuepic/vue-datepicker";
import "@vuepic/vue-datepicker/dist/main.css";
import { endOfMonth, startOfMonth, subMonths } from "date-fns";
import wastageReasonEnum from "../../../enums/modules/wastageReasonEnum";
import appService from "../../../services/appService";
import alertService from "../../../services/alertService";

export default {
    name: 'InventoryWastageListComponent',
    components: {
        LoadingComponent,
        SmIconDeleteComponent,
        PaginationTextComponent,
        PaginationBox,
        PaginationSMBox,
        InventoryWastageCreateComponent,
        Datepicker
    },
    data() {
        return {
            loading: {
                isActive: false
            },
            dateRange: null,
            presetRanges: [
                { label: this.$t('label.preset_today'), range: [new Date(), new Date()] },
                { label: this.$t('label.this_month'), range: [startOfMonth(new Date()), endOfMonth(new Date())] },
                {
                    label: this.$t('label.last_month'),
                    range: [startOfMonth(subMonths(new Date(), 1)), endOfMonth(subMonths(new Date(), 1))]
                }
            ],
            search: {
                paginate: 1,
                page: 1,
                per_page: 10,
                from_date: '',
                to_date: '',
                inventory_item_id: null
            }
        }
    },
    computed: {
        wastages: function () {
            return this.$store.getters['inventoryWastage/lists'];
        },
        pagination: function () {
            return this.$store.getters['inventoryWastage/pagination'];
        },
        paginationPage: function () {
            return this.$store.getters['inventoryWastage/page'];
        },
        items: function () {
            return this.$store.getters['inventoryWastage/items'];
        }
    },
    mounted() {
        this.list();
        this.$store.dispatch('inventoryWastage/items').catch((err) => {
            alertService.error(err.response?.data?.message || err.message);
        });
    },
    methods: {
        permissionChecker: function (permission) {
            return appService.permissionChecker(permission);
        },
        cleanNumber: function (value) {
            return Number(value).toLocaleString(undefined, { maximumFractionDigits: 3 });
        },
        reasonLabel: function (reason) {
            const labels = {
                [wastageReasonEnum.SPOILED]: 'wastage_spoiled',
                [wastageReasonEnum.DAMAGED]: 'wastage_damaged',
                [wastageReasonEnum.KITCHEN_ERROR]: 'wastage_kitchen_error',
                [wastageReasonEnum.OTHER]: 'wastage_other'
            };
            return labels[reason] ? this.$t('label.' + labels[reason]) : '-';
        },
        // The API expects plain YYYY-MM-DD dates (local, not UTC); clearing the picker shows every date.
        handleDate: function (range) {
            this.search.from_date = range ? range[0].toLocaleDateString('en-CA') : '';
            this.search.to_date = range ? (range[1] || range[0]).toLocaleDateString('en-CA') : '';
            this.list(1);
        },
        list: function (page = 1) {
            this.loading.isActive = true;
            this.search.page = page;
            this.$store.dispatch('inventoryWastage/lists', this.search).then(() => {
                this.loading.isActive = false;
            }).catch((err) => {
                this.loading.isActive = false;
                alertService.error(err.response?.data?.message || err.message);
            });
        },
        destroy: function (id) {
            appService.destroyConfirmation().then(() => {
                this.loading.isActive = true;
                this.$store.dispatch('inventoryWastage/destroy', { id: id, search: this.search }).then(() => {
                    this.loading.isActive = false;
                    alertService.successFlip(null, this.$t('menu.inventory_wastage'));
                }).catch((err) => {
                    this.loading.isActive = false;
                    alertService.error(err.response?.data?.message || err.message);
                });
            }).catch(() => {});
        }
    }
}
</script>
