<template>
  <v-container>
    <v-row>
      <v-text-field
        v-model="form.invoice"
        :rules="rules"
        label="Factura:"
        placeholder="Folio"
        class="mx-2"
        outlined
        dense
      />
      <v-text-field
        v-model="form.invoice_date"
        :rules="rules"
        label="Fecha Factura"
        class="mx-2"
        type="date"
        outlined
        dense
      />
      <v-text-field
        v-model="form.amount"
        :rules="rules"
        label="Importe Factura:"
        placeholder="0.00"
        type="Number"
        class="mx-2 title"
        prepend-inner-icon="mdi-currency-usd"
        outlined
        filled
        dense
      >
        <template v-slot:append>
          <v-btn
            outlined
            small
            @click="
              form.currency == 'MXN'
                ? (form.currency = 'USD')
                : (form.currency = 'MXN'),
                (form.exchange_rate = 1)
            "
          >
            {{ form.currency }}
          </v-btn>
        </template>
      </v-text-field>
      <templater v-if="form.currency == 'USD'">
        <v-text-field
          v-model="form.exchange_rate"
          :rules="rules"
          label="Tipo Cambio:"
          class="title"
          type="Number"
          hide-details
          reverse
          outlined
          filled
          dense
        />
      </templater>
    </v-row>
    <small>*Indica Campo requerido</small>
  </v-container>
</template>

<script>
export default {
  name: 'GpsInvoiceForm',
  props: {
    form: {
      require: true,
    },
  },
  data() {
    return {
      rules: [(v) => !!v || 'Campo Requerido'],
    };
  },
};
</script>

<style></style>
