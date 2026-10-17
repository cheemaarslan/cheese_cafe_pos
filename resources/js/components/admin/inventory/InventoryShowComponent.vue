<template>
    <LoadingComponent :props="loading" />

    <div class="col-12">
        <div class="db-card p-4 sm:p-6">
            <div class="flex items-center justify-between mb-5">
                <div>
                    <h3 class="db-card-title">{{ item.name }}</h3>
                    <span v-if="item.is_low_stock" class="db-table-badge text-red-600 bg-red-100 mt-2">
                        {{ $t('label.low_stock') }}
                    </span>
                </div>
                <router-link :to="{ name: 'admin.inventory.list' }" class="db-btn py-2 text-white bg-primary">
                    <i class="lab lab-arrow-left"></i>
                    <span>{{ $t('button.back') }}</span>
                </router-link>
            </div>

            <div class="row py-2">
                <div v-for="field in fields" :key="field.label" class="col-12 sm:col-6 !py-1.5">
                    <div class="db-list-item p-0">
                        <span class="db-list-item-title w-full sm:w-1/2">{{ field.label }}</span>
                        <span class="db-list-item-text w-full sm:w-1/2">{{ field.value }}</span>
                    </div>
                </div>

                <div class="col-12 !py-1.5">
                    <div class="db-list-item p-0">
                        <span class="db-list-item-text mt-2 w-full">
                            <span class="db-list-item-title">{{ $t('label.notes') }}</span>
                            <br>
                            <span class="mt-2 whitespace-pre-line">{{ item.notes || '-' }}</span>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12">
        <div class="db-card">
            <div class="db-card-header border-none">
                <h3 class="db-card-title">{{ $t('label.stock_history') }}</h3>
            </div>
            <div class="db-table-responsive">
                <table class="db-table stripe">
                    <thead class="db-table-head">
                        <tr class="db-table-head-tr">
                            <th class="db-table-head-th">{{ $t('label.date') }}</th>
                            <th class="db-table-head-th">{{ $t('label.type') }}</th>
                            <th class="db-table-head-th">{{ $t('label.change') }}</th>
                            <th class="db-table-head-th">{{ $t('label.balance') }}</th>
                            <th class="db-table-head-th">{{ $t('label.note') }}</th>
                            <th class="db-table-head-th">{{ $t('label.by') }}</th>
                        </tr>
                    </thead>
                    <tbody class="db-table-body">
                        <tr v-for="transaction in transactions" :key="transaction.id" class="db-table-body-tr">
                            <td class="db-table-body-td whitespace-nowrap">{{ transaction.converted_created_at }}</td>
                            <td class="db-table-body-td whitespace-nowrap">{{ typeLabel(transaction.type) }}</td>
                            <td class="db-table-body-td whitespace-nowrap font-semibold"
                                :class="Number(transaction.quantity) < 0 ? 'text-red-600' : 'text-green-600'">
                                {{ Number(transaction.quantity) > 0 ? '+' : '' }}{{ number(transaction.quantity) }}
                                {{ transaction.unit }}
                            </td>
                            <td class="db-table-body-td whitespace-nowrap">
                                {{ number(transaction.balance_after) }} {{ transaction.unit }}
                            </td>
                            <td class="db-table-body-td">{{ transaction.note || '-' }}</td>
                            <td class="db-table-body-td">{{ transaction.created_by_name || '-' }}</td>
                        </tr>
                        <tr v-if="!transactions.length" class="db-table-body-tr">
                            <td class="db-table-body-td text-center" colspan="6">
                                {{ $t('message.no_data_available') }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div v-if="transactions.length"
                class="flex items-center justify-between border-t border-gray-200 bg-white px-4 py-6">
                <PaginationSMBox :pagination="transactionPagination" :method="transactionList" />
                <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
                    <PaginationTextComponent :props="{ page: transactionPage }" />
                    <PaginationBox :pagination="transactionPagination" :method="transactionList" />
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
import statusEnum from "../../../enums/modules/statusEnum";
import inventoryTransactionTypeEnum from "../../../enums/modules/inventoryTransactionTypeEnum";
import alertService from "../../../services/alertService";

export default {
    name: 'InventoryShowComponent',
    components: {
        LoadingComponent,
        PaginationTextComponent,
        PaginationBox,
        PaginationSMBox
    },
    data() {
        return {
            loading: {
                isActive: false
            }
        }
    },
    computed: {
        item: function () {
            return this.$store.getters['inventory/show'] || {};
        },
        transactions: function () {
            return this.$store.getters['inventory/transactions'];
        },
        transactionPagination: function () {
            return this.$store.getters['inventory/transactionPagination'];
        },
        transactionPage: function () {
            return this.$store.getters['inventory/transactionPage'];
        },
        fields: function () {
            const unit = this.item.purchase_unit_label || '';
            // e.g. "2.5 kg (2,500 g)": the base unit is what recipes and orders deduct in
            const stock = (value, baseValue) => this.item.purchase_unit && this.item.purchase_unit !== this.item.unit
                ? `${this.number(value)} ${unit} (${this.number(baseValue)} ${this.item.unit})`
                : `${this.number(value)} ${unit}`;

            return [
                { label: this.$t('label.sku'), value: this.item.sku || '-' },
                { label: this.$t('label.category'), value: this.item.category || '-' },
                { label: this.$t('label.current_stock'), value: stock(this.item.purchase_quantity, this.item.quantity) },
                {
                    label: this.$t('label.minimum_stock'),
                    value: stock(this.item.purchase_minimum_stock, this.item.minimum_stock)
                },
                {
                    label: this.$t('label.unit_cost'),
                    value: this.item.purchase_unit_cost === null || typeof this.item.purchase_unit_cost === 'undefined'
                        ? '-'
                        : `${this.number(this.item.purchase_unit_cost)} / ${unit}`
                },
                { label: this.$t('label.total_value'), value: this.number(this.item.total_value) },
                { label: this.$t('label.supplier'), value: this.item.supplier || '-' },
                {
                    label: this.$t('label.status'),
                    value: this.item.status === statusEnum.ACTIVE ? this.$t('label.active') : this.$t('label.inactive')
                }
            ];
        }
    },
    mounted() {
        this.loading.isActive = true;
        this.$store.dispatch('inventory/show', this.$route.params.id).finally(() => {
            this.loading.isActive = false;
        });
        this.transactionList();
    },
    methods: {
        number: function (value) {
            return value === null || value === '' || typeof value === 'undefined'
                ? '-'
                : Number(value).toLocaleString(undefined, { maximumFractionDigits: 6 });
        },
        typeLabel: function (type) {
            const labels = {
                [inventoryTransactionTypeEnum.OPENING]: 'inventory_opening',
                [inventoryTransactionTypeEnum.PURCHASE]: 'inventory_purchase',
                [inventoryTransactionTypeEnum.ADJUSTMENT]: 'inventory_adjustment',
                [inventoryTransactionTypeEnum.WASTAGE]: 'inventory_wastage',
                [inventoryTransactionTypeEnum.ORDER]: 'inventory_order',
                [inventoryTransactionTypeEnum.ORDER_UPDATE]: 'inventory_order_update',
                [inventoryTransactionTypeEnum.ORDER_RETURN]: 'inventory_order_return'
            };
            return labels[type] ? this.$t('label.' + labels[type]) : '-';
        },
        transactionList: function (page = 1) {
            this.$store.dispatch('inventory/transactions', {
                id: this.$route.params.id,
                search: { paginate: 1, page: page, per_page: 10 }
            }).catch((err) => {
                alertService.error(err.response?.data?.message || err.message);
            });
        }
    }
}
</script>
