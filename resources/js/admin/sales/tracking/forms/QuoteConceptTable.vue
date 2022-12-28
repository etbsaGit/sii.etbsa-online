<template>
  <v-card flat>
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
            <v-list-item-title class="title overline" v-text="item.name" />
            <v-list-item-subtitle
              class="title overline"
              v-text="`SKU: ${item.sku}`"
            />
            <div
              v-text="item.description"
              class="overflow-auto"
              style="width: 120px;"
            />
          </v-list-item-content>
        </v-list-item>
      </template>
      <template v-slot:[`item.price`]="{ item, value }">
        <span class="font-weight-medium">
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
            readonly
            outlined
            dense
            :value="item.qty"
            style="max-width: 50px;"
            hide-details
            reverse
            single-line
            class="py-1"
          ></v-text-field>
          <template v-slot:input>
            <div class="mt-4 text-h6">
              Actualizar Cantidad
            </div>
            <v-text-field
              v-model="item.qty"
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
    <v-dialog v-model="Dialog" max-width="1200px">
      <v-card>
        <v-card-title>
          <span class="text-h5">{{ Title }} Productos</span>
        </v-card-title>
        <v-card-text>
          <v-container>
            <products-list></products-list>
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
export default {
  components: { ProductsList },
  name: "QuoteConceptTable",
  props: {
    dialogForm: {
      default: false,
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
        { text: "Precio U.", value: "price", align: "right", width: "120px" },
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
    Amount(item) {
      return item.qty * item.price - item.discount;
    },
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
      const { id, name, sku, description, price_1, qty, currency } = item;

      concept.id = id;
      concept.name = name;
      concept.sku = sku;
      concept.qty = qty;
      concept.description = description;
      concept.price = price_1;
      concept.currency = currency;
      concept.subtotal = qty * price_1;

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
