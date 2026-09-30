<template>
    <div :class="popover ? 'relative' : ''">
        <button v-if="popover || !open" type="button" @click="open ? close() : openForm()"
            :disabled="!order || !order.id" :class="popover
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

let instances = 0;

// The restaurant's customers have Pakistani mobiles (03XX XXXXXXX). Returns the
// digits-only international form WhatsApp needs (923XXXXXXXXX), or "" while the
// number is incomplete. Also accepts 3XX..., 923..., +92 3XX... and 0092...
function pakistaniMobile(input) {
    let digits = String(input || "").replace(/\D/g, "");
    if (digits.startsWith("0092")) {
        digits = digits.slice(2);
    } else if (digits.startsWith("0")) {
        digits = "92" + digits.slice(1);
    } else if (digits.startsWith("3")) {
        digits = "92" + digits;
    }
    // "+92 0345..." is a common typo: drop the local 0 after the country code.
    if (digits.startsWith("920")) {
        digits = "92" + digits.slice(3);
    }
    return /^923\d{9}$/.test(digits) ? digits : "";
}

// What the number box shows: a complete number in the familiar 03XXXXXXXXX
// form, otherwise the digits typed so far.
function localFormat(input) {
    const full = pakistaniMobile(input);
    if (full) return "0" + full.slice(2);
    const digits = String(input || "").replace(/\D/g, "");
    return digits.startsWith("0") && !digits.startsWith("00") ? digits.slice(0, 11) : digits.slice(0, 14);
}

// How long the invoice may take to prepare before Send is enabled anyway.
const PREPARE_TIMEOUT = 10000;

// Rejects if the promise hasn't settled in time, so a stalled step can't keep
// the form waiting forever.
function withTimeout(promise, ms) {
    let timer;
    const timeout = new Promise((resolve, reject) => {
        timer = setTimeout(() => reject(new Error("Timed out")), ms);
    });
    return Promise.race([promise, timeout]).finally(() => clearTimeout(timer));
}

// Per computer: whether to open WhatsApp Web or the installed desktop app.
const TARGET_KEY = "whatsappInvoiceTarget";

function savedTarget() {
    try {
        return localStorage.getItem(TARGET_KEY) === "app" ? "app" : "web";
    } catch (e) {
        return "web";
    }
}

/*
 * Sends an order's invoice to a customer through WhatsApp's click-to-chat link
 * (WhatsApp Web or the desktop app): a snapshot of the receipt is copied so the
 * cashier can paste it into the chat as an image. When copying isn't possible
 * the message carries a signed link to the invoice PDF instead.
 */
export default {
    name: "WhatsappInvoiceComponent",
    props: {
        order: Object,
        orderItems: Array,
        // Returns the receipt element to snapshot; it may be hidden on the page.
        receipt: Function,
        // Show the number form as a dropdown under the button instead of in place of it.
        popover: {
            type: Boolean,
            default: false
        }
    },
    data() {
        return {
            inputId: "whatsappInvoiceNumber" + (++instances),
            target: savedTarget(),
            open: false,
            number: "",
            error: "",
            preparing: false,
            // Bumped on every prepare/close so a slower earlier run can't overwrite a newer one.
            prepareRun: 0,
            link: null,
            image: null,
        }
    },
    computed: {
        company: function () {
            return this.$store.getters['company/lists'];
        },
        setting: function () {
            return this.$store.getters['frontendSetting/lists'];
        },
        // Digits-only international number, or "" while incomplete.
        fullNumber: function () {
            return pakistaniMobile(this.number);
        },
    },
    watch: {
        // A receipt can stay mounted while its order changes (e.g. the POS
        // last-order receipt), so drop anything prepared for the previous one.
        "order.id": function () {
            this.close();
        },
    },
    methods: {
        // Walk-in orders use a shared placeholder account, not the real customer.
        realCustomer: function () {
            const user = this.order && this.order.user;
            if (!user || user.username === "default-customer" || /^walk(ing|-?in)\b/i.test(user.name || "")) return null;
            return user;
        },
        openForm: function () {
            if (!this.order || !this.order.id) return;

            const customer = this.realCustomer();
            // Skip dummy numbers such as 3000000000 kept on placeholder accounts.
            const phone = customer && customer.phone && !/^3?0+$/.test(String(customer.phone).replace(/\D/g, ""))
                ? (customer.country_code || "") + String(customer.phone).replace(/^0+/, "")
                : "";
            this.open = true;
            this.error = "";
            this.number = pakistaniMobile(phone) ? localFormat(phone) : "";
            this.$nextTick(() => this.$refs.numberInput && this.$refs.numberInput.focus());
            this.requestNotificationPermission();
            this.prepare();
        },
        close: function () {
            this.open = false;
            this.number = "";
            this.error = "";
            this.preparing = false;
            this.prepareRun++;
            this.link = null;
            this.image = null;
        },
        // Fetch the invoice link and snapshot the receipt as soon as the form
        // opens, so pressing Send only has to copy and open (clipboard and
        // pop-ups both need to run straight off the click).
        prepare: function () {
            const run = ++this.prepareRun;
            const current = () => run === this.prepareRun && this.open;
            this.preparing = true;
            this.error = "";

            const link = axios.get(`admin/pos-order/invoice-link/${this.order.id}`, { timeout: PREPARE_TIMEOUT }).then(res => {
                if (current()) this.link = res.data.data.url;
            });
            // Started inside then() so an error thrown while starting the capture
            // still settles instead of leaving the form stuck on "Preparing".
            const image = withTimeout(Promise.resolve().then(() => this.captureReceipt()), PREPARE_TIMEOUT).then(blob => {
                if (current()) this.image = blob;
            });

            Promise.allSettled([link, image]).then(() => {
                if (!current()) return;
                this.preparing = false;
                // Either one is enough: the image is sent when it can be copied,
                // the link only as a fallback.
                if (!this.image && !this.link) {
                    this.error = this.$t("message.whatsapp_invoice_link_failed");
                }
            });
        },
        captureReceipt: function () {
            const receipt = this.receipt && this.receipt();
            if (!receipt) return Promise.reject(new Error("Receipt not found"));

            const width = receipt.offsetWidth;
            const direction = getComputedStyle(receipt).direction;

            // html2canvas finds the text baseline with a 1px gif on the live page;
            // Tailwind's `img { display: block }` breaks that and draws all text
            // too low, so let only that gif stay inline while capturing.
            const baselineFix = document.createElement("style");
            baselineFix.textContent = 'img[src^="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP"] { display: inline !important; }';
            document.head.appendChild(baselineFix);

            return import("html2canvas").then(({ default: html2canvas }) => {
                return html2canvas(receipt, {
                    scale: 2,
                    backgroundColor: "#ffffff",
                    logging: false,
                    // Load the logo with CORS so a URL on another host can't taint the canvas.
                    useCORS: true,
                    scrollX: 0,
                    scrollY: 0,
                    windowHeight: Math.max(window.innerHeight, receipt.scrollHeight),
                    // Copy only the receipt and the elements around it. Otherwise the whole
                    // page is copied and html2canvas waits, with no time limit, for every
                    // image on it (all the POS item photos) to load, which can hang.
                    ignoreElements: (element) => document.body.contains(element)
                        && !element.contains(receipt)
                        && !receipt.contains(element)
                        && !["STYLE", "LINK"].includes(element.tagName),
                    // The receipt sits in a modal that may be hidden or scrolled, which
                    // would blank or crop the snapshot, so capture a copy pinned to
                    // the top of the page instead.
                    onclone: (doc, clone) => {
                        doc.documentElement.style.overflow = "visible";
                        doc.body.style.overflow = "visible";
                        Object.assign(clone.style, {
                            position: "absolute",
                            top: "0",
                            left: "0",
                            width: width + "px",
                            margin: "0",
                            direction: direction,
                            visibility: "visible",
                            opacity: "1",
                        });
                        doc.body.appendChild(clone);
                    },
                });
            }).finally(() => baselineFix.remove()).then(canvas => new Promise((resolve, reject) => {
                canvas.toBlob(blob => blob ? resolve(blob) : reject(new Error("Receipt capture failed")), "image/png");
            }));
        },
        onNumberInput: function (event) {
            this.number = localFormat(event.target.value);
            // Keep the box in sync when characters were dropped.
            event.target.value = this.number;
            this.error = "";
        },
        setTarget: function (target) {
            this.target = target;
            try {
                localStorage.setItem(TARGET_KEY, target);
            } catch (e) {
                // Not saved in private windows; it just resets next time.
            }
        },
        // Typed into the chat alongside the pasted invoice image.
        thanks: function () {
            return "*" + this.$t("message.whatsapp_thanks_title", { company: this.company.company_name }) + "*\n"
                + this.$t("message.whatsapp_thanks_body");
        },
        // Only sent when the invoice image couldn't be copied for pasting, so it
        // carries the download link instead.
        message: function () {
            const lines = [
                this.thanks(),
                "",
                "*" + this.$t("message.whatsapp_invoice_title", { serial: this.order.order_serial_no }) + "*",
                this.order.order_date + " " + this.order.order_time,
                "",
                ...(this.orderItems || []).map(item => `${item.quantity} x ${item.item_name}  ${item.total_currency_price}`),
                "",
                "*" + this.$t("message.whatsapp_invoice_total", { total: this.order.total_currency_price }) + "*",
                "",
                this.$t("message.whatsapp_invoice_download"),
                this.link,
            ];
            return lines.join("\n");
        },
        send: async function () {
            const phone = this.fullNumber;
            if (!phone) {
                this.error = this.$t("message.whatsapp_number_invalid") + " " + this.$t("message.whatsapp_number_hint");
                return;
            }
            // Copy before opening the tab: the clipboard only accepts writes
            // while this page still has focus.
            let imageCopied = false;
            if (this.image && navigator.clipboard && window.ClipboardItem) {
                try {
                    await navigator.clipboard.write([new ClipboardItem({ "image/png": this.image })]);
                    imageCopied = true;
                } catch (e) {
                    imageCopied = false;
                }
            }

            if (!imageCopied && !this.link) {
                this.prepare();
                return;
            }

            // With the image copied the chat opens with the thank-you typed in,
            // ready for the invoice to be pasted. Otherwise the full summary goes
            // with the download link.
            const text = imageCopied ? this.thanks() : this.message();
            const query = `phone=${phone}&text=${encodeURIComponent(text)}`;

            if (this.target === "app") {
                // The desktop app reuses its one window, so no tab piles up.
                window.location.href = `whatsapp://send?${query}`;
            } else {
                // WhatsApp Web won't let other sites reuse its tab, so this is
                // always a new one.
                const tab = window.open(`https://web.whatsapp.com/send?${query}`, "_blank");
                if (!tab) {
                    this.error = this.$t("message.whatsapp_popup_blocked");
                    return;
                }
            }

            if (imageCopied) {
                this.remindToPaste();
            }
            alertService.success(this.$t(imageCopied ? "message.whatsapp_opened_with_image" : "message.whatsapp_opened"));
            this.close();
        },
        // Asked while the cashier is still on the POS, so the permission prompt
        // doesn't get left behind when the WhatsApp tab opens.
        requestNotificationPermission: function () {
            if ("Notification" in window && Notification.permission === "default") {
                try {
                    Promise.resolve(Notification.requestPermission()).catch(() => {});
                } catch (e) {
                    // Older browsers without the promise form; the reminder is optional.
                }
            }
        },
        // A system notification stays visible over the WhatsApp tab, unlike the
        // in-page toast, which the cashier can't see once WhatsApp opens.
        remindToPaste: function () {
            if (!("Notification" in window) || Notification.permission !== "granted") return;
            try {
                const notification = new Notification(this.$t("message.whatsapp_paste_title"), {
                    body: this.$t("message.whatsapp_paste_body"),
                    icon: this.setting.theme_favicon_logo || undefined,
                    tag: "whatsapp-invoice",
                    requireInteraction: true,
                });
                notification.onclick = () => notification.close();
                // WhatsApp Web takes a few seconds to load the chat, so keep the
                // reminder up for a while, then clear it.
                setTimeout(() => notification.close(), 20000);
            } catch (e) {
                // Notifications are only a reminder; sending already worked.
            }
        },
    },
}
</script>
