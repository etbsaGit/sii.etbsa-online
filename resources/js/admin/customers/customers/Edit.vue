<template>
  <v-card flat>
    <v-container fluid class="pt-0">
      <v-card-text>
        <form-customer
          ref="FormCustomer"
          :value="valid"
          :form.sync="form"
        ></form-customer>
      </v-card-text>
      <v-card-actions class="justify-center">
        <v-row dense>
          <v-col cols="6">
            <v-btn color="green" block @click="submit">Editar</v-btn>
          </v-col>
          <v-col cols="6">
            <v-btn text block @click="$emit('cancel')">Cancelar</v-btn>
          </v-col>
        </v-row>
      </v-card-actions>
    </v-container>
  </v-card>
</template>
<script>
import FormCustomer from "./FormCustomer.vue";
export default {
  name: "EditCustomer",
  components: { FormCustomer },
  props: {
    editItemId: {
      type: Number | String,
      require: true,
    },
  },
  mounted() {
    this.loadFormEdit();
  },
  data() {
    return {
      valid: true,
      form: {},
    };
  },
  methods: {
    async submit() {
      const _this = this;
      if (!_this.$refs.FormCustomer.$refs.form.validate()) return;
      let payload = {
        ..._this.form,
        is_moral: !!(_this.form.fiscal_type == "PM"),
      };
      await axios
        .put(`/admin/customers/${_this.editItemId}`, payload)
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
        .get(`/admin/customers/${_this.editItemId}/edit`)
        .then((res) => {
          _this.form = { ...res.data.data };
        });
    },
  },
};
</script>
<style></style>
