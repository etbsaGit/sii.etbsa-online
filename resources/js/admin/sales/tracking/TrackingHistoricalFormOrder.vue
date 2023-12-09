<template>
  <v-form ref="form" v-model="valid" lazy-validation>
    <v-row>
      <v-col cols="12" md="6">
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
              v-model="form.date_invoice"
              label="Fecha Factura"
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
          <v-date-picker v-model="form.date_invoice" scrollable>
            <v-spacer></v-spacer>
            <v-btn text color="primary" @click="dialogCalendar = false">
              Cancel
            </v-btn>
            <v-btn
              text
              color="primary"
              @click="$refs.dialog.save(form.date_invoice)"
            >
              OK
            </v-btn>
          </v-date-picker>
        </v-dialog>
      </v-col>
      <v-col cols="12" md="6">
        <v-text-field
          v-model="form.invoice"
          label="Folio Factura"
          :rules="[(v) => !!v || 'Requerido']"
          prepend-icon="mdi-file"
          outlined
          filled
          dense
        >
        </v-text-field>
      </v-col>
      <v-col cols="12" md="6">
        <v-text-field
          v-model="form.price"
          label="Importe Factura"
          :rules="[(v) => !!v || 'Requerido']"
          prepend-icon="mdi-cash-usd-outline"
          type="number"
          outlined
          filled
          dense
        >
        </v-text-field>
      </v-col>
      <v-col cols="12" md="6">
        <v-select
          v-model="form.currency"
          :items="[
            { id: 1, name: 'MXN' },
            { id: 2, name: 'USD' },
          ]"
          label="Tipo de Moneda"
          item-value="id"
          item-text="name"
          :rules="[(v) => !!v || 'Requerido']"
          prepend-icon="mdi-cash-usd-outline"
          outlined
          filled
          dense
        >
        </v-select>
      </v-col>
      <v-col cols="12">
        <v-textarea
          v-model="form.message"
          label="Comentario"
          placeholder="Describir el Cierre de Venta"
          :rules="[(v) => !!v || 'Es Requerido']"
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
import Assertiveness from "@admin/sales/tracking/resources/assertiveness.json";
export default {
  name: "TrackingHistoricalFormAdd",
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
        assertiveness: 0.9,
        type_contacted: "Llamada",
        estatus: "formalizado",
        invoice: "",
        date_next_tracking: null,
        date_invoice: null,
        date_lost_sale: null,
        date_won_sale: null,
        message: null,
      },
      options: {
        type_contacted: ["Llamada", "Visita Campo", "En Agencia"],
        condition: ["Por definir", "Contado", "Financiamiento", "Renta"],
        assertiveness: Assertiveness,
      },
    };
  },
  methods: {
    async save() {
      const _this = this;
      if (!_this.$refs.form.validate()) return;
      let payload = {
        ..._this.form,
        last_assertiveness: _this.form.assertiveness,
        last_price: _this.form.price,
        last_currency: _this.form.currency,
        date_won_sale: new Date().toISOString().substr(0, 10),
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
