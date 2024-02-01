<template>
  <v-card>
    <v-card-title>
      <v-icon left>mdi-account-plus</v-icon> Registrar Nuevo Proveedor
      <v-spacer></v-spacer>
      <v-btn
        v-if="$gate.allow('createSupplier', 'compras')"
        color="primary"
        :disabled="!valid"
        @click="createSupplier"
        dark
      >
        Guardar
      </v-btn>
    </v-card-title>
    <v-divider></v-divider>
    <v-card-text>
      <supplier-form v-model="valid" ref="form" :form.sync="form">
      </supplier-form>
    </v-card-text>
    <v-card-actions>
      <v-btn
        v-if="$gate.allow('createSupplier', 'compras')"
        color="primary"
        :disabled="!valid"
        @click="createSupplier"
        block
        dark
      >
        Guardar
      </v-btn>
    </v-card-actions>
  </v-card>
</template>
<script>
import SupplierForm from "../components/forms/SupplierForm.vue";

export default {
  name: "CreateSupplier",
  components: { SupplierForm },
  data() {
    return {
      valid: true,
      form: {
        code_equip: "",
        business_name: "",
        rfc: "",
        address: "",
        email: "",
        phone: "",
        estate_id: "",
        township_id: "",
        observation: "",
        credit_days: "",
        credit_limit: 0,
        observation: "",
        isActive: 0,
        contacts: [],
        giro: [],
        documents: [],
        billing_data: {
          bank: "",
          account: "",
          clabe: "",
          agency: "",
        },
      },
    };
  },
  mounted() {
    this.loadForm();
    this.$store.commit("setBreadcrumbs", [
      { label: "Proveedores", to: { name: "suppliers.list" } },
      { label: "Crear" },
    ]);
  },
  methods: {
    async createSupplier() {
      const _this = this;
      if (!_this.$refs.form.$refs.formSupplier.validate()) return;
      const formData = _this.createFormDataFromObject(_this.form);

      return await axios
        .post("/admin/suppliers", formData)
        .then(function(response) {
          _this.$store.commit("showSnackbar", {
            message: response.data.message,
            color: "success",
            duration: 3000,
          });
          _this.$router.push({
            name: "suppliers.list",
          });
        })
        .catch(function(error) {
          _this.$store.commit("hideLoader");
          if (error.response) {
            _this.$store.commit("showSnackbar", {
              message: error.response.data.message,
              color: "error",
              duration: 3000,
            });
            return false;
          } else if (error.request) {
            console.log(error.request);
          } else {
            console.log("Error", error.message);
          }
        });
    },
    async loadForm() {
      let { data: options } = await axios.get("/admin/suppliers/create");
      this.form.documents = options.data.requirements;
    },
    createFormDataFromObject(obj) {
      const formData = new FormData();
      Object.entries(obj).forEach(([key, value]) => {
        if (Array.isArray(value)) {
          value.map((item, index) => {
            if (typeof item === "object") {
              for (const [keyA, valueA] of Object.entries(item)) {
                formData.append(`${key}[${index}][${keyA}]`, valueA ?? "");
              }
            } else {
              formData.append(`${key}[${index}]`, item);
            }
          });
        } else if (typeof value === "object") {
          for (const [keyB, valueB] of Object.entries(value)) {
            formData.append(`${key}[${keyB}]`, valueB ?? "");
          }
        } else {
          formData.append(`${key}`, value ?? "");
        }
      });
      return formData;
    },
  },
};
</script>
<style></style>
