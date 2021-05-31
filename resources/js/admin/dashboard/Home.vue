<template>
  <v-layout align-center justify-center row fill-height>
    <v-img src="../img/etbsa-logo-agricola.png" class="swiss ma-4"></v-img>
    <v-img src="../img/etbsa-logo-construccion.png" class="swiss ma-4"></v-img>
  </v-layout>
</template>

<script>
export default {
  mounted() {
    const self = this;
    self.$store.commit("setBreadcrumbs", [{ label: "Dashboard", name: "" }]);
  },
};
</script>

<style scoped>
.swiss {
  max-width: 300px;
  opacity: 0.2;
}
</style>
