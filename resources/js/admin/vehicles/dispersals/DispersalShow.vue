<template>
  <!-- form -->
  <v-container>
    <!-- <v-form v-model="valid" ref="dispersalForm" lazy-validation> -->
    <!-- <v-row class="align-center"> -->
    <v-col cols="12">
      <v-card class="mx-auto" dark max-width="700">
        <v-list-item two-line>
          <v-list-item-content>
            <v-list-item-title class="headline">
              Matricula: {{ Dispersal.vehicle.matricula }}
            </v-list-item-title>
            <v-list-item-subtitle>
              {{
                $appFormatters.formatDate(Dispersal.created_at, "MMMM, DD yyyy")
              }}
            </v-list-item-subtitle>
          </v-list-item-content>
          <v-spacer></v-spacer>
          <v-chip class="ma-2" label>
            {{ Dispersal.estatus.title }}
          </v-chip>
        </v-list-item>

        <v-card-text class="py-0">
          <v-row align="center">
            <v-col
              class="display-3 text-center elevation-5"
              cols="12"
              xs="12"
              md="4"
            >
              <v-edit-dialog
                :return-value.sync="Dispersal.gas_lts"
                large
                persistent
                save-text="Cambiar"
              >
                {{ Dispersal.gas_lts }} Lts
                <template v-slot:input>
                  <v-text-field v-model.lazy="Dispersal.gas_lts">
                  </v-text-field>
                </template>
              </v-edit-dialog>
            </v-col>
            <v-col cols="12" xs="12" md="8">
              <v-list-item>
                <v-list-item-icon>
                  <v-icon>mdi-car-cruise-control</v-icon>
                </v-list-item-icon>
                <v-list-item-subtitle>
                  {{ Dispersal.kilometraje_anterior }} km (anterior)
                </v-list-item-subtitle>
              </v-list-item>
              <v-list-item>
                <v-list-item-icon>
                  <v-icon>mdi-car-cruise-control</v-icon>
                </v-list-item-icon>
                <v-list-item-subtitle>
                  {{ Dispersal.kilometraje_actual }} km (actual)
                </v-list-item-subtitle>
              </v-list-item>

              <v-list-item>
                <v-list-item-icon>
                  <v-icon>mdi-car-coolant-level</v-icon>
                </v-list-item-icon>
                <v-list-item-subtitle>
                  <v-progress-linear
                    color="green lighten-1"
                    height="10"
                    :value="Dispersal.odometro_percent_gas"
                  >
                    <strong class="caption"
                      >{{ Dispersal.odometro_percent_gas }}%</strong
                    >
                  </v-progress-linear>
                </v-list-item-subtitle>
              </v-list-item>
            </v-col>
          </v-row>
        </v-card-text>

        <v-list class="transparent text-uppercase" dense>
          <v-list-item>
            <v-list-item-title>Destino / Motivo</v-list-item-title>
            <v-list-item-subtitle class="text-right">
              <span class="overline"> {{ Dispersal.destino }} </span>,
              {{ Dispersal.motivo }}
            </v-list-item-subtitle>
          </v-list-item>
          <v-list-item>
            <v-list-item-title>Con Cargo a:</v-list-item-title>
            <v-list-item-subtitle class="text-right">
              {{ Dispersal.cargo_a.title }} / {{ Dispersal.con_cargo_a }}
            </v-list-item-subtitle>
          </v-list-item>
          <v-list-item>
            <v-list-item-title>Tarjeta:</v-list-item-title>
            <v-list-item-subtitle class="text-right">
              {{ Dispersal.tarjeta }}
            </v-list-item-subtitle>
          </v-list-item>
          <v-list-item>
            <v-list-item-title>Solicitante:</v-list-item-title>
            <v-list-item-subtitle class="text-right">
              {{ Dispersal.user.name }}
            </v-list-item-subtitle>
          </v-list-item>
        </v-list>

        <v-divider></v-divider>

        <v-card-actions class="justify-end">
          <v-btn
            color="primary"
            @click="change_estatus('dispersal:autorizado')"
          >
            Autorizar
          </v-btn>
          <v-btn
            text
            color="error"
            @click="change_estatus('dispersal:rechazada')"
          >
            Denegar
          </v-btn>
          <v-btn
            text
            color="orange"
            @click="change_estatus('dispersal:dispersado')"
          >
            Dispersado
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-col>
    <!-- <v-col cols="4">
          <v-card>
            <v-card-title>Acciones</v-card-title>
            <v-row>
              <div class="flex-grow-1 pa-2">
                <v-btn color="primary">button</v-btn>
              </div>
              <div class="flex-grow-1 pa-2">
                <v-btn color="error">button</v-btn>
              </div>
              <div class="flex-grow-1 pa-2">
                <v-btn color="orange">button</v-btn>
              </div>
            </v-row>
          </v-card>
        </v-col> -->
    <!-- </v-row> -->
    <!-- </v-form> -->
  </v-container>
  <!-- /form -->
</template>

<script>
export default {
  props: {
    propsVehicleDispersalId: {
      required: true,
    },
  },
  data() {
    return {
      valid: false,
      isLoading: false,
      Dispersal: {},
      form: {
        estatus: null,
        kilometraje_actual: 152999,
        odometro_percent_gas: 30,
        tarjeta: "798",
        destino: "Silao",
        motivo: "Viaje a Silao",
        con_cargo_a: "Sistemas",
        suc_cargo: null,
        gas_lts: 30,
      },
      options: {
        agencies: [],
        users: [],
        vehicles: [],
      },
    }; 
  },
  created() {
    this.$store.commit("setBreadcrumbs", [
      { label: "Flotilla", to: { name: "vehicle.list" } },
      { label: "Disperciones", name: "vehicle.dispersion.list" },
      { label: "Detalle", name: "" },
    ]);
    this.loadResource(() => {});
    this.loadDispersal(() => {});
  },
  methods: {
    edit() {
      const self = this;
      if (self.$refs.dispersalForm.validate()) {
        const playload = {
          vehicle_id: self.vehicle.id,
          ...self.form,
        };
        axios
          .put("/admin/vehicle-dispersal", playload)
          .then(function(response) {
            self.$store.commit("showSnackbar", {
              message: response.data.message,
              color: "success",
              duration: 3000,
            });
          })
          .catch(function(error) {
            if (error.response) {
              self.$store.commit("showSnackbar", {
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
      }
    },
    change_estatus(key) {
      const self = this;

      self.$store.commit("showDialog", {
        type: "confirm",
        title: "Confimacion de Cambio de Estatus",
        message: "¿Seguro en Actualizar el Estatus de la Dispecion?",
        okCb: () => {
          axios
            .post(
              "/admin/vehicle-dispersal/" +
                self.propsVehicleDispersalId +
                "/estatus",
              { estatus_key: key }
            )
            .then(function(response) {
              self.$store.commit("showSnackbar", {
                message: response.data.message,
                color: "success",
                duration: 3000,
              });
              self.$router.go(-1);
            })
            .catch(function(error) {
              if (error.response) {
                self.$store.commit("showSnackbar", {
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
          console.log("CANCEL");
        },
      });
    },
    loadResource(cb) {
      const self = this;
      axios.get("/admin/resource/agencies").then(function(response) {
        let Data = response.data.data;
        self.options.agencies = Data.agencies;
        (cb || Function)();
      });
      axios.get("/admin/resource/users").then(function(response) {
        let Data = response.data.data;
        self.options.users = Data.users;
        (cb || Function)();
      });
    },
    loadDispersal(cb) {
      const self = this;
      axios
        .get("/admin/vehicle-dispersal/" + self.propsVehicleDispersalId)
        .then(function(response) {
          self.Dispersal = response.data.data;
          (cb || Function)();
        });
    },
  },
};
</script>
