<template>
  <v-card flat min-height="100vh">
    <v-app-bar flat dense>
      <v-spacer></v-spacer>
      <template v-if="item.estatus_key == 'pendiente'">
        <v-btn color="green" small class="ml-2" @click="actionAutorize()">
          <v-icon left>mdi-check-all</v-icon> Autorizar
        </v-btn>
        <v-btn color="red" small class="ml-2" @click="actionDenied()">
          <v-icon left>mdi-close</v-icon> Denegar
        </v-btn>
      </template>
      <template v-else-if="item.estatus_key == 'autorizado'">
        <v-btn color="purple" small class="ml-2" @click="actionDisperse()">
          <v-icon left>mdi-cash-refund</v-icon> Dispersar
        </v-btn>
      </template>
      <template
        v-else-if="
          item.estatus_key == 'flotilla.dispersado' ||
          (item.estatus_key == 'flotilla.despachado' &&
            item.ticket_balance > item.liters_cost)
        "
      >
        <v-btn
          color="orange"
          small
          class="ml-2"
          @click="(dialogConfirm = true), (formTicket = {})"
        >
          <v-icon left>mdi-ticket-confirmation</v-icon> Registrar ticket
        </v-btn>
      </template>
    </v-app-bar>
    <v-container fluid>
      <v-row dense>
        <v-col cols="12" md="4">
          <v-card min-height="200" color="grey lighten-4">
            <v-card-title>
              Detalle de la Solicitud
              <v-spacer></v-spacer>
              <!-- <v-btn icon>
                <v-icon color="green">mdi-pencil</v-icon>
              </v-btn> -->
            </v-card-title>
            <v-col cols="12" class="display-2 text-center my-4">
              <v-edit-dialog
                v-if="
                  item.estatus_key == 'pendiente' ||
                  item.etatus_key == 'rechazada'
                "
                :return-value.sync="item.fuel_liters"
                large
                persistent
                save-text="Cambiar"
                @save="save"
              >
                <v-hover v-slot="{ hover }">
                  <span class="pa-2" :class="`elevation-${hover ? 5 : 2}`">
                    {{ item.fuel_liters }} Lts
                  </span>
                </v-hover>
                <template v-slot:input>
                  <div class="mt-4 title">
                    Cantidad de Litros
                  </div>
                  <v-text-field v-model="item.fuel_liters" type="number">
                  </v-text-field>
                </template>
              </v-edit-dialog>
              <span v-else class="pa-2"> {{ item.fuel_liters }} Lts </span>
            </v-col>
            <v-list class="transparent elevation-0" dense>
              <v-list-item class="caption text-uppercase">
                <v-list-item-title>Con Cargo a:</v-list-item-title>
                <v-list-item-subtitle class="text-right text-no-wrap caption">
                  {{ item.agency }} - {{ item.department }}
                </v-list-item-subtitle>
              </v-list-item>
              <v-list-item class="overline">
                <v-list-item-title>Ticket Card:</v-list-item-title>
                <v-list-item-subtitle class="text-right">
                  #{{ item.ticket_card }}
                </v-list-item-subtitle>
              </v-list-item>
              <v-list-item class="overline">
                <v-list-item-title>Costo/Litro:</v-list-item-title>
                <v-list-item-subtitle class="text-right">
                  {{ item.liters_cost | money }}
                </v-list-item-subtitle>
              </v-list-item>
              <v-list-item class="overline">
                <v-list-item-title>Importe a Cargar:</v-list-item-title>
                <v-list-item-subtitle class="text-right font-weight-bold">
                  {{ AmountDispersal | money }}
                </v-list-item-subtitle>
              </v-list-item>
              <v-list-item class="overline">
                <v-list-item-title>Fecha a Dispersar:</v-list-item-title>
                <v-list-item-subtitle class="text-right">
                  {{
                    $appFormatters.formatDate(
                      item.fecha_dispersar,
                      "DD MMM YYYY"
                    )
                  }}
                </v-list-item-subtitle>
              </v-list-item>
              <v-list-item class="overline">
                <v-list-item-title>Fecha de solicitud:</v-list-item-title>
                <v-list-item-subtitle class="text-right">
                  {{
                    $appFormatters.formatDate(
                      item.fecha_solicitud,
                      "DD MMM YYYY"
                    )
                  }}
                </v-list-item-subtitle>
              </v-list-item>
              <v-list-item class="caption text-uppercase">
                <v-list-item-title>
                  Solicitante:
                </v-list-item-title>
                <v-list-item-subtitle class="text-right caption">
                  {{ item.solicitante }}
                </v-list-item-subtitle>
              </v-list-item>

              <v-expansion-panels flat>
                <v-expansion-panel class="transparent">
                  <v-expansion-panel-header class="px-4 py-0 overline">
                    <template v-slot:default="{ open }">
                      <v-row no-gutters dense>
                        <v-col cols="4">
                          Motivo:
                        </v-col>
                        <v-col cols="8">
                          <v-fade-transition leave-absolute>
                            <span v-if="open" key="0">
                              {{ item.motivo }}
                            </span>
                          </v-fade-transition>
                        </v-col>
                      </v-row>
                    </template>
                  </v-expansion-panel-header>
                  <v-expansion-panel-content class="text-uppercase">
                    <p>{{ item.motivo_descripcion }}</p>
                    <v-list-item
                      v-if="item.motivo_detalle.origin"
                      class="caption text-uppercase"
                    >
                      <v-list-item-title>
                        Origen:
                      </v-list-item-title>
                      <v-list-item-subtitle class="text-right caption">
                        {{ item.motivo_detalle.origin }}
                      </v-list-item-subtitle>
                    </v-list-item>
                    <v-list-item
                      v-if="item.motivo_detalle.destination"
                      class="caption text-uppercase"
                    >
                      <v-list-item-title>
                        Destino:
                      </v-list-item-title>
                      <v-list-item-subtitle class="text-right caption">
                        {{ item.motivo_detalle.destination }}
                      </v-list-item-subtitle>
                    </v-list-item>
                    <v-list-item
                      v-if="item.motivo_detalle.reference"
                      class="caption text-uppercase"
                    >
                      <v-list-item-title>
                        Referencia Serv.:
                      </v-list-item-title>
                      <v-list-item-subtitle class="text-right caption">
                        {{ item.motivo_detalle.reference }}
                      </v-list-item-subtitle>
                    </v-list-item>
                  </v-expansion-panel-content>
                </v-expansion-panel>
              </v-expansion-panels>
            </v-list>
          </v-card>
        </v-col>
        <v-col cols="12" md="4">
          <v-card min-height="200" color="grey lighten-4">
            <v-card-title>Detalle de la Unidad</v-card-title>
            <v-row align="start" dense>
              <v-col cols="6">
                <v-list class="transparent">
                  <v-list-item>
                    <v-list-item-icon>
                      <v-icon>mdi-car-coolant-level</v-icon>
                    </v-list-item-icon>
                    <v-list-item-subtitle>
                      {{ item.fuel }}
                    </v-list-item-subtitle>
                  </v-list-item>
                  <v-list-item>
                    <v-list-item-icon>
                      <v-icon>
                        {{ !item.drum_dispersal ? "mdi-car" : "mdi-fuel" }}
                      </v-icon>
                    </v-list-item-icon>
                    <v-list-item-subtitle>
                      {{ item.vehiculo.modelo }}
                    </v-list-item-subtitle>
                  </v-list-item>
                  <v-list-item>
                    <v-list-item-icon>
                      <v-icon>
                        mdi-id-card
                      </v-icon>
                    </v-list-item-icon>
                    <v-list-item-subtitle class="text-wrap">
                      {{ item.vehiculo.matricula }}
                      <!-- <span class="caption">{{ item.vehiculo.serie }}</span> -->
                    </v-list-item-subtitle>
                  </v-list-item>
                </v-list>
              </v-col>
              <template v-if="!item.drum_dispersal">
                <v-col cols="6">
                  <v-list class="transparent">
                    <v-list-item>
                      <v-list-item-icon>
                        <v-icon>mdi-car-cruise-control</v-icon>
                      </v-list-item-icon>
                      <v-list-item-subtitle class="text-wrap">
                        {{ item.mileage_last }} Km Anterior
                      </v-list-item-subtitle>
                    </v-list-item>
                    <v-list-item>
                      <v-list-item-icon>
                        <v-icon>mdi-car-cruise-control</v-icon>
                      </v-list-item-icon>
                      <v-list-item-subtitle class="text-wrap">
                        {{ item.mileage_actual }} Km Actual
                      </v-list-item-subtitle>
                    </v-list-item>
                    <v-list-item>
                      <v-list-item-icon>
                        <v-icon>mdi-car-traction-control</v-icon>
                      </v-list-item-icon>
                      <v-list-item-subtitle class="text-wrap">
                        {{ item.vehiculo.rendimiento }} Km/Lt
                      </v-list-item-subtitle>
                    </v-list-item>
                  </v-list>
                </v-col>
                <v-col cols="12">
                  <v-subheader>ODOMETRO COMBUSTIBLE</v-subheader>
                  <gauge
                    :value="item.vehiculo.odometro"
                    :min="0"
                    :max="100"
                    :fontSize="'16px'"
                    :padding="false"
                    :svgStyle="{
                      maxWidth: 180,
                      maxHeight: 125,
                    }"
                    :activeFill="'#1976D2'"
                    :pointerStroke="'#F44336'"
                    :pivotStroke="'#E91E63'"
                    :pivotFill="'#4CAF50'"
                    :maxThresholdFill="'#4CAF50'"
                    :minThresholdFill="'#F44336'"
                    :maxThreshold="80"
                    :minThreshold="25"
                    :minLabel="'E'"
                    :maxLabel="'F'"
                    :unit="'%'"
                    :unitOnArc="false"
                  />
                </v-col>
              </template>
              <template v-else>
                <v-col cols="12" class="text-center">
                  <v-icon size="150" color="red darken-3">mdi-fuel</v-icon>
                </v-col>
              </template>
            </v-row>
          </v-card>
        </v-col>
        <v-col cols="12" md="4">
          <v-card min-height="200" color="grey lighten-4">
            <v-card-title class="pb-0">Historico de Tickets</v-card-title>
            <v-row dense>
              <v-col cols="6">
                <v-list class="transparent">
                  <v-subheader>Saldo en Tarjeta</v-subheader>
                  <v-list-item>
                    <v-list-item-icon>
                      <v-icon>mdi-cash-usd-outline</v-icon>
                    </v-list-item-icon>
                    <v-list-item-title>
                      {{ item.ticket_balance | money }}
                    </v-list-item-title>
                  </v-list-item>
                </v-list>
              </v-col>
              <v-col cols="6">
                <v-list class="transparent">
                  <v-subheader>Saldo Gastado</v-subheader>
                  <v-list-item>
                    <v-list-item-icon>
                      <v-icon>mdi-cash-usd-outline</v-icon>
                    </v-list-item-icon>
                    <v-list-item-title>
                      {{ item.dispatched_amount | money }}
                    </v-list-item-title>
                  </v-list-item>
                </v-list>
              </v-col>
            </v-row>

            <app-widget
              icon-title="mdi-gas-station-outline"
              title="Historico de Tickets"
              header-hide
              padding-hide
            >
              <v-simple-table
                slot="widget-content"
                class="text-uppercase elevation-0 transparent"
                dense
              >
                <template v-slot:default>
                  <thead>
                    <tr>
                      <th class="text-left">
                        Ticket ID
                      </th>
                      <th class="text-left">
                        Lts
                      </th>
                      <th class="text-left">
                        Costo / Lts
                      </th>
                      <th class="text-left">
                        Subtotal
                      </th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="(ticket, index) in item.tickets" :key="index">
                      <td class="caption">{{ ticket.id }}</td>
                      <td class="caption">{{ ticket.liters }}</td>
                      <td class="caption">
                        {{ ticket.cost_per_liter | money }}
                      </td>
                      <td class="caption">
                        {{ (ticket.cost_per_liter * ticket.liters) | money }}
                      </td>
                    </tr>
                  </tbody>
                </template>
              </v-simple-table>
            </app-widget>
          </v-card>
        </v-col>
      </v-row>
    </v-container>
    <dialog-confirm
      :show="dialogConfirm"
      :max-width="500"
      @close="(dialogConfirm = false), (formTicket = {})"
      @agree="actionDispatched()"
    >
      <template #title>
        Registrar Ticket y Odometro
      </template>
      <template #body>
        <v-card-text>
          <v-form v-model="validTicket" ref="FormTicket" lazy-validation>
            <v-row dense>
              <v-col cols="4">
                <v-text-field
                  v-model="formTicket.id"
                  label="id"
                  :rules="[(v) => !!v || 'Es Requerido']"
                ></v-text-field>
              </v-col>
              <v-col cols="4">
                <v-text-field
                  v-model="formTicket.liters"
                  label="Litros"
                  suffix="Lts"
                  :rules="[(v) => !!v || 'Es Requerido']"
                ></v-text-field>
              </v-col>
              <v-col cols="4">
                <v-text-field
                  v-model="formTicket.cost_per_liter"
                  label="Costo por Litro"
                  prefix="$"
                  filled
                  :rules="[(v) => !!v || 'Es Requerido']"
                ></v-text-field>
              </v-col>
              <v-col cols="6">
                <v-text-field
                  v-model="formTicket.mileage_last"
                  label="Kilometraje Anterior"
                  suffix="Kms"
                  filled
                ></v-text-field>
              </v-col>
              <v-col cols="6">
                <v-text-field
                  v-model="formTicket.mileage_actual"
                  label="Kilometraje Actual"
                  suffix="Kms"
                  filled
                  :rules="[(v) => !!v || 'Es Requerido']"
                ></v-text-field>
              </v-col>
              <v-col cols="12">
                <v-subheader>ODOMETRO COMBUSTIBLE</v-subheader>
                <gauge
                  :value="item.odometro"
                  :min="0"
                  :max="100"
                  :fontSize="'16px'"
                  :padding="false"
                  :svgStyle="{
                    maxWidth: 180,
                    maxHeight: 125,
                  }"
                  :activeFill="'#1976D2'"
                  :pointerStroke="'#F44336'"
                  :pivotStroke="'#E91E63'"
                  :pivotFill="'#4CAF50'"
                  :maxThresholdFill="'#4CAF50'"
                  :minThresholdFill="'#F44336'"
                  :maxThreshold="80"
                  :minThreshold="25"
                  :minLabel="'E'"
                  :maxLabel="'F'"
                  :unit="'%'"
                  :unitOnArc="false"
                />
              </v-col>
              <v-col>
                <v-slider
                  v-model="item.odometro"
                  label="% Actual"
                  thumb-color="green"
                  thumb-label="always"
                  :rules="[(v) => !!v || 'Es Requerido']"
                ></v-slider>
              </v-col>
            </v-row>
          </v-form>
        </v-card-text>
      </template>
    </dialog-confirm>
  </v-card>
</template>
<script>
import DialogConfirm from "@admin/components/DialogConfirm.vue";
import AppWidget from "@admin/components/AppWidget.vue";
export default {
  name: "DispersalShow",
  components: { DialogConfirm, AppWidget },
  props: {
    itemId: {
      type: Number | String,
      require: true,
    },
  },
  mounted() {
    this.loadItem();
  },
  data() {
    return {
      dialogConfirm: false,
      validTicket: true,
      item: {},
      formTicket: {
        id: "",
        liters: "",
        cost_per_liter: "",
        mileage_last: 0,
        mileage_actual: 0,
      },
    };
  },
  computed: {
    AmountDispersal() {
      return this.item.fuel_liters * this.item.liters_cost;
    },
  },
  methods: {
    async loadItem() {
      const _this = this;
      await axios.get(`admin/vehicle-dispersal/${_this.itemId}`).then((res) => {
        _this.item = res.data.data;
      });
    },
    save() {
      this.$store.commit("showSnackbar", {
        message: "Litros Actualizados",
        color: "success",
        duration: 2000,
      });
    },
    actionAutorize() {
      const _this = this;
      _this.$store.commit("showDialog", {
        type: "confirm",
        icon: "mdi-check-all",
        title: "Confirmar Autorizacion",
        message: `La Dispersion Cambiara a Estatus autorizada con ${_this.item.fuel_liters}Lts`,
        okCb: () => {
          let payload = {
            fuel_liters: _this.item.fuel_liters,
            estatus_key: "autorizado",
          };
          _this.actionDispersalEstatus(payload);
        },
        cancelCb: () => {
          console.log("CANCEL");
        },
      });
    },
    actionDenied() {
      const _this = this;
      _this.$store.commit("showDialog", {
        type: "confirm",
        icon: "warning",
        title: "Confirmar Denegacion",
        message: `La Dispersion Cambiara a Estatus Denegada`,
        okCb: () => {
          let payload = {
            fuel_liters: _this.item.fuel_liters,
            estatus_key: "denegar",
          };
          _this.actionDispersalEstatus(payload);
        },
        cancelCb: () => {
          console.log("CANCEL");
        },
      });
    },
    actionDisperse() {
      const _this = this;
      _this.$store.commit("showDialog", {
        type: "confirm",
        icon: "mdi-check-all",
        title: "Confirmar Dispersion",
        message: `La Dispersion Cambiara a Dispersado, Se le Cargara Saldo de $ ${
          _this.item.fuel_liters * _this.item.liters_cost
        } a la Tarjeta ${_this.item.ticket_card}`,
        okCb: () => {
          let payload = {
            fuel_liters: _this.item.fuel_liters,
            estatus_key: "flotilla.dispersado",
          };
          _this.actionDispersalEstatus(payload);
        },
        cancelCb: () => {
          console.log("CANCEL");
        },
      });
    },
    actionDispatched() {
      const _this = this;
      if (!_this.$refs.FormTicket.validate()) return;
      if (
        _this.AmountDispersal - _this.item.dispatched_amount <
        _this.formTicket.liters * _this.formTicket.cost_per_liter
      )
        return _this.$store.commit("showSnackbar", {
          message: "El Monto del Ticket Supera al Cargo de la Dispercion",
          color: "error",
          duration: 3000,
        });
      _this.$store.commit("showDialog", {
        type: "confirm",
        icon: "mdi-check-all",
        title: "Confirmar ticket",
        message: `Se Pasara a Estatus Despachado`,
        okCb: () => {
          let payload = {
            estatus_key: "flotilla.despachado",
            fuel_odometer: _this.item.odometro,
            ticket: _this.formTicket,
          };
          _this.actionDispersalEstatus(payload);
        },
        cancelCb: () => {
          _this.formTicket = {};
          _this.dialogConfirm = false;
          console.log("CANCEL");
        },
      });
    },
    async actionDispersalEstatus(payload) {
      const _this = this;
      await axios
        .post(`admin/vehicle-dispersal/${_this.itemId}/estatus`, payload)
        .then(function (response) {
          _this.$store.commit("showSnackbar", {
            message: response.data.message,
            color: "success",
            duration: 3000,
          });
          _this.loadItem();
          _this.$eventBus.$emit("DISPERSAL_LIST_REFRESH");
        })
        .catch(function (error) {
          _this.$store.commit("hideLoader");
          if (error.response) {
            _this.$store.commit("showSnackbar", {
              message: error.response.data.message,
              color: "error",
              duration: 3000,
            });
          } else if (error.request) {
            console.log(error.request);
          } else {
            console.log("Error", error.message);
          }
        });
    },
  },
};
</script>
<style></style>
