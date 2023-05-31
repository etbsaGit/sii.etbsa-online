<template>
  <v-card>
    <v-card-title>
      <v-icon class="mr-2">mdi-account-edit</v-icon> Editar Informacion
      Proveedor
      <v-spacer></v-spacer>
      <v-btn
        v-if="$gate.allow('editSupplier', 'compras')"
        color="primary"
        :disabled="!valid"
        @click="updateSupplier"
        dark
      >
        Guardar
      </v-btn>
    </v-card-title>
    <v-card-text>
      <supplier-form
        v-model="valid"
        ref="form"
        :form.sync="supplier"
      ></supplier-form>
    </v-card-text>
    <v-card-actions>
      <v-btn
        v-if="$gate.allow('editSupplier', 'compras')"
        color="green"
        :disabled="!valid"
        @click="updateSupplier"
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
  name: "EditSupplier",
  components: { SupplierForm },
  props: {
    supplierId: {
      require: true,
      type: [Number, String],
    },
  },
  data() {
    return {
      valid: true,
      supplier: {
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
    const _this = this;
    _this.loadSupplierEdit(() => {
      _this.$store.commit("setBreadcrumbs", [
        { label: "Proveedores", to: { name: "suppliers.list" } },
        { label: "Editar" },
        { label: this.supplier.code_equip },
      ]);
    });
  },
  methods: {
    async updateSupplier() {
      if (!this.$refs.form.$refs.formSupplier.validate()) return;
      const _this = this;
      const formData = _this.createFormDataFromObject(_this.supplier);
      return await axios
        .post(`/admin/suppliers/${_this.supplierId}`, formData, {
          headers: {
            "Content-Type": "multipart/form-data",
          },
        })
        .then(function(response) {
          _this.loadSupplierEdit(() => {
            _this.$store.commit("showSnackbar", {
              message: response.data.message,
              color: "success",
              duration: 3000,
            });
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
            // return false;
          } else if (error.request) {
            console.log(error.request);
          } else {
            console.log("Error", error.message);
          }
        });
    },
    async loadSupplierEdit(cb) {
      let { data: response } = await axios.get(
        `/admin/suppliers/${this.supplierId}/edit`
      );
      this.supplier = { ...response.data.supplier };
      this.$store.commit("showSnackbar", {
        message: response.message,
        color: "success",
        duration: 3000,
      });
      cb() || Function;
    },
    createFormDataFromObject(obj) {
      const formData = new FormData();
      formData.append("_method", "PUT");
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
        } else if (value && typeof value === "object") {
          for (const [keyA, valueA] of Object.entries(value)) {
            formData.append(`${key}[${keyA}]`, valueA ?? "");
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
<style lang=""></style>
