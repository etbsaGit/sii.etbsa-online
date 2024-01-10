<template >
  <v-card>
    <v-tabs
      v-model="tab"
      background-color="deep-purple accent-4"
      dark
      icons-and-text
      grow
    >
      <v-tabs-slider></v-tabs-slider>

      <v-tab href="#tab-1">
        Agencias
        <v-icon>mdi-store</v-icon>
      </v-tab>

      <v-tab href="#tab-2">
        Departamentos
        <v-icon>mdi-shape-plus</v-icon>
      </v-tab>

      <v-tab href="#tab-3">
        Puestos
        <v-icon>mdi-account-box</v-icon>
      </v-tab>
    </v-tabs>

    <v-tabs-items v-model="tab">
      <v-tab-item value="tab-1">
        <v-card flat>
          <agencies />
        </v-card>
      </v-tab-item>
      <v-tab-item value="tab-2">
        <v-card flat>
          <departments />
        </v-card>
      </v-tab-item>
      <v-tab-item value="tab-3">
        <v-card flat>
          <jobs />
        </v-card>
      </v-tab-item>
    </v-tabs-items>
  </v-card>
</template>
<script>
import Agencies from "./Agencies.vue";
import Departments from "./Departments.vue";
import Jobs from "./Jobs.vue";
export default {
  components: { Agencies, Departments, Jobs },
  name: "RRHHCatalogs",
  data() {
    return {
      tab: null,
      text: "Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.",
    };
  },
};
</script>
<style >
</style>