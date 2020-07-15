<template>
  <div class="component-wrap">
    <v-card>
      <v-form v-model="valid" ref="gpsChipFormAdd" lazy-validation>
        <v-container grid-list-md>
          <v-layout row wrap>
             <v-flex xs12>
              <div class="body-2">CHIP GPS Detalle</div>
            </v-flex>
            <v-flex xs12 md4>
              <v-text-field
                v-model="sim"
                label="SIM"
                :rules="rules"
              ></v-text-field>
            </v-flex>
            <v-flex xs12 md4>
              <v-text-field
                v-model="imei"
                label="IMEI"
                :rules="rules"
              ></v-text-field>
            </v-flex>
            <v-flex xs12 md4>
              <v-text-field
                v-model="cuenta"
                label="Cuenta"
              ></v-text-field>
            </v-flex>
            <v-flex xs12 md4>
              <v-text-field
                v-model="costo"
                label="Costo:"
                :rules="rules"
              ></v-text-field>
            </v-flex>
            <v-flex xs12>
              <v-btn block @click="save()" :disabled="!valid" color="primary"
                >Guardar Nuevo</v-btn
              >
            </v-flex>
          </v-layout>
        </v-container>
      </v-form>
    </v-card>
  </div>
</template>

<script>
export default {
  data() {
    return {
      valid: false,
      isLoading: false,
      sim: "",
      imei: "",
      costo: "",
      cuenta: "",
      rules: [v => !!v || "Campo Requerido"],
    };
  },
  methods: {
    save() {
      const self = this;

      let payload = {
        sim: self.sim,
        imei: self.imei,
        cuenta: self.cuenta,
        costo: self.costo
      };

      self.isLoading = true;

      axios
        .post("/admin/gps-chips/", payload)
        .then(function(response) {
          self.$store.commit("showSnackbar", {
            message: response.data.message,
            color: "success",
            duration: 3000
          });

          self.$eventBus.$emit("GPS_CHIP_ADDED");
        })
        .catch(function(error) {
          if (error.response) {
            self.$store.commit("showSnackbar", {
              message: error.response.data.message,
              color: "error",
              duration: 3000
            });
          } else if (error.request) {
            console.log(error.request);
          } else {
            console.log("Error", error.message);
          }
        })
        .finally(function() {
          self.isLoading = false;
        });
    }
  }
};
</script>