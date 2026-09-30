<template>
    <LoadingComponent :props="loading" />
    <section class="mb-16 mt-8">
        <div class="container">
            <div v-if="categories.length > 0" class="swiper mb-12 menu-swiper pb-4">
                <CategoryComponent :categories="categories" :design="categoryProps.design" :padding="'pb-4'" />
            </div>

            <div v-if="categories.length > 0" class="flex flex-wrap gap-3 w-full mb-12 veg-navs">
                <button :disabled="itemProps.type !== null && itemProps.type === itemTypeEnum.VEG"
                    @click.prevent="itemProps.type === itemTypeEnum.NON_VEG ? itemTypeReset() : itemTypeSet(itemTypeEnum.NON_VEG)"
                    :class="itemProps.type === itemTypeEnum.NON_VEG ? 'veg-active' : ''" type="button"
                    class="flex items-center gap-3 w-fit pl-3 pr-4 py-1.5 rounded-3xl transition hover:shadow-filter hover:bg-white bg-[#EFF0F6]">
                    <img :src="setting.image_vag" alt="category" class="h-6">
                    <span class="capitalize text-sm font-medium text-heading">{{ $t('label.frontend_non_veg') }}</span>
                    <i
                        class="lab-close-circle-line text-xl text-red-500 transition opacity-0 ltr:-ml-8 rtl:-mr-8 clear-item-type-filter font-fill-danger lab-font-size-24"></i>
                </button>
                <button :disabled="itemProps.type !== null && itemProps.type === itemTypeEnum.NON_VEG"
                    @click.prevent="itemProps.type === itemTypeEnum.VEG ? itemTypeReset() : itemTypeSet(itemTypeEnum.VEG)"
                    :class="itemProps.type === itemTypeEnum.VEG ? 'veg-active' : ''" type="button"
                    class="flex items-center gap-3 w-fit pl-3 pr-4 py-1.5 rounded-3xl transition hover:shadow-filter hover:bg-white bg-[#EFF0F6]">
                    <img :src="setting.image_non_vag" alt="category" class="h-6">
                    <span class="capitalize text-sm font-medium text-heading">{{ $t('label.veg') }}</span>
                    <i
                        class="lab-close-circle-line text-xl text-red-500 transition opacity-0 ltr:-ml-8 rtl:-mr-8 font-fill-danger lab-font-size-24"></i>
                </button>
            </div>

            <!-- View toggle buttons -->
            <div class="flex gap-4 flex-col sm:flex-row items-center justify-between mb-6">
                <h2 class="capitalize text-[36px] leading-[40px] font-semibold text-center sm:text-left text-primary">
                    {{ selectedCategorySlug ? (category.name || $t('label.menu')) : $t('label.all_items') || 'All Items' }}
                </h2>
                <div class="flex items-center gap-3">
                    <button type="button" class="lab lab-row-vertical lab-font-size-20 text-xl"
                        v-on:click="itemProps.design = itemDesignEnum.LIST"
                        :class="itemProps.design === itemDesignEnum.LIST ? 'text-primary' : 'text-[#A0A3BD]'"></button>
                    <button type="button" class="lab lab-element-3 lab-font-size-20 text-xl"
                        v-on:click="itemProps.design = itemDesignEnum.GRID"
                        :class="itemProps.design === itemDesignEnum.GRID ? 'text-primary' : 'text-[#A0A3BD]'"></button>
                </div>
            </div>

            <!-- Display items grouped by category -->
            <div v-if="groupedCategories.length > 0">
                <div v-for="categoryGroup in groupedCategories" :key="categoryGroup.id" class="mb-12">
                    <!-- Category heading (only show when not filtering by specific category) -->
                    <h3 v-if="!selectedCategorySlug" class="text-3xl font-semibold text-heading mb-6 pb-4 border-b-2 border-primary/20">
                        {{ categoryGroup.name }}
                        <span class="text-sm text-gray-500 font-normal ml-2">({{ categoryGroup.items.length }} {{ categoryGroup.items.length === 1 ? 'item' : 'items' }})</span>
                    </h3>
                    
                    <!-- Items for this category -->
                    <ItemComponent :items="categoryGroup.items" :type="itemProps.type" :design="itemProps.design" />
                </div>
            </div>

            <div class="mt-12" v-else>
                <div class="max-w-[250px] mx-auto">
                    <img class="w-full mb-8" :src="setting.image_order_not_found" alt="image_order_not_found">
                </div>
                <span class="w-full mb-4 text-center text-black">{{ $t('message.no_data_available') }}</span>
            </div>
        </div>
    </section>
</template>

<script>

import statusEnum from "../../../enums/modules/statusEnum";
import categoryDesignEnum from "../../../enums/modules/categoryDesignEnum";
import CategoryComponent from "../components/CategoryComponent";
import ItemComponent from "../components/ItemComponent";
import itemDesignEnum from "../../../enums/modules/itemDesignEnum";
import itemTypeEnum from "../../../enums/modules/itemTypeEnum";
import LoadingComponent from "../components/LoadingComponent";

export default {
    name: "MenuComponent",
    components: { CategoryComponent, ItemComponent, LoadingComponent },
    data() {
        return {
            loading: {
                isActive: false
            },
            itemTypeEnum: itemTypeEnum,
            itemDesignEnum: itemDesignEnum,
            allItems: [], // Store all items
            selectedCategorySlug: null, // Track selected category
            categoryProps: {
                search: {
                    paginate: 0,
                    order_column: 'sort',
                    order_type: 'asc',
                    status: statusEnum.ACTIVE
                },
                design: categoryDesignEnum.SECOND
            },
            itemProps: {
                design: itemDesignEnum.LIST,
                type: null
            }
        }
    },
    computed: {
        categories: function () {
            return this.$store.getters['frontendItemCategory/lists'];
        },
        setting: function () {
            return this.$store.getters['frontendSetting/lists'];
        },
        category: function () {
            // Get current category based on selected slug
            if (this.selectedCategorySlug && this.categories.length > 0) {
                return this.categories.find(cat => cat.slug === this.selectedCategorySlug) || {};
            }
            return {};
        },
        filteredItems: function () {
            let items = this.allItems;

            // Filter by category if one is selected
            if (this.selectedCategorySlug) {
                items = items.filter(item => item.item_category_slug === this.selectedCategorySlug);
            }

            // Filter by veg/non-veg type if selected
            if (this.itemProps.type !== null) {
                items = items.filter(item => item.item_type === this.itemProps.type);
            }

            return items;
        },
        groupedCategories: function () {
            // Group filtered items by category
            const grouped = {};
            const filteredItems = this.filteredItems;

            // Group items by category
            filteredItems.forEach(item => {
                const categoryId = item.item_category_id;
                const categorySlug = item.item_category_slug;
                const categoryName = item.category_name;

                if (!grouped[categoryId]) {
                    grouped[categoryId] = {
                        id: categoryId,
                        slug: categorySlug,
                        name: categoryName || 'Uncategorized',
                        items: []
                    };
                }
                grouped[categoryId].items.push(item);
            });

            // Convert to array and sort by category order from categories list
            const groupedArray = Object.values(grouped);
            
            // Sort categories based on the original categories order
            groupedArray.sort((a, b) => {
                const indexA = this.categories.findIndex(cat => cat.id === a.id);
                const indexB = this.categories.findIndex(cat => cat.id === b.id);
                return indexA - indexB;
            });

            return groupedArray;
        },
        items: function () {
            // Return in the same format expected by ItemComponent
            return {
                items: this.filteredItems
            };
        },
        hasItems() {
            return this.filteredItems.length > 0;
        }
    },
    mounted() {
        this.loading.isActive = true;
        
        // Load categories
        this.$store.dispatch("frontendItemCategory/lists", this.categoryProps.search).then((res) => {
            // After categories are loaded, load all items
            this.loadAllItems();
        }).catch((err) => {
            this.loading.isActive = false;
        });
        
        // Set initial category from query string
        this.updateCategoryFromRoute();
    },
    methods: {
        loadAllItems: function () {
            // Load all items without category filter
            this.$store.dispatch("frontendItem/lists", {
                paginate: 0,
                order_column: 'id',
                order_type: 'desc',
                status: statusEnum.ACTIVE,
                vuex: false
            }).then((res) => {
                this.allItems = res.data.data || [];
                this.loading.isActive = false;
            }).catch((err) => {
                this.loading.isActive = false;
            });
        },
        itemTypeSet: function (e) {
            this.itemProps.type = e;
        },
        itemTypeReset: function () {
            this.itemProps.type = null;
        },
        updateCategoryFromRoute: function () {
            // Update selected category based on URL query parameter
            if (typeof this.$route.query.s !== "undefined" && this.$route.query.s !== "") {
                this.selectedCategorySlug = this.$route.query.s;
            } else {
                this.selectedCategorySlug = null;
            }
        }
    },
    watch: {
        $route() {
            // Update category when route changes (user clicks different category)
            this.updateCategoryFromRoute();
        }
    }
}
</script>