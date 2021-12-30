<template>
  <v-card>
    <v-card-title>
      <v-icon>mdi-account</v-icon> Levantar un Seguimiento a Prospecto
      <v-spacer></v-spacer>
      <v-btn :disabled="!valid" color="success" @click="save">
        Guardar
      </v-btn>
    </v-card-title>
    <v-divider class="mb-3"></v-divider>
    <v-form ref="form" v-model="valid" lazy-validation>
      <tracking-form :form.sync="form"></tracking-form>
    </v-form>
  </v-card>
</template>

<script>
import TrackingForm from "@admin/sales/tracking/forms/Tracking.vue";
export default {
  components: { TrackingForm },
  props: {
    propProspectId: {
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
        currency: "MXN",
        reference: null,
        agency_id: null,
        prospect_id: null,
        attended_by: null,
        department_id: null,
        first_contact: "online",
        description_topic: null,
        tracking_condition: "Por definir",
      },
    };
  },
  mounted() {
    if (this.propProspectId) {
      this.form.prospect_id = this.propProspectId;
    }
  },
  methods: {
    save() {
      if (!this.$refs.form.validate()) return;
      const self = this;
      self.isLoading = true;

      axios
        .post("/admin/tracking", this.form)
        .then(function (response) {
          self.$store.commit("showSnackbar", {
            message: response.data.message,
            color: "success",
            duration: 3000,
          });

          // reset
          self.isLoading = false;
          self.$router.push({ name: "tracking.list" });
        })
        .catch(function (error) {
          self.isLoading = false;
          self.$store.commit("hideLoader");

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
    },
  },
};
</script>
