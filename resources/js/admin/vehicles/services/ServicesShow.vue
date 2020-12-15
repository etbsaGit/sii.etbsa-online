<template>
  <div class="component-wrap">
    <!-- form -->
    <v-container grid-list-md>
      <!-- <v-form v-model="valid" ref="dispersalForm" lazy-validation> -->
      <!-- <v-row class="align-center"> -->
      <v-col cols="12">
        <v-card class="mx-auto" dark>
          <v-list-item two-line>
            <v-list-item-content>
              <v-list-item-title class="headline">
                Matricula: {{ Service.vehicle.matricula }}
              </v-list-item-title>
              <v-list-item-subtitle>
                {{
                  $appFormatters.formatDate(Service.created_at, "MMMM, DD yyyy")
                }}
              </v-list-item-subtitle>
            </v-list-item-content>
            <v-spacer></v-spacer>
            <v-chip class="ma-2" label>
              {{ Service.estatus.title }}
            </v-chip>
          </v-list-item>

          <v-card-text class="py-0">
            <v-row align="center">
              <v-list-item>
                <v-list-item-icon>
                  <v-icon>mdi-car-cruise-control</v-icon>
                </v-list-item-icon>
                <v-list-item-subtitle>
                  {{ Service.kilometraje_servicio }} km (actual)
                </v-list-item-subtitle>
              </v-list-item>
            </v-row>
          </v-card-text>

          <v-list class="transparent" dense>
            <v-list-item>
              <v-list-item-title>Motivo Servicio</v-list-item-title>
              <v-list-item-subtitle class="text-right">
                {{ Service.motivo }}
              </v-list-item-subtitle>
            </v-list-item>
            <v-list-item>
              <v-list-item-title>Con Cargo a:</v-list-item-title>
              <v-list-item-subtitle class="text-right">
                {{ Service.cargo_a.title }} / {{ Service.con_cargo_a }}
              </v-list-item-subtitle>
            </v-list-item>
            <v-list-item>
              <v-list-item-title>Solicitante:</v-list-item-title>
              <v-list-item-subtitle class="text-right">
                {{ Service.user.name }}
              </v-list-item-subtitle>
            </v-list-item>
          </v-list>

          <v-divider></v-divider>

          <v-card-actions class="justify-end">
            <v-btn
              color="primary"
              @click="change_estatus('service:autorizado')"
            >
              Autorizar
            </v-btn>
            <v-btn
              text
              color="orange"
              @click="change_estatus('service:completado')"
            >
              Completado
            </v-btn>
          </v-card-actions>
        </v-card>
      </v-col>
    </v-container>
    <!-- /form -->
  </div>
</template>

<script>
export default {
  props: {
    propsVehicleServiceId: {
      required: true,
    },
  },
  data() {
    return {
      valid: false,
      isLoading: false,
      Service: {},
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
    this.loadService(() => {});
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
        title: "Confirmacion de Cambio de Estatus",
        message: "¿Seguro en Actualizar el Estatus de la Solicitud de Servicio?",
        okCb: () => {
          axios
            .post(
              "/admin/vehicle-services/" +
                self.propsVehicleServiceId +
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
    loadService(cb) {
      const self = this;
      axios
        .get("/admin/vehicle-services/" + self.propsVehicleServiceId)
        .then(function(response) {
          self.Service = response.data.data;
          // console.log(response.data.data);
          (cb || Function)();
        });
    },
  },
};
</script>
