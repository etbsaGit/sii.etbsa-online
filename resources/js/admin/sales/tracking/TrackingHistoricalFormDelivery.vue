<template>
  <v-form ref="form" v-model="valid" lazy-validation>
    <v-row>
      <v-col cols="12">
        <v-dialog
          ref="dialog"
          v-model="dialogCalendar"
          :return-value.sync="form.date_invoice"
          persistent
          transition="scale-transition"
          width="324px"
        >
          <template v-slot:activator="{ on, attrs }">
            <v-text-field
              v-model="form.date_delivery"
              label="Fecha de Entrega"
              prepend-icon="mdi-calendar"
              :rules="[(v) => !!v || 'Requerido']"
              v-bind="attrs"
              v-on="on"
              readonly
              outlined
              filled
              dense
            ></v-text-field>
          </template>
          <v-date-picker v-model="form.date_delivery" scrollable>
            <v-spacer></v-spacer>
            <v-btn text color="primary" @click="dialogCalendar = false">
              Cancel
            </v-btn>
            <v-btn
              text
              color="primary"
              @click="$refs.dialog.save(form.date_delivery)"
            >
              OK
            </v-btn>
          </v-date-picker>
        </v-dialog>
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
        date_delivery: null,
        type_contacted: "Llamada",
        estatus: "formalizado",
        message: null,
      },
    };
  },
  methods: {
    async save() {
      const _this = this;
      if (!_this.$refs.form.validate()) return;
      let payload = {
        ..._this.form,
        last_assertiveness: 1,
        last_price: _this.form.price,
        last_currency: _this.form.currency,
      };
      await axios
        .post(`/admin/tracking/${_this.trackingId}/historical`, payload)
        .then((response) => {
          _this.$refs.form.resetValidation();
          _this.$eventBus.$emit("MESSAGE_ADDED");
          _this.$eventBus.$emit("REFRESH_TRACKING");
          _this.$store.commit("showSnackbar", {
            message: response.data.message,
            color: "success",
            duration: 3000,
          });
          _this.$eventBus.$emit("CLOSE_TRACKING_DIALOG");
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
