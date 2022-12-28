<template>
  <v-tabs color="deep-purple accent-4">
    <v-tab>Facturas</v-tab>
    <v-tab>Complementos</v-tab>

    <v-tab-item>
      <v-container fluid>
        <invoices></invoices>
      </v-container>
    </v-tab-item>
    <v-tab-item>
      <v-container fluid>
        <complementos></complementos>
      </v-container>
    </v-tab-item>
  </v-tabs>
</template>
<script>
import Invoices from "./facturas/Index.vue";
import Complementos from "./complementos/Index.vue";
export default {
  components: { Invoices, Complementos },
};
</script>
<style></style>
