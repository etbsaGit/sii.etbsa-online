<template>
  <v-card id="vehicles-card">
    <!-- tabs -->
    <v-tabs v-model="tab" show-arrows>
      <v-tab v-for="tab in Tabs" :key="tab.icon" :to="tab.to">
        <v-icon size="20" class="me-3">
          {{ tab.icon }}
        </v-icon>
        <span>{{ tab.title }}</span>
      </v-tab>
    </v-tabs>

    <v-slide-x-transition>
      <keep-alive>
        <router-view></router-view>
      </keep-alive>
    </v-slide-x-transition>
  </v-card>
</template>

<script>
export default {
  name: "Vehicles",
  data() {
    return {
      tab: "",
    };
  },
  computed: {
    maxHeight() {
      const height = this.$vuetify.breakpoint.mobile ? "90vh" : "83vh";
      return `calc(${height} - ${this.$vuetify.application.top}px)`;
    },
    Tabs() {
      return [
        {
          title: "Flotilla",
          icon: "mdi-car",
          to: { name: "vehicle.list" },
          // show: this.$gate.allow("updateUser", "user"),
          show: true,
        },
        {
          title: "Dispersiones",
          icon: "mdi-gas-station",
          to: { name: "vehicle.dispersal.list" },
          show: true,
          // show: this.$gate.allow("assignSeller", "tracking"),
        },
        {
          title: "Servicios",
          icon: "mdi-tools",
          to: { name: "vehicle.services.list" },
          show: true,
        },
      ];
    },
  },
};
</script>
