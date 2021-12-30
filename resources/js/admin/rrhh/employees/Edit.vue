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
          <v-btn color="green" block @click="submit">Modificar</v-btn>
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
  name: "EditEmployee",
  components: { FormEmployee },
  props: {
    editItemId: {
      type: Number | String,
      require: true,
    },
  },
  data() {
    return {
      valid: true,
      form: {
        active: false,
      },
    };
  },
  mounted() {
    this.loadFormEdit();
  },
  methods: {
    async submit() {
      const _this = this;
      if (!_this.$refs.FormEmployee.$refs.form.validate()) return;
      let payload = {
        ..._this.form,
        active: _this.form.active ? moment().format("YYYY-MM-DD") : null,
      };
      await axios
        .put(`/admin/employees/${_this.editItemId}`, payload)
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
    async loadFormEdit() {
      const _this = this;
      await axios
        .get(`/admin/employees/${_this.editItemId}/edit`)
        .then((res) => {
          _this.form = { ...res.data.data };
        });
    },
  },
};
</script>
<style></style>
