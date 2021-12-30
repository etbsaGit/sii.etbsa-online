<template>
  <v-card flat v-if="!!Tracking">
    <v-card-title>
      <span class="text-h4">
        Folio #{{ propTracking.id.toString().padStart(5, 0) }}
      </span>
      <v-spacer />
      <v-tooltip left>
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
      <v-chip
        class="ml-2 overline"
        :color="color[propTracking.estatus.key]"
        dark
      >
        {{ propTracking.estatus.title }}
      </v-chip>
      <v-btn
        v-if="$gate.allow('isGerente', 'tracking')"
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
        <v-col cols="12" md="7" v-if="Tracking.historical">
          <v-card flat>
            <v-card-actions
              class="d-flex justify-space-around align-content-center flex-wrap"
            >
              <v-btn
                dark
                small
                class="ma-1"
                color="green"
                @click="dialog = true"
                v-if="propTracking.estatus.key == 'activo'"
              >
                Agregar Actividad <v-icon right>mdi-plus-thick</v-icon>
              </v-btn>

              <v-btn
                dark
                small
                class="ma-1"
                color="purple"
                @click="dialogDelivery = true"
                v-if="propTracking.estatus.key == 'formalizado'"
              >
                Programar / Registrar Entrega
                <v-icon right>mdi-timeline-clock-outline</v-icon>
              </v-btn>

              <v-btn
                dark
                small
                class="ma-1"
                color="red"
                @click="dialogDiscard = true"
                v-if="propTracking.estatus.key == 'activo'"
              >
                Venta Perdida <v-icon right>mdi-trash-can</v-icon>
              </v-btn>
              <v-btn
                dark
                small
                class="ma-1"
                color="indigo"
                @click="dialogFormalize = true"
                v-if="propTracking.estatus.key != 'formalizado'"
              >
                Venta Ganada <v-icon right>mdi-receipt</v-icon>
              </v-btn>
              <v-btn
                dark
                small
                class="ma-1"
                color="blue"
                @click="dialogMessages = true"
              >
                Mensajes <v-icon right>mdi-forum</v-icon>
              </v-btn>
            </v-card-actions>
            <v-card-title>
              Actividades
            </v-card-title>
            <v-card-text>
              <historical-tracking
                :Tracking="propTracking"
                :timeline="timeline"
              ></historical-tracking>
            </v-card-text>
          </v-card>
        </v-col>
      </v-row>
    </v-card-text>

    <dialog-component
      :show="dialog"
      @close="(dialog = false), forceRerender()"
      :maxWidth="600"
      title="Agregar Proximo Seguimiento"
      closeable
    >
      <tracking-historical-form-add
        :key="componentKey"
        :trackingId="propTracking.id"
        :tracking="propTracking"
      ></tracking-historical-form-add>
    </dialog-component>

    <dialog-component
      :show="dialogDiscard"
      @close="(dialogDiscard = false), forceRerender()"
      :maxWidth="600"
      title="Descartar Seguimiento"
      closeable
    >
      <tracking-historical-form-discard
        :key="componentKey"
        :trackingId="propTracking.id"
        :tracking="propTracking"
      ></tracking-historical-form-discard>
    </dialog-component>

    <dialog-component
      :show="dialogDelivery"
      @close="(dialogDelivery = false), forceRerender()"
      :maxWidth="600"
      title="Programar o Resgitrar Entrega"
      closeable
    >
      <tracking-historical-form-delivery
        :key="componentKey"
        :trackingId="propTracking.id"
        :tracking="propTracking"
      ></tracking-historical-form-delivery>
    </dialog-component>

    <dialog-component
      :show="dialogFormalize"
      @close="dialogFormalize = false"
      :maxWidth="600"
      title="Formalizar Registrar Factura"
      closeable
    >
      <tracking-historical-form-order
        v-if="dialogFormalize"
        :trackingId="propTracking.id"
        :tracking="propTracking"
      >
      </tracking-historical-form-order>
    </dialog-component>

    <dialog-component
      :show="dialogMessages"
      @close="dialogMessages = false"
      :maxWidth="600"
      title=" Mensajes del Seguimiento"
      closeable
    >
      <message-tracking
        :seller-id="propTracking.owner"
        :tracking-id="propTracking.id"
      ></message-tracking>
    </dialog-component>
  </v-card>
</template>

<script>
import CardInfoTracking from "@admin/sales/tracking/components/CardInfoTracking.vue";
import HistoricalTracking from "@admin/sales/tracking/components/HistoricalTracking.vue";
import MessageTracking from "./components/TrackingMessages.vue";
import DialogComponent from "../../components/DialogComponent.vue";
import TrackingHistoricalFormAdd from "./TrackingHistoricalFormAdd.vue";
import TrackingHistoricalFormDiscard from "./TrackingHistoricalFormDiscard.vue";
import TrackingHistoricalFormOrder from "./TrackingHistoricalFormOrder.vue";
import Assertiveness from "@admin/sales/tracking/resources/assertiveness.json";
import TrackingHistoricalFormDelivery from "./TrackingHistoricalFormDelivery.vue";

export default {
  components: {
    CardInfoTracking,
    HistoricalTracking,
    MessageTracking,
    DialogComponent,
    TrackingHistoricalFormAdd,
    TrackingHistoricalFormDiscard,
    TrackingHistoricalFormOrder,
    TrackingHistoricalFormDelivery,
  },
  props: {
    propTrackingId: {
      required: true,
      type: [Number, String],
    },
  },
  data() {
    return {
      componentKey: 0,
      dialogMessages: false,
      dialogCalendar: false,
      dialogDiscard: false,
      dialogDelivery: false,
      dialogFormalize: false,
      dialog: false,
      form: {
        date_next_tracking: null,
      },
      formAdd: {},
      Tracking: null,
      tab: null,
      items: ["Seguimiento", "Mensajes"],
      color: {
        activo: "primary",
        finalizado: "red",
        formalizado: "blue",
      },
    };
  },

  mounted() {
    const self = this;
    self.loadTracking(() => {});
    self.$eventBus.$on(["MESSAGE_ADDED"], () => {
      self.loadTracking(() => {});
      self.dialogMessages = false;
      self.dialogCalendar = false;
      self.dialogDiscard = false;
      self.dialogDelivery = false;
      self.dialogFormalize = false;
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
        tracking_condition,
        assertiveness,
        invoice,
        date_invoice,
        date_won_sale,
        date_lost_sale,
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
      };
    },
    percenAssertiveness() {
      return Assertiveness.find((item) => {
        return item.value == this.propTracking.assertiveness;
      });
    },
  },

  methods: {
    forceRerender() {
      this.componentKey += 1;
    },
    async loadTracking(cb) {
      const self = this;
      await axios
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
