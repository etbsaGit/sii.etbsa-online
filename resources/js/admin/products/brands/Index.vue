<template>
  <v-card>
    <v-tabs color="deep-purple accent-4">
      <v-tab>Marcas</v-tab>
      <v-tab>Proveedores</v-tab>

      <v-tab-item>
        <v-container fluid>
          <product-brands></product-brands>
        </v-container>
      </v-tab-item>
      <v-tab-item>
        <v-container fluid>
          <product-suppliers></product-suppliers>
        </v-container>
      </v-tab-item>
    </v-tabs>
  </v-card>
</template>
<script>
import ProductBrands from "./ProductBrands.vue";
import ProductSuppliers from "./ProductSuppliers.vue";
export default {
  name: "TabsBrandsSuppliers",
  components: { ProductBrands, ProductSuppliers },
  mounted() {
    const _this = this;
    _this.$store.commit("setBreadcrumbs", [
      { label: "Productos", to: { name: "products.index" } },
      { label: "Marcas y Proveedores", name: "" },
    ]);
  },
};
</script>
