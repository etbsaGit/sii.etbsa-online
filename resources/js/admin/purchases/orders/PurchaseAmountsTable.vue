<template>
  <v-simple-table>
    <template v-slot:default>
      <tbody>
        <tr>
          <td>Subtotal</td>
          <td class="text-right" style="width: 100px;">
            {{ Subtotal || 0 | money }}
          </td>
        </tr>

        <!-- <tr>
          <td>Descuento</td>
          <td class="text-right">
            {{ Discount || 0 | money }}
          </td>
        </tr> -->
        <tr>
          <td class="d-flex align-center">
            <v-switch
              v-model="form.with_tax"
              label="Impuestos 16%"
              dense
            ></v-switch>
          </td>
          <td class="text-right" style="width: 100px;">
            {{ Tax || 0 | money }}
          </td>
        </tr>
        <tr>
          <td style="width: 100px;">Descuento Adicional</td>
          <td class="text-right pr-2">
            <v-text-field
              v-model="form.discount"
              outlined
              dense
              hide-details
              type="number"
              suffix="$"
              reverse
            ></v-text-field>
          </td>
        </tr>
        <tr>
          <td>Total</td>
          <td class="text-right" style="width: 100px;">
            <b>{{ Total || 0 | money }}</b>
          </td>
        </tr>
      </tbody>
    </template>
  </v-simple-table>
</template>
<script>
export default {
  name: "PurchaseAmountsTable",
  props: {
    items: {
      type: Array,
      require: true,
    },
    form: {
      type: Object,
    },
  },

  computed: {
    Subtotal() {
      return (this.form.subtotal = this.items
        .map(
          (item) =>
            parseFloat(item.quantity) * parseFloat(item.price) -
            parseFloat(item.discount)
        )
        .reduce((acc, crr) => acc + crr, 0));
    },
    Discount() {
      return this.items
        .map((item) => parseFloat(item.discount))
        .reduce((acc, crr) => acc + crr, 0);
    },
    Tax() {
      return (this.form.tax = this.form.with_tax ? this.Subtotal * 0.16 : 0);
    },
    Total() {
      let Total = this.Subtotal + this.Tax;
      this.form.total = Total - this.form.discount;
      return this.form.total;
    },
  },
};
</script>
<style></style>
