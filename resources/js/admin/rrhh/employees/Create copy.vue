<template>
  <v-card flat>
    <v-card-text>
      <form-employee
        ref="FormEmployee"
        :value="valid"
        :form.sync="form"
      ></form-employee>
    </v-card-text>
    <v-card-actions class="justify-center">
      <v-row dense>
        <v-col cols="6">
          <v-btn color="green" block @click="submit">Guardar</v-btn>
        </v-col>
        <v-col cols="6">
          <v-btn text block @click="$emit('cancel')">Cancelar</v-btn>
        </v-col>
      </v-row>
    </v-card-actions>
  </v-card>
</template>
<script>
import FormEmployee from "./FormEmployee.vue";
export default {
  name: "CreateCustomer",
  components: { FormEmployee },
  data() {
    return {
      valid: true,
      form: {
        name: "",
        last_name: "",
        birthday_date: null,
        admission_date: null,
        number_employee: "",
        job_title: "",
        active: "",
        phone: "",
        address: "",
        colonia: "",
        code_postal: "",
        estate: "",
        town: "",
      },
    };
  },
  methods: {
    async submit() {
      if (!this.$refs.FormEmployee.$refs.form.validate()) return;
      const _this = this;
      let payload = {
        ...this.form,
        active: this.form.active ? Date().now : null,
      };
      await axios
        .post("/admin/employees", payload)
        .then(function (response) {
          _this.$store.commit("showSnackbar", {
            message: response.data.message,
            color: "success",
            duration: 3000,
          });
          _this.$emit("cancel");
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
<style></style>
