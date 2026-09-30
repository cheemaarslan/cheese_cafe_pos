<template>
    <LoadingComponent :props="loading" />

    <div class="db-card db-tab-div active">
        <div class="db-card-header border-none">
            <h3 class="db-card-title">{{ $t("menu.settings_tabs") }}</h3>
        </div>

        <div class="db-table-responsive">
            <table class="db-table stripe">
                <thead class="db-table-head">
                    <tr class="db-table-head-tr">
                        <th class="db-table-head-th">{{ $t("label.menu_item") }}</th>
                        <th class="db-table-head-th">{{ $t("label.status") }}</th>
                    </tr>
                </thead>
                <tbody class="db-table-body" v-if="menus.length > 0">
                    <tr class="db-table-body-tr" v-for="menu in menus" :key="menu.id">
                        <td class="db-table-body-td">
                            <i class="text-sm" :class="menu.icon" v-if="menu.icon"></i>
                            {{ $t('menu.' + menu.language) }}
                        </td>
                        <td class="db-table-body-td">
                            <label for="switcher"
                                class="cs-custom-switcher flex justify-start items-center sm:items-start sm:justify-start gap-1.5">
                                <input type="checkbox" :id="'switcher-' + menu.id"
                                    :checked="menu.status !== enums.statusEnum.INACTIVE"
                                    @change="changeStatus($event, menu.id)">
                            </label>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<script>
import LoadingComponent from "../../components/LoadingComponent";
import alertService from "../../../../services/alertService";
import statusEnum from "../../../../enums/modules/statusEnum";

export default {
    name: "SettingsMenuComponent",
    components: { LoadingComponent },
    data() {
        return {
            loading: {
                isActive: false,
            },
            enums: {
                statusEnum: statusEnum,
            },
        };
    },
    mounted() {
        this.list();
    },
    computed: {
        menus: function () {
            return this.$store.getters["settingsMenu/lists"];
        },
    },
    methods: {
        list: function () {
            this.loading.isActive = true;
            this.$store
                .dispatch("settingsMenu/lists")
                .then((res) => {
                    this.loading.isActive = false;
                })
                .catch((err) => {
                    this.loading.isActive = false;
                });
        },
        changeStatus: function (e, id) {
            try {
                this.loading.isActive = true;
                const isChecked = e.target.checked;
                const status = isChecked ? statusEnum.ACTIVE : statusEnum.INACTIVE;
                this.$store.dispatch('settingsMenu/changeStatus', {
                    id: id,
                    form: { status: status },
                }).then((res) => {
                    this.loading.isActive = false;
                    alertService.successFlip(1, this.$t('menu.settings_tabs'));
                }).catch((err) => {
                    this.loading.isActive = false;
                    alertService.error(err.response.data.message);
                });
            } catch (err) {
                this.loading.isActive = false;
                alertService.error(err.response.data.message);
            }
        },
    },
};
</script>
