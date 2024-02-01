<template>
  <v-card>
    <v-card-title>Registrar Nuevo Producto de Proveedor</v-card-title>
  </v-card>
</template>
<script>
export default {
  name: "SupplierProductCreate",
  props: {
    supplierIdProp: {
      require: false,
      type: [String, Number],
    },
  },
};
</script>
<style></style>
