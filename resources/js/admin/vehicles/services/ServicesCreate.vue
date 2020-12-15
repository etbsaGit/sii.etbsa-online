<template>
  <div class="component-wrap">
    <!-- form -->
    <v-card>
      <v-card-title>
        <v-icon>mdi-settings</v-icon>
        Solicitar Servicio a Vehiculo
        <span v-if="!!vehicle.user.name">
          , Responsable: {{ vehicle.user.name }}
        </span>
      </v-card-title>
      <v-divider class="mb-2"></v-divider>
      <v-container grid-list-md>
        <v-form v-model="valid" ref="vehicleServiceForm" lazy-validation>
          <v-row class="align-center">
            <v-col cols="12" md="6">
              <v-autocomplete
                v-model="vehicle"
                :items="options.vehicles"
                label="Vehiculo Matricula"
                :rules="[(v) => !!v || 'Valor Requerido']"
                item-text="matricula"
                :hint="vehicle.serie"
                persistent-hint
                return-object
                required
              ></v-autocomplete>
            </v-col>

            <v-col cols="12" md="6">
              <v-text-field
                v-model="form.kilometraje_servicio"
                :rules="[
                  (v) => !!v || 'Valor Requerido',
                  (v) =>
                    v > vehicle.ultimo_kilometraje ||
                    `Valor debe ser mayor al utimo Kilometraje (${vehicle.ultimo_kilometraje} kms)`,
                ]"
                label="Kilometraje Actual"
                suffix="KM"
                type="numeric"
                :hint="`Ultimo Kilometraje Servicio ${vehicle.ultimo_kilometraje_servicio} kms`"
                persistent-hint
                required
              ></v-text-field>
            </v-col>

            <v-col cols="12" md="6">
              <span>
                Cargo a Sucursal y/o departamento
              </span>
              <v-divider></v-divider>
              <v-autocomplete
                v-model="form.suc_cargo"
                :items="options.agencies"
                item-text="title"
                item-value="id"
                label="Agencia"
                placeholder="Con Cargo A"
                filled
              ></v-autocomplete>
              <v-text-field
                v-model="form.con_cargo_a"
                label="Departamento"
                placeholder="Con Cargo A"
                filled
              ></v-text-field>
            </v-col>
            <v-col cols="12" md="6">
              <v-textarea
                v-model="form.motivo"
                :rules="[(v) => !!v || 'Valor Requerido']"
                label="Motivo"
                required
                filled
              ></v-textarea>
            </v-col>

            <v-col cols="12" md="4">
              <!-- <v-file-input
                accept="image/png, image/jpeg, image/bmp"
                placeholder="Foto de tacometro (opcional)"
                prepend-icon="mdi-camera"
                label="Tacometro Medidor"
              ></v-file-input> -->
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
  props: {
    propsVehicleId: {
      required: false,
    },
  },
  data() {
    return {
      valid: false,
      isLoading: false,
      vehicle: {
        serie: "",
        ultimo_kilometraje: 0,
        capacidad_tanque: 0,
        user: {
          name: "",
        },
      },
      form: {
        kilometraje_servicio: null,
        motivo: "La unidad ya Requiere el Servicio por el Kilometraje",
        con_cargo_a: "Sistemas",
        suc_cargo: null,
      },
      options: {
        agencies: [],
        users: [],
        vehicles: [],
      },
    };
  },
  mounted() {
    this.$store.commit("setBreadcrumbs", [
      { label: "Flotilla", to: { name: "vehicle.list" } },
      { label: "Disperciones", to: { name: "vehicle.dispersion.list" } },
      { label: "Solicitar Dispercion", name: "" },
    ]);
    this.loadResource(() => {});
    this.loadVehicles(() => {
      if (this.propsVehicleId) {
        this.loadVehicle(() => {});
      }
    });
  },
  methods: {
    save() {
      const self = this;
      if (self.$refs.vehicleServiceForm.validate()) {
        const payload = {
          vehicle_id: self.vehicle.id,
          kilometraje_anterior: self.vehicle.ultimo_kilometraje,
          ...self.form,
        };
        axios
          .post("/admin/vehicle-services", payload)
          .then(function(response) {
            self.$store.commit("showSnackbar", {
              message: response.data.message,
              color: "success",
              duration: 3000,
            });
            self.$router.push({ name: "vehicle.services.list" });
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
    loadVehicle(cb) {
      const self = this;

      axios
        .get("/admin/vehicles/" + self.propsVehicleId)
        .then(function(response) {
          let Vehicle = response.data.data;
          self.vehicle = Vehicle;

          (cb || Function)();
        });
    },
    loadVehicles(cb) {
      const self = this;

      let params = {
        paginate: "no",
      };

      axios.get("/admin/vehicles", { params: params }).then(function(response) {
        self.options.vehicles = response.data.data;
        (cb || Function)();
      });
    },
  },
};
</script>
