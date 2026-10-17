<template>
    <div :class="popover ? 'relative' : ''">
        <button v-if="popover || !open" type="button" @click="open ? close() : openForm()"
            :disabled="!order || !order.id"
            :class="popover
                ? 'flex items-center justify-center gap-2 px-4 h-[38px] rounded shadow-db-card bg-[#25D366] disabled:opacity-50'
                : 'w-full flex items-center justify-center gap-1.5 py-2 px-4 rounded bg-[#25D366] disabled:opacity-50'">
            <i class="fa-brands fa-whatsapp text-base leading-none text-white"></i>
            <span class="text-white" :class="popover ? 'text-sm' : 'text-xs leading-5'">
                {{ $t('button.send_on_whatsapp') }}
            </span>
        </button>

        <form v-if="open" @submit.prevent="send"
            :class="popover ? 'absolute top-full mt-2 z-10 w-72 p-4 rounded-lg shadow-db-card bg-white right-0 rtl:right-auto rtl:left-0' : ''">
            <label :for="inputId" class="block text-xs font-medium mb-1.5 text-heading">
                {{ $t('label.customer_whatsapp_number') }}
            </label>
            <input :id="inputId" ref="numberInput" :value="number" @input="onNumberInput" type="tel" dir="ltr"
                inputmode="numeric" autocomplete="off" placeholder="03XXXXXXXXX"
                class="h-10 w-full rounded border px-3 text-sm tracking-wide border-[#D9DBE9] text-black">

            <div class="mt-2">
                <span class="block text-[11px] leading-4 mb-1 text-[#6E7191]">{{ $t('label.whatsapp_open_in') }}</span>
                <div class="flex gap-1 p-0.5 rounded bg-[#F7F7FC]">
                    <button v-for="option in ['web', 'app']" :key="option" type="button" @click="setTarget(option)"
                        class="flex-1 py-1 rounded text-[11px] leading-4"
                        :class="target === option ? 'bg-white shadow-sm font-medium text-heading' : 'text-[#6E7191]'">
                        {{ $t(option === 'web' ? 'label.whatsapp_web' : 'label.whatsapp_app') }}
                    </button>
                </div>
            </div>

            <p v-if="error" class="text-xs mt-1 text-[#FB4E4E]">{{ error }}</p>
            <p v-else-if="preparing" class="text-xs mt-1 text-[#6E7191]">
                {{ $t('message.whatsapp_preparing_invoice') }}
            </p>

            <ol class="mt-2 ps-4 list-decimal space-y-0.5 text-[11px] leading-4 text-[#6E7191]">
                <li>{{ $t('message.whatsapp_step_open') }}</li>
                <li>{{ $t('message.whatsapp_step_paste') }}</li>
                <li>{{ $t('message.whatsapp_step_send') }}</li>
            </ol>

            <div class="flex gap-2 mt-2">
                <button type="button" @click="close"
                    class="flex-1 py-2 px-4 rounded text-xs leading-5 bg-[#F7F7FC] text-heading">
                    {{ $t('button.cancel') }}
                </button>
                <button type="submit" :disabled="preparing || !fullNumber"
                    class="flex-1 flex items-center justify-center gap-1.5 py-2 px-4 rounded bg-[#25D366] disabled:opacity-50">
                    <i class="fa-brands fa-whatsapp text-base leading-none text-white"></i>
                    <span class="text-xs leading-5 text-white">{{ $t('button.send_on_whatsapp') }}</span>
                </button>
            </div>
        </form>
    </div>
</template>

<script>
import axios from "axios";
import alertService from "../../../services/alertService";

let uid = 0;

// A Pakistani mobile number as 923XXXXXXXXX (what wa.me / WhatsApp expects), or "" if it isn't one.
function toInternational(value) {
    let digits = String(value || "").replace(/\D/g, "");
    if (digits.startsWith("0092")) {
        digits = digits.slice(2);
    } else if (digits.startsWith("0")) {
        digits = "92" + digits.slice(1);
    } else if (digits.startsWith("3")) {
        digits = "92" + digits;
    }
    if (digits.startsWith("920")) {
        digits = "92" + digits.slice(3);
    }
    return /^923\d{9}$/.test(digits) ? digits : "";
}

// What the input shows: 03XXXXXXXXX once the number is valid, otherwise the digits typed so far.
function toLocal(value) {
    const international = toInternational(value);
    if (international) {
        return "0" + international.slice(2);
    }
    const digits = String(value || "").replace(/\D/g, "");
    return digits.startsWith("0") && !digits.startsWith("00") ? digits.slice(0, 11) : digits.slice(0, 14);
}

const TARGET_KEY = "whatsappInvoiceTarget";

function savedTarget() {
    try {
        return localStorage.getItem(TARGET_KEY) === "app" ? "app" : "web";
    } catch (e) {
        return "web";
    }
}

export default {
    name: "WhatsappInvoiceComponent",
    props: {
        order: Object,
        orderItems: Array,
        // Returns the receipt element to snapshot as the invoice image.
        receipt: Function,
        popover: {
            type: Boolean,
            default: false,
        },
    },
    data() {
        return {
            inputId: "whatsappInvoiceNumber" + ++uid,
            target: savedTarget(),
            open: false,
            number: "",
            error: "",
            preparing: false,
            link: null,
            image: null,
        };
    },
    computed: {
        company: function () {
            return this.$store.getters["company/lists"];
        },
        setting: function () {
            return this.$store.getters["frontendSetting/lists"];
        },
        fullNumber: function () {
            return toInternational(this.number);
        },
    },
    watch: {
        "order.id": function () {
            this.close();
        },
    },
    methods: {
        // The order's customer, unless it is the POS walk-in placeholder.
        realCustomer: function () {
            const user = this.order && this.order.user;
            if (!user || user.username === "default-customer" || /^walk(ing|-?in)\b/i.test(user.name || "")) {
                return null;
            }
            return user;
        },
        openForm: function () {
            if (!this.order || !this.order.id) {
                return;
            }
            const customer = this.realCustomer();
            const phone = customer && customer.phone && !/^3?0+$/.test(String(customer.phone).replace(/\D/g, ""))
                ? (customer.country_code || "") + String(customer.phone).replace(/^0+/, "")
                : "";

            this.open = true;
            this.error = "";
            this.number = toInternational(phone) ? toLocal(phone) : "";
            this.$nextTick(() => this.$refs.numberInput && this.$refs.numberInput.focus());
            this.requestNotificationPermission();
            this.prepare();
        },
        close: function () {
            this.open = false;
            this.number = "";
            this.error = "";
            this.preparing = false;
            this.link = null;
            this.image = null;
        },
        // Fetch the shareable invoice link and snapshot the receipt as an image, side by side.
        prepare: function () {
            const orderId = this.order.id;
            this.preparing = true;
            this.error = "";

            const link = axios.get(`admin/pos-order/invoice-link/${orderId}`).then((res) => {
                if (this.order.id === orderId) {
                    this.link = res.data.data.url;
                }
            });
            const image = this.captureReceipt().then((blob) => {
                if (this.order.id === orderId) {
                    this.image = blob;
                }
            });

            Promise.allSettled([link, image]).then(() => {
                if (this.order.id === orderId && this.open) {
                    this.preparing = false;
                    if (!this.image && !this.link) {
                        this.error = this.$t("message.whatsapp_invoice_link_failed");
                    }
                }
            });
        },
        captureReceipt: function () {
            const element = this.receipt && this.receipt();
            if (!element) {
                return Promise.reject(new Error("Receipt not found"));
            }

            const width = element.offsetWidth;
            const direction = getComputedStyle(element).direction;
            // Keep the 1x1 placeholder GIFs laid out inline while html2canvas renders.
            const style = document.createElement("style");
            style.textContent = 'img[src^="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP"] { display: inline !important; }';
            document.head.appendChild(style);

            return import("html2canvas")
                .then(({ default: html2canvas }) => html2canvas(element, {
                    scale: 2,
                    backgroundColor: "#ffffff",
                    logging: false,
                    useCORS: true,
                    scrollX: 0,
                    scrollY: 0,
                    windowHeight: Math.max(window.innerHeight, element.scrollHeight),
                    // The receipt lives in a hidden modal: render the clone fully visible at its real width.
                    onclone: (clonedDocument, clonedElement) => {
                        clonedDocument.documentElement.style.overflow = "visible";
                        clonedDocument.body.style.overflow = "visible";
                        Object.assign(clonedElement.style, {
                            position: "absolute",
                            top: "0",
                            left: "0",
                            width: width + "px",
                            margin: "0",
                            direction: direction,
                            visibility: "visible",
                            opacity: "1",
                        });
                        clonedDocument.body.appendChild(clonedElement);
                    },
                }))
                .finally(() => style.remove())
                .then((canvas) => new Promise((resolve, reject) => {
                    canvas.toBlob((blob) => (blob ? resolve(blob) : reject(new Error("Receipt capture failed"))), "image/png");
                }));
        },
        onNumberInput: function (event) {
            this.number = toLocal(event.target.value);
            event.target.value = this.number;
            this.error = "";
        },
        setTarget: function (target) {
            this.target = target;
            try {
                localStorage.setItem(TARGET_KEY, target);
            } catch (e) {}
        },
        thanks: function () {
            return "*" + this.$t("message.whatsapp_thanks_title", { company: this.company.company_name }) + "*\n"
                + this.$t("message.whatsapp_thanks_body");
        },
        // Text-only invoice, used when the receipt image couldn't be copied.
        message: function () {
            return [
                this.thanks(),
                "",
                "*" + this.$t("message.whatsapp_invoice_title", { serial: this.order.order_serial_no }) + "*",
                this.order.order_date + " " + this.order.order_time,
                "",
                ...(this.orderItems || []).map((item) => `${item.quantity} x ${item.item_name}  ${item.total_currency_price}`),
                "",
                "*" + this.$t("message.whatsapp_invoice_total", { total: this.order.total_currency_price }) + "*",
                "",
                this.$t("message.whatsapp_invoice_download"),
                this.link,
            ].join("\n");
        },
        send: async function () {
            const phone = this.fullNumber;
            if (!phone) {
                this.error = this.$t("message.whatsapp_number_invalid") + " " + this.$t("message.whatsapp_number_hint");
                return;
            }

            // With the receipt image on the clipboard the cashier just pastes it into the chat.
            let copied = false;
            if (this.image && navigator.clipboard && window.ClipboardItem) {
                try {
                    await navigator.clipboard.write([new ClipboardItem({ "image/png": this.image })]);
                    copied = true;
                } catch (e) {
                    copied = false;
                }
            }

            if (!copied && !this.link) {
                this.prepare();
                return;
            }

            const text = copied ? this.thanks() : this.message();
            const query = `phone=${phone}&text=${encodeURIComponent(text)}`;
            if (this.target === "app") {
                window.location.href = `whatsapp://send?${query}`;
            } else if (!window.open(`https://web.whatsapp.com/send?${query}`, "_blank")) {
                this.error = this.$t("message.whatsapp_popup_blocked");
                return;
            }

            if (copied) {
                this.remindToPaste();
            }
            alertService.success(this.$t(copied ? "message.whatsapp_opened_with_image" : "message.whatsapp_opened"));
            this.close();
        },
        requestNotificationPermission: function () {
            if ("Notification" in window && Notification.permission === "default") {
                try {
                    Promise.resolve(Notification.requestPermission()).catch(() => {});
                } catch (e) {}
            }
        },
        // A desktop notification so the cashier remembers to paste the receipt image into the chat.
        remindToPaste: function () {
            if ("Notification" in window && Notification.permission === "granted") {
                try {
                    const notification = new Notification(this.$t("message.whatsapp_paste_title"), {
                        body: this.$t("message.whatsapp_paste_body"),
                        icon: this.setting.theme_favicon_logo || undefined,
                        tag: "whatsapp-invoice",
                        requireInteraction: true,
                    });
                    notification.onclick = () => notification.close();
                    setTimeout(() => notification.close(), 20000);
                } catch (e) {}
            }
        },
    },
};
</script>
