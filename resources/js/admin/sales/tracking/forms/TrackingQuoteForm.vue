<template>
  <v-card flat class="grey lighten-3" min-height="80vh">
    <v-card-text>
      <quote-concept-table
        :dialogForm="dialog"
        @edit="dialog = true"
        @close="dialog = false"
        :items.sync="form.products"
      ></quote-concept-table>
    </v-card-text>
    <v-card-actions>
      <v-btn text color="blue" @click="dialog = true">
        <v-icon left>mdi-plus</v-icon> Agregar Producto
      </v-btn>
    </v-card-actions>
    <v-col cols="12">
      <v-simple-table class="grey lighten-3">
        <tr class="py-3">
          <td>Subtotal:</td>
          <th class="text-right pr-2">
            {{ Subtotal | money }} {{ Currency.currency.name }}
          </th>
        </tr>
        <tr class="py-3">
          <td>IVA:</td>
          <th class="d-flex justify-end mb-3">
            <v-checkbox v-model="CheckedTax" hide-details class="shrink mr-2">
              <template v-slot:label>
                <div>Con IVA: {{ form.tax | percent }}</div>
              </template>
            </v-checkbox>
          </th>
        </tr>
        <tr class="py-3">
          <td>Descuento:</td>
          <th class="d-flex justify-end">
            <v-text-field
              v-model="form.discount"
              type="number"
              outlined
              suffix="$"
              hide-details
              :prefix="Currency.currency.name"
              reverse
              style="max-width: 200px;"
              dense
            ></v-text-field>
          </th>
        </tr>
        <tr>
          <v-divider class="my-2" />
        </tr>
        <tr>
          <td>Total:</td>
          <th class="text-right pr-2 text-h4">
            {{ Total | money }} {{ Currency.currency.name }}
          </th>
        </tr>
      </v-simple-table>
    </v-col>
    <v-col cols="12">
      <v-textarea
        v-model="form.observation"
        label="Nota de Vendedor"
        outlined
        filled
        hide-details
      ></v-textarea>
    </v-col>
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
    // checkedTax: false,
  }),

  computed: {
    formTitle() {
      // return this.editedIndex === -1 ? "New Item" : "Edit Item";
    },
    Currency() {
      const _this = this;
      let currency = { currency: { name: "" } };
      if (_this.form.products.length > 0) {
        currency = _this.form.products.values().next().value;
      }
      return (_this.form.currency = currency);
    },
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
    // checkedTax(val) {
    //   val ? (this.form.tax = 0.16) : (this.form.tax = 0);
    // },
    dialog(val) {
      val || this.close();
    },
    dialog(val) {
      val || this.close();
    },
    dialogDelete(val) {
      val || this.closeDelete();
    },
  },

  mounted() {
    this.initialize();
  },

  methods: {
    initialize() {},
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
  },
};
</script>
F
