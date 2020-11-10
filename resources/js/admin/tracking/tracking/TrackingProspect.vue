<template>
  <v-row v-if="tracking" justify="space-between">
    <v-col xs="12" sm="12" md="5" lg="5" xl="6">
      <template>
        <v-card class="mx-auto my-auto" dark>
          <v-card-title v-if="tracking.id">
            Seguimiento
            <v-spacer></v-spacer>
            Folio: {{ tracking.id.toString().padStart(5, 0) }}
          </v-card-title>
          <v-card-subtitle class="py-0">
            <v-row align="center">
              <v-chip class="ml-4" :color="getColor(tracking.estatus.key)">
                {{ tracking.estatus.title }}
              </v-chip>
              <v-spacer />
              <v-tooltip right>
                <template v-slot:activator="{ on, attrs }">
                  <v-btn
                    text
                    v-if="tracking.date_next_tracking"
                    v-bind="attrs"
                    v-on="on"
                  >
                    {{
                      $appFormatters.formatTimeDiffNow(
                        tracking.date_next_tracking,
                        "days"
                      )
                    }}
                    Dia(s) prox. Seguimiento
                  </v-btn>
                </template>
                <span>{{ tracking.date_next_tracking }}</span>
              </v-tooltip>
            </v-row>
            <v-btn small text class="mt-1">
              Contacto por: {{ tracking.first_contact }}
            </v-btn>
          </v-card-subtitle>
          <v-divider></v-divider>
          <v-list two-line dense>
            <v-list-item>
              <v-list-item-icon>
                <v-icon color="indigo">mdi-shape</v-icon>
              </v-list-item-icon>

              <v-list-item-content>
                <v-list-item-subtitle>Titulo Seguimiento</v-list-item-subtitle>
                <v-list-item-content>{{ tracking.title }}</v-list-item-content>
              </v-list-item-content>
              <v-list-item-content>
                <v-list-item-subtitle>Referencia</v-list-item-subtitle>
                <v-list-item-content>{{
                  tracking.reference
                }}</v-list-item-content>
              </v-list-item-content>
            </v-list-item>

            <v-list-item>
              <v-list-item-action></v-list-item-action>

              <v-list-item-content>
                <v-list-item-subtitle>Descripcion</v-list-item-subtitle>
                <v-list-item-content>{{
                  tracking.description_topic
                }}</v-list-item-content>
              </v-list-item-content>
            </v-list-item>

            <v-divider inset></v-divider>
            <v-list-item>
              <v-list-item-icon>
                <v-icon color="indigo">mdi-account</v-icon>
              </v-list-item-icon>

              <v-list-item-content>
                <v-list-item-subtitle>Nombre Prospecto</v-list-item-subtitle>
                <v-list-item-title>{{
                  tracking.prospect.full_name
                }}</v-list-item-title>
              </v-list-item-content>
              <v-list-item-content>
                <v-list-item-subtitle>Telefono</v-list-item-subtitle>
                <v-list-item-title>{{
                  tracking.prospect.phone || "s/r"
                }}</v-list-item-title>
              </v-list-item-content>
            </v-list-item>

            <v-divider inset></v-divider>
            <v-list-item>
              <v-list-item-icon>
                <v-icon color="indigo">mdi-store</v-icon>
              </v-list-item-icon>

              <v-list-item-content>
                <v-list-item-subtitle>Agencia:</v-list-item-subtitle>
                <v-list-item-title>{{
                  tracking.agency.title
                }}</v-list-item-title>
              </v-list-item-content>
              <v-list-item-content>
                <v-list-item-subtitle>Departamento:</v-list-item-subtitle>
                <v-list-item-title>{{
                  tracking.department.title
                }}</v-list-item-title>
              </v-list-item-content>
            </v-list-item>

            <v-divider inset></v-divider>

            <v-list-item>
              <v-list-item-icon>
                <v-icon color="indigo">mdi-account</v-icon>
              </v-list-item-icon>

              <v-list-item-content>
                <v-list-item-subtitle>
                  Atendido por:
                  <v-dialog
                    ref="dialogReasign"
                    v-model="dialogSeller"
                    :return-value.sync="now"
                    persistent
                    transition="scale-transition"
                    width="290px"
                  >
                    <template v-slot:activator="{ on, attrs }">
                      <v-btn
                        v-if="$gate.allow('assignSeller', 'tracking')"
                        icon
                        x-small
                        text
                        v-bind="attrs"
                        v-on="on"
                        @click="loadSellers()"
                      >
                        <v-icon>
                          mdi-pencil
                        </v-icon>
                      </v-btn>
                    </template>
                    <v-card>
                      <v-card-title class="accent"
                        >Asignar a otro Vendedor</v-card-title
                      >
                      <v-container>
                        <v-autocomplete
                          v-model="seller"
                          :items="options.sellers"
                          item-text="name"
                          item-value="id"
                          label="VENDEDORES DISPONIBLES:"
                          placeholder="Vendedor sugerido."
                        ></v-autocomplete>
                      </v-container>
                      <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn
                          text
                          color="primary"
                          @click="dialogSeller = false"
                        >
                          Cancel
                        </v-btn>
                        <v-btn
                          text
                          color="primary"
                          @click="reasigSeller(), (dialogSeller = false)"
                        >
                          OK
                        </v-btn>
                      </v-card-actions>
                    </v-card>
                  </v-dialog>
                </v-list-item-subtitle>
                <v-list-item-title>{{
                  tracking.attended.name
                }}</v-list-item-title>
              </v-list-item-content>
              <v-list-item-content>
                <v-list-item-subtitle>Asignado por:</v-list-item-subtitle>
                <v-list-item-title>{{
                  tracking.assigned.name
                }}</v-list-item-title>
              </v-list-item-content>
              <v-list-item-content>
                <v-list-item-subtitle>Registrado por:</v-list-item-subtitle>
                <v-list-item-title>{{
                  tracking.registered.name
                }}</v-list-item-title>
              </v-list-item-content>
            </v-list-item>
          </v-list>
        </v-card>
      </template>
    </v-col>
    <v-col>
      <v-timeline dense clipped>
        <v-timeline-item
          fill-dot
          class="white--text mb-6"
          color="orange"
          large
          v-if="tracking.estatus.key == 'activo'"
        >
          <template v-slot:icon>
            <v-tooltip top>
              <template v-slot:activator="{ on, attrs }">
                <span v-bind="attrs" v-on="on">{{ initials.init }}</span>
              </template>
              <span>{{ initials.full_name }}</span>
            </v-tooltip>
          </template>
          <v-radio-group v-model="row" row hide-details class="my-2">
            <v-radio label="Proximo Seguimiento" :value="'activo'"></v-radio>
            <v-radio
              label="Finalizar Seguimiento"
              :value="'finalizado'"
            ></v-radio>
          </v-radio-group>
          <v-dialog
            ref="dialog"
            v-model="nowMenu"
            :return-value.sync="now"
            persistent
            transition="scale-transition"
            width="290px"
          >
            <template v-slot:activator="{ on, attrs }">
              <v-text-field
                v-show="row == 'activo'"
                v-model="now"
                class="my-3"
                label="Fecha Proximo Seguimiento"
                prepend-icon="mdi-calendar"
                dense
                readonly
                outlined
                hide-details
                v-bind="attrs"
                v-on="on"
              ></v-text-field>
            </template>
            <v-date-picker
              v-model="now"
              scrollable
              :min="new Date().toISOString().substr(0, 10)"
            >
              <v-spacer></v-spacer>
              <v-btn text color="primary" @click="nowMenu = false">
                Cancel
              </v-btn>
              <v-btn text color="primary" @click="$refs.dialog.save(now)">
                OK
              </v-btn>
            </v-date-picker>
          </v-dialog>
          <v-switch
            v-show="row == 'finalizado'"
            v-model="isFormalized"
            label="Formalizo en Venta"
          ></v-switch>
          <v-row align="center">
            <v-col cols="12" md="6">
              <v-text-field
                v-model="lastPrice"
                hide-details
                label="Ultimo Precio a Tratar:"
                outlined
                dense
                :rules="[(v) => !!v || 'Requerido']"
                class="mb-2"
                reverse
                type="number"
                append-icon="mdi-currency-usd"
              >
                <!-- :prefix="tracking.currency" -->
                <template v-slot:prepend>
                  <v-btn text @click="changeCurrency()">
                    {{ lastCurrency }}
                  </v-btn>
                </template>
              </v-text-field>
            </v-col>
            <v-col cols="12" md="6">
              <v-radio-group v-model="radioContacted" row class="mt-0">
                <v-radio v-for="n in radios" :key="n" :value="n" :label="n">
                </v-radio>
              </v-radio-group>
            </v-col>
          </v-row>
          <v-textarea
            v-model="input"
            hide-details
            label="Escribir Comentario..."
            solo
            outlined
            :rules="[(v) => !!v || 'Requerido']"
          >
            <template v-slot:append>
              <v-container fluid class="pa-0">
                <v-col cols="12" class="pa-0">
                  <v-btn class="mx-0" depressed @click="comment" block>
                    Enviar
                  </v-btn>
                </v-col>
              </v-container>
            </template>
          </v-textarea>
        </v-timeline-item>

        <v-slide-x-transition group>
          <v-timeline-item
            v-for="event in timeline"
            :key="event.id"
            class="mb-2"
            color="pink"
            small
          >
            <v-row justify="space-between align-center" class="overline">
              <v-col cols="8" v-text="event.message" class="blue-grey lighten-5 elevation-2"></v-col>
              <v-col class="text-right" cols="4">
                {{
                  $appFormatters.formatDate(
                    event.created_at,
                    "DD MMM YYYY hh:mm"
                  )
                }}
                <div class="caption">{{ event.last_price | money() }} {{ event.last_currency }}</div>
                <div class="caption">{{ event.user.name }}</div>
                <div class="caption blue--text">{{ event.type_contacted }}</div>
              </v-col>
            </v-row>
          </v-timeline-item>
        </v-slide-x-transition>
      </v-timeline>
    </v-col>
  </v-row>
</template>

<script>
export default {
  props: {
    propTrackingId: {
      required: false,
      type: [Number, String],
    },
  },
  data: () => ({
    tracking: null,
    historical: [],
    input: null,
    nonce: 0,
    row: "activo",
    nowMenu: false,
    dialogSeller: false,
    now: null,
    isFormalized: false,
    seller: null,
    lastPrice: 0,
    lastCurrency: "MXN",
    radioContacted: "Llamada",
    radios: ["Llamada", "Visita", "En piso"],
    options: {
      sellers: [],
    },
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
      return this.tracking.historical.slice().reverse();
    },
    initials() {
      var names = window.LSK_APP.AUTH_USER.name.split(" "),
        initials = names[0].substring(0, 1).toUpperCase();

      if (names.length > 1) {
        initials += names[names.length - 1].substring(0, 1).toUpperCase();
      }
      return {
        init: initials,
        full_name: window.LSK_APP.AUTH_USER.name,
      };
    },
  },

  methods: {
    comment() {
      const self = this;
      if (self.row == "activo") {
        if (!self.now)
          return self.$store.commit("showSnackbar", {
            message: "Fecha Proximo Seguimiento es necesaria",
            color: "warning",
            duration: 3000,
          });
      } else {
        self.now = null;
      }

      let payload = {
        estatus: self.isFormalized ? "formalizado" : self.row,
        date_next_tracking: self.now,
        message: self.input,
        last_price: self.lastPrice,
        last_currency: self.lastCurrency,
        type_contacted: self.radioContacted,
      };

      axios
        .put("/admin/tracking/" + self.propTrackingId, payload)
        .then(function(response) {
          self.$eventBus.$emit("MESSAGE_ADDED");
        });

      this.input = null;
    },

    loadTracking(cb) {
      const self = this;

      // reset first
      self.groups = [];

      axios
        .get("/admin/tracking/" + self.propTrackingId)
        .then(function(response) {
          let Tracking = response.data.data;
          self.tracking = Tracking;
          self.lastPrice = Tracking.price;
          self.lastCurrency = Tracking.currency;

          cb();
        });
    },
    getColor(value) {
      if (value == "finalizado") return "red";
      else if (value == "formalizado") return "blue";
      else return "primary";
    },
    reasigSeller() {
      const self = this;
      let payload = {
        attended_by: self.seller,
      };

      axios
        .put("/admin/tracking/assignSeller/" + self.propTrackingId, payload)
        .then(function(response) {
          self.$store.commit("showSnackbar", {
            message: response.data.message,
            color: "success",
            duration: 3000,
          });
          self.$eventBus.$emit("MESSAGE_ADDED");
          cb();
        });
    },
    loadSellers(cb) {
      const self = this;
      self.$store.commit("showLoader");
      let department = [self.tracking.department_id];
      self.seller = self.tracking.attended_by;

      let params = {
        agency: self.agency,
        seller_type_id: department.join(","),
        paginate: "no",
      };
      axios.get("/admin/sellers", { params: params }).then(function(response) {
        self.options.sellers = response.data.data;
        self.$store.commit("hideLoader");
      });

      // return seller;
    },
    changeCurrency() {
      const self = this;
      if (self.lastCurrency == "MXN") {
        self.lastCurrency = "USD";
      } else {
        self.lastCurrency = "MXN";
      }
    },
  },
};
</script>
