<template>
  <v-card flat v-if="!!Tracking">
    <v-card-title>
      <span class="text-h4">
        Folio #{{ propTracking.id.toString().padStart(5, 0) }}
      </span>
      <v-chip
        class="ml-2 overline"
        :color="color[propTracking.estatus.key]"
        dark
      >
        {{ propTracking.estatus.title }}
      </v-chip>
      <v-tooltip bottom>
        <template v-slot:activator="{ on, attrs }">
          <v-progress-circular
            v-bind="attrs"
            v-on="on"
            :rotate="360"
            :size="56"
            :width="5"
            :value="propTracking.assertiveness * 100"
            :color="percenAssertiveness.color"
            class="ml-2 caption"
          >
            <span class="text--primary">
              {{ propTracking.assertiveness | percent }}
            </span>
          </v-progress-circular>
        </template>
        <span>{{ percenAssertiveness.text }}</span>
      </v-tooltip>
      <v-spacer />
      <v-btn
        color="primary"
        class="ml-2"
        @click="
          $router.push({
            name: 'tracking.edit',
            params: { propTrackingId: propTracking.id },
          })
        "
      >
        <v-icon left>mdi-pencil</v-icon> Modificar</v-btn
      >
    </v-card-title>
    <v-card-text>
      <v-row dense v-if="Tracking">
        <v-col cols="12" md="5" v-if="Tracking">
          <card-info-tracking :info="Tracking"></card-info-tracking>
        </v-col>
        <v-col cols="12" md="7">
          <v-tabs color="deep-purple accent-3" right>
            <v-tab>Actividades</v-tab>
            <v-tab>Cotizaciones</v-tab>
            <v-tab>Archivos</v-tab>
            <v-tab>Mensajes de Apoyo</v-tab>

            <v-tab-item>
              <tracking-activity
                v-if="Tracking.historical"
                :propTracking="propTracking"
                :timeline="timeline"
              ></tracking-activity>
            </v-tab-item>
            <v-tab-item>
              <v-container fluid>
                <tracking-quote-component
                  :tracking-id="propTrackingId"
                ></tracking-quote-component>
              </v-container>
            </v-tab-item>
            <v-tab-item>
              <v-toolbar>
                <v-file-input
                  v-model="files"
                  label="Archivo"
                  placeholder="Seleccionar Archivo"
                  persistent-placeholder
                  accept="image/png, image/jpeg, image/bmp,application/pdf"
                  :rules="[
                    (value) =>
                      !value ||
                      value.every((file) => file.size < 4000000) ||
                      'El Archivo debe ser Menor de 4 MB!',
                  ]"
                  show-size
                  multiple
                  hide-details
                  outlined
                  dense
                ></v-file-input>
                <v-btn
                  v-if="
                    files.length > 0 &&
                    files.every((file) => file.size < 4000000)
                  "
                  class="ml-2"
                  dark
                  @click="attachFiles"
                >
                  Subir Archivo
                </v-btn>
              </v-toolbar>
              <tracking-files :files="Files"></tracking-files>
            </v-tab-item>

            <v-tab-item>
              <message-tracking
                :seller-id="propTracking.owner"
                :tracking-id="propTracking.id"
              ></message-tracking>
            </v-tab-item>
          </v-tabs>
        </v-col>
      </v-row>
    </v-card-text>
  </v-card>
</template>

<script>
import CardInfoTracking from "@admin/sales/tracking/components/CardInfoTracking.vue";
import HistoricalTracking from "@admin/sales/tracking/components/HistoricalTracking.vue";
import MessageTracking from "@admin/sales/tracking/components/TrackingMessages.vue";
import Assertiveness from "@admin/sales/tracking/resources/assertiveness.json";
import TrackingActivity from "@admin/sales/tracking/components/TrackingActivityComponent.vue";
import TrackingQuoteComponent from "@admin/sales/tracking/components/TrackingQuoteComponent.vue";
import TrackingFiles from "./TrackingFiles.vue";

export default {
  components: {
    CardInfoTracking,
    HistoricalTracking,
    MessageTracking,
    TrackingActivity,
    TrackingQuoteComponent,
    TrackingFiles,
  },
  props: {
    propTrackingId: {
      type: [Number, String],
      required: true,
    },
  },
  data() {
    return {
      Tracking: null,
      tab: null,
      files: [],
      color: {
        activo: "primary",
        finalizado: "red",
        formalizado: "blue",
      },
    };
  },

  mounted() {
    const self = this;
    self.$store.commit("setBreadcrumbs", [
      { label: "Segumientos", to: { name: "tracking.list" } },
      { label: "Detalle Seguimiento", to: "" },
    ]);
    self.loadTracking(() => {});
    self.$eventBus.$on(["MESSAGE_ADDED", "TRACKING_REFRESH"], () => {
      self.loadTracking(() => {});
    });
  },
  computed: {
    timeline() {
      return this.Tracking.historical.slice().reverse();
    },
    Files() {
      return this.Tracking.files;
    },
    propTracking() {
      let {
        id,
        price,
        tracking_condition,
        assertiveness,
        invoice,
        date_invoice,
        date_won_sale,
        date_lost_sale,
        first_contact,
      } = this.Tracking.detail;
      let currency = this.Tracking.currency;
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
        invoice,
        date_invoice,
        date_won_sale,
        date_lost_sale,
        first_contact,
      };
    },
    percenAssertiveness() {
      return Assertiveness.find((item) => {
        return item.value == this.propTracking.assertiveness;
      });
    },
  },

  methods: {
    async loadTracking(cb) {
      const self = this;
      await axios
        .get("/admin/tracking/" + self.propTrackingId)
        .then(function (response) {
          self.Tracking = response.data.data;
          cb();
        });
    },
    async attachFiles() {
      const _this = this;
      const formData = new FormData();
      _this.files.forEach((item) => {
        formData.append("file[]", item);
      });

      await axios.post(
        `/admin/tracking-file/attach/${_this.propTrackingId}`,
        formData
      );
      _this.files = [];
      // _this.$eventBus.$emit("ORDERS_REFRESH");
      _this.loadTracking(() => {});
    },
  },
};
</script>

<style lang="sass" scoped>
.v-list-item__content
  padding: 0
</style>
