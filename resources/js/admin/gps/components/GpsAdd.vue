<template>
  <div class="component-wrap">
    <v-card>
      <v-form v-model="valid" ref="gpsFormEdit" lazy-validation>
        <v-container grid-list-lg>
          <v-row>
            <v-col cols="12">
              <v-text-field
                label="Nombre GPS"
                v-model="name"
                :rules="fieldRules"
              ></v-text-field>
            </v-col>
            <v-col cols="12" md="6">
              <v-autocomplete
                v-model="gps_group_id"
                clearable
                label="Selecciona Cliente GPS"
                :items="propOptionGroups"
                item-text="name"
                item-value="id"
              ></v-autocomplete>
            </v-col>
            <v-col cols="6" md="3">
              <v-text-field
                label="SIM"
                v-model="sim"
                :rules="fieldRules"
              ></v-text-field>
            </v-col>
            <v-col cols="6" md="3">
              <v-text-field label="Costo Linea" readonly prefix="$" type="Number"></v-text-field>
            </v-col>
          </v-row>
          <v-row>
            <v-col cols="12" md="3">
              <v-text-field
                label="Folio Factura"
                :rules="fieldRules"
              ></v-text-field>
            </v-col>
            <v-col cols="12" md="3">
              <v-text-field
                label="Importe Factura"
                v-model="amount"
                type="Number"
                :rules="fieldRules"
                prefix="$"
                placeholder="0.00"
              ></v-text-field>
            </v-col>
            <v-col cols="6" md="3">
              <v-select v-model="currency" :items="options.currency" label="Moneda"></v-select>
            </v-col>
            <v-col cols="6" md="3">
              <v-text-field v-model="exchange_rate" label="Tipo Cambio" type="Number" prefix="$"></v-text-field>
            </v-col>
          </v-row>
          <v-row>
            <v-col cols="6" md="6">
              <v-text-field
                label="Fecha Instalacion"
                v-model="activation_date"
                type="date"
                :rules="fieldRules"
              ></v-text-field>
            </v-col>
            <v-col cols="6" md="6">
              <v-text-field
                label="Fecha Vencimiento"
                v-model="due_date"
                type="date"
                :rules="fieldRules"
                readonly
              ></v-text-field>
            </v-col>
            <v-col cols="12">
              <v-textarea
                label="Descripcion:"
                v-model="comment"
                outlined
              ></v-textarea>
            </v-col>
          </v-row>
          <v-row>
            <v-btn block @click="save()" :disabled="!valid" color="primary">
              Registrar Nuevo
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
    propOptionGroups: {
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
      gps_group_id: null,
      cost: null,
      amount: null,
      activation_date: null,
      due_date: null,
      comment: "",
      currency: "MXN",
      exchange_rate: 1,
      options: {
        currency: ["MXN", "USD"]
      }
    };
  },
  mounted() {
    const self = this;
    self.$refs.gpsFormEdit.reset();
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
          comment: self.comment,
          uploaded_by: LSK_APP.AUTH_USER.id
        };
        axios
          .post("/admin/gps", payload)
          .then(function(response) {
            self.$store.commit("showSnackbar", {
              message: response.data.message,
              color: "success",
              duration: 3000
            });

            self.$eventBus.$emit("GPS_ADDED");
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
    }
  }
};
</script>