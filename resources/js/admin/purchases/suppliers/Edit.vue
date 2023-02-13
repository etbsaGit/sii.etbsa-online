<template>
  <v-card>
    <v-card-title>
      <v-icon class="mr-2">mdi-account-edit</v-icon> Editar Informacion
      Proveedor
      <v-spacer></v-spacer>
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
        id: null,
        code_equip: null,
        business_name: null,
        rfc: null,
        address: null,
        email: null,
        phone: null,
        contact: null,
        addresses: null,
        billing_data: [],
        estate_id: "",
        township_id: "",
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
      await axios
        .put(`/admin/suppliers/${_this.supplierId}`, _this.supplier)
        .then(function (response) {
          _this.$store.commit("showSnackbar", {
            message: response.data.message,
            color: "success",
            duration: 3000,
          });
          _this.$eventBus.$emit("SUPPLIER_REFRESH");
        })
        .catch(function (error) {
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
      this.supplier = await axios
        .get(`/admin/suppliers/${this.supplierId}/edit`)
        .then((response) => {
          let Supplier = response.data.data;
          return { ...Supplier };
        });
      (cb || Function)();
    },
  },
};
</script>
<style lang=""></style>
