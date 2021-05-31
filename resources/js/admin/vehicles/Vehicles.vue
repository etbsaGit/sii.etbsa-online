<template>
  <v-card flat>
    <v-app-bar dark>
      <v-tabs
        v-model="tab"
        background-color="blue lighten-1"
        active-class="blue darken-1"
        icons-and-text
        centered
        grow
        dark
      >
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
    <v-sheet
      id="scrolling-techniques-3"
      class="overflow-y-auto"
      :max-height="maxHeight"
    >
      <v-slide-x-transition>
        <v-container fluid>
          <keep-alive max="2">
            <router-view></router-view>
          </keep-alive>
        </v-container>
      </v-slide-x-transition>
    </v-sheet>
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
