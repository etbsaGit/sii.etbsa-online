<template>
  <v-container fluid>
    <v-row v-if="Tracking" no-gutters>
      <v-col cols="12" md="5" v-if="Tracking">
        <card-info-tracking :info="Tracking"></card-info-tracking>
      </v-col>
      <v-col cols="12" md="7" v-if="Tracking.historical">
        <historical-tracking
          :Tracking="propTracking"
          :timeline="timeline"
        ></historical-tracking>
      </v-col>
    </v-row>
  </v-container>
</template>

<script>
import CardInfoTracking from '@admin/tracking/tracking/components/CardInfoTracking.vue';
import HistoricalTracking from '@admin/tracking/tracking/components/HistoricalTracking.vue';

export default {
  components: { CardInfoTracking, HistoricalTracking },
  props: {
    propTrackingId: {
      required: true,
      type: [Number, String],
    },
  },
  data: () => ({
    Tracking: null,
  }),

  mounted() {
    const self = this;
    self.loadTracking(() => {});
    self.$eventBus.$on(['MESSAGE_ADDED'], () => {
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
      return {
        id,
        price,
        currency,
        estatus,
        tracking_condition,
        assertiveness,
      };
    },
  },

  methods: {
    loadTracking(cb) {
      const self = this;
      axios
        .get('/admin/tracking/' + self.propTrackingId)
        .then(function(response) {
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
