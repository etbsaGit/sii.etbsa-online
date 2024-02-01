<template>
  <v-container fluid>
    <v-card>
      <v-toolbar flat>
        <v-toolbar-title>
          <v-icon>mdi-crosshairs</v-icon> Registrar Nuevo GPS
        </v-toolbar-title>
        <v-spacer> </v-spacer>
      </v-toolbar>
      <v-divider class="mb-3"></v-divider>
      <v-card-text>
        <v-form ref="form" v-model="valid" lazy-validation>
          <gps-form :form.sync="form" :options="options"></gps-form>
        </v-form>
      </v-card-text>
      <v-card-actions>
        <v-btn
          :disabled="!valid"
          color="success"
          class="mr-4"
          @click="save"
          block
        >
          Guardar
          <v-icon right>mdi-check</v-icon>
        </v-btn>
      </v-card-actions>
    </v-card>
  </v-container>
</template>

<script>
import GpsForm from "../forms/GpsForm.vue";
export default {
  components: { GpsForm },

  data() {
    return {
      valid: true,
      form: {
        clave: null,
        name: null,
        gps_group_id: null,
        gps_chip_id: null,
        installation_date: null,
        renew_date: null,
        description: null,
        payment_type: "CONTADO",
        currency: "MXN",
        exchange_rate: 1.0,
        invoice: null,
        invoice_date: null,
      },
      options: {
        groups: [],
        chips: [],
        types: [],
      },
    };
  },
  mounted() {
    this.$store.commit("setBreadcrumbs", [
      { label: "GPS", to: { name: "gps.list" } },
      { label: "Crear", to: { name: "" } },
    ]);
    this.loadResources();
  },
  methods: {
    save() {
      if (!this.$refs.form.validate()) return;
      const self = this;

      axios
        .post("/admin/gps", self.form)
        .then(function (response) {
          self.$store.commit("showSnackbar", {
            message: response.data.message,
            color: "success",
            duration: 3000,
          });

          self.$eventBus.$emit("GPS_REFRESH");
        })
        .catch(function (error) {
          if (error.response) {
            self.$store.commit("showSnackbar", {
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
    loadResources() {
      axios.get("/admin/gps/create").then((response) => {
        let Options = response.data.data.resources;
        this.options.groups = Options.groups_gps;
        this.options.chips = Options.chips_gps;
        this.options.types = Options.types;
      });
    },
  },
};
</script>
