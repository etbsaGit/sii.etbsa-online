<template>
  <v-card flat>
    <v-card-text>
      <tracking-form ref="form" :form.sync="form"></tracking-form>
    </v-card-text>
    <v-card-actions>
      <v-btn :disabled="!valid" color="primary" @click="save" block dark>
        Crear Seguimiento
      </v-btn>
    </v-card-actions>
  </v-card>
</template>

<script>
import TrackingForm from "./TrackingForm.vue";
export default {
  components: { TrackingForm },
  props: {
    propProspectId: {
      type: [String, Number],
      required: false,
    },
  },
  data() {
    return {
      valid: true,
      form: {
        prospect_id: null,
        title: null,
        reference: null,
        price: 0,
        currency: 1,
        currency_id: 1,
        exchange_value: 1,
        agency_id: null,
        department_id: null,
        seller_id: null,
        attended_by: null,
        assertiveness: 0.01,
        tracking_condition: "por_definir",
        date_next_tracking: null,
        first_contact: "Online",
        description_topic: null,
        withQuote: false,
        subtotal: 0,
        discount: 0,
        tax: 0,
        total: 0,
        currency: null,
        products: [],
      },
    };
  },
  mounted() {
    if (this.propProspectId) {
      this.form.prospect_id = this.propProspectId;
    }
  },
  methods: {
    async save() {
      if (!this.$refs.form.$refs.form.validate()) return;
      if (this.form.withQuote && this.form.products.length == 0)
        return this.$store.commit("showSnackbar", {
          message: "Aun no Agrega una Partida",
          color: "warning",
          duration: 3000,
        });
      const _this = this;
      _this.isLoading = true;
      // console.log("Currency", _this.form.currency.name);
      let payload = {
        ..._this.form,
        price: _this.form.withQuote ? _this.form.total : _this.form.price,
        // currency: _this.form.currency.name,
        payment_condition: _this.form.tracking_condition,
      };

      await axios
        .post("/admin/tracking", payload)
        .then(function (response) {
          _this.$store.commit("showSnackbar", {
            message: response.data.message,
            color: "success",
            duration: 3000,
          });
          // reset
          if (!!response.data.data.quotation) {
            let idQuote = response.data.data.quotation.id;
            window.open(
              `/admin/quote/${idQuote}/print`,
              "_blank",
              "noreferrer"
            );
          }
          _this.isLoading = false;
          // _this.$emit("success");
          _this.$eventBus.$emit("CLOSE_TRACKING_DIALOG");
          // self.$router.push({ name: "tracking.list" });
        })
        .catch(function (error) {
          _this.isLoading = false;
          _this.$store.commit("hideLoader");

          if (error.response) {
            _this.$store.commit("showSnackbar", {
              message: error.response.data.message,
              color: "error",
              duration: 3000,
            });
          } else if (error.request) {
            console.log(error.request);
          } else {
            console.log("Error", error.message);
          }
        });
    },
  },
};
</script>
