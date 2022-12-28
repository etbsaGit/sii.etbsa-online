<template>
  <v-form ref="form" v-model="valid" lazy-validation>
    <v-row class="overline">
      <v-col cols="12">
        <v-autocomplete
          v-model="form.customer_id"
          :items="options.customers"
          item-text="full_name"
          item-value="id"
          label="Cliente"
          placeholder="Seleccionar el Cliente a quien correponde el Pedido"
          persistent-placeholder
          hint="buscar por rfc,nombre,telefono,email"
          persistent-hint
          outlined
          filled
          dense
        ></v-autocomplete>
      </v-col>
      <v-col cols="12">
        <v-select
          v-model="form.payment_condition"
          label="Tramite"
          :items="[
            'Contado',
            'Credito ETBSA',
            'Credito FINETBSA',
            'Credito JDF',
          ]"
          placeholder="Seleccionar el Tipo de Tramite"
          persistent-placeholder
          hint="es requerido"
          outlined
          filled
          dense
        ></v-select>
      </v-col>
      <v-col cols="12" md="6">
        <v-text-field
          v-model="form.price"
          label="Precio tratado"
          outlined
          dense
        ></v-text-field>
      </v-col>
      <v-col cols="12" md="6">
        <v-select
          v-model="form.currency_id"
          :items="[
            { text: 'MXN', value: 1 },
            { text: 'USD', value: 2 },
          ]"
          label="Moneda"
          outlined
          dense
        ></v-select>
      </v-col>
      <v-col cols="12">
        <v-textarea
          v-model="form.message"
          label="Comentario del Seguimiento"
          placeholder="Escribir el detalle del seguimiento"
          persistent-placeholder
          outlined
        ></v-textarea>
      </v-col>
    </v-row>
    <small>
      *El seguimiento pasara a Formalizado, y se creara un pedido al cliente
      correspondiente al cual se le Facturara
    </small>
    <v-card-actions class="py-0">
      <v-btn dark text block color="primary" @click="save">Guardar</v-btn>
    </v-card-actions>
  </v-form>
</template>
<script>
// import Assertiveness from "@admin/sales/tracking/resources/assertiveness.json";
export default {
  name: "TrackingHistoricalFormDiscard",
  props: {
    trackingId: {
      type: Number | String,
      require: true,
    },
    tracking: {
      type: Object,
      require: false,
    },
  },
  mounted() {
    this.getOptionSaleOrder();
  },
  data() {
    return {
      valid: true,
      dialogCalendar: false,
      form: {
        price: this.tracking.price || 0,
        currency: this.tracking.currency || null,
        assertiveness: this.tracking.assertiveness || 0,
        date_next_tracking: null,
        type_contacted: "Llamada",
        estatus: "finalizado",
        message: null,
      },
      options: {
        customers: [],
      },
    };
  },
  methods: {
    async save() {
      const _this = this;
      if (!this.$refs.form.validate()) return;
      let payload = {
        ..._this.form,
        last_assertiveness: _this.form.assertiveness,
        last_price: _this.form.price,
        last_currency: _this.form.currency,
      };
      await axios
        .post(`/admin/tracking/${_this.trackingId}/historical`, payload)
        .then((response) => {
          _this.$refs.form.resetValidation();
          _this.$eventBus.$emit("MESSAGE_ADDED");
          _this.$store.commit("showSnackbar", {
            message: response.data.message,
            color: "success",
            duration: 3000,
          });
        })
        .catch(function (error) {
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
    async getOptionSaleOrder() {
      const _this = this;

      await axios.get("/admin/sale-order/create").then((response) => {
        _this.options.customers = response.data.data.customers;
      });
    },
  },
};
</script>
<style></style>
