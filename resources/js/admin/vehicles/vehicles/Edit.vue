<template>
  <v-card flat>
    <v-card-text class="overline">
      <v-container fluid class="grey lighten-3 elevation-2">
        <v-row dense align="center">
          <v-col cols="12" md="8">
            <v-container fluid>
              <vehicle-form ref="VehicleForm" v-model="valid" :form.sync="form">
              </vehicle-form>
            </v-container>
          </v-col>
          <v-col cols="12" md="4">
            <v-container class="elevation-2 text-center">
              <gauge
                heading="ODOMETRO DE COMBUSTIBLE"
                :value="form.fuel_odometer"
                :min="0"
                :max="100"
                :fontSize="'16px'"
                :padding="false"
                :svgStyle="{ maxWidth: 225, maxHeight: 200 }"
                :activeFill="'#1976D2'"
                :pointerStroke="'#F44336'"
                :pivotStroke="'#E91E63'"
                :pivotFill="'#4CAF50'"
                :maxThresholdFill="'#4CAF50'"
                :minThresholdFill="'#F44336'"
                :maxThreshold="80"
                :minThreshold="25"
                :minLabel="'E'"
                :maxLabel="'F'"
                :unit="'%'"
                :unitOnArc="false"
              />
            </v-container>
          </v-col>
        </v-row>
        <v-card-actions class="py-0">
          <v-btn
            @click="update()"
            :loading="isLoading"
            :disabled="!valid || isLoading"
            color="primary"
            dark
            block
          >
            Editar
          </v-btn>
        </v-card-actions>
      </v-container>
    </v-card-text>
    <v-card-text>
      <v-row dense>
        <v-col lg="6" sm="12" cols="12">
          <app-widget
            icon-title="mdi-gas-station"
            title="Disperciones"
            padding-hide
            dark
          >
            <dispersal-table slot="widget-content" :items="form.dispersals" />
          </app-widget>
        </v-col>
        <v-col lg="6" sm="12" cols="12">
          <app-widget
            icon-title="mdi-tools"
            title="Servicios en Taller"
            padding-hide
          >
            <services-table
              slot="widget-content"
              :items="form.services"
            ></services-table>
          </app-widget>
        </v-col>
      </v-row>
    </v-card-text>
  </v-card>
</template>

<script>
import AppWidget from "@admin/components/AppWidget.vue";
import DispersalTable from "./DispersalTable.vue";
import ServicesTable from "./ServicesTable.vue";
import VehicleForm from "./VehicleForm.vue";
export default {
  components: { VehicleForm, AppWidget, DispersalTable, ServicesTable },
  props: {
    vehicleId: {
      require: true,
      type: [Number, String],
    },
  },
  data() {
    return {
      valid: true,
      isLoading: false,
      form: {
        actual_mileage: null,
        agency_id: null,
        brand: null,
        fuel: null,
        fuel_odometer: 0,
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
      },
      options: { users: [], agencies: [] },
    };
  },
  mounted() {
    const _this = this;
    _this.loadVehicle(() => {});
    _this.$eventBus.$on(["VEHICLE_REFRESH"], () => {
      _this.loadVehicle(() => {});
    });
  },
  computed: {
    minHeight() {
      const height = "100vh";
      return `calc(${height} - ${this.$vuetify.application.bottom}px)`;
    },
  },
  methods: {
    async update() {
      if (!this.$refs.VehicleForm.$refs.form.validate()) return;
      const _this = this;
      await axios
        .put(`/admin/vehicles/${_this.vehicleId}`, _this.form)
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
    async loadVehicle(cb) {
      const _this = this;

      await axios
        .get(`/admin/vehicles/${_this.vehicleId}`)
        .then(function (response) {
          let Vehicle = response.data.data;
          _this.form = { ...Vehicle };
          (cb || Function)();
        });
    },
  },
};
</script>
