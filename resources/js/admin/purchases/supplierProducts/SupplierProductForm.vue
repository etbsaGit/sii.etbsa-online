<template>
  <v-card>
    <v-container>
      <v-form>
        <v-row>
          <v-col cols="12">
            <v-text-field label="SKU"></v-text-field>
          </v-col>
          <v-col cols="12">
            <v-text-field label="Nombre"></v-text-field>
          </v-col>
          <v-col cols="12">
            <v-select label="Clave Unidad"></v-select>
          </v-col>
          <v-col cols="12">
            <v-textarea label="Descripcion"></v-textarea>
          </v-col>
        </v-row>
      </v-form>
    </v-container>
  </v-card>
</template>
<script>
export default {
  name: "SupplierProductForm",
  props: {
    form: {
      require: true,
      type: Object,
    },
  },
};
</script>
<style></style>
