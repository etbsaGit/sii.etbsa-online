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
    <!-- <v-sheet
      id="scrolling-techniques-3"
      class="overflow-y-auto"
      :max-height="minHeight"
    > -->
    <v-slide-x-transition>
      <keep-alive>
        <router-view></router-view>
      </keep-alive>
    </v-slide-x-transition>
    <!-- </v-sheet> -->
  </v-card>
</template>
<script>
import GpsList from "@admin/gps/gps/Index.vue";
import CustomersList from "@admin/gps/groups/Index.vue";
import ChipsList from "@admin/gps/chips/Index.vue";
export default {
  components: {
    GpsList,
    CustomersList,
    ChipsList,
  },
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
          title: "GPS",
          icon: "mdi-crosshairs-gps",
          to: { name: "gps.list" },
          // show: this.$gate.allow("updateUser", "user"),
          show: true,
        },
        {
          title: "Clientes",
          icon: "mdi-account-group",
          to: { name: "gps.customer.list" },
          show: true,
          // show: this.$gate.allow("assignSeller", "tracking"),
        },
        {
          title: "Chips GPS",
          icon: "mdi-sim",
          to: { name: "gps.chips.list" },
          show: true,
        },
      ];
    },
  },
};
</script>
