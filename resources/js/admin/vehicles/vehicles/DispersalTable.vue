<template>
  <v-card>
    <v-card-text class="pa-0">
      <template>
        <v-data-table
          :headers="headers"
          :items="items"
          :sort-by="'created_at'"
          :sort-desc="true"
          class="elevation-0 table-striped"
        >
          <template v-slot:[`item.created_at`]="{ value }">
            {{ $appFormatters.formatDate(value, "DD MMM YYYY") }}
          </template>
          <template v-slot:[`item.estatus`]="{ item }">
            <v-chip
              label
              small
              :color="getColorByStatus(item.estatus.key)"
              text-color="white"
            >
              {{ item.estatus.title }}
            </v-chip>
          </template>
          <template v-slot:[`item.action`]="{ item }">
            <v-btn
              v-if="canDispatch(item)"
              rounded
              small
              color="orange"
              icon
              @click="dialogConfirm = true"
            >
              <v-icon>mdi-receipt</v-icon>
            </v-btn>
            <dialog-confirm
              :show="dialogConfirm"
              :max-width="500"
              @close="(dialogConfirm = false), (formTicket = {})"
              @agree="actionDispatched(item)"
            >
              <template #title>
                Registrar Ticket y Odometro
              </template>
              <template #body>
                <v-card-text>
                  <v-form
                    v-model="validTicket"
                    ref="FormTicket"
                    lazy-validation
                  >
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
                          :value="item.fuel_odometer"
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
                          v-model="item.fuel_odometer"
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
          </template>
        </v-data-table>
      </template>
      <v-divider />
    </v-card-text>
  </v-card>
</template>

<script>
import DialogConfirm from "@admin/components/DialogConfirm.vue";
export default {
  components: { DialogConfirm },
  props: {
    items: {
      type: Array,
      default: () => {
        [];
      },
    },
  },
  data: () => ({
    dialogConfirm: false,
    validTicket: true,
    formTicket: {
      id: "",
      liters: "",
      cost_per_liter: "",
      mileage_last: 0,
      mileage_actual: 0,
    },
    headers: [
      {
        text: "#",
        align: "left",
        value: "id",
        sortable: false,
      },
      { text: "Fecha", value: "created_at" },
      { text: "Litros", value: "fuel_liters", sortable: false },
      { text: "Estatus", value: "estatus", sortable: false },
      { text: "Accion", value: "action", sortable: false },
    ],
    // items,
    colors: {
      pendiente: "blue",
      autorizado: "green",
      "flotilla.dispersado": "purple",
      denegar: "red",
    },
  }),
  methods: {
    getColorByStatus(status) {
      return this.colors[status];
    },
    async update(item) {
      const _this = this;
      if (!_this.$refs.formOdometer.validate()) return;
      let payload = {
        estatus_key: "flotilla.dispersado",
        ...item,
      };
      await axios
        .put(`/admin/vehicle-dispersal/${item.id}`, payload)
        .then(function (response) {
          _this.$store.commit("showSnackbar", {
            message: response.data.message,
            color: "success",
            duration: 3000,
          });
          _this.dialog = false;
          _this.$eventBus.$emit("REFRESH");
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
    actionDispatched(item) {
      const _this = this;
      if (!_this.$refs.FormTicket.validate()) return;
      _this.$store.commit("showDialog", {
        type: "confirm",
        icon: "mdi-check-all",
        title: "Confirmar ticket",
        message: `Se Pasara a Estatus Despachado`,
        okCb: () => {
          let payload = {
            estatus_key: "flotilla.despachado",
            fuel_odometer: item.fuel_odometer,
            ticket: _this.formTicket,
          };
          axios
            .post(`admin/vehicle-dispersal/${item.id}/estatus`, payload)
            .then(function (response) {
              _this.$store.commit("showSnackbar", {
                message: response.data.message,
                color: "success",
                duration: 3000,
              });
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
        cancelCb: () => {
          _this.formTicket = {};
          _this.dialogConfirm = false;
          console.log("CANCEL");
        },
      });
    },
    canDispatch(item) {
      let amount = item.fuel_cost_liter * item.fuel_liters;
      let balance = item.dispatched_amount + item.fuel_cost_liter;
      let account = item.vehicle.ticket.account_balance;
      return (
        item.estatus.key == "flotilla.dispersado" ||
        (item.estatus.key == "flotilla.despachado" &&
          balance < amount &&
          account > 0)
      );
    },
  },
};
</script>
