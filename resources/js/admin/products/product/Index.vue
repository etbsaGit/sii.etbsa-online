<template>
  <div>
    <v-toolbar flat class="text-h4 align-center">
      <v-icon left x-large>mdi-tractor</v-icon>Lista de Productos
      <v-spacer></v-spacer>
      <v-text-field
        v-model="filter.search"
        outlined
        dense
        prepend-icon="mdi-magnify"
        label="Buscar Nombre o SKU"
        class="mr-3"
        hide-details
        clearable
      ></v-text-field>
      <v-btn
        v-if="$gate.allow('isAdmin', 'products')"
        color="primary"
        dark
        @click="create"
      >
        Registrar Producto
      </v-btn>
      <v-spacer />
      <table-header-buttons
        :updateSearchPanel="updateSearchPanel"
        :reloadTable="reloadTable"
        :exportTable="exportTable"
      ></table-header-buttons>
    </v-toolbar>

    <v-data-table
      :headers="headers"
      :items="items"
      :options.sync="pagination"
      :server-items-length="totalItems"
      fixed-header
      calculate-widths
      caption
      dense
    >
      <template v-slot:top>
        <search-panel
          :rightDrawer="rightDrawer"
          @cancelSearch="cancelSearch"
          @resetFilter="resetFilter"
        >
          <v-form ref="formFilter">
            <v-row class="mr-2 offset-1 overline" dense>
              <v-col cols="12">
                <v-select
                  v-model="filter.category_id"
                  :items="options.category"
                  item-value="id"
                  item-text="name"
                  prepend-icon="mdi-magnify"
                  label="Filtrar por Categoria"
                  hide-details
                  outlined
                  clearable
                  filled
                  dense
                  :disabled="!($router.currentRoute.name === 'products.index')"
                ></v-select>
              </v-col>
              <v-col cols="12">
                <v-text-field
                  v-model="filter.sku"
                  prepend-icon="mdi-magnify"
                  label="Filtrar por SKU"
                  hide-details
                  outlined
                  clearable
                  filled
                  dense
                ></v-text-field>
              </v-col>

              <v-col cols="12">
                <v-select
                  v-model="filter.model_id"
                  :items="options.model"
                  item-value="id"
                  item-text="name"
                  prepend-icon="mdi-magnify"
                  label="Filtrar por Modelos"
                  hide-details
                  outlined
                  clearable
                  filled
                  dense
                ></v-select>
              </v-col>
              <v-col cols="12">
                <v-select
                  v-model="filter.agency_id"
                  :items="options.agency"
                  item-value="id"
                  item-text="title"
                  prepend-icon="mdi-magnify"
                  label="Filtrar por Ubicacion (Sucursal)"
                  hide-details
                  outlined
                  clearable
                  filled
                  dense
                ></v-select>
              </v-col>
              <v-col
                cols="12"
                v-if="$router.currentRoute.name === 'products.index'"
              >
                <v-select
                  v-model="filter.desactive"
                  :items="[
                    { text: 'Con', value: 'with' },
                    { text: 'Solamente', value: 'only' },
                  ]"
                  prepend-icon="mdi-check-circle"
                  label="Productos No activos"
                  hide-details
                  clearable
                  outlined
                  filled
                  dense
                ></v-select>
              </v-col>
              <v-col cols="12">
                <v-select
                  v-model="filter.is_usado"
                  :items="[
                    { text: 'Con', value: 'with' },
                    { text: 'Solamente', value: 'only' },
                  ]"
                  prepend-icon="mdi-magnify"
                  label="Productos Usados"
                  hide-details
                  clearable
                  outlined
                  filled
                  dense
                ></v-select>
              </v-col>
              <v-col
                cols="12"
                v-if="$router.currentRoute.name == 'products.index'"
              >
                <v-select
                  v-model="filter.price_type"
                  :items="price_types"
                  prepend-icon="mdi-magnify"
                  label="Forma de Pago"
                  hide-details
                  clearable
                  outlined
                  filled
                  dense
                ></v-select>
              </v-col>
            </v-row>
          </v-form>
        </search-panel>
      </template>

      <template #[`header.qty`]="{ header }">
        <span v-if="$router.currentRoute.name != 'products.index'">
          {{ header.text }}
        </span>
        <span v-else>
          Ultimo Cambio
        </span>
      </template>
      <template #[`item.name`]="{ item }">
        <div class="text-uppercase font-weight-bold">{{ item.name }}</div>
        <div class="caption text--secondary">SKU: {{ item.sku }}</div>
      </template>
      <template #[`item.suggested_price`]="{ item }">
        {{ item.suggested_price | money }} {{ item.is_dollar ? "USD" : "MXN" }}
      </template>
      <template #[`item.active`]="{ value }">
        <v-icon :color="value ? 'green' : 'grey'">mdi-check-circle</v-icon>
      </template>
      <template #[`item.is_usado`]="{ value }">
        <v-icon :color="value ? 'green' : 'grey'">mdi-check-circle</v-icon>
      </template>
      <template #[`item.qty`]="{ item }">
        <v-text-field
          v-if="$router.currentRoute.name != 'products.index'"
          v-model.number="item.qty"
          placeholder="qty"
          append-outer-icon="pz"
          type="number"
          outlined
          hide-details
          dense
          reverse
        ></v-text-field>
        <span v-else>
          {{ $appFormatters.formatDate(item.updated_at, "MMM YYYY") }}
        </span>
      </template>
      <template #[`item.action`]="{ item }">
        <td v-if="$router.currentRoute.name != 'products.index'">
          <v-btn @click="select(item)" icon>
            <v-icon class="green--text">
              mdi-plus-thick
            </v-icon>
          </v-btn>
        </td>
        <td v-else>
          <v-icon right class="green--text" @click="editItem(item)">
            mdi-pencil
          </v-icon>

          <v-icon left class="red--text" @click="deleteItem(item.id)">
            mdi-trash-can
          </v-icon>
        </td>
      </template>
    </v-data-table>

    <v-dialog
      v-if="dialog"
      v-model="dialog"
      max-width="600px"
      scrollable
      persistent
    >
      <v-card>
        <v-toolbar dense dark color="primary" class="text-h6">
          {{ formTitle }}
        </v-toolbar>
        <v-card-text class="pt-4">
          <v-form ref="form" v-model="valid" lazy-validation>
            <v-select
              v-model="form.product_category_id"
              :items="options.category"
              item-text="name"
              item-value="id"
              label="Categoria"
              :rules="[(v) => !!v || 'Es Requerido']"
              outlined
              dense
            />
            <v-select
              v-model="form.product_model_id"
              :items="optionModel"
              item-text="name"
              item-value="id"
              label="Modelo"
              :rules="[(v) => !!v || 'Es Requerido']"
              outlined
              dense
            />
            <v-select
              v-model.number="form.brand_id"
              :items="options.brands"
              item-text="name"
              item-value="id"
              label="Marca"
              :rules="[(v) => !!v || 'Es Requerido']"
              outlined
              dense
            />
            <v-select
              v-model.number="form.supplier_id"
              :items="options.suppliers"
              item-text="name"
              item-value="id"
              label="Proveedor"
              :rules="[(v) => !!v || 'Es Requerido']"
              outlined
              dense
            />

            <v-text-field
              v-model="form.name"
              label="Nombre Producto"
              type="text"
              :rules="[(v) => !!v || 'Es Requerido']"
              outlined
              dense
            />
            <v-textarea
              v-model="form.description"
              label="Descripcion"
              type="text"
              :rules="[(v) => !!v || 'Es Requerido']"
              outlined
              dense
            />
            <v-text-field
              v-model="form.sku"
              label="SKU"
              type="text"
              :rules="[(v) => !!v || 'Es Requerido']"
              outlined
              dense
            />
            <v-select
              v-model="form.agency_id"
              :items="options.agency"
              item-text="title"
              item-value="id"
              label="Ubicacion"
              :rules="[(v) => !!v || 'Es Requerido']"
              outlined
              dense
            />
            <div class="d-flex flex-row justify-space-around">
              <v-switch
                v-model="form.active"
                label="Activo"
                class="mx-2"
                outlined
                dense
              />
              <v-switch
                v-model="form.is_usado"
                label="Es Usado"
                class="mx-2"
                outlined
                dense
              />
              <v-switch
                v-model="form.is_dollar"
                label="(DOLLAR) USD"
                class="mx-2"
                outlined
                dense
              />
            </div>

            <v-currency-field
              v-model.number="form.price_1"
              :default-value="form.price_1"
              label="Precio Lista"
              prefix="$"
              :suffix="form.is_dollar ? 'USD' : 'MXN'"
              type="number"
              :rules="[(v) => !!v || 'Es Requerido o Poner Valor en Cero']"
              outlined
              dense
            />
            <v-currency-field
              v-model.number="form.price_2"
              :default-value="form.price_2"
              label="Precio Contado"
              prefix="$"
              :suffix="form.is_dollar ? 'USD' : 'MXN'"
              type="number"
              :rules="[(v) => !!v || 'Es Requerido o Poner Valor en Cero']"
              outlined
              dense
            />
            <v-currency-field
              v-model.number="form.price_3"
              :default-value="form.price_3"
              label="Precio Financiamiento a 2 años"
              prefix="$"
              :suffix="form.is_dollar ? 'USD' : 'MXN'"
              type="number"
              :rules="[(v) => !!v || 'Es Requerido o Poner Valor en Cero']"
              outlined
              dense
            />
            <v-currency-field
              v-model.number="form.price_4"
              :default-value="form.price_4"
              label="Precio Financiamiento a 5 años"
              prefix="$"
              :suffix="form.is_dollar ? 'USD' : 'MXN'"
              type="number"
              :rules="[(v) => !!v || 'Es Requerido o Poner Valor en Cero']"
              outlined
              dense
            />
            <v-currency-field
              v-model.number="form.price_5"
              :default-value="form.price_5"
              label="Precio EXPO"
              prefix="$"
              :suffix="form.is_dollar ? 'USD' : 'MXN'"
              type="number"
              :rules="[(v) => !!v || 'Es Requerido o Poner Valor en Cero']"
              outlined
              dense
            />
            <v-currency-field
              v-model.number="form.price_6"
              :default-value="form.price_6"
              label="Precio por Volumen"
              prefix="$"
              :suffix="form.is_dollar ? 'USD' : 'MXN'"
              type="number"
              :rules="[(v) => !!v || 'Es Requerido o Poner Valor en Cero']"
              outlined
              dense
            />
            <v-currency-field
              v-model.number="form.price_7"
              :default-value="form.price_7"
              label="Precio Renta/Arrendamiento 1 mes"
              prefix="$"
              :suffix="form.is_dollar ? 'USD' : 'MXN'"
              type="number"
              :rules="[(v) => !!v || 'Es Requerido o Poner Valor en Cero']"
              outlined
              dense
            />
            <v-currency-field
              v-model.number="form.price_8"
              :default-value="form.price_8"
              label="Precio Renta/Arrendamiento 2 meses"
              prefix="$"
              :suffix="form.is_dollar ? 'USD' : 'MXN'"
              type="number"
              :rules="[(v) => !!v || 'Es Requerido o Poner Valor en Cero']"
              outlined
              dense
            />
            <v-currency-field
              v-model.number="form.price_9"
              :default-value="form.price_9"
              label="Precio Renta/Arrendamiento 3 meses"
              prefix="$"
              :suffix="form.is_dollar ? 'USD' : 'MXN'"
              type="number"
              :rules="[(v) => !!v || 'Es Requerido o Poner Valor en Cero']"
              outlined
              dense
            />
            <v-currency-field
              v-model.number="form.price_10"
              :default-value="form.price_10"
              label="Precio Credito 30 dias"
              prefix="$"
              :suffix="form.is_dollar ? 'USD' : 'MXN'"
              type="number"
              :rules="[(v) => !!v || 'Es Requerido o Poner Valor en Cero']"
              outlined
              dense
            />
          </v-form>
          <div class="d-flex"></div>
        </v-card-text>
        <v-card-actions>
          <v-btn text color="error" @click="dialog = false">
            Cancel
          </v-btn>
          <v-spacer />
          <v-btn :loading="loading" color="primary" @click="submit">
            Save
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </div>
</template>
<script>
import TableHeaderButtons from "../../components/shared/TableHeaderButtons.vue";
import SearchPanel from "@admin/components/shared/SearchPanel.vue";
export default {
  components: { TableHeaderButtons, SearchPanel },
  name: "ProductsList",
  props: {
    Filters: {
      type: Object,
      default: () => {
        return {
          search: "",
          name: "",
          sku: "",
          category_id: null,
          model_id: null,
          is_active: null,
          is_usado: null,
          desactive: null,
          price_type: null,
        };
      },
      require: false,
    },
  },
  mounted() {
    const _this = this;
    _this.$store.commit("setBreadcrumbs", [{ label: "Productos", name: "" }]);
    // // this.loadProducts();
  },
  data() {
    return {
      headers: [
        {
          text: "ID",
          value: "id",
          align: "left",
          sortable: false,
        },
        {
          text: "Nombre Producto",
          value: "name",
          align: "left",
        },
        {
          text: "Categoria",
          value: "category.name",
          align: "left",
          sortable: false,
        },
        {
          text: "Ubicacion",
          value: "agency.title",
          align: "left",
          sortable: false,
        },
        {
          text: "Precio Sugerido",
          value: "suggested_price",
          align: "left",
        },
        {
          text: "Activo",
          value: "active",
          align: "left",
          sortable: false,
        },
        {
          text: "Usado",
          value: "is_usado",
          align: "left",
          sortable: false,
        },

        {
          text: "Cantidad",
          value: "qty",
          align: "center",
          sortable: false,
          width: "150",
        },
        { text: "Action", value: "action", align: "left", sortable: false },
      ],
      valid: true,
      dialog: false,
      loading: false,
      isUpdate: false,
      errors: {},
      form: {
        product_category_id: null,
        product_model_id: null,
        agency_id: null,
        brand_id: null,
        supplier_id: null,
        name: "",
        description: "",
        sku: "",
        active: false,
        is_usado: false,
        is_dollar: false,
        price_1: 0,
        price_2: 0,
        price_3: 0,
        price_4: 0,
        price_5: 0,
        price_6: 0,
        price_7: 0,
        price_8: 0,
        price_9: 0,
        price_10: 0,
      },
      formDefault: {
        product_category_id: null,
        product_model_id: null,
        agency_id: null,
        brand_id: null,
        supplier_id: null,
        name: "",
        description: "",
        sku: "",
        active: false,
        is_usado: false,
        is_dollar: false,
        price_1: 0,
        price_2: 0,
        price_3: 0,
        price_4: 0,
        price_5: 0,
        price_6: 0,
        price_7: 0,
        price_8: 0,
        price_9: 0,
        price_10: 0,
      },
      rules: {},
      items: [],
      totalItems: 0,
      pagination: {
        itemsPerPage: 10,
        page: 1,
      },
      filter: { ...this.Filters },
      options: {
        category: [],
        model: [],
        agency: [],
        brands: [],
        suppliers: [],
      },
      price_types: [
        { text: "Por Definir", value: "por_definir" },
        { text: "P. Lista", value: "precio_lista" },
        { text: "Contado", value: "contado" },
        { text: "JDF 2 años", value: "jdf_2y" },
        { text: "JDF 5 años", value: "jdf_5y" },
        { text: "Expo", value: "precio_expo" },
        { text: "Precio Volumen", value: "por_volumen" },
        { text: "Arrendamiento", value: "renta_1" },
        { text: "Arrendamiento 2 meses", value: "renta_2" },
        { text: "Arrendamiento +3 meses", value: "renta_3" },
        { text: "Credito 30 dias", value: "credito_30d" },
      ],
      showSearchPanel: false,
    };
  },
  watch: {
    pagination: {
      handler: _.debounce(function (v) {
        this.loadProducts(() => {});
      }, 700),
      deep: true,
    },
    filter: {
      handler: _.debounce(function (v) {
        this.pagination.page = 1;
        this.loadProducts(() => {});
      }, 1000),
      deep: true,
    },
    dialog(val) {
      val || this.close();
    },
    dialogDelete(val) {
      val || this.closeDelete();
    },
  },
  computed: {
    formTitle() {
      return this.isUpdate ? "Editar Producto" : "Registrar Producto";
    },
    optionModel() {
      const _this = this;
      let category = _this.options.category.filter(
        (e) => e.id == _this.form.product_category_id
      );
      return category.length > 0 ? category[0].models : [];
    },
    rightDrawer: {
      get() {
        return this.showSearchPanel;
      },
      set(_showSearchPanel) {
        this.showSearchPanel = _showSearchPanel;
      },
    },
  },
  methods: {
    reloadTable() {
      this.loadProducts(() => {});
    },
    async loadProducts(cb) {
      const _this = this;

      let params = {
        ..._this.filter,
        order_by: _this.pagination.sortBy[0] || "id",
        search: _this.filter.search,
        order_sort: _this.pagination.sortDesc[0] ? "asc" : "desc",
        order_by: _this.pagination.sortBy[0] || "id",
        page: _this.pagination.page,
        per_page: _this.pagination.itemsPerPage,
      };

      await axios.get("/admin/products", { params }).then((response) => {
        _this.items = response.data.data.data.data;
        _this.options = response.data.data.options;
        _this.totalItems = response.data.data.data.total;
        _this.pagination.totalItems = response.data.data.data.total;
        (cb || Function)();
      });
    },
    async exportTable(cb) {
      const _this = this;

      let params = {
        ..._this.filter,
        paginate: "no",
      };

      await axios
        .get("/admin/product-export", { params, responseType: "blob" })
        .then((res) => {
          const url = window.URL.createObjectURL(new Blob([res.data]));
          const link = document.createElement("a");
          link.href = url;
          link.setAttribute("download", "products.xlsx"); //or any other extension
          document.body.appendChild(link);
          link.click();
        })
        .catch(function (error) {
          if (error.response) {
            _this.$store.commit("showSnackbar", {
              message: error.response.data.message,
              color: "error",
              duration: 3000,
            });
          } else if (error.request) {
            console.log(error.request);
          } else {
            console.log("Error", error.message);
          }
        });
    },

    deleteItem(item) {
      this.editedId = item;
      this.dialogDelete = true;
    },
    deleteItemConfirm() {
      // Axios Delete
      this.closeDelete();
    },
    close() {
      this.dialog = false;
      this.$nextTick(() => {
        this.editedId = -1;
      });
    },
    closeDelete() {
      this.dialogDelete = false;
      this.$nextTick(() => {
        this.editedId = -1;
        this.loadProducts();
      });
    },
    create() {
      const _this = this;
      _this.dialog = true;
      _this.editedId = -1;
      _this.$refs.form.reset();
      _this.$refs.form.resetValidation();
      setTimeout(() => {
        _this.form = { ...this.formDefault };
      }, 1000);
    },
    editItem(item) {
      const _this = this;
      _this.dialog = true;
      setTimeout(() => {
        _this.$refs.form.resetValidation();
        _this.editedId = item.id;
        _this.form.product_category_id = item.product_category_id;
        _this.form.product_model_id = item.product_model_id;
        _this.form.brand_id = item.brand_id;
        _this.form.supplier_id = item.supplier_id;
        _this.form.agency_id = item.agency_id;
        _this.form.name = item.name;
        _this.form.description = item.description;
        _this.form.sku = item.sku;
        _this.form.active = item.active;
        _this.form.is_usado = item.is_usado;
        _this.form.is_dollar = item.is_dollar;
        _this.form.price_1 = item.price_1;
        _this.form.price_2 = item.price_2;
        _this.form.price_3 = item.price_3;
        _this.form.price_4 = item.price_4;
        _this.form.price_5 = item.price_5;
        _this.form.price_6 = item.price_6;
        _this.form.price_7 = item.price_7;
        _this.form.price_8 = item.price_8;
        _this.form.price_9 = item.price_9;
        _this.form.price_10 = item.price_10;
      }, 1000);
    },
    async submit() {
      const _this = this;
      if (!this.$refs.form.validate()) {
        return;
      }
      let payload = {
        ..._this.form,
        active: !!_this.form.active,
        is_usado: Boolean(_this.form.is_usado),
        is_dollar: !!_this.form.is_dollar,
        currency_id: !!_this.form.is_dollar ? 2 : 1,
      };
      if (_this.editedId === -1) {
        //create
        console.log("Store", this.form);
        await axios
          .post("/admin/products", payload)
          .then((response) => {
            _this.$store.commit("showSnackbar", {
              message: response.data.message,
              color: "success",
              duration: 3000,
            });
            _this.loadProducts();
            _this.close();
            // _this.$emit("submit");
          })
          .catch(function (error) {
            if (error.response) {
              _this.$store.commit("showSnackbar", {
                message: error.response.data.message,
                color: "error",
                duration: 3000,
              });
            } else if (error.request) {
              console.log(error.request);
            } else {
              console.log("Error", error.message);
            }
          });
      } else {
        //update
        console.log("Update", this.form, this.editedId);
        await axios
          .put(`/admin/products/${_this.editedId}`, payload)
          .then(function (response) {
            _this.$store.commit("showSnackbar", {
              message: response.data.message,
              color: "success",
              duration: 3000,
            });
            _this.loadProducts();
            _this.close();
            // _this.$emit("submit");
          })
          .catch(function (error) {
            if (error.response) {
              _this.$store.commit("showSnackbar", {
                message: error.response.data.message,
                color: "error",
                duration: 3000,
              });
            } else if (error.request) {
              console.log(error.request);
            } else {
              console.log("Error", error.message);
            }
          });
      }
    },
    select(product) {
      const _this = this;
      _this.$eventBus.$emit("PRODUCT_SELECTED", product);
    },
    cancelSearch() {
      this.showSearchPanel = false;
    },
    updateSearchPanel() {
      this.rightDrawer = !this.rightDrawer;
    },
    resetFilter() {
      const _this = this;
      _this.$refs.formFilter.reset();
      _this.pagination.itemsPerPage = 10;
      _this.pagination.page = 1;
    },
  },
};
</script>
<style></style>
