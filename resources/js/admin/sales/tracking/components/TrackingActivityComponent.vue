<template>
  <div>
    <v-card flat>
      <v-card-actions
        class="d-flex justify-space-around align-content-center flex-wrap"
      >
        <v-spacer />
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
          v-if="propTracking.estatus.key == 'activo' && (timeline.length >= 3 || propTracking.first_contact == 'Visita en Agencia') "
        >
          Venta Ganada <v-icon right>mdi-receipt</v-icon>
        </v-btn>
      </v-card-actions>
      <v-card-text>
        <historical-tracking
          :Tracking="propTracking"
          :timeline.sync="timeline"
        ></historical-tracking>
      </v-card-text>
    </v-card>
    <!-- tracking-historical-form-add -->
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
    <!-- tracking-historical-form-discard Venta Perdida-->
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

    <!-- tracking-historical-form-delivery -->
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

    <!-- tracking-historical-form-order Venta Ganada -->
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
  </div>
</template>
<script>
import DialogComponent from "@admin/components/DialogComponent.vue";
import HistoricalTracking from "@admin/sales/tracking/components/HistoricalTracking.vue";
import TrackingHistoricalFormAdd from "@admin/sales/tracking/TrackingHistoricalFormAdd.vue";
import TrackingHistoricalFormDiscard from "@admin/sales/tracking/TrackingHistoricalFormDiscard.vue";
import TrackingHistoricalFormOrder from "@admin/sales/tracking/TrackingHistoricalFormOrder.vue";
import TrackingHistoricalFormDelivery from "@admin/sales/tracking/TrackingHistoricalFormDelivery.vue";
export default {
  name: "TrackingActivity",
  props: {
    propTracking: {
      require: true,
      type: Object,
    },
    timeline: {
      type: Array,
    },
  },
  components: {
    HistoricalTracking,
    DialogComponent,
    TrackingHistoricalFormAdd,
    TrackingHistoricalFormDiscard,
    TrackingHistoricalFormOrder,
    TrackingHistoricalFormDelivery,
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
      Activities: 0,
    };
  },
  mounted() {
    // console.log(this.timeline.length, "prop");
    this.dialogMessages = false;
    this.dialogCalendar = false;
    this.dialogDiscard = false;
    this.dialogDelivery = false;
    this.dialogFormalize = false;
    this.Activities = this.timeline.length;
  },
  methods: {
    forceRerender() {
      this.componentKey += 1;
    },
  },
};
</script>
<style></style>
