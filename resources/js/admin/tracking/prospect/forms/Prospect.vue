<template>
  <v-form ref="formProspect" v-model="valid" lazy-validation @submit="submit()">
    <v-container grid-list-xs>
      <v-switch v-model="form.is_moral" flat class="mx-auto"></v-switch>

      <v-text-field
        v-model="form.full_name"
        label="Nombre a quien va dirigido:"
        :rules="rules"
        filled
        outlined
      />

      <v-text-field
        v-model="form.company"
        label="Razon Social:"
        filled
        outlined
      />

      <v-text-field v-model="form.rfd" label="RFC:" filled outlined />

      <v-autocomplete
        v-model="estate_id"
        :items="options.estates"
        item-text="name"
        item-value="id"
        label="Estado:"
        filled
        outlined
        :rules="rules"
      />

      <v-autocomplete
        v-model="form.township_id"
        :items="options.townships"
        label="Municipio"
        item-text="name"
        item-value="id"
        filled
        outlined
        :rules="rules"
        outline
      />

      <v-text-field
        v-model="form.phone"
        label="Telefono:"
        :rules="rules"
        filled
        outlined
        counter="10"
      />

      <v-text-field v-model="form.email" label="email:" filled outlined />

      <v-text-field
        v-model="form.town"
        label="Nombre Racho/comunidad (optional):"
        placeholder="¿De donde nos visita?"
        filled
        outlined
      />

      <v-btn block type="submit" color="success" dark :disabled="!valid">
        {{ textBtn }}
      </v-btn>
    </v-container>
  </v-form>
</template>

<script>
import { mixinEstates } from '~/common/mixin/estate_township.js';
export default {
  mixins: [mixinEstates],
  props: {
    form: {
      required: true,
      type: Object,
    },
    textBtn: {
      required: false,
      type: String,
      default: 'Guardar',
    },
  },
  data() {
    return {
      valid: true,
      rules: [(v) => !!v || 'campo requerido.'],
    };
  },
  created() {
    const self = this;
    self.loadEstates(() => {
      self.estate_id = self.form.estate_id ? self.form.estate_id : null;
    });
  },
  methods: {
    submit() {
      if (!this.$refs.formProspect.validate()) return;
      this.$emit('submit');
    },
  },
};
</script>

<style></style>
