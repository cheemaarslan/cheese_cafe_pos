<template>
    <LoadingComponent :props="loading" />
    <div class="col-12">
        <div class="db-card">
            <div class="db-card-header border-none flex-wrap gap-3">
                <div>
                    <h3 class="db-card-title">{{ $t('menu.new_stock_count') }}</h3>
                    <p class="text-xs mt-1 text-[#6E7191]">{{ $t('message.stock_count_create_hint') }}</p>
                </div>
                <router-link :to="{ name: 'admin.stock-counts.list' }" class="db-btn py-2 text-white bg-primary">
                    <i class="lab lab-arrow-left"></i>
                    <span>{{ $t('button.back') }}</span>
                </router-link>
            </div>

            <div class="px-4 sm:px-5 pb-4 flex flex-wrap gap-3">
                <input v-model="filter" type="text" class="db-field-control w-full sm:w-64"
                    :placeholder="$t('label.search') + '...'">
                <input v-model="note" type="text" maxlength="500" class="db-field-control w-full sm:flex-1"
                    :placeholder="$t('label.note')">
            </div>

            <div class="db-table-responsive">
                <table class="db-table stripe">
                    <thead class="db-table-head">
                        <tr class="db-table-head-tr">
                            <th class="db-table-head-th">{{ $t('label.item') }}</th>
                            <th class="db-table-head-th">{{ $t('label.category') }}</th>
                            <th class="db-table-head-th">{{ $t('label.system_stock') }}</th>
                            <th class="db-table-head-th">{{ $t('label.counted_stock') }}</th>
                            <th class="db-table-head-th">{{ $t('label.difference') }}</th>
                            <th class="db-table-head-th">{{ $t('label.value') }}</th>
                        </tr>
                    </thead>
                    <tbody class="db-table-body">
                        <tr v-for="item in visibleItems" :key="item.id" class="db-table-body-tr">
                            <td class="db-table-body-td font-medium">{{ item.name }}</td>
                            <td class="db-table-body-td">{{ item.category || '-' }}</td>
                            <td class="db-table-body-td whitespace-nowrap">
                                {{ cleanNumber(item.purchase_quantity) }} {{ item.purchase_unit_label }}
                            </td>
                            <td class="db-table-body-td">
                                <div class="flex items-center gap-2">
                                    <input v-model="counted[item.id]" type="number" min="0" step="any"
                                        class="db-field-control w-28" :class="lineError(item.id) ? 'invalid' : ''">
                                    <span class="text-sm">{{ item.purchase_unit_label }}</span>
                                </div>
                                <small v-if="lineError(item.id)" class="db-field-alert">{{ lineError(item.id) }}</small>
                            </td>
                            <td class="db-table-body-td whitespace-nowrap font-semibold" :class="differenceClass(item)">
                                <template v-if="difference(item) !== null">
                                    {{ difference(item) > 0 ? '+' : '' }}{{ cleanNumber(difference(item)) }} {{ item.purchase_unit_label }}
                                </template>
                                <template v-else>-</template>
                            </td>
                            <td class="db-table-body-td whitespace-nowrap" :class="differenceClass(item)">
                                {{ value(item) === null ? '-' : money(value(item)) }}
                            </td>
                        </tr>
                        <tr v-if="!visibleItems.length" class="db-table-body-tr">
                            <td class="db-table-body-td text-center" colspan="6">{{ $t('message.no_data_available') }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="flex flex-wrap items-center justify-between gap-3 border-t border-gray-200 px-4 py-5">
                <div class="text-sm">
                    {{ $t('label.items_counted') }}: <b>{{ entered.length }}</b>
                    <span class="mx-1.5">·</span>
                    {{ $t('label.difference_value') }}:
                    <b :class="totalValue < 0 ? 'text-red-600' : (totalValue > 0 ? 'text-green-600' : '')">{{ money(totalValue) }}</b>
                </div>
                <div class="flex items-center gap-3">
                    <small v-if="errors.items" class="db-field-alert">{{ errors.items[0] }}</small>
                    <button type="button" class="db-btn py-2 text-white bg-primary" :disabled="!entered.length" @click="save">
                        <i class="lab lab-save"></i>
                        <span>{{ $t('button.save_stock_count') }}</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import LoadingComponent from "../components/LoadingComponent";
import appService from "../../../services/appService";
import alertService from "../../../services/alertService";
import inventoryUnitService from "../../../services/inventoryUnitService";

export default {
    name: 'StockCountCreateComponent',
    components: {
        LoadingComponent
    },
    data() {
        return {
            loading: {
                isActive: false
            },
            filter: '',
            note: '',
            // inventory item id => stock found, in the item's purchase unit
            counted: {},
            errors: {}
        }
    },
    computed: {
        items: function () {
            return this.$store.getters['stockCount/items'];
        },
        setting: function () {
            return this.$store.getters['frontendSetting/lists'] || {};
        },
        visibleItems: function () {
            const filter = this.filter.trim().toLowerCase();
            if (!filter) {
                return this.items;
            }
            return this.items.filter((item) => item.name.toLowerCase().includes(filter)
                || (item.category || '').toLowerCase().includes(filter));
        },
        // Items with a count typed in; the rest are left as they are
        entered: function () {
            return this.items.filter((item) => this.hasCount(item.id));
        },
        totalValue: function () {
            return this.entered.reduce((sum, item) => sum + (this.value(item) || 0), 0);
        }
    },
    mounted() {
        this.loading.isActive = true;
        this.$store.dispatch('stockCount/items').then(() => {
            this.loading.isActive = false;
        }).catch((err) => {
            this.loading.isActive = false;
            alertService.error(err.response?.data?.message || err.message);
        });
    },
    methods: {
        hasCount: function (id) {
            const value = this.counted[id];
            return value !== '' && value !== null && typeof value !== 'undefined' && !isNaN(Number(value));
        },
        difference: function (item) {
            if (!this.hasCount(item.id)) {
                return null;
            }
            return Number((Number(this.counted[item.id]) - Number(item.purchase_quantity)).toFixed(6));
        },
        value: function (item) {
            const difference = this.difference(item);
            if (difference === null || item.unit_cost === null) {
                return null;
            }
            return difference * inventoryUnitService.factor(item.purchase_unit) * Number(item.unit_cost);
        },
        differenceClass: function (item) {
            const difference = this.difference(item);
            return difference < 0 ? 'text-red-600' : (difference > 0 ? 'text-green-600' : '');
        },
        lineError: function (id) {
            const index = this.entered.findIndex((item) => item.id === id);
            const error = index === -1 ? null : this.errors[`items.${index}.counted`];
            return error ? error[0] : '';
        },
        cleanNumber: function (value) {
            return Number(value).toLocaleString(undefined, { maximumFractionDigits: 3 });
        },
        money: function (value) {
            return appService.currencyFormat(
                value || 0,
                this.setting.site_digit_after_decimal_point ?? 2,
                this.setting.site_default_currency_symbol,
                this.setting.site_currency_position
            );
        },
        save: function () {
            this.loading.isActive = true;
            this.$store.dispatch('stockCount/save', {
                note: this.note,
                items: this.entered.map((item) => ({ inventory_item_id: item.id, counted: this.counted[item.id] }))
            }).then((res) => {
                this.loading.isActive = false;
                alertService.successFlip(0, this.$t('menu.stock_counts'));
                this.$router.push({ name: 'admin.stock-counts.show', params: { id: res.data.data.id } });
            }).catch((err) => {
                this.loading.isActive = false;
                this.errors = err.response?.data?.errors || {};
                if (!err.response?.data?.errors) {
                    alertService.error(err.response?.data?.message || err.message);
                }
            });
        }
    }
}
</script>
