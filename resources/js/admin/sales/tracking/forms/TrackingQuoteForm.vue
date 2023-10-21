<template>
  <v-card flat class="grey lighten-3" min-height="80vh">
    <v-row dense>
      <v-col cols="12" md="8">
        <v-card class="ma-4">
          <v-card-text>
            <quote-concept-table
              :dialogForm="dialog"
              @edit="dialog = true"
              @close="dialog = false"
              :items.sync="form.products"
              :Category_id="form.category_id"
              :propCurrency="form.currency"
              :exchangeValue="form.exchange_value"
              :paymentCondition="form.payment_condition"
              :readOnly="form.read_only"
              @payment="(v) => (form.payment_condition = v)"
              @currency="(v) => (form.currency = v)"
            ></quote-concept-table>
          </v-card-text>
          <v-card-actions>
            <v-btn text color="blue" @click="ShowProducts">
              <v-icon left>mdi-plus</v-icon> Agregar Producto
            </v-btn>
          </v-card-actions>
        </v-card>
      </v-col>
      <v-col cols="12" md="4">
        <v-subheader class="title">Resumen</v-subheader>
        <v-simple-table class="elevation-2 pa-4 mx-3 overline">
          <tr class="py-3">
            <td>Subtotal:</td>
            <th class="text-right pr-2 text-h5">
              <!-- {{ Subtotal | money }} {{ Currency.name }} -->
              {{ Subtotal | money }} {{ form.currency.name }}
            </th>
          </tr>
          <tr class="py-3">
            <td>IVA:</td>
            <th class="d-flex justify-end mb-3">
              <v-checkbox v-model="CheckedTax" hide-details class="shrink mr-2">
                <template v-slot:label>
                  <span>{{ form.tax | percent }}</span>
                </template>
              </v-checkbox>
            </th>
          </tr>
          <tr>
            <td>T.C.</td>
            <th class="d-flex justify-end">
              <v-currency-field
                v-model.number="form.exchange_value"
                :default-value="form.exchange_value"
                placeholder="0.00"
                :rules="[(v) => !!v || 'Es Requerido']"
                type="number"
                suffix="$"
                prefix="MXN"
                style="max-width: 200px"
                hide-details
                readonly
                outlined
                filled
                dense
                reverse
              ></v-currency-field>
            </th>
          </tr>
          <tr class="py-3" v-if="$gate.allow('isGerente', 'tracking')">
            <td>Descuento:</td>
            <th class="d-flex justify-end">
              <v-currency-field
                v-model="form.discount"
                :default-value="form.discount"
                type="number"
                outlined
                suffix="$"
                hide-details
                :prefix="form.currency.name"
                reverse
                style="max-width: 200px"
                dense
              ></v-currency-field>
            </th>
          </tr>
          <tr>
            <v-divider class="my-2" />
          </tr>
          <tr>
            <td>Total:</td>
            <th class="text-right pr-2 text-h4">
              <!-- {{ Total | money }} {{ Currency.name }} -->
              {{ Total | money }} {{ form.currency.name }}
            </th>
          </tr>
          <!-- <tr v-if="Currency.id === 2">
            <td>Total MXN:</td>
            <th class="text-right pr-2 text-h4">
              {{ (Total * form.exchange_value) | money }} MXN
            </th>
          </tr> -->
        </v-simple-table>
        <v-col cols="12">
          <v-textarea
            v-model="form.observation"
            label="Nota de Vendedor"
            outlined
            filled
            hide-details
          ></v-textarea>
        </v-col>
      </v-col>
    </v-row>
  </v-card>
</template>
<script>
import QuoteConceptTable from "./QuoteConceptTable.vue";
export default {
  components: { QuoteConceptTable },
  name: "TrackingQuoteForm",
  props: {
    form: {
      type: Object,
    },
  },
  data: () => ({
    dialog: false,
    dialogDelete: false,
    exchange_value: 1,
  }),

  computed: {
    // Currency() {
    //   const _this = this;
    //   let _currency = { currency: { id: 1, name: "MXN" } };
    //   if (_this.form.products.length > 0) {
    //     _currency = _this.form.products.values().next().value;
    //   }
    //   return (_this.form.currency = _currency.currency);
    // },
    CheckedTax: {
      get() {
        return this.form.tax == 0.16;
      },
      set(val) {
        val ? (this.form.tax = 0.16) : (this.form.tax = 0);
      },
    },
    Subtotal: {
      get() {
        const _this = this;
        return (_this.form.subtotal = _this.form.products
          .map((item) => parseFloat(item.subtotal))
          .reduce((acc, crr) => acc + crr, 0));
      },
    },
    Total: {
      get() {
        const _this = this;
        let amountTax = _this.Subtotal + _this.Subtotal * _this.form.tax;
        return (_this.form.total = amountTax - _this.form.discount);
      },
    },
  },

  watch: {
    dialog(val) {
      val || this.close();
    },
    dialog(val) {
      val || this.close();
    },
    dialogDelete(val) {
      val || this.closeDelete();
    },
    // Currency: function (v) {
    //   _this.form.exchange_value = _this.exchange_value;
    // },
  },

  mounted() {
    // this.initialize();
  },

  methods: {
    async initialize() {
      const _this = this;
      await axios
        .get("/admin/tracking/sales_history/resources")
        .then(function (response) {
          let { exchange_value } = response.data.data;
          _this.exchange_value = exchange_value;
        });
    },
    loadFormQouteInfo() {
      // console.log(this.TrackingQuoteId);
    },

    editItem(item) {
      // this.editedIndex = this.products.indexOf(item);
      // this.editedItem = Object.assign({}, item);
      this.dialog = true;
    },

    deleteItem(item) {
      this.editedIndex = this.form.products.indexOf(item);
      this.editedItem = Object.assign({}, item);
      this.dialogDelete = true;
    },

    deleteItemConfirm() {
      this.products.splice(this.editedIndex, 1);
      this.closeDelete();
    },

    close() {
      this.dialog = false;
      this.$nextTick(() => {
        this.editedItem = Object.assign({}, this.defaultItem);
        this.editedIndex = -1;
      });
    },

    closeDelete() {
      this.dialogDelete = false;
      this.$nextTick(() => {
        this.editedItem = Object.assign({}, this.defaultItem);
        this.editedIndex = -1;
      });
    },
    ShowProducts() {
      const _this = this;
      return _this.form.payment_condition !== "por_definir"
        ? (_this.dialog = true)
        : _this.$store.commit("showSnackbar", {
            message: "Seleccionar una Condicion de Pago",
            color: "warning",
            duration: 3000,
          });
    },
  },
};
</script>
F
