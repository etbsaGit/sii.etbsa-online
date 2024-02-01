<template>
  <v-card>
    <v-card-text>
      <chip-form :value="valid" :form.sync="form" ref="ChipForm"></chip-form>
    </v-card-text>
    <v-card-actions>
      <v-btn block @click="save()" :disabled="!valid" color="primary">
        Guardar Nuevo
      </v-btn>
    </v-card-actions>
  </v-card>
</template>

<script>
import ChipForm from "./ChipForm.vue";

export default {
  components: { ChipForm },
  data() {
    return {
      valid: true,
      form: {
        sim: "",
        imei: "",
        costo: "",
        cuenta: "",
        fecha_activacion: null,
        fecha_cancelacion: null,
        descripcion: "",
      },
      rules: [(v) => !!v || "Campo Requerido"],
    };
  },
  watch: {
    fecha_activacion: {
      handler: function (val) {
        let date = new Date(val);
        date.setDate(date.getDate() + 365);
        this.fecha_renovacion = this.$appFormatters.formatDate(
          date,
          "yyyy-MM-DD"
        );
      },
    },
  },
  methods: {
    async save() {
      const _this = this;
      if (!_this.$refs.ChipForm.$refs.form.validate()) return;
      await axios
        .post("admin/chips/", _this.form)
        .then(function (response) {
          _this.$store.commit("showSnackbar", {
            message: response.data.message,
            color: "success",
            duration: 3000,
          });

          _this.$eventBus.$emit("GPS_CHIP_ADDED");
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
  },
};
</script>
