<template>
  <div class="component-wrap">
    <!-- form -->
    <v-card>
      <v-card-title>
        <v-icon>mdi-car</v-icon> Registrar Vehiculo a la Flotilla
      </v-card-title>
      <v-divider class="mb-2"></v-divider>
      <v-container grid-list-md>
        <v-form v-model="valid" ref="vechicleForm" lazy-validation>
          <v-row>
            <v-col cols="12" md="4">
              <v-text-field
                v-model="Vehicle.matricula"
                :rules="[(v) => !!v || 'Valor Requerido']"
                label="Matricula"
                required
              ></v-text-field>
            </v-col>

            <v-col cols="12" md="4">
              <v-text-field
                v-model="Vehicle.modelo"
                :rules="[(v) => !!v || 'Valor Requerido']"
                label="Modelo"
                required
              ></v-text-field>
            </v-col>

            <v-col cols="12" md="4">
              <v-text-field
                v-model="Vehicle.marca"
                :rules="[(v) => !!v || 'Valor Requerido']"
                label="Marca"
                required
              ></v-text-field>
            </v-col>
            <v-col cols="12" md="4">
              <v-text-field
                v-model="Vehicle.serie"
                :rules="[(v) => !!v || 'Valor Requerido']"
                label="Serie"
                required
              ></v-text-field>
            </v-col>

            <v-col cols="12" md="4">
              <v-text-field
                v-model="Vehicle.ticket_card"
                :rules="[(v) => !!v || 'Valor Requerido']"
                label="Ticket Card"
                required
              ></v-text-field>
            </v-col>

            <v-col cols="12" md="4">
              <v-text-field
                v-model="Vehicle.tipo_combustible"
                :rules="[(v) => !!v || 'Valor Requerido']"
                label="Tipo Combustible"
                required
              ></v-text-field>
            </v-col>
            <v-col cols="12" md="4">
              <v-text-field
                v-model="Vehicle.ultimo_kilometraje"
                :rules="[(v) => !!v || 'Valor Requerido']"
                label="Kilometraje"
                required
              ></v-text-field>
            </v-col>
            <v-col cols="12" md="4">
              <v-text-field
                v-model="Vehicle.capacidad_tanque"
                :rules="[(v) => !!v || 'Valor Requerido']"
                label="Capacidad max Tanque (lts)"
                required
              ></v-text-field>
            </v-col>
            <v-col cols="12" md="4">
              <v-autocomplete
                v-model="Vehicle.sucursal"
                :items="options.agencies"
                item-text="title"
                item-value="id"
                label="Agencia"
                placeholder="Agencia a cual correponde."
              ></v-autocomplete>
            </v-col>

            <v-col cols="12" md="4">
              <v-autocomplete
                v-model="Vehicle.responsable"
                :items="options.users"
                item-text="name"
                item-value="id"
                label="Responsable Unidad"
                placeholder="Usuario a quien correcponde la unidad."
              ></v-autocomplete>
            </v-col>
          </v-row>
          <v-col cols="12">
            <v-btn
              @click="update()"
              :loading="isLoading"
              :disabled="!valid || isLoading"
              color="primary"
              dark
              block
            >
              Guardar
            </v-btn>
          </v-col>
        </v-form>
      </v-container>
    </v-card>
    <!-- /form -->
  </div>
</template>

<script>
export default {
  props: {
    propVehicleId: {
      required: true,
    },
  },
  data() {
    return {
      valid: false,
      isLoading: false,
      Vehicle: {},
      options: {
        agencies: [],
        users: [],
      },
    };
  },
  mounted() {
    this.$store.commit("setBreadcrumbs", [
      { label: "Flotilla", to: { name: "vehicle.list" } },
      { label: "Registar Vehiculo", name: "" },
    ]);

    this.loadResource(() => {
      this.loadVehicle(() => {});
    });
  },
  methods: {
    update() {
      const self = this;
      if (self.$refs.vechicleForm.validate()) {
        let payload = {};

        axios
          .put("/admin/vehicles/" + self.propVehicleId, self.Vehicle)
          .then(function(response) {
            self.$store.commit("showSnackbar", {
              message: response.data.message,
              color: "success",
              duration: 3000,
            });
            self.$router.push({ name: "vehicle.list" });
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
    loadVehicle(cb) {
      const self = this;

      axios
        .get("/admin/vehicles/" + self.propVehicleId)
        .then(function(response) {
          let Vehicle = response.data.data;
          self.Vehicle = Vehicle;

          (cb || Function)();
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
  },
};
</script>
