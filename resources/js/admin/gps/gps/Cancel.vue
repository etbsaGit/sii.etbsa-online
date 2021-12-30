<template>
  <v-card flat class="overline">
    <v-card-text class="py-4">
      <v-form ref="form" v-model="valid" lazy-validation>
        <gps-cancel-form :form.sync="form"></gps-cancel-form>
      </v-form>
    </v-card-text>
    <v-card-actions>
      <v-btn color="red darken-1" block :disabled="!valid" @click="save">
        Dar de Baja el GPS
      </v-btn>
    </v-card-actions>
  </v-card>
</template>

<script>
import GpsCancelForm from "../forms/GpsCancel.vue";

export default {
  components: { GpsCancelForm },
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
        cancellation_date: null,
        description: null,
      },
    };
  },
  methods: {
    save() {
      if (!this.$refs.form.validate()) return;
      const _this = this;
      _this.$store.commit("showDialog", {
        type: "confirm",
        title: "Confirmar Cancelacion",
        message: "¿Seguro en Cancelar el GPS?",
        okCb: () => {
          axios
            .put(`/admin/gps/${_this.propGpsId}/cancel`, _this.form)
            .then(function (response) {
              _this.$store.commit("showSnackbar", {
                message: response.data.message,
                color: "success",
                duration: 3000,
              });

              _this.$eventBus.$emit("GPS_REFRESH");
            })
            .catch(function (error) {
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
        cancelCb: () => {
          console.log("CANCEL");
        },
      });
    },
  },
};
</script>

<style></style>
