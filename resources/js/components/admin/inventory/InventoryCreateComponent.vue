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
                            <input id="inventory-name" v-model="props.form.name" :class="errors.name ? 'invalid' : ''"
                                class="db-field-control" type="text">
                            <small v-if="errors.name" class="db-field-alert">{{ errors.name[0] }}</small>
                        </div>

                        <div class="form-col-12 sm:form-col-6">
                            <label for="inventory-sku" class="db-field-title">{{ $t('label.sku') }}</label>
                            <input id="inventory-sku" v-model="props.form.sku" :class="errors.sku ? 'invalid' : ''"
                                class="db-field-control" type="text">
                            <small v-if="errors.sku" class="db-field-alert">{{ errors.sku[0] }}</small>
                        </div>

                        <div class="form-col-12 sm:form-col-6">
                            <label for="inventory-category" class="db-field-title">{{ $t('label.category') }}</label>
                            <input id="inventory-category" v-model="props.form.category"
                                :class="errors.category ? 'invalid' : ''" class="db-field-control" type="text"
                                :placeholder="$t('placeholder.inventory_category')">
                            <small v-if="errors.category" class="db-field-alert">{{ errors.category[0] }}</small>
                        </div>

                        <div class="form-col-12 sm:form-col-6">
                            <label for="inventory-unit" class="db-field-title required">{{ $t('label.unit') }}</label>
                            <select id="inventory-unit" v-model="purchaseUnit" :class="errors.purchase_unit ? 'invalid' : ''"
                                class="db-field-control f-b-custom-select">
                                <option value="" disabled>--</option>
                                <option v-for="unit in unitOptions" :key="unit.id" :value="unit.id">{{ unit.name }}</option>
                            </select>
                            <small v-if="errors.purchase_unit" class="db-field-alert">{{ errors.purchase_unit[0] }}</small>
                            <small v-else-if="unitHint" class="block mt-1 text-xs text-gray-500">{{ unitHint }}</small>
                        </div>

                        <div v-if="stockIsTotal" class="form-col-12 sm:form-col-6">
                            <label for="inventory-quantity" class="db-field-title required">
                                {{ $t('label.stock_quantity') }}<span v-if="unitLabel"> ({{ unitLabel }})</span>
                            </label>
                            <input id="inventory-quantity" v-model="props.form.quantity"
                                :class="errors.quantity ? 'invalid' : ''" class="db-field-control" type="number" min="0"
                                step="any">
                            <small v-if="errors.quantity" class="db-field-alert">{{ errors.quantity[0] }}</small>
                            <small v-else-if="isEditing" class="block mt-1 text-xs text-gray-500">
                                {{ $t('message.inventory_unit_changed_hint') }}
                            </small>
                        </div>

                        <div v-else class="form-col-12 sm:form-col-6">
                            <label for="inventory-add-quantity" class="db-field-title">
                                {{ $t('label.add_stock') }}<span v-if="unitLabel"> ({{ unitLabel }})</span>
                            </label>
                            <input id="inventory-add-quantity" v-model="props.form.add_quantity"
                                :class="errors.add_quantity ? 'invalid' : ''" class="db-field-control" type="number"
                                step="any" placeholder="0">
                            <small v-if="errors.add_quantity" class="db-field-alert">{{ errors.add_quantity[0] }}</small>
                            <small class="block mt-1 text-xs text-gray-500">
                                {{ $t('label.current_stock') }}:
                                <span :class="currentStock < 0 ? 'font-semibold text-red-600' : 'font-semibold'">
                                    {{ cleanNumber(currentStock) }} {{ unitLabel }}
                                </span>
                                <template v-if="newStock !== null">
                                    → {{ $t('label.new_stock') }}:
                                    <span class="font-semibold">{{ cleanNumber(newStock) }} {{ unitLabel }}</span>
                                </template>
                                <br>{{ $t('message.inventory_add_stock_hint') }}
                            </small>
                        </div>

                        <div class="form-col-12 sm:form-col-6">
                            <label for="inventory-minimum" class="db-field-title required">
                                {{ $t('label.minimum_stock') }}<span v-if="unitLabel"> ({{ unitLabel }})</span>
                            </label>
                            <input id="inventory-minimum" v-model="props.form.minimum_stock"
                                :class="errors.minimum_stock ? 'invalid' : ''" class="db-field-control" type="number"
                                min="0" step="any">
                            <small v-if="errors.minimum_stock" class="db-field-alert">
                                {{ errors.minimum_stock[0] }}
                            </small>
                        </div>

                        <div class="form-col-12 sm:form-col-6">
                            <label for="inventory-cost" class="db-field-title">
                                {{ $t('label.unit_cost') }}<span v-if="unitLabel"> ({{ $t('label.per_unit', { unit: unitLabel }) }})</span>
                            </label>
                            <input id="inventory-cost" v-model="props.form.unit_cost"
                                :class="errors.unit_cost ? 'invalid' : ''" class="db-field-control" type="number"
                                min="0" step="0.000001">
                            <small v-if="errors.unit_cost" class="db-field-alert">{{ errors.unit_cost[0] }}</small>
                        </div>

                        <div class="form-col-12 sm:form-col-6">
                            <label for="inventory-supplier" class="db-field-title">{{ $t('label.supplier') }}</label>
                            <input id="inventory-supplier" v-model="props.form.supplier"
                                :class="errors.supplier ? 'invalid' : ''" class="db-field-control" type="text">
                            <small v-if="errors.supplier" class="db-field-alert">{{ errors.supplier[0] }}</small>
                        </div>

                        <div class="form-col-12">
                            <label class="db-field-title required">{{ $t('label.status') }}</label>
                            <div class="db-field-radio-group">
                                <div class="db-field-radio">
                                    <div class="custom-radio">
                                        <input id="inventory-active" v-model="props.form.status"
                                            :value="enums.statusEnum.ACTIVE" type="radio" class="custom-radio-field">
                                        <span class="custom-radio-span"></span>
                                    </div>
                                    <label for="inventory-active" class="db-field-label">{{ $t('label.active') }}</label>
                                </div>
                                <div class="db-field-radio">
                                    <div class="custom-radio">
                                        <input id="inventory-inactive" v-model="props.form.status"
                                            :value="enums.statusEnum.INACTIVE" type="radio" class="custom-radio-field">
                                        <span class="custom-radio-span"></span>
                                    </div>
                                    <label for="inventory-inactive" class="db-field-label">
                                        {{ $t('label.inactive') }}
                                    </label>
                                </div>
                            </div>
                            <small v-if="errors.status" class="db-field-alert">{{ errors.status[0] }}</small>
                        </div>

                        <div class="form-col-12">
                            <label for="inventory-notes" class="db-field-title">{{ $t('label.notes') }}</label>
                            <textarea id="inventory-notes" v-model="props.form.notes"
                                :class="errors.notes ? 'invalid' : ''" class="db-field-control" rows="3"></textarea>
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
import SmSidebarModalCreateComponent from "../components/buttons/SmSidebarModalCreateComponent";
import LoadingComponent from "../components/LoadingComponent";
import statusEnum from "../../../enums/modules/statusEnum";
import alertService from "../../../services/alertService";
import appService from "../../../services/appService";
import inventoryUnitService from "../../../services/inventoryUnitService";

export default {
    name: 'InventoryCreateComponent',
    components: {
        SmSidebarModalCreateComponent,
        LoadingComponent
    },
    props: ['props'],
    data() {
        return {
            loading: {
                isActive: false
            },
            errors: {},
            enums: {
                statusEnum: statusEnum
            }
        }
    },
    computed: {
        addButton: function () {
            return { title: this.$t('button.add_inventory_item') };
        },
        unitOptions: function () {
            return inventoryUnitService.units.map((unit) => ({ id: unit, name: this.$t('label.unit_' + unit) }));
        },
        purchaseUnit: {
            get: function () {
                return this.props.form.purchase_unit;
            },
            set: function (unit) {
                this.changeUnit(unit);
            }
        },
        unitLabel: function () {
            return inventoryUnitService.label(this.props.form.purchase_unit);
        },
        isEditing: function () {
            return this.$store.getters['inventory/temp'].isEditing;
        },
        // A new item, or one switched to another kind of unit (pcs to litre), is given its stock as a total;
        // otherwise stock bought is added to the current stock
        stockIsTotal: function () {
            return !this.isEditing
                || inventoryUnitService.baseUnit(this.props.form.purchase_unit) !== this.props.form.original_unit;
        },
        currentStock: function () {
            return Number(this.props.form.current_quantity || 0)
                / inventoryUnitService.factor(this.props.form.purchase_unit);
        },
        newStock: function () {
            const added = this.props.form.add_quantity;
            if (added === '' || added === null || typeof added === 'undefined' || isNaN(Number(added))
                || Number(added) === 0) {
                return null;
            }
            return Number((this.currentStock + Number(added)).toFixed(6));
        },
        unitHint: function () {
            const unit = this.props.form.purchase_unit;
            const factor = inventoryUnitService.factor(unit);
            if (!unit || factor === 1) {
                return '';
            }
            return this.$t('message.inventory_unit_hint', {
                unit: inventoryUnitService.label(unit),
                factor: factor,
                base: inventoryUnitService.label(inventoryUnitService.baseUnit(unit))
            });
        }
    },
    methods: {
        changeUnit: function (unit) {
            const form = this.props.form;
            const from = form.purchase_unit;

            // Same kind of unit (e.g. kg to g): convert what's already typed so it still means the same stock
            if (from && unit && from !== unit
                && inventoryUnitService.baseUnit(from) === inventoryUnitService.baseUnit(unit)) {
                const ratio = inventoryUnitService.factor(from) / inventoryUnitService.factor(unit);
                const scale = (value, by) => value === '' || value === null || isNaN(Number(value))
                    ? value
                    : Number((Number(value) * by).toFixed(6));

                form.quantity = scale(form.quantity, ratio);
                form.add_quantity = scale(form.add_quantity, ratio);
                form.minimum_stock = scale(form.minimum_stock, ratio);
                form.unit_cost = scale(form.unit_cost, 1 / ratio);
            }

            form.purchase_unit = unit;
        },
        cleanNumber: function (value) {
            return Number(value).toLocaleString(undefined, { maximumFractionDigits: 6 });
        },
        emptyForm: function () {
            return {
                name: '',
                sku: '',
                category: '',
                quantity: 0,
                add_quantity: '',
                purchase_unit: '',
                minimum_stock: 0,
                unit_cost: '',
                supplier: '',
                notes: '',
                status: statusEnum.ACTIVE
            };
        },
        reset: function () {
            appService.sideDrawerHide();
            this.$store.dispatch('inventory/reset');
            this.errors = {};
            this.props.form = this.emptyForm();
        },
        addReset: function () {
            this.$store.dispatch('inventory/reset');
            this.errors = {};
            this.props.form = this.emptyForm();
        },
        save: function () {
            const tempId = this.$store.getters['inventory/temp'].temp_id;
            this.loading.isActive = true;
            this.$store.dispatch('inventory/save', this.props).then(() => {
                appService.sideDrawerHide();
                this.loading.isActive = false;
                alertService.successFlip(tempId === null ? 0 : 1, this.$t('menu.inventory'));
                this.props.form = this.emptyForm();
                this.errors = {};
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
