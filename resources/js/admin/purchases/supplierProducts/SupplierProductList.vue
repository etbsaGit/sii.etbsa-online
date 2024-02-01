<template>
  <v-card flat>
    <v-card-title>
      Productos del Proveedor
      <v-spacer></v-spacer>
      <v-btn @click="dialog = true" dark>Agregar Nuevo Producto</v-btn>
      <v-dialog v-model="dialog">
        <supplier-product-form :form.sync="editedItem"></supplier-product-form>
      </v-dialog>
    </v-card-title>
    <v-card-title>
      <v-spacer></v-spacer>
      <v-text-field outlined label="Buscar" dark></v-text-field>
    </v-card-title>
    <v-card-text>
      <v-data-table
        :headers="headers"
        :items="items"
        :options.sync="pagination"
        :server-items-length="totalItems"
        fixed-header
        calculate-widths
        caption
        dense
      ></v-data-table>
    </v-card-text>
  </v-card>
</template>
<script>
import SupplierProductForm from "./SupplierProductForm.vue";
export default {
  components: { SupplierProductForm },
  name: "SupplierProductList",
  props: {
    supplierIdProp: {
      require: false,
      type: Number | String,
    },
    paginateProp: {
      require: false,
      type: String,
      default: () => {
        return null;
      },
    },
  },
  data() {
    return {
      items: [],
      headers: [
        {
          text: "ID",
          value: "id",
          align: "left",
          sortable: false,
        },
        {
          text: "SKU",
          value: "sku",
          align: "left",
        },
        {
          text: "Nombre Producto",
          value: "name",
          align: "left",
        },
        {
          text: "Clave Unidad",
          value: "unit",
          align: "left",
        },
        {
          text: "Acciones",
          value: "actions",
          align: "left",
          sortable: false,
        },
      ],
      pagination: {
        itemsPerPage: 10,
        page: 1,
      },
      totalItems: 0,
      dialog: false,
      dialogDelete: false,
      editedItemId: -1,
      editedItem: {
        name: "",
        description: "",
        sku: "",
        clave_unit_sat: null,
      },
      defaultItem: {
        name: "",
        description: "",
        sku: "",
        clave_unit_sat: null,
      },
      options: {
        claveUnitSat: [],
      },
    };
  },
  mounted() {
    this.getSupplierProducts();
  },
  watch: {
    supplierIdProp: {
      handler: (val) => {
        this.getSupplierProducts();
      },
    },
  },
  methods: {
    async getSupplierProducts(cb) {
      const _this = this;

      let params = {
        // ..._this.filters,
        supplier_id: _this.supplierIdProp || null,
        paginate: _this.paginateProp,
        // order_by: _this.pagination.sortBy[0] || "id",
        // search: _this.filters.search,
        // order_sort: _this.pagination.sortDesc[0] ? "asc" : "desc",
        // order_by: _this.pagination.sortBy[0] || "id",
        // page: _this.pagination.page,
        // per_page: _this.pagination.itemsPerPage,
      };

      await axios
        .get("/admin/supplier-products", { params })
        .then((response) => {
          _this.items = response.data;
          // _this.options = response.data.data.options;
          _this.totalItems = response.data.data.data.total;
          _this.pagination.totalItems = response.data.data.data.total;
          (cb || Function)();
        });
    },
  },
};
</script>
<style></style>
