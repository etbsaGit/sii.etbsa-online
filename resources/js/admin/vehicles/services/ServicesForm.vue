<template>
  <v-form v-model="valid" ref="form" lazy-validation>
    <v-row dense class="overline">
      <v-col cols="12" md="4">
        <div class="d-flex flex-column">
          <v-autocomplete
            v-model="Vehicle"
            :items="options.vehicles"
            item-text="matricula"
            item-value="id"
            label="Matricula"
            :rules="[(v) => !!v || 'Es requerido']"
            placeholder="Seleccione una Matricula"
            :hint="`${vehicle.last_mileage || 0} Kms ultimo Kilometraje`"
            persistent-hint
            return-object
            outlined
            clearable
            dense
          ></v-autocomplete>
          <div class="overline pb-2">Con Cargo a:</div>
          <v-autocomplete
            v-model="form.agency_id"
            :items="options.agencies"
            item-text="title"
            item-value="id"
            label="Sucursal"
            :rules="[(v) => !!v || 'Es requerido']"
            outlined
            clearable
            dense
          ></v-autocomplete>
          <v-autocomplete
            v-model="form.department_id"
            :items="options.departments"
            item-text="title"
            item-value="id"
            label="Departamento"
            :rules="[(v) => !!v || 'Es requerido']"
            outlined
            clearable
            dense
          ></v-autocomplete>
        </div>
      </v-col>
      <v-col cols="12" md="4">
        <div class="d-flex flex-column align-content-space-around">
          <v-select
            v-model="form.reason"
            :items="['Servicio', 'Reparacion', 'Refaccion', 'Otro']"
            label="Motivo de Servicio"
            :rules="[(v) => !!v || 'Es requerido']"
            outlined
            clearable
            dense
          ></v-select>

          <v-textarea
            v-model="form.reason_description"
            label="Descripcion Motivo"
            placeholder="Describa el motivo del Servicio"
            :rules="[(v) => !!v || 'Es requerido']"
            persistent-placeholder
            outlined
          ></v-textarea>
        </div>
      </v-col>
      <v-col cols="12" md="4">
        <div v-if="form.reason == 'Servicio'">
          <v-text-field
            v-model="form.mileage"
            label="Kilometraje Actual"
            :rules="RulesMileage"
            :hint="`Debe ser mayor a ${vehicle.last_mileage || 0} Kms `"
            persistent-hint
            prepend-icon="mdi-car-traction-control"
            type="number"
            suffix="Kms"
            outlined
            dense
          ></v-text-field>
        </div>
        <v-text-field
          v-model="form.cost"
          label="Costo del Servicio"
          :rules="[(v) => !!v || 'Es requerido']"
          type="number"
          suffix="MXN"
          prefix="$"
          prepend-icon="mdi-cash-multiple"
          outlined
          dense
        ></v-text-field>
      </v-col>
    </v-row>
  </v-form>
</template>
<script>
export default {
  props: {
    value: {
      type: Boolean,
      default: true,
    },
    form: {
      require: true,
      type: Object,
      default: () => {
        return {};
      },
    },
    propVehicleId: {
      type: [String, Number],
      require: false,
      default: null,
    },
  },

  mounted() {
    this.loadOptions(() => {});
    if (this.propVehicleId) {
      this.getVehicle((vehicle) => {
        this.form.vehicle_id = vehicle.id;
      });
    }
  },
  data() {
    return {
      vehicle: {},
      options: {
        vehicles: [],
        agencies: [],
        departments: [],
      },
    };
  },
  computed: {
    valid: {
      get() {
        return this.value;
      },
      set(value) {
        this.$emit("input", value);
      },
    },
    Vehicle: {
      get() {
        return this.vehicle;
      },
      set(vehicle) {
        this.vehicle = vehicle;
        this.form.vehicle_id = vehicle.id;
        this.form.mileage = vehicle.last_mileage;
      },
    },
    RulesMileage() {
      return [
        (v) =>
          v > this.vehicle.last_mileage ||
          "Debe ser mayor al Ultimo Kilometraje",
        (v) => !!v || "Es Requerido",
      ];
    },
  },
  methods: {
    async getVehicle(cb) {
      const _this = this;
      await axios
        .get(`/admin/vehicles/${_this.propVehicleId}`)
        .then(function (response) {
          let Vehicle = response.data.data;
          _this.vehicle = Vehicle;
          _this.form.mileage = Vehicle.last_mileage;
          (cb || Function)(Response);
        });
    },
    async loadOptions(cb) {
      const _this = this;
      await axios
        .get(`/admin/vehicle-services/create`)
        .then(function (response) {
          let Data = response.data.data;
          _this.options.vehicles = Data.vehicles;
          _this.options.agencies = Data.agencies;
          _this.options.departments = Data.departments;
          (cb || Function)();
        });
    },
  },
};
</script>
<style lang=""></style>
