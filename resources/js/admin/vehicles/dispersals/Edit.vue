<template>
  <v-card flat>
    <v-sheet
      id="scrolling-techniques-3"
      class="overflow-y-auto"
      :min-height="minHeight"
    >
      <v-card-text class="py-0">
        <v-row align="center" dense>
          <v-col cols="12" md="4" class="display-2 text-center mt-4">
            <v-edit-dialog
              :return-value.sync="dispersal.fuel_liters"
              large
              persistent
              save-text="Cambiar"
              @save="save"
            >
              <v-hover v-slot="{ hover }">
                <span class="pa-4" :class="`elevation-${hover ? 5 : 2}`">
                  {{ dispersal.fuel_liters }} Lts
                  <v-icon>mdi-pencil</v-icon>
                </span>
              </v-hover>
              <template v-slot:input>
                <div class="mt-4 title">
                  Cantidad de Litros
                </div>
                <v-text-field v-model="dispersal.fuel_liters" type="number">
                </v-text-field>
              </template>
            </v-edit-dialog>
          </v-col>
          <v-col cols="6" md="4">
            <v-list dense>
              <v-subheader>Info</v-subheader>
              <v-list-item v-if="!dispersal.drum_dispersion">
                <v-list-item-icon>
                  <v-icon>mdi-car-cruise-control</v-icon>
                </v-list-item-icon>
                <v-list-item-subtitle class="text-wrap">
                  {{ dispersal.mileage_last }} KMs (Anterior)
                </v-list-item-subtitle>
              </v-list-item>
              <v-list-item v-if="!dispersal.drum_dispersion">
                <v-list-item-icon>
                  <v-icon>mdi-car-cruise-control</v-icon>
                </v-list-item-icon>
                <v-list-item-subtitle class="text-wrap">
                  {{ dispersal.mileage_actual }} KMs (Actual)
                </v-list-item-subtitle>
              </v-list-item>
              <v-list-item>
                <v-list-item-icon>
                  <v-icon>mdi-car-coolant-level</v-icon>
                </v-list-item-icon>
                <v-list-item-subtitle>
                  {{ dispersal.fuel_name }}
                </v-list-item-subtitle>
              </v-list-item>
            </v-list>
          </v-col>
          <v-col cols="6" md="4" v-if="!dispersal.drum_dispersion">
            <v-list dense>
              <v-subheader>ODOMETRO COMBUSTIBLE</v-subheader>
              <gauge
                :value="dispersal.fuel_odometer"
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
            </v-list>
          </v-col>
        </v-row>
      </v-card-text>
      <v-card-text>
        <v-row dense>
          <v-col cols="12" md="7">
            <v-list class="transparent elevation-2" dense>
              <v-list-item class="overline">
                <v-list-item-title>Con Cargo a:</v-list-item-title>
                <v-list-item-subtitle
                  v-if="dispersal.agency || dispersal.department"
                  class="text-right text-no-wrap"
                >
                  {{ dispersal.agency.title }} -
                  {{ dispersal.department.title }}
                </v-list-item-subtitle>
              </v-list-item>
              <v-list-item class="overline">
                <v-list-item-title>Ticket Card:</v-list-item-title>
                <v-list-item-subtitle class="text-right">
                  {{ dispersal.ticket_card }}
                </v-list-item-subtitle>
              </v-list-item>
              <v-list-item class="overline">
                <v-list-item-title>Costo x Litro:</v-list-item-title>
                <v-list-item-subtitle class="text-right">
                  {{ dispersal.fuel_cost_liter | money }}
                </v-list-item-subtitle>
              </v-list-item>
              <v-list-item class="overline">
                <v-list-item-title>Importe:</v-list-item-title>
                <v-list-item-subtitle class="text-right">
                  {{
                    (dispersal.fuel_liters * dispersal.fuel_cost_liter) | money
                  }}
                </v-list-item-subtitle>
              </v-list-item>
              <v-list-item class="overline">
                <v-list-item-title>
                  Solicitante:
                </v-list-item-title>
                <v-list-item-subtitle class="text-right">
                  {{ dispersal.solicitante.name }}
                </v-list-item-subtitle>
              </v-list-item>

              <v-expansion-panels flat>
                <v-expansion-panel>
                  <v-expansion-panel-header class="px-4 py-0 overline">
                    <template v-slot:default="{ open }">
                      <v-row no-gutters dense>
                        <v-col cols="4">
                          Motivo de Dispercion:
                        </v-col>
                        <v-col cols="8">
                          <v-fade-transition leave-absolute>
                            <span v-if="open" key="0">
                              {{ dispersal.reason_dispersal }}
                            </span>
                          </v-fade-transition>
                        </v-col>
                      </v-row>
                    </template>
                  </v-expansion-panel-header>
                  <v-expansion-panel-content class="text-uppercase">
                    {{ dispersal.reason_note }}
                  </v-expansion-panel-content>
                </v-expansion-panel>
              </v-expansion-panels>
            </v-list>
          </v-col>
          <v-col cols="12" md="5">
            <app-widget
              icon-title="mdi-gas-station-outline"
              title="Tickets de Gasolina"
              padding-hide
            >
              <v-btn
                slot="widget-header-action"
                v-if="
                  dispersal.estatus.key == 'flotilla.dispersado' ||
                  dispersal.estatus.key == 'flotilla.despachado'
                "
                :color="getColorByStatus('flotilla.despachado')"
                @click="changeEstatus('flotilla.despachado')"
                small
              >
                <v-icon left>mdi-receipt</v-icon>
                Registrar ticket
              </v-btn>
              <v-simple-table
                slot="widget-content"
                v-show="dispersal.tickets_info.length > 0"
                class="text-uppercase elevation-0"
                dense
              >
                <template v-slot:default>
                  <thead>
                    <tr>
                      <th class="text-left">
                        Ticket
                      </th>
                      <th class="text-left">
                        Litros
                      </th>
                      <th class="text-left">
                        Costo
                      </th>
                      <th class="text-left">
                        Kms Actual
                      </th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr
                      v-for="(item, index) in dispersal.tickets_info"
                      :key="index"
                    >
                      <td class="caption">{{ item.id }}</td>
                      <td class="caption">{{ item.liters }}</td>
                      <td class="caption">{{ item.cost_per_liter }}</td>
                      <td class="caption">{{ item.mileage_actual }}</td>
                    </tr>
                  </tbody>
                </template>
              </v-simple-table>
            </app-widget>
          </v-col>
        </v-row>
      </v-card-text>
    </v-sheet>
    <v-divider class="mt-3"></v-divider>
    <v-card-actions class="justify-end">
      <v-btn
        v-if="
          dispersal.estatus.key == 'pendiente' ||
          dispersal.estatus.key == 'denegar'
        "
        :color="getColorByStatus('autorizado')"
        @click="changeEstatus('autorizado')"
        small
      >
        Autorizar
      </v-btn>
      <v-btn
        v-if="
          dispersal.estatus.key != 'autorizado' &&
          dispersal.estatus.key != 'flotilla.despachado' &&
          dispersal.estatus.key != 'flotilla.dispersado'
        "
        :color="getColorByStatus('denegar')"
        text
        @click="changeEstatus('denegar')"
        small
      >
        Denegar
      </v-btn>
      <v-btn
        v-if="dispersal.estatus.key == 'autorizado'"
        :color="getColorByStatus('flotilla.dispersado')"
        text
        @click="changeEstatus('flotilla.dispersado')"
        small
      >
        Dispersar
      </v-btn>
      <!-- <v-btn
        v-if="
          dispersal.estatus.key == 'flotilla.dispersado' ||
          dispersal.estatus.key == 'flotilla.despachado'
        "
        :color="getColorByStatus('flotilla.despachado')"
        @click="changeEstatus('flotilla.despachado')"
        
        small
      >
        Registrar ticket <v-icon left>mdi-receipt</v-icon>
      </v-btn> -->
    </v-card-actions>

    <dialog-confirm
      :show="dialog"
      :max-width="500"
      @close="dialog = false"
      @agree="update()"
    >
      <template #title>
        Confirmar Accion.
      </template>
      <template #body>
        <v-container
          v-if="
            dispersal.estatus.key == 'flotilla.dispersado' ||
            dispersal.estatus.key == 'flotilla.despachado'
          "
        >
          <v-simple-table
            v-show="dispersal.tickets_info.length > 0"
            dense
            class="text-uppercase mt-3"
          >
            <template v-slot:default>
              <thead>
                <tr>
                  <th class="text-left">
                    Ticket
                  </th>
                  <th class="text-left">
                    Litros
                  </th>
                  <th class="text-left">
                    Costo
                  </th>
                  <th class="text-left">
                    Kms Actual
                  </th>
                  <th class="text-left"></th>
                </tr>
              </thead>
              <tbody>
                <tr
                  v-for="(item, index) in dispersal.tickets_info"
                  :key="index"
                >
                  <td class="caption">{{ item.id }}</td>
                  <td class="caption">{{ item.liters }}</td>
                  <td class="caption">{{ item.cost_per_liter }}</td>
                  <td class="caption">{{ item.mileage_actual }}</td>
                  <td class="text-right" style="width: 50px;">
                    <v-icon color="red" small @click="deleteTicket(index)">
                      mdi-delete
                    </v-icon>
                  </td>
                </tr>
              </tbody>
            </template>
          </v-simple-table>
          <v-form v-model="validFormTicket" lazy-validation ref="FormTicket">
            <v-row class="overline mt-3" dense>
              <v-col cols="12" md="6">
                <v-text-field
                  v-model="ticket.id"
                  label="Num. Autorizacion"
                  :rules="[(v) => !!v || 'Requerido']"
                  outlined
                  dense
                ></v-text-field>
              </v-col>
              <v-col cols="12" md="6">
                <v-text-field
                  v-model="ticket.liters"
                  label="Litros"
                  :rules="[(v) => !!v || 'Requerido']"
                  outlined
                  dense
                ></v-text-field>
              </v-col>
              <v-col cols="12" md="6">
                <v-text-field
                  v-model="ticket.cost_per_liter"
                  label="Precio por Litro"
                  :rules="[(v) => !!v || 'Requerido']"
                  outlined
                  dense
                ></v-text-field>
              </v-col>
              <v-col cols="12" md="6">
                <v-text-field
                  v-model="ticket.mileage_actual"
                  label="Kms Actual"
                  :rules="[(v) => !!v || 'Requerido']"
                  append-outer-icon="mdi-plus-thick"
                  @click:append-outer="addTicket"
                  outlined
                  dense
                ></v-text-field>
              </v-col>
              <!-- <div>
                <div class="text-end text-primary">
                  Nivel del Odometro
                </div>
                <v-slider
                  v-model="dispersal.fuel_odometer"
                  :label="'Nivel %'"
                  append-icon="mdi-car-cruise-control"
                  :thumb-color="'red'"
                  thumb-label
                  :hint="`${dispersal.vehicle.fuel_odometer}% nivel Actual`"
                  :rules="[
                    (v) => !!v || 'Es Requerido',
                    (v) =>
                      v > dispersal.vehicle.fuel_odometer ||
                      'Debe ser Mayor al Nivel Actual',
                  ]"
                  persistent-hint
                ></v-slider>
              </div> -->
            </v-row>
            <div class="text-h5 text-right grey--text d-flex">
              Total: {{ (ticket.liters * ticket.cost_per_liter) | money }}
            </div>
          </v-form>
          <!-- <gauge
            :value="dispersal.fuel_odometer"
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
          /> -->
        </v-container>
        <v-container v-else>
          La solciitud de Dispersion Cambiara a Estatus:
          {{ dispersal.estatus.title }}
        </v-container>
      </template>
    </dialog-confirm>
  </v-card>
</template>

<script>
import AppWidget from "@admin/components/AppWidget.vue";
import DialogConfirm from "@admin/components/DialogConfirm.vue";
export default {
  components: { DialogConfirm, AppWidget },
  props: {
    dispersalId: {
      required: true,
      type: [Number, String],
    },
  },
  data() {
    return {
      validFormTicket: true,
      dialog: false,
      validOdometer: true,
      ticket: {
        id: null,
        liters: 0,
        cost_per_liter: 0,
        mileage_actual: 0,
      },
      defaulTicket: {
        id: null,
        liters: 0,
        cost_per_liter: 0,
        mileage_actual: 0,
      },
      dispersal: {
        tickets_info: [],
      },
      colors: {
        pendiente: "blue",
        autorizado: "green",
        "flotilla.dispersado": "purple",
        "flotilla.despachado": "brown darken-1",
        denegar: "red",
      },
    };
  },
  computed: {
    minHeight() {
      const height = "88vh";
      return `calc(${height} - ${this.$vuetify.application.top}px)`;
    },
    RulesOdometer() {
      return [
        (v) =>
          v > this.dispersal.vehicle.fuel_odometer ||
          "Debe ser Mayor al ultimo Odometro",
      ];
    },
  },
  created() {
    this.loadDispersal();
  },
  methods: {
    async update() {
      const _this = this;

      const payload = {
        ..._this.dispersal,
      };
      await axios
        .put(`/admin/vehicle-dispersal/${_this.dispersalId}`, payload)
        .then(function (response) {
          _this.$store.commit("showSnackbar", {
            message: response.data.message,
            color: "success",
            duration: 5000,
          });
          _this.dialog = false;
          _this.$eventBus.$emit("VEHICLE_REFRESH");
          _this.$eventBus.$emit("CLOSE_DIALOG");
        })
        .catch(function (error) {
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
    async loadDispersal(cb) {
      const _this = this;
      this.dispersal = await axios
        .get(`/admin/vehicle-dispersal/${_this.dispersalId}`)
        .then((response) => {
          let Data = response.data.data;
          return { ...Data };
          // (cb || Function)();
        });
    },
    save() {
      this.$store.commit("showSnackbar", {
        message: "Guardar",
        color: "success",
        duration: 2000,
      });
    },
    cancel() {
      this.$store.commit("showSnackbar", {
        message: "Cancelado",
        color: "error",
        duration: 2000,
      });
    },
    open() {
      this.$store.commit("showSnackbar", {
        message: "Modificar Cantidad de Litros",
        color: "info",
        duration: 2000,
      });
    },
    changeEstatus(key = "pendiente") {
      const _this = this;
      _this.dispersal.estatus_key = key;
      _this.dialog = true;
    },
    getColorByStatus(status) {
      return this.colors[status];
    },
    addTicket() {
      const _this = this;
      if (!_this.$refs.FormTicket.validate()) return;
      _this.dispersal.tickets_info.push(this.ticket);
      _this.ticket = { ..._this.defaultTicket };
    },
    deleteTicket(index) {
      this.dispersal.tickets_info.splice(index, 1);
    },
  },
};
</script>
