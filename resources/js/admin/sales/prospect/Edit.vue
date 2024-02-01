<template>
  <div class="component-wrap">
    <v-card>
      <v-card-title>
        <v-icon>mdi-account-box-outline</v-icon> Registrar Nuevo Prospecto
      </v-card-title>
      <v-card-text>
        <form-prospect
          v-if="form"
          :form.sync="form"
          @submit="update()"
        ></form-prospect>
      </v-card-text>
    </v-card>
  </div>
</template>

<script>
import FormProspect from './forms/Prospect';
export default {
  components: { FormProspect },
  props: {
    propProspectId: {
      required: true,
      type: [Number, String],
    },
  },
  data() {
    return {
      form: null,
    };
  },
  mounted() {
    const self = this;
    self.loadProspect(() => {});
    self.$store.commit('setBreadcrumbs', [
      { label: 'Prospectos', to: { name: 'prospect.list' } },
      { label: 'Editar', name: '' },
    ]);
  },
  methods: {
    update() {
      const self = this;
      axios
        .put('/admin/prospects/' + self.propProspectId, self.form)
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
    loadProspect() {
      const self = this;

      axios
        .get('/admin/prospects/' + self.propProspectId)
        .then(function(response) {
          let prospect = response.data.data;
          let estate_id = prospect.township
            ? prospect.township.estate_id
            : null;
          self.form = {
            ...prospect,
            estate_id: estate_id,
          };
        });
    },
  },
};
</script>
