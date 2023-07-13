<template>
  <v-data-table
    :headers="headers"
    :items="product_inventory"
    :options.sync="pagination"
    :server-items-length="totalItems"
    class="elevation-1"
    show-group-by
    dense
    >
    <!-- sort-by="calories" -->
    <!-- group-by="location_branch.title" -->
    <template v-slot:top>
      <div class="d-flex flex-row justify-end">
        <v-dialog v-model="dialog" max-width="750px" persistent scrollable>
          <template v-slot:activator="{ on, attrs }">
            <v-btn color="primary" dark class="mb-2" v-bind="attrs" v-on="on">
              Agregar Producto
            </v-btn>
          </template>
          <v-card>
            <v-card-title>
              <span class="text-h5">{{ formTitle }}</span>
              <v-spacer />
              <v-icon @click="close" color="red">mdi-close</v-icon>
            </v-card-title>

            <v-card-text>
              <v-container fluid>
                <product-inventory-form
                  ref="form"
                  :form.sync="editedItem"
                  :options="options"
                ></product-inventory-form>
              </v-container>
            </v-card-text>

            <v-card-actions>
              <v-spacer></v-spacer>
              <v-btn color="blue darken-1" text @click="close"> Cancel </v-btn>
              <v-btn color="blue darken-1" text @click="save"> Save </v-btn>
            </v-card-actions>
          </v-card>
        </v-dialog>
        <v-dialog v-model="dialogDelete" max-width="500px">
          <v-card>
            <v-card-title class="text-h5"
              >Are you sure you want to delete this item?</v-card-title
            >
            <v-card-actions>
              <v-spacer></v-spacer>
              <v-btn color="blue darken-1" text @click="closeDelete"
                >Cancel</v-btn
              >
              <v-btn color="blue darken-1" text @click="deleteItemConfirm"
                >OK</v-btn
              >
              <v-spacer></v-spacer>
            </v-card-actions>
          </v-card>
        </v-dialog>
      </div>
      <v-toolbar flat>
        <v-toolbar-title>Inventario de Productos</v-toolbar-title>
        <v-divider class="mx-4" inset vertical></v-divider>
        <v-spacer></v-spacer>

        <v-toolbar-items>
          <table-header-buttons
            :reloadTable="initialize"
          ></table-header-buttons>
        </v-toolbar-items>
      </v-toolbar>
    </template>
    <template v-slot:item.actions="{ item }">
      <v-icon small class="mr-2" @click="editItem(item)"> mdi-pencil </v-icon>
      <v-icon small @click="deleteItem(item)"> mdi-delete </v-icon>
    </template>
    <template v-slot:no-data>
      <v-btn color="primary" @click="initialize"> Reset </v-btn>
    </template>
  </v-data-table>
</template>

<script>
import TableHeaderButtons from "../../components/shared/TableHeaderButtons.vue";
import ProductInventoryForm from "./ProductInventoryForm.vue";
export default {
  components: { ProductInventoryForm, TableHeaderButtons },
  data: () => ({
    dialog: false,
    dialogDelete: false,
    headers: [
      {
        text: "Nombre Equipo",
        align: "start",
        sortable: true,
        value: "product.name",
        class: "col-3",
        cellClass: "text-uppercase",
      },
      { text: "Categoria", value: "product.category.name" },
      { text: "Serie", value: "num_serie", class: "col-2" },
      { text: "Num Economico", value: "num_economico" },
      { text: "Serie Motor", value: "num_serie_motor" },
      { text: "Factura", value: "invoice" },
      { text: "Ubicacion Fisico", value: "location_branch.title" },
      { text: "Actions", value: "actions", sortable: false },
    ],
    product_inventory: [],
    editedId: -1,
    editedItem: {
      product_id: null,
      location: null,
      assigned_branch: null,
      model: null,
      num_serie: null,
      num_serie_motor: null,
      num_economico: null,
      costo_jd: null,
      costo_etbsa: null,
      invoice: null,
      invoice_date: null,
      arrival_date: null,
    },
    defaultItem: {
      product_id: null,
      location: null,
      assigned_branch: null,
      model: null,
      num_serie: null,
      num_serie_motor: null,
      num_economico: null,
      costo_jd: null,
      costo_etbsa: null,
      invoice: null,
      invoice_date: null,
      arrival_date: null,
    },
    filters: {},
    search: "",
    options: {
      products: [],
      agencies: [],
    },
    totalItems: 0,
    pagination: {
      itemsPerPage: 10,
      page: 1,
    },
  }),

  computed: {
    formTitle() {
      return this.editedId === -1 ? "Nuevo Registro" : "Editar Registro";
    },
  },

  watch: {
    pagination: {
      handler: _.debounce(function () {
        this.initialize();
      }, 999),
      deep: true,
    },
    dialog(val) {
      val || this.close();
    },
    dialogDelete(val) {
      val || this.closeDelete();
    },
  },

  created() {
    this.initialize();
  },

  methods: {
    async initialize() {
      const _this = this;
      let params = {
        ..._this.filters,
        search: _this.search,
        page: _this.pagination.page,
        per_page: _this.pagination.itemsPerPage,
      };
      const {
        data: {
          data: { productInventories, options },
          message,
        },
      } = await axios.get("admin/products/inventory", { params: params });

      _this.product_inventory = productInventories.data;
      _this.totalItems = productInventories.total;
      _this.pagination.totalItems = productInventories.total;
      _this.options = options;

      _this.$store.commit("showSnackbar", {
        message: message,
        color: "success",
        duration: 3000,
      });
    },

    editItem(item) {
      this.editedId = item.id;
      this.editedItem = Object.assign(
        {},
        {
          ...item,
          location: item.location.id,
          assigned_branch: item.assigned_branch.id,
        }
      );
      this.dialog = true;
    },

    deleteItem(item) {
      this.editedId = item.id;
      this.dialogDelete = true;
    },

    async deleteItemConfirm() {
      const {
        data: { message },
      } = await axios.delete(` /admin/products/inventory/${this.editedId}`);
      this.$store.commit("showSnackbar", {
        message: message,
        color: "error",
        duration: 3000,
      });
      this.initialize();
      this.closeDelete();
    },

    close() {
      this.dialog = false;
      this.$nextTick(() => {
        this.editedItem = Object.assign({}, this.defaultItem);
        this.editedId = -1;
      });
    },

    closeDelete() {
      this.dialogDelete = false;
      this.$nextTick(() => {
        this.editedItem = Object.assign({}, this.defaultItem);
        this.editedId = -1;
      });
    },

    async save() {
      if (!this.$refs.form.$refs.formProductInventory.validate()) return;
      let payload = {
        ...this.editedItem,
      };
      if (this.editedId > -1) {
        try {
          const { data: message } = await axios.put(
            `/admin/products/inventory/${this.editedId}`,
            payload
          );
          _this.$store.commit("showSnackbar", {
            message: message,
            color: "success",
            duration: 3000,
          });
        } catch (error) {
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
        }
      } else {
        try {
          const { data: message } = await axios.post(
            "/admin/products/inventory",
            payload
          );
          _this.$store.commit("showSnackbar", {
            message: message,
            color: "success",
            duration: 3000,
          });
        } catch (error) {
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
        }
      }
      this.initialize();
      this.close();
    },
  },
};
</script>