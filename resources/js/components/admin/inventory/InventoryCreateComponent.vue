<template>
    <LoadingComponent :props="loading" />
    <SmSidebarModalCreateComponent :props="addButton" @click="addReset" />

    <div id="sidebar" class="drawer">
        <div class="drawer-dialog">
            <div class="drawer-header">
                <h3 class="drawer-title">{{ $t('menu.inventory') }}</h3>
                <button class="fa-solid fa-xmark close-btn" type="button" @click="reset"></button>
            </div>
            <div class="drawer-body">
                <form @submit.prevent="save">
                    <div class="form-row">
                        <div class="form-col-12 sm:form-col-6">
                            <label for="inventory-name" class="db-field-title required">{{ $t('label.name') }}</label>
                            <input id="inventory-name" v-model="props.form.name" :class="errors.name ? 'invalid' : ''" type="text" class="db-field-control">
                            <small v-if="errors.name" class="db-field-alert">{{ errors.name[0] }}</small>
                        </div>

                        <div class="form-col-12 sm:form-col-6">
                            <label for="inventory-sku" class="db-field-title">{{ $t('label.sku') }}</label>
                            <input id="inventory-sku" v-model="props.form.sku" :class="errors.sku ? 'invalid' : ''" type="text" class="db-field-control">
                            <small v-if="errors.sku" class="db-field-alert">{{ errors.sku[0] }}</small>
                        </div>

                        <div class="form-col-12 sm:form-col-6">
                            <label for="inventory-category" class="db-field-title">{{ $t('label.category') }}</label>
                            <input id="inventory-category" v-model="props.form.category" :class="errors.category ? 'invalid' : ''" type="text" class="db-field-control" :placeholder="$t('placeholder.inventory_category')">
                            <small v-if="errors.category" class="db-field-alert">{{ errors.category[0] }}</small>
                        </div>

                        <div class="form-col-12 sm:form-col-6">
                            <label for="inventory-unit" class="db-field-title required">{{ $t('label.unit') }}</label>
                            <input id="inventory-unit" v-model="props.form.unit" :class="errors.unit ? 'invalid' : ''" type="text" class="db-field-control" :placeholder="$t('placeholder.inventory_unit')">
                            <small v-if="errors.unit" class="db-field-alert">{{ errors.unit[0] }}</small>
                        </div>

                        <div class="form-col-12 sm:form-col-6">
                            <label for="inventory-quantity" class="db-field-title required">{{ $t('label.stock_quantity') }}</label>
                            <input id="inventory-quantity" v-model="props.form.quantity" :class="errors.quantity ? 'invalid' : ''" type="number" min="0" step="0.001" class="db-field-control">
                            <small v-if="errors.quantity" class="db-field-alert">{{ errors.quantity[0] }}</small>
                        </div>

                        <div class="form-col-12 sm:form-col-6">
                            <label for="inventory-minimum" class="db-field-title required">{{ $t('label.minimum_stock') }}</label>
                            <input id="inventory-minimum" v-model="props.form.minimum_stock" :class="errors.minimum_stock ? 'invalid' : ''" type="number" min="0" step="0.001" class="db-field-control">
                            <small v-if="errors.minimum_stock" class="db-field-alert">{{ errors.minimum_stock[0] }}</small>
                        </div>

                        <div class="form-col-12 sm:form-col-6">
                            <label for="inventory-cost" class="db-field-title">{{ $t('label.unit_cost') }}</label>
                            <input id="inventory-cost" v-model="props.form.unit_cost" :class="errors.unit_cost ? 'invalid' : ''" type="number" min="0" step="0.000001" class="db-field-control">
                            <small v-if="errors.unit_cost" class="db-field-alert">{{ errors.unit_cost[0] }}</small>
                        </div>

                        <div class="form-col-12 sm:form-col-6">
                            <label for="inventory-supplier" class="db-field-title">{{ $t('label.supplier') }}</label>
                            <input id="inventory-supplier" v-model="props.form.supplier" :class="errors.supplier ? 'invalid' : ''" type="text" class="db-field-control">
                            <small v-if="errors.supplier" class="db-field-alert">{{ errors.supplier[0] }}</small>
                        </div>

                        <div class="form-col-12">
                            <label class="db-field-title required">{{ $t('label.status') }}</label>
                            <div class="db-field-radio-group">
                                <div class="db-field-radio">
                                    <div class="custom-radio">
                                        <input id="inventory-active" v-model="props.form.status" :value="enums.statusEnum.ACTIVE" type="radio" class="custom-radio-field">
                                        <span class="custom-radio-span"></span>
                                    </div>
                                    <label for="inventory-active" class="db-field-label">{{ $t('label.active') }}</label>
                                </div>
                                <div class="db-field-radio">
                                    <div class="custom-radio">
                                        <input id="inventory-inactive" v-model="props.form.status" :value="enums.statusEnum.INACTIVE" type="radio" class="custom-radio-field">
                                        <span class="custom-radio-span"></span>
                                    </div>
                                    <label for="inventory-inactive" class="db-field-label">{{ $t('label.inactive') }}</label>
                                </div>
                            </div>
                            <small v-if="errors.status" class="db-field-alert">{{ errors.status[0] }}</small>
                        </div>

                        <div class="form-col-12">
                            <label for="inventory-notes" class="db-field-title">{{ $t('label.notes') }}</label>
                            <textarea id="inventory-notes" v-model="props.form.notes" :class="errors.notes ? 'invalid' : ''" rows="3" class="db-field-control"></textarea>
                            <small v-if="errors.notes" class="db-field-alert">{{ errors.notes[0] }}</small>
                        </div>

                        <div class="col-12">
                            <div class="flex flex-wrap gap-3 mt-4">
                                <button type="submit" class="db-btn py-2 text-white bg-primary">
                                    <i class="lab lab-save"></i>
                                    <span>{{ $t('label.save') }}</span>
                                </button>
                                <button type="button" class="modal-btn-outline modal-close" @click="reset">
                                    <i class="lab lab-close"></i>
                                    <span>{{ $t('button.close') }}</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script>
import SmSidebarModalCreateComponent from '../components/buttons/SmSidebarModalCreateComponent';
import LoadingComponent from '../components/LoadingComponent';
import statusEnum from '../../../enums/modules/statusEnum';
import alertService from '../../../services/alertService';
import appService from '../../../services/appService';

export default {
    name: 'InventoryCreateComponent',
    components: { SmSidebarModalCreateComponent, LoadingComponent },
    props: ['props'],
    data() {
        return {
            loading: { isActive: false },
            errors: {},
            enums: { statusEnum },
        };
    },
    computed: {
        addButton() {
            return { title: this.$t('button.add_inventory_item') };
        },
    },
    methods: {
        emptyForm() {
            return {
                name: '',
                sku: '',
                category: '',
                quantity: 0,
                unit: '',
                minimum_stock: 0,
                unit_cost: '',
                supplier: '',
                notes: '',
                status: statusEnum.ACTIVE,
            };
        },
        reset() {
            appService.sideDrawerHide();
            this.$store.dispatch('inventory/reset');
            this.errors = {};
            this.props.form = this.emptyForm();
        },
        addReset() {
            this.$store.dispatch('inventory/reset');
            this.errors = {};
            this.props.form = this.emptyForm();
        },
        save() {
            const tempId = this.$store.getters['inventory/temp'].temp_id;
            this.loading.isActive = true;
            this.$store.dispatch('inventory/save', this.props).then(() => {
                appService.sideDrawerHide();
                this.loading.isActive = false;
                alertService.successFlip(tempId === null ? 0 : 1, this.$t('menu.inventory'));
                this.props.form = this.emptyForm();
                this.errors = {};
            }).catch(err => {
                this.loading.isActive = false;
                this.errors = err.response?.data?.errors || {};
                if (!err.response?.data?.errors) {
                    alertService.error(err.response?.data?.message || err.message);
                }
            });
        },
    },
};
</script>
