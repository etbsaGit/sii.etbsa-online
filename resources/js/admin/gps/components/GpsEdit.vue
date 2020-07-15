<template>
  <div class="component-wrap">
    <v-card>
      <v-form v-model="valid" ref="gpsFormEdit" lazy-validation>
        <v-container grid-list-lg>
          <v-row>
            <v-col cols="12" md="9">
              <v-text-field
                label="Nombre GPS"
                v-model="name"
                :rules="fieldRules"
              ></v-text-field>
            </v-col>
            <v-col cols="12" md="3">
              <v-select
                v-model="payment_type"
                :items="options.payment_type"
                label="Tipo de Pago"
              ></v-select>
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
              <v-autocomplete
                v-model="gps_chip"
                clearable
                label="Selecciona Chip GPS"
                :items="propOptionChips"
                item-text="sim"
                item-value="id"
                return-object
              ></v-autocomplete>
            </v-col>
            <v-col cols="6" md="3">
              <v-text-field
                v-if="gps_chip"
                v-model="gps_chip.costo"
                label="Costo Linea"
                readonly
                prefix="$"
                type="Number"
              ></v-text-field>
            </v-col>
          </v-row>
          <v-row>
            <v-col cols="12" md="3">
              <v-text-field
                label="Folio Factura"
                v-model="invoice"
              ></v-text-field>
            </v-col>
            <v-col cols="12" md="3">
              <v-text-field
                label="Importe Factura"
                v-model="amount"
                type="Number"
                prefix="$"
                placeholder="0.00"
              ></v-text-field>
            </v-col>
            <v-col cols="6" md="3">
              <v-select
                v-model="currency"
                :items="options.currency"
                label="Moneda"
              ></v-select>
            </v-col>
            <v-col cols="6" md="3">
              <v-text-field
                v-model="exchange_rate"
                label="Tipo Cambio"
                type="Number"
                prefix="$"
              ></v-text-field>
            </v-col>
          </v-row>
          <v-row>
            <v-col cols="6" md="6">
              <v-text-field
                label="Fecha Instalacion"
                v-model="installation_date"
                type="date"
                :rules="fieldRules"
              ></v-text-field>
            </v-col>
            <v-col cols="6" md="6">
              <v-text-field
                label="Fecha Vencimiento"
                v-model="renew_date"
                type="date"
                :rules="fieldRules"
              ></v-text-field>
            </v-col>
            <v-col cols="12">
              <v-textarea
                label="Descripcion:"
                v-model="description"
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
    propGpsId: {
      required: true
    },
    propOptionGroups: {
      required: true
    },
    propOptionChips: {
      required: false
    }
  },
  data() {
    return {
      valid: false,
      fieldRules: [v => !!v || "Campo requerido"],
      name: "",
      gps_group_id: null,
      gps_chip: {},
      cost: null,
      amount: null,
      invoice: null,
      installation_date: null,
      renew_date: null,
      description: "",
      currency: "MXN",
      exchange_rate: 1,
      payment_type: "CONTADO",
      options: {
        currency: ["MXN", "USD"],
        payment_type: ["CARGO", "CONTADO", "CREDITO"]
      }
    };
  },
  mounted() {
    const self = this;
    self.loadGpsChips(() => {});
    self.loadGps(() => {});
  },
  methods: {
    save() {
      const self = this;
      if (self.$refs.gpsFormEdit.validate()) {
        let payload = {
          name: self.name,
          amount: self.amount,
          invoice: self.invoice,
          installation_date: self.installation_date,
          renew_date: self.renew_date,
          currency: self.currency,
          exchange_rate: self.exchange_rate,
          description: self.description,
          gps_group_id: self.gps_group_id,
          gps_chip_id: self.gps_chip.id,
          payment_type: self.payment_type,
          uploaded_by: LSK_APP.AUTH_USER.id
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
        self.amount = Gps.amount;
        self.invoice = Gps.invoice;
        self.installation_date = self.$appFormatters.formatDate(
          Gps.installation_date,
          "yyyy-MM-DD"
        );
        self.renew_date = self.$appFormatters.formatDate(
          Gps.renew_date,
          "yyyy-MM-DD"
        );
        self.currency = Gps.currency;
        self.exchange_rate = Gps.exchange_rate;
        self.description = Gps.description;
        self.gps_group_id = Gps.gps_group_id;
        self.gps_chip = Gps.chip;
        self.payment_type = Gps.payment_type;
        cb();
      });
    },
    loadGpsChips(cb) {
      const self = this;
      let params = {
        per_page: -1
      };

      axios
        .get("/admin/gps-chips", { params: params })
        .then(function(response) {
          self.propOptionChips = response.data.data.data;
          cb();
        });
    }
  }
};
</script>