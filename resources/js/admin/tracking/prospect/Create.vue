<template>
  <div class="component-wrap">
    <v-card>
      <v-card-title>
        <v-icon>mdi-account-box-outline</v-icon> Registrar Nuevo Prospecto
      </v-card-title>
      <v-card-text>
        <form-prospect :form.sync="form" @submit="Save()"></form-prospect>
      </v-card-text>
    </v-card>
  </div>
</template>

<script>
import FormProspect from "./forms/Prospect";
export default {
  components: { FormProspect },
  data() {
    return {
      form: {
        rfc: null,
        town: null,
        email: null,
        phone: null,
        company: null,
        full_name: null,
        is_moral: 0,
        estate_id: null,
        township_id: null,
      },
    };
  },
  mounted() {
    this.$store.commit("setBreadcrumbs", [
      { label: "Prospectos", to: { name: "prospect.list" } },
      { label: "Nuevo", name: "" },
    ]);
  },
  methods: {
    Save() {
      const self = this;
      axios
        .post("/admin/prospects", self.form)
        .then(function (response) {
          self.$store.commit("showSnackbar", {
            message: response.data.message,
            color: "success",
            duration: 3000,
          });
          self.$router.go(-1);
        })
        .catch(function (error) {
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
