<template>
    <LoadingComponent :props="loading" />

    <div class="db-card db-tab-div active">
        <div class="db-card-header border-none">
            <h3 class="db-card-title">{{ $t("menu.blogs") }}</h3>
            <div class="db-card-filter">
                <TableLimitComponent :method="list" :search="props.search" :page="paginationPage" />
                <BlogCreateComponent :props="props" />
            </div>
        </div>

        <div class="db-table-responsive">
            <table class="db-table stripe">
                <thead class="db-table-head">
                    <tr class="db-table-head-tr">
                        <th class="db-table-head-th">
                            {{ $t("label.title") }}
                        </th>
                        <th class="db-table-head-th">
                            {{ $t("label.status") }}
                        </th>
                        <th class="db-table-head-th">
                            {{ $t("label.action") }}
                        </th>
                    </tr>
                </thead>
                <tbody class="db-table-body" v-if="pages.length > 0">
                    <tr class="db-table-body-tr" v-for="page in pages" :key="page.id">
                        <td class="db-table-body-td">
                            {{ textShortener(page.title) }}
                        </td>
                        <td class="db-table-body-td">
                            <span :class="statusClass(page.status)">
                                {{ enums.statusEnumArray[page.status] }}
                            </span>
                        </td>
                        <td class="db-table-body-td">
                            <div class="flex justify-start items-center sm:items-start sm:justify-start gap-1.5">
                                <SmViewComponent :link="'admin.settings.blogs.show'" :id="page.id" />
                                <SmModalEditComponent @click="edit(page)" />
                                <SmDeleteComponent @click="destroy(page.id)" />
                            </div>
                        </td>
                    </tr>
                </tbody>
                <tbody class="db-table-body" v-else>
                    <tr class="db-table-body-tr">
                        <td class="db-table-body-td text-center" colspan="7">
                            <div class="p-4">
                                <div class="max-w-[300px] mx-auto mt-2">
                                    <img class="w-full h-full" :src="ENV.API_URL + '/images/default/not-found.png'"
                                        alt="Not Found">
                                </div>
                                <span class="d-block mt-3 text-lg">{{ $t('message.no_data_available') }}</span>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="flex items-center justify-between border-t border-gray-200 bg-white px-4 py-6"
            v-if="pages.length > 0">
            <PaginationSMBox :pagination="pagination" :method="list" />
            <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
                <PaginationTextComponent :props="{ page: paginationPage }" />
                <PaginationBox :pagination="pagination" :method="list" />
            </div>
        </div>
    </div>
</template>
<script>
import LoadingComponent from "../../components/LoadingComponent";
import BlogCreateComponent from "./BlogCreateComponent";
import alertService from "../../../../services/alertService";
import PaginationTextComponent from "../../components/pagination/PaginationTextComponent";
import PaginationBox from "../../components/pagination/PaginationBox";
import PaginationSMBox from "../../components/pagination/PaginationSMBox";
import appService from "../../../../services/appService";
import statusEnum from "../../../../enums/modules/statusEnum";
import TableLimitComponent from "../../components/TableLimitComponent";
import SmDeleteComponent from "../../components/buttons/SmDeleteComponent";
import SmModalEditComponent from "../../components/buttons/SmModalEditComponent";
import SmViewComponent from "../../components/buttons/SmViewComponent";
import ENV from "../../../../config/env";

export default {
    name: "BlogListComponent",
    components: {
        TableLimitComponent,
        PaginationSMBox,
        PaginationBox,
        PaginationTextComponent,
        BlogCreateComponent,
        LoadingComponent,
        SmDeleteComponent,
        SmModalEditComponent,
        SmViewComponent,
    },
    data() {
        return {
            loading: {
                isActive: false,
            },
            enums: {
                statusEnum: statusEnum,
                statusEnumArray: {
                    [statusEnum.ACTIVE]: this.$t("label.active"),
                    [statusEnum.INACTIVE]: this.$t("label.inactive"),
                },
            },
            props: {
                form: {
                    title: "",
                    description: "",
                    template_id: null,
                    status: statusEnum.ACTIVE,
                },
                search: {
                    paginate: 1,
                    page: 1,
                    per_page: 10,
                    order_column: "id",
                    order_type: "desc",
                },
            },
            ENV: ENV
        };
    },
    mounted() {
        this.list();
        this.$store.dispatch("menuSection/lists");
        this.$store.dispatch("menuTemplate/lists");
    },
    computed: {
        pages: function () {
            return this.$store.getters["blog/lists"];
        },
        pagination: function () {
            return this.$store.getters["blog/pagination"];
        },
        paginationPage: function () {
            return this.$store.getters["blog/page"];
        },
        menuSections: function () {
            return this.$store.getters["menuSection/lists"];
        },
        menuTemplates: function () {
            return this.$store.getters["menuTemplate/lists"];
        },
    },
    methods: {
        statusClass: function (status) {
            return appService.statusClass(status);
        },
        textShortener: function (text, number = 30) {
            return appService.textShortener(text, number);
        },
        list: function (page = 1) {
            this.loading.isActive = true;
            this.props.search.page = page;
            this.$store
                .dispatch("blog/lists", this.props.search)
                .then((res) => {
                    this.loading.isActive = false;
                })
                .catch((err) => {
                    this.loading.isActive = false;
                });
        },
        edit: function (page) {
            appService.modalShow();
            this.loading.isActive = true;
            // mark as editing in store
            this.$store.dispatch("blog/edit", page.id);

            // fetch full blog details to populate category/content and other fields
            this.$store
                .dispatch("blog/show", page.id)
                .then((res) => {
                    const b = res && res.data && res.data.data ? res.data.data : page;
                    this.props.form = {
                        title: b.title || page.title || "",
                        status: typeof b.status !== 'undefined' ? b.status : page.status,
                        description: b.description || page.description || "",
                        content: b.content || "",
                        category: b.category || "",
                        menu_section_id: typeof b.menu_section_id !== 'undefined' ? (b.menu_section_id === 0 ? null : b.menu_section_id) : null,
                        template_id: typeof b.template_id !== 'undefined' ? (b.template_id === 0 ? null : b.template_id) : null,
                    };
                    this.loading.isActive = false;
                })
                .catch(() => {
                    // fallback to available list data if show fails
                    this.props.form = {
                        title: page.title,
                        status: page.status,
                        description: page.description,
                        content: "",
                        category: page.category || "",
                        menu_section_id: page.menu_section_id || null,
                        template_id: page.template_id === 0 ? null : page.template_id,
                    };
                    this.loading.isActive = false;
                });
        },
        destroy: function (id) {
            appService
                .destroyConfirmation()
                .then((res) => {
                    try {
                        this.loading.isActive = true;
                        this.$store
                            .dispatch("blog/destroy", {
                                id: id,
                                search: this.props.search,
                            })
                            .then((res) => {
                                this.loading.isActive = false;
                                alertService.successFlip(
                                    null,
                                    this.$t("menu.blogs")
                                );
                            })
                            .catch((err) => {
                                this.loading.isActive = false;
                                alertService.error(err.response.data.message);
                            });
                    } catch (err) {
                        this.loading.isActive = false;
                        alertService.error(err.response.data.message);
                    }
                })
                .catch((err) => {
                    this.loading.isActive = false;
                });
        },
    },
};
</script>