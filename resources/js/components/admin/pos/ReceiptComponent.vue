<template>
    <div :id="modalId" class="modal">
        <div class="modal-dialog max-w-[340px] rounded-none" :id="printId" :dir="direction">
            <div class="modal-header hidden-print">
                <button type="button" @click="reset"
                    class="modal-close flex items-center justify-center gap-1.5 py-2 px-4 rounded bg-[#FB4E4E]">
                    <i class="lab lab-back-bold lab-font-size-16 text-white"></i>
                    <span class="text-xs leading-5 capitalize text-white">{{ $t('button.close') }}</span>
                </button>
                <button type="button" v-print="printObj"
                    class="flex items-center justify-center gap-1.5 py-2 px-4 rounded bg-[#1AB759]">
                    <i class="lab lab-print-bold lab-font-size-16 text-white"></i>
                    <span class="text-xs leading-5 capitalize text-white">{{ $t('button.print_invoice') }}</span>
                </button>
            </div>
            <div class="hidden-print px-4 pt-4">
                <WhatsappInvoiceComponent ref="whatsapp" :order="order" :orderItems="orderItems"
                    :receipt="() => $refs.receiptBody" />
            </div>
            <div class="modal-body" ref="receiptBody">
                <div class="text-center pb-3.5 border-b border-dashed border-gray-400">
                    <img v-if="setting.theme_logo" :src="'/receipt-logo?v=' + encodeURIComponent(setting.theme_logo)" alt=""
                        class="max-w-[200px] max-h-[130px] mx-auto mb-2">
                    <h3 class="text-2xl font-bold mb-1">{{ company.company_name }}</h3>
                    <h4 class="text-sm font-normal">{{ branch.address }}</h4>
                    <h5 class="text-sm font-normal">Tel: {{ branch.phone }}</h5>
                </div>

                <table class="w-full my-1.5">
                    <tbody>
                        <tr>
                            <td class="text-xs text-left py-0.5 text-heading">
                                <strong>{{ $t('button.order') }} #{{ order.order_serial_no }}</strong>
                            </td>
                        </tr>

                        <tr>
                            <td class="text-xs text-left py-0.5 text-heading">
                                <strong>{{ order.order_date }}</strong>
                            </td>
                            <td class="text-xs text-right py-0.5 text-heading">
                                <strong>{{ order.order_time }}</strong>
                            </td>
                        </tr>

                        <tr v-if="customerName">
                            <td class="text-xs text-left py-0.5 text-heading">
                                <strong>{{ $t('label.customer') }}: {{ customerName }}</strong>
                            </td>
                        </tr>
                    </tbody>

                </table>

                <table class="w-full">
                    <thead class="border-t border-b border-dashed border-gray-400">
                        <tr>
                            <th scope="col" class="py-1 font-normal text-xs capitalize text-left text-heading w-8">
                                {{ $t('label.qty') }}
                            </th>
                            <th scope="col"
                                class="py-1 font-normal text-xs capitalize flex items-center justify-between text-heading">
                                <span>{{ $t('label.item_description') }}</span>
                                <span>{{ $t('label.price') }}</span>
                            </th>
                        </tr>
                    </thead>

                    <tbody class="border-b border-dashed border-gray-400">
                        <tr v-if="orderItems.length > 0" v-for="item in orderItems" :key="item">
                            <td class="text-left font-normal align-top py-1">
                                <p class="text-xs leading-5 text-heading">{{ item.quantity }}</p>
                            </td>
                            <td class="text-left font-normal align-top py-1">
                                <div class="flex items-center justify-between">
                                    <h4 class="text-sm font-normal capitalize">{{ item.item_name }}</h4>
                                    <p class="text-xs leading-5 text-heading">{{
                                        withoutCurrency(item.total_without_tax_currency_price) }}
                                    </p>
                                </div>
                                <p v-if="item.item_variations && item.item_variations.filter(v => v.variation_name && v.name).length > 0"
                                    class="text-xs leading-5 font-normal text-heading max-w-[200px]">
                                    <span v-for="(variation, index) in item.item_variations.filter(v => v.variation_name && v.name)">
                                        {{ variation.variation_name }}: {{ variation.name }}
                                        <span v-if="index + 1 < item.item_variations.filter(v => v.variation_name && v.name).length">, </span>
                                    </span>
                                </p>
                                <p v-if="item.item_extras && item.item_extras.filter(e => e.name).length > 0"
                                    class="text-xs leading-5 font-normal text-heading max-w-[200px]">
                                    {{ $t('label.extras') }}:
                                    <span v-for="(extra, index) in item.item_extras.filter(e => e.name)">
                                        {{ extra.name }}
                                        <span v-if="index + 1 < item.item_extras.filter(e => e.name).length">, </span>
                                    </span>
                                </p>
                                <p v-if="item.instruction"
                                    class="text-xs leading-5 font-normal text-heading max-w-[200px]">
                                    {{ $t('label.instruction') }}: {{ item.instruction }}
                                </p>

                                <div class="flex items-center justify-between" v-if="item.tax_rate > 0">
                                    <p class="text-xs leading-5 font-normal text-heading">
                                        {{ item.tax_name }} ({{ item.tax_currency_rate }}{{ item.tax_type === '%' ?
                                            item.tax_type : '' }})</p>
                                    <p class="text-xs leading-5 font-normal text-heading">
                                        {{ withoutCurrency(item.tax_currency_amount) }}
                                    </p>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <div class="py-2 pl-7">
                    <table class="w-full">
                        <tbody>
                            <tr>
                                <td class="text-xs text-left py-0.5 uppercase text-heading">{{ $t('label.subtotal') }}:
                                </td>
                                <td class="text-xs text-right py-0.5 text-heading">{{
                                    withoutCurrency(order.subtotal_without_tax_currency_price)
                                    }}</td>
                            </tr>
                            <tr>
                                <td class="text-xs text-left py-0.5 uppercase text-heading">
                                    {{ $t('label.total_tax') }}:
                                </td>
                                <td class="text-xs text-right py-0.5 text-heading">
                                    {{ withoutCurrency(order.total_tax_currency_price) }}
                                </td>
                            </tr>
                            <tr>
                                <td class="text-xs text-left py-0.5 uppercase text-heading">{{ $t('label.discount') }}:
                                </td>
                                <td class="text-xs text-right py-0.5 text-heading">{{
                                    withoutCurrency(order.discount_currency_price) }}
                                </td>
                            </tr>
                            <tr v-if="order.order_type === orderTypeEnum.DELIVERY">
                                <td class="text-xs text-left py-0.5 uppercase text-heading">{{
                                    $t('label.delivery_charge') }}:</td>
                                <td class="text-xs text-right py-0.5 text-heading">{{
                                    withoutCurrency(order.delivery_charge_currency_price) }}</td>
                            </tr>

                            <tr>
                                <td class="text-xs text-left py-0.5 font-bold uppercase text-heading">
                                    {{ $t('label.total') }}:
                                </td>
                                <td class="text-xs text-right py-0.5 font-bold text-heading">
                                    {{ withoutCurrency(order.total_currency_price) }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="text-xs py-2 border-t border-b border-dashed border-gray-400 text-heading">
                    <table class="w-full">
                        <tbody>
                            <tr>
                                <td class="pt-1 pb-1 pr-1"> {{ $t('label.order_type') }}: {{
                                    enums.orderTypeEnumArray[order.order_type] }}</td>
                            </tr>
                            <tr>
                                <td class="pt-1 pb-1 pr-1 align-top text-start">{{ $t('label.payment_type') }}: {{
                                    posPaymentMethodEnumArray[order.pos_payment_method] }}</td>
                                <td class="pt-1 pb-1 text-end" v-if="order.cash_back_amount > 0">
                                    <div>{{ $t('label.cash') }}: {{ withoutCurrency(order.pos_received_currency_amount)
                                        }}</div>
                                    <span>{{ $t('label.change') }} : {{
                                        withoutCurrency(order.cash_back_currency_amount) }}</span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <h4 v-if="order.token"
                    class="py-2 capitalize text-xl font-bold text-center border-b border-dashed border-gray-400">
                    {{ $t('label.token') }} #{{ order.token }}
                </h4>
                <div class="text-center pt-2 pb-4">
                    <p class="text-[11px] leading-[14px] capitalize text-heading">
                        {{ $t('message.thank_you') }}
                    </p>
                    <p class="text-[11px] leading-[14px] capitalize text-heading">
                        {{ $t('message.please_come_again') }}
                    </p>
                </div>
                <div class="flex flex-col items-end">
                    <h5 class="text-[8px] font-normal text-left w-[46px] leading-[10px]">
                        {{ $t('label.powered_by') }}
                    </h5>
                    <h6 class="text-xs font-normal leading-4">{{ company.company_name }}</h6>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import print from "vue3-print-nb";
import appService from "../../../services/appService";
import displayModeEnum from "../../../enums/modules/displayModeEnum";
import posPaymentMethodEnum from "../../../enums/modules/posPaymentMethodEnum";
import orderTypeEnum from "../../../enums/modules/orderTypeEnum";
import WhatsappInvoiceComponent from "../components/WhatsappInvoiceComponent";

export default {
    name: "ReceiptComponent",
    components: { WhatsappInvoiceComponent },
    props: {
        order: Object,
        // Ids are configurable so a second receipt (the reprint) can live on the
        // same page without colliding with the one shown after checkout.
        modalId: {
            type: String,
            default: "receiptModal"
        },
        printId: {
            type: String,
            default: "print"
        }
    },
    data() {
        return {
            printObj: {
                id: this.printId,
                popTitle: this.$t("menu.order_receipt"),
            },
            posPaymentMethodEnumArray: {
                [posPaymentMethodEnum.CASH]: this.$t("label.cash"),
                [posPaymentMethodEnum.CARD]: this.$t("label.card"),
                [posPaymentMethodEnum.MOBILE_BANKING]: this.$t("label.mobile_banking"),
                [posPaymentMethodEnum.OTHER]: this.$t("label.other"),
            },
            orderTypeEnum: orderTypeEnum,
            enums: {
                orderTypeEnumArray: {
                    [orderTypeEnum.DELIVERY]: this.$t("label.delivery"),
                    [orderTypeEnum.TAKEAWAY]: this.$t("label.takeaway"),
                    [orderTypeEnum.DINING_TABLE]: this.$t("label.dining_table")
                }
            }
        }
    },
    computed: {
        company: function () {
            return this.$store.getters['company/lists'];
        },
        setting: function () {
            return this.$store.getters['frontendSetting/lists'];
        },
        branch: function () {
            return this.$store.getters['backendGlobalState/branchShow'];
        },
        orderItems: function () {
            // Prefer the items carried on this receipt's own order, so two
            // receipts on the page never read each other's store state.
            if (this.order && Array.isArray(this.order.order_items)) {
                return this.order.order_items;
            }
            return this.$store.getters['posOrder/orderItems'];
        },
        orderUser: function () {
            return this.$store.getters['posOrder/orderUser'];
        },
        direction: function () {
            return this.$store.getters['frontendLanguage/show'].display_mode === displayModeEnum.RTL ? 'rtl' : 'ltr';
        },
        customers: function () {
            return this.$store.getters['user/lists'];
        },
        customerName: function () {
            if (!this.order) return '';
            // Prefer orderUser from the posOrder Vuex module (set in posOrder/show)
            if (this.orderUser && (this.orderUser.name || this.orderUser.full_name)) {
                return this.orderUser.name || this.orderUser.full_name;
            }
            // Prefer nested customer/user object on the order itself
            if (this.order.user && (this.order.user.name || this.order.user.full_name)) {
                return this.order.user.name || this.order.user.full_name;
            }
            if (this.order.customer && (this.order.customer.name || this.order.customer.full_name)) {
                return this.order.customer.name || this.order.customer.full_name;
            }
            // Try common id fields and lookup in global customers list
            const customerId = this.order.customer_id || this.order.user_id || this.order.userId || null;
            if (customerId && Array.isArray(this.customers)) {
                const found = this.customers.find(c => c.id === customerId || c.id == customerId);
                if (found) return found.name || found.full_name || '';
            }
            // Fallback to any customer_name field from order
            if (this.order.customer_name || this.order.user_name) return this.order.customer_name || this.order.user_name;
            return '';
        }
    },
    mounted() {
        this.$store.dispatch("company/lists").then().catch();
    },
    methods: {
        reset: function () {
            this.$refs.whatsapp.close();
            appService.modalHide('#' + this.modalId);
        },
        withoutCurrency: function (amount) {
            return appService.withoutCurrency(amount);
        },
    },
    directives: {
        print
    },
}
</script>
<style scoped>
@media print {
    .hidden-print {
        display: none !important;
    }
}
</style>