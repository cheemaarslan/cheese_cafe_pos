<template>
    <LoadingComponent :props="loading" />

    <div class="md:w-[calc(100%-340px)] lg:w-[calc(100%-320px)] xl:w-[calc(100%-377px)]">
        <div class="flex items-center gap-3 mb-4">
            <form @submit.prevent="search"
                class="flex items-center flex-grow h-[38px] leading-[38px] rounded-lg bg-white border-[#EFF0F6] border-t border-l border-b">
                <input type="text" v-model="props.search.name" :placeholder="$t('label.search_by_menu_item')"
                    class="w-full px-5 rounded-tl-lg rounded-bl-lg placeholder:text-xs placeholder:font-rubik placeholder:text-[#A0A3BD]">
                <button @click="resetName" type="button" v-if="props.search.name"
                    class="text-sm text-red-500 fa-regular fa-circle-xmark mr-4"></button>
                <button type="submit"
                    class="flex-shrink-0 w-[38px] h-full text-center ltr:rounded-tr-lg ltr:rounded-br-lg rtl:rounded-tl-lg rtl:rounded-bl-lg bg-primary">
                    <i class="lab lab-search-normal text-white"></i>
                </button>
            </form>
            <button @click.prevent="printLastOrder" type="button"
                :title="$t('button.print_last_order_invoice')"
                class="flex-shrink-0 flex items-center justify-center gap-2 h-[38px] px-3 rounded-lg text-white bg-[#1AB759] hover:bg-[#149A4A] transition shadow-sm active:scale-95">
                <i class="lab lab-print-bold lab-font-size-16 text-white"></i>
                <span class="hidden lg:inline text-xs font-medium font-rubik capitalize whitespace-nowrap">
                    {{ $t('button.print_last_order_invoice') }}
                </span> 
            </button>
        </div>

        <div class="swiper pos-menu-swiper mb-6" v-if="categories.length > 1">
            <button type="button" v-show="!categoryAtStart" @click="scrollCategories(-1)"
                class="absolute z-10 top-1/2 -translate-y-1/2 left-1 w-8 h-8 rounded-full bg-white shadow-md text-heading hover:text-primary transition">
                <i class="fa-solid fa-chevron-left text-xs"></i>
            </button>
            <Swiper dir="ltr" :modules="categorySwiperModules" :speed="600" slidesPerView="auto" :spaceBetween="16"
                :freeMode="true" :grabCursor="true" :mousewheel="{ forceToAxis: false, releaseOnEdges: true }"
                @swiper="updateCategoryEdges" @progress="updateCategoryEdges" @resize="updateCategoryEdges"
                class="menu-slides">
                <SwiperSlide class="!w-fit !h-auto" v-for="(category, index) in categories" :key="category"
                    :class="category.id === props.search.item_category_id || (category.id === 0 && props.search.item_category_id === '') ? 'pos-group' : ''">
                    <router-link v-if="index === 0" to="#" @click.prevent="allCategory" draggable="false"
                        class="w-28 h-full flex flex-col items-center text-center gap-4 py-4 px-3 rounded-lg border-b-2 border-transparent transition hover:bg-[#FFEDF4] hover:border-primary bg-white">
                        <img class="h-7 drop-shadow-category" :src="category.thumb" alt="category" draggable="false">
                        <h3 class="text-xs leading-[16px] font-medium font-rubik">{{ category.name }}</h3>
                    </router-link>
                    <router-link v-else to="#" @click.prevent="setCategory(category.id)" draggable="false"
                        class="w-28 h-full flex flex-col items-center text-center gap-4 py-4 px-3 rounded-lg border-b-2 border-transparent transition hover:bg-[#FFEDF4] hover:border-primary bg-white">
                        <img class="h-7 drop-shadow-category" :src="category.thumb" alt="category" draggable="false">
                        <h3 class="text-xs leading-[16px] font-medium font-rubik">{{ category.name }}</h3>
                    </router-link>
                </SwiperSlide>
            </Swiper>
            <button type="button" v-show="!categoryAtEnd" @click="scrollCategories(1)"
                class="absolute z-10 top-1/2 -translate-y-1/2 right-1 w-8 h-8 rounded-full bg-white shadow-md text-heading hover:text-primary transition">
                <i class="fa-solid fa-chevron-right text-xs"></i>
            </button>
        </div>
        <ItemComponent :items="items" v-if="items.length > 0" />

        <div class="my-12" v-else-if="items.length === 0 && !props.search.name">
            <div class="max-w-[350px] mx-auto">
                <img class="w-full mb-8" :src="setting.image_order_not_found" alt="image_order_not_found">
            </div>
            <span class="w-full mb-4 text-center text-black">{{ $t('message.no_data_available') }}</span>
        </div>
        <div class="my-12" v-else-if="items.length === 0 && props.search.name">
            <div class="max-w-[250px] mx-auto">
                <img class="w-full mb-8" :src="setting.item_not_found" alt="item_not_found">
            </div>
            <span class="w-full mb-4 text-center text-black">{{ $t('message.no_items_found') }}</span>
        </div>
    </div>


    <div id="pos-cart"
        class="db-pos-cartDiv fixed top-0 ltr:right-0 rtl:left-0 w-full h-screen rounded-none z-50 md:z-10 md:top-[85px] ltr:md:right-5 rtl:md:left-5 md:w-[322px] lg:w-[305px] xl:w-[360px] md:h-[calc(100vh-85px)] md:rounded-lg overflow-y-auto thin-scrolling bg-white">
        <div class="p-4">
            <div class="md:hidden text-right mb-3">
                <button class="db-pos-cartCls" @click="closeCanvas('pos-cart')">
                    <i class="lab-close-circle-line font-fill-danger lab-font-size-24"></i>
                </button>
            </div>
            <div v-if="editOrder"
                class="flex items-center justify-between gap-2 mb-3 px-3 py-2 rounded-lg border border-[#FFBC1F] bg-[#FFF8E6]">
                <span class="text-sm font-medium font-rubik text-heading">
                    {{ $t('label.editing_order') }} #{{ editOrder.order_serial_no }}
                </span>
                <button @click.prevent="resetCart" type="button"
                    class="text-xs font-medium font-rubik capitalize text-[#FB4E4E] hover:underline">
                    {{ $t('button.cancel_edit') }}
                </button>
            </div>
            <div class="flex items-center w-full gap-4 mb-3">
                <div class="db-field flex-grow">
                    <vue-select
                        class="db-field-control text-sm rounded-lg appearance-none text-heading border-[#D9DBE9]"
                        id="customer" v-model="checkoutProps.form.customer_id" :options="customers"
                        @update:modelValue="changingUser" label-by="name" value-by="id" :closeOnSelect="true"
                        :searchable="true" :clearOnClose="true" :placeholder="$t('label.select_customer')"
                        :search-placeholder="$t('label.search_customer')" />
                </div>
                <div data-modal="#addCustomer" @click.prevent="addCustomers"
                    class="w-10 h-10 bg-primary rounded-lg flex items-center justify-center cursor-pointer">
                    <i class="fa-solid fa-circle-plus text-white"></i>
                </div>
            </div>
            <input v-on:keypress="onlyNumber($event)"
                class="db-field-control text-sm rounded-lg appearance-none text-heading border-[#D9DBE9] mb-3 bg-gray-50 cursor-not-allowed"
                type="text" id="token" v-model="checkoutProps.form.token" :placeholder="$t('label.token_no')" readonly />

            <div class="p-3 pt-2 rounded-lg border border-[#D9DBE9]">
                <h4 class="text-sm font-medium mb-3">{{ $t('label.select_order_type') }}</h4>

                <div class="db-field-radio-group gap-1 active-group">

                    <label @click="dineInOrder" ref="dineIn" for="dinein" data-dine="#dine"
                        class="!w-fit db-field-radio px-2.5 py-2 rounded-lg border border-[#F7F7FC] bg-[#F7F7FC] active">
                        <div class="custom-radio sm">
                            <input ref="dineInInput" type="radio" id="dinein" name="orderType"
                                :value="orderTypeEnums.dineIn" v-model="checkoutProps.form.order_type"
                                class="custom-radio-field" />
                            <span class="custom-radio-span"></span>
                        </div>
                        <h3 class="db-field-label text-sm text-heading">
                            {{ $t('label.dine_in') }}
                        </h3>
                    </label>
                    <label ref="takeAway" @click="takeAwayOrder" for="takeway"
                        class="!w-fit db-field-radio px-2.5 py-2 rounded-lg border border-[#F7F7FC] bg-[#F7F7FC]">
                        <div class="custom-radio sm">
                            <input ref="takeAwayInput" type="radio" id="takeway" name="orderType"
                                :value="orderTypeEnums.takeAway" v-model="checkoutProps.form.order_type"
                                class="custom-radio-field" />
                            <span class="custom-radio-span"></span>
                        </div>
                        <h3 class="db-field-label text-sm text-heading">
                            {{ $t('label.takeaway') }}
                        </h3>
                    </label>


                    <label ref="deliveryOrderLabel" @click="deliveryOrder" for="delivery"
                        data-orderdelivery="#orderdelivery" type="button"
                        class="!w-fit db-field-radio px-2.5 py-2 rounded-lg border border-[#F7F7FC] bg-[#F7F7FC]">
                        <div class="custom-radio sm">
                            <input ref="deliveryOrderInput" type="radio" id="delivery" name="orderType"
                                :value="orderTypeEnums.delivery" v-model="checkoutProps.form.order_type"
                                class="custom-radio-field" />
                            <span class="custom-radio-span"></span>
                        </div>
                        <h3 class="db-field-label text-sm text-heading">
                            {{ $t('label.delivery') }}
                        </h3>
                    </label>
                </div>
                <div ref="deliveryOrderDiv" id="orderdelivery" class="h-auto hidden transition">
                    <div class="my-3 relative">
                        <div class="db-field">
                            <input type="text" id="deliveryAddress" autocomplete="off" v-model="addressInput"
                                @input="onAddressInput" @focus="addressDropdownOpen = true"
                                @blur="addressDropdownOpen = false" :placeholder="$t('label.address')"
                                class="db-field-control text-sm rounded-lg text-heading border-[#D9DBE9]" />
                        </div>
                        <ul v-if="addressDropdownOpen && addressSuggestions.length"
                            class="absolute z-20 left-0 right-0 mt-1 max-h-48 overflow-y-auto bg-white border border-[#D9DBE9] rounded-lg shadow-lg">
                            <li v-for="suggestion in addressSuggestions" :key="suggestion.id"
                                @mousedown.prevent="chooseSavedAddress(suggestion)"
                                class="px-3 py-2 text-sm leading-6 text-heading cursor-pointer hover:bg-[#F3F4F8]">
                                {{ suggestion.address }}
                            </li>
                        </ul>
                    </div>
                </div>
                <div ref="dineInDiv" id="dine" class="h-auto hidden transition">
                    <div class="mt-3">
                        <div class="db-field flex-grow">
                            <vue-select
                                class="db-field-control text-sm rounded-lg appearance-none text-heading border-[#D9DBE9]"
                                id="diningtables" :options="diningtables" v-model="checkoutProps.form.dining_table_id"
                                value-by="id" label-by="name" :closeOnSelect="true" :searchable="true"
                                :clearOnClose="true" :placeholder="$t('label.select_table')"
                                :search-placeholder="$t('label.search_table')" />
                        </div>
                    </div>
                </div>

            </div>

            <div class="my-3 p-3 rounded-lg border border-[#D9DBE9]">
                <h3 class="text-sm font-medium mb-2">
                    {{ $t('label.special_instructions') }}
                </h3>
                <textarea v-model="temp.instruction" :placeholder="$t('message.add_note')"
                    class="h-12 w-full rounded-lg border py-1.5 px-2 placeholder:text-[10px] placeholder:text-[#6E7191] border-[#D9DBE9]"></textarea>
                <small class="db-field-alert" v-if="instructionError">{{ instructionError }}</small>
            </div>
        </div>
        <table class="w-full">
            <thead class="bg-[#FFEDF4]">
                <tr class="h-9">
                    <th class="capitalize text-xs font-normal font-rubik text-left pl-3 text-heading"></th>
                    <th class="capitalize text-xs font-normal font-rubik text-left px-3 text-heading">
                        {{ $t('label.item') }}
                    </th>
                    <th class="capitalize text-xs font-normal font-rubik text-left px-3 text-heading">
                        {{ $t('label.qty') }}
                    </th>
                    <th class="capitalize text-xs font-normal font-rubik text-left px-3 text-heading">
                        {{ $t('label.price') }}
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(cart, index) in carts">
                    <td class="pl-3 py-3 last:pr-3 align-top border-b border-[#EFF0F6]">
                        <button @click.prevent="deleteCartItem(index)">
                            <i class="lab lab-trash-line-2 font-fill-danger"></i>
                        </button>
                    </td>
                    <td class="pl-3 py-3 last:pr-3 align-top border-b border-[#EFF0F6]">
                        <h3 class="capitalize text-xs font-rubik text-[#2E2F38]">{{ cart.name }}</h3>
                        <p v-if="Object.keys(cart.item_variations.variations).length !== 0">
                            <span v-for="(variation, variationName, index) in cart.item_variations.names">
                                <span class="capitalize text-[10px] leading-4 font-rubik text-heading">{{
                                    variationName
                                }}:
                                    &nbsp;</span>
                                <span class="capitalize text-[10px] leading-4 font-rubik">{{ variation }}
                                    <span v-if="index + 1 < Object.keys(cart.item_variations.names).length">,
                                        &nbsp;</span>
                                </span>
                            </span>
                        </p>
                        <ul v-if="cart.item_extras.extras.length > 0 || cart.instruction !== ''">
                            <li v-if="cart.item_extras.extras.length > 0" class="leading-4">
                                <span class="capitalize text-[10px] leading-4 font-rubik text-heading">
                                    {{ $t('label.extras') }}:
                                </span>
                                <p class="capitalize text-[10px] leading-4 font-rubik">
                                    <span v-for="(extra, index) in cart.item_extras.names">
                                        {{ extra }}
                                        <span v-if="index + 1 < cart.item_extras.extras.length">, &nbsp;</span>
                                    </span>
                                </p>
                            </li>
                            <li v-if="cart.instruction !== ''" class="leading-4">
                                <span class="capitalize text-[10px] leading-4 font-rubik text-heading">
                                    {{ $t('label.instruction') }}:
                                </span>
                                <span class="capitalize text-[10px] leading-4 font-rubik">
                                    {{ cart.instruction }}
                                </span>
                            </li>
                        </ul>
                    </td>
                    <td class="pl-3 py-3 last:pr-3 align-top border-b border-[#EFF0F6]">
                        <div class="flex items-center indec-group">
                            <button @click.prevent="cartQuantityDecrement(index)"
                                :class="cart.quantity === 1 ? 'fa-trash-can' : 'fa-minus'"
                                class="fa-solid text-[10px] w-[18px] h-[18px] leading-4 text-center rounded-full border transition text-primary border-primary hover:bg-primary hover:text-white indec-minus"></button>
                            <input v-on:keypress="onlyNumber($event)" v-on:keyup="cartQuantityUp(index, $event)"
                                type="number" :value="cart.quantity"
                                class="text-center w-7 text-xs font-semibold text-heading indec-value">
                            <button @click.prevent="cartQuantityIncrement(index)"
                                class="fa-solid fa-plus text-[10px] w-[18px] h-[18px] leading4 text-center rounded-full border transition text-primary border-primary hover:bg-primary hover:text-white indec-plus"></button>
                        </div>
                    </td>
                    <td class="pl-3 py-3 last:pr-3 align-top border-b border-[#EFF0F6] text-xs font-rubik text-heading">
                        {{ amountFormat(cart.total, setting.site_digit_after_decimal_point) }}
                    </td>
                </tr>
            </tbody>
        </table>
        <div class="p-4">
            <div class="mb-4">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm font-medium font-rubik capitalize text-[#2E2F38]">
                        {{ $t('label.extra_items') }}
                    </span>
                    <button @click.prevent="addExtraItemRow" type="button" :title="$t('button.add_more')"
                        class="fa-solid fa-plus text-[10px] w-[22px] h-[22px] text-center rounded-full border transition text-primary border-primary hover:bg-primary hover:text-white"></button>
                </div>
                <div v-for="(extraItem, index) in extraItems" :key="index" class="flex items-center gap-2 mb-2 h-[38px]">
                    <input v-model="extraItem.name" type="text" :placeholder="$t('label.name')"
                        class="flex-1 min-w-0 h-full rounded border px-3 text-sm border-[#EFF0F6]">
                    <input v-on:keypress="floatNumber($event)" v-model="extraItem.price" type="text"
                        :placeholder="$t('label.price')"
                        class="w-20 h-full rounded border px-3 text-sm border-[#EFF0F6]">
                    <button v-if="extraItems.length > 1" @click.prevent="removeExtraItemRow(index)" type="button"
                        class="flex-shrink-0">
                        <i class="lab lab-trash-line-2 font-fill-danger"></i>
                    </button>
                </div>
                <button @click.prevent="addExtraItems" type="button"
                    class="w-full h-[38px] text-sm font-medium font-rubik capitalize rounded-lg text-white bg-[#008BBA]">
                    {{ $t('button.add') }}
                </button>
            </div>

            <div class="flex h-[38px]" v-if="carts.length > 0">
                <div class="dropdown-group">
                    <button
                        class="flex items-center justify-start w-[120px] h-full text-sm font-rubik rounded-tl rounded-bl appearance-none border pl-3 text-heading border-[#EFF0F6] dropdown-btn">
                        <span class="flex-1 text-start" v-if="discountType === discountTypeEnum.PERCENTAGE">{{
                            $t("label.percentage") }}</span>
                        <span class="flex-1 text-start" v-else>{{ $t("label.fixed") }}</span>
                        <i class="lab lab-arrow-down-2 lab-font-size-17 mx-1"></i>
                    </button>
                    <ul
                        class="p-2 rounded-lg shadow-xl absolute top-10 ltr:right-0 rtl:left-0 z-10 bg-white transition-all duration-300 origin-top scale-y-0 dropdown-list w-full">
                        <li class="flex items-center gap-2 py-1 px-2.5 rounded-md cursor-pointer hover:bg-gray-100"
                            v-for="option in [
                                { name: $t('label.percentage'), value: discountTypeEnum.PERCENTAGE },
                                { name: $t('label.fixed'), value: discountTypeEnum.FIXED }
                            ]" :key="option" @click="selectDiscount(option.value)">
                            <span class="text-heading capitalize text-sm">{{ option.name }}</span>

                        </li>
                    </ul>
                </div>
                <input v-on:keypress="floatNumber($event)" v-model="discount" type="text"
                    :placeholder="$t('label.add_discount')"
                    class="w-full h-full border-t border-b px-3 border-[#EFF0F6]">
                <button @click.prevent="applyDiscount" type="submit"
                    class="flex-shrink-0 w-16 h-full text-sm font-medium font-rubik capitalize ltr:rounded-tr-lg ltr:rounded-br-lg rtl:rounded-tl-lg rtl:rounded-bl-lg text-white bg-[#008BBA]">
                    {{ $t('button.apply') }}
                </button>
            </div>

            <ul class="flex flex-col gap-1.5 mb-4 mt-4">
                <li class="flex items-center justify-between">
                    <span class="text-sm font-rubik capitalize leading-6 text-[#2E2F38]">
                        {{ $t("label.sub_total") }}
                    </span>
                    <span class="text-sm font-rubik capitalize leading-6 text-[#2E2F38]">
                        {{ amountFormat(subtotal, setting.site_digit_after_decimal_point) }}
                    </span>
                </li>
                <li class="flex items-center justify-between">
                    <span class="text-sm font-rubik capitalize leading-6">{{ $t("label.discount") }}</span>
                    <span class="text-sm font-rubik capitalize leading-6">{{
                        amountFormat(posDiscount, setting.site_digit_after_decimal_point)
                    }}</span>
                </li>
                <li class="flex items-center justify-between gap-2"
                    v-if="checkoutProps.form.order_type === orderTypeEnums.delivery">
                    <span class="text-sm font-rubik capitalize leading-6">{{ $t("label.delivery_charge") }}</span>
                    <label for="deliveryCharge"
                        class="flex items-center gap-1.5 h-8 px-2 rounded-lg border border-dashed border-[#008BBA] bg-[#008BBA]/5 cursor-text transition focus-within:border-solid focus-within:bg-white focus-within:ring-1 focus-within:ring-[#008BBA]">
                        <i class="lab lab-edit-line lab-font-size-14 text-[#008BBA]"></i>
                        <input v-on:keypress="floatNumber($event)" v-model="deliveryChargeInput"
                            @input="applyDeliveryCharge" @blur="normalizeDeliveryCharge" type="text"
                            id="deliveryCharge" placeholder="0"
                            class="w-16 bg-transparent text-sm text-end font-rubik font-medium text-[#1AB759] focus:outline-none">
                    </label>
                </li>
                <li class="flex items-center justify-between">
                    <span class="text-sm font-medium font-rubik capitalize leading-6 text-[#2E2F38]">
                        {{ $t("label.total") }}
                    </span>
                    <span class="text-sm font-medium font-rubik capitalize leading-6 text-[#2E2F38]">
                        {{
                            amountFormat((subtotal + checkoutProps.form.delivery_charge) - posDiscount,
                                setting.site_digit_after_decimal_point)
                        }}
                    </span>
                </li>
            </ul>
            <div class="flex items-center justify-center gap-6" v-if="carts.length > 0">
                <button @click.prevent="resetCart"
                    class="capitalize text-sm font-medium leading-6 font-rubik w-full text-center rounded-3xl py-2 text-white bg-[#FB4E4E] hover:bg-red-600 transition shadow-sm cursor-pointer active:scale-95">
                    {{ $t('button.cancel') }}
                </button>
                <button @click.prevent="orderSubmit"
                    class="capitalize text-sm font-medium leading-6 font-rubik w-full text-center rounded-3xl py-2 text-white bg-primary hover:bg-primary/90 transition shadow-sm cursor-pointer active:scale-95">
                    {{ editOrder ? $t('button.update_order') : $t('button.order') }}
                </button>
            </div>
        </div>
    </div>


    <!--====================================
        ADD CUSTOMER MODAL PART START
=====================================-->
    <div id="addCustomer" class="modal">
        <div class="modal-dialog">
            <div class="modal-header pb-3 border-b border-[#D9DBE9]">
                <h3 class="capitalize font-medium">{{ $t('button.add_customer') }}</h3>
                <button @click="resetCustomer" class="modal-close fa-regular fa-circle-xmark"></button>
            </div>
            <div class="modal-body">
                <form @submit.prevent="saveCustomer">
                    <div class="row mb-3">
                        <div class="col-12 sm:col-6">
                            <label class="db-field-title required">{{ $t("label.name") }}</label>
                            <input type="text" v-model="customerProps.form.name"
                                v-bind:class="errors.name ? 'invalid' : ''" id="name"
                                class="w-full h-12 text-sm rounded-lg border px-4 text-heading border-[#D9DBE9]" />
                            <small class="db-field-alert" v-if="errors.name">{{
                                errors.name[0]
                            }}</small>
                        </div>
                        <div class="col-12 sm:col-6">
                            <label for="phone" class="db-field-title">{{ $t("label.phone") }}</label>
                            <div :class="errors.phone ? 'invalid' : ''"
                                class="w-full h-12 rounded-lg border px-4 flex items-center border-[#D9DBE9]">
                                <div class="w-fit flex-shrink-0 dropdown-group">
                                    <button type="button" class="flex items-center gap-1 dropdown-btn">
                                        {{ flag }}
                                        <span class="whitespace-nowrap flex-shrink-0 text-xs">
                                            {{
                                                customerProps.form.country_code
                                            }}
                                        </span>
                                        <input type="hidden" v-model="customerProps.form.country_code
                                            " />
                                    </button>
                                </div>
                                <input v-model="customerProps.form.phone" v-on:keypress="phoneNumber($event)"
                                    v-bind:class="errors.phone ? 'invalid' : ''" type="text" id="phone"
                                    class="pl-2 text-sm w-full h-full" />
                            </div>
                            <small class="db-field-alert" v-if="errors.phone">
                                {{ errors.phone[0] }}
                            </small>
                        </div>
                        <div class="col-12 sm:col-6">
                            <label class="db-field-title required">{{ $t("label.email") }}</label>
                            <input type="email" id="email" v-model="customerProps.form.email"
                                v-bind:class="errors.email ? 'invalid' : ''"
                                class="w-full h-12 text-sm rounded-lg border px-4 text-heading border-[#D9DBE9]" />
                            <small class="db-field-alert" v-if="errors.email">{{
                                errors.email[0]
                            }}</small>
                        </div>
                        <div class="col-12 sm:col-6">
                            <label for="password" class="db-field-title required">{{ $t("label.password") }}</label>
                            <input v-model="customerProps.form.password" v-bind:class="errors.password ? 'invalid' : ''"
                                type="text" id="password"
                                class="w-full h-12 text-sm rounded-lg border px-4 text-heading border-[#D9DBE9]"
                                autocomplete="off" />
                            <small class="db-field-alert" v-if="errors.password">{{ errors.password[0] }}</small>
                        </div>
                        <input type="hidden" v-model="customerProps.form.password_confirmation" />
                    </div>
                    <button type="submit"
                        class="rounded-3xl text-base py-3 px-3 font-medium w-full text-white bg-primary">
                        {{ $t('button.add_customer') }}
                    </button>
                </form>
            </div>
        </div>
    </div>
    <!--====================================
          ADD CUSTOMER MODAL PART END
    =====================================-->

    <!--====================================
      PAYMENT MODAL PART START
  =====================================-->
    <PaymentComponent :props="checkoutProps" @saved="orderSaved" />
    <!--====================================
          PAYMENT MODAL PART END
      =====================================-->


    <!--====================================
      LAST ORDER RECEIPT MODAL PART START
  =====================================-->
    <ReceiptComponent :order="lastOrder" modal-id="lastOrderReceiptModal" print-id="lastOrderPrint" />
    <!--====================================
          LAST ORDER RECEIPT MODAL PART END
      =====================================-->


    <button @click="openCanvas('pos-cart')" type="button"
        class="db-pos-cartBtn fixed md:hidden bottom-0 z-10 left-0 w-full h-14 py-4 text-center flex items-center justify-center shadow-xl-top gap-3 bg-primary">
        <i class="lab lab-bag-2 lab-font-size-13 text-white"></i>
        <span class="text-base font-medium font-rubik text-white">
            {{ totalItems() }} {{ $t('label.items') }} - {{
                amountFormat(subtotal - posDiscount, setting.site_digit_after_decimal_point)
            }}
        </span>
    </button>
</template>
<script>
import LoadingComponent from "../components/LoadingComponent";
import 'vue3-carousel/dist/carousel.css';
import ItemComponent from "./ItemComponent";
import sourceEnum from "../../../enums/modules/sourceEnum";
import orderTypeEnum from "../../../enums/modules/orderTypeEnum";
import axios from 'axios';
import isAdvanceOrderEnum from "../../../enums/modules/isAdvanceOrderEnum";
import statusEnum from "../../../enums/modules/statusEnum";
import roleEnum from "../../../enums/modules/roleEnum";
import appService from "../../../services/appService";
import discountTypeEnum from "../../../enums/modules/discountTypeEnum";
import displayModeEnum from "../../../enums/modules/displayModeEnum";
import alertService from "../../../services/alertService";
import PaymentComponent from "./PaymentComponent";
import ReceiptComponent from "./ReceiptComponent";
import posPaymentMethodEnum from "../../../enums/modules/posPaymentMethodEnum";
import { markRaw } from 'vue';
import { Swiper, SwiperSlide } from 'swiper/vue';
import { FreeMode, Mousewheel } from 'swiper/modules';
import 'swiper/css';
import 'swiper/css/free-mode';
import focustrap from "bootstrap/js/src/util/focustrap";
import CustomerAddressCreateComponent from "../customers/address/CustomerAddressCreateComponent.vue";
import labelEnum from "../../../enums/modules/labelEnum";

export default {
    name: "PosComponent",
    components: {
        CustomerAddressCreateComponent,
        LoadingComponent,
        ItemComponent,
        Swiper,
        SwiperSlide,
        PaymentComponent,
        ReceiptComponent
    },
    data() {
        return {
            loading: {
                isActive: false,
            },
            order: {},
            lastOrder: {},
            editOrder: null,
            categorySwiperModules: [FreeMode, Mousewheel],
            categorySwiper: null,
            categoryAtStart: true,
            categoryAtEnd: true,
            discount: null,
            extraItems: [{ name: '', price: '' }],
            deliveryChargeInput: '',
            deliveryChargeEdited: false,
            temp: {
                instruction: '',
            },
            instructionError: '',
            checkoutProps: {
                form: {
                    branch_id: null,
                    subtotal: 0,
                    token: "",
                    customer_id: null,
                    discount: 0,
                    delivery_charge: 0,
                    delivery_time: null,
                    total: 0,
                    order_type: orderTypeEnum.DINING_TABLE,
                    is_advance_order: isAdvanceOrderEnum.NO,
                    pos_payment_method: posPaymentMethodEnum.CASH,
                    pos_payment_note: '',
                    source: sourceEnum.POS,
                    address_id: null,
                    coupon_id: null,
                    items: [],
                    dining_table_id: null,
                    pos_received_amount: null,

                },
                // Set while an existing order is loaded into the cart, so checkout updates it instead of placing a new one
                editOrderId: null,
            },
            props: {
                search: {
                    paginate: 0,
                    order_column: "id",
                    order_type: "asc",
                    name: "",
                    item_category_id: "",
                    status: statusEnum.ACTIVE
                },
            },
            categoryProps: {
                paginate: 0,
                order_column: 'sort',
                order_type: 'asc',
                status: statusEnum.ACTIVE
            },

            statusEnum: statusEnum,
            discountTypeEnum: discountTypeEnum,
            discountType: discountTypeEnum.PERCENTAGE,
            posPaymentMethodEnum: posPaymentMethodEnum,
            customerProps: {
                form: {
                    name: "",
                    email: "",
                    phone: "",
                    password: "123456",
                    password_confirmation: "123456",
                    country_code: "",
                    status: statusEnum.ACTIVE,
                },
                search: {
                    paginate: 0,
                    order_column: "id",
                    order_type: "asc",
                    status: statusEnum.ACTIVE,
                    excepts: `${roleEnum.ADMIN}|${roleEnum.CHEF}|${roleEnum.DELIVERY_BOY}`
                },
            },
            errors: {},

            flag: "",
            address: {
                form: {
                    address: "",
                    apartment: "",
                    latitude: "",
                    longitude: "",
                    label: "",
                    user_id: "",
                },
                search: {
                    order_column: "id",
                    order_type: "desc",
                },
                status: false,
                switchLabel: "",
                isMap: false,
                vuex: false
            },
            selectedAddress: {},
            addressInput: "",
            addressDropdownOpen: false,
            orderTypeEnums: {
                dineIn: orderTypeEnum.DINING_TABLE,
                takeAway: orderTypeEnum.TAKEAWAY,
                delivery: orderTypeEnum.DELIVERY,
            },
            location: {
                lat: null,
                lng: null
            },
            clearAddresses: false,

        }
    },
    computed: {
        focustrap() {
            return focustrap
        },
        setting: function () {
            return this.$store.getters['frontendSetting/lists'];
        },
        categories: function () {
            return this.$store.getters["posCategory/lists"];
        },
        items: function () {
            return this.$store.getters["item/lists"];
        },
        customers: function () {
            return this.$store.getters['user/lists'];
        },
        carts: function () {
            return this.$store.getters['posCart/lists'];
        },
        subtotal: function () {
            return this.$store.getters['posCart/subtotal'];
        },
        posDiscount: function () {
            return this.$store.getters['posCart/discount'];
        },
        direction: function () {
            return this.$store.getters['frontendLanguage/show'].display_mode === displayModeEnum.RTL ? 'rtl' : 'ltr';
        },
        diningtables: function () {
            return this.$store.getters["diningTable/lists"];
        },
        filteredCustomerAddresses: function () {
            if (this.clearAddresses) {
                return [];
            }
            return this.customerAddresses;
        },
        customerAddresses: function () {
            return this.$store.getters["user/addressLists"];
        },
        addressSuggestions: function () {
            const addresses = this.filteredCustomerAddresses || [];
            const typed = (this.addressInput || "").trim().toLowerCase();
            if (!typed) {
                return addresses;
            }
            return addresses.filter((item) => (item.address || "").toLowerCase().includes(typed));
        },
    },
    mounted() {
        this.closeSidebar();
        this.$refs.dineIn.click();
        if (this.$route.query.edit) {
            this.startEdit(this.$route.query.edit);
        } else {
            this.generateToken();
        }
        this.itemCategories();
        this.itemList();
        try {
            this.loading.isActive = true;
            this.$store.dispatch("defaultAccess/show").then((res) => {
                this.checkoutProps.form.branch_id = res.data.data.branch_id
                this.$store.dispatch("frontendBranch/show", this.checkoutProps.form.branch_id).then(res => {
                    this.location = {
                        lat: res.data.data.latitude,
                        lng: res.data.data.longitude
                    };
                }).catch();

            }).catch((err) => {
                this.loading.isActive = false;
            });

            this.loading.isActive = true;
            this.$store.dispatch('user/lists', {
                order_column: 'id',
                order_type: 'asc',
                status: statusEnum.ACTIVE,
                excepts: `${roleEnum.ADMIN}|${roleEnum.CHEF}|${roleEnum.DELIVERY_BOY}`,
            }).then((res) => {
                if (!this.checkoutProps.editOrderId) {
                    this.checkoutProps.form.customer_id = res.data.data[1].id;
                    this.address.form.user_id = res.data.data[1].id;
                    this.gettingUserAddress(this.checkoutProps.form.customer_id);
                }
                this.loading.isActive = false;
            }).catch((err) => {
                this.loading.isActive = false;
            });

            this.loading.isActive = true;
            this.$store.dispatch("company/lists").then((res) => {
                this.company.name = res.data.data.company_name;
                this.company.email = res.data.data.company_email;
                this.company.phone = res.data.data.company_phone;
                this.company.address = res.data.data.company_address;
                this.loading.isActive = false;
            }).catch((err) => {
                this.loading.isActive = false;
            });
            this.loading.isActive = true;
            this.$store
                .dispatch("diningTable/lists", {
                    order_column: 'id',
                    order_type: 'desc',
                    status: statusEnum.ACTIVE,
                })
                .then((res) => {
                    this.loading.isActive = false;
                })
                .catch((err) => {
                    this.loading.isActive = false;
                });

            this.$store
                .dispatch("company/lists")
                .then((companyRes) => {
                    this.$store
                        .dispatch(
                            "countryCode/show",
                            companyRes.data.data.company_country_code
                        )
                        .then((res) => {
                            if (this.customerProps.form.country_code === "") {
                                this.customerProps.form.country_code =
                                    res.data.data.calling_code;
                                this.country_code = res.data.data.calling_code;
                            }
                            this.flag = res.data.data.flag_emoji;
                        })
                        .catch();
                })
                .catch();

        } catch (err) {
            this.loading.isActive = false;
        }

    },
    methods: {
        printLastOrder: function () {
            this.loading.isActive = true;
            this.$store.dispatch('posOrder/lastOrder').then((res) => {
                this.lastOrder = res.data.data;
                this.loading.isActive = false;
                this.$nextTick(() => {
                    appService.modalShow('#lastOrderReceiptModal');
                });
            }).catch((err) => {
                this.loading.isActive = false;
                alertService.error(err.response?.data?.message ?? this.$t('message.no_data_available'));
            });
        },
        onlyNumber: function (e) {
            return appService.onlyNumber(e);
        },
        floatNumber: function (e) {
            return appService.floatNumber(e);
        },
        amountFormat: function (amount, decimal) {
            return appService.amountFormat(amount, decimal);
        },
        withoutCurrency: function (amount) {
            return appService.withoutCurrency(amount);
        },
        openCanvas: function (id) {
            return appService.openCanvas(id);
        },
        closeCanvas: function (id) {
            return appService.closeCanvas(id);
        },
        resetName: function () {
            this.props.search.name = "";
            this.itemList();
        },
        selectDiscount(value) {
            this.discountType = value;
        },
        search: function () {
            this.itemList();
        },
        allCategory: function () {
            this.props.search.name = "";
            this.props.search.item_category_id = "";
            this.itemList();
        },
        updateCategoryEdges: function (swiper) {
            this.categorySwiper = markRaw(swiper);
            this.categoryAtStart = swiper.isBeginning;
            this.categoryAtEnd = swiper.isEnd;
        },
        scrollCategories: function (direction) {
            const swiper = this.categorySwiper;
            if (!swiper) {
                return;
            }
            const target = Math.max(0, Math.min(swiper.activeIndex + direction * 3, swiper.slides.length - 1));
            swiper.slideTo(target);
        },
        closeSidebar: function () {
            this.$store.dispatch("globalState/set", { topSidebar: false });
            document?.querySelector(".db-sidebar")?.classList?.add("active");
            document?.querySelector(".db-main")?.classList?.add("expand");
        },
        itemCategories: function (page = 1) {
            this.loading.isActive = true;
            this.props.search.page = page;
            this.$store.dispatch("posCategory/lists", this.categoryProps).then((res) => {
                this.loading.isActive = false;
            }).catch((err) => {
                this.loading.isActive = false;
            });
        },
        itemList: function (page = 1) {
            this.loading.isActive = true;
            this.props.search.page = page;
            this.$store.dispatch("item/lists", this.props.search).then((res) => {
                this.loading.isActive = false;
            }).catch((err) => {
                this.loading.isActive = false;
            });
        },
        setCategory: function (id) {
            this.props.search.item_category_id = id;
            this.itemList();
        },
        cartQuantityUp: function (id, e) {
            if (e.target.value > 0) {
                this.$store.dispatch('posCart/quantity', { id: id, status: e.target.value }).then().catch();
            }
        },
        cartQuantityIncrement: function (id) {
            this.$store.dispatch('posCart/quantity', { id: id, status: "increment" }).then().catch();
        },
        cartQuantityDecrement: function (id) {
            this.$store.dispatch('posCart/quantity', { id: id, status: "decrement" }).then().catch();
        },
        deleteCartItem: function (id) {
            this.$store.dispatch('posCart/deleteCartItem', { id: id, status: "decrement" }).then().catch();
        },
        addExtraItemRow: function () {
            this.extraItems.push({ name: '', price: '' });
        },
        removeExtraItemRow: function (index) {
            this.extraItems.splice(index, 1);
        },
        addExtraItems: function () {
            const rows = this.extraItems.filter(row => row.name.trim() !== '' || row.price !== '');
            if (rows.length === 0) {
                return alertService.error(this.$t('message.extra_item_required'));
            }
            // The price is optional: free items (e.g. a complimentary drink) go in at 0.
            if (rows.some(row => row.name.trim() === '' || parseFloat(row.price) < 0)) {
                return alertService.error(this.$t('message.extra_item_invalid'));
            }

            this.$store.dispatch('posCart/lists', rows.map(row => {
                const price = parseFloat(row.price) || 0;
                return {
                    is_custom: true,
                    item_id: null,
                    name: row.name.trim(),
                    image: null,
                    instruction: '',
                    discount: 0,
                    quantity: 1,
                    convert_price: price,
                    currency_price: this.amountFormat(price, this.setting.site_digit_after_decimal_point),
                    item_variation_total: 0,
                    item_extra_total: 0,
                    item_variations: { variations: {}, names: {} },
                    item_extras: { extras: [], names: [] },
                };
            })).then().catch();

            this.extraItems = [{ name: '', price: '' }];
        },
        generateToken: function () {
            axios.get('/admin/pos/token').then(res => {
                if (res.data.status) {
                    this.checkoutProps.form.token = res.data.data;
                } else {
                    this.checkoutProps.form.token = String(Math.floor(Math.random() * 1000) + 1).padStart(4, '0');
                }
            }).catch(err => {
                this.checkoutProps.form.token = String(Math.floor(Math.random() * 1000) + 1).padStart(4, '0');
            });
        },
        applyDiscount: function () {
            if (this.discountType == discountTypeEnum.FIXED) {
                if (this.subtotal < this.discount) {
                    return alertService.error(this.$t('message.discount_fixed_error_message'));
                } else {
                    this.checkoutProps.form.discount = parseFloat(+this.discount).toFixed(this.setting.site_digit_after_decimal_point);
                    this.$store.dispatch('posCart/discount', this.checkoutProps.form.discount).then().catch();
                }

            } else {
                if (this.discount > 100) {
                    return alertService.error(this.$t('message.discount_error_message'));
                } else {

                    this.checkoutProps.form.discount = parseFloat((this.subtotal * this.discount) / 100).toFixed(this.setting.site_digit_after_decimal_point);
                    this.$store.dispatch('posCart/discount', this.checkoutProps.form.discount).then().catch();

                }
            }
        },
        resetCart: function () {
            this.exitEdit();
            this.$store.dispatch('posCart/resetCart').then(res => {
                this.generateToken();
            }).catch();
        },
        startEdit: function (id) {
            this.checkoutProps.editOrderId = id;
            this.loading.isActive = true;
            this.$store.dispatch('posOrder/edit', id).then((res) => {
                this.loading.isActive = false;
                this.applyEditOrder(res.data.data);
            }).catch((err) => {
                this.loading.isActive = false;
                alertService.error(err.response?.data?.message ?? this.$t('message.no_data_available'));
                this.resetCart();
            });
        },
        // Loads a saved order back into the cart in the same shape ItemComponent adds items in
        applyEditOrder: function (order) {
            this.editOrder = { id: order.id, order_serial_no: order.order_serial_no };
            this.checkoutProps.editOrderId = order.id;

            this.$store.dispatch('posCart/resetCart').then().catch();
            this.$store.dispatch('posCart/lists', order.order_items.map(item => {
                const variations = {};
                const variationNames = {};
                _.forEach(item.item_variations, (variation) => {
                    variations[variation.item_attribute_id] = variation.id;
                    variationNames[variation.variation_name] = variation.name;
                });

                const extras = [];
                const extraNames = [];
                _.forEach(item.item_extras, (extra) => {
                    extras.push(extra.id);
                    extraNames.push(extra.name);
                });

                return {
                    is_custom: item.is_custom,
                    item_id: item.item_id,
                    name: item.name,
                    image: item.image,
                    instruction: item.instruction ?? '',
                    discount: item.discount,
                    quantity: item.quantity,
                    convert_price: item.price,
                    currency_price: this.amountFormat(item.price, this.setting.site_digit_after_decimal_point),
                    item_variation_total: item.item_variation_total,
                    item_extra_total: item.item_extra_total,
                    item_variations: { variations: variations, names: variationNames },
                    item_extras: { extras: extras, names: extraNames },
                };
            })).then().catch();

            this.discountType = discountTypeEnum.FIXED;
            this.discount = order.discount > 0 ? order.discount : null;
            this.checkoutProps.form.discount = order.discount;
            this.$store.dispatch('posCart/discount', order.discount).then().catch();

            this.checkoutProps.form.token = order.token;
            this.checkoutProps.form.customer_id = order.customer_id;
            this.address.form.user_id = order.customer_id;
            this.temp.instruction = order.instruction ?? '';
            this.checkoutProps.form.pos_payment_method = order.pos_payment_method ?? posPaymentMethodEnum.CASH;

            if (order.order_type === orderTypeEnum.DELIVERY) {
                this.deliveryOrder();
                this.checkoutProps.form.order_type = orderTypeEnum.DELIVERY;
                this.deliveryChargeInput = order.delivery_charge ? String(order.delivery_charge) : '';
                this.checkoutProps.form.delivery_charge = order.delivery_charge;
                this.deliveryChargeEdited = true;
                this.addressInput = order.address ?? '';
                // Link the order's address to the customer's saved one once those are loaded
                this.$store.dispatch("user/addressLists", {
                    id: order.customer_id,
                    order_column: "id",
                    order_type: "desc",
                }).then(() => {
                    this.resolveAddressInput();
                }).catch();
            } else {
                if (order.order_type === orderTypeEnum.TAKEAWAY) {
                    this.takeAwayOrder();
                } else {
                    this.dineInOrder();
                }
                this.checkoutProps.form.order_type = order.order_type;
                this.checkoutProps.form.dining_table_id = order.dining_table_id;
                this.gettingUserAddress(order.customer_id);
            }
        },
        exitEdit: function () {
            if (!this.checkoutProps.editOrderId) {
                return;
            }
            this.editOrder = null;
            this.checkoutProps.editOrderId = null;
            if (this.$route.query.edit) {
                this.$router.replace({ name: 'admin.pos' });
            }
        },
        orderSaved: function () {
            // The payment modal already cleared the cart and fetched a fresh token
            this.exitEdit();
            this.temp.instruction = '';
        },
        orderSubmit: function () {
            this.loading.isActive = true;
            // Quantity changes clear the cart discount, so take the one currently shown
            this.checkoutProps.form.discount = parseFloat(this.posDiscount) || 0;
            this.checkoutProps.form.subtotal = this.subtotal;
            this.checkoutProps.form.total = parseFloat(this.subtotal + this.checkoutProps.form.delivery_charge - this.checkoutProps.form.discount).toFixed(this.setting.site_digit_after_decimal_point);
            this.checkoutProps.form.items = [];
            _.forEach(this.carts, (item, index) => {
                let item_variations = [];
                if (Object.keys(item.item_variations.variations).length > 0) {
                    _.forEach(item.item_variations.variations, (value, index) => {
                        item_variations.push({
                            "id": value,
                            "item_id": item.item_id,
                            "item_attribute_id": index,
                        });
                    });
                }

                if (Object.keys(item.item_variations.names).length > 0) {
                    let i = 0;
                    _.forEach(item.item_variations.names, (value, index) => {
                        item_variations[i].variation_name = index;
                        item_variations[i].name = value;
                        i++;
                    });
                }

                let item_extras = [];
                if (item.item_extras.extras.length) {
                    _.forEach(item.item_extras.extras, (value) => {
                        item_extras.push({
                            id: value,
                            item_id: item.item_id,
                        });
                    });
                }

                if (item.item_extras.names.length) {
                    let i = 0;
                    _.forEach(item.item_extras.names, (value) => {
                        item_extras[i].name = value;
                        i++;
                    });
                }

                this.checkoutProps.form.items.push({
                    item_id: item.item_id,
                    is_custom: item.is_custom ?? false,
                    item_name: item.is_custom ? item.name : null,
                    item_price: item.convert_price,
                    branch_id: this.checkoutProps.form.branch_id,
                    instruction: item.instruction,
                    quantity: item.quantity,
                    discount: item.discount,
                    total_price: item.total,
                    item_variation_total: item.item_variation_total,
                    item_extra_total: item.item_extra_total,
                    item_variations: item_variations,
                    item_extras: item_extras
                });
            });
            this.checkoutProps.form.items = JSON.stringify(this.checkoutProps.form.items);
            // include special instructions from the POS temp input
            this.checkoutProps.form.instruction = this.temp.instruction;
            this.loading.isActive = false;
            if (!this.checkoutProps.form.token) {
                return alertService.error(this.$t("message.token_field_required"));
            }
            if (this.checkoutProps.form.order_type === orderTypeEnum.DINING_TABLE && !this.checkoutProps.form.dining_table_id) {
                return alertService.error(this.$t("message.table_field_required"));
            }
            if (this.checkoutProps.form.order_type === orderTypeEnum.DELIVERY) {
                if (!(this.addressInput || "").trim()) {
                    return alertService.error(this.$t("message.address_field_required"));
                }
                return this.saveTypedAddress()
                    .then(() => {
                        appService.modalShow('#orderpayment');
                    })
                    .catch((err) => {
                        alertService.error(err?.response?.data?.message || this.$t("message.address_field_required"));
                    });
            }
            appService.modalShow('#orderpayment');
        },
        totalItems: function () {
            if (this.carts.length > 0) {
                let totalItem = 0;
                this.carts.forEach(cart => {
                    totalItem += cart.quantity;
                });
                return totalItem;
            }
        },
        phoneNumber(e) {
            return appService.phoneNumber(e);
        },
        addCustomers: function () {
            appService.modalShow("#addCustomer");
        },
        resetCustomer: function () {
            appService.modalHide("#addCustomer");
            this.$store.dispatch("user/reset").then().catch();
            this.errors = {};
            this.customerProps.form = {
                name: "",
                email: "",
                phone: "",
                password: "123456",
                password_confirmation: "123456",
                status: statusEnum.ACTIVE,
                country_code: this.country_code,
            };
        },
        saveCustomer: function () {
            try {
                this.loading.isActive = true;
                this.$store
                    .dispatch("user/save", this.customerProps)
                    .then((res) => {
                        appService.modalHide("#addCustomer");
                        alertService.successFlip(0, this.$t("menu.customers"));
                        this.$store
                            .dispatch("user/lists", {
                                order_column: "id",
                                order_type: "asc",
                                status: statusEnum.ACTIVE,
                                vuex: true
                            })
                            .then((customerResponse) => {
                                this.loading.isActive = false;
                                this.checkoutProps.form.customer_id = res.data.data.id;
                                this.address.form.user_id = res.data.data.id;
                                this.selectedAddress = {};
                                this.gettingUserAddress(this.checkoutProps.form.customer_id);
                            })
                            .catch((err) => {
                                this.loading.isActive = false;
                            });

                        this.customerProps.form = {
                            name: "",
                            email: "",
                            phone: "",
                            password: "123456",
                            password_confirmation: "123456",
                            status: statusEnum.ACTIVE,
                            country_code: this.country_code,
                        };
                        this.errors = {};
                    })
                    .catch((err) => {
                        this.loading.isActive = false;
                        this.errors = err.response.data.errors;
                    });
            } catch (err) {
                this.loading.isActive = false;
                alertService.error(err);
            }
        },
        dineInOrder: function () {
            this.resetAddressField();

            this.$refs.dineIn.classList.add('active');
            this.$refs.dineInDiv.classList.add('block');
            this.$refs.dineInDiv.classList.remove('hidden');
            this.$refs.takeAway.classList.remove('active');
            this.$refs.deliveryOrderLabel.classList.remove('active');
            this.$refs.deliveryOrderDiv.classList.add('hidden');
            this.$refs.deliveryOrderDiv.classList.remove('block');
        },
        takeAwayOrder: function () {
            this.checkoutProps.form.dining_table_id = null;
            this.resetAddressField();

            this.$refs.takeAway.classList.add('active');
            this.$refs.dineIn.classList.remove('active');
            this.$refs.dineInDiv.classList.add('hidden');
            this.$refs.dineInDiv.classList.remove('block');
            this.$refs.deliveryOrderLabel.classList.remove('active');
            this.$refs.deliveryOrderDiv.classList.add('hidden');
            this.$refs.deliveryOrderDiv.classList.remove('block');
        },
        deliveryOrder: function () {
            this.checkoutProps.form.dining_table_id = null;
            this.resolveAddressInput();

            this.$refs.deliveryOrderLabel.classList.add('active');
            this.$refs.deliveryOrderDiv.classList.add('block');
            this.$refs.deliveryOrderDiv.classList.remove('hidden');
            this.$refs.dineIn.classList.remove('active');
            this.$refs.dineInDiv.classList.add('hidden');
            this.$refs.dineInDiv.classList.remove('block');
            this.$refs.takeAway.classList.remove('active');
        },

        gettingUserAddress: function (userId) {
            this.$store
                .dispatch("user/addressLists", {
                    id: userId,
                    order_column: "id",
                    order_type: "desc",
                })
                .then()
                .catch();
        },
        onAddressInput: function () {
            this.addressDropdownOpen = true;
            this.resolveAddressInput();
        },
        chooseSavedAddress: function (address) {
            this.addressInput = address.address;
            this.addressDropdownOpen = false;
            this.applyAddress(address);
        },
        // Links the typed text to an already saved address when it matches one,
        // otherwise leaves it unsaved until the order is submitted.
        resolveAddressInput: function () {
            const typed = (this.addressInput || "").trim().toLowerCase();
            const match = (this.customerAddresses || []).find((item) => (item.address || "").trim().toLowerCase() === typed);
            if (typed && match) {
                this.applyAddress(match);
            } else {
                this.checkoutProps.form.address_id = null;
                this.selectedAddress = {};
                this.setDeliveryCharge();
            }
        },
        applyAddress: function (address) {
            this.selectedAddress = address;
            this.checkoutProps.form.address_id = address.id;
            this.setDeliveryCharge();
        },
        setDeliveryCharge: function () {
            // A charge typed by hand wins over the default until the order type or
            // the customer changes, since the amount depends on the delivery location.
            if (this.deliveryChargeEdited) {
                return;
            }
            if (this.checkoutProps.form.order_type !== orderTypeEnum.DELIVERY) {
                this.checkoutProps.form.delivery_charge = 0;
                this.deliveryChargeInput = '';
                return;
            }
            const defaultCharge = parseFloat(this.setting.order_setup_basic_delivery_charge || 0);
            this.checkoutProps.form.delivery_charge = isNaN(defaultCharge) ? 0 : defaultCharge;
            this.deliveryChargeInput = this.checkoutProps.form.delivery_charge ? String(this.checkoutProps.form.delivery_charge) : '';
        },
        applyDeliveryCharge: function () {
            const amount = parseFloat(this.deliveryChargeInput);
            this.checkoutProps.form.delivery_charge = isNaN(amount) ? 0 : amount;
            this.deliveryChargeEdited = true;
        },
        normalizeDeliveryCharge: function () {
            const amount = parseFloat(this.deliveryChargeInput);
            if (isNaN(amount)) {
                this.deliveryChargeInput = '';
                this.checkoutProps.form.delivery_charge = 0;
                return;
            }
            this.deliveryChargeInput = String(amount);
            this.checkoutProps.form.delivery_charge = amount;
        },
        // Stores a newly typed address against the customer so it shows up in the
        // dropdown next time, then resolves with the address id to use.
        saveTypedAddress: function () {
            return new Promise((resolve, reject) => {
                if (this.checkoutProps.form.address_id) {
                    return resolve(this.checkoutProps.form.address_id);
                }
                const typed = (this.addressInput || "").trim();
                const customerId = this.checkoutProps.form.customer_id;
                if (!typed || !customerId) {
                    return reject(new Error(this.$t("message.address_field_required")));
                }
                this.loading.isActive = true;
                this.$store
                    .dispatch("user/saveAddress", {
                        search: this.address.search,
                        id: customerId,
                        form: {
                            address: typed,
                            apartment: "",
                            label: typed.substring(0, 190),
                            latitude: "0",
                            longitude: "0",
                            user_id: customerId,
                        },
                    })
                    .then((res) => {
                        this.loading.isActive = false;
                        this.applyAddress(res.data.data);
                        resolve(res.data.data.id);
                    })
                    .catch((err) => {
                        this.loading.isActive = false;
                        reject(err);
                    });
            });
        },
        changingUser: function () {
            if (this.checkoutProps.form.customer_id !== null) {
                this.clearAddresses = false;
                this.gettingUserAddress(this.checkoutProps.form.customer_id);
            } else {
                this.clearAddresses = true;
            }
            this.address.form.user_id = this.checkoutProps.form.customer_id;
            this.resetAddressField();
        },
        resetAddressField: function () {
            this.addressInput = "";
            this.addressDropdownOpen = false;
            this.selectedAddress = {};
            this.checkoutProps.form.address_id = null;
            this.checkoutProps.form.delivery_charge = 0;
            this.deliveryChargeInput = '';
            this.deliveryChargeEdited = false;
        },
    },
    beforeUnmount() {
        // Don't leave an edited order's items behind in the cart for the next new order
        if (this.checkoutProps.editOrderId) {
            this.$store.dispatch('posCart/resetCart').then().catch();
        }
    },
    watch: {
        "customerProps.form.password"(newValue) {
            this.customerProps.form.password_confirmation = newValue;
        },
        "$route.query.edit"(id) {
            if (id && String(id) !== String(this.checkoutProps.editOrderId)) {
                this.startEdit(id);
            } else if (!id && this.checkoutProps.editOrderId) {
                this.resetCart();
            }
        },
        carts: {
            handler(newCarts) {
                // Emptying the cart of an order being edited keeps that order's customer and type
                if ((!newCarts || newCarts.length === 0) && !this.checkoutProps.editOrderId) {
                    this.discount = null;
                    this.discountType = discountTypeEnum.PERCENTAGE;
                    this.$nextTick(() => {
                        if (this.$refs.dineIn) {
                            this.$refs.dineIn.click();
                            this.resetAddressField();
                            if (this.customers.length > 1) {
                                this.checkoutProps.form.customer_id = this.customers[1].id;
                                this.address.form.user_id = this.customers[1].id;
                                this.gettingUserAddress(this.checkoutProps.form.customer_id);
                            }

                        }
                    });
                }
            },
            deep: true,
            immediate: true
        }
    },
}
</script>
