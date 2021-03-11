<template>
  <v-card flat>
    <v-toolbar flat>
      <v-toolbar-title>
        <v-icon>mdi-crosshairs</v-icon> Detalle del GPS
      </v-toolbar-title>
      <v-spacer> </v-spacer>
      <v-btn :disabled="!valid" color="success" class="mr-4" @click="save">
        Editar
        <v-icon right>mdi-pencil</v-icon>
      </v-btn>
    </v-toolbar>
    <v-divider class="mb-3"></v-divider>
    <v-card-text>
      <v-form ref="form" v-model="valid" lazy-validation>
        <gps-form :form.sync="form" :options="options" editing></gps-form>
      </v-form>
    </v-card-text>
    <v-card-text>
      <v-simple-table
        max-height="300"
        fixed-header
        dense
        dark
        class="caption text-uppercase"
      >
        <template v-slot:default>
          <thead>
            <tr>
              <th class="text-left">
                Factura
              </th>
              <th class="text-left">
                Importe Moneda
              </th>

              <th class="text-left">
                Fecha Facturacion
              </th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(item, index) in desserts" :key="index">
              <td>{{ item.calories }}</td>
              <td>{{ item.calories | currency }} {{ item.name }}</td>
              <td>01-01-2020</td>
            </tr>
          </tbody>
        </template>
      </v-simple-table>
    </v-card-text>
  </v-card>
</template>

<script>
import GpsForm from '../forms/GpsForm.vue';
export default {
  components: { GpsForm },
  props: {
    propGpsId: {
      required: true,
      type: [String, Number],
    },
  },

  data() {
    return {
      valid: true,
      form: {},
      options: {
        groups: [],
        chips: [],
        types: ['CONTADO', 'CREDITO', 'CARGO'],
      },
      desserts: [
        {
          name: 'MXN',
          calories: 159,
        },
        {
          name: 'MXN',
          calories: 237,
        },
      ],
    };
  },
  mounted() {
    // this.$store.commit('setBreadcrumbs', [
    //   { label: 'GPS', to: { name: 'gps' } },
    //   { label: 'Detalle', to: { name: '' } },
    // ]);
    this.loadGps(() => {});
    this.loadOptions(() => {});
  },
  methods: {
    save() {
      if (!this.$refs.form.validate()) return;
      const self = this;

      axios
        .put(`/admin/gps/${self.propGpsId}`, self.form)
        .then(function(response) {
          self.$store.commit('showSnackbar', {
            message: response.data.message,
            color: 'success',
            duration: 3000,
          });

          self.$eventBus.$emit('GPS_REFRESH');
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
        });
    },
    loadGps(cb) {
      const self = this;
      axios
        .get('/admin/gps/' + self.propGpsId + '/edit')
        .then(function(response) {
          let Gps = response.data.data;
          self.form = {
            ...Gps,
            installation_date: self.$appFormatters.formatDate(
              Gps.installation_date
            ),
            renew_date: self.$appFormatters.formatDate(Gps.renew_date),
            invoice_date: Gps.invoice_date
              ? self.$appFormatters.formatDate(Gps.invoice_date || '')
              : null,
          };

          self.$store.commit('showSnackbar', {
            message: response.data.message,
            color: 'success',
            duration: 3000,
          });
        })
        .catch(function(error) {
          self.$store.commit('hideLoader');

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
        });
      cb();
    },
    loadOptions(cb) {
      const self = this;
      axios.get('/admin/gps/search/resources').then(function(response) {
        self.options.chips = response.data.data.chips;
        self.options.groups = response.data.data.groups;
        (cb || Function)();
      });
    },
  },
};
</script>
