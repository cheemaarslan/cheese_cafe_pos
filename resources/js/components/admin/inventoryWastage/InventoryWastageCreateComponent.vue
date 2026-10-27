<template>
    <LoadingComponent :props="loading" />
    <SmSidebarModalCreateComponent :props="addButton" @click="addReset" />

    <div id="sidebar" class="drawer">
        <div class="drawer-dialog">
            <div class="drawer-header">
                <h3 class="drawer-title">{{ $t('menu.inventory_wastage') }}</h3>
                <button class="fa-solid fa-xmark close-btn" type="button" @click="reset"></button>
            </div>
            <div class="drawer-body">
                <form @submit.prevent="save">
                    <div class="form-row">
                        <div class="form-col-12">
                            <label for="wastage-item" class="db-field-title required">{{ $t('label.item') }}</label>
                            <select id="wastage-item" v-model="itemId" class="db-field-control f-b-custom-select"
                                :class="errors.inventory_item_id ? 'invalid' : ''">
                                <option :value="null" disabled>--</option>
                                <option v-for="item in items" :key="item.id" :value="item.id">
                                    {{ item.name }} ({{ cleanNumber(item.purchase_quantity) }} {{ item.purchase_unit_label }})
                                </option>
                            </select>
                            <small v-if="errors.inventory_item_id" class="db-field-alert">{{ errors.inventory_item_id[0] }}</small>
                            <small v-else-if="selectedItem" class="block mt-1 text-xs text-gray-500">
                                {{ $t('label.current_stock') }}:
                                <span :class="Number(selectedItem.quantity) < 0 ? 'font-semibold text-red-600' : 'font-semibold'">
                                    {{ cleanNumber(selectedItem.purchase_quantity) }} {{ selectedItem.purchase_unit_label }}
                                </span>
                            </small>
                        </div>

                        <div class="form-col-12 sm:form-col-6">
                            <label for="wastage-quantity" class="db-field-title required">{{ $t('label.quantity') }}</label>
                            <input id="wastage-quantity" v-model="form.quantity" type="number" min="0" step="any"
                                class="db-field-control" :class="errors.quantity ? 'invalid' : ''">
                            <small v-if="errors.quantity" class="db-field-alert">{{ errors.quantity[0] }}</small>
                            <small v-else-if="value !== null" class="block mt-1 text-xs text-gray-500">
                                ≈ {{ money(value) }}
                            </small>
                        </div>

                        <div class="form-col-12 sm:form-col-6">
                            <label for="wastage-unit" class="db-field-title required">{{ $t('label.unit') }}</label>
                            <select id="wastage-unit" v-model="form.unit" class="db-field-control f-b-custom-select"
                                :class="errors.unit ? 'invalid' : ''">
                                <option v-for="unit in unitOptions" :key="unit" :value="unit">{{ unitLabel(unit) }}</option>
                            </select>
                            <small v-if="errors.unit" class="db-field-alert">{{ errors.unit[0] }}</small>
                        </div>

                        <div class="form-col-12">
                            <label for="wastage-reason" class="db-field-title required">{{ $t('label.reason') }}</label>
                            <select id="wastage-reason" v-model="form.reason" class="db-field-control f-b-custom-select"
                                :class="errors.reason ? 'invalid' : ''">
                                <option v-for="reason in reasonOptions" :key="reason.id" :value="reason.id">{{ reason.name }}</option>
                            </select>
                            <small v-if="errors.reason" class="db-field-alert">{{ errors.reason[0] }}</small>
                        </div>

                        <div class="form-col-12">
                            <label for="wastage-note" class="db-field-title">{{ $t('label.note') }}</label>
                            <textarea id="wastage-note" v-model="form.note" rows="3" class="db-field-control"
                                :class="errors.note ? 'invalid' : ''"></textarea>
                            <small v-if="errors.note" class="db-field-alert">{{ errors.note[0] }}</small>
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
import wastageReasonEnum from "../../../enums/modules/wastageReasonEnum";
import alertService from "../../../services/alertService";
import appService from "../../../services/appService";
import inventoryUnitService from "../../../services/inventoryUnitService";

export default {
    name: 'InventoryWastageCreateComponent',
    components: {
        SmSidebarModalCreateComponent,
        LoadingComponent
    },
    props: ['search'],
    data() {
        return {
            loading: {
                isActive: false
            },
            form: this.emptyForm(),
            errors: {}
        }
    },
    computed: {
        addButton: function () {
            return { title: this.$t('button.add_wastage') };
        },
        items: function () {
            return this.$store.getters['inventoryWastage/items'];
        },
        setting: function () {
            return this.$store.getters['frontendSetting/lists'] || {};
        },
        // Choosing an item also picks its usual unit (kg for cheese bought in kg)
        itemId: {
            get: function () {
                return this.form.inventory_item_id;
            },
            set: function (id) {
                this.form.inventory_item_id = id;
                const item = this.items.find((candidate) => candidate.id === id);
                if (item) {
                    this.form.unit = item.purchase_unit;
                }
            }
        },
        selectedItem: function () {
            return this.items.find((item) => item.id === this.form.inventory_item_id) || null;
        },
        // Units of the same kind as the item's stock: kg and g for a weighed item, and so on
        unitOptions: function () {
            if (!this.selectedItem) {
                return [];
            }
            return inventoryUnitService.units.filter((unit) => inventoryUnitService.baseUnit(unit) === this.selectedItem.unit);
        },
        reasonOptions: function () {
            return [
                { id: wastageReasonEnum.SPOILED, name: this.$t('label.wastage_spoiled') },
                { id: wastageReasonEnum.DAMAGED, name: this.$t('label.wastage_damaged') },
                { id: wastageReasonEnum.KITCHEN_ERROR, name: this.$t('label.wastage_kitchen_error') },
                { id: wastageReasonEnum.OTHER, name: this.$t('label.wastage_other') }
            ];
        },
        value: function () {
            const item = this.selectedItem;
            const quantity = Number(this.form.quantity);
            if (!item || item.unit_cost === null || this.form.quantity === '' || isNaN(quantity) || !this.form.unit) {
                return null;
            }
            return quantity * inventoryUnitService.factor(this.form.unit) * Number(item.unit_cost);
        }
    },
    methods: {
        emptyForm: function () {
            return {
                inventory_item_id: null,
                quantity: '',
                unit: '',
                reason: wastageReasonEnum.SPOILED,
                note: ''
            };
        },
        unitLabel: function (unit) {
            return inventoryUnitService.label(unit);
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
        addReset: function () {
            this.errors = {};
            this.form = this.emptyForm();
        },
        reset: function () {
            appService.sideDrawerHide();
            this.errors = {};
            this.form = this.emptyForm();
        },
        save: function () {
            this.loading.isActive = true;
            this.$store.dispatch('inventoryWastage/save', { form: this.form, search: this.search }).then(() => {
                appService.sideDrawerHide();
                this.loading.isActive = false;
                alertService.successFlip(0, this.$t('menu.inventory_wastage'));
                this.form = this.emptyForm();
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
