<template>
  <v-card>
    <v-card-title class="py-0 overline">
      <v-icon class="mr-2">mdi-file-document</v-icon> Registrar Orden de Compra
      <v-spacer></v-spacer>
    </v-card-title>
    <v-divider></v-divider>
    <v-card-text>
      <purchase-form
        ref="form"
        v-model="valid"
        :form.sync="purchase"
        @submit="createPurchase"
      ></purchase-form>
    </v-card-text>
  </v-card>
</template>
<script>
import PurchaseForm from "../components/forms/PurchaseForm.vue";
export default {
  name: "CreatePurchase",
  components: { PurchaseForm },
  data() {
    return {
      valid: true,
      purchase: {
        subtotal: 0,
        tax: 0,
        check_tax: false,
        total: 0,
        concepts: [],
        supplier_id: null,
        agency_id: null,
        department_id: null,
        estatus: { title: "Pendiente" },
        estatus_id: null,
        reason: null,
        uso_cfdi_id: null,
        metodo_pago_id: null,
        forma_pago_id: null,
        uso_cfdi: null,
        metodo_pago: null,
        forma_pago: null,
        deliver_date: new Date().toISOString().substr(0, 10),
        payment_condition: "30 dias",
      },
    };
  },
  mounted() {
    this.$store.commit("setBreadcrumbs", [
      { label: "Ordenes de Compra", to: { name: "purchase.list" } },
      { label: "Crear", name: "" },
    ]);
  },
  computed: {
    minHeight() {
      const height = "82vh";
      return `calc(${height} - ${this.$vuetify.application.top}px)`;
    },
  },
  methods: {
    async createPurchase() {
      if (!this.$refs.form.$refs.formPurchase.validate()) return;
      if (this.purchase.concepts.length <= 0)
        return this.$store.commit("showSnackbar", {
          message: "Agrege al Menos un Concepto",
          color: "error",
          duration: 3000,
        });
      const _this = this;
      await axios
        .post("/admin/purchase-order", _this.purchase)
        .then(function (response) {
          _this.$store.commit("showSnackbar", {
            message: response.data.message,
            color: "success",
            duration: 3000,
          });
          _this.$eventBus.$emit("ORDERS_REFRESH");
          _this.$eventBus.$emit("CLOSE_DIALOG");
          // _this.$router.push();
        })
        .catch(function (error) {
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
  },
};
</script>
<style lang=""></style>
