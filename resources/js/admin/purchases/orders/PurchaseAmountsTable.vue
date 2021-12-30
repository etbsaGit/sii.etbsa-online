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

        <tr>
          <td>Impuesto</td>
          <td class="text-right" style="width: 100px;">
            {{ Tax || 0 | money }}
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
        .map((item) => parseFloat(item.quantity) * parseFloat(item.price))
        .reduce((acc, crr) => acc + crr, 0));
    },
    Tax() {
      return (this.form.tax = this.items
        .map(
          (item) =>
            parseFloat(item.quantity) *
            parseFloat(item.price) *
            parseFloat(item.tax ? item.tax : 0)
        )
        .reduce((acc, crr) => acc + crr, 0));
    },
    Total() {
      return (this.form.total = this.Subtotal + this.Tax);
    },
  },
};
</script>
<style></style>
