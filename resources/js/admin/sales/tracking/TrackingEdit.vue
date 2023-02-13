<template>
  <v-card>
    <v-card-title class="grey lighten-4">
      <v-icon left>mdi-file</v-icon> Editar Segumiento
      <v-spacer></v-spacer>
      <v-btn :disabled="!valid" color="primary" @click="update">
        Editar Seguimiento Folio {{ propTrackingId.toString().padStart(5, 0) }}
      </v-btn>
    </v-card-title>
    <v-divider></v-divider>
    <v-card-text>
      <tracking-form ref="form" :form.sync="form"></tracking-form>
    </v-card-text>
    <!-- <v-card-actions>
      <v-btn :disabled="!valid" color="primary" @click="update">
        Crear Seguimiento
      </v-btn>
    </v-card-actions> -->
  </v-card>
</template>

<script>
import TrackingForm from "./TrackingForm.vue";
export default {
  name: "TrackingEdit",
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
    const _this = this;
    _this.loadTracking(() => {});
  },
  methods: {
    async update() {
      if (!this.$refs.form.$refs.form.validate()) return;
      const _this = this;
      await axios
        .put("/admin/tracking/" + _this.propTrackingId, _this.form)
        .then(function (response) {
          _this.$store.commit("showSnackbar", {
            message: response.data.message,
            color: "success",
            duration: 3000,
          });
          _this.$router.push({
            name: "tracking.prospect",
            params: { propTrackingId: _this.propTrackingId },
          });

          // _this.$router.go(-1);
        })
        .catch(function (error) {
          _this.$store.commit("hideLoader");

          if (error.response) {
            _this.$store.commit("showSnackbar", {
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
    async loadTracking(cb) {
      const _this = this;
      await axios
        .get("/admin/tracking/" + _this.propTrackingId + "/edit")
        .then(function (response) {
          _this.form = { ...response.data.data };
          _this.$store.commit("showSnackbar", {
            message: response.data.message,
            color: "success",
            duration: 3000,
          });
        })
        .catch(function (error) {
          _this.$store.commit("hideLoader");

          if (error.response) {
            _this.$store.commit("showSnackbar", {
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
      cb();
    },
  },
};
</script>
