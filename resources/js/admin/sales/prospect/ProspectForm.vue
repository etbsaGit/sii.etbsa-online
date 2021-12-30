<template>
  <v-form ref="form" v-model="valid" lazy-validation @submit.prevent="submit()">
    <v-row align="start" align-content="start" dense>
      <v-col cols="12">
        <v-text-field
          v-model="form.full_name"
          label="Nombre del Contacto *"
          placeholder="Nombre completo"
          persistent-placeholder
          :rules="rules"
          outlined
          filled
          dense
        />
      </v-col>
      <v-col cols="12">
        <v-text-field
          v-model="form.company"
          label="Compañia o Razon Social "
          outlined
          filled
          dense
        />
      </v-col>

      <v-col cols="12">
        <v-text-field v-model="form.rfc" label="RFC" filled dense outlined />
      </v-col>

      <v-col cols="12" md="6">
        <v-text-field
          v-model="form.email"
          label="Correo Electronico"
          type="email"
          outlined
          filled
          dense
        />
      </v-col>
      <v-col cols="12" md="6">
        <v-text-field
          v-model="form.phone"
          label="Telefono *"
          :rules="rules"
          counter="10"
          outlined
          filled
          dense
        />
      </v-col>
      <v-col cols="12">
        <v-textarea
          v-model="form.town"
          label="Nombre Racho/comunidad "
          placeholder="Comunidad o Rancho"
          height="100"
          outlined
          filled
          dense
        />
      </v-col>
      <v-col cols="12" md="6">
        <v-autocomplete
          v-model="form.estate_id"
          :items="options.estates"
          item-text="name"
          item-value="id"
          label="Estado *"
          :rules="rules"
          outlined
          filled
          dense
        />
      </v-col>

      <v-col cols="12" md="6">
        <v-autocomplete
          v-model="form.township_id"
          :items="options.townships"
          label="Municipio *"
          item-text="name"
          item-value="id"
          :rules="rules"
          outlined
          filled
          dense
        />
      </v-col>
    </v-row>

    <v-btn block type="submit" color="primary" :disabled="!valid">
      {{ textBtn }}
    </v-btn>
  </v-form>
</template>
<script>
import { mixinEstates } from "~/common/mixin/estate_township.js";
export default {
  mixins: [mixinEstates],
  props: {
    form: {
      type: Object,
      required: true,
      default: () => {
        return {};
      },
    },
    textBtn: {
      type: String,
      required: false,
      default: "Guardar",
    },
  },
  data() {
    return {
      valid: true,
      rules: [(v) => !!v || "campo requerido."],
    };
  },
  mounted() {
    const _this = this;
    _this.loadEstates(() => {
      _this.estate_id = _this.form.estate_id ? _this.form.estate_id : null;
    });
  },
  methods: {
    submit() {
      if (!this.$refs.form.validate()) return;
      this.$emit("submit");
    },
  },
};
</script>
<style></style>
