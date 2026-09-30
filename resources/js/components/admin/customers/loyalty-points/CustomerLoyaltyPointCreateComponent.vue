<template>
  <LoadingComponent :props="loading" />
  <SmModalCreateComponent v-on:click="openModal" :props="addButton" />

  <div id="modal" class="modal">
    <div class="modal-dialog">
      <div class="modal-header">
        <h3 class="modal-title">{{ $t("label.loyalty_point") }}</h3>
        <button class="modal-close fa-solid fa-xmark text-xl text-slate-400 hover:text-red-500" @click="reset"></button>
      </div>
      <div class="modal-body">
        <form @submit.prevent="save">
          <div class="form-row">
            <div class="form-col-12 sm:form-col-6">
              <label for="order_subtotal" class="db-field-title required">
                {{ $t("label.order_subtotal") }}
              </label>
              <input v-model="props.form.order_subtotal" v-bind:class="errors.order_subtotal ? 'invalid' : ''" type="number" step="0.01" id="order_subtotal" class="db-field-control" />
              <small class="db-field-alert" v-if="errors.order_subtotal">{{ errors.order_subtotal[0] }}</small>
            </div>

            <div class="form-col-12 sm:form-col-6">
              <label for="percentage" class="db-field-title required">
                {{ $t("label.percentage") }}
              </label>
              <input v-model="props.form.percentage" v-bind:class="errors.percentage ? 'invalid' : ''" type="number" step="0.01" min="0" max="100" id="percentage" class="db-field-control" />
              <small class="db-field-alert" v-if="errors.percentage">{{ errors.percentage[0] }}</small>
            </div>

            <div class="form-col-12 sm:form-col-6">
              <label for="points" class="db-field-title required">
                {{ $t("label.points") }}
              </label>
              <input v-model="props.form.points" v-bind:class="errors.points ? 'invalid' : ''" type="number" min="0" id="points" class="db-field-control" />
              <small class="db-field-alert" v-if="errors.points">{{ errors.points[0] }}</small>
            </div>

            <div class="form-col-12 sm:form-col-6">
              <label for="status" class="db-field-title required">
                {{ $t("label.status") }}
              </label>
              <select v-model="props.form.status" v-bind:class="errors.status ? 'invalid' : ''" id="status" class="db-field-control">
                <option value="1">{{ $t("label.active") }}</option>
                <option value="0">{{ $t("label.inactive") }}</option>
              </select>
              <small class="db-field-alert" v-if="errors.status">{{ errors.status[0] }}</small>
            </div>

            <div class="form-col-12 sm:form-col-6">
              <label for="earned_at" class="db-field-title">
                {{ $t("label.earned_at") }}
              </label>
              <input v-model="props.form.earned_at" v-bind:class="errors.earned_at ? 'invalid' : ''" type="datetime-local" id="earned_at" class="db-field-control" />
              <small class="db-field-alert" v-if="errors.earned_at">{{ errors.earned_at[0] }}</small>
            </div>

            <div class="form-col-12 sm:form-col-6">
              <label for="expired_at" class="db-field-title">
                {{ $t("label.expired_at") }}
              </label>
              <input v-model="props.form.expired_at" v-bind:class="errors.expired_at ? 'invalid' : ''" type="datetime-local" id="expired_at" class="db-field-control" />
              <small class="db-field-alert" v-if="errors.expired_at">{{ errors.expired_at[0] }}</small>
            </div>

            <div class="form-col-12">
              <div class="modal-btns">
                <button type="button" class="modal-btn-outline modal-close" @click="reset">
                  <i class="lab lab-close"></i>
                  <span>{{ $t("button.close") }}</span>
                </button>

                <button type="submit" class="db-btn py-2 text-white bg-primary">
                  <i class="lab lab-save"></i>
                  <span>{{ $t("button.save") }}</span>
                </button>
              </div>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
import SmModalCreateComponent from "../../components/buttons/SmModalCreateComponent";
import LoadingComponent from "../../components/LoadingComponent";
import appService from "../../../../services/appService";
import alertService from "../../../../services/alertService";

export default {
  name: "CustomerLoyaltyPointCreateComponent",
  components: { SmModalCreateComponent, LoadingComponent },
  props: {
    props: Object,
  },
  data() {
    return {
      loading: {
        isActive: false,
      },
      errors: {},
    };
  },
  computed: {
    addButton: function () {
      return { title: this.$t('button.add_loyalty_point') };
    }
  },
  methods: {
    openModal: function () {
      appService.modalShow();
    },
    reset: function () {
      appService.modalHide();
      this.$store.dispatch("customerLoyaltyPoint/reset").then().catch();
      this.errors = {};
      this.$props.props.form = {
        order_id: null,
        order_subtotal: "",
        percentage: "",
        points: "",
        status: 1,
        earned_at: "",
        expired_at: "",
      };
    },
    save: function () {
      try {
        const tempId = this.$store.getters["customerLoyaltyPoint/temp"].temp_id;
        this.loading.isActive = true;
        this.$store
          .dispatch("customerLoyaltyPoint/save", {
            search: this.props.search,
            form: this.props.form,
            id: this.$route.params.id,
          })
          .then((res) => {
            appService.modalHide();
            this.loading.isActive = false;
            alertService.successFlip(tempId === null ? 0 : 1, this.$t("label.loyalty_point"));
            this.props.form = {
              order_id: null,
              order_subtotal: "",
              percentage: "",
              points: "",
              status: 1,
              earned_at: "",
              expired_at: "",
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
  },
};
</script>