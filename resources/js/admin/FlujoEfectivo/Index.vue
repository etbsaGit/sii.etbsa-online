<template>
  <v-card flat>
    <v-app-bar>
      <v-tabs v-model="tab" icons-and-text centered grow>
        <v-tabs-slider color="purple"></v-tabs-slider>
        <v-tab
          v-for="item in Tabs.filter((i) => i.show)"
          :key="item.title"
          :to="item.to"
        >
          {{ item.title }} <v-icon>{{ item.icon }}</v-icon>
        </v-tab>
      </v-tabs>
    </v-app-bar>

    <v-slide-x-transition>
      <keep-alive>
        <router-view></router-view>
      </keep-alive>
    </v-slide-x-transition>
  </v-card>
</template>
  <script>
export default {
  data() {
    return {
      tab: "",
    };
  },
  computed: {
    minHeight() {
      const height = this.$vuetify.breakpoint.mobile ? "90vh" : "83vh";
      return `calc(${height} - ${this.$vuetify.application.top}px)`;
    },
    Tabs() {
      return [
        {
          title: "Polizas",
          icon: "mdi-crosshairs-gps",
          to: { name: "poliza.ingresos.list" },
          // show: this.$gate.allow("updateUser", "user"),
          show: true,
        },
        // {
        //   title: "Polizas Egresos",
        //   icon: "mdi-account-group",
        //   to: { name: "poliza.egresos.list" },
        //   show: true,
        //   // show: this.$gate.allow("assignSeller", "tracking"),
        // },
      ];
    },
  },
};
</script>
  