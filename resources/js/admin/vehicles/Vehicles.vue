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
      activeTab: '',
    };
  },
  computed: {
    Tabs() {
      return [
        {
          title: 'Flotilla',
          icon: 'mdi-car',
          to: { name: 'vehicle.list' },
          // show: this.$gate.allow("updateUser", "user"),
          show: true,
        },
        {
          title: 'Dispersiones',
          icon: 'mdi-gas-station',
          to: { name: 'vehicle.dispersion.list' },
          show: true,
          // show: this.$gate.allow("assignSeller", "tracking"),
        },
        {
          title: 'Servicios',
          icon: 'mdi-settings',
          to: { name: 'vehicle.services.list' },
          show: true,
        },
      ];
    },
  },
};
</script>
