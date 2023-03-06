<template>
  <v-card flat>
    <v-card-text>
      <tracking-form ref="form" :form.sync="form"></tracking-form>
    </v-card-text>
    <v-card-actions>
      <v-btn :disabled="!valid" color="primary" @click="save" block dark>
        Crear Seguimiento
      </v-btn>
    </v-card-actions>
  </v-card>
</template>

<script>
import TrackingForm from "./TrackingForm.vue";
export default {
  components: { TrackingForm },
  props: {
    propProspectId: {
      type: [String, Number],
      required: false,
    },
  },
  data() {
    return {
      valid: true,
      form: {
        prospect_id: null,
        title: null,
        reference: null,
        product: null,
        price: null,
        currency: 1,
        currency_id: 1,
        agency_id: null,
        department_id: null,
        seller_id: null,
        attended_by: null,
        assertiveness: 0.01,
        tracking_condition: "Por definir",
        first_contact: "Online",
        description_topic: null,
      },
    };
  },
  mounted() {
    if (this.propProspectId) {
      this.form.prospect_id = this.propProspectId;
    }
  },
  methods: {
    async save() {
      if (!this.$refs.form.$refs.form.validate()) return;
      const _this = this;
      _this.isLoading = true;

      // const params = {
      //   ..._this.form,
      //   reference: _this.reference.name,
      // };

      await axios
        .post("/admin/tracking", this.form)
        .then(function (response) {
          _this.$store.commit("showSnackbar", {
            message: response.data.message,
            color: "success",
            duration: 3000,
          });

          // reset
          _this.isLoading = false;
          _this.$emit("success");
          // self.$router.push({ name: "tracking.list" });
        })
        .catch(function (error) {
          _this.isLoading = false;
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
  },
};
</script>
