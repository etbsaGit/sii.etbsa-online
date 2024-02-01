<template>
  <v-form v-model="valid" ref="form" lazy-validation>
    <v-row class="align-center">
      <v-col cols="12" md="4">
        <v-autocomplete
          v-model="vehicle"
          :items="options.vehicles"
          label="Vehiculo Matricula"
          :rules="[(v) => !!v || 'Valor Requerido']"
          item-text="matricula"
          :hint="vehicle.serie"
          persistent-hint
          return-object
          required
        ></v-autocomplete>
      </v-col>

      <v-col cols="12" md="4">
        <v-text-field
          v-model="form.kilometraje_actual"
          :rules="[
            (v) => !!v || 'Valor Requerido',
            (v) =>
              v > vehicle.ultimo_kilometraje ||
              `Valor debe ser mayor al utimo Kilometraje (${vehicle.ultimo_kilometraje} kms)`,
          ]"
          label="Kilometraje Actual"
          suffix="KM"
          type="numeric"
          :hint="`Ultimo Kilometraje ${vehicle.ultimo_kilometraje} kms`"
          persistent-hint
          required
        ></v-text-field>
      </v-col>

      <v-col cols="12" md="4">
        <v-slider
          v-model="form.odometro_percent_gas"
          label="% Odometro Gas Actual"
          step="5"
          thumb-color="green"
          thumb-label="always"
        ></v-slider>
      </v-col>
      <v-col cols="12" md="4">
        <v-text-field
          v-model="form.tarjeta"
          :rules="[(v) => !!v || 'Valor Requerido']"
          label="Tarjeta"
          required
        ></v-text-field>
      </v-col>

      <v-col cols="12" md="4">
        <v-text-field
          v-model="form.destino"
          :rules="[(v) => !!v || 'Valor Requerido']"
          label="Destino"
          required
        ></v-text-field>
      </v-col>

      <v-col cols="12" md="4">
        <v-autocomplete
          v-model="form.suc_cargo"
          :items="options.agencies"
          item-text="title"
          item-value="id"
          label="Agencia"
          placeholder="Con Cargo A"
        ></v-autocomplete>
        <v-text-field
          v-model="form.con_cargo_a"
          label="Departamento"
          placeholder="Con Cargo A"
        ></v-text-field>
      </v-col>

      <v-col cols="12" md="4">
        <v-textarea
          v-model="form.motivo"
          :rules="[(v) => !!v || 'Valor Requerido']"
          label="Motivo"
          required
        ></v-textarea>
      </v-col>

      <v-col cols="12" md="4">
        <v-text-field
          v-model="form.gas_lts"
          :rules="[(v) => !!v || 'Valor Requerido']"
          label="Litros a Solicitar"
          suffix="Litros"
          :hint="`Capacidad Maxima ${vehicle.capacidad_tanque} LTS`"
          persistent-hint
          outlined
          required
        ></v-text-field>
      </v-col>

      <v-col cols="12" md="4">
        <!-- <v-file-input
                accept="image/png, image/jpeg, image/bmp"
                placeholder="Foto de tacometro (opcional)"
                prepend-icon="mdi-camera"
                label="Tacometro Medidor"
              ></v-file-input> -->
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
  },
  data() {
    return {
      vehicle: {},
      options: {
        vehicles: [],
        agencies: [],
        department: [],
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
  },
};
</script>
<style lang=""></style>
