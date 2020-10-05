<template>
  <v-container fluid>
    <v-tabs v-model="activeTab" grow>
      <template v-for="item in Tabs">
        <v-tab v-if="item.show" :key="item.title" :to="item.to">
          <v-icon left>{{ item.icon }}</v-icon>
          {{ item.title }}
        </v-tab>
      </template>
    </v-tabs>
    <v-slide-x-transition>
      <router-view></router-view>
    </v-slide-x-transition>
  </v-container>
</template>

<script>
export default {
  data() {
    return {
      activeTab: "",
    };
  },
  computed: {
    Tabs() {
      return [
        {
          title: "Vendedores",
          icon: "mdi-view-dashboard",
          to: { name: "sellers.list" },
          show: this.$gate.allow("updateUser", "user"),
        },
        {
          title: "Prospectos",
          icon: "mdi-account",
          to: { name: "prospect.list" },
          show: this.$gate.allow("assignSeller", "tracking"),
        },
        {
          title: "Seguimientos",
          icon: "mdi-gavel",
          to: { name: "tracking.list" },
          show: true,
        },
      ];
    },
  },
};
</script>
