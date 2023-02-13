<template>
  <v-card>
    <v-tabs color="deep-purple accent-4">
      <v-tab>Categorias</v-tab>
      <v-tab>Modelos</v-tab>

      <v-tab-item>
        <v-container fluid>
          <product-category />
        </v-container>
      </v-tab-item>
      <v-tab-item>
        <v-container fluid>
          <product-model />
        </v-container>
      </v-tab-item>
    </v-tabs>
  </v-card>
</template>
<script>
import ProductCategory from "./productCategory.vue";
import ProductModel from "./productModel.vue";
export default {
  name: "TabsCategory",
  components: { ProductCategory, ProductModel },
  mounted() {
    const _this = this;
    _this.$store.commit("setBreadcrumbs", [
      { label: "Productos", name: "" },
      { label: "Categorias y Modelos", name: "" },
    ]);
  },
};
</script>
