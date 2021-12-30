<template>
  <v-container fluid>
    <v-tabs
      v-model="activeTab"
      centered
      icons-and-text
      grow
      show-arrows
      color="success"
      class="elevation-4 mb-2"
      background-color="grey lighten-4"
    >
      <template v-for="item in Tabs">
        <v-tab v-if="item.show" :key="item.title" :to="item.to">
          {{ item.title }}
          <v-icon left>{{ item.icon }}</v-icon>
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
        // {
        //   title: 'Vendedores',
        //   icon: 'mdi-view-dashboard',
        //   to: { name: 'sellers.list' },
        //   show: this.$gate.allow('updateUser', 'user'),
        // },
        {
          title: "Prospectos",
          icon: "mdi-account",
          to: { name: "prospect.list" },
          show: true,
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
