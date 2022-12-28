<template>
  <v-card flat :min-height="minHeight">
    <v-card-title>
      <!-- <v-icon left>mdi-file-document</v-icon> Acciones
      <v-spacer></v-spacer> -->
      Folio: {{Tracking.detail.id}}
      <v-spacer></v-spacer>
      <v-tooltip left>
        <template v-slot:activator="{ on, attrs }">
          <v-progress-circular
            v-bind="attrs"
            v-on="on"
            :rotate="360"
            :size="56"
            :width="5"
            :value="100"
            color="blue"
            class="ml-2 caption"
          >
            {{ 1 | percent }}
          </v-progress-circular>
        </template>
        <span>Texto</span>
      </v-tooltip>
      <v-chip class="ml-2 overline" color="orange">
        {{ Tracking.estatus.title }}
      </v-chip>
      <!-- <v-btn color="primary" text class="ml-2">
        <v-icon left>mdi-pencil</v-icon> Modificar
      </v-btn> -->
    </v-card-title>
    <v-card-text>
      <v-row dense>
        <v-col cols="12" md="5">
          <v-card min-height="50vh" color="grey lighten-3" flat>
            <card-info-tracking :info="Tracking"></card-info-tracking>
          </v-card>
        </v-col>
        <v-col cols="12" md="7" v-if="Tracking.historical">
          <v-card min-height="200" color="grey lighten-3">
            <v-toolbar flat class="transparent">
              <v-spacer />
              <div class="d-flex justify-end flex-wrap-reverse">
                <v-btn small class="ml-2" color="green" @click="dialog = true">
                  Agregar <v-icon right>mdi-plus-thick</v-icon>
                </v-btn>
                <v-btn
                  small
                  class="ml-2"
                  color="red"
                  @click="dialogDiscard = true"
                >
                  Descartar <v-icon right>mdi-trash-can</v-icon>
                </v-btn>
                <v-btn
                  small
                  class="ml-2"
                  color="indigo"
                  @click="dialogFormalize = true"
                >
                  Formalizar <v-icon right>mdi-receipt</v-icon>
                </v-btn>
                <v-btn
                  small
                  class="ml-2"
                  color="blue"
                  @click="dialogMessages = true"
                >
                  Mensajes <v-icon right>mdi-forum</v-icon>
                </v-btn>
              </div>
            </v-toolbar>
            <v-card-title>
              Historial del Seguimiento
              <v-spacer></v-spacer>
              <!-- <v-btn color="purple">
                <v-icon left>mdi-eye</v-icon> Ver Pedido
              </v-btn> -->
            </v-card-title>
            <v-card-text class="pl-0">
              <historical-tracking
                :Tracking="propTracking"
                :timeline="timeline"
              ></historical-tracking>
            </v-card-text>
          </v-card>
        </v-col>
      </v-row>
    </v-card-text>

    <v-dialog v-model="dialog" max-width="600px">
      <v-card>
        <v-card-title>
          Agregar Seguimiento
          <v-spacer></v-spacer>
          <v-btn icon @click="dialog = false">
            <v-icon>mdi-close</v-icon>
          </v-btn>
        </v-card-title>
        <v-divider></v-divider>
        <v-card-text class="pa-0" style="height: 100%;">
          <v-card min-height="200" color="grey lighten-3">
            <v-card-text style="min-height: 180px;">
              <v-row dense>
                <v-col cols="12" md="6">
                  <v-select
                    label="Tipo de Seguimiento:"
                    :rules="[(v) => !!v || 'Requerido']"
                    prepend-icon="mdi-chat-alert-outline"
                    hide-details
                    outlined
                    filled
                    dense
                  >
                  </v-select>
                </v-col>
                <v-col cols="12" md="6">
                  <v-dialog
                    ref="dialog"
                    v-model="dialogCalendar"
                    :return-value.sync="form.date_next_tracking"
                    persistent
                    transition="scale-transition"
                    width="324px"
                  >
                    <template v-slot:activator="{ on, attrs }">
                      <v-text-field
                        v-model="form.date_next_tracking"
                        label="Fecha Proximo Seguimiento"
                        prepend-icon="mdi-calendar"
                        :rules="[(v) => !!v || 'Requerido']"
                        v-bind="attrs"
                        v-on="on"
                        hide-details
                        readonly
                        outlined
                        filled
                        dense
                      ></v-text-field>
                    </template>
                    <v-date-picker
                      v-model="form.date_next_tracking"
                      scrollable
                      :min="new Date().toISOString().substr(0, 10)"
                    >
                      <v-spacer></v-spacer>
                      <v-btn
                        text
                        color="primary"
                        @click="dialogCalendar = false"
                      >
                        Cancel
                      </v-btn>
                      <v-btn
                        text
                        color="primary"
                        @click="$refs.dialog.save(form.date_next_tracking)"
                      >
                        OK
                      </v-btn>
                    </v-date-picker>
                  </v-dialog>
                </v-col>
                <v-col cols="12" md="6">
                  <v-select
                    label="Ultima Certeza:"
                    :rules="[(v) => !!v || 'Requerido']"
                    prepend-icon="mdi-circle-slice-2"
                    hide-details
                    outlined
                    filled
                    dense
                  >
                  </v-select>
                </v-col>

                <v-col cols="12" md="6">
                  <v-text-field
                    label="Ultimo precio a tratar"
                    :rules="[(v) => !!v || 'Requerido']"
                    prepend-icon="mdi-cash-usd-outline"
                    type="number"
                    hide-details
                    outlined
                    filled
                    dense
                  >
                  </v-text-field>
                </v-col>
                <v-col cols="12">
                  <v-textarea
                    outlined
                    label="Comentario"
                    placeholder="Escribir el detalle del seguimiento"
                    persistent-placeholder
                  ></v-textarea>
                </v-col>
              </v-row>
            </v-card-text>
            <v-card-actions>
              <v-spacer />
              <v-btn color="red">Cancelar</v-btn>
              <v-btn color="green" text>Agregar</v-btn>
            </v-card-actions>
          </v-card>
        </v-card-text>
      </v-card>
    </v-dialog>

    <v-dialog v-model="dialogDiscard" max-width="600px">
      <v-card>
        <v-card-title>
          Descartar Seguimiento
          <v-spacer></v-spacer>
          <v-btn icon @click="dialogDiscard = false">
            <v-icon>mdi-close</v-icon>
          </v-btn>
        </v-card-title>
        <v-divider></v-divider>
        <v-card-text class="pa-0" style="height: 100%;">
          <v-card min-height="200" color="grey lighten-3">
            <v-card-text style="min-height: 180px;">
              <v-row dense>
                <v-col cols="12">
                  <v-textarea
                    outlined
                    label="Comentario"
                    placeholder="Escribir el detalle del seguimiento"
                    persistent-placeholder
                  ></v-textarea>
                </v-col>
              </v-row>
            </v-card-text>
            <v-card-actions>
              <v-spacer />
              <v-btn color="red">Cancelar</v-btn>
              <v-btn color="green" text>Agregar</v-btn>
            </v-card-actions>
          </v-card>
        </v-card-text>
      </v-card>
    </v-dialog>

    <v-dialog v-model="dialogFormalize" max-width="600px">
      <v-card>
        <v-card-title>
          Formalizar Seguimiento (Crear Pedido)
          <v-spacer></v-spacer>
          <v-btn icon @click="dialogFormalize = false">
            <v-icon>mdi-close</v-icon>
          </v-btn>
        </v-card-title>
        <v-divider></v-divider>
        <v-card-text class="pa-0" style="height: 100%;">
          <v-card min-height="200" color="grey lighten-3">
            <v-card-text style="min-height: 180px;">
              <v-row dense>
                <v-col cols="12">
                  <v-autocomplete
                    label="Cliente"
                    placeholder="Seleccionar el Cliente a quien correponde el Pedido"
                    persistent-placeholder
                    hint="buscar por rfc,nombre,telefono,email"
                    persistent-hint
                    outlined
                    filled
                    append-outer-icon="mdi-account-plus"
                  ></v-autocomplete>
                </v-col>
                <v-col cols="12" md="6">
                  <v-select
                    label="Tramite"
                    :items="[
                      'Contado',
                      'Credito ETBSA',
                      'Credito FINETBSA',
                      'Credito JDF',
                    ]"
                    placeholder="Seleccioanr el Tipo de Tramite"
                    hint="es requerido"
                    outlined
                    filled
                    dense
                  ></v-select>
                </v-col>
                <v-col cols="12" md="6">
                  <v-text-field
                    label="Precio tratado"
                    outlined
                    dense
                  ></v-text-field>
                </v-col>
                <v-col cols="12">
                  <v-textarea
                    outlined
                    label="Comentario del Seguimiento"
                    placeholder="Escribir el detalle del seguimiento"
                    persistent-placeholder
                  ></v-textarea>
                </v-col>
              </v-row>
              <small>
                *El seguimiento pasara a Formalizado, y se creara un pedido al
                cliente correspondiente al cual se le Facturara
              </small>
            </v-card-text>
            <v-card-actions>
              <v-spacer />
              <v-btn color="red">Cancelar</v-btn>
              <v-btn color="green" text>Crear Pedido</v-btn>
            </v-card-actions>
          </v-card>
        </v-card-text>
      </v-card>
    </v-dialog>

    <v-dialog v-model="dialogMessages" scrollable max-width="600px">
      <v-card>
        <v-card-title>
          Mensajes del Seguimiento
          <v-spacer></v-spacer>
          <v-btn icon @click="dialogMessages = false">
            <v-icon>mdi-close</v-icon>
          </v-btn>
        </v-card-title>
        <v-divider></v-divider>
        <v-card-text class="pa-0" style="height: 100%;">
          <tracking-messages
            :seller-id="Tracking.owner"
            :tracking-id="Tracking.detail.id"
          ></tracking-messages>
        </v-card-text>
      </v-card>
    </v-dialog>
  </v-card>
</template>

<script>
import CardInfoTracking from "@admin/sales/tracking/components/CardInfoTracking.vue";
import HistoricalTracking from "@admin/sales/tracking/components/HistoricalTracking.vue";
import TrackingMessages from "./components/TrackingMessages.vue";

export default {
  components: { CardInfoTracking, HistoricalTracking, TrackingMessages },
  props: {
    propTrackingId: {
      required: true,
      type: [Number, String],
    },
  },
  data() {
    return {
      dialogMessages: false,
      dialogCalendar: false,
      dialogDiscard: false,
      dialogFormalize: false,
      dialog: false,
      Tracking: null,
      tab: null,
      items: ["Seguimiento", "Mensajes"],
      form: {
        date_next_tracking: null,
      },
    };
  },
  mounted() {
    const self = this;
    self.loadTracking(() => {});
    self.$eventBus.$on(["MESSAGE_ADDED"], () => {
      self.loadTracking(() => {});
    });
  },
  computed: {
    minHeight() {
      const height = this.$vuetify.breakpoint.mobile ? "100vh" : "100vh";
      return `calc(${height} - ${this.$vuetify.application.top}px)`;
    },
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
