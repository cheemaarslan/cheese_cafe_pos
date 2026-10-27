<template>
    <LoadingComponent :props="loading" />
    <div class="col-12">
        <div class="db-card">
            <div class="db-card-header border-none flex-wrap gap-3">
                <div>
                    <h3 class="db-card-title">{{ $t('menu.stock_counts') }} #{{ count.id }}</h3>
                    <p class="text-sm mt-1 text-[#6E7191]">
                        {{ count.converted_created_at }}
                        <template v-if="count.created_by_name"> · {{ count.created_by_name }}</template>
                    </p>
                    <p v-if="count.note" class="text-sm mt-1">{{ count.note }}</p>
                </div>
                <router-link :to="{ name: 'admin.stock-counts.list' }" class="db-btn py-2 text-white bg-primary">
                    <i class="lab lab-arrow-left"></i>
                    <span>{{ $t('button.back') }}</span>
                </router-link>
            </div>

            <div class="px-4 sm:px-5 pb-4 text-sm">
                {{ $t('label.items_counted') }}: <b>{{ count.items_count }}</b>
                <span class="mx-1.5">·</span>
                {{ $t('label.items_different') }}: <b>{{ count.changed_items_count }}</b>
                <span class="mx-1.5">·</span>
                {{ $t('label.difference_value') }}:
                <b :class="count.difference_value < 0 ? 'text-red-600' : (count.difference_value > 0 ? 'text-green-600' : '')">
                    {{ count.difference_value_currency_price }}
                </b>
            </div>

            <div class="db-table-responsive">
                <table class="db-table stripe">
                    <thead class="db-table-head">
                        <tr class="db-table-head-tr">
                            <th class="db-table-head-th">{{ $t('label.item') }}</th>
                            <th class="db-table-head-th">{{ $t('label.system_stock') }}</th>
                            <th class="db-table-head-th">{{ $t('label.counted_stock') }}</th>
                            <th class="db-table-head-th">{{ $t('label.difference') }}</th>
                            <th class="db-table-head-th">{{ $t('label.value') }}</th>
                        </tr>
                    </thead>
                    <tbody class="db-table-body">
                        <tr v-for="line in count.items || []" :key="line.id" class="db-table-body-tr">
                            <td class="db-table-body-td font-medium">{{ line.item_name }}</td>
                            <td class="db-table-body-td whitespace-nowrap">{{ cleanNumber(line.system_quantity) }} {{ line.unit }}</td>
                            <td class="db-table-body-td whitespace-nowrap">{{ cleanNumber(line.counted_quantity) }} {{ line.unit }}</td>
                            <td class="db-table-body-td whitespace-nowrap font-semibold"
                                :class="line.difference < 0 ? 'text-red-600' : (line.difference > 0 ? 'text-green-600' : '')">
                                {{ line.difference > 0 ? '+' : '' }}{{ cleanNumber(line.difference) }} {{ line.unit }}
                            </td>
                            <td class="db-table-body-td whitespace-nowrap"
                                :class="line.value < 0 ? 'text-red-600' : (line.value > 0 ? 'text-green-600' : '')">
                                {{ line.value_currency_price || '-' }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>

<script>
import LoadingComponent from "../components/LoadingComponent";
import alertService from "../../../services/alertService";

export default {
    name: 'StockCountShowComponent',
    components: {
        LoadingComponent
    },
    data() {
        return {
            loading: {
                isActive: false
            }
        }
    },
    computed: {
        count: function () {
            return this.$store.getters['stockCount/show'] || {};
        }
    },
    mounted() {
        this.loading.isActive = true;
        this.$store.dispatch('stockCount/show', this.$route.params.id).then(() => {
            this.loading.isActive = false;
        }).catch((err) => {
            this.loading.isActive = false;
            alertService.error(err.response?.data?.message || err.message);
        });
    },
    methods: {
        cleanNumber: function (value) {
            return Number(value).toLocaleString(undefined, { maximumFractionDigits: 3 });
        }
    }
}
</script>
