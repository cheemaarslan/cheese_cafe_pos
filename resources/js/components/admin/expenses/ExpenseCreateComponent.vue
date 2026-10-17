<template>
    <LoadingComponent :props="loading" />
    <SmSidebarModalCreateComponent :props="addButton" @click="addReset" />

    <div id="sidebar" class="drawer">
        <div class="drawer-dialog">
            <div class="drawer-header">
                <h3 class="drawer-title">{{ $t('menu.expenses') }}</h3>
                <button class="fa-solid fa-xmark close-btn" type="button" @click="reset"></button>
            </div>
            <div class="drawer-body">
                <form @submit.prevent="save">
                    <div class="form-row">
                        <div class="form-col-12 sm:form-col-6">
                            <label for="expense-title" class="db-field-title required">{{ $t('label.title') }}</label>
                            <input id="expense-title" v-model="props.form.title" :class="errors.title ? 'invalid' : ''"
                                class="db-field-control" type="text" :placeholder="$t('placeholder.expense_title')">
                            <small v-if="errors.title" class="db-field-alert">{{ errors.title[0] }}</small>
                        </div>

                        <div class="form-col-12 sm:form-col-6">
                            <label for="expense-category" class="db-field-title">{{ $t('label.category') }}</label>
                            <input id="expense-category" v-model="props.form.category" list="expense-categories"
                                :class="errors.category ? 'invalid' : ''" class="db-field-control" type="text"
                                :placeholder="$t('placeholder.expense_category')">
                            <datalist id="expense-categories">
                                <option v-for="category in categories" :key="category" :value="category"></option>
                            </datalist>
                            <small v-if="errors.category" class="db-field-alert">{{ errors.category[0] }}</small>
                        </div>

                        <div class="form-col-12 sm:form-col-6">
                            <label for="expense-amount" class="db-field-title required">{{ $t('label.amount') }}</label>
                            <input id="expense-amount" v-model="props.form.amount" :class="errors.amount ? 'invalid' : ''"
                                class="db-field-control" type="number" min="0" step="0.01">
                            <small v-if="errors.amount" class="db-field-alert">{{ errors.amount[0] }}</small>
                        </div>

                        <div class="form-col-12 sm:form-col-6">
                            <label for="expense-date" class="db-field-title required">{{ $t('label.date') }}</label>
                            <input id="expense-date" v-model="props.form.expense_date"
                                :class="errors.expense_date ? 'invalid' : ''" class="db-field-control" type="date">
                            <small v-if="errors.expense_date" class="db-field-alert">{{ errors.expense_date[0] }}</small>
                        </div>

                        <div class="form-col-12">
                            <label for="expense-notes" class="db-field-title">{{ $t('label.notes') }}</label>
                            <textarea id="expense-notes" v-model="props.form.notes" :class="errors.notes ? 'invalid' : ''"
                                class="db-field-control" rows="3"></textarea>
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
import alertService from "../../../services/alertService";
import appService from "../../../services/appService";

export default {
    name: 'ExpenseCreateComponent',
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
            errors: {}
        }
    },
    computed: {
        addButton: function () {
            return { title: this.$t('button.add_expense') };
        },
        // Suggest the usual categories plus any already used, so the same names keep being reused.
        categories: function () {
            const used = this.$store.getters['expense/lists'].map((expense) => expense.category).filter(Boolean);
            return [...new Set([
                this.$t('label.expense_rent'),
                this.$t('label.expense_salary'),
                this.$t('label.expense_utilities'),
                this.$t('label.expense_maintenance'),
                ...used
            ])];
        }
    },
    methods: {
        emptyForm: function () {
            return {
                title: '',
                category: '',
                amount: '',
                expense_date: new Date().toLocaleDateString('en-CA'),
                notes: ''
            };
        },
        reset: function () {
            appService.sideDrawerHide();
            this.$store.dispatch('expense/reset');
            this.errors = {};
            this.props.form = this.emptyForm();
        },
        addReset: function () {
            this.$store.dispatch('expense/reset');
            this.errors = {};
            this.props.form = this.emptyForm();
        },
        save: function () {
            const tempId = this.$store.getters['expense/temp'].temp_id;
            this.loading.isActive = true;
            this.$store.dispatch('expense/save', this.props).then(() => {
                appService.sideDrawerHide();
                this.loading.isActive = false;
                alertService.successFlip(tempId === null ? 0 : 1, this.$t('menu.expenses'));
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
