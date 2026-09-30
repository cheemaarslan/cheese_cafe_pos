<template>
  <LoadingComponent :props="loading" />
  <div class="db-card db-tab-div active">
    <div class="db-card-header border-none">
      <h3 class="db-card-title">{{ $t("label.loyalty_points") }}</h3>
      <div class="db-card-filter">
        <TableLimitComponent :method="list" :search="loyaltyPoint.search" :page="paginationPage" />
        <!-- <CustomerLoyaltyPointCreateComponent :props="loyaltyPoint" /> -->
      </div>
    </div>

    <div class="db-table-responsive">
      <table class="db-table stripe">
        <thead class="db-table-head">
          <tr class="db-table-head-tr">
            <th class="db-table-head-th">
              {{ $t("label.order_id") }}
            </th>
            <th class="db-table-head-th">
              {{ $t("label.order_subtotal") }}
            </th>
            <th class="db-table-head-th">
              {{ $t("label.percentage") }}
            </th>
            <th class="db-table-head-th">
              {{ $t("label.points") }}
            </th>
            <th class="db-table-head-th">
              {{ $t("label.status") }}
            </th>
            <th class="db-table-head-th">
              {{ $t("label.earned_at") }}
            </th>
            <th class="db-table-head-th">
              {{ $t("label.action") }}
            </th>
          </tr>
        </thead>
        <tbody class="db-table-body" v-if="loyaltyPoints.length > 0">
          <tr class="db-table-body-tr" v-for="loyaltyPoint in loyaltyPoints" :key="loyaltyPoint">
            <td class="db-table-body-td">
              {{ loyaltyPoint.order_id ? '#' + loyaltyPoint.order_id : '-' }}
            </td>
            <td class="db-table-body-td">
              {{ loyaltyPoint.order_subtotal }}
            </td>
            <td class="db-table-body-td">
              {{ loyaltyPoint.percentage }}%
            </td>
            <td class="db-table-body-td">
              {{ loyaltyPoint.points }}
            </td>
            <td class="db-table-body-td">
              <span :class="statusClass(loyaltyPoint.status)">
                {{ loyaltyPoint.status_text }}
              </span>
            </td>
            <td class="db-table-body-td">
              {{ loyaltyPoint.earned_at || '-' }}
            </td>
            <td class="db-table-body-td">
              <div class="flex justify-start items-center sm:items-start sm:justify-start gap-1.5">
                <!-- <SmIconSidebarModalEditComponent @click="edit(loyaltyPoint)" /> -->
                <SmIconDeleteComponent @click="destroy(loyaltyPoint.id)" />
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <div class="flex items-center justify-between border-t border-gray-200 bg-white px-4 py-6" v-if="loyaltyPoints.length > 0">
      <PaginationSMBox :pagination="pagination" :method="list" />
      <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
        <PaginationTextComponent :props="{ page: paginationPage }" />
        <PaginationBox :pagination="pagination" :method="list" />
      </div>
    </div>

    <div class="w-full p-4 text-center" v-else>
      <div class="max-w-[300px] mx-auto mt-2">
        <img class="w-full h-full" :src="ENV.API_URL + '/images/default/not-found.png'" alt="Not Found">
      </div>
      <span class="d-block mt-3 text-lg">{{ $t('message.no_data_available') }}</span>
    </div>
  </div>
</template>

<script>
import alertService from "../../../../services/alertService";
import LoadingComponent from "../../components/LoadingComponent";
import TableLimitComponent from "../../components/TableLimitComponent";
// import CustomerLoyaltyPointCreateComponent from "./CustomerLoyaltyPointCreateComponent";
import appService from "../../../../services/appService";
import SmIconDeleteComponent from "../../components/buttons/SmIconDeleteComponent";
import SmIconSidebarModalEditComponent from "../../components/buttons/SmIconSidebarModalEditComponent";
import statusEnum from "../../../../enums/modules/statusEnum";
import PaginationTextComponent from "../../components/pagination/PaginationTextComponent";
import PaginationBox from "../../components/pagination/PaginationBox";
import PaginationSMBox from "../../components/pagination/PaginationSMBox";
import ENV from '../../../../config/env';

export default {
  name: "CustomerLoyaltyPointList",
  components: {
    LoadingComponent,
    // CustomerLoyaltyPointCreateComponent,
    TableLimitComponent,
    SmIconDeleteComponent,
    SmIconSidebarModalEditComponent,
    PaginationSMBox,
    PaginationBox,
    PaginationTextComponent
  },
  props: ["props"],
  data() {
    return {
      loading: {
        isActive: false,
      },
      loyaltyPoint: {
        form: {
          order_id: null,
          order_subtotal: "",
          percentage: "",
          points: "",
          status: 1,
          earned_at: "",
          expired_at: "",
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
  },
  computed: {
    loyaltyPoints: function () {
      return this.$store.getters["customerLoyaltyPoint/lists"];
    },
    pagination: function () {
      return this.$store.getters["customerLoyaltyPoint/pagination"];
    },
    paginationPage: function () {
      return this.$store.getters["customerLoyaltyPoint/page"];
    },
  },
  methods: {
    statusClass: function (status) {
      return appService.statusClass(status);
    },
    list: function (page = 1) {
      this.loading.isActive = true;
      this.loyaltyPoint.search.page = page;
      this.$store
        .dispatch("customerLoyaltyPoint/lists", {
          id: this.props,
          search: this.loyaltyPoint.search,
        })
        .then((res) => {
          this.loading.isActive = false;
        })
        .catch((err) => {
          this.loading.isActive = false;
        });
    },
    edit: function (loyaltyPoint) {
      appService.modalShow();
      this.loading.isActive = true;
      this.$store
        .dispatch("customerLoyaltyPoint/edit", loyaltyPoint.id)
        .then((res) => {
          this.loading.isActive = false;
          this.loyaltyPoint.form = {
            order_id: loyaltyPoint.order_id,
            order_subtotal: loyaltyPoint.order_subtotal,
            percentage: loyaltyPoint.percentage,
            points: loyaltyPoint.points,
            status: loyaltyPoint.status,
            earned_at: loyaltyPoint.earned_at,
            expired_at: loyaltyPoint.expired_at,
          };
        })
        .catch((err) => {
          alertService.error(err.response.data.message);
        });
    },
    destroy: function (loyaltyPointId) {
      appService
        .destroyConfirmation()
        .then((res) => {
          try {
            this.loading.isActive = true;
            this.$store
              .dispatch("customerLoyaltyPoint/destroy", {
                id: this.props,
                loyaltyPointId: loyaltyPointId,
                search: this.loyaltyPoint.search,
              })
              .then((res) => {
                this.loading.isActive = false;
                alertService.successFlip(null, this.$t("label.loyalty_point"));
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