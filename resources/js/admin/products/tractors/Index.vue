<template>
  <div>
    <v-toolbar flat class="text-h4 align-center">
      <v-icon left x-large>mdi-tractor</v-icon>Lista de Tractores
      <v-spacer></v-spacer>
      <v-text-field
        v-model="filters.search"
        outlined
        dense
        prepend-icon="mdi-magnify"
        label="Buscar"
        class="mr-3"
        hide-details
      ></v-text-field>
      <v-btn color="primary" dark @click="create">
        Registrar Tractor
      </v-btn>
    </v-toolbar>
    <v-dialog v-model="dialog" max-width="500px" scrollable>
      <v-card>
        <v-toolbar dense dark color="primary" class="text-h6">{{
          formTitle
        }}</v-toolbar>
        <v-card-text class="pt-4">
          <v-form ref="form" v-model="valid" lazy-validation>
            <v-select label="Modelo Tractor" type="text" outlined dense />
            <v-text-field label="Nombre" type="text" outlined dense />
            <v-select label="Ubicacion" type="text" outlined dense />
            <v-textarea label="Descripcion Larga" outlined dense />
            <v-text-field label="Serie Motor" type="text" outlined dense />
            <v-switch label="Activo" type="text" outlined dense />
            <v-text-field
              label="Precio Lista"
              prefix="$"
              type="number"
              outlined
              dense
            />
            <v-text-field
              label="Precio Costo"
              prefix="$"
              type="number"
              outlined
              dense
            />
            <v-text-field
              label="Precio Financiamiento"
              prefix="$"
              type="number"
              outlined
              dense
            />
          </v-form>
          <div class="d-flex"></div>
        </v-card-text>
        <v-card-actions>
          <v-btn text color="error" @click="dialog = false">
            Cancel
          </v-btn>
          <v-spacer />
          <v-btn :loading="loading" color="primary" @click="submit">
            Save
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </div>
</template>
<script>
export default {
  name: "Tractors",
  mounted() {
    const _this = this;
    _this.$store.commit("setBreadcrumbs", [{ label: "Tractores", name: "" }]);
  },
  data() {
    return {
      valid: true,
      dialog: false,
      loading: false,
      isUpdate: false,
      form: {},
      rules: {},
      filters: {
        search: "",
      },
    };
  },
  computed: {
    formTitle() {
      return this.isUpdate ? "Editar Tractor" : "Registrar Tractor";
    },
  },
  methods: {
    create() {
      this.dialog = true;
      this.isUpdate = false;
      this.$refs.form.reset();
      this.$refs.form.resetValidation();
    },
    submit() {
      this.dialog = false;
    },
  },
};
</script>
<style></style>
