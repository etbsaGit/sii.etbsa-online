<template>
  <v-card>
    <v-tabs color="deep-purple accent-4">
      <v-tab>Caracteristicas</v-tab>
      <!-- <v-tab>Atributos</v-tab> -->

      <v-tab-item>
        <v-container fluid>
          <list-character></list-character>
        </v-container>
      </v-tab-item>
      <!-- <v-tab-item>
        <v-container fluid>
          <product-suppliers></product-suppliers>
        </v-container>
      </v-tab-item> -->
    </v-tabs>
  </v-card>
</template>
<script>
import ListCharacter from "./ListCharacter.vue";
export default {
  name: "TabsBAttributesSProduct",
  components: { ListCharacter },
  mounted() {
    const _this = this;
    _this.$store.commit("setBreadcrumbs", [
      { label: "Productos", to: { name: "products.index" } },
      { label: "Atributos y Caracteristicas", name: "" },
    ]);
  },
};
</script>
