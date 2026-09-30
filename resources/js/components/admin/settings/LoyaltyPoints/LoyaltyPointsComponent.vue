<template>
  <LoadingComponent :props="loading" />

  <div id="loyaltyPoints" class="db-card db-tab-div active">
    <div class="db-card-header">
      <h3 class="db-card-title">{{ $t("menu.loyalty_points") }}</h3>
    </div>

    <div class="db-card-body">
      <form @submit.prevent="save" class="form-row">
        <!-- Percentage -->
        <div class="form-col-12 sm:form-col-6">
          <label for="percentage" class="db-field-title required">
            {{ $t("label.percentage") }}
          </label>
          <input
            v-model.number="form.percentage"
            :class="{ invalid: errors.percentage }"
            type="number"
            min="0"
            max="100"
            step="0.01"
            id="percentage"
            class="db-field-control"
            placeholder="e.g. 10.5"
          />
          <small v-if="errors.percentage" class="db-field-alert">
            {{ errors.percentage[0] }}
          </small>
        </div>

        <!-- Minimum Order Price -->
        <div class="form-col-12 sm:form-col-6">
          <label for="min_order_price" class="db-field-title required">
            {{ $t("label.min_order_price") }}
          </label>
          <input
            v-model.number="form.min_order_price"
            :class="{ invalid: errors.min_order_price }"
            type="number"
            min="0"
            step="0.01"
            id="min_order_price"
            class="db-field-control"
            placeholder="e.g. 50.00"
          />
          <small v-if="errors.min_order_price" class="db-field-alert">
            {{ errors.min_order_price[0] }}
          </small>
        </div>

        <!-- Status -->
        <div class="form-col-12 sm:form-col-6">
          <label class="db-field-title required">{{ $t("label.status") }}</label>
          <div class="db-field-radio-group">
            <div class="db-field-radio">
              <div class="custom-radio">
                <input
                  v-model="form.status"
                  :value="enums.statusEnum.ACTIVE"
                  type="radio"
                  id="active"
                  class="custom-radio-field"
                />
                <span class="custom-radio-span"></span>
              </div>
              <label for="active" class="db-field-label">{{ $t("label.active") }}</label>
            </div>

            <div class="db-field-radio">
              <div class="custom-radio">
                <input
                  v-model="form.status"
                  :value="enums.statusEnum.INACTIVE"
                  type="radio"
                  id="inactive"
                  class="custom-radio-field"
                />
                <span class="custom-radio-span"></span>
              </div>
              <label for="inactive" class="db-field-label">{{ $t("label.inactive") }}</label>
            </div>
          </div>
        </div>

        <!-- Submit -->
        <div class="form-col-12">
          <button type="submit" class="db-btn text-white bg-primary">
            <i class="lab lab-save"></i>
            <span>{{ $t("button.save") }}</span>
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script>
import LoadingComponent from "../../components/LoadingComponent";
import alertService from "../../../../services/alertService";
import statusEnum from "../../../../enums/modules/statusEnum";

export default {
  name: "LoyaltyPointsComponent",

  components: {
    LoadingComponent,
  },

  data() {
    return {
      loading: {
        isActive: false,
      },

      enums: {
        statusEnum,
      },

      form: {
        percentage: 0,
        min_order_price: 0,
        status: statusEnum.ACTIVE,
      },

      errors: {},
    };
  },

  mounted() {
    this.loadInfo();
  },

  methods: {
    async loadInfo() {
      this.loading.isActive = true;
      this.errors = {};

      try {
        // Pass empty object to prevent 'undefined' error in store
        const res = await this.$store.dispatch("loyaltyPoints/lists", {});

        if (res?.data?.data) {
          const data = res.data.data;

          console.log("Loyalty Points Data:", data);

          // Update properties individually → preserves reactivity
          this.form.percentage = Number(data.percentage) || 0;
          this.form.min_order_price = Number(data.min_order_price) || 0;
          this.form.status = Number(data.status) || statusEnum.ACTIVE;
        }
        // If no data → keep defaults (0 + ACTIVE)
      } catch (err) {
        console.error("Failed to load loyalty points:", err);
        alertService.error(err?.response?.data?.message || "Failed to load settings");
      } finally {
        this.loading.isActive = false;
      }
    },

    async save() {
      this.loading.isActive = true;
      this.errors = {};

      try {
        const payload = {
          percentage: Number(this.form.percentage),
          min_order_price: Number(this.form.min_order_price),
          status: Number(this.form.status),
        };

        const res = await this.$store.dispatch("loyaltyPoints/save", { 
          form: payload,
          search: {} // Pass empty search object to prevent undefined error
        });

        alertService.successFlip(
          res.config.method === "put" ? 1 : 0,
          this.$t("menu.loyalty_points")
        );

        // Reload to get updated data
        await this.loadInfo();
      } catch (err) {
        if (err.response?.data?.errors) {
          this.errors = err.response.data.errors;
        } else {
          alertService.error(err?.response?.data?.message || "Failed to save");
          console.error(err);
        }
      } finally {
        this.loading.isActive = false;
      }
    },
  },
};
</script>