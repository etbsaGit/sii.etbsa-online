<template>
  <v-card>
    <v-tabs color="deep-purple accent-4">
      <v-tab>Administrar</v-tab>
      <v-tab>Inventario</v-tab>

      <v-tab-item>
        <v-container fluid>
          <product-inventory-table></product-inventory-table>
        </v-container>
      </v-tab-item>
      <v-tab-item>
        <inventory-report-table></inventory-report-table>
      </v-tab-item>
    </v-tabs>
  </v-card>
</template>
  <script>
import InventoryReportTable from "./InventoryReportTable.vue";
import ProductInventoryTable from "./ProductInventoryTable.vue";
export default {
  components: { ProductInventoryTable, InventoryReportTable },
  name: "InventoryIndex",
  mounted() {
    const _this = this;
    _this.$store.commit("setBreadcrumbs", [
      { label: "Inventario Productos", name: "" },
    ]);
  },
};
</script>
  