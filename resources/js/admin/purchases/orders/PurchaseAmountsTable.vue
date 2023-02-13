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
          <td class="d-flex align-center">
            <v-switch
              v-model="form.with_isr"
              label="Retencion ISR 10%"
              dense
            ></v-switch>
          </td>
          <td class="text-right pr-2">
            <v-text-field
              v-model.number="form.tax_isr"
              outlined
              dense
              hide-details
              type="number"
              suffix="$"
              :disabled="!form.with_isr"
              reverse
            ></v-text-field>
          </td>
        </tr>
        <tr>
          <td class="d-flex align-center">
            <v-switch
              v-model="form.with_iva_retenido"
              label="IVA Retenido 10.667%"
              dense
            ></v-switch>
          </td>
          <td class="text-right pr-2">
            <v-text-field
              v-model.number="form.tax_iva_retenido"
              outlined
              dense
              hide-details
              type="number"
              suffix="$"
              :disabled="!form.with_iva_retenido"
              reverse
            ></v-text-field>
          </td>
        </tr>
        <tr>
          <td class="d-flex align-center">
            <v-switch
              v-model="form.with_retencion_cedular"
              label="Cedular GTO 2%"
              dense
            ></v-switch>
          </td>
          <td class="text-right pr-2">
            <v-text-field
              v-model.number="form.tax_retencion_cedular"
              outlined
              dense
              hide-details
              type="number"
              suffix="$"
              :disabled="!form.with_retencion_cedular"
              reverse
            ></v-text-field>
          </td>
        </tr>
        <tr>
          <td class="d-flex align-center">
            <v-switch
              v-model="form.with_retencion_125"
              label="Retencion 1.25%"
              dense
            ></v-switch>
          </td>
          <td class="text-right pr-2">
            <v-text-field
              v-model.number="form.tax_retencion_125"
              outlined
              dense
              hide-details
              type="number"
              suffix="$"
              :disabled="!form.with_retencion_125"
              reverse
            ></v-text-field>
          </td>
        </tr>
        <tr>
          <td class="d-flex align-center">
            <v-switch
              v-model="form.with_flete"
              label="Flete 4%"
              dense
            ></v-switch>
          </td>
          <td class="text-right pr-2">
            <v-text-field
              v-model.number="form.tax_flete"
              outlined
              dense
              hide-details
              type="number"
              suffix="$"
              :disabled="!form.with_flete"
              reverse
            ></v-text-field>
          </td>
        </tr>
        <tr>
          <td style="width: 100px;">Descuento Adicional</td>
          <td class="text-right pr-2">
            <v-text-field
              v-model.number="form.discount"
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

  watch: {
    "form.with_isr": {
      handler(val) {
        const _this = this;
        this.form.tax_isr = val ? _this.Subtotal * 0.1 : 0;
      },
    },
    "form.with_iva_retenido": {
      handler(val) {
        const _this = this;
        this.form.tax_iva_retenido = val ? _this.Subtotal * 0.10667 : 0;
      },
    },
    "form.with_retencion_cedular": {
      handler(val) {
        const _this = this;
        this.form.tax_retencion_cedular = val ? _this.Subtotal * 0.02 : 0;
      },
    },
    "form.with_retencion_125": {
      handler(val) {
        const _this = this;
        this.form.tax_retencion_125 = val ? _this.Subtotal * 0.0125 : 0;
      },
    },
    "form.with_flete": {
      handler(val) {
        const _this = this;
        this.form.tax_flete = val ? _this.Subtotal * 0.04 : 0;
      },
    },
  },

  computed: {
    Subtotal() {
      return (this.form.subtotal = this.items
        .map(
          (item) =>
            parseFloat(item.qty) * parseFloat(item.price) -
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
    // ISR() {
    //   return (this.form.tax_isr = this.form.with_isr ? this.Subtotal * 0.1 : 0);
    // },
    Retention() {
      const result =
        this.form.tax_isr -
        this.form.tax_iva_retenido -
        this.form.tax_retencion_cedular +
        this.form.tax_retencion_125 +
        this.form.tax_flete;

      return parseFloat(result);
    },
    Total() {
      let Total = this.Subtotal + this.Tax + this.Retention;
      this.form.total = Total - this.form.discount;
      return this.form.total;
    },
  },
};
</script>
<style></style>
