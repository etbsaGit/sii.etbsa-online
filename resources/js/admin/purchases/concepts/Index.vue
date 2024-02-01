<template>
  <v-tabs color="deep-purple accent-4">
    <v-tab>Conceptos de Compra</v-tab>
    <!-- <v-tab>Grupos de Articulo</v-tab> -->

    <v-tab-item>
      <v-container fluid>
        <purchase-concept></purchase-concept>
      </v-container>
    </v-tab-item>
    <!-- <v-tab-item>
      <v-container fluid>
        <purchase-concept-product></purchase-concept-product>
      </v-container>
    </v-tab-item> -->
  </v-tabs>
</template>
<script>
import PurchaseConceptProduct from "./PurchaseConceptProduct.vue";
import PurchaseConcept from "./PurchaseConcept.vue";
export default {
  components: { PurchaseConcept, PurchaseConceptProduct },
};
</script>
<style></style>
