<template>
  <div>
    <form-ams-equipment :form.sync="form"></form-ams-equipment>

    <v-card-actions>
      <v-btn @click="create" color="primary" dark>Guardar</v-btn>
      <v-btn @click="cancel">Cancelar</v-btn>
    </v-card-actions>
  </div>
</template>
<script>
import formAmsEquipment from "./Components/FormAmsEquipment.vue";
export default {
  components: { formAmsEquipment },
  data() {
    return {
      form: {
        category: "",
        unit: [],
        name: "",
        value: "",
        price: "",
      },
    };
  },
  methods: {
    async create() {
      const _this = this;
      // if (!_this.$refs.FormCustomer.$refs.form.validate()) return;
      let payload = {
        ..._this.form,
      };

      await axios
        .post("/admin/ams-equipment", payload)
        .then(function (response) {
          _this.$store.commit("showSnackbar", {
            message: response.data.message,
            color: "success",
            duration: 3000,
          });
          _this.$emit("submit");
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
    cancel() {
      this.$emit("submit");
    },
  },
};
</script>
<style></style>
