<template>
  <v-card
    class="d-flex flex-wrap justify-space-around align-stretch text-uppercase"
    flat
    tile
  >
    <v-col cols="12" class="py-0">
      <v-text-field
        v-model="form.name"
        label="Nombre GPS"
        placeholder="Referencia"
        :rules="rules"
        outlined
        filled
        dense
    /></v-col>
    <v-col cols="12" md="6" class="py-0">
      <v-autocomplete
        v-model="form.gps_group_id"
        :items="options.groups"
        label="Cliente:"
        placeholder="A quien pertence"
        item-text="name"
        item-value="id"
        :rules="rules"
        clearable
        outlined
        filled
        dense
    /></v-col>
    <v-col cols="12" md="3" class="py-0">
      <v-autocomplete
        v-model="form.gps_chip_id"
        :items="options.chips"
        label="Chip (SIM): "
        placeholder="CHIP Asignado"
        item-text="sim"
        item-value="sim"
        :rules="rules"
        clearable
        outlined
        filled
        dense
    /></v-col>
    <v-col cols="12" md="3" class="py-0">
      <v-select
        v-model="form.payment_type"
        :items="options.types"
        label="Tipo de Pago"
        :rules="rules"
        outlined
        filled
        dense
      />
    </v-col>

    <v-col cols="12" md="8" class="pa-0">
      <v-row no-gutters align="center">
        <v-col cols="12" md="6" class="pa-0">
          <v-text-field
            v-model="form.installation_date"
            label="Fecha Instalacion"
            class="mx-2"
            type="date"
            :rules="rules"
            outlined
            filled
            dense
          />
        </v-col>
        <v-col cols="12" md="6" class="pa-0">
          <v-text-field
            v-model="form.renew_date"
            label="Fecha en que Renueva"
            class="mx-2"
            type="date"
            :rules="rules"
            :readonly="editing"
            outlined
            filled
            dense
          />
        </v-col>
      </v-row>
      <v-row no-gutters>
        <p class="subtitle text-center mx-2">
          Datos de Facturacion (Opcionales):
        </p>
        <v-text-field
          v-model="form.invoice"
          label="Factura:"
          placeholder="Folio"
          class="mx-2"
          :readonly="editing"
          outlined
          dense
        />
        <v-text-field
          v-model="form.invoice_date"
          label="Fecha Factura"
          class="mx-2"
          type="date"
          outlined
          dense
        />
        <v-text-field
          v-model="form.amount"
          label="Importe Factura:"
          placeholder="0.00"
          type="Number"
          class="mx-2 title"
          prepend-inner-icon="mdi-currency-usd"
          :readonly="editing"
          outlined
          filled
          dense
        >
          <template v-slot:append>
            <v-btn
              :disabled="editing"
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
        <template v-if="form.currency == 'USD'">
          <v-text-field
            v-model="form.exchange_rate"
            label="Tipo Cambio:"
            class="title"
            type="Number"
            :rules="rules"
            :readonly="editing"
            hide-details
            reverse
            outlined
            filled
            dense
          />
        </template>
      </v-row>
    </v-col>
    <v-col cols="12" md="4" class="py-0">
      <v-textarea
        v-model="form.description"
        label="Ultimo Comentario:"
        placeholder="Comentario"
        class="title font-weight-bold"
        :rules="rules"
        outlined
        filled
        dense
      ></v-textarea>
    </v-col>
  </v-card>
</template>

<script>
export default {
  name: 'GpsForm',
  props: {
    form: {
      required: true,
      type: Object,
    },
    options: {
      require: false,
      type: Object,
      default: () => {
        return {};
      },
    },
    editing: {
      required: false,
      type: Boolean,
      default: false,
    },
  },
  data() {
    return {
      rules: [(v) => !!v || 'Campo Requerido'],
    };
  },
};
</script>

<style scoped>
.v-input__append-outer {
  margin-top: 0;
}
</style>
