<template>
  <div class="component-wrap">
    <v-card>
      <v-form v-model="valid" ref="gpsChipFormEdit" lazy-validation>
        <v-container grid-list-md>
          <v-row>
            <v-col cols="12" md="4">
              <v-text-field
                v-model="sim"
                label="SIM"
                :rules="rules"
              ></v-text-field>
            </v-col>
            <v-col cols="12" md="4">
              <v-text-field v-model="imei" label="IMEI" counter></v-text-field>
            </v-col>
            <v-col cols="12" md="4">
              <v-text-field
                v-model="cuenta"
                label="Cuenta"
                counter
              ></v-text-field>
            </v-col>
            <v-col cols="12" md="4">
              <v-text-field
                v-model="costo"
                label="Costo:"
                :rules="rules"
                type="Numeric"
                prefix="$"
                suffix="MXN"
              ></v-text-field>
            </v-col>

            <v-col cols="6" md="4">
              <v-text-field
                label="Fecha Activacion"
                v-model="fecha_activacion"
                type="date"
                :rules="rules"
              ></v-text-field>
            </v-col>
            <v-col cols="6" md="4">
              <v-text-field
                label="Fecha Renovacion"
                v-model="fecha_renovacion"
                type="date"
                :rules="rules"
              ></v-text-field>
            </v-col>
            <v-col cols="12">
              <v-textarea
                label="Descripcion:"
                v-model="description"
                outlined
              ></v-textarea>
            </v-col>

            <v-col cols="12">
              <v-btn block @click="save()" :disabled="!valid" color="primary"
                >Guardar Nuevo</v-btn
              >
            </v-col>
          </v-row>
        </v-container>
      </v-form>
    </v-card>
  </div>
</template>

<script>
export default {
  props: {
    propGpsChipId: {
      required: true,
    },
  },
  data() {
    return {
      valid: false,
      sim: '',
      imei: '',
      costo: '',
      cuenta: '',
      fecha_activacion: null,
      fecha_renovacion: null,
      descripcion: '',
      rules: [(v) => !!v || 'Campo Requerido'],
    };
  },
  mounted() {
    const self = this;
    self.loadGpsChip(() => {});
  },
  methods: {
    save() {
      const self = this;
      if (self.$refs.gpsChipFormEdit.validate()) {
        let payload = {
          sim: self.sim,
          imei: self.imei,
          cuenta: self.cuenta,
          costo: self.costo,
          fecha_activacion: self.fecha_activacion,
          fecha_renovacion: self.fecha_renovacion,
          descripcion: self.descripcion,
        };

        self.isLoading = true;

        axios
          .put('/admin/chips/' + self.propGpsChipId, payload)
          .then(function(response) {
            self.$store.commit('showSnackbar', {
              message: response.data.message,
              color: 'success',
              duration: 3000,
            });

            self.$eventBus.$emit('GPS_CHIP_UPDATED');
          })
          .catch(function(error) {
            if (error.response) {
              self.$store.commit('showSnackbar', {
                message: error.response.data.message,
                color: 'error',
                duration: 3000,
              });
            } else if (error.request) {
              console.log(error.request);
            } else {
              console.log('Error', error.message);
            }
          })
          .finally(function() {
            self.isLoading = false;
          });
      }
    },
    loadGpsChip(cb) {
      const self = this;

      axios.get('/admin/chips/' + self.propGpsChipId).then(function(response) {
        let Chip = response.data.data;

        self.sim = Chip.sim;
        self.imei = Chip.imei;
        self.cuenta = Chip.cuenta;
        self.costo = Chip.costo;
        self.fecha_activacion = self.$appFormatters.formatDate(
          Chip.fecha_activacion,
          'yyyy-MM-DD'
        );
        self.fecha_renovacion = self.$appFormatters.formatDate(
          Chip.fecha_renovacion,
          'yyyy-MM-DD'
        );
        self.descripcion = Chip.descripcion;
        cb();
      });
    },
  },
};
</script>
