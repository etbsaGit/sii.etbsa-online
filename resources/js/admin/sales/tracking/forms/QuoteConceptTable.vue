<template>
  <v-card flat>
    <v-card-title>
      <v-spacer></v-spacer>
      <v-select
        v-model="Payment"
        :items="optionsPaymentcondition"
        label="Condicion de Pago"
        placeholder="Placeholder"
        class="py-2"
        style="max-width: 250px;"
        hide-details
        outlined
        dense
      ></v-select>
      <!-- :disabled="items.length > 0" -->
      <!-- <v-btn color="red" text @click="items = []">Borrar Productos</v-btn> -->
    </v-card-title>
    <v-data-table
      :headers="headers"
      :items="items"
      :items-per-page="-1"
      fixed-header
      hide-default-footer
      mobile-breakpoint="0"
      dense
    >
      <template v-slot:[`item.name`]="{ item }">
        <v-list-item dense class="pa-0">
          <v-list-item-content>
            <v-list-item-title class="title overline">
              {{ item.name }}
            </v-list-item-title>
            <v-list-item-subtitle class="title overline">
              SKU: {{ item.sku }}
            </v-list-item-subtitle>
            <div
              v-text="item.description"
              class="overflow-auto"
              style="width: 120px;"
            />
          </v-list-item-content>
        </v-list-item>
      </template>
      <template v-slot:[`item.price`]="{ item, value }">
        <!-- v-if="!$gate.allow('assignSeller', 'tracking')" -->

        <!-- $gate.allow('isGerente', 'tracking') -->
        <v-edit-dialog
          v-if="$gate.allow('isGerente', 'tracking')"
          :return-value.sync="item.price"
          @save="saveSnack(item)"
          @cancel="cancelSnack"
          @open="openSnack"
          @close="closeSnack"
          save-text="Guardar"
          cancel-text="Cancelar"
          persistent
          large
        >
          <v-currency-field
            :value="item.price"
            :default-value="item.price"
            outlined
            dense
            hide-details
            reverse
            single-line
            class="py-1"
            suffix="$"
            :prefix="item.currency.name"
            readonly
          ></v-currency-field>
          <template v-slot:input>
            <div class="mt-4 text-h6">
              Actualizar Precio
            </div>
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
        <span v-else class="font-weight-medium">
          {{ value | money }} {{ item.currency.name }}
        </span>
      </template>
      <template v-slot:[`item.qty`]="{ item, value }">
        <span v-if="readOnly">{{ value }}</span>
        <v-edit-dialog
          v-else
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
            style="max-width: 50px;"
            class="py-1"
            hide-details
            single-line
            readonly
            outlined
            reverse
            dense
          ></v-text-field>
          <template v-slot:input>
            <div class="mt-4 text-h6">
              Actualizar Cantidad
            </div>
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
      <template v-slot:[`item.subtotal`]="{ item, value }">
        {{ value | money }} {{ item.currency.name }}
      </template>
      <template v-slot:[`item.accion`]="{ item }">
        <div class="d-flex flex-row">
          <v-icon small righ color="red" @click="deleteItem(item)">
            mdi-trash-can
          </v-icon>
        </div>
      </template>
    </v-data-table>
    <v-dialog v-model="Dialog" max-width="90%">
      <v-card>
        <v-card-title>
          <span class="text-h5">{{ Title }} Productos</span>
        </v-card-title>
        <v-card-text>
          <v-container>
            <products-list
              v-if="Dialog"
              :Filters="{
                price_type: Payment,
                category_id: Category_id,
              }"
            ></products-list>
          </v-container>
        </v-card-text>
      </v-card>
    </v-dialog>
    <v-snackbar v-model="snack" :timeout="3000" :color="snackColor">
      {{ snackText }}

      <template v-slot:action="{ attrs }">
        <v-btn v-bind="attrs" text @click="snack = false">
          Close
        </v-btn>
      </template>
    </v-snackbar>
  </v-card>
</template>
<script>
import ProductsList from "../../../products/product/Index.vue";

const _paymentCondition = [
  { text: "Por Definir", value: "por_definir", type_price: "" },
  { text: "P. Lista", value: "precio_lista", type_price: "price_1" },
  { text: "Contado", value: "contado", type_price: "price_2" },
  { text: "JDF 2 años", value: "jdf_2y", type_price: "price_3" },
  { text: "JDF 5 años", value: "jdf_5y", type_price: "price_4" },
  { text: "Expo", value: "precio_expo", type_price: "price_5" },
  { text: "Precio Volumen", value: "por_volumen", type_price: "price_6" },
  { text: "Arrendamiento", value: "renta_1", type_price: "price_7" },
  { text: "Arrendamiento 2 meses", value: "renta_2", type_price: "price_8" },
  { text: "Arrendamiento +3 meses", value: "renta_3", type_price: "price_9" },
  { text: "Credito 30 Dias", value: "credito_30d", type_price: "price_10" },
  { text: "Arrendadoras", value: "arrendadoras", type_price: "price_11" },
];

export default {
  components: { ProductsList },
  name: "QuoteConceptTable",
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
    optionsPaymentcondition: {
      type: Array,
      default: () => {
        return _paymentCondition;
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
  },
  watch: {
    Payment(v, old) {
      console.log(v, old);
      const _this = this;
      let price_type =
        _paymentCondition.find((p) => p.value == v).type_price ?? "";
      _this.items.forEach((product) => {
        product.price = product.prices[price_type] ?? product.price;
        product.subtotal = product.qty * product.price;
      });
    },
  },
  data() {
    return {
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
          width: "120px",
        },
        { text: "Precio U.", value: "price", align: "right", width: "220px" },
        {
          text: "Subtotal",
          value: "subtotal",
          sortable: false,
          align: "right",
          width: "120px",
        },
        { value: "accion", sortable: false, width: "120px" },
      ],
    };
  },
  mounted() {
    const _this = this;
    _this.$eventBus.$on(["PRODUCT_SELECTED"], (product) => {
      _this.productSelected(product);
    });
  },
  methods: {
    saveSnack(product) {
      product.subtotal = product.price * product.qty;
      this.snack = true;
      this.snackColor = "success";
      this.snackText = "Cantidad Actualizada";
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
      console.log("Dialog closed");
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
          console.log("CANCEL");
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
      if (_this.sameCurrency(product)) {
        return _this.$store.commit("showSnackbar", {
          message: "Moneda no Valida",
          color: "warning",
          duration: 3000,
        });
      }
      return _this.addItem(product);
    },
    duplicateExists(product_selected) {
      const _this = this;
      return _this.items.some((item) => item.sku == product_selected.sku);
    },
    sameCurrency(product_selected) {
      const _this = this;
      return !_this.items.every(
        (item) => item.currency.id === product_selected.currency.id
      );
    },
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
      concept.subtotal = item.qty * item.suggested_price;

      _this.items.push(concept);
      _this.$store.commit("showSnackbar", {
        message: "Producto Agregado",
        color: "success",
        duration: 3000,
      });
    },
  },
};
</script>
