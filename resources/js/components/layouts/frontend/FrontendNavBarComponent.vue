<template>
    <LoadingComponent :props="loading" />

    <!-- ✅ Premium Header with Glass Morphism -->
    <header
        class="sticky top-0 z-50 bg-white/90 backdrop-blur-xl border-b border-white/20 shadow-2xl shadow-black/5 transition-all duration-500"
        ref="ffHeader">
        <div class="container mx-auto">

            <!-- ✅ Mobile Layout (Minimal & Elegant) -->
            <div class="w-full flex items-center justify-between lg:hidden py-4">
                <!-- Mobile Logo -->
                <router-link :to="{ name: 'frontend.home' }" class="flex-shrink-0 group">
                    <img class="h-14 w-auto object-contain transition-transform duration-300 group-hover:scale-105"
                        :src="setting.theme_logo" alt="logo">
                </router-link>

                <!-- Mobile Actions -->
                <div class="flex items-center gap-2">
                    <!-- Mobile Online Order -->
                    <!-- <button @click="goToOnlineOrder()"
                        class="flex items-center gap-1.5 px-3 h-10 rounded-xl bg-primary to-primary-dark text-white font-semibold text-xs hover:shadow-lg hover:shadow-primary/30 transition-all duration-300 whitespace-nowrap">
                        <i class="lab lab-bag-2 transition lab-font-size-14"></i>
                        <span>Online Order</span>
                    </button> -->

                    <!-- Mobile Search -->
                    <button type="button" @click="showMobileSearch = true" aria-label="Search"
                        class="flex items-center justify-center w-10 h-10 rounded-xl bg-gradient-to-br from-white to-gray-50 text-gray-600 hover:text-primary border border-gray-100 hover:border-primary/30 hover:shadow-lg hover:shadow-primary/10 transition-all duration-300">
                        <i class="lab lab-search-normal text-lg"></i>
                    </button>

                    <!-- Mobile Cart -->
                    <button @click.prevent="openCanvas('cart')" type="button" aria-label="Cart"
                        class="relative flex items-center justify-center w-10 h-10 rounded-xl bg-primary to-primary-dark text-white hover:shadow-lg hover:shadow-primary/30 hover:-translate-y-0.5 transition-all duration-300 group">
                        <i class="lab lab-bag-2 text-lg group-hover:scale-110 transition-transform duration-300"></i>
                        <span v-if="cartCount > 0"
                            class="absolute -top-1 -right-1 flex items-center justify-center w-5 h-5 bg-red-500 text-white text-[10px] font-bold rounded-full border-2 border-white animate-pulse">
                            {{ cartCount }}
                        </span>
                        <span v-if="cartCount > 0"
                            class="absolute inset-0 rounded-xl bg-primary/30 animate-ping opacity-75"></span>
                    </button>
                </div>
            </div>

            <!-- ✅ Mobile Menu (Slide-in) -->
            <div v-if="mobileMenuOpen && !logged"
                class="lg:hidden fixed inset-x-0 top-[72px] bg-white/95 backdrop-blur-xl border-t border-gray-100 shadow-2xl z-40 transition-all duration-500 animate-slide-down">
                <div class="container mx-auto px-4 py-6">
                    <nav class="flex flex-col space-y-1">
                        <router-link v-for="(item, index) in mobileMenuItems" :key="index" :to="item.to"
                            @click="mobileMenuOpen = false"
                            :class="['flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition-all duration-300',
                                checkIsPathAndRoutePathSame(item.path) ? 'bg-primary/10 text-primary' : 'text-gray-600 hover:bg-gray-50 hover:text-primary']">
                            <i :class="item.icon" class="text-base"></i>
                            {{ item.label }}
                        </router-link>

                        <!-- Online Order Button -->
                        <!-- <button @click="mobileMenuOpen = false; goToOnlineOrder()"
                            class="mt-2 flex items-center justify-center gap-2 px-4 py-3 rounded-xl bg-primary to-primary-dark text-white font-semibold text-sm hover:shadow-lg hover:shadow-primary/30 transition-all duration-300 w-full">
                            <i class="lab lab-bag-2 transition lab-font-size-14"></i>
                            Online Order
                        </button> -->

                        <!-- Login Button -->
                        <router-link :to="{ name: 'auth.login' }" @click="mobileMenuOpen = false"
                            class="mt-2 flex items-center justify-center gap-2 px-4 py-3 rounded-xl bg-primary text-white font-semibold text-sm hover:shadow-lg hover:shadow-primary/30 transition-all duration-300">
                            <i class="lab lab-profile-circle text-base"></i>
                            {{ $t('label.login') }}
                        </router-link>
                    </nav>
                </div>
            </div>

            <!-- ✅ Desktop Layout (Premium Split Design) -->
            <div class="hidden lg:flex items-center justify-between py-4 gap-6 xl:gap-8">

                <!-- Left: Logo with Subtle Glow -->
                <div class="flex-shrink-0 relative z-20">
                    <router-link :to="{ name: 'frontend.home' }" class="block group relative">
                        <div
                            class="absolute inset-0 bg-gradient-to-r from-primary/0 via-primary/5 to-primary/0 blur-xl opacity-0 group-hover:opacity-100 transition-opacity duration-700">
                        </div>
                        <img class="h-[3.5rem] w-auto object-contain relative z-10 transition-all duration-700 group-hover:scale-[1.02] group-hover:drop-shadow-lg"
                            alt="logo" :src="setting.theme_logo" width="448" height="140">
                    </router-link>
                </div>

                <!-- Center: Navigation Menu -->
                <nav class="flex-1 flex items-center justify-center min-w-0">
                    <div
                        class="desktop-nav-list flex items-center justify-center gap-1 bg-white/50 backdrop-blur-sm px-4 xl:px-5 py-2 rounded-2xl border border-gray-100 shadow-inner max-w-full overflow-hidden">

                        <router-link v-for="(item, index) in navItems" :key="index" :to="item.to" :class="[
                            'desktop-nav-link relative px-2.5 xl:px-4 py-3 text-[13px] xl:text-sm font-semibold rounded-xl transition-all duration-300 overflow-hidden group whitespace-nowrap',
                            checkIsPathAndRoutePathSame(item.path)
                                ? 'text-primary bg-white shadow-lg shadow-primary/10'
                                : 'text-gray-600 hover:text-primary hover:bg-white/80'
                        ]">

                            <div v-if="!checkIsPathAndRoutePathSame(item.path)"
                                class="absolute inset-0 bg-gradient-to-r from-primary/0 via-primary/5 to-primary/0 translate-x-[-100%] group-hover:translate-x-[100%] transition-transform duration-700">
                            </div>

                            <span class="relative z-10 flex items-center gap-2 whitespace-nowrap">
                                {{ item.label }}
                                <i v-if="item.icon" :class="item.icon"
                                    class="text-xs opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                </i>
                            </span>

                            <div v-if="checkIsPathAndRoutePathSame(item.path)"
                                class="absolute bottom-0 left-1/2 transform -translate-x-1/2 w-6 h-1 bg-gradient-to-r from-primary to-primary-dark rounded-full">
                            </div>
                        </router-link>
                    </div>
                </nav>

                <!-- Right: Actions Bar -->
                <div class="flex items-center gap-3">

                    <!-- Enhanced Search Bar -->
                    <div class="relative dropdown-group">
                        <button @click="toggleSearch"
                            class="flex items-center justify-center w-10 h-10 rounded-xl bg-gradient-to-br from-white to-gray-50 text-gray-600 border border-gray-100 hover:border-primary/30 hover:text-primary hover:shadow-lg hover:shadow-primary/10 transition-all duration-300" title="Search item">
                            <i class="lab lab-search-normal text-lg"></i>
                        </button>

                        <!-- Search Dropdown -->
                        <div v-if="showSearchDropdown"
                            class="absolute top-full right-0 mt-3 w-96 bg-white/95 backdrop-blur-xl rounded-2xl border border-gray-100 shadow-2xl shadow-black/10 z-50 overflow-hidden animate-fade-in">
                            <div class="p-4 border-b border-gray-100">
                                <div class="relative">
                                    <i
                                        class="lab lab-search-normal absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400 text-lg"></i>
                                    <input type="search" v-model="searchItem"
                                        :placeholder="$t('button.search_products')" autofocus @input="handleSearchInput"
                                        class="w-full h-12 pl-12 pr-10 rounded-xl bg-gray-50 border border-gray-200 focus:bg-white focus:border-primary focus:ring-2 focus:ring-primary/20 text-sm font-medium transition-all">
                                    <button v-if="searchItem" @click.prevent="searchReset"
                                        class="absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-red-500 transition-colors">
                                        <i class="lab lab-close-circle-line text-lg"></i>
                                    </button>
                                </div>
                            </div>

                            <!-- Search Results -->
                            <div v-if="searchLoading" class="p-8 text-center">
                                <div class="inline-flex items-center gap-2 text-primary">
                                    <i class="lab lab-loading animate-spin text-xl"></i>
                                    <span class="text-sm font-medium">Searching...</span>
                                </div>
                            </div>

                            <div v-else-if="searchResults.length > 0" class="max-h-96 overflow-y-auto">
                                <div class="p-2">
                                    <div v-for="item in searchResults" :key="item.id"
                                        @mousedown.prevent="selectSearchItem(item)"
                                        class="group flex items-center gap-3 p-3 rounded-xl hover:bg-gradient-to-r hover:from-primary/5 hover:to-primary/10 cursor-pointer transition-all duration-300 border border-transparent hover:border-primary/20">
                                        <div class="relative">
                                            <img :src="item.thumb" :alt="item.name"
                                                class="w-14 h-14 object-cover rounded-lg border border-gray-100 group-hover:scale-105 transition-transform duration-300">
                                            <div v-if="item.offer && item.offer.length > 0"
                                                class="absolute -top-1 -right-1 bg-red-500 text-white text-[10px] font-bold px-1.5 py-0.5 rounded-full">
                                                OFFER
                                            </div>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <h4 class="text-sm font-bold text-heading truncate mb-1">{{ item.name }}
                                            </h4>
                                            <div class="flex items-center gap-2">
                                                <span v-if="item.offer && item.offer.length > 0"
                                                    class="text-xs font-bold text-primary">
                                                    {{ item.offer[0].currency_price }}
                                                </span>
                                                <span v-else class="text-xs font-bold text-primary">
                                                    {{ item.currency_price }}
                                                </span>
                                                <span v-if="item.offer && item.offer.length > 0"
                                                    class="text-xs text-gray-400 line-through">
                                                    {{ item.currency_price }}
                                                </span>
                                            </div>
                                        </div>
                                        <i
                                            class="lab lab-arrow-right-3 text-gray-400 group-hover:text-primary transform group-hover:translate-x-1 transition-all duration-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Language Selector -->
                    <div v-if="setting.site_language_switch === enums.activityEnum.ENABLE && languages.length > 1"
                        class="relative dropdown-group">
                        <button
                            class="flex items-center justify-center w-10 h-10 rounded-xl bg-gradient-to-br from-white to-gray-50 text-gray-600 border border-gray-100 hover:border-primary/30 hover:text-primary hover:shadow-lg hover:shadow-primary/10 transition-all duration-300 dropdown-btn">
                            <img :src="language.image" alt="flag" class="w-5 h-5 rounded-full object-cover">
                        </button>
                        <div
                            class="dropdown-list absolute top-full right-0 mt-3 w-48 bg-white/95 backdrop-blur-xl rounded-2xl border border-gray-100 shadow-2xl shadow-black/10 z-50 overflow-hidden">
                            <div class="p-3 border-b border-gray-100">
                                <h4 class="text-xs font-bold text-gray-500 uppercase tracking-wider">{{
                                    $t('label.language') }}</h4>
                            </div>
                            <div class="p-2">
                                <button @click="changeLanguage(lang.id, lang.code)" v-for="lang in languages"
                                    :key="lang.id" :class="['flex items-center gap-3 w-full px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-300',
                                        language.code === lang.code
                                            ? 'bg-primary/10 text-primary'
                                            : 'text-gray-600 hover:bg-gray-50']">
                                    <img :src="lang.image" alt="flag"
                                        class="w-5 h-5 rounded-full border border-gray-200">
                                    <span class="flex-1 text-left">{{ lang.name }}</span>
                                    <i v-if="language.code === lang.code" class="lab lab-check-circle text-primary"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Cart with Badge -->
                    <button @click.prevent="openCanvas('cart')" type="button" aria-label="Cart"
                        class="relative flex items-center justify-center w-10 h-10 rounded-xl bg-primary to-primary-dark text-white hover:shadow-lg hover:shadow-primary/30 hover:-translate-y-0.5 transition-all duration-300 group">
                        <i class="lab lab-bag-2 text-lg group-hover:scale-110 transition-transform duration-300"></i>
                        <span v-if="cartCount > 0"
                            class="absolute -top-1 -right-1 flex items-center justify-center w-5 h-5 bg-red-500 text-white text-[10px] font-bold rounded-full border-2 border-white animate-pulse">
                            {{ cartCount }}
                        </span>
                        <span v-if="cartCount > 0"
                            class="absolute inset-0 rounded-xl bg-primary/30 animate-ping opacity-75"></span>
                    </button>

                    <!-- Notification Bell -->
                    <div v-if="logged" class="relative" ref="notifDropdown">
                        <button aria-label="Notifications"
                            class="relative flex items-center justify-center w-10 h-10 rounded-xl bg-gradient-to-br from-white to-gray-50 text-gray-600 border border-gray-100 hover:border-primary/30 hover:text-primary hover:shadow-lg hover:shadow-primary/10 transition-all duration-300"
                            @click.stop="toggleNotifDropdown" title="Notifications">
                            <i class="fa-solid fa-bell" :class="{ 'blinking': unreadCount > 0 }"></i>
                            <span v-if="unreadCount > 0"
                                class="absolute -top-1.5 -right-1.5 flex items-center justify-center w-5 h-5 rounded-full bg-primary text-white text-[10px] font-medium">
                                {{ unreadCount > 9 ? '9+' : unreadCount }}
                            </span>
                        </button>

                        <div v-show="notifOpen"
                            class="fixed sm:absolute top-[75px] sm:top-12 ltr:right-0 rtl:left-0 z-[60] w-full sm:w-[320px] rounded-xl shadow-paper bg-white border border-[#EFF0F6] overflow-hidden">
                            <div class="flex items-center justify-between px-4 py-3 border-b border-[#EFF0F6]">
                                <span class="text-sm font-medium text-heading">{{ $t('label.notifications') }}</span>
                                <button @click="markAllRead" :disabled="notifLoading || notifications.length === 0"
                                    :class="{ 'opacity-50 cursor-not-allowed': notifLoading || notifications.length === 0 }"
                                    class="text-xs text-primary">
                                    {{ notifLoading ? $t('button.loading') : $t('button.mark_all_read') }}
                                </button>
                            </div>

                            <ul class="max-h-[300px] overflow-y-auto">
                                <li v-if="notifLoading" class="px-4 py-8 text-center">
                                    <div class="inline-block">
                                        <div
                                            class="animate-spin rounded-full h-6 w-6 border-2 border-primary border-t-transparent">
                                        </div>
                                    </div>
                                </li>
                                <li v-else-if="notifications.length === 0"
                                    class="px-4 py-8 text-center text-xs text-gray-400">
                                    {{ $t('message.no_notifications') }}
                                </li>
                                <li v-for="notif in notifications" :key="notif.id" @click="readNotification(notif)"
                                    class="flex items-start gap-3 px-4 py-3 border-b last:border-none border-[#EFF0F6] cursor-pointer transition hover:bg-gray-50"
                                    :class="{ 'opacity-60': readingNotifications[notif.id], 'bg-blue-50': !notif.read_at }">
                                    <span class="mt-1.5 w-2 h-2 rounded-full flex-shrink-0"
                                        :class="notif.read_at ? 'bg-gray-200' : 'bg-primary'"></span>
                                    <div class="flex-1">
                                        <p class="text-xs text-heading leading-5">{{ notif.data?.message ||
                                            'Notification' }}</p>
                                        <p class="text-[11px] text-gray-400 mt-0.5">{{ timeAgo(notif.created_at) }}</p>
                                    </div>
                                    <div v-if="readingNotifications[notif.id]" class="animate-spin">
                                        <i class="fa-solid fa-circle-notch fa-xs text-gray-400"></i>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <!-- Online Order Button -->
                    <!-- <button @click="goToOnlineOrder()"
                        class="flex items-center gap-2 px-4 py-2.5 rounded-xl bg-primary to-primary-dark text-white font-semibold text-sm hover:shadow-lg hover:shadow-primary/30 hover:-translate-y-0.5 transition-all duration-300 group whitespace-nowrap">
                        <i class="lab lab-bag-2 transition lab-font-size-14 group-hover:scale-110 transition-transform duration-300"></i>
                        <span>Online Order</span>
                    </button> -->

                    <!-- User Profile / Authentication -->
                    <template v-if="!logged">
                        <router-link :to="{ name: 'auth.login' }"
                            class="flex items-center gap-2 px-5 py-2.5 rounded-xl bg-primary text-white font-semibold text-sm hover:shadow-lg hover:shadow-primary/30 hover:-translate-y-0.5 transition-all duration-300 group">
                            <i
                                class="lab lab-profile-circle text-base group-hover:rotate-12 transition-transform duration-300"></i>
                            <span>{{ $t('label.login') }}</span>
                        </router-link>
                    </template>
                    <template v-else>
                        <div class="relative dropdown-group">
                            <button
                                class="flex items-center gap-2 px-3 py-2 rounded-xl bg-gradient-to-br from-white to-gray-50 border border-gray-100 hover:border-primary/30 hover:shadow-lg hover:shadow-primary/10 transition-all duration-300 group dropdown-btn" title="Profile">
                                <div class="relative">
                                    <img :src="profile.image" alt="avatar"
                                        class="w-8 h-8 rounded-full object-cover border-2 border-white shadow-sm group-hover:scale-110 transition-transform duration-300">
                                    <div
                                        class="absolute bottom-0 right-0 w-2.5 h-2.5 bg-green-500 rounded-full border border-white">
                                    </div>
                                </div>
                                <!-- <span class="text-sm font-semibold text-heading max-w-[100px] truncate">{{ profile.name
                                    }}</span>
                                <i
                                    class="lab lab-arrow-down-2 text-[10px] text-gray-400 transform transition-transform duration-300"></i> -->
                            </button>

                            <!-- Premium Dropdown — uses .dropdown-list + hover CSS (not click-based) -->
                            <div
                                class="dropdown-list absolute top-full right-0 mt-3 w-64 bg-white/95 backdrop-blur-xl rounded-2xl border border-gray-100 shadow-2xl shadow-black/10 z-50 overflow-hidden">
                                <!-- Profile Header -->
                                <div
                                    class="bg-gradient-to-r from-primary/5 to-primary/10 p-4 border-b border-gray-100/50">
                                    <div class="flex items-center gap-3">
                                        <div class="relative">
                                            <img class="w-12 h-12 rounded-full object-cover border-2 border-white shadow-lg"
                                                :src="profile.image" alt="avatar">
                                            <div
                                                class="absolute bottom-0 right-0 w-3 h-3 bg-green-500 rounded-full border-2 border-white">
                                            </div>
                                        </div>
                                        <div class="flex-1 overflow-hidden">
                                            <h3 class="text-sm font-bold text-heading truncate">{{ profile.name }}</h3>
                                            <p class="text-xs text-gray-500 truncate">{{ profile.email }}</p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Menu Items -->
                                <nav class="p-2">
                                    <router-link
                                        v-if="profile.role_id !== enums.roleEnum.CUSTOMER && Object.keys(authDefaultPermission).length > 0"
                                        :to="{ path: '/admin/' + defaultMenu?.url }"
                                        class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-gray-600 hover:text-primary hover:bg-gradient-to-r hover:from-primary/5 hover:to-primary/10 transition-all duration-300 group">
                                        <i
                                            :class="[defaultMenu?.icon, 'group-hover:scale-110 transition-transform duration-300']"></i>
                                        <span>{{ $t('menu.' + defaultMenu?.language) }}</span>
                                    </router-link>

                                    <router-link v-for="item in userMenuItems" :key="item.to" :to="item.to"
                                        class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-gray-600 hover:text-primary hover:bg-gradient-to-r hover:from-primary/5 hover:to-primary/10 transition-all duration-300 group">
                                        <i
                                            :class="[item.icon, 'group-hover:scale-110 transition-transform duration-300']"></i>
                                        <span>{{ item.label }}</span>
                                    </router-link>

                                    <div
                                        class="h-px bg-gradient-to-r from-transparent via-gray-200 to-transparent my-1 mx-3">
                                    </div>

                                    <button @click="logout()"
                                        class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-red-500 hover:bg-gradient-to-r hover:from-red-50 hover:to-red-100 transition-all duration-300 group">
                                        <i
                                            class="lab lab-logout group-hover:scale-110 transition-transform duration-300"></i>
                                        {{ $t('button.logout') }}
                                    </button>
                                </nav>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </header>

    <!-- ✅ Mobile Search Modal -->
    <transition name="slide-fade">
        <div v-if="showMobileSearch" class="fixed inset-0 z-[100] bg-black/50 backdrop-blur-md lg:hidden"
            @click.self="showMobileSearch = false">
            <div class="absolute top-0 inset-x-0 bg-white rounded-b-3xl shadow-2xl animate-slide-down">
                <div class="p-6">
                    <!-- Header -->
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-bold text-heading">{{ $t('label.search') }}</h3>
                        <button @click="showMobileSearch = false"
                            class="w-10 h-10 flex items-center justify-center rounded-xl bg-gray-100 text-gray-500 hover:text-red-500 hover:bg-red-50 transition-colors">
                            <i class="lab lab-close-circle-line text-xl"></i>
                        </button>
                    </div>

                    <!-- Search Input -->
                    <div class="relative mb-6">
                        <div class="relative">
                            <i
                                class="lab lab-search-normal absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400 text-lg"></i>
                            <input type="search" v-model="searchItem" :placeholder="$t('button.search_products')"
                                autofocus @input="handleSearchInput"
                                class="w-full h-14 pl-12 pr-10 rounded-xl bg-gray-50 border-2 border-gray-200 focus:bg-white focus:border-primary focus:ring-2 focus:ring-primary/20 text-heading text-base font-medium transition-all"
                                @keyup.enter="search">
                            <button v-if="searchItem" @click.prevent="searchReset"
                                class="absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-red-500 transition-colors">
                                <i class="lab lab-close-circle-line text-lg"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Search Results -->
                    <div v-if="searchLoading" class="py-12 text-center">
                        <div class="inline-flex flex-col items-center gap-3">
                            <i class="lab lab-loading animate-spin text-3xl text-primary"></i>
                            <span class="text-sm text-gray-500">Searching delicious items...</span>
                        </div>
                    </div>

                    <div v-else-if="searchResults.length > 0" class="space-y-3 pb-6">
                        <h4 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-3">{{ $t('label.results')
                        }}
                        </h4>
                        <div v-for="item in searchResults" :key="item.id" @click="selectSearchItem(item)"
                            class="flex items-center gap-4 p-4 bg-gradient-to-r from-white to-gray-50 border border-gray-100 rounded-2xl shadow-sm active:scale-[0.98] transition-all duration-300">
                            <div class="relative">
                                <img :src="item.thumb" :alt="item.name"
                                    class="w-20 h-20 object-cover rounded-xl shadow-sm">
                                <div v-if="item.offer && item.offer.length > 0"
                                    class="absolute -top-2 -right-2 bg-gradient-to-r from-red-500 to-red-600 text-white text-[10px] font-bold px-2 py-1 rounded-full shadow-lg">
                                    SALE
                                </div>
                            </div>
                            <div class="flex-1 min-w-0">
                                <h4 class="text-base font-bold text-heading truncate mb-1">{{ item.name }}</h4>
                                <div class="flex items-center gap-2">
                                    <span class="text-lg font-bold text-primary">
                                        {{ (item.offer && item.offer.length > 0) ? item.offer[0].currency_price :
                                            item.currency_price }}
                                    </span>
                                    <span v-if="item.offer && item.offer.length > 0"
                                        class="text-sm text-gray-400 line-through">
                                        {{ item.currency_price }}
                                    </span>
                                </div>
                                <p class="text-xs text-gray-500 mt-1 truncate">{{ item.description?.substring(0, 60)
                                }}...
                                </p>
                            </div>
                        </div>
                    </div>

                    <div v-else-if="searchItem && !searchLoading" class="py-12 text-center">
                        <div class="inline-flex flex-col items-center gap-3">
                            <i class="lab lab-search-status text-4xl text-gray-300"></i>
                            <div>
                                <p class="text-gray-500 font-medium mb-1">{{ $t('message.no_items_found') }}</p>
                                <p class="text-xs text-gray-400">Try different keywords</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </transition>

    <!-- ✅ Order Notification Modal -->
    <div id="order" v-if="orderNotificationStatus" ref="orderNotificationModal"
        class="modal active backdrop-blur-sm bg-black/50">
        <div class="modal-dialog max-w-md p-0 overflow-hidden relative rounded-3xl shadow-2xl">
            <div class="absolute inset-0 bg-primary/10 via-white to-primary/5"></div>

            <div class="relative z-10 p-8 text-center">
                <button @click.prevent="closeOrderNotificationModal"
                    class="absolute top-4 right-4 w-10 h-10 flex items-center justify-center rounded-full bg-white/80 text-gray-400 hover:text-red-500 hover:bg-white transition-all duration-300 shadow-sm">
                    <i class="fa-regular fa-circle-xmark text-xl"></i>
                </button>

                <div class="relative w-20 h-20 mx-auto mb-6">
                    <div
                        class="absolute inset-0 bg-gradient-to-r from-primary to-primary-dark rounded-full opacity-20 animate-ping">
                    </div>
                    <div
                        class="relative w-full h-full bg-gradient-to-r from-primary to-primary-dark rounded-full flex items-center justify-center text-white shadow-lg">
                        <i class="lab lab-notification text-3xl"></i>
                    </div>
                </div>

                <h3
                    class="text-2xl font-bold text-heading mb-2 bg-gradient-to-r from-primary to-primary-dark bg-clip-text text-transparent">
                    Order Update!
                </h3>
                <p class="text-gray-600 mb-6 leading-relaxed">
                    {{ orderNotificationMessage }}
                    <span class="block text-sm text-gray-500 mt-2">{{ $t('message.please_check_your_order_list')
                    }}</span>
                </p>

                <div class="flex gap-3">
                    <button @click="closeOrderNotificationModal"
                        class="flex-1 h-12 rounded-xl bg-gray-100 text-gray-600 font-semibold hover:bg-gray-200 transition-all duration-300">
                        {{ $t('button.later') }}
                    </button>
                    <router-link :to="{ path: '/admin/' + orderNotification.url }"
                        class="flex-1 h-12 rounded-xl bg-gradient-to-r from-primary to-primary-dark text-white font-semibold shadow-lg shadow-primary/30 hover:shadow-primary/50 hover:-translate-y-0.5 transition-all duration-300 flex items-center justify-center gap-2 no-underline">
                        <i class="lab lab-arrow-right-3"></i>
                        {{ $t('button.let_me_check') }}
                    </router-link>
                </div>
            </div>
        </div>
    </div>
</template>

<style>
@keyframes slide-down {
    from {
        opacity: 0;
        transform: translateY(-20px);
    }

    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes fade-in {
    from {
        opacity: 0;
        transform: scale(0.95);
    }

    to {
        opacity: 1;
        transform: scale(1);
    }
}

@keyframes slide-fade-enter {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }

    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes slide-fade-leave {
    from {
        opacity: 1;
        transform: translateY(0);
    }

    to {
        opacity: 0;
        transform: translateY(-10px);
    }
}

.animate-slide-down {
    animation: slide-down 0.3s ease-out;
}

.animate-fade-in {
    animation: fade-in 0.2s ease-out;
}

.slide-fade-enter-active {
    animation: slide-fade-enter 0.3s ease-out;
}

.slide-fade-leave-active {
    animation: slide-fade-leave 0.2s ease-in;
}

.glass {
    background: rgba(255, 255, 255, 0.8);
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.2);
}

.desktop-nav-list {
    scrollbar-width: none;
}

.desktop-nav-list::-webkit-scrollbar {
    display: none;
}

.desktop-nav-link {
    flex: 0 1 auto;
    min-width: fit-content;
    line-height: 1rem;
}

@media (min-width: 1024px) and (max-width: 1279px) {
    .desktop-nav-link {
        padding-left: 0.5rem;
        padding-right: 0.5rem;
        font-size: 0.75rem;
    }
}

/*
 * ✅ FIX: Only apply scale: 0 / scale: 1 trick to .dropdown-list elements
 *    that are NOT the notification panel (.notif-panel handles its own
 *    visibility via Vue v-show — injecting scale:0 would override it).
 */
.dropdown-list {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    transform: scale(0);
    transform-origin: top right;
}

.dropdown-group:hover .dropdown-list,
.dropdown-btn:focus+.dropdown-list {
    transform: scale(1);
}

/* ✅ Notification panel — visibility fully controlled by v-show (display:none/block).
 *    No scale manipulation here. */
.notif-panel {
    /* intentionally no scale — v-show handles show/hide */
    transition: none;
}

.gradient-text {
    background: linear-gradient(45deg, #4f46e5, #7c3aed);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

a {
    text-decoration: none !important;
}

.no-underline {
    text-decoration: none !important;
}

.hover-lift {
    transition: transform 0.3s ease;
}

.hover-lift:hover {
    transform: translateY(-2px);
}

.focus-ring {
    transition: box-shadow 0.3s ease;
}

.focus-ring:focus {
    box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
}

.btn-hover-effect {
    position: relative;
    overflow: hidden;
}

.btn-hover-effect::after {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    width: 5px;
    height: 5px;
    background: rgba(255, 255, 255, 0.5);
    opacity: 0;
    border-radius: 100%;
    transform: scale(1, 1) translate(-50%);
    transform-origin: 50% 50%;
}

.btn-hover-effect:focus:not(:active)::after {
    animation: ripple 1s ease-out;
}

@keyframes ripple {
    0% {
        transform: scale(0, 0);
        opacity: 0.5;
    }

    20% {
        transform: scale(25, 25);
        opacity: 0.3;
    }

    100% {
        transform: scale(40, 40);
        opacity: 0;
    }
}

/* Bell blinking when unread */
@keyframes bell-blink {

    0%,
    100% {
        color: inherit;
    }

    50% {
        color: rgb(183, 22, 29);
    }
}

.blinking {
    animation: bell-blink 1.5s ease-in-out infinite;
}
</style>

<script>
import statusEnum from "../../../enums/modules/statusEnum";
import appService from "../../../services/appService";
import alertService from "../../../services/alertService";
import LoadingComponent from "../../frontend/components/LoadingComponent";
import axios from 'axios';
import { initializeApp } from "firebase/app";
import { getMessaging, getToken, onMessage } from "firebase/messaging";
import activityEnum from "../../../enums/modules/activityEnum";
import roleEnum from "../../../enums/modules/roleEnum";
import _ from "lodash";
import moment from "moment";

export default {
    name: "FrontendNavbarComponent",
    components: { LoadingComponent },

    data() {
        return {
            loading: { isActive: false },

            showMobileSearch: false,
            mobileMenuOpen: false,
            showSearchDropdown: false,

            orderNotificationStatus: false,
            orderNotificationMessage: "",
            currentRoute: "",
            defaultBranch: null,
            defaultLanguage: null,
            defaultCountryCode: null,

            enums: {
                activityEnum: activityEnum,
                roleEnum: roleEnum,
            },

            branchProps: {
                paginate: 0,
                order_column: "id",
                order_type: "asc",
                status: statusEnum.ACTIVE,
            },
            languageProps: {
                paginate: 0,
                order_column: "id",
                order_type: "asc",
                status: statusEnum.ACTIVE,
            },
            categoryProps: {
                search: {
                    paginate: 0,
                    order_column: 'sort',
                    order_type: 'asc',
                    status: statusEnum.ACTIVE,
                },
                slug: '',
            },

            orderNotification: { permission: false, url: "" },

            searchItem: "",
            searchResults: [],
            searchLoading: false,
            showSearchResults: false,
            searchTimeout: null,

            // ✅ FIXED: notification state — toggle via click, not CSS hover
            notifOpen: false,
            notifications: [],
            notifLoading: false,
            readingNotifications: {},

            // outside-click cleanup reference
            _notifOutsideClickHandler: null,
        };
    },

    computed: {
        logged() { return this.$store.getters.authStatus; },
        authDefaultPermission() { return this.$store.getters.authDefaultPermission; },
        profile() { return this.$store.getters.authInfo; },
        setting() { return this.$store.getters['frontendSetting/lists']; },
        language() { return this.$store.getters['frontendLanguage/show']; },
        languages() { return this.$store.getters['frontendLanguage/lists']; },
        categories() { return this.$store.getters['frontendItemCategory/lists']; },
        subtotal() { return this.$store.getters['frontendCart/subtotal']; },
        defaultMenu() { return this.$store.getters.authDefaultMenu; },

        unreadCount() {
            if (!this.notifications || !this.notifications.length) return 0;
            return this.notifications.filter(n => !n.read_at).length;
        },

        navItems() {
            return [
                { to: { name: 'frontend.home' }, path: '/home', label: this.$t('menu.home') },
                { to: { name: 'frontend.menu' }, path: '/menu', label: this.$t('menu.menu') },
                { to: { name: 'frontend.ourSpecials' }, path: '/our-specials', label: this.$t('menu.ourSpecials') },
                { to: { name: 'frontend.offers' }, path: '/offers', label: this.$t('menu.offers') },
                { to: { name: 'frontend.blog' }, path: '/blog', label: this.$t('menu.blogs') },
                { to: { name: 'frontend.about' }, path: '/about-us', label: this.$t('menu.about') },
                { to: { name: 'frontend.contact' }, path: '/contact', label: this.$t('menu.contact') },
            ];
        },

        mobileMenuItems() {
            return this.navItems.map(i => ({ to: i.to, path: i.path, label: i.label, icon: '' }));
        },

        cartCount() {
            const lists = this.$store.getters['frontendCart/lists'] || [];
            return Array.isArray(lists) ? lists.length : 0;
        },

        userMenuItems() {
            return [
                { to: { name: 'frontend.profile' }, label: this.$t('menu.profile'), icon: 'lab lab-profile-circle' },
                { to: { name: 'frontend.myOrder' }, label: this.$t('menu.my_orders'), icon: 'lab lab-reserve-line' },
            ];
        },
    },

    mounted() {
        // Scroll shadow on header
        window.addEventListener('scroll', () => {
            const resetRoutes = [
                'frontend.profile', 'frontend.myOrder', 'frontend.editProfile',
                'frontend.chat', 'frontend.address', 'frontend.changePassword',
                'auth.login', 'auth.signupPhone', 'auth.signupRegister',
                'auth.forgetPassword', 'auth.verifyEmail', 'frontend.page', 'frontend.offers',
            ];
            if (this.$refs.ffHeader) {
                if (!resetRoutes.includes(this.$route.name) && window.scrollY > 0) {
                    this.$refs.ffHeader.classList.add('active');
                } else {
                    this.$refs.ffHeader.classList.remove('active');
                }
            }
        });

        this.currentRoute = this.$route.path;
        this.orderPermissionCheck();

        // ✅ FIXED: outside-click handler to close notif dropdown
        this._notifOutsideClickHandler = (e) => {
            if (this.$refs.notifDropdown && !this.$refs.notifDropdown.contains(e.target)) {
                this.notifOpen = false;
            }
        };
        document.addEventListener('click', this._notifOutsideClickHandler);

        this.$store.dispatch('frontendSetting/lists').then(res => {
            this.defaultBranch = res.data.data.site_default_branch;
            this.defaultLanguage = res.data.data.site_default_language;
            this.defaultCountryCode = res.data.data.company_country_code;
            this.updateSeoTags();

            const globalState = this.$store.getters['globalState/lists'];
            if (globalState.branch_id > 0) this.defaultBranch = globalState.branch_id;
            if (globalState.language_id > 0) this.defaultLanguage = globalState.language_id;

            this.$store.dispatch('frontendLanguage/lists', this.languageProps).then().catch();
            this.$store.dispatch('frontendLanguage/show', this.defaultLanguage).then(res => {
                this.$i18n.locale = res.data.data.code;
                this.$store.dispatch("globalState/init", { language_code: res.data.data.code });
            }).catch();
            this.$store.dispatch('frontendItemCategory/lists', this.categoryProps.search).then().catch();
            this.loading.isActive = false;

            window.setTimeout(() => {
                this.$store.dispatch('frontendCountryCode/show', this.defaultCountryCode).then().catch();
                this.$store.dispatch('frontendCart/initOrderType', {
                    order_setup_delivery: res.data.data.order_setup_delivery,
                    order_setup_takeaway: res.data.data.order_setup_takeaway,
                });

                const d = res.data.data;
                if (
                    this.$store.getters.authStatus &&
                    d.notification_fcm_api_key &&
                    d.notification_fcm_auth_domain &&
                    d.notification_fcm_project_id &&
                    d.notification_fcm_storage_bucket &&
                    d.notification_fcm_messaging_sender_id &&
                    d.notification_fcm_app_id &&
                    d.notification_fcm_measurement_id
                ) {
                    initializeApp({
                        apiKey: d.notification_fcm_api_key,
                        authDomain: d.notification_fcm_auth_domain,
                        projectId: d.notification_fcm_project_id,
                        storageBucket: d.notification_fcm_storage_bucket,
                        messagingSenderId: d.notification_fcm_messaging_sender_id,
                        appId: d.notification_fcm_app_id,
                        measurementId: d.notification_fcm_measurement_id,
                    });

                    const messaging = getMessaging();
                    Notification.requestPermission().then(permission => {
                        if (permission === 'granted') {
                            getToken(messaging, { vapidKey: d.notification_fcm_public_vapid_key })
                                .then(currentToken => {
                                    if (currentToken) {
                                        axios.post('/frontend/device-token/web', { token: currentToken })
                                            .catch(error => {
                                                if (error.response?.data?.message === 'Unauthenticated.') {
                                                    this.$store.dispatch('loginDataReset');
                                                }
                                            });
                                    }
                                }).catch();
                        }
                    });

                    onMessage(messaging, payload => {
                        const notificationTitle = payload.notification.title;
                        new Notification(notificationTitle, {
                            body: payload.notification.body,
                            icon: '/images/default/firebase-logo.png',
                        });

                        if (payload.data.topicName === 'new-order-found' && this.orderNotification.permission) {
                            this.orderNotificationStatus = true;
                            this.orderNotificationMessage = payload.notification.body;
                            new Audio(d.notification_audio).play();
                        }
                    });
                }
            }, 3000);
        }).catch(() => {
            this.loading.isActive = false;
        });
    },

    beforeUnmount() {
        // Clean up outside-click listener
        if (this._notifOutsideClickHandler) {
            document.removeEventListener('click', this._notifOutsideClickHandler);
        }
    },

    methods: {
        // ─── Navigation ───────────────────────────────────────────────────────────
        goToOnlineOrder() {
            window.open('/online-orders', '_blank', 'noopener,noreferrer');
        },

        updateSeoTags() {
            if (typeof document === 'undefined') {
                return;
            }

            const setting = this.setting || {};
            const canonicalUrl = `${window.location.origin}${this.$route.path}`;
            const title = setting.site_meta_title || setting.site_og_title || setting.company_name || document.title;
            const description = setting.site_meta_description || setting.site_og_description || '';
            const keywords = setting.site_meta_keywords || '';
            const image = setting.site_og_image || '';

            document.title = title;

            const upsertMeta = (selector, attributes) => {
                let tag = document.head.querySelector(selector);
                if (!tag) {
                    tag = document.createElement('meta');
                    document.head.appendChild(tag);
                }

                Object.entries(attributes).forEach(([key, value]) => tag.setAttribute(key, value));
            };

            upsertMeta('meta[name="description"]', { name: 'description', content: description });
            upsertMeta('meta[name="keywords"]', { name: 'keywords', content: keywords });
            upsertMeta('meta[property="og:title"]', { property: 'og:title', content: setting.site_og_title || title });
            upsertMeta('meta[property="og:description"]', { property: 'og:description', content: description });
            upsertMeta('meta[property="og:image"]', { property: 'og:image', content: image });
            upsertMeta('meta[property="og:type"]', { property: 'og:type', content: 'website' });
            upsertMeta('meta[name="twitter:card"]', { name: 'twitter:card', content: 'summary_large_image' });
            upsertMeta('meta[name="twitter:title"]', { name: 'twitter:title', content: setting.site_og_title || title });
            upsertMeta('meta[name="twitter:description"]', { name: 'twitter:description', content: description });
            upsertMeta('meta[name="twitter:image"]', { name: 'twitter:image', content: image });

            let canonical = document.head.querySelector('link[rel="canonical"]');
            if (!canonical) {
                canonical = document.createElement('link');
                canonical.setAttribute('rel', 'canonical');
                document.head.appendChild(canonical);
            }
            canonical.setAttribute('href', canonicalUrl);
        },

        // ─── Utilities ────────────────────────────────────────────────────────────
        textShortener(text, number = 30) { return appService.textShortener(text, number); },
        openCanvas(id) { return appService.openCanvas(id); },
        currencyFormat(amount, decimal, currency, position) {
            return appService.currencyFormat(amount, decimal, currency, position);
        },
        checkIsPathAndRoutePathSame(path) { return this.currentRoute === path; },

        timeAgo(date) { return moment(date).fromNow(); },

        // ─── Navigation / Auth ────────────────────────────────────────────────────
        changeBranch(id) {
            this.$store.dispatch('frontendBranch/show', id);
            this.$store.dispatch("globalState/set", { branch_id: id });
        },
        changeLanguage(id, code) {
            this.hideLanguageModal();
            this.defaultLanguage = id;
            this.$store.dispatch("globalState/set", { language_id: id, language_code: code }).then(() => {
                this.$store.dispatch('frontendLanguage/show', id).then(res => {
                    this.$i18n.locale = res.data.data.code;
                }).catch();
            }).catch();
        },
        hideLanguageModal() { appService.modalHide('#language'); },
        toggleMobileMenu() { this.mobileMenuOpen = !this.mobileMenuOpen; },
        logout() {
            this.$store.dispatch("logout").then(() => {
                this.$router.push({ name: "frontend.home" });
            }).catch();
        },

        // ─── Search ───────────────────────────────────────────────────────────────
        toggleSearch() {
            this.showSearchDropdown = !this.showSearchDropdown;
            if (this.showSearchDropdown) {
                this.$nextTick(() => {
                    const el = document.querySelector('.search-dropdown-panel input[type="search"]');
                    if (el) el.focus();
                });
            }
        },
        search() {
            const term = (this.searchItem || '').trim();
            if (!term) { this.showMobileSearch = false; return; }
            this.$router.push({ name: "frontend.search", query: { s: term } }).then(() => {
                this.searchItem = "";
                this.showMobileSearch = false;
            }).catch(err => console.warn('Search nav error:', err));
        },
        searchReset() {
            this.searchItem = "";
            this.searchResults = [];
            this.showSearchDropdown = false;
            this.showSearchResults = false;
        },
        handleSearchInput: _.debounce(function () {
            const term = (this.searchItem || '').trim();
            if (term.length >= 2) {
                this.performSearch(term);
            } else {
                this.searchResults = [];
                this.showSearchResults = false;
            }
        }, 300),
        performSearch(term) {
            this.searchLoading = true;
            this.showSearchResults = true;
            this.$store.dispatch("frontendItem/lists", {
                paginate: 8,
                order_column: 'id',
                order_type: 'asc',
                name: term,
                status: statusEnum.ACTIVE,
            }).then(res => {
                this.searchResults = res.data.data || [];
                this.searchLoading = false;
            }).catch(() => {
                this.searchResults = [];
                this.searchLoading = false;
            });
        },
        selectSearchItem(item) {
            this.showMobileSearch = false;
            this.showSearchResults = false;
            this.searchItem = "";
            this.searchResults = [];
            this.$router.push({ name: "frontend.search", query: { s: item.name } });
        },

        // ─── ✅ FIXED: Notification methods (previously missing entirely) ─────────
        toggleNotifDropdown() {
            this.notifOpen = !this.notifOpen;
            if (this.notifOpen) {
                this.fetchNotifications();
            }
        },

        fetchNotifications() {
            this.notifLoading = true;
            axios.get('/frontend/notifications')
                .then(res => {
                    this.notifications = res.data.data || [];
                    this.notifLoading = false;
                })
                .catch(() => {
                    this.notifLoading = false;
                });
        },

        markAllRead() {
            if (this.notifLoading || !this.notifications.length) return;
            this.notifLoading = true;
            axios.post('/frontend/notifications/read-all')
                .then(() => {
                    const now = new Date().toISOString();
                    this.notifications = this.notifications.map(n => ({ ...n, read_at: now }));
                    this.notifLoading = false;
                })
                .catch(() => { this.notifLoading = false; });
        },

        readNotification(notif) {
            if (notif.read_at || this.readingNotifications[notif.id]) return;
            this.$set(this.readingNotifications, notif.id, true);
            axios.post(`/frontend/notifications/${notif.id}/read`)
                .then(() => {
                    notif.read_at = new Date().toISOString();
                    this.$set(this.readingNotifications, notif.id, false);
                })
                .catch(() => {
                    this.$set(this.readingNotifications, notif.id, false);
                });
        },

        // ─── Cart helpers ─────────────────────────────────────────────────────────
        quickAddToCart(item) {
            if (item.has_variation || item.has_extras || item.has_addons) {
                this.showMobileSearch = false;
                this.$store.dispatch('frontendItem/details', item.id).then(() => {
                    this.$router.push({ name: "frontend.search", query: { s: item.name } });
                }).catch();
            } else {
                this.directAddToCart(item);
            }
        },
        directAddToCart(item) {
            const price = (item.offer && item.offer.length > 0) ? item.offer[0].currency_price : item.currency_price;
            const convertPx = (item.offer && item.offer.length > 0) ? item.offer[0].convert_price : item.convert_price;
            const itemArray = [{
                name: item.name || '',
                image: item.thumb || item.image || '',
                item_id: item.id,
                quantity: 1,
                discount: 0,
                currency_price: price,
                convert_price: convertPx,
                item_variations: { variations: {}, names: {} },
                item_extras: { extras: [], names: [] },
                item_variation_total: 0,
                item_extra_total: 0,
                instruction: "",
            }];
            this.$store.dispatch("frontendCart/lists", itemArray).then(() => {
                this.showMobileSearch = false;
                this.showSearchResults = false;
                this.searchItem = "";
                this.searchResults = [];
                alertService.success(this.$t('message.add_to_cart'));
            }).catch(err => {
                alertService.error(err.response?.data?.message || 'Error adding to cart');
            });
        },

        // ─── Profile image ────────────────────────────────────────────────────────
        saveImage() {
            if (!this.$refs.imageProperty?.files[0]) return;
            this.loading.isActive = true;
            const formData = new FormData();
            formData.append("image", this.$refs.imageProperty.files[0]);
            this.$store.dispatch("frontendEditProfile/changeImage", { form: formData })
                .then(res => {
                    this.$store.dispatch('updateAuthInfo', res.data.data).then(() => {
                        this.loading.isActive = false;
                        alertService.success(this.$t("message.photo_update"));
                        this.$refs.imageProperty.value = null;
                    }).catch(() => { this.loading.isActive = false; });
                })
                .catch(err => {
                    this.loading.isActive = false;
                    this.imageErrors = err.response?.data?.errors;
                });
        },

        // ─── Order notification ───────────────────────────────────────────────────
        orderPermissionCheck() {
            const permissions = this.$store.getters.authPermission;
            if (!permissions?.length) return;
            _.forEach(permissions, permission => {
                if (permission.name === 'online-orders' && permission.access === true) {
                    this.orderNotification.permission = true;
                    this.orderNotification.url = permission.url;
                }
            });
        },
        closeOrderNotificationModal() {
            this.$refs.orderNotificationModal?.classList?.remove("active");
            document.body.style.overflowY = "auto";
            this.loading.isActive = false;
            this.orderNotificationStatus = false;
        },
    },

    watch: {
        $route(to) {
            this.currentRoute = to.path;
            this.showMobileSearch = false;
            this.searchResults = [];
            this.showSearchResults = false;
            this.notifOpen = false; // close notif on route change
            this.updateSeoTags();
        },
        showMobileSearch(val) {
            document.body.style.overflow = val ? 'hidden' : 'auto';
            if (!val) this.searchResults = [];
        },
        searchItem(val) {
            if (!val || val.trim() === '') {
                this.searchResults = [];
                this.showSearchResults = false;
            }
        },
        categories: {
            deep: true,
            handler(category) {
                if (category.length > 0 && category[0].slug !== "undefined") {
                    this.categoryProps.slug = category[0].slug;
                }
            },
        },
        setting: {
            deep: true,
            handler() {
                this.updateSeoTags();
            },
        },
    },
};
</script>
