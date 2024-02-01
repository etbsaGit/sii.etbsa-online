<template>
  <v-card flat>
    <v-card-title>
      <v-autocomplete
        v-model="Category"
        :items="options.categories"
        item-value="id"
        item-text="name"
        placeholder="Seleccionar"
        label="Categoria"
        hide-details
        outlined
        filled
        dense
        @input="(value) => $emit('SELECTED_CATEGRORY', value)"
        :disabled="hasItems"
      >
      </v-autocomplete>
      <v-spacer></v-spacer>
      <v-select
        v-model="Payment"
        :items="PaymentConditionConfig"
        label="Condicion de Pago"
        placeholder="Placeholder"
        class="py-2"
        style="max-width: 250px"
        hide-details
        outlined
        dense
      ></v-select>
      <v-select
        v-model="Currency"
        :items="options.currency"
        item-value="id"
        item-text="name"
        placeholder="Moneda"
        label="Moneda"
        class="ml-2"
        style="max-width: 250px"
        hide-details
        outlined
        filled
        dense
        return-object
      ></v-select>
    </v-card-title>
    <v-data-table
      :headers="headers"
      :items="items"
      fixed-header
      hide-default-footer
      dense
      disable-pagination
    >
      <template v-slot:[`item.name`]="{ item }">
        <h3>{{ item.name }}</h3>
        <h4>SKU: {{ item.sku }}</h4>
        <v-card flat class="caption overflow-auto" max-height="160">
          <v-card-text class="pa-0">
            {{ item.description }}
          </v-card-text>
        </v-card>
      </template>
      <template v-slot:[`item.price`]="{ item, value }">
        <v-edit-dialog
          ref="refEditPriceProduct"
          :return-value="item.price"
          @save="saveNewPrice(item, value, item.prices[TypePrice])"
          @cancel="cancelSnack"
          @open="openSnack"
          @close="closeSnack"
          save-text="Guardar"
          cancel-text="Cancelar"
          persistent
          large
        >
          <!-- <v-chip outlined label large color="primary">
            {{ item.price | currency }} {{ item.currency.name }}
          </v-chip> -->
          <v-chip outlined label large color="primary">
            <span class="font-weight-bold">
              {{ ConvertionExchange(item) | money }} {{ Currency.name }}
            </span>
          </v-chip>

          <template v-slot:input>
            <div class="mt-4 text-h6">Actualizar Precio</div>
            <v-currency-field
              v-model.number="item.price"
              :default-value="item.price"
              prefix="$"
              :suffix="item.currency.name"
              type="number"
              label="Edit"
              hide-details
              single-line
              counter
              autofocus
              outlined
            ></v-currency-field>
          </template>
        </v-edit-dialog>
      </template>
      <template v-slot:[`item.qty`]="{ item }">
        <!-- <span v-if="readOnly">{{ value }}</span> -->
        <!-- v-else -->
        <v-edit-dialog
          :return-value.sync="item.qty"
          large
          persistent
          @save="saveSnack(item)"
          @cancel="cancelSnack"
          @open="openSnack"
          @close="closeSnack"
          save-text="Guardar"
          cancel-text="Cancelar"
        >
          <v-text-field
            :value="item.qty"
            style="max-width: 125px"
            class="py-1"
            hide-details
            single-line
            readonly
            outlined
            reverse
            dense
          ></v-text-field>
          <template v-slot:input>
            <div class="mt-4 text-h6">Actualizar Cantidad</div>
            <v-text-field
              v-model.number="item.qty"
              label="Edit"
              hide-details
              single-line
              counter
              autofocus
              outlined
              type="number"
            ></v-text-field>
          </template>
        </v-edit-dialog>
      </template>
      <template v-slot:[`item.subtotal`]="{ value }">
        <v-chip outlined label large>
          <!-- {{ value | money }} {{ item.currency.name }} -->
          {{ value | money }} {{ Currency.name }}
        </v-chip>
      </template>
      <template v-slot:[`item.accion`]="{ item }">
        <div class="d-flex flex-row">
          <v-icon small righ color="red" @click="deleteItem(item)">
            mdi-trash-can
          </v-icon>
        </div>
      </template>
    </v-data-table>
    <dialog-component
      :show="Dialog"
      @close="(Dialog = false), loadOptions()"
      :fullscreen="$vuetify.breakpoint.mobile"
      :title="`${Title} Productos`"
      :maxWidth="1200"
      closeable
      key="product-list"
    >
      <products-list
        v-if="Dialog"
        :Filters="{
          price_type: Payment,
          category_id: Category,
        }"
      ></products-list>
    </dialog-component>
    <v-snackbar v-model="snack" :timeout="5000" :color="snackColor">
      {{ snackText }}

      <template v-slot:action="{ attrs }">
        <v-btn v-bind="attrs" text @click="snack = false"> Cerrar </v-btn>
      </template>
    </v-snackbar>
  </v-card>
</template>
<script>
import DialogComponent from "../../../components/DialogComponent.vue";
import ProductsList from "../../../products/product/Index.vue";

const _paymentCondition = [
  { text: "Por Definir", value: "por_definir", config: [] },
  {
    text: "P. Lista",
    value: "precio_lista",
    config: [5, 6, 11, 14, 16, 9, 17],
  },
  {
    text: "Contado",
    value: "contado",
    config: [1, 2, 3, 10, 5, 6, 11, 16, 9, 17, 14],
  },
  { text: "JDF 2 años", value: "jdf_2y", config: [1, 2, 3, 10, 17] },
  { text: "JDF 5 años", value: "jdf_5y", config: [1] },
  { text: "Financiamiento 3 años S/I", value: "jdf_3y_si", config: [1] },
  { text: "Expo", value: "precio_expo", config: [1, 5] },
  { text: "Precio Volumen", value: "por_volumen", config: [5, 14] },
  { text: "Arrendamiento", value: "renta_1", config: [6, 15] },
  { text: "Arrendamiento 2 meses", value: "renta_2", config: [15] },
  { text: "Arrendamiento +3 meses", value: "renta_3", config: [15] },
  { text: "Credito 30 Dias", value: "credito_30d", config: [5, 17] },
  { text: "Arrendadoras", value: "arrendadoras", config: [6] },
];
const _pricesConfig = [
  { por_definir: "price_2" },
  { precio_lista: "price_1" },
  { contado: "price_2" },
  { jdf_2y: "price_3" },
  { jdf_5y: "price_4" },
  { precio_expo: "price_5" },
  { por_volumen: "price_6" },
  { renta_1: "price_7" },
  { renta_2: "price_8" },
  { renta_3: "price_9" },
  { credito_30d: "price_10" },
  { arrendadoras: "price_11" },
  { jdf_3y_si: "price_12" },
];

export default {
  name: "QuoteConceptTable",
  components: { ProductsList, DialogComponent },
  props: {
    dialogForm: {
      default: false,
    },
    paymentCondition: {
      default: "por_definir",
    },
    Category_id: {
      type: Number | String,
      require: false,
      default: null,
    },
    propCurrency: {
      type: Object,
      require: false,
      default: () => {},
    },
    exchangeValue: {
      type: Number | String,
      require: true,
    },
    currency_id: {
      type: Number | String,
      require: false,
      default: 1,
    },
    optionsPaymentcondition: {
      type: Array,
      default: () => {
        return _paymentCondition;
      },
      require: false,
    },
    optionsTypePrices: {
      type: Array,
      default: () => {
        return _pricesConfig;
      },
      require: false,
    },
    items: {
      type: Array,
      default: () => {
        return [];
      },
    },
    readOnly: {
      type: Boolean,
      default: false,
    },
  },
  computed: {
    hasItems() {
      return this.items.length > 0;
    },
    Title() {
      return this.editedIndex > -1 ? "Editar" : "Agregar";
    },
    Dialog: {
      get() {
        return this.dialogForm;
      },
      set(v) {
        if (!v) {
          this.$emit("close");
          this.$nextTick(() => {
            // this.form = { ...this.formDefault };
            // this.editedIndex = -1;
          });
        } else {
          return this.$emit("edit");
        }
      },
    },
    Payment: {
      get() {
        return this.paymentCondition;
      },
      set(v) {
        this.$emit("payment", v);
        this.$nextTick(() => {});
      },
    },
    Currency: {
      get() {
        return this.propCurrency;
      },
      set(v) {
        this.$emit("currency", v);
        this.$nextTick(() => {});
      },
    },
    Category: {
      get() {
        return this.Category_id;
        // return this.Category_id ? this.Category_id : this.select_category_id;
      },
      set(v) {
        this.select_category_id = v;
        this.$emit("category", v);
        this.$nextTick(() => {});
      },
    },
    PaymentConditionConfig() {
      const _this = this;
      let Default = [{ text: "Por Definir", value: "por_definir" }];
      let result = [];
      if (_this.select_category_id) {
        const even = (e) => e == _this.select_category_id;
        result = _this.options.price_types.filter((option) =>
          option.config.some(even)
        );
      }
      return Default.concat(result);
    },
    TypePrice() {
      const key = this.Payment;
      const type = this.optionsTypePrices.find(
        (item) => Object.keys(item)[0] === key
      );
      return type[key];
    },
  },
  watch: {
    Payment(v, old) {
      // console.log(v, old);
      const _this = this;
      // let price_type =
      //   _paymentCondition.find((p) => p.value == v).type_price ?? "";

      _this.items.forEach((product) => {
        product.price = !product.quotation
          ? product.prices[_this.TypePrice] ?? product.price
          : product.quotation.price_unit;
        // product.subtotal = product.qty * product.price;
        product.subtotal = product.qty * this.ConvertionExchange(product);
      });
    },
    Currency(v, old) {
      // console.log(v, old);
      const _this = this;
      // let price_type =
      //   _paymentCondition.find((p) => p.value == v).type_price ?? "";

      _this.items.forEach((product) => {
        product.price = !product.quotation
          ? product.prices[_this.TypePrice] ?? product.price
          : product.quotation.price_unit;

        // product.subtotal = product.qty * product.price;
        product.subtotal = product.qty * this.ConvertionExchange(product);
      });
    },
  },
  data() {
    return {
      select_category_id: this.Category_id ?? null,
      options: { categories: [], price_types: _paymentCondition },
      valid: true,
      snack: false,
      snackColor: "",
      snackText: "",
      headers: [
        {
          text: "Producto",
          align: "start",
          sortable: false,
          value: "name",
          divider: true,
        },
        {
          text: "Cantidad",
          value: "qty",
          align: "center",
        },
        { text: "Precio U.", value: "price", align: "right", width: "220px" },
        {
          text: "Subtotal",
          value: "subtotal",
          sortable: false,
          align: "right",
          width: "220px",
        },
        { text: "Accion", value: "accion", sortable: false },
      ],
    };
  },
  mounted() {
    const _this = this;
    _this.$eventBus.$on(["PRODUCT_SELECTED"], (product) => {
      _this.productSelected(product);
    });
    this.loadOptions();
  },
  methods: {
    async loadOptions() {
      const _this = this;
      await axios
        .get("/admin/tracking/sales_history/resources")
        .then(function (response) {
          let { categories, currency, exchange_value } = response.data.data;

          _this.options.categories = categories;
          _this.options.currency = currency;
          _this.options.exchange_value = exchange_value;
        });
    },
    saveSnack(product) {
      // product.subtotal =  product.price * product.qty;
      product.subtotal = this.ConvertionExchange(product) * product.qty;
      this.snack = true;
      this.snackColor = "success";
      this.snackText = "Cantidad Actualizada";
      return product;
    },
    saveNewPrice(product, newPrice, priceBase) {
      const lessPrice = priceBase * 0.9;
      if (!this.$gate.allow("isGerente", "tracking")) {
        if (newPrice < lessPrice) {
          this.snack = true;
          this.snackColor = "error";
          this.snackText = "Precio No Permitido";
          product.price = priceBase;
        }
      }
      product.subtotal = this.ConvertionExchange(product) * product.qty;
      return product;
    },
    cancelSnack() {
      this.snack = true;
      this.snackColor = "error";
      this.snackText = "Canceled";
    },
    openSnack() {
      this.snack = true;
      this.snackColor = "info";
      this.snackText = "Dialog opened";
    },
    closeSnack() {
      // console.log("Dialog closed");
    },
    deleteItem(item) {
      const _this = this;
      _this.$store.commit("showDialog", {
        type: "confirm",
        title: "Confirmacion de eliminacion",
        message: "¿Estás seguro de que quieres eliminar este registro?",
        okCb: () => {
          _this.editedIndex = _this.items.indexOf(item);
          _this.items.splice(_this.editedIndex, 1);
          _this.editedIndex = -1;
        },
        cancelCb: () => {
          _this.editedIndex = -1;
          // console.log("CANCEL");
        },
      });
    },
    productSelected(product) {
      const _this = this;
      if (_this.duplicateExists(product)) {
        return _this.$store.commit("showSnackbar", {
          message: "Producto Duplicado ",
          color: "warning",
          duration: 3000,
        });
      }
      // if (_this.sameCurrency(product)) {
      //   return _this.$store.commit("showSnackbar", {
      //     message: "Moneda no Valida",
      //     color: "warning",
      //     duration: 3000,
      //   });
      // }
      return _this.addItem(product);
    },
    duplicateExists(product_selected) {
      const _this = this;
      return _this.items.some((item) => item.sku == product_selected.sku);
    },
    // sameCurrency(product_selected) {
    //   const _this = this;
    //   return !_this.items.every(
    //     (item) => item.currency.id === product_selected.currency.id
    //   );
    // },
    addItem(item) {
      const _this = this;
      let concept = {};
      const {
        price_1,
        price_2,
        price_3,
        price_4,
        price_5,
        price_6,
        price_7,
        price_8,
        price_9,
        price_10,
        price_11,
      } = item;

      concept.id = item.id;
      concept.name = item.name;
      concept.sku = item.sku;
      concept.description = item.description;
      concept.price = item.suggested_price;
      concept.prices = {
        price_1,
        price_2,
        price_3,
        price_4,
        price_5,
        price_6,
        price_7,
        price_8,
        price_9,
        price_10,
        price_11,
      };
      concept.qty = item.qty;
      concept.currency = item.currency;
      concept.subtotal =
        item.qty *
        _this.ConvertionExchange({ ...item, price: item.suggested_price });

      _this.items.push(concept);
      _this.$store.commit("showSnackbar", {
        message: "Producto Agregado",
        color: "success",
        duration: 3000,
      });
    },
    ConvertionExchange(item) {
      const _this = this;
      if (_this.propCurrency.id === item.currency.id) {
        return item.price;
      } else if (_this.propCurrency.id === 2 && item.currency.id === 1)
        return item.price / _this.options.exchange_value;
      else if (_this.propCurrency.id === 1 && item.currency.id === 2)
        return item.price * _this.options.exchange_value;
    },
  },
};
</script>
