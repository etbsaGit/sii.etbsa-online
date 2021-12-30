<template>
  <v-stepper v-model="step">
    <v-stepper-header>
      <v-stepper-step :complete="step > 1" step="1" editable>
        Seleccionar Matricula
      </v-stepper-step>

      <v-divider></v-divider>

      <v-stepper-step :complete="step > 2" step="2">
        Motivo y Cargo
      </v-stepper-step>

      <v-divider></v-divider>

      <v-stepper-step step="3">
        Combustible a Solicitar
      </v-stepper-step>
    </v-stepper-header>

    <v-stepper-items>
      <v-subheader class="caption text-uppercase">
        Matricula seleccionada: {{ Vehicle.matricula || "" }}
        {{ Vehicle.model || "" }}
      </v-subheader>
      <v-stepper-content step="1">
        <v-card class="mb-12" color="grey lighten-4">
          <v-container fluid>
            <v-form
              v-model="validFormStepOne"
              ref="formStepOne"
              lazy-validation
            >
              <v-row dense align="center">
                <v-col cols="12" md="8">
                  <div class="d-flex flex-column overline">
                    <v-autocomplete
                      v-model="Vehicle"
                      :items="options.vehicles"
                      label="Seleccione una Matricula:"
                      item-text="matricula"
                      item-value="id"
                      :rules="[
                        (v) => !!v || 'Seleccione una Matricula',
                        (v) => !!v.id || 'Seleccione una Matricula',
                      ]"
                      return-object
                      outlined
                      dense
                    ></v-autocomplete>
                    <template v-if="!form.drum_dispersion && !!Vehicle.id">
                      <v-row
                        dense
                        align="start"
                        justify="center"
                        class="text-center"
                      >
                        <v-col cols="12" md="6">
                          <div>Ultimo Kilometraje</div>
                          <span class="text-h5">
                            {{ vehicle.mileage_actual || 0 }} Kms
                          </span>
                        </v-col>
                        <v-col cols="12" md="6">
                          <div class="text-end text-primary">
                            Kilometraje Actual
                          </div>
                          <v-text-field
                            v-model="form.mileage_actual"
                            placeholder="Registrar Kilometraje"
                            type="Number"
                            prefix="KMs"
                            clearable
                            :rules="[
                              (v) => !!v || 'Registre el Kilometraje',
                              (v) =>
                                v > vehicle.mileage_actual ||
                                'Debe ser Mayor al Ultimo Kilometraje',
                            ]"
                            outlined
                            reverse
                            dense
                          ></v-text-field>
                        </v-col>
                      </v-row>
                      <div>
                        <div class="text-end text-primary">
                          Nivel del Odometro
                        </div>
                        <v-slider
                          v-model="form.fuel_odometer"
                          :label="'Nivel %'"
                          append-icon="mdi-car-cruise-control"
                          :thumb-color="'red'"
                          thumb-label
                          :hint="`${fuel_odometer}% nivel Actual`"
                          :rules="[
                            (v) => !!v || 'Es Requerido',
                            (v) =>
                              v < fuel_odometer ||
                              'Debe ser menor al Odometro Actual',
                          ]"
                          persistent-hint
                        ></v-slider>
                      </div>
                    </template>
                  </div>
                </v-col>
                <v-col cols="12" md="4" class="overline text-center">
                  <template v-if="!form.drum_dispersion && !!Vehicle.id">
                    <div class="text-center">
                      Marca del Odometro de la ultima Dispercion
                    </div>
                    <gauge
                      :value="form.fuel_odometer"
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
                    />
                  </template>
                  <template v-else>
                    <v-icon size="150" color="red">mdi-fuel</v-icon>
                  </template>
                </v-col>
              </v-row>
            </v-form>
          </v-container>
        </v-card>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn text>
            Cancel
          </v-btn>
          <v-btn color="primary" @click="validateStepOne">
            Continuar
          </v-btn>
        </v-card-actions>
      </v-stepper-content>

      <v-stepper-content step="2">
        <v-card class="mb-12" color="grey lighten-4">
          <v-container fluid>
            <v-form
              v-model="validFormStepTwo"
              ref="formStepTwo"
              lazy-validation
            >
              <v-row dense class="overline">
                <v-col cols="12" md="4">
                  <v-subheader>Motivo de la Solicitud:</v-subheader>
                  <v-select
                    v-model="form.reason_dispersal"
                    :items="options.reason_dispersal"
                    label="Motivo"
                    :rules="[(v) => !!v || 'Es Requeridio']"
                    outlined
                    dense
                  ></v-select>
                  <template v-if="form.reason_dispersal == 'Servicio'">
                    <v-subheader class="caption">
                      Relacionar (OT, Serie, Factura o NE):
                    </v-subheader>
                    <v-text-field
                      v-model="form.reason_data.reference"
                      label="Referencia:"
                      outlined
                      dense
                      prepend-icon="mdi-swap-horizontal"
                      :rules="[(v) => !!v || 'Es Requeridio']"
                    ></v-text-field>
                  </template>
                  <template v-else-if="form.reason_dispersal == 'Viaje'">
                    <v-subheader class="caption">
                      Inidicar Origen y Destino:
                    </v-subheader>
                    <v-text-field
                      v-model="form.reason_data.origin"
                      label="Origen"
                      outlined
                      dense
                      prepend-icon="mdi-arrow-collapse-right"
                      :rules="[(v) => !!v || 'Es Requeridio']"
                    ></v-text-field>
                    <v-text-field
                      v-model="form.reason_data.destination"
                      label="Destino"
                      outlined
                      dense
                      prepend-icon="mdi-arrow-collapse-left"
                      :rules="[(v) => !!v || 'Es Requeridio']"
                    ></v-text-field>
                  </template>
                </v-col>
                <v-col cols="12" md="4">
                  <v-subheader>Con Cargo a:</v-subheader>
                  <v-select
                    v-model="form.agency_id"
                    label="Sucursal"
                    :items="options.agencies"
                    item-text="title"
                    item-value="id"
                    outlined
                    dense
                    prepend-icon="mdi-domain"
                    :rules="[(v) => !!v || 'Es Requeridio']"
                  ></v-select>
                  <v-select
                    v-model="form.department_id"
                    label="Departamento"
                    :items="options.departments"
                    item-text="title"
                    item-value="id"
                    outlined
                    dense
                    prepend-icon="mdi-vector-arrange-below"
                    :rules="[(v) => !!v || 'Es Requeridio']"
                  ></v-select>
                </v-col>
                <v-col cols="12" md="4">
                  <v-subheader>Fecha para Dispersar:</v-subheader>
                  <v-dialog
                    ref="dialog"
                    v-model="modal"
                    :return-value.sync="form.date_to_disperse"
                    persistent
                    width="350px"
                  >
                    <template v-slot:activator="{ on, attrs }">
                      <v-text-field
                        v-model="form.date_to_disperse"
                        :rules="[(v) => !!v || 'Es Requeridio']"
                        label="Fecha para Dispersar"
                        prepend-icon="mdi-calendar"
                        readonly
                        v-bind="attrs"
                        v-on="on"
                        outlined
                        dense
                      ></v-text-field>
                    </template>
                    <v-date-picker
                      v-model="form.date_to_disperse"
                      scrollable
                      :min="new Date().toISOString().substr(0, 10)"
                    >
                      <v-spacer></v-spacer>
                      <v-btn text color="primary" @click="modal = false">
                        Cancel
                      </v-btn>
                      <v-btn
                        text
                        color="primary"
                        @click="$refs.dialog.save(form.date_to_disperse)"
                      >
                        OK
                      </v-btn>
                    </v-date-picker>
                  </v-dialog>
                  <v-textarea
                    v-model="form.reason_note"
                    label="Comentario:"
                    placeholder="Describa el Motivo de la Solicitud"
                    :rules="[(v) => !!v || 'Es Requeridio']"
                    outlined
                    dense
                  ></v-textarea>
                </v-col>
              </v-row>
            </v-form>
          </v-container>
        </v-card>

        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn text>
            Cancel
          </v-btn>
          <v-btn color="primary" @click="validateStepTwo">
            Continuar
          </v-btn>
        </v-card-actions>
      </v-stepper-content>

      <v-stepper-content step="3">
        <v-card class="mb-12" color="grey lighten-4">
          <v-container fluid>
            <v-form
              v-model="validFormStepThree"
              ref="formStepThree"
              lazy-validation
            >
              <v-row class="mb-4 overline" align="end">
                <v-col
                  class="d-flex flex-wrap text-left align-end"
                  cols="12"
                  md="8"
                >
                  <div class="mr-auto">
                    <span
                      class="text-h2 font-weight-light"
                      v-text="form.fuel_liters"
                      :class="`${color}--text`"
                    ></span>
                    <span class="subheading font-weight-light mr-1">Lts</span>
                  </div>
                  <div class="ml-auto text-right">
                    <div class="font-weight-light mr-1">Ticket Card</div>
                    <span
                      class="text-h4 font-weight-light grey--text"
                      v-text="vehicle.ticket.ticket_card"
                    >
                    </span>
                  </div>
                  <div class="ml-auto text-right">
                    <div class="font-weight-light mr-1">Saldo Actual</div>
                    <span class="text-h4 font-weight-light grey--text">
                      {{ vehicle.ticket.account_balance | money }} MXN
                    </span>
                  </div>
                </v-col>
                <v-col cols="12" md="4">
                  <v-select
                    v-model="Fuel"
                    label="Combustible:"
                    :items="options.vehicle_fuel"
                    item-text="name"
                    item-value="id"
                    return-object
                    outlined
                    prepend-icon="mdi-fuel"
                    :readonly="!form.drum_dispersion"
                    :rules="[(v) => !!v || 'Es Requeridio']"
                    hide-details
                    filled
                  ></v-select>
                </v-col>
              </v-row>

              <v-slider
                v-model="form.fuel_liters"
                :color="color"
                track-color="grey"
                always-dirty
                hint="indique los litros"
                persistent-hint
                :readonly="form.reason_dispersal == 'Solicitud Semanal'"
                :rules="[(v) => !!v || 'Es Requeridio']"
                min="5"
                max="500"
              >
                <template v-slot:prepend>
                  <v-icon :color="color" @click="form.fuel_liters--">
                    mdi-minus
                  </v-icon>
                </template>

                <template v-slot:append>
                  <v-icon :color="color" @click="form.fuel_liters++">
                    mdi-plus
                  </v-icon>
                </template>
              </v-slider>
            </v-form>
          </v-container>
        </v-card>

        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn text>
            Cancel
          </v-btn>
          <v-btn color="primary" @click="validateStepThree">
            Finalizar
          </v-btn>
        </v-card-actions>
      </v-stepper-content>
    </v-stepper-items>

    <dialog-confirm
      v-if="dialogConfirm"
      :show="dialogConfirm"
      @close="dialogConfirm = false"
      @agree="save()"
      max-width="525"
    >
      <template #title>
        Confirmar Solicitud.
      </template>
      <template #body>
        <v-list class="transparent" dense>
          <v-list-item class="overline">
            <v-list-item-title>Matricula:</v-list-item-title>
            <v-list-item-subtitle class="text-right caption">
              <b>{{ Vehicle.matricula || "" }} {{ Vehicle.model || "" }}</b>
            </v-list-item-subtitle>
          </v-list-item>
          <v-list-item class="overline">
            <v-list-item-title>Fecha para dispersion:</v-list-item-title>
            <v-list-item-subtitle class="text-right caption">
              <b>
                {{ $appFormatters.formatDate(form.date_to_disperse, "ll") }}
              </b>
            </v-list-item-subtitle>
          </v-list-item>
          <v-list-item class="overline">
            <v-list-item-title>Ticket Card:</v-list-item-title>
            <v-list-item-subtitle class="text-right caption">
              <b>
                {{ form.ticket_card }}
              </b>
            </v-list-item-subtitle>
          </v-list-item>
          <v-list-item class="overline">
            <v-list-item-title>Motivo de Solicitud:</v-list-item-title>
            <v-list-item-subtitle class="text-right caption">
              <b>
                {{ form.reason_dispersal }}
              </b>
            </v-list-item-subtitle>
          </v-list-item>
          <v-list-item class="overline">
            <v-list-item-title>Combustible a solicitar:</v-list-item-title>
            <v-list-item-subtitle class="text-right caption">
              <b> {{ form.fuel_liters }} Lts, {{ form.fuel_name }} </b>
            </v-list-item-subtitle>
          </v-list-item>
          <v-list-item class="overline">
            <v-list-item-title>Cargo a:</v-list-item-title>
            <v-list-item-subtitle class="text-right caption">
              <b>
                {{
                  options.agencies.find((e) => e.id == form.agency_id).title ||
                  ""
                }},
                {{
                  options.departments.find((e) => e.id == form.department_id)
                    .title || ""
                }}
              </b>
            </v-list-item-subtitle>
          </v-list-item>
        </v-list>
      </template>
    </dialog-confirm>
  </v-stepper>
</template>
<script>
import DialogConfirm from "@admin/components/DialogConfirm.vue";
export default {
  name: "DispersalCreate",
  components: { DialogConfirm },
  mounted() {
    this.$store.commit("setBreadcrumbs", [
      { label: "Flotilla", to: { name: "vehicle.list" } },
      { label: "Disperciones", to: { name: "vehicle.dispersal.list" } },
      { label: "Crear" },
    ]);
    this.loadOptions(() => {});
  },
  data() {
    return {
      step: 1,
      fuel_odometer: 0,
      vehicle: {
        matricula: "",
        model: "",
        combustible: {},
        bidon_fuel: false,
        ticket: {
          ticket_card: null,
          account_balance: 0,
        },
      },
      form: {
        vehicle_id: null,
        drum_dispersion: false,
        fuel_odometer: 100,
        mileage_last: 0,
        mileage_actual: 0,
        reason_dispersal: null,
        reason_data: {},
        reason_note: "",
        fuel_name: null,
        fuel_id: null,
        fuel_liters: 0,
        fuel_cost_liter: 0,
        ticket_card: null,
        ticket_info: [],
        date_to_disperse: new Date(
          Date.now() - new Date().getTimezoneOffset() * 60000
        )
          .toISOString()
          .substr(0, 10),
        agency_id: null,
        department_id: null,
      },
      options: {
        vehicles: [],
        agencies: [],
        departments: [],
        vehicle_fuel: [],
        reason_dispersal: [
          "Solicitud Semanal",
          "Servicio",
          "Viaje",
          "Adicional",
          "Otro",
        ],
      },
      modal: false,
      validFormStepOne: true,
      validFormStepTwo: true,
      validFormStepThree: true,
      dialogConfirm: false,
    };
  },
  methods: {
    async loadOptions(cb) {
      const _this = this;
      await axios
        .get("admin/vehicle-dispersal/create")
        .then(function (response) {
          let Response = response.data.data;
          _this.options.vehicles = Response.vehicles.filter(
            (v) => !!v.can_dispersal
          );
          _this.options.agencies = Response.agencies;
          _this.options.departments = Response.departments;
          _this.options.vehicle_fuel = Response.vehicle_fuel;
          (cb || Function)();
        });
    },
    validateStepOne() {
      const _this = this;
      if (!_this.$refs.formStepOne.validate()) return;
      _this.form.vehicle_id = _this.vehicle.id;
      _this.form.mileage_last = _this.vehicle.mileage_last || 0;
      _this.step = 2;
    },
    validateStepTwo() {
      const _this = this;
      if (!_this.$refs.formStepTwo.validate()) return;
      _this.step = 3;
    },
    validateStepThree() {
      const _this = this;
      if (!_this.$refs.formStepThree.validate()) return;
      _this.dialogConfirm = true;
    },
    async save() {
      const _this = this;
      _this.dialogConfirm = false;
      await axios
        .post("/admin/vehicle-dispersal", _this.form)
        .then(function (response) {
          _this.$store.commit("showSnackbar", {
            message: response.data.message,
            color: "success",
            duration: 3000,
          });
          _this.$eventBus.$emit("VEHICLE_REFRESH");
          _this.$router.push({ name: "vehicle.dispersal.list" });
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
  },
  computed: {
    Fuel: {
      get() {
        if (this.vehicle.combustible) {
          this.form.fuel_name = this.vehicle.combustible.name;
          this.form.fuel_id = this.vehicle.combustible.id;
          this.form.fuel_cost_liter = this.vehicle.combustible.cost_lt;
          return this.vehicle.combustible;
        }
      },
      set(value) {
        this.form.fuel_id = value.id;
        this.form.fuel_name = value.name;
        this.form.fuel_cost_liter = value.cost_lt;
      },
    },
    Vehicle: {
      get() {
        this.form.vehicle_id = this.vehicle.id || null;
        this.form.drum_dispersion = !!this.vehicle.bidon_fuel;
        this.form.mileage_last = this.vehicle.mileage_last || 0;
        this.form.fuel_odometer = this.vehicle.fuel_odometer || 0;
        this.fuel_odometer = this.vehicle.fuel_odometer || 0;
        this.form.fuel_liters = this.vehicle.liters_per_week || 0;
        this.form.ticket_card = this.vehicle.ticket.ticket_card || null;
        return this.vehicle;
      },
      set(value) {
        this.form.vehicle_id = value.id || null;
        this.form.drum_dispersion = !!value.bidon_fuel;
        this.form.mileage_last = value.mileage_last || 0;
        this.form.fuel_odometer = value.fuel_odometer || 0;
        this.fuel_odometer = value.fuel_odometer || 0;
        this.form.fuel_liters = value.liters_per_week || 0;
        this.form.ticket_card = value.ticket.ticket_card || null;
        this.vehicle = value;
      },
    },
    color() {
      if (this.form.fuel_liters < 25) return "green";
      if (this.form.fuel_liters < 50) return "teal";
      if (this.form.fuel_liters < 100) return "indigo";
      if (this.form.fuel_liters < 150) return "orange";
      return "red";
    },
  },
  watch: {
    "form.reason_dispersal": _.debounce(function (v) {
      console.log(v);
      if (v == "Solicitud Semanal") {
        var d = new Date();
        d.setDate(d.getDate() + ((((7 - d.getDay()) % 7) + 1) % 7));
        this.form.date_to_disperse = d.toISOString().substr(0, 10);
      } else {
        this.form.date_to_disperse = new Date(
          Date.now() - new Date().getTimezoneOffset() * 60000
        )
          .toISOString()
          .substr(0, 10);
      }
    }, 999),
  },
};
</script>
<style lang=""></style>
