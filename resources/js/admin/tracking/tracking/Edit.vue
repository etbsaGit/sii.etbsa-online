<template>
  <v-card>
    <v-card-title>
      <v-icon>mdi-account</v-icon> Editar el Seguimiento
    </v-card-title>
    <v-divider class="mb-3"></v-divider>
    <v-form ref="form" v-model="valid" lazy-validation>
      <tracking-form :form.sync="form" editing></tracking-form>
    </v-form>
    <v-card-actions>
      <v-btn
        :disabled="!valid"
        color="success"
        class="mr-4"
        @click="submit"
        block
      >
        Modificar
      </v-btn>
    </v-card-actions>
  </v-card>
</template>

<script>
import TrackingForm from '@admin/tracking/tracking/forms/Tracking.vue';
export default {
  components: { TrackingForm },
  props: {
    propTrackingId: {
      required: false,
      type: [String, Number],
    },
  },
  data() {
    return {
      valid: true,
      form: {
        price: null,
        title: null,
        currency: null,
        reference: null,
        agency_id: null,
        prospect_id: null,
        attended_by: null,
        department_id: null,
        first_contact: null,
        description_topic: null,
        tracking_condition: null,
      },
    };
  },
  mounted() {
    const self = this;
    self.loadTracking(() => {});
  },
  methods: {
    submit() {
      if (!this.$refs.form.validate()) return;
      const self = this;
      axios
        .put('/admin/tracking/' + self.propTrackingId, self.form)
        .then(function(response) {
          self.$store.commit('showSnackbar', {
            message: response.data.message,
            color: 'success',
            duration: 3000,
          });

          self.$router.go(-1);
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
    },
    loadTracking(cb) {
      const self = this;
      axios
        .get('/admin/tracking/' + self.propTrackingId + '/edit')
        .then(function(response) {
          self.form = { ...response.data.data };
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
  },
};
</script>
