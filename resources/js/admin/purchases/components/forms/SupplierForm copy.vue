<template>
  <v-form ref="formSupplier" v-model="valid" lazy-validation>
    <v-row class="overline">
      <v-col cols="12">
        <v-text-field
          v-model="form.alias"
          label="Alias del Proveedor:"
          :rules="[(v) => !!v || 'Es Requerido']"
          dense
        ></v-text-field>
      </v-col>
      <v-col cols="12">
        <v-text-field
          v-model="form.business_name"
          label="Razon Social:"
          :rules="[(v) => !!v || 'Es Requerido']"
          dense
        ></v-text-field>
      </v-col>
      <v-col cols="12">
        <v-text-field
          v-model="form.rfc"
          label="RFC:"
          :rules="[(v) => !!v || 'Es Requerido']"
          counter="12"
          dense
        ></v-text-field>
      </v-col>
      <v-col cols="12">
        <v-text-field
          v-model="form.address"
          label="Domicilio:"
          dense
        ></v-text-field>
      </v-col>
      <v-col cols="12" md="6">
        <v-text-field
          v-model="form.phone"
          v-mask="'(###)###-##-##'"
          placeholder="(###)###-##-##"
          hint="Numero 10 digitos"
          label="Telefono:"
          dense
        ></v-text-field>
      </v-col>
      <v-col cols="12" md="6">
        <v-text-field
          v-model="form.email"
          label="Email:"
          type="email"
          dense
        ></v-text-field>
      </v-col>
    </v-row>
    <v-card-actions>
      <v-spacer></v-spacer>
      <v-btn @click="$eventBus.$emit('CLOSE_DIALOG')">Cancelar</v-btn>
      <v-btn
        v-if="$gate.allow('editSupplier', 'compras')"
        color="green"
        class="ml-4"
        @click="save"
        :disabled="!valid"
      >
        Guardar
      </v-btn>
    </v-card-actions>
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
    return {};
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
  methods: {
    save() {
      this.$emit("submit");
    },
  },
};
</script>

<style></style>
