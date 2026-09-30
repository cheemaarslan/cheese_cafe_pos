<template>
    <LoadingComponent :props="loading" />

    <footer
        class="footer-part relative overflow-hidden bg-[#0d0705] text-white"
    >
        <!-- Texture overlay -->
        <div class="footer-noise" aria-hidden="true"></div>

        <!-- Diagonal accent stripe -->
        <div class="footer-stripe" aria-hidden="true"></div>

        <!-- ── HERO BAND ── -->
        <div class="footer-hero relative z-10 pt-12 pb-0">
            <div class="footer-container mx-auto">
                <div class="footer-hero-inner">
                    <!-- Left: Brand block -->
                    <div class="brand-block">
                        <router-link
                            :to="{ name: 'frontend.home' }"
                            class="brand-logo-link"
                        >
                            <img
                                class="brand-logo object-contain"
                                :src="setting.theme_logo"
                                alt="logo"
                                width="78"
                                height="100"
                            />
                        </router-link>

                        <!-- Contact pills - Hidden on mobile -->
                        <div class="contact-pills hidden md:flex">
                            <a
                                v-if="setting.company_email"
                                :href="`mailto:${setting.company_email}`"
                                class="contact-pill"
                            >
                                <span class="pill-icon"
                                    ><i class="fa-solid fa-envelope"></i
                                ></span>
                                <span>{{ setting.company_email }}</span>
                            </a>
                            <a
                                :href="`tel:${setting.company_phone}`"
                                class="contact-pill contact-pill--phone"
                            >
                                <span class="pill-icon"
                                    ><i class="lab lab-call-center"></i
                                ></span>
                                <span>{{ setting.company_phone }}</span>
                            </a>
                        </div>

                        <!-- Socials - Hidden on mobile -->
                        <div class="social-row hidden md:flex">
                            <a
                                v-if="setting.social_media_facebook"
                                target="_blank"
                                :href="setting.social_media_facebook"
                                class="social-btn"
                                aria-label="Facebook"
                            >
                                <i class="fa-brands fa-facebook-f"></i>
                            </a>
                            <a
                                v-if="setting.social_media_twitter"
                                target="_blank"
                                :href="setting.social_media_twitter"
                                class="social-btn"
                                aria-label="X / Twitter"
                            >
                                <i class="fa-brands fa-x-twitter"></i>
                            </a>
                            <a
                                v-if="setting.social_media_instagram"
                                target="_blank"
                                :href="setting.social_media_instagram"
                                class="social-btn"
                                aria-label="Instagram"
                            >
                                <i class="fa-brands fa-instagram"></i>
                            </a>
                            <a
                                v-if="setting.social_media_youtube"
                                target="_blank"
                                :href="setting.social_media_youtube"
                                class="social-btn"
                                aria-label="YouTube"
                            >
                                <i class="fa-brands fa-youtube"></i>
                            </a>
                        </div>
                    </div>

                    <!-- Center: Nav columns -->
                    <div class="nav-columns">
                        <div class="nav-col">
                            <h4 class="nav-col__heading">Quick Links</h4>
                            <nav class="nav-col__links">
                                <template v-if="pages.length > 0">
                                    <router-link
                                        v-for="page in pages"
                                        :key="page.slug"
                                        class="nav-col__link"
                                        :to="resolvePageRoute(page)"
                                    >
                                        <span class="nav-col__arrow">→</span>
                                        {{ page.title }}
                                    </router-link>
                                </template>
                                <template v-else>
                                    <div
                                        class="nav-skeleton"
                                        v-for="n in 4"
                                        :key="n"
                                    ></div>
                                </template>
                            </nav>
                        </div>

                        <div class="nav-col hidden md:block">
                            <h4 class="nav-col__heading">Opening Hours</h4>
                            <ul class="hours-list">
                                <li class="hours-item">
                                    <span class="hours-day">Mon </span>
                                    <span class="hours-time"
                                        >11:00 AM – 10:00 PM</span
                                    >
                                </li>
                                <li class="hours-item">
                                    <span class="hours-day">Tue </span>
                                    <span class="hours-time"
                                        >11:00 AM – 10:00 PM</span
                                    >
                                </li>
                                <li class="hours-item">
                                    <span class="hours-day">Wed </span>
                                    <span class="hours-time"
                                        >11:00 AM – 10:00 PM</span
                                    >
                                </li>
                                <li class="hours-item">
                                    <span class="hours-day">Thu </span>
                                    <span class="hours-time"
                                        >11:00 AM – 10:00 PM</span
                                    >
                                </li>
                                <li class="hours-item">
                                    <span class="hours-day">Fri </span>
                                    <span class="hours-time"
                                        >11:00 AM – 10:00 PM</span
                                    >
                                </li>
                                <li class="hours-item">
                                    <span class="hours-day">Sat </span>
                                    <span class="hours-time"
                                        >11:00 AM – 10:00 PM</span
                                    >
                                </li>
                                <li class="hours-item">
                                    <span class="hours-day">Sun </span>
                                    <span class="hours-time"
                                        >11:00 AM – 10:00 PM</span
                                    >
                                </li>
                            </ul>

                            <!-- App Store buttons -->
                            <div
                                v-if="
                                    setting.site_android_app_link ||
                                    setting.site_ios_app_link
                                "
                                class="app-badges"
                            >
                                <h4 class="nav-col__heading mt-6">
                                    Download App
                                </h4>
                                <div class="app-badges__row">
                                    <a
                                        target="_blank"
                                        v-if="setting.site_android_app_link"
                                        :href="setting.site_android_app_link"
                                        class="app-badge"
                                    >
                                        <img
                                            :src="setting.image_play_store"
                                            alt="Google Play"
                                            width="135"
                                            height="40"
                                        />
                                    </a>
                                    <a
                                        target="_blank"
                                        v-if="setting.site_ios_app_link"
                                        :href="setting.site_ios_app_link"
                                        class="app-badge"
                                    >
                                        <img
                                            :src="setting.image_app_store"
                                            alt="App Store"
                                            width="135"
                                            height="40"
                                        />
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right: Subscribe + Chef (hidden on mobile) -->
                    <div class="subscribe-chef hidden md:block">
                        <div class="subscribe-block">
                            <div class="subscribe-label">Newsletter</div>
                            <h3 class="subscribe-heading">Stay in the loop</h3>
                            <p class="subscribe-sub">
                                {{ $t("label.subscribe_short_text") }}
                            </p>

                            <form
                                @submit.prevent="saveSubscription"
                                class="subscribe-form"
                            >
                                <div class="subscribe-input-wrap">
                                    <i
                                        class="fa-solid fa-envelope subscribe-input-icon"
                                    ></i>
                                    <input
                                        type="email"
                                        :placeholder="
                                            $t('label.your_email_address')
                                        "
                                        v-model="subscriptionProps.post.email"
                                        class="subscribe-input"
                                    />
                                </div>
                                <button type="submit" class="subscribe-btn">
                                    {{ $t("button.subscribe") }}
                                    <i class="fa-solid fa-paper-plane"></i>
                                </button>
                            </form>

                            <!-- Trust badges -->
                            <div class="trust-row">
                                <span class="trust-badge"
                                    ><i class="fa-solid fa-shield-halved"></i>
                                    No spam</span
                                >
                                <span class="trust-badge"
                                    ><i class="fa-solid fa-bolt"></i> Weekly
                                    deals</span
                                >
                                <span class="trust-badge"
                                    ><i class="fa-solid fa-ban"></i> Unsubscribe
                                    anytime</span
                                >
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ── CHEF IMAGE BAND ── -->
        <div class="chef-band relative z-10">
            <div class="footer-container mx-auto">
                <div class="chef-band__inner">
                    <div class="chef-band__copy">
                        <span class="chef-band__eyebrow"
                            >Fresh. Fast. Flavourful.</span
                        >
                        <p class="chef-band__text">
                            Every burger and gyro made to order — no shortcuts,
                            no compromises.
                        </p>
                    </div>
                    <div class="chef-band__divider" aria-hidden="true"></div>
                    <!-- <div class="chef-band__stats">
                        <div class="stat">
                            <span class="stat__num">{{ footerData?.stats?.years || 5 }}+</span>
                            <span class="stat__label">Years</span>
                        </div>
                        <div class="stat">
                            <span class="stat__num">{{ footerData?.stats?.awards || 7 }}+</span>
                            <span class="stat__label">Awards</span>
                        </div>
                        <div class="stat">
                            <span class="stat__num">{{ footerData?.stats?.locations || 10 }}+</span>
                            <span class="stat__label">Locations</span>
                        </div>
                    </div> -->
                </div>
            </div>
            <!-- Chef image breakout -->
            <img
                class="chef-img"
                :src="
                    footerData?.theme_footer_image ||
                    '/images/default/footer_bg.webp'
                "
                alt="Chef"
                width="500"
                height="500"
            />
        </div>

        <!-- ── COPYRIGHT ── -->
        <div class="footer-copy relative z-10">
            <div class="footer-container mx-auto">
                <div class="copy-inner">
                    <p class="copy-text">{{ setting.site_copyright }}</p>
                    <button @click="openDeveloperLink" class="bitlogicx-badge">
                        <span class="bitlogicx-badge__icon"
                            ><i class="fa-solid fa-code"></i
                        ></span>
                        <span
                            >Developed by
                            <strong>{{
                                footerData?.developer_info?.name || "Bitlogicx"
                            }}</strong></span
                        >
                    </button>
                </div>
            </div>
        </div>
    </footer>
</template>

<style scoped>
/* ── Base ── */
.footer-part {
    font-family: "Segoe UI", system-ui, sans-serif;
    color: rgba(255, 255, 255, 0.85);
}

/* ── Noise texture overlay ── */
.footer-noise {
    position: absolute;
    inset: 0;
    background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 200 200' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.75' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)' opacity='0.04'/%3E%3C/svg%3E");
    background-size: 200px 200px;
    pointer-events: none;
    z-index: 1;
    opacity: 0.5;
}

/* ── Diagonal accent ── */
.footer-stripe {
    position: absolute;
    top: 0;
    right: 0;
    width: 55%;
    height: 100%;
    background: linear-gradient(
        135deg,
        transparent 45%,
        rgba(133, 10, 9, 0.18) 45%
    );
    pointer-events: none;
    z-index: 1;
}

/* ── Container ── */
.footer-container {
    width: 100%;
    padding-left: 1.25rem;
    padding-right: 1.25rem;
}

@media (min-width: 640px) {
    .footer-container {
        padding-left: 2rem;
        padding-right: 2rem;
    }
}

@media (min-width: 1300px) {
    .footer-container {
        padding-left: 7rem;
        padding-right: 7rem;
    }
}

@media (min-width: 1800px) {
    .footer-container {
        padding-left: 10rem;
        padding-right: 10rem;
    }
}

/* ── Hero grid ── */
.footer-hero-inner {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 2rem;
    padding-bottom: 2rem;
}

@media (min-width: 768px) {
    .footer-hero-inner {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 2rem;
    }
}

@media (max-width: 767px) {
    .nav-columns {
        grid-template-columns: 1fr;
        text-align: center;
    }

    .nav-col {
        width: 100%;
    }

    .nav-col__links {
        align-items: center;
        justify-content: center;
    }

    .nav-col__link {
        justify-content: center;
        text-align: center;
        width: 100%;
    }

    .nav-col__arrow {
        display: inline-flex;
        align-items: center;
    }
}

@media (min-width: 1100px) {
    .footer-hero-inner {
        grid-template-columns: 1.1fr 1fr 1.3fr;
        gap: 3rem;
    }
}

/* ── Brand block ── */
.brand-logo-link {
    display: block;
    margin-bottom: 1.25rem;
    text-align: center;
}

@media (max-width: 767px) {
    .brand-logo-link {
        display: flex;
        justify-content: center;
        align-items: center;
        width: 100%;
    }

    .brand-logo {
        margin: 0 auto;
    }
}

.brand-logo {
    width: 14rem;
    max-width: 100%;
    height: auto;
    aspect-ratio: 224 / 70;
    drop-shadow: 0 4px 24px rgba(0, 0, 0, 0.4);
}

@media (min-width: 768px) {
    .brand-logo-link {
        text-align: left;
    }
}

.brand-tagline {
    font-size: 0.95rem;
    color: rgba(255, 255, 255, 0.5);
    font-style: italic;
    letter-spacing: 0.04em;
    margin-bottom: 1.5rem;
    line-height: 1.6;
    text-align: center;
}

@media (min-width: 768px) {
    .brand-tagline {
        text-align: left;
    }
}

/* Contact pills */
.contact-pills {
    display: flex;
    flex-direction: column;
    gap: 0.6rem;
    margin-bottom: 1.75rem;
    align-items: center;
    min-height: 5.5rem;
}

@media (min-width: 768px) {
    .contact-pills {
        align-items: flex-start;
        min-height: 80px;
    }
}

.contact-pill {
    display: inline-flex;
    align-items: center;
    gap: 0.6rem;
    font-size: 1.2rem;
    color: #fff;
    text-decoration: none;
    transition: color 0.2s;
}

.contact-pill:hover {
    color: #fff;
}

.contact-pill--phone {
    font-size: 1.1rem;
    font-weight: 700;
    color: #fff;
}

.pill-icon {
    width: 2rem;
    height: 2rem;
    border-radius: 50%;
    background: rgba(133, 10, 9, 0.55);
    border: 1px solid rgba(133, 10, 9, 0.8);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.8rem;
    color: #f87171;
    flex-shrink: 0;
}

/* Socials */
.social-row {
    display: flex;
    gap: 0.65rem;
    flex-wrap: wrap;
    justify-content: center;
    min-height: 2.4rem;
}

@media (min-width: 768px) {
    .social-row {
        justify-content: flex-start;
    }
}

.social-btn {
    width: 2.4rem;
    height: 2.4rem;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid rgba(255, 255, 255, 0.12);
    display: flex;
    align-items: center;
    justify-content: center;
    color: rgba(255, 255, 255, 0.7);
    font-size: 0.95rem;
    text-decoration: none;
    transition:
        background 0.2s,
        border-color 0.2s,
        color 0.2s,
        transform 0.2s;
}

.social-btn:hover {
    background: #850a09;
    border-color: #850a09;
    color: #fff;
    transform: translateY(-2px);
}

/* ── Nav columns ── */
.nav-columns {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
    margin-top: 0;
    text-align: center;
    width: 100%;
}

@media (min-width: 640px) {
    .nav-columns {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 2rem;
        margin-top: 2rem;
        text-align: left;
    }
}

@media (min-width: 1100px) {
    .nav-columns {
        grid-template-columns: 1fr 1fr;
    }
}

.nav-col__heading {
    font-size: 0.7rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.15em;
    color: #f87171;
    margin-bottom: 1.1rem;
    text-align: center;
}

@media (min-width: 768px) {
    .nav-col__heading {
        text-align: left;
    }
}

.nav-col__links {
    display: flex;
    flex-direction: column;
    gap: 0.55rem;
    min-height: 130px;
}

.nav-skeleton {
    height: 1rem;
    width: 70%;
    background: rgba(255, 255, 255, 0.06);
    border-radius: 4px;
}

.nav-col__link {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    color: rgba(255, 255, 255, 0.65);
    text-decoration: none;
    font-size: 0.92rem;
    transition:
        color 0.2s,
        gap 0.2s;
}

.nav-col__link:hover {
    color: #fff;
    gap: 0.75rem;
}

.nav-col__arrow {
    font-size: 0.75rem;
    color: #850a09;
    transition: transform 0.2s;
}

.nav-col__link:hover .nav-col__arrow {
    transform: translateX(3px);
}

/* Hours */
.hours-list {
    list-style: none;
    padding: 0;
    margin: 0;
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.hours-item {
    display: flex;
    justify-content: space-between;
    font-size: 0.88rem;
}

.hours-day {
    color: rgba(255, 255, 255, 0.55);
}

.hours-time {
    color: rgba(255, 255, 255, 0.9);
    font-size: 12px;
    font-weight: 600;
}

/* App badges */
.app-badges__row {
    display: flex;
    gap: 0.75rem;
    flex-wrap: wrap;
    margin-top: 0.75rem;
}

.app-badge {
    width: 7.5rem;
    border-radius: 0.5rem;
    overflow: hidden;
    transition:
        transform 0.2s,
        box-shadow 0.2s;
    display: block;
}

.app-badge:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.4);
}

.app-badge img {
    width: 100%;
    height: auto;
    aspect-ratio: 135 / 40;
    display: block;
    border-radius: 0.5rem;
}

/* ── Subscribe block ── */
.subscribe-block {
    background: rgba(255, 255, 255, 0.03);
    border: 1px solid rgba(255, 255, 255, 0.08);
    border-radius: 1rem;
    padding: 1.5rem;
}

@media (min-width: 768px) {
    .subscribe-block {
        border-radius: 1.25rem;
        padding: 2rem;
    }
}

.subscribe-label {
    font-size: 0.6rem;
    text-transform: uppercase;
    letter-spacing: 0.18em;
    color: #f87171;
    font-weight: 700;
    margin-bottom: 0.4rem;
}

@media (min-width: 768px) {
    .subscribe-label {
        font-size: 0.68rem;
        margin-bottom: 0.5rem;
    }
}

.subscribe-heading {
    font-size: 1.25rem;
    font-weight: 800;
    color: #fff;
    margin-bottom: 0.3rem;
    line-height: 1.2;
}

@media (min-width: 768px) {
    .subscribe-heading {
        font-size: 1.6rem;
        margin-bottom: 0.4rem;
    }
}

.subscribe-sub {
    font-size: 0.8rem;
    color: rgba(255, 255, 255, 0.5);
    margin-bottom: 1rem;
    line-height: 1.5;
}

@media (min-width: 768px) {
    .subscribe-sub {
        font-size: 0.85rem;
        margin-bottom: 1.5rem;
        line-height: 1.6;
    }
}

.subscribe-form {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.subscribe-input-wrap {
    position: relative;
    display: flex;
    align-items: center;
}

.subscribe-input-icon {
    position: absolute;
    left: 1rem;
    color: rgba(255, 255, 255, 0.35);
    font-size: 0.9rem;
    pointer-events: none;
}

.subscribe-input {
    width: 100%;
    height: 3rem;
    padding-left: 2.75rem;
    padding-right: 1rem;
    border-radius: 0.6rem;
    background: rgba(255, 255, 255, 0.06);
    border: 1px solid rgba(255, 255, 255, 0.12);
    color: #fff;
    font-size: 0.9rem;
    outline: none;
    transition:
        border-color 0.2s,
        background 0.2s;
}

.subscribe-input::placeholder {
    color: rgba(255, 255, 255, 0.35);
}

.subscribe-input:focus {
    border-color: #850a09;
    background: rgba(133, 10, 9, 0.08);
}

.subscribe-btn {
    height: 3rem;
    border-radius: 0.6rem;
    background: #850a09;
    border: none;
    color: #fff;
    font-weight: 700;
    font-size: 0.88rem;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.6rem;
    transition:
        background 0.2s,
        transform 0.15s;
}

.subscribe-btn:hover {
    background: #a80c0b;
    transform: translateY(-1px);
}

.subscribe-btn:active {
    transform: translateY(0);
}

/* Trust badges */
.trust-row {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
    margin-top: 1rem;
}

.trust-badge {
    font-size: 0.72rem;
    color: rgba(255, 255, 255, 0.4);
    display: flex;
    align-items: center;
    gap: 0.3rem;
}

.trust-badge i {
    color: rgba(133, 10, 9, 0.8);
}

/* ── Chef band ── */
.chef-band {
    position: relative;
    background: rgba(133, 10, 9, 0.18);
    border-top: 1px solid rgba(133, 10, 9, 0.3);
    border-bottom: 1px solid rgba(133, 10, 9, 0.3);
    overflow: visible;
    margin-top: 1rem;
    min-height: 120px;
    padding: 1.5rem 0;
}

@media (min-width: 768px) {
    .chef-band {
        padding: 2.5rem 0;
    }
}

.chef-band__inner {
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    gap: 1.5rem;
    position: relative;
    z-index: 2;
}

@media (min-width: 768px) {
    .chef-band__inner {
        flex-direction: row;
        align-items: center;
        gap: 2rem;
    }
}

@media (min-width: 1100px) {
    .chef-band__inner {
        gap: 3rem;
    }
}

.chef-band__copy {
    flex: 1;
    min-width: 200px;
}

.chef-band__eyebrow {
    display: block;
    font-size: 0.65rem;
    text-transform: uppercase;
    letter-spacing: 0.2em;
    color: #f87171;
    font-weight: 700;
    margin-bottom: 0.4rem;
}

@media (min-width: 768px) {
    .chef-band__eyebrow {
        font-size: 0.7rem;
    }
}

.chef-band__text {
    font-size: 0.9rem;
    color: rgba(255, 255, 255, 0.6);
    line-height: 1.5;
    max-width: 100%;
}

@media (min-width: 768px) {
    .chef-band__text {
        font-size: 1rem;
        max-width: 28rem;
    }
}

.chef-band__divider {
    width: 100%;
    height: 1px;
    background: rgba(255, 255, 255, 0.12);
    flex-shrink: 0;
}

@media (min-width: 768px) {
    .chef-band__divider {
        width: 1px;
        height: 4rem;
    }
}

.chef-band__stats {
    display: flex;
    gap: 1.5rem;
    flex-wrap: wrap;
    padding-right: 0;
    width: 100%;
    justify-content: center;
}

@media (min-width: 768px) {
    .chef-band__stats {
        gap: 2rem;
    }
}

@media (min-width: 1100px) {
    .chef-band__stats {
        padding-right: 22rem;
        justify-content: flex-end;
    }
}

.stat {
    text-align: left;
}

@media (min-width: 768px) {
    .stat {
        text-align: center;
    }
}

.stat__num {
    display: block;
    font-size: 1.5rem;
    font-weight: 900;
    color: #fff;
    line-height: 1;
    letter-spacing: -0.02em;
}

@media (min-width: 768px) {
    .stat__num {
        font-size: 2rem;
    }
}

.stat__label {
    font-size: 0.65rem;
    text-transform: uppercase;
    letter-spacing: 0.12em;
    color: rgba(255, 255, 255, 0.45);
    margin-top: 0.25rem;
    display: block;
}

@media (min-width: 768px) {
    .stat__label {
        font-size: 0.72rem;
        margin-top: 0.3rem;
    }
}

/* Chef image — breaks out of band at bottom-right */
.chef-img {
    display: none;
}

@media (min-width: 1100px) {
    .chef-img {
        display: block;
        position: absolute;
        bottom: 0;
        right: 2rem;
        height: 110%;
        width: auto;
        max-width: 16rem;
        aspect-ratio: 1 / 1;
        object-fit: contain;
        object-position: bottom;
        pointer-events: none;
        z-index: 3;
    }
}

@media (min-width: 1300px) {
    .chef-img {
        right: 4rem;
        max-width: 20rem;
    }
}

@media (min-width: 1800px) {
    .chef-img {
        right: 10rem;
        max-width: 26rem;
    }
}

/* ── Copyright ── */
.footer-copy {
    background: rgba(0, 0, 0, 0.35);
    padding: 1.25rem 0;
}

.copy-inner {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 0.75rem;
}

.copy-text {
    font-size: 0.8rem;
    color: rgba(255, 255, 255, 0.4);
}

/* ── Bitlogicx badge ── */
.bitlogicx-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 3px 12px 3px 8px;
    border-radius: 999px;
    border: 1px solid rgba(255, 255, 255, 0.15);
    background: rgba(255, 255, 255, 0.06);
    color: rgba(255, 255, 255, 0.7);
    font-size: 0.75rem;
    font-weight: 400;
    letter-spacing: 0.01em;
    text-decoration: none;
    backdrop-filter: blur(6px);
    transition:
        background 0.25s,
        color 0.2s,
        transform 0.2s;
    white-space: nowrap;
}

.bitlogicx-badge strong {
    font-weight: 700;
    color: #fff;
}

.bitlogicx-badge__icon {
    font-size: 0.85rem;
    filter: drop-shadow(0 0 4px rgba(255, 200, 60, 0.7));
    animation: zap-pulse 2.4s ease-in-out infinite;
}

@keyframes zap-pulse {
    0%,
    100% {
        opacity: 1;
        transform: scale(1);
    }

    50% {
        opacity: 0.7;
        transform: scale(1.2);
    }
}

.bitlogicx-badge:hover {
    background: rgba(255, 255, 255, 0.12);
    color: #fff;
    transform: translateY(-1px);
}

.subscribe-chef {
    margin-top: 2rem;
}

/* Hide newsletter on mobile */
@media (max-width: 767px) {
    .subscribe-chef {
        display: none;
    }
}

@media (min-width: 768px) {
    .subscribe-chef {
        display: block;
    }
}

/* Mobile responsive adjustments */
@media (max-width: 767px) {
    .brand-logo {
        width: 10rem;
    }

    .brand-tagline {
        font-size: 0.85rem;
    }

    .contact-pill {
        font-size: 0.95rem;
    }

    .contact-pill--phone {
        font-size: 0.9rem;
    }

    .pill-icon {
        width: 1.75rem;
        height: 1.75rem;
        font-size: 0.7rem;
    }

    .social-btn {
        width: 2rem;
        height: 2rem;
        font-size: 0.85rem;
    }

    .nav-col__heading {
        font-size: 0.65rem;
    }

    .nav-col__link {
        font-size: 0.85rem;
    }

    .hours-item {
        font-size: 0.8rem;
    }

    .app-badge {
        width: 6rem;
    }

    .footer-copy {
        padding-bottom: 4rem;
    }
}

/* Copyright mobile */
@media (max-width: 480px) {
    .copy-inner {
        flex-direction: column;
        text-align: center;
        gap: 0.5rem;
    }
}
</style>
<script>
import { useRouter } from "vue-router";
import { computed, ref, onMounted, getCurrentInstance } from "vue";
import statusEnum from "../../../enums/modules/statusEnum";
import menuSectionEnum from "../../../enums/modules/menuSectionEnum";
import axios from "axios";
import alertService from "../../../services/alertService";
import LoadingComponent from "../../frontend/components/LoadingComponent";

export default {
    name: "FrontendFooterComponent",
    components: { LoadingComponent },
    setup() {
        const router = useRouter();
        const instance = getCurrentInstance();
        const proxy = instance.proxy;
        const loading = ref({ isActive: false });
        const subscriptionProps = ref({ post: { email: "" } });

        const setting = computed(
            () => proxy.$store.getters["frontendSetting/lists"],
        );
        const pages = computed(
            () => proxy.$store.getters["frontendPage/lists"],
        );

        const footerData = computed(
            () => proxy.$store.getters["frontendFooterData/lists"],
        );

        const resolvePageRoute = (page) => {
            const namedRoute = `frontend.${page.slug}`;
            if (router.hasRoute(namedRoute)) {
                return { name: namedRoute };
            }
            return {
                name: "frontend.page",
                params: { slug: page.slug },
            };
        };

        // ─── Navigation ───────────────────────────────────────────────────────────
        const openDeveloperLink = () => {
            const url =
                footerData.value?.developer_info?.url ||
                "https://bitlogicx.com";
            window.open(url, "_blank", "noopener,noreferrer");
        };

        onMounted(() => {
            proxy.$store.dispatch("frontendFooterData/lists").catch(() => {});

            if (pages.value.length === 0) {
                loading.value.isActive = true;
                proxy.$store
                    .dispatch("frontendPage/lists", {
                        paginate: 0,
                        order_column: "id",
                        order_type: "asc",
                        menu_section_id: menuSectionEnum.FOOTER,
                        status: statusEnum.ACTIVE,
                    })
                    .then(() => {
                        loading.value.isActive = false;
                    })
                    .catch(() => {
                        loading.value.isActive = false;
                    });
            }
        });

        const saveSubscription = () => {
            const url = "/frontend/subscriber";
            loading.value.isActive = true;
            axios
                .post(url, subscriptionProps.value.post)
                .then(() => {
                    loading.value.isActive = false;
                    subscriptionProps.value.post.email = "";
                    alertService.success(proxy.$t("message.subscribe"));
                })
                .catch((err) => {
                    if (typeof err.response.data.errors === "object") {
                        _.forEach(err.response.data.errors, (error) => {
                            alertService.error(error[0]);
                        });
                    }
                    loading.value.isActive = false;
                });
        };

        return {
            loading,
            subscriptionProps,
            setting,
            pages,
            footerData,
            resolvePageRoute,
            openDeveloperLink,
            saveSubscription,
        };
    },
};
</script>
