<template>
    <LoadingComponent :props="loading" />

    <p class="text-sm text-gray-500 mb-4">{{ $t('message.recipe_hint') }}</p>

    <div v-for="section in sections" :key="section.key" class="db-card mb-5">
        <div class="db-card-header border-none flex-wrap gap-3">
            <div>
                <h3 class="db-card-title">{{ section.title }}</h3>
                <div class="text-sm text-gray-500 mt-1">
                    <span>{{ section.costLabel }}: <b>{{ money(section.cost) }}</b></span>
                    <template v-if="section.price !== null">
                        <span class="mx-1.5">·</span>
                        <span>{{ $t('label.price') }}: <b>{{ money(section.price) }}</b></span>
                        <template v-if="section.price > 0 && section.cost > 0">
                            <span class="mx-1.5">·</span>
                            <span>{{ $t('label.food_cost') }}: <b>{{ percent(section.cost, section.price) }}</b></span>
                        </template>
                    </template>
                    <span v-if="section.missingCost" class="text-red-600"> *</span>
                </div>
            </div>
            <button type="button" data-modal="#recipeModal" class="db-btn py-2 text-white bg-primary"
                @click="add(section)">
                <i class="lab lab-add-circle-line"></i>
                <span>{{ $t('button.add_ingredient') }}</span>
            </button>
        </div>
        <div class="db-table-responsive">
            <table class="db-table stripe">
                <thead class="db-table-head">
                    <tr class="db-table-head-tr">
                        <th class="db-table-head-th">{{ $t('label.ingredient') }}</th>
                        <th class="db-table-head-th">{{ $t('label.quantity') }}</th>
                        <th class="db-table-head-th">{{ $t('label.cost') }}</th>
                        <th class="db-table-head-th">{{ $t('label.action') }}</th>
                    </tr>
                </thead>
                <tbody class="db-table-body">
                    <tr v-for="line in section.lines" :key="line.id" class="db-table-body-tr">
                        <td class="db-table-body-td">
                            {{ line.inventory_item_name }}
                            <span v-if="line.inventory_item_deleted" class="db-table-badge text-red-600 bg-red-100 ml-1">
                                {{ $t('label.deleted') }}
                            </span>
                        </td>
                        <td class="db-table-body-td whitespace-nowrap">{{ number(line.quantity) }} {{ line.unit }}</td>
                        <td class="db-table-body-td whitespace-nowrap">{{ line.cost === null ? '-' : money(line.cost) }}</td>
                        <td class="db-table-body-td">
                            <SmIconModalEditComponent @click="edit(line)" />
                            <SmIconDeleteComponent @click="destroy(line.id)" />
                        </td>
                    </tr>
                    <tr v-if="!section.lines.length" class="db-table-body-tr">
                        <td class="db-table-body-td text-center text-gray-500" colspan="4">
                            {{ $t('message.no_ingredients') }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <p v-if="sections.some((section) => section.missingCost)" class="text-sm text-red-600">
        * {{ $t('message.recipe_missing_cost') }}
    </p>

    <div id="recipeModal" class="modal">
        <div class="modal-dialog">
            <div class="modal-header">
                <h3 class="modal-title">{{ $t('label.recipe') }}</h3>
                <button class="modal-close fa-solid fa-xmark text-xl text-slate-400 hover:text-red-500"
                    @click="reset"></button>
            </div>
            <div class="modal-body">
                <form @submit.prevent="save">
                    <div class="form-row">
                        <div class="form-col-12">
                            <label for="recipe-part" class="db-field-title required">{{ $t('label.used_in') }}</label>
                            <select id="recipe-part" v-model="form.part" class="db-field-control f-b-custom-select"
                                :class="errors.item_variation_id || errors.item_extra_id ? 'invalid' : ''">
                                <option value="base">{{ $t('label.every_order') }}</option>
                                <optgroup v-for="group in variationGroups" :key="group.attribute" :label="group.attribute">
                                    <option v-for="variation in group.variations" :key="variation.id"
                                        :value="'variation:' + variation.id">
                                        {{ group.attribute }}: {{ variation.name }}
                                    </option>
                                </optgroup>
                                <optgroup v-if="options.extras.length" :label="$t('label.extra')">
                                    <option v-for="extra in options.extras" :key="extra.id" :value="'extra:' + extra.id">
                                        {{ $t('label.extra') }}: {{ extra.name }}
                                    </option>
                                </optgroup>
                            </select>
                            <small v-if="errors.item_variation_id" class="db-field-alert">{{ errors.item_variation_id[0] }}</small>
                            <small v-if="errors.item_extra_id" class="db-field-alert">{{ errors.item_extra_id[0] }}</small>
                        </div>

                        <div class="form-col-12 sm:form-col-6">
                            <label for="recipe-ingredient" class="db-field-title required">{{ $t('label.ingredient') }}</label>
                            <select id="recipe-ingredient" v-model="form.inventory_item_id"
                                class="db-field-control f-b-custom-select"
                                :class="errors.inventory_item_id ? 'invalid' : ''">
                                <option :value="null" disabled>--</option>
                                <option v-for="inventoryItem in ingredientOptions" :key="inventoryItem.id"
                                    :value="inventoryItem.id">
                                    {{ inventoryItem.name }} ({{ inventoryItem.unit }}){{ inventoryItem.status === enums.statusEnum.ACTIVE ? '' : ' - ' + $t('label.inactive') }}
                                </option>
                            </select>
                            <small v-if="errors.inventory_item_id" class="db-field-alert">{{ errors.inventory_item_id[0] }}</small>
                            <small v-else-if="!options.inventory_items.length" class="block mt-1 text-xs text-gray-500">
                                {{ $t('message.recipe_no_inventory') }}
                            </small>
                        </div>

                        <div class="form-col-12 sm:form-col-6">
                            <label for="recipe-quantity" class="db-field-title required">
                                {{ $t('label.quantity') }}<span v-if="selectedIngredient"> ({{ selectedIngredient.unit }})</span>
                            </label>
                            <input id="recipe-quantity" v-model="form.quantity" type="number" min="0" step="any"
                                class="db-field-control" :class="errors.quantity ? 'invalid' : ''">
                            <small v-if="errors.quantity" class="db-field-alert">{{ errors.quantity[0] }}</small>
                            <small v-else class="block mt-1 text-xs text-gray-500">
                                {{ $t('message.recipe_quantity_hint', { item: options.item.name || '' }) }}
                                <template v-if="formCost !== null"> ≈ {{ money(formCost) }}</template>
                            </small>
                        </div>

                        <div class="form-col-12">
                            <div class="modal-btns">
                                <button type="button" class="modal-btn-outline modal-close" @click="reset">
                                    <i class="lab lab-close"></i>
                                    <span>{{ $t('button.close') }}</span>
                                </button>
                                <button type="submit" class="db-btn py-2 text-white bg-primary">
                                    <i class="lab lab-save"></i>
                                    <span>{{ $t('button.save') }}</span>
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
import LoadingComponent from "../../components/LoadingComponent";
import SmIconModalEditComponent from "../../components/buttons/SmIconModalEditComponent";
import SmIconDeleteComponent from "../../components/buttons/SmIconDeleteComponent";
import statusEnum from "../../../../enums/modules/statusEnum";
import appService from "../../../../services/appService";
import alertService from "../../../../services/alertService";

export default {
    name: "ItemRecipeComponent",
    components: { LoadingComponent, SmIconModalEditComponent, SmIconDeleteComponent },
    props: {
        item: { type: Number },
    },
    data() {
        return {
            loading: {
                isActive: false,
            },
            enums: {
                statusEnum: statusEnum,
            },
            form: this.emptyForm(),
            errors: {},
        };
    },
    computed: {
        lines: function () {
            return this.$store.getters['itemIngredient/lists'];
        },
        options: function () {
            return this.$store.getters['itemIngredient/options'];
        },
        setting: function () {
            return this.$store.getters['frontendSetting/lists'] || {};
        },
        variationGroups: function () {
            const groups = [];
            this.options.variations.forEach((variation) => {
                let group = groups.find((existing) => existing.attribute === variation.attribute);
                if (!group) {
                    group = { attribute: variation.attribute, variations: [] };
                    groups.push(group);
                }
                group.variations.push(variation);
            });
            return groups;
        },
        // Every-order lines, then each variation's and each extra's lines, with what one serving costs
        sections: function () {
            const total = (lines) => lines.reduce((sum, line) => sum + (line.cost === null ? 0 : Number(line.cost)), 0);
            const missing = (lines) => lines.some((line) => line.cost === null);
            const itemPrice = Number(this.options.item.price || 0);
            const hasVariations = this.options.variations.length > 0;

            const baseLines = this.lines.filter((line) => !line.item_variation_id && !line.item_extra_id);
            const sections = [{
                key: 'base',
                part: 'base',
                title: this.$t('label.every_order'),
                lines: baseLines,
                costLabel: this.$t('label.recipe_cost'),
                cost: total(baseLines),
                // With variations the price depends on the one chosen, so it's shown per variation instead
                price: hasVariations ? null : itemPrice,
                missingCost: missing(baseLines),
            }];

            this.options.variations.forEach((variation) => {
                const lines = this.lines.filter((line) => line.item_variation_id === variation.id);
                sections.push({
                    key: 'variation-' + variation.id,
                    part: 'variation:' + variation.id,
                    title: `${variation.attribute}: ${variation.name}`,
                    lines: lines,
                    costLabel: this.$t('label.cost_with_every_order'),
                    cost: total(baseLines) + total(lines),
                    price: itemPrice + Number(variation.price || 0),
                    missingCost: missing(baseLines) || missing(lines),
                });
            });

            this.options.extras.forEach((extra) => {
                const lines = this.lines.filter((line) => line.item_extra_id === extra.id);
                sections.push({
                    key: 'extra-' + extra.id,
                    part: 'extra:' + extra.id,
                    title: `${this.$t('label.extra')}: ${extra.name}`,
                    lines: lines,
                    costLabel: this.$t('label.recipe_cost'),
                    cost: total(lines),
                    price: Number(extra.price || 0),
                    missingCost: missing(lines),
                });
            });

            return sections;
        },
        // Active inventory items, plus the one already on the line being edited even if it's been made inactive
        ingredientOptions: function () {
            return this.options.inventory_items.filter((inventoryItem) => inventoryItem.status === statusEnum.ACTIVE
                || inventoryItem.id === this.form.inventory_item_id);
        },
        selectedIngredient: function () {
            return this.options.inventory_items.find((inventoryItem) => inventoryItem.id === this.form.inventory_item_id) || null;
        },
        formCost: function () {
            const ingredient = this.selectedIngredient;
            const quantity = Number(this.form.quantity);
            if (!ingredient || ingredient.unit_cost === null || this.form.quantity === '' || isNaN(quantity)) {
                return null;
            }
            return quantity * Number(ingredient.unit_cost);
        },
    },
    mounted() {
        this.list();
    },
    methods: {
        emptyForm: function (part = 'base') {
            return {
                part: part,
                inventory_item_id: null,
                quantity: '',
            };
        },
        list: function () {
            this.loading.isActive = true;
            return this.$store.dispatch('itemIngredient/lists', { id: this.item }).then(() => {
                this.loading.isActive = false;
            }).catch((err) => {
                this.loading.isActive = false;
                alertService.error(err.response?.data?.message || err.message);
            });
        },
        number: function (value) {
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
        percent: function (cost, price) {
            return (cost / price * 100).toFixed(1) + '%';
        },
        add: function (section) {
            this.$store.dispatch('itemIngredient/reset');
            this.errors = {};
            this.form = this.emptyForm(section.part);
            appService.modalShow('#recipeModal');
        },
        edit: function (line) {
            this.$store.dispatch('itemIngredient/edit', line.id);
            this.errors = {};
            this.form = {
                part: line.item_variation_id
                    ? 'variation:' + line.item_variation_id
                    : (line.item_extra_id ? 'extra:' + line.item_extra_id : 'base'),
                inventory_item_id: line.inventory_item_id,
                quantity: Number(line.quantity),
            };
            appService.modalShow('#recipeModal');
        },
        reset: function () {
            appService.modalHide('#recipeModal');
            this.$store.dispatch('itemIngredient/reset');
            this.errors = {};
            this.form = this.emptyForm();
        },
        save: function () {
            const [type, id] = this.form.part.split(':');
            const tempId = this.$store.getters['itemIngredient/temp'].temp_id;
            this.loading.isActive = true;
            this.$store.dispatch('itemIngredient/save', {
                id: this.item,
                form: {
                    item_variation_id: type === 'variation' ? Number(id) : null,
                    item_extra_id: type === 'extra' ? Number(id) : null,
                    inventory_item_id: this.form.inventory_item_id,
                    quantity: this.form.quantity,
                },
            }).then(() => {
                this.loading.isActive = false;
                appService.modalHide('#recipeModal');
                alertService.successFlip(tempId === null ? 0 : 1, this.$t('label.ingredient'));
                this.errors = {};
                this.form = this.emptyForm();
            }).catch((err) => {
                this.loading.isActive = false;
                this.errors = err.response?.data?.errors || {};
                if (!err.response?.data?.errors) {
                    alertService.error(err.response?.data?.message || err.message);
                }
            });
        },
        destroy: function (id) {
            appService.destroyConfirmation().then(() => {
                this.loading.isActive = true;
                this.$store.dispatch('itemIngredient/destroy', { item: this.item, id: id }).then(() => {
                    this.loading.isActive = false;
                    alertService.successFlip(null, this.$t('label.ingredient'));
                }).catch((err) => {
                    this.loading.isActive = false;
                    alertService.error(err.response?.data?.message || err.message);
                });
            }).catch(() => {});
        },
    },
};
</script>
