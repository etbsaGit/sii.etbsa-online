<template>
  <v-form v-model="valid" ref="form" lazy-validation>
    <v-row class="overline">
      <v-col cols="12" md="8">
        <v-row>
          <v-col cols="12" class="pb-0">
            <v-autocomplete
              v-model="form.vehicle"
              :items="options.vehicles"
              label="Selecciones una Matricula"
              item-text="matricula"
              item-value="id"
              :rules="[(v) => !!form.vehicle.id || 'Selecciones una Matricula']"
              return-object
              outlined
              dense
            ></v-autocomplete>
          </v-col>
          <template v-if="!form.vehicle.bidon_fuel">
            <v-col
              cols="12"
              md="6"
              class="d-flex flex-column justify-start align-center"
            >
              <div>Ultimo Kilometraje</div>
              <span class="text-h4">
                {{ vehicle.last_mileage ? vehicle.last_mileage : "----" }}
                KMS
              </span>
            </v-col>
            <v-col cols="12" md="6" align-self="end">
              <div class="text-end text-primary">
                Kilometraje Actual
              </div>
              <v-text-field
                v-model="form.actual_mileage"
                placeholder="Registrar Kilometraje"
                type="Number"
                prefix="KMs"
                :rules="RulesMileage"
                clearable
                outlined
                reverse
              ></v-text-field>
            </v-col>
          </template>
          <!-- <template v-else>
            <v-col cols="12" class="pb-0">
              <v-select
                v-model="Fuel"
                :rules="[(v) => !!v || 'Valor Requerido']"
                :items="options.vehicle_fuel"
                item-text="name"
                label="Tipo Combustible"
                return-object
                required
                outlined
                dense
              ></v-select>
            </v-col>
          </template> -->
        </v-row>
      </v-col>
      <v-col
        cols="12"
        md="4"
        class="d-flex flex-column justify-center align-center py-0"
        v-if="!form.vehicle.bidon_fuel"
      >
        <div class="text-center">
          Marca del Odometro de la ultima Dispercion
        </div>
        <gauge
          :value="gas_odometer.val"
          :min="0"
          :max="100"
          :fontSize="'16px'"
          :padding="false"
          :svgStyle="{
            maxWidth: 430,
            maxHeight: 150,
          }"
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
        />
        <div style="width: 100%; margin-top: 1rem;">
          <v-slider
            v-model="form.gas_odometer.val"
            :label="gas_odometer.label"
            :thumb-color="gas_odometer.color"
            :rules="RulesOdometer"
            thumb-label
            dense
          ></v-slider>
        </div>
      </v-col>
    </v-row>
  </v-form>
</template>
<script>
export default {
  name: "DispersalFormStepOne",
  props: {
    value: {
      type: Boolean,
      default: true,
    },
    options: {
      type: Object,
    },
    form: {
      type: Object,
      require: true,
      default: () => {
        return {};
      },
    },
  },
  data() {
    return {
      gas_odometer: { label: "Odometro Actual*", val: 50, color: "red" },
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
    RulesMileage() {
      return [
        (v) =>
          v > this.form.vehicle.last_mileage ||
          "Debe ser mayor al Ultimo Kilometraje",
        (v) => !!v || "Es Requerido",
      ];
    },
    RulesOdometer() {
      return [
        (v) =>
          v < this.form.vehicle.fuel_odometer ||
          "Debe ser Menor al ultimo Odometro",
      ];
    },
  },
  watch: {
    ["form.vehicle"]: {
      handler: _.debounce(function (v) {
        this.gas_odometer.val = v.fuel_odometer || 100;
      }, 200),
      deep: true,
    },
  },
};
</script>
<style></style>
