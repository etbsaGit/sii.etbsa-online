<template>
  <HistorySales />
</template>

<script>
import HistorySales from "@admin/marketing/components/HistorySales.vue";
export default {
  name: "MarketingIndex",
  components: {
    HistorySales,
  },
  mounted() {
    const self = this;
    self.$store.commit("setBreadcrumbs", [{ label: "Historial Ventas", name: "" }]);
  },
};
</script>
