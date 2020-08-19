<template>
  <div class="page_wrap_vue">
    <v-tabs color="primary" v-model="active">
      <v-tab key="gps" href="#gps" ripple>
        GPS
      </v-tab>
      <v-tab key="manage-groups-gps" href="#manage-groups-gps" ripple>
        Clientes
      </v-tab>
      <v-tab key="manage-chips-gps" href="#manage-chips-gps" ripple>
        Chips GPS
      </v-tab>
      <v-tab key="upload" href="#upload" ripple>
        Upload
      </v-tab>

      <v-tab-item value="gps">
        <v-card flat>
          <v-card-text>
            <gps-lists></gps-lists>
          </v-card-text>
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
      <v-tab-item value="upload">
        <v-card flat v-if="active === 'upload'">
          <v-card-text>
            <gps-upload></gps-upload>
          </v-card-text>
        </v-card>
      </v-tab-item>
    </v-tabs>
  </div>
</template>

<script>
import GpsLists from "@admin/gps/components/GpsLists.vue";
import GpsGroupLists from "@admin/gps/components/GpsGroupLists.vue";
import ChipsLists from "@admin/gps/components/GpsChipsLists.vue";
import GpsUpload from "@admin/gps/components/GpsUpload.vue";
export default {
  components: {
    GpsUpload,
    GpsGroupLists,
    ChipsLists,
    GpsLists
  },
  mounted() {
    const self = this;
    self.$eventBus.$on(["UPLOAD_COMPLETE"], () => {
      self.active = "gps";
    });
    self.$store.commit("setBreadcrumbs", [{ label: "GPS", name: "" }]);
  },
  data() {
    return {
      active: "gps"
    };
  },
  watch: {
    active(v) {
      console.log("active tab: " + v);
    }
  },
  methods: {}
};
</script>

<style scoped="">
.finder_wrap {
  padding: 0 20px;
}
</style>