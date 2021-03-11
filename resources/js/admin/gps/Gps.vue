<template>
  <v-tabs
    v-model="active"
    centered
    icons-and-text
    grow
    color="success"
    class="elevation-4 mb-2"
  >
    <v-tab key="gps" href="#gps" ripple> GPS </v-tab>
    <v-tab key="manage-groups-gps" href="#manage-groups-gps" ripple>
      Clientes
    </v-tab>
    <v-tab key="manage-chips-gps" href="#manage-chips-gps" ripple>
      Chips GPS
    </v-tab>

    <v-tab-item value="gps">
      <v-card flat color="grey lighten-3">
        <gps-index></gps-index>
      </v-card>
    </v-tab-item>
    <v-tab-item value="manage-groups-gps">
      <v-card flat>
        <v-card-text>
          <gps-group-lists></gps-group-lists>
        </v-card-text>
      </v-card>
    </v-tab-item>
    <v-tab-item value="manage-chips-gps">
      <v-card flat>
        <v-card-text>
          <chips-lists></chips-lists>
        </v-card-text>
      </v-card>
    </v-tab-item>
  </v-tabs>
</template>

<script>
import GpsIndex from '@admin/gps/gps/Index.vue';
import GpsGroupLists from '@admin/gps/groups/Index.vue';
import ChipsLists from '@admin/gps/chips/Index.vue';
export default {
  components: {
    GpsGroupLists,
    ChipsLists,
    GpsIndex,
  },
  mounted() {
    const self = this;
    self.$eventBus.$on(['UPLOAD_COMPLETE'], () => {
      self.active = 'gps';
    });
    self.$store.commit('setBreadcrumbs', [{ label: 'GPS', name: '' }]);
  },
  data() {
    return {
      active: 'gps',
    };
  },
  watch: {
    active(v) {
      console.log('active tab: ' + v);
    },
  },
  methods: {},
};
</script>

<style scoped="">
.finder_wrap {
  padding: 0 20px;
}
</style>
