<template>
  <v-card color="basil">
    <v-tabs v-model="tab" background-color="transparent" color="basil" grow>
      <v-tab v-for="(item, index) in items" :key="`tab-mapa-${index}`">
        {{ item.name }}
      </v-tab>
    </v-tabs>

    <v-tabs-items v-model="tab">
      <v-tab-item v-for="(item, index) in items" :key="`mapas-ventas-${index}`">
        <v-card color="basil" flat>
          <iframe
            :src="item.src"
            style="height: 80vh"
            width="100%"
            :title="item.name"
          ></iframe>
        </v-card>
      </v-tab-item>
    </v-tabs-items>
  </v-card>
</template>
  
  <script>
export default {
  data() {
    return {
      tab: null,
      items: [
        {
          name: "Analitica de Datos",
          src:
            "https://sites.google.com/view/radiografiadedatos?usp=sharing",
        },
        // {
        //   name: "Ventas Por Categoria 2013-2023",
        //   src:
        //     "https://www.google.com/maps/d/viewer?mid=1jvKVM0VMGbFEYnbdy7Yg_Ebzm5A7cfY&ll=20.833392554664837%2C-100.65528555&z=8" +
        //     "&output=embed",
        // },
      ],
    };
  },
  mounted() {
    const self = this;
    self.$store.commit("setBreadcrumbs", [{ label: "Mapas", name: "" }]);
  },
};
</script>
  
  <style>
/* Helper classes */
.basil {
  background-color: #fffbe6 !important;
}
.basil--text {
  color: #356859 !important;
}
</style>