<template>
  <v-row>
    <!-- Products -->
    <v-col cols="12" class="pa-0">
      <!-- <v-card-text class="add-products-form pt-9 pb-10 px-8"> -->
      <div
        v-for="(item, index) in editedItem.products"
        :key="`form-${index}`"
        class="single-product-form"
      >
        <div class="add-products-header mb-2 d-none d-md-flex">
          <div class="px-5 py-2 flex-grow-1 font-weight-semibold">
            <v-row>
              <v-col cols="12" md="6"><span>Producto</span></v-col>
              <v-col cols="12" md="2"><span>Precio U.</span></v-col>
              <v-col cols="12" md="2"><span>Cantidad</span></v-col>
              <v-col cols="12" md="2"><span>Subtoal</span></v-col>
            </v-row>
          </div>
          <div class="header-spacer"></div>
        </div>
        <div class="d-flex v-card v-sheet v-sheet--outlined theme--light">
          <div class="pa-3 flex-grow-1">
            <v-form :ref="`form-${index}`">
              <v-row>
                <v-col cols="12" md="5">
                  <v-select
                    v-model="item.category_id"
                    :items="options.category"
                    item-value="id"
                    item-text="name"
                    class="mb-3"
                    hide-details
                    dense
                    outlined
                    persistent-placeholder
                    label="Categoria Producto"
                    @change="getProductByCategory(item.category_id, index)"
                  ></v-select>

                  <v-autocomplete
                    v-model="item.product"
                    :items="options.products[index]"
                    item-value="id"
                    item-text="name"
                    class="mb-3"
                    hide-details
                    dense
                    outlined
                    persistent-placeholder
                    clearable
                    return-object
                    label="Producto"
                  ></v-autocomplete>
                  <v-textarea
                    v-model="item.product.description"
                    hide-details
                    outlined
                    placeholder="Description"
                    label="Description"
                    readonly
                  ></v-textarea>
                </v-col>
                <v-col cols="12" md="3" sm="4">
                  <v-text-field
                    v-model="item.product.price_1"
                    outlined
                    hide-details
                    dense
                    type="number"
                    label="Precio Unitario"
                  ></v-text-field>
                </v-col>
                <v-col cols="12" md="2" sm="4">
                  <v-text-field
                    v-model="item.quantity"
                    outlined
                    hide-details
                    dense
                    type="number"
                    label="Cantidad"
                  ></v-text-field>
                </v-col>
                <v-col cols="12" md="2" sm="4">
                  <p class="my-2 text-right">
                    <span class="d-inline d-md-none">Subtotal: </span>
                    <span>
                      {{ (item.product.price_1 * item.quantity) | money }}
                      {{ item.is_dollar ? "USD" : "MXN" }}
                    </span>
                  </p>
                </v-col>
              </v-row>
            </v-form>
          </div>
          <div class="d-flex flex-column item-actions rounded-0 pa-1">
            <v-btn icon rounded small @click="deleteProductForm(index)">
              <v-icon>mdi-close</v-icon>
            </v-btn>
          </div>
        </div>
      </div>
      <!-- buttom ADD -->
      <v-btn
        text
        large
        outlined
        color="primary"
        class="mt-4"
        @click="addProductForm()"
      >
        Agregar un Producto
      </v-btn>
    </v-col>
    <v-col cols="12">
      <v-simple-table>
        <tr>
          <td class="pe-16">Subtotal:</td>
          <th class="text-right">{{ editedItem.subtotal | money }}</th>
        </tr>
        <!-- <tr>
          <td class="pe-16">Discount:</td>
          <th class="text-right">$28</th>
        </tr> -->
        <tr>
          <td class="pe-16">Tax:</td>
          <th class="text-right">{{ editedItem.tax | percent }}</th>
        </tr>
      </v-simple-table>
      <v-divider class="mt-4 m-b3" />
      <v-simple-table>
        <tr>
          <td class="pe-16">Total:</td>
          <th class="text-right">{{ editedItem.total | money }}</th>
        </tr>
      </v-simple-table>
    </v-col>
    <v-col cols="12">
      <!-- Observaciones Notas -->
      <v-card-text class="pa-0">
        <p class="font-weight-semibold mb-2">
          Observaciones:
        </p>
        <v-textarea
          v-model="editedItem.observation"
          outlined
          dense
        ></v-textarea>
      </v-card-text>
    </v-col>
  </v-row>
</template>
<script>
let defaultItem = {
  tracking_id: null,
  observation: "",
  subtotal: 0,
  total: 0,
  products: [],
};
export default {
  name: "TrackingQuoteForm",
  props: {
    editedItem: {
      require: true,
      type: Object,
      default: () => {
        return defaultItem;
      },
    },
    editedIndex: {
      require: true,
      type: Number,
    },
  },
  mounted() {
    console.log("mounted FORM QUOTE");
    this.getProductCategory();
  },
  data() {
    return {
      options: {
        category: [],
        products: [],
      },
    };
  },
  watch: {
    "editedItem.products": {
      handler: function (val) {
        this.editedItem.subtotal = val
          .map(
            (item) =>
              parseFloat(item.quantity) * parseFloat(item.product.price_1)
          )
          .reduce((acc, crr) => acc + crr, 0);
        this.Total();
        this.$forceUpdate();
      },
      deep: true,
    },
  },
  methods: {
    getProductCategory() {
      const _this = this;
      axios.get("/admin/quote/options").then(function (res) {
        _this.options.category = res.data.data;
      });
    },
    async getProductByCategory(categoryId, index) {
      const _this = this;

      let products = await axios
        .get(`/admin/quote/products_by_category/${categoryId}`)
        .then(function (res) {
          return res.data.data;
        });
      _this.$nextTick(() => {
        _this.options.products[index] = products;
      });
      this.$forceUpdate();
    },
    addProductForm() {
      const _this = this;
      _this.editedItem.products.push({
        product: {
          id: null,
          name: "",
          description: "",
          price_1: 0,
        },
        currency: null,
        category_id: null,
        model_id: null,
        quantity: 1,
        subtotal: 0,
      });
    },
    deleteProductForm(index) {
      const _this = this;
      this.ne;
      _this.editedItem.products.splice(index, 1);
      _this.options.products.splice(index, 1);
      _this.$forceUpdate();
    },
    Total() {
      const _this = this;
      _this.editedItem.total =
        _this.editedItem.subtotal * _this.editedItem.tax +
        _this.editedItem.subtotal;
    },
  },
};
</script>
<style scoped>
/* .item-actions {
  border-left: thin solid rgba(94, 86, 105, 0.14);
  border-left-width: thin;
  border-left-style: solid;
  border-left-color: rgba(94, 86, 105, 0.14);
}
.add-products-form .single-product-form:not(:first-child) {
  margin-top: 2rem;
} */
</style>
