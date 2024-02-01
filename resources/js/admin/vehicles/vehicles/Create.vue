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
        actual_mileage: null,
        agency_id: null,
        brand: null,
        fuel: "MAGNA",
        fue_id: null,
        fuel_odometer: null,
        last_mileage: null,
        matricula: null,
        max_lts_fuel: null,
        mileage_last_service: null,
        mileage_range_service: null,
        model: null,
        serie: null,
        ticket_card: null,
        user_id: null,
        year: null,
        bidon_fuel: false,
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
