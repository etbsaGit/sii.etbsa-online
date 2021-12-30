<template>
  <v-container fluid>
    <v-row v-if="Tracking">
      <v-col cols="12" md="5" v-if="Tracking">
        <card-info-tracking :info="Tracking"></card-info-tracking>
      </v-col>
      <v-col cols="12" md="7" v-if="Tracking.historical">
        <v-tabs v-model="tab" background-color="transparent" centered grow>
          <v-tab v-for="item in items" :key="item">
            <template v-if="item == 'Mensajes'">
              <v-badge color="deep-purple accent-4" icon="mdi-bell">
                {{ item }}
              </v-badge>
            </template>
            <template v-else>{{ item }}</template>
          </v-tab>
        </v-tabs>

        <v-tabs-items v-model="tab">
          <v-tab-item>
            <historical-tracking
              :Tracking="propTracking"
              :timeline="timeline"
            ></historical-tracking>
          </v-tab-item>
          <v-tab-item>
            <message-tracking
              :seller-id="Tracking.owner"
              :tracking-id="Tracking.detail.id"
            ></message-tracking>
          </v-tab-item>
        </v-tabs-items>
      </v-col>
    </v-row>
  </v-container>
</template>

<script>
import CardInfoTracking from "@admin/sales/tracking/components/CardInfoTracking.vue";
import HistoricalTracking from "@admin/sales/tracking/components/HistoricalTracking.vue";
import MessageTracking from "@admin/sales/tracking/components/MessageTracking.vue";

export default {
  components: { CardInfoTracking, HistoricalTracking, MessageTracking },
  props: {
    propTrackingId: {
      required: true,
      type: [Number, String],
    },
  },
  data: () => ({
    Tracking: null,
    tab: null,
    items: ["Seguimiento", "Mensajes"],
  }),

  mounted() {
    const self = this;
    self.loadTracking(() => {});
    self.$eventBus.$on(["MESSAGE_ADDED"], () => {
      self.loadTracking(() => {});
    });
  },
  computed: {
    timeline() {
      return this.Tracking.historical.slice().reverse();
    },
    propTracking() {
      let {
        id,
        price,
        currency,
        tracking_condition,
        assertiveness,
      } = this.Tracking.detail;
      let estatus = this.Tracking.estatus;
      let owner = this.Tracking.owner;
      return {
        id,
        price,
        currency,
        estatus,
        tracking_condition,
        assertiveness,
        owner,
      };
    },
  },

  methods: {
    loadTracking(cb) {
      const self = this;
      axios
        .get("/admin/tracking/" + self.propTrackingId)
        .then(function (response) {
          self.Tracking = response.data.data;
          cb();
        });
    },
  },
};
</script>

<style lang="sass" scoped>
.v-list-item__content
  padding: 0
</style>
