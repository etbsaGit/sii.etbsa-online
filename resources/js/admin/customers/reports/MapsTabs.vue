<template>
  <v-card color="basil">
    <v-tabs v-model="tab" background-color="transparent" color="basil" grow>
      <v-tab v-for="item in items" :key="item">
        {{ item.name }}
      </v-tab>
    </v-tabs>

    <v-tabs-items v-model="tab">
      <v-tab-item v-for="item in items" :key="item">
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
          name: "AGRICULTURA PROTEGIDA",
          src: "https://cmgs.gob.mx/siapdsg/apps/webappviewer/index.html?id=aaf4b04e58a1488f932a9bf468eb0990",
        },
        {
          name: "ESTIMACIÓN DE SUPERFICIE AGRÍCOLA",
          src: "https://cmgs.gob.mx/siapdsg/apps/webappviewer/index.html?id=f2a0fc332f24421095d11cfe6ffc2824",
        },
        {
          name: "FRONTERA AGRÍCOLA",
          src: "https://cmgs.gob.mx/siapdsg/apps/webappviewer/index.html?id=19e91e718f7644c380c178703e46f1ef",
        },
        {
          name: "PRESAS AGRICOLAS",
          src: "https://cmgs.gob.mx:31/presasagricolas/",
        },
        {
          name: "SEGUIMIENTO METEOROLÓGICO",
          src: "https://cmgs.gob.mx/siapdsg/apps/webappviewer/index.html?id=a332db9e7dd548c1819f0ebdd97e3d10",
        },
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