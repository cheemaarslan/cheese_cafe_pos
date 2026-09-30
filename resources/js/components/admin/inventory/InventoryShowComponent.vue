<template>
    <LoadingComponent :props="loading" />
    <div class="col-12">
        <div class="db-card p-4 sm:p-6">
            <div class="flex items-center justify-between mb-5">
                <div>
                    <h3 class="db-card-title">{{ item.name }}</h3>
                    <span v-if="item.is_low_stock" class="db-table-badge text-red-600 bg-red-100 mt-2">{{ $t('label.low_stock') }}</span>
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
                            <span class="db-list-item-title">{{ $t('label.notes') }}</span><br>
                            <span class="mt-2">{{ item.notes || '-' }}</span>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import LoadingComponent from '../components/LoadingComponent';
import statusEnum from '../../../enums/modules/statusEnum';

export default {
    name: 'InventoryShowComponent',
    components: { LoadingComponent },
    data() {
        return { loading: { isActive: false } };
    },
    computed: {
        item() {
            return this.$store.getters['inventory/show'] || {};
        },
        fields() {
            const number = value => value === null || value === '' || typeof value === 'undefined'
                ? '-'
                : Number(value).toLocaleString(undefined, { maximumFractionDigits: 6 });
            return [
                { label: this.$t('label.sku'), value: this.item.sku || '-' },
                { label: this.$t('label.category'), value: this.item.category || '-' },
                { label: this.$t('label.current_stock'), value: `${number(this.item.quantity)} ${this.item.unit || ''}` },
                { label: this.$t('label.minimum_stock'), value: `${number(this.item.minimum_stock)} ${this.item.unit || ''}` },
                { label: this.$t('label.unit_cost'), value: number(this.item.unit_cost) },
                { label: this.$t('label.total_value'), value: number(this.item.total_value) },
                { label: this.$t('label.supplier'), value: this.item.supplier || '-' },
                { label: this.$t('label.status'), value: this.item.status === statusEnum.ACTIVE ? this.$t('label.active') : this.$t('label.inactive') },
            ];
        },
    },
    mounted() {
        this.loading.isActive = true;
        this.$store.dispatch('inventory/show', this.$route.params.id).finally(() => {
            this.loading.isActive = false;
        });
    },
};
</script>
