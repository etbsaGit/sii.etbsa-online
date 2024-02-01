<template>
  <v-card>
    <v-card-text>
      <chip-form :value="valid" :form.sync="form" ref="ChipForm"></chip-form>
    </v-card-text>
    <v-card-actions>
      <v-btn block @click="update()" :disabled="!valid" color="primary">
        Guardar Nuevo
      </v-btn>
    </v-card-actions>
  </v-card>
</template>

<script>
import ChipForm from "./ChipForm.vue";

export default {
  components: { ChipForm },
  props: {
    propGpsChipId: {
      required: true,
    },
  },
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
  mounted() {
    const _this = this;
    _this.loadGpsChip(() => {});
  },
  methods: {
    update() {
      const _this = this;
      if (!_this.$refs.ChipForm.$refs.form.validate()) return;
      _this.isLoading = true;
      axios
        .put("/admin/chips/" + _this.propGpsChipId, _this.form)
        .then(function (response) {
          _this.$store.commit("showSnackbar", {
            message: response.data.message,
            color: "success",
            duration: 3000,
          });

          _this.$eventBus.$emit("GPS_CHIP_UPDATED");
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
        })
        .finally(function () {
          _this.isLoading = false;
        });
    },
    async loadGpsChip(cb) {
      const _this = this;

      await axios
        .get("/admin/chips/" + _this.propGpsChipId)
        .then(function (response) {
          let Chip = response.data.data;

          _this.form.sim = Chip.sim;
          _this.form.imei = Chip.imei;
          _this.form.cuenta = Chip.cuenta;
          _this.form.costo = Chip.costo;
          _this.form.fecha_activacion = _this.$appFormatters.formatDate(
            Chip.fecha_activacion,
            "yyyy-MM-DD"
          );
          _this.form.fecha_renovacion = _this.$appFormatters.formatDate(
            Chip.fecha_renovacion,
            "yyyy-MM-DD"
          );
          _this.form.fecha_cancelacion = Chip.fecha_cancelacion
            ? _this.$appFormatters.formatDate(
                Chip.fecha_cancelacion,
                "yyyy-MM-DD"
              )
            : null;
          _this.form.descripcion = Chip.descripcion;
          (cb || Function)();
        });
    },
  },
};
</script>
