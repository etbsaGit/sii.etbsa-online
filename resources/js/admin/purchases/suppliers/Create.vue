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
      if (!this.$refs.form.$refs.formSupplier.validate()) return;
      const _this = this;
      const formData = new FormData();

      for (const [key, value] of Object.entries(_this.form)) {
        if (Array.isArray(value)) {
          value.map((item, index) => {
            if (typeof item === "object") {
              for (const [keyA, valueA] of Object.entries(item)) {
                formData.append(`${key}[${index}][${keyA}]`, valueA);
              }
            } else {
              formData.append(`${key}[${index}]`, item);
            }
          });
        } else if (typeof value === "object") {
          for (const [keyA, valueA] of Object.entries(value)) {
            formData.append(`${keyA}`, valueA);
          }
        } else {
          formData.append(`${key}`, value);
        }
      }
      return await axios
        .post("/admin/suppliers", formData)
        .then(function(response) {
          _this.$store.commit("showSnackbar", {
            message: response.data.message,
            color: "success",
            duration: 3000,
          });
          _this.$eventBus.$emit("SUPPLIER_REFRESH");
          // _this.$eventBus.$emit("CLOSE_DIALOG");
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
      this.form.documents = options.data.requirements.map((item) => {
        let status = item.status ?? "none";
        return { ...item, status };
      });
    },
    createFormDataFromObject(obj) {
      const formData = new FormData();
      this.transformToFormData(obj, formData);
      return formData;
    },
    transformToFormData(obj, formData, parentKey) {
      const _this = this;

      if (obj && typeof obj === "object") {
        if (Array.isArray(obj)) {
          obj.forEach((value, index) => {
            const nestedKey = parentKey
              ? `${parentKey}[${index}]`
              : `[${index}]`;
            _this.transformToFormData(value, formData, nestedKey);
          });
        } else {
          Object.keys(obj).forEach((key) => {
            const nestedKey = parentKey ? `${parentKey}.${key}` : key;
            _this.transformToFormData(obj[key], formData, nestedKey);
          });
        }
      } else {
        if (obj instanceof File) {
          formData.append(parentKey, obj);
        } else {
          formData.append(parentKey, obj);
        }
      }

      // if (obj && typeof obj === "object") {
      //   if (Array.isArray(obj)) {
      //     obj.forEach((value, index) => {
      //       const nestedKey = parentKey
      //         ? `${parentKey}[${index}]`
      //         : `[${index}]`;
      //       _this.transformToFormData(value, formData, nestedKey);
      //     });
      //   } else {
      //     Object.keys(obj).forEach((key) => {
      //       const nestedKey = parentKey ? `${parentKey}[${key}]` : key;
      //       _this.transformToFormData(obj[key], formData, nestedKey);
      //     });
      //   }
      // } else {
      //   formData.append(parentKey, obj);
      // }
      // 
      // if (obj && typeof obj === "object") {
      //   if (Array.isArray(obj)) {
      //     obj.forEach((value, index) => {
      //       const key = parentKey ? `${parentKey}[${index}]` : `[${index}]`;
      //       _this.transformToFormData(value, formData, key);
      //     });
      //   } else {
      //     Object.keys(obj).forEach((key) => {
      //       const nestedKey = parentKey ? `${parentKey}.${key}` : key;
      //       _this.transformToFormData(obj[key], formData, nestedKey);
      //     });
      //   }
      // } else {
      //   formData.append(parentKey, obj);
      // }
    },
  },
};
</script>
<style></style>
