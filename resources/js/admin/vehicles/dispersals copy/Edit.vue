<template>
  <v-card flat>
    <v-sheet
      id="scrolling-techniques-3"
      class="overflow-y-auto"
      :height="minHeight"
    >
      <v-card-text class="py-0">
        <v-row align="center" dense>
          <v-col cols="12" md="4" class="display-2 text-center mt-4">
            <v-edit-dialog
              :return-value.sync="dispersal.fuel_lts"
              large
              persistent
              save-text="Cambiar"
              @save="save"
            >
              <v-hover v-slot="{ hover }">
                <span class="pa-4" :class="`elevation-${hover ? 5 : 2}`">
                  {{ dispersal.fuel_lts }} Lts
                  <v-icon>mdi-pencil</v-icon>
                </span>
              </v-hover>
              <template v-slot:input>
                <div class="mt-4 title">
                  Cantidad de Litros
                </div>
                <v-text-field v-model="dispersal.fuel_lts" type="number">
                </v-text-field>
              </template>
            </v-edit-dialog>
          </v-col>
          <v-col cols="6" md="4">
            <v-list dense>
              <v-subheader>KILOMETRAJE</v-subheader>
              <v-list-item>
                <v-list-item-icon>
                  <v-icon>mdi-car-cruise-control</v-icon>
                </v-list-item-icon>
                <v-list-item-subtitle class="text-wrap">
                  {{ dispersal.last_mileage }} KMs (Anterior)
                </v-list-item-subtitle>
              </v-list-item>
              <v-list-item>
                <v-list-item-icon>
                  <v-icon>mdi-car-cruise-control</v-icon>
                </v-list-item-icon>
                <v-list-item-subtitle class="text-wrap">
                  {{ dispersal.actual_mileage }} KMs (Actual)
                </v-list-item-subtitle>
              </v-list-item>
              <v-list-item>
                <v-list-item-icon>
                  <v-icon>mdi-car-coolant-level</v-icon>
                </v-list-item-icon>
                <v-list-item-subtitle>
                  {{ dispersal.vehicle.fuel }}
                </v-list-item-subtitle>
              </v-list-item>
            </v-list>
          </v-col>
          <v-col cols="6" md="4">
            <v-list dense>
              <v-subheader>ODOMETRO COMBUSTIBLE</v-subheader>
              <gauge
                :value="dispersal.vehicle.fuel_odometer"
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
      <v-list class="transparent" dense>
        <v-list-item class="overline">
          <v-list-item-title>Con Cargo a:</v-list-item-title>
          <v-list-item-subtitle class="text-right">
            {{ `${dispersal.agency.title} - ${dispersal.department.title} ` }}
          </v-list-item-subtitle>
        </v-list-item>
        <v-list-item class="overline">
          <v-list-item-title>Tarjeta:</v-list-item-title>
          <v-list-item-subtitle class="text-right">
            {{ dispersal.vehicle.ticket_card }}
          </v-list-item-subtitle>
        </v-list-item>
        <v-list-item class="overline">
          <v-list-item-title>Costo x Litro:</v-list-item-title>
          <v-list-item-subtitle class="text-right">
            {{ dispersal.cost_fuel_lts | money }}
          </v-list-item-subtitle>
        </v-list-item>
        <v-list-item class="overline">
          <v-list-item-title>Importe:</v-list-item-title>
          <v-list-item-subtitle class="text-right">
            {{ dispersal.amount | money }}
          </v-list-item-subtitle>
        </v-list-item>
        <v-list-item class="overline">
          <v-list-item-title class="blue--text">Solicitante:</v-list-item-title>
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
                        {{ dispersal.reason }}
                      </span>
                    </v-fade-transition>
                  </v-col>
                </v-row>
              </template>
            </v-expansion-panel-header>
            <v-expansion-panel-content class="text-uppercase">
              {{ dispersal.reason_description }}
            </v-expansion-panel-content>
          </v-expansion-panel>
        </v-expansion-panels>
      </v-list>
    </v-sheet>
    <v-divider class="mt-3"></v-divider>
    <v-card-actions
      class="justify-end"
      v-if="dispersal.estatus.key != 'flotilla.dispersado'"
    >
      <v-btn color="primary" @click="changeEstatus('autorizado')">
        Autorizar
      </v-btn>
      <v-btn text color="error" @click="changeEstatus('denegar')">
        Rechazar
      </v-btn>
      <v-btn text color="orange" @click="changeEstatus('flotilla.dispersado')">
        Dispersado
      </v-btn>
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
          fluid
          v-if="dispersal.estatus_key == 'flotilla.dispersado'"
        >
          <div class="text-center">
            Inidica como quedo el Odometro despues de la Dispersion
          </div>
          <gauge
            :value="dispersal.fuel_odometer"
            :min="0"
            :max="100"
            :fontSize="'16px'"
            :padding="false"
            :svgStyle="{
              maxWidth: 430,
              maxHeight: 150,
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
          <div style="width: 100%; margin-top: 1rem;">
            <v-form v-model="validOdometer" ref="formOdometer" lazy-validation>
              <v-slider
                v-model="dispersal.fuel_odometer"
                :label="'Odometro'"
                :thumb-color="'blue'"
                :rules="RulesOdometer"
                class="mb-2"
                thumb-label
                dense
              ></v-slider>
              <v-text-field
                v-model="dispersal.cost_fuel_lts"
                :label="'Costo x Litro'"
                :rules="[(v) => !!v || 'El Costo por litro es requerido']"
                prefix="MXN"
                suffix="$"
                reverse
                outlined
                dense
              ></v-text-field>
            </v-form>
          </div>
        </v-container>
        <v-container v-else>
          La solciitud de Dispersion Cambiara a Estatus:
          {{ dispersal.estatus_key }}
        </v-container>
      </template>
    </dialog-confirm>
  </v-card>
</template>

<script>
import DialogConfirm from "@admin/components/DialogConfirm.vue";
export default {
  components: { DialogConfirm },
  props: {
    dispersalId: {
      required: true,
      type: [Number, String],
    },
  },
  data() {
    return {
      dialog: false,
      validOdometer: true,
      dispersal: {
        actual_mileage: 0,
        created_at: "",
        estatus: {},
        fuel_lts: 0,
        fuel_odometer: 0,
        amount: 0,
        cost_fuel_lts: 0,
        id: 4,
        last_mileage: 0,
        reason: "",
        reason_description: "",
        solicitante: {},
        updated_at: "",
        updated_by: null,
        vehicle: {
          fuel_odometer: 0,
        },
        agency: {
          title: "",
        },
        department: {
          title: "",
        },
      },
    };
  },
  computed: {
    minHeight() {
      const height = this.$vuetify.breakpoint.mobile ? "90vh" : "65vh";
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
    this.loadDispersal(() => {});
  },
  methods: {
    async update() {
      const _this = this;
      if (_this.dispersal.estatus_key == "flotilla.dispersado") {
        if (!_this.$refs.formOdometer.validate()) return;
        _this.dispersal.amount =
          _this.dispersal.cost_fuel_lts * _this.dispersal.fuel_lts;
      }
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
      await axios
        .get(`/admin/vehicle-dispersal/${_this.dispersalId}`)
        .then(function (response) {
          let Data = response.data.data;
          _this.dispersal = { ...Data };
          (cb || Function)();
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
  },
};
</script>
