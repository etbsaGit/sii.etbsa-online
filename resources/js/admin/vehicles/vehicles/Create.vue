<template>
  <v-card>
    <v-card-title>
      <v-icon class="mr-2">mdi-car</v-icon> Registrar Vehiculo a la Flotilla
      <v-spacer></v-spacer>
    </v-card-title>
    <v-divider class="mb-2"></v-divider>
    <v-card-text>
      <vehicle-form
        ref="VehicleForm"
        v-model="valid"
        :form.sync="form"
      ></vehicle-form>
    </v-card-text>
    <v-card-actions>
      <v-btn
        @click="save()"
        :loading="isLoading"
        :disabled="!valid || isLoading"
        color="primary"
        block
        dark
      >
        Guardar
      </v-btn>
    </v-card-actions>
  </v-card>
</template>

<script>
import VehicleForm from "./VehicleForm.vue";
export default {
  components: { VehicleForm },
  data() {
    return {
      valid: true,
      isLoading: false,
      form: {
        matricula: "",
        modelo: "",
        marca: "",
        serie: "",
        ticket_card: "",
        tipo_combustible: "",
        ultimo_kilometraje: 0,
        ultimo_kilometraje_servicio: 0,
        rango_kilometros_servicio: 0,
        capacidad_tanque: 0,
        sucursal: null,
        responsable: null,
      },
    };
  },
  mounted() {
    this.$store.commit("setBreadcrumbs", [
      { label: "Flotilla", to: { name: "vehicle.list" } },
      { label: "Registar Vehiculo", name: "" },
    ]);
  },
  methods: {
    async save() {
      if (!this.$refs.VehicleForm.$refs.form.validate()) return;
      const _this = this;

      await axios
        .post("/admin/vehicles", _this.form)
        .then(function (response) {
          _this.$store.commit("showSnackbar", {
            message: response.data.message,
            color: "success",
            duration: 3000,
          });
          _this.$eventBus.$emit("VEHICLE_REFRESH");
          _this.$router.push({ name: "vehicle.list" });
        })
        .catch(function (error) {
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
