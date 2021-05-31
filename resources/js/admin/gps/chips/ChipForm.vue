<template>
  <v-form v-model="valid" ref="form" lazy-validation>
    <v-row class="align-start">
      <v-col cols="12" md="4">
        <v-text-field
          v-model="form.sim"
          label="SIM"
          :rules="rules"
        ></v-text-field>
      </v-col>
      <v-col cols="12" md="4">
        <v-text-field v-model="form.imei" label="IMEI" counter></v-text-field>
      </v-col>
      <v-col cols="12" md="4">
        <v-text-field
          v-model="form.cuenta"
          label="Cuenta"
          counter
        ></v-text-field>
      </v-col>
      <v-col cols="12" md="4">
        <v-text-field
          v-model="form.costo"
          label="Costo:"
          :rules="rules"
          type="Numeric"
          prefix="$"
          suffix="MXN"
        ></v-text-field>
      </v-col>

      <v-col cols="6" md="4">
        <v-text-field
          v-model="form.fecha_activacion"
          label="Fecha Activacion"
          type="date"
          :rules="rules"
        ></v-text-field>
      </v-col>

      <v-col cols="12" md="4">
        <v-text-field
          v-model="form.fecha_cancelacion"
          label="Fecha Cancelacion"
          type="date"
          clearable
        ></v-text-field>
      </v-col>
      <v-col cols="12" md="8">
        <v-textarea
          label="Descripcion:"
          v-model="form.description"
          outlined
        ></v-textarea> </v-col
    ></v-row>
  </v-form>
</template>
<script>
export default {
  props: {
    value: {
      type: Boolean,
      default: true,
    },
    form: { type: Object, require: true },
  },
  data() {
    return {
      valid: true,
      rules: [(v) => !!v || "Campo Requerido"],
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
