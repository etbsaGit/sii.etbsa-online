<template>
  <v-form ref="form" v-model="valid" lazy-validation>
    <v-row>
      <v-col cols="12" md="6">
        <v-select
          v-model="form.type_contacted"
          :items="options.type_contacted"
          label="Tipo de Seguimiento:"
          :rules="[(v) => !!v || 'Requerido']"
          prepend-icon="mdi-chat-alert-outline"
          outlined
          filled
          dense
        >
        </v-select>
      </v-col>
      <v-col cols="12" md="6">
        <v-dialog
          ref="dialog"
          v-model="dialogCalendar"
          :return-value.sync="form.date_next_tracking"
          persistent
          transition="scale-transition"
          width="324px"
        >
          <template v-slot:activator="{ on, attrs }">
            <v-text-field
              v-model="form.date_next_tracking"
              label="Fecha Proximo Seguimiento"
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
          <v-date-picker
            v-model="form.date_next_tracking"
            scrollable
            :min="new Date().toISOString().substr(0, 10)"
          >
            <v-spacer></v-spacer>
            <v-btn text color="primary" @click="dialogCalendar = false">
              Cancel
            </v-btn>
            <v-btn
              text
              color="primary"
              @click="$refs.dialog.save(form.date_next_tracking)"
            >
              OK
            </v-btn>
          </v-date-picker>
        </v-dialog>
      </v-col>
      <v-col cols="12">
        <v-select
          v-model="form.assertiveness"
          :items="options.assertiveness"
          label="Ultima Certeza:"
          :rules="[(v) => !!v || 'Requerido']"
          prepend-icon="mdi-circle-slice-2"
          outlined
          filled
          dense
        >
          <template v-slot:item="data">
            <v-list-item-content>
              <v-list-item-title v-text="data.item.text"></v-list-item-title>
            </v-list-item-content>
            <v-list-item-action>
              <v-chip small dark :color="data.item.color">
                {{ data.item.value | percent }}
              </v-chip>
            </v-list-item-action>
          </template>
        </v-select>
      </v-col>
      <v-col cols="12" md="6">
        <v-text-field
          v-model="form.price"
          label="Ultimo precio a tratar"
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
          :items="['MXN', 'USD']"
          label="Tipo de Moneda"
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
        assertiveness: this.tracking.assertiveness || 0,
        date_next_tracking: null,
        type_contacted: "Llamada",
        estatus: "activo",
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
        currency_id: _this.form.currency == "MXN" ? 1 : 2,
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
