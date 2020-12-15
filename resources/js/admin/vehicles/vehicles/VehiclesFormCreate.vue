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
                v-model="form.matricula"
                :rules="[(v) => !!v || 'Valor Requerido']"
                label="Matricula"
                required
              ></v-text-field>
            </v-col>

            <v-col cols="12" md="4">
              <v-text-field
                v-model="form.modelo"
                :rules="[(v) => !!v || 'Valor Requerido']"
                label="Modelo"
                required
              ></v-text-field>
            </v-col>

            <v-col cols="12" md="4">
              <v-text-field
                v-model="form.marca"
                :rules="[(v) => !!v || 'Valor Requerido']"
                label="Marca"
                required
              ></v-text-field>
            </v-col>
            <v-col cols="12" md="4">
              <v-text-field
                v-model="form.serie"
                :rules="[(v) => !!v || 'Valor Requerido']"
                label="Serie"
                required
              ></v-text-field>
            </v-col>

            <v-col cols="12" md="4">
              <v-text-field
                v-model="form.ticket_card"
                :rules="[(v) => !!v || 'Valor Requerido']"
                label="Ticket Card"
                required
              ></v-text-field>
            </v-col>

            <v-col cols="12" md="4">
              <v-text-field
                v-model="form.tipo_combustible"
                :rules="[(v) => !!v || 'Valor Requerido']"
                label="Tipo Combustible"
                required
              ></v-text-field>
            </v-col>
            <v-col cols="12" md="4">
              <v-text-field
                v-model="form.ultimo_kilometraje"
                :rules="[(v) => !!v || 'Valor Requerido']"
                label="Kilometraje"
                required
              ></v-text-field>
            </v-col>
            <v-col cols="12" md="4">
              <v-text-field
                v-model="form.capacidad_tanque"
                :rules="[(v) => !!v || 'Valor Requerido']"
                label="Capacidad max Tanque (lts)"
                required
              ></v-text-field>
            </v-col>

            <v-col cols="12" md="4">
              <v-autocomplete
                v-model="form.sucursal"
                :items="options.agencies"
                item-text="title"
                item-value="id"
                label="Agencia"
                placeholder="Agencia a cual correponde."
              ></v-autocomplete>
            </v-col>

            <v-col cols="12" md="4">
              <v-autocomplete
                v-model="form.responsable"
                :items="options.users"
                item-text="name"
                item-value="id"
                label="Responsable Unidad"
                placeholder="Usuario a quien correcponde la unidad."
              ></v-autocomplete>
            </v-col>
            <v-col cols="12" md="4">
              <v-text-field
                v-model="form.ultimo_kilometraje_servicio"
                :rules="[(v) => !!v || 'Valor Requerido']"
                label="Kilometraje ultimo Servicio"
                required
              ></v-text-field>
            </v-col>
            <v-col cols="12" md="4">
              <v-text-field
                v-model="form.rango_kilometros_servicio"
                :rules="[(v) => !!v || 'Valor Requerido']"
                label="Ranfo Kilometros para Servicio"
                required
              ></v-text-field>
            </v-col>
          </v-row>
          <v-col cols="12">
            <v-btn
              @click="save()"
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
  data() {
    return {
      valid: false,
      isLoading: false,
      form: {
        matricula: "QAWS",
        modelo: "Saveiro",
        marca: "VW",
        serie: "s662-jbsu-hb782-jb",
        ticket_card: "7889",
        tipo_combustible: "magna",
        ultimo_kilometraje: 20000,
        ultimo_kilometraje_servicio: 10000,
        rango_kilometros_servicio: 15000,
        capacidad_tanque: 80,
        sucursal: 1,
        responsable: 1,
      },
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
    this.loadResource(() => {});
  },
  methods: {
    save() {
      const self = this;
      if (self.$refs.vechicleForm.validate()) {
        let payload = {};

        axios
          .post("/admin/vehicles", self.form)
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
