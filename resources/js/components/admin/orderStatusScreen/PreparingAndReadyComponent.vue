<template>
  <LoadingContentComponent :props="loading" />
  <div class="col-span-1 customer-screen db-card rounded-[10px] h-screen md:h-[calc(100vh-117px)] overflow-hidden">
    <h3 class="text-lg font-semibold text-white p-3 pb-2 bg-primary mb-2 rounded-t-[10px] text-center">{{
      $t("label.preparing") }}
    </h3>
    <div class="content-wrapper p-3 overflow-auto thin-scrolling h-full">
      <div v-if="preparingItems.length > 0" class="space-y-4">
        <div
          v-for="preparingItem in preparingItems"
          :key="preparingItem.id"
          class="bg-gradient-to-r from-orange-50 to-orange-100 border-l-4 border-orange-500 rounded-lg p-6 shadow-md hover:shadow-lg transition-shadow">
          <div class="flex items-center justify-between">
        <div>
          <p class="text-sm font-medium text-gray-600 mb-1">Order Serial No</p>
          <p class="text-xl font-bold text-orange-600">{{ preparingItem.order_serial_no }}</p>
        </div>
        <div class="text-right">
          <span class="inline-block bg-orange-500 text-white px-4 py-2 rounded-full text-sm font-semibold">
            {{ $t("label.preparing") }}
          </span>
        </div>
          </div>
        </div>
      </div>
      <div v-else class="flex items-center justify-center h-40">
        <p class="text-gray-400 text-lg">{{ $t("label.noOrders") }}</p>
      </div>
    </div>
  </div>
  <div class="col-span-1 customer-screen db-card rounded-[10px] h-screen md:h-[calc(100vh-117px)] overflow-hidden">
    <h3 class="text-lg font-semibold text-white p-3 pb-2 bg-[#1AB759] mb-2 rounded-t-[10px] text-center">{{
      $t("label.ready") }}</h3>
    <div class="content-wrapper p-3 overflow-auto thin-scrolling h-full">
      <div v-if="preparedItems.length > 0" class="space-y-4">
        <div
          v-for="preparedItem in preparedItems"
          :key="preparedItem.id"
          class="bg-gradient-to-r from-green-50 to-green-100 border-l-4 border-green-500 rounded-lg p-6 shadow-md hover:shadow-lg transition-shadow">
          <div class="flex items-center justify-between">
        <div>
          <p class="text-sm font-medium text-gray-600 mb-1">Order Serial No</p>
          <p class="text-xl font-bold text-green-600">{{ preparedItem.order_serial_no }}</p>
        </div>
        <div class="text-right">
          <span class="inline-block bg-green-500 text-white px-4 py-2 rounded-full text-sm font-semibold">
            {{ $t("label.ready") }}
          </span>
        </div>
          </div>
        </div>
      </div>
      <div v-else class="flex items-center justify-center h-40">
        <p class="text-gray-400 text-lg">{{ $t("label.noOrders") }}</p>
      </div>
    </div>
  </div>
</template>
<script>
import LoadingContentComponent from "../components/LoadingContentComponent";
import orderStatusEnum from "../../../enums/modules/orderStatusEnum";


export default {
  name: "PreparingAndReadyComponent",
  components: {
    LoadingContentComponent,
  },
  data() {
    return {
      loading: {
        isActive: false,
      },
      preparedItems: [],
      preparingItems: [],
      enums: {
        orderStatusEnum: orderStatusEnum,
      },
      autoRefreshInterval: null,
    };
  },
  computed: {
    orders: function () {
      return this.$store.getters["orderStatusScreenOrder/lists"];
    },
    items: function () {
      return this.$store.getters["orderStatusScreenOrder/mostPopularItems"];
    },
  },
  mounted() {
    this.list();
    this.startAutoRefresh();
  },
  methods: {
    startAutoRefresh() {
      if (this.$route.path.includes('order-status-screen')) {
        this.autoRefreshInterval = setInterval(() => {
          this.list();
        }, 30000);
      }
    },
    stopAutoRefresh() {
      if (this.autoRefreshInterval) {
        clearInterval(this.autoRefreshInterval);
        this.autoRefreshInterval = null;
      }
    },
    list: function () {
      this.loading.isActive = true;
      this.$store
        .dispatch("orderStatusScreenOrder/lists")
        .then((res) => {
          this.preparingItems = res.data.data.filter(
            (item) => item.status === orderStatusEnum.PREPARING
          );
          this.preparedItems = res.data.data.filter(
            (item) => item.status === orderStatusEnum.PREPARED
          );

          this.loading.isActive = false;
        })
        .catch((err) => {
          this.loading.isActive = false;
        });
    },
  },
  beforeUnmount() {
    this.stopAutoRefresh();

  },
};
</script>