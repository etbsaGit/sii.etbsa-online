<template>
  <div class="component-wrap">
    <v-card>
      <v-form v-model="valid" ref="gpsFormEdit" lazy-validation>
        <v-container grid-list-lg>
          <v-row dense>
            <div class="text-h5">Detalle GPS</div>
          </v-row>
          <v-row dense>
            <v-text-field
              label="Nombre GPS"
              v-model="name"
              :rules="fieldRules"
            ></v-text-field>
          </v-row>
          <v-row dense>
            <v-col cols="12" md="6">
              <v-text-field
                label="SIM"
                v-model="sim"
                :rules="fieldRules"
              ></v-text-field>
            </v-col>
            <v-col cols="12" md="6">
              <v-text-field
                label="IMEI"
                v-model="imei"
                :rules="fieldRules"
              ></v-text-field>
            </v-col>
          </v-row>
          <v-row dense>
            <v-col cols="12" md="3">
              <v-text-field
                label="Costo"
                v-model="cost"
                type="tel"
                :rules="fieldRules"
              ></v-text-field>
            </v-col>
            <v-col cols="12" md="3">
              <v-text-field
                label="Importe"
                v-model="amount"
                type="tel"
                :rules="fieldRules"
              ></v-text-field>
            </v-col>
            <v-col cols="6" md="3">
              <v-text-field label="Moneda"></v-text-field>
            </v-col>
            <v-col cols="6" md="3">
              <v-text-field label="Tipo Cambio"></v-text-field>
            </v-col>
          </v-row>
          <!-- <v-row dense>
          </v-row> -->
          <v-row dense>
            <v-col cols="12" md="6">
              <v-text-field
                label="Fecha Activacion"
                v-model="activation_date"
                type="date"
                :rules="fieldRules"
              ></v-text-field>
            </v-col>
            <v-col cols="12" md="6">
              <v-text-field
                label="Fecha Vencimiento"
                v-model="due_date"
                type="date"
                :rules="fieldRules"
              ></v-text-field>
            </v-col>
          </v-row>
          <v-col cols="12">
            <v-textarea
              label="Comentario:"
              v-model="comment"
              outlined
            ></v-textarea>
          </v-col>
          <v-row>
            <v-btn block @click="save()" :disabled="!valid" color="primary">
              Modificar
            </v-btn>
          </v-row>
        </v-container>
      </v-form>
    </v-card>
  </div>
</template>

<script>
export default {
  props: {
    propGpsId: {
      required: true
    }
  },
  data() {
    return {
      valid: false,
      isLoading: false,
      fieldRules: [v => !!v || "Campo requerido"],
      name: "",
      sim: null,
      imei: null,
      cost: null,
      amount: null,
      activation_date: null,
      due_date: null,
      comment: ""
    };
  },
  mounted() {
    const self = this;
    self.loadGps(() => {});
  },
  methods: {
    save() {
      const self = this;
      if (self.$refs.gpsFormEdit.validate()) {
        let payload = {
          name: self.name,
          sim: self.sim,
          imei: self.imei,
          cost: self.cost,
          amount: self.amount,
          activation_date: self.activation_date,
          due_date: self.due_date,
          currency: self.currency,
          exchange_rate: self.exchange_rate,
          comment: self.comment
        };
        axios
          .put("/admin/gps/" + self.propGpsId, payload)
          .then(function(response) {
            self.$store.commit("showSnackbar", {
              message: response.data.message,
              color: "success",
              duration: 3000
            });

            self.$eventBus.$emit("GPS_UPDATED");
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
          });
      }
    },
    loadGps(cb) {
      const self = this;

      axios.get("/admin/gps/" + self.propGpsId).then(function(response) {
        let Gps = response.data.data;
        self.name = Gps.name;
        self.sim = Gps.sim;
        self.imei = Gps.imei;
        self.cost = Gps.cost;
        self.amount = Gps.amount;
        self.activation_date = self.$appFormatters.formatDate(
          Gps.activation_date,
          "yyyy-MM-DD"
        );
        self.due_date = self.$appFormatters.formatDate(
          Gps.due_date,
          "yyyy-MM-DD"
        );
        self.comment = Gps.comment;
        cb();
      });
    }
  }
};
</script>