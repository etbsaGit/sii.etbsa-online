<template>
  <v-card>
    <v-card-title>
      <v-icon class="mr-2">mdi-toolbox-outline</v-icon> Solicitar Servicio a
      Vehiculo
      <v-spacer></v-spacer>
    </v-card-title>
    <v-divider class="mb-2"></v-divider>
    <v-card-text>
      <services-form
        ref="ServicesForm"
        v-model="valid"
        :form.sync="form"
        :propVehicleId="propVehicleId"
      ></services-form>
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
import ServicesForm from "./ServicesForm.vue";
export default {
  components: { ServicesForm },
  props: {
    propVehicleId: {
      type: [String, Number],
      require: false,
      default: null,
    },
  },
  data() {
    return {
      valid: true,
      isLoading: false,
      form: {
        agency_id: null,
        cost: null,
        department_id: null,
        mileage: null,
        reason: "",
        reason_description: "",
        vehicle_id: null,
      },
    };
  },
  mounted() {
    this.$store.commit("setBreadcrumbs", [
      { label: "Flotilla", to: { name: "vehicle.list" } },
      { label: "Servicios", to: { name: "vehicle.sevices.list" } },
      { label: "Solicitud" },
    ]);
  },
  methods: {
    async save() {
      if (!this.$refs.ServicesForm.$refs.form.validate()) return;
      const _this = this;
      let payload = {
        ..._this.form,
        mileage: _this.form.reason == "Servicio" ? _this.form.mileage : null,
      };
      await axios
        .post("/admin/vehicle-services", payload)
        .then(function (response) {
          _this.$store.commit("showSnackbar", {
            message: response.data.message,
            color: "success",
            duration: 3000,
          });
          _this.$eventBus.$emit("VEHICLE_REFRESH");
          _this.$router.push({ name: "vehicle.service.list" });
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
