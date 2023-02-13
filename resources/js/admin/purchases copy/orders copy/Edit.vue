<template>
  <purchase-form
    v-model="valid"
    ref="form"
    :form.sync="purchase"
    @submit="updatePurchase"
  ></purchase-form>
</template>
<script>
import PurchaseForm from "../components/forms/PurchaseForm.vue";
export default {
  name: "EditPurchase",
  components: { PurchaseForm },
  props: {
    purchaseId: {
      require: true,
      type: Number,
    },
  },
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
        estatus: null,
        estatus_id: null,
        estatus_key: null,
        reason: null,
        metodo_pago_id: null,
        uso_cfdi_id: null,
        forma_pago_id: null,
        metodo_pago: null,
        uso_cfdi: null,
        forma_pago: null,
        deliver_date: null,
        payment_condition: null,
      },
    };
  },
  mounted() {
    const _this = this;
    _this.loadPurchaseEdit();
    _this.$eventBus.$on("CHANGE_ESTATUS", () => {
      _this.loadPurchaseEdit();
    });
  },
  methods: {
    async updatePurchase() {
      if (!this.$refs.form.$refs.formPurchase.validate()) return;
      const _this = this;
      let payload = {
        ..._this.purchase,
        estatus_key: _this.purchase.estatus.key,
      };
      await axios
        .put(`/admin/purchase-order/${_this.purchaseId}`, payload)
        .then(function (response) {
          _this.$store.commit("showSnackbar", {
            message: response.data.message,
            color: "success",
            duration: 3000,
          });
          _this.$eventBus.$emit("ORDERS_REFRESH");
          _this.$eventBus.$emit("CLOSE_DIALOG");
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
    async loadPurchaseEdit() {
      this.purchase = await axios
        .get(`/admin/purchase-order/${this.purchaseId}/edit`)
        .then((response) => {
          return { ...response.data.data };
        });
    },
  },
};
</script>
<style lang=""></style>
