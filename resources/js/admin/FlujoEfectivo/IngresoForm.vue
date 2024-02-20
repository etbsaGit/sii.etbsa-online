<template >
  <v-container>
    <v-row>
      <v-col cols="12">
        <v-select
          v-model.number="editedItem.apply_bank_accounts_id"
          :items="options.agency_bank_accounts"
          item-value="id"
          item-text="bank_name"
          label="Cuenta de Sucursal"
          outlined
          dense
        ></v-select>
      </v-col>

      <v-col cols="12" md="6">
        <v-text-field
          v-model.number="editedItem.amount"
          label="Importe"
          outlined
          dense
        ></v-text-field>
      </v-col>
      <v-col cols="12" md="6">
        <v-select
          v-model.number="editedItem.currency_id"
          :items="options.currencies"
          item-value="id"
          item-text="name"
          label="Moneda"
          outlined
          dense
        ></v-select>
      </v-col>

      <v-col cols="12" md="6">
        <v-select
          v-model.number="editedItem.payment_source_id"
          :items="options.payment_sources"
          item-value="id"
          item-text="title"
          label="Forma Pago"
          outlined
          dense
        ></v-select>
      </v-col>
      <v-col cols="12" md="6">
        <v-select
          v-model.number="editedItem.category_id"
          :items="options.category"
          item-value="id"
          item-text="name"
          label="Categoria"
          outlined
          dense
        ></v-select>
      </v-col>
      <v-col cols="12">
        <v-text-field
          v-model="editedItem.reference_number"
          label="Numero Cliente"
          persistent-hint
          outlined
          dense
        ></v-text-field>
      </v-col>
      <v-col cols="12">
        <v-text-field
          v-model="editedItem.reference_name"
          label="Nombre Cliente"
          persistent-hint
          outlined
          dense
        ></v-text-field>
      </v-col>
      <v-col cols="12">
        <v-text-field
          v-model="editedItem.reference_concept"
          label="Referencia"
          hint="Folio, Factura, Numero de Referencia"
          persistent-hint
          outlined
          dense
        ></v-text-field>
      </v-col>
      <v-col cols="12">
        <v-textarea
          v-model="editedItem.description"
          label="Descripcion"
          outlined
          dense
        ></v-textarea>
      </v-col>
    </v-row>
  </v-container>
</template>
<script>
export default {
  props: {
    // editedIndex: {
    //   type: String | Number,
    //   default: -1,
    // },
    editedItem: {
      type: Object,
      default: () => {
        return {
          external_id: null,
          reference_number: null,
          reference_name: null,
          reference_concept: null,
          description: null,
          amount: 0,
          currency_id: 1,
          payment_source_id: 1,
          tipo_poliza_id: 1,
          category_id: null,
          // origen_bank_accounts_id: null,
          apply_bank_accounts_id: null,
          is_applied: false,
          // apply_date: null,
          // user_created: null,
          // user_updated: null,
        };
      },
    },
  },
  mounted() {
    this.getOptions();
  },
  data: () => {
    return {
      //   editedIndex: -1,
      //   editedItem: {
      //     // external_id: null,
      //     reference_number: null,
      //     reference_name: null,
      //     reference_concept: null,
      //     description: null,
      //     amount: 0,
      //     currency_id: 1,
      //     payment_source_id: 1,
      //     tipo_poliza_id: 1,
      //     category_id: null,
      //     // origen_bank_accounts_id: null,
      //     apply_bank_accounts_id: null,
      //     is_applied: false,
      //     // apply_date: null,
      //     // user_created: null,
      //     // user_updated: null,
      //   },
      defaultItem: {
        // external_id: null,
        reference_number: null,
        reference_name: null,
        reference_concept: null,
        description: null,
        amount: 0,
        currency_id: 1,
        payment_source_id: 1,
        tipo_poliza_id: 1,
        category_id: null,
        // origen_bank_accounts_id: null,
        apply_bank_accounts_id: null,
        is_applied: false,
        // apply_date: null,
        // user_created: null,
        // user_updated: null,
      },
      options: {
        agency_bank_accounts: [],
        currencies: [],
        payment_sources: [],
        category: [],
      },
    };
  },
  methods: {
    async getOptions() {
      const _this = this;
      try {
        let {
          data: { data, message },
        } = await axios.get("/admin/polizas/create");
        _this.options = data.options;

        _this.$store.commit("showSnackbar", {
          message: message,
          color: "success",
          duration: 3000,
        });
      } catch (error) {
        _this.$store.commit("showSnackbar", {
          message: error.response.data.message,
          color: "error",
          duration: 3000,
        });
      }
    },
  },
};
</script>
<style >
</style>