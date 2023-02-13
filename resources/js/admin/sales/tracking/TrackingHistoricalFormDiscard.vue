<template>
  <v-form ref="form" v-model="valid" lazy-validation>
    <v-row>
      <v-col cols="12">
        <v-select
          v-model="reason"
          :items="options.reason_discart"
          label="Motivo de Perdida"
          placeholder="Seleccioanr Motivo"
          hide-details
          outlined
          dense
        ></v-select>
      </v-col>
      <v-col cols="12">
        <v-textarea
          v-model="form.message"
          label="comentario"
          placeholder="Escribir detalle del seguimiento"
          persistent-placeholder
          outlined
        ></v-textarea>
      </v-col>
    </v-row>
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
  data() {
    return {
      valid: true,
      dialogCalendar: false,
      form: {
        price: this.tracking.price || 0,
        currency: this.tracking.currency || null,
        assertiveness: this.tracking.assertiveness || 0,
        date_next_tracking: null,
        date_won_sale: null,
        invoice: null,
        date_delivery: null,
        type_contacted: "Llamada",
        estatus: "finalizado",
        message: null,
      },
      reason: "",
      options: {
        reason_discart: [
          "Por Precio",
          "Se fue a la Competencia",
          "No Interesado",
          "Sin Respuesta",
          "Descistio la Compra",
          "Falta de documentacion",
          "Rechazo de Credito",
          "Falta de Fondos",
          "Seguimiento Duplicado",
          "Sin STOCK",
          "Otro Motivo",
        ],
      },
    };
  },
  methods: {
    async save() {
      const _this = this;
      if (!_this.$refs.form.validate()) return;
      let payload = {
        ..._this.form,
        message: `${_this.reason}: ${_this.form.message}`,
        last_assertiveness: _this.form.assertiveness,
        last_price: _this.form.price,
        last_currency: _this.form.currency,
        date_lost_sale: new Date().toISOString().substr(0, 10),
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
  },
};
</script>
<style></style>
