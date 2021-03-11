<template>
  <v-card flat max-width="600">
    <v-form ref="form" v-model="valid" lazy-validation>
      <gps-invoice :form.sync="form"></gps-invoice>
    </v-form>
    <v-card-actions>
      <v-spacer></v-spacer>
      <v-btn color="red darken-1" block dark :disabled="!valid" @click="save">
        Facturar
      </v-btn>
    </v-card-actions>
  </v-card>
</template>

<script>
import GpsInvoice from '../forms/GpsInvoice.vue';

export default {
  components: { GpsInvoice },
  props: {
    propGpsId: {
      required: true,
      type: [String, Number],
    },
  },
  data() {
    return {
      valid: false,

      form: {
        invoice: null,
        invoice_date: null,
        amount: 0.0,
        currency: 'MXN',
        exchange_rate: 1,
      },
    };
  },
  methods: {
    save() {
      if (!this.$refs.form.validate()) return;
      const self = this;
      axios
        .put(`/admin/gps/${self.propGpsId}/invoice`, self.form)
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
  },
};
</script>

<style></style>
