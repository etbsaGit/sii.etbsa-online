<template>
  <v-container fluid>
    <v-data-table
      :headers="headers"
      :items="items"
      :page.sync="page"
      :items-per-page="itemsPerPage"
      :server-items-length="totalItems"
      @page-count="pageCount = $event"
      class="elevation-2 text-uppercase text--primary"
      hide-default-footer
      :height="minHeight"
      fixed-header
      dense
    >
      <template v-slot:top>
        <search-panel
          :rightDrawer="rightDrawer"
          @cancelSearch="cancelSearch"
          @resetFilter="cleanFilter"
        >
          <v-form ref="filterForm">
            <v-row class="mr-2 offset-1 overline" dense>
              <v-col cols="12" xs="12" class="py-0">
                <v-text-field
                  v-model="filters.product_name"
                  prepend-icon="mdi-magnify"
                  type="text"
                  label="Descripcion Producto"
                  clearable
                ></v-text-field>
              </v-col>
              <v-col cols="12" xs="12" class="py-0">
                <v-text-field
                  v-model="filters.customer_name"
                  prepend-icon="mdi-magnify"
                  type="text"
                  label="Nombre Cliente"
                  clearable
                ></v-text-field>
              </v-col>
              <v-col cols="12" xs="12" class="py-0">
                <v-select
                  v-model="filters.years"
                  :items="options.years"
                  label="Filtro por Años"
                  item-text="ANIO"
                  item-value="ANIO"
                  dense
                  filled
                  multiple
                  outlined
                  clearable
                  small-chips
                  hide-details
                  deletable-chips
                  prepend-icon="mdi-calendar"
                  class="mt-2"
                ></v-select>
              </v-col>
              <v-col cols="12" xs="12" class="py-0">
                <v-autocomplete
                  v-model="filters.months"
                  hide-details
                  outlined
                  dense
                  filled
                  multiple
                  small-chips
                  deletable-chips
                  clearable
                  prepend-icon="mdi-calendar-month"
                  label="Filtro por Mes"
                  :items="options.months"
                  item-text="MES"
                  item-value="MES"
                  class="mt-2"
                ></v-autocomplete>
              </v-col>
              <v-col cols="12" xs="12" class="py-0">
                <v-autocomplete
                  v-model="filters.agencies"
                  hide-details
                  outlined
                  dense
                  filled
                  multiple
                  small-chips
                  deletable-chips
                  clearable
                  prepend-icon="mdi-filter-variant"
                  label="Filtro por Sucursal"
                  :items="options.agencies"
                  item-text="SUCURSAL"
                  item-value="SUCURSAL"
                  class="mt-2"
                ></v-autocomplete>
              </v-col>
              <v-col cols="12" xs="12" class="py-0">
                <v-autocomplete
                  v-model="filters.type_of_sale"
                  hide-details
                  outlined
                  dense
                  filled
                  multiple
                  small-chips
                  deletable-chips
                  clearable
                  prepend-icon="mdi-filter-variant"
                  label="Filtro por Tipo de Venta"
                  :items="options.type_of_sale"
                  item-text="TIPO_DE_VENTA"
                  item-value="TIPO_DE_VENTA"
                  class="mt-2"
                ></v-autocomplete>
              </v-col>
              <v-col cols="12" xs="12" class="py-0">
                <v-autocomplete
                  v-model="filters.brands"
                  hide-details
                  outlined
                  dense
                  filled
                  multiple
                  small-chips
                  deletable-chips
                  clearable
                  prepend-icon="mdi-filter-variant"
                  label="Filtro por Marca"
                  :items="options.brands"
                  item-text="MARCA"
                  item-value="MARCA"
                  class="mt-2"
                ></v-autocomplete>
              </v-col>
              <v-col cols="12" xs="12" class="py-0">
                <v-autocomplete
                  v-model="filters.seller"
                  hide-details
                  outlined
                  dense
                  filled
                  multiple
                  small-chips
                  deletable-chips
                  clearable
                  prepend-icon="mdi-filter-variant"
                  label="Filtro por Vendedor"
                  :items="options.seller"
                  item-text="VENDEDOR"
                  item-value="VENDEDOR"
                  class="mt-2"
                ></v-autocomplete>
              </v-col>
              <v-row class="justify-space-around mx-2 justify-center">
                <v-checkbox
                  v-model="filters.isMXN"
                  label="MXN"
                  hide-details
                ></v-checkbox>
                <v-checkbox
                  v-model="filters.isUSD"
                  label="USD"
                  hide-details
                ></v-checkbox>
              </v-row>
            </v-row>
          </v-form>
        </search-panel>
        <v-toolbar flat dense>
          <v-toolbar-title> Historico de Ventas</v-toolbar-title>
          <v-spacer></v-spacer>
          <v-toolbar-items>
            <v-btn-toggle borderless>
              <v-btn value="left">
                <span class="hidden-sm-and-down">total Registros:</span>
                <span class="hidden-sm-and-down">{{ totalItems }}</span>
              </v-btn>
            </v-btn-toggle>
            <v-overflow-btn
              v-model="itemsPerPage"
              :items="[5, 10, 25, 50, 100]"
              editable
              label="Items Pagina"
              hide-details
              class="pa-0 pr-2"
              style="max-width: 100px;"
            ></v-overflow-btn>
            <table-header-buttons
              :updateSearchPanel="updateSearchPanel"
              :reloadTable="loadResources"
              :exportTable="excelGenerate"
            >
            </table-header-buttons>
          </v-toolbar-items>
        </v-toolbar>
      </template>
      <template v-slot:[`item.MES`]="{ item }">
        <span class="caption text--primary">
          {{ item.MES }} {{ item.ANIO }}
        </span>
      </template>
      <template v-slot:[`item.MARCA`]="{ item }">
        <span class="caption text--primary">{{ item.MARCA }}</span>
      </template>
      <template v-slot:[`item.DESCRIPCION_PRODUCTO`]="{ item }">
        <span class="caption text--primary">{{
          item.DESCRIPCION_PRODUCTO
        }}</span>
      </template>
      <template v-slot:[`item.NOMBRE_CLIENTE`]="{ item }">
        <span class="caption text--primary">{{ item.NOMBRE_CLIENTE }}</span>
      </template>
      <template v-slot:[`item.MONEDA`]="{ item }">
        {{ item.MONEDA || "MXN" }}
      </template>
      <template v-slot:[`item.PRECIO_VENTA`]="{ item }">
        {{ item.PRECIO_VENTA | money() }}
      </template>
      <template v-slot:[`item.TOTAL_COSTO`]="{ item }">
        {{ item.TOTAL_COSTO | money() }}
      </template>
      <template v-slot:[`item.TOTAL`]="{ item }">
        <v-tooltip top>
          <template v-slot:activator="{ on, attrs }">
            <span v-bind="attrs" v-on="on">
              <!-- {{ item.TOTAL | money(item.MONEDA || "MXN") }} -->
              {{ item.TOTAL | money() }}
            </span>
          </template>
          <span>Folio Fiscal: {{ item.FOLIO_FISCAL }}</span>
        </v-tooltip>
      </template>
      <template v-slot:[`item.PORCENTAJE_MARGEN`]="{ item }">
        <v-chip :color="getColor(item.PORCENTAJE_MARGEN * 100)" small>
          {{ item.PORCENTAJE_MARGEN | percent() }}
        </v-chip>
      </template>

      <template v-slot:footer>
        <v-pagination
          light
          v-model="page"
          :page="page"
          :length="pageCount"
          :total-visible="16"
          prev-icon="mdi-arrow-left"
          next-icon="mdi-arrow-right"
          class="mx-auto"
        ></v-pagination>
      </template>
    </v-data-table>
  </v-container>
</template>

<script>
import SearchPanel from "@admin/components/shared/SearchPanel.vue";
import TableHeaderButtons from "@admin/components/shared/TableHeaderButtons.vue";
export default {
  name: "HistoricalSales",
  components: { SearchPanel, TableHeaderButtons },
  mounted() {
    const _this = this;
    _this.loadSalesHistory(() => {});
    _this.loadResources(() => {});
  },
  data() {
    return {
      showSearchPanel: false,
      page: 1,
      pageCount: 0,
      itemsPerPage: 10,
      totalItems: 0,
      // Table data.
      items: [],
      currentItems: [],
      options: {
        years: [],
        months: [],
        type_of_sale: [],
        brands: [],
        agencies: [],
        seller: [],
      },
      filters: {
        product_name: "",
        customer_name: "",
        years: [],
        months: [],
        type_of_sale: [],
        brands: [],
        agencies: [],
        seller: [],
        isMXN: null,
        isUSD: null,
      },
    };
  },
  computed: {
    headers() {
      return [
        {
          text: "Mes / Año",
          align: "left",
          sortable: false,
          value: "MES",
          class: "font-weight-bold",
        },
        // { text: "Mes", align: "right", sortable: false, value: "MES" },
        {
          text: "Marca",
          align: "left",
          sortable: false,
          value: "MARCA",
        },
        {
          text: "Producto",
          align: "left",
          sortable: false,
          value: "DESCRIPCION_PRODUCTO",
          width: 150,
        },
        {
          text: "Cliente",
          align: "left",
          sortable: false,
          value: "NOMBRE_CLIENTE",
          width: "250",
        },
        {
          text: "Tipo Venta",
          align: "center",
          sortable: false,
          value: "TIPO_DE_VENTA",
          divider: true,
        },
        {
          text: "Moneda",
          align: "center",
          sortable: false,
          value: "MONEDA",
        },
        {
          text: "Precio Venta",
          align: "right",
          sortable: false,
          value: "PRECIO_VENTA",
        },
        {
          text: "Costo",
          align: "right",
          sortable: false,
          value: "TOTAL_COSTO",
        },
        { text: "Total", align: "right", sortable: false, value: "TOTAL" },
        {
          text: "% Margen",
          align: "right",
          sortable: false,
          value: "PORCENTAJE_MARGEN",
        },
      ];
    },
    minHeight() {
      const height = this.$vuetify.breakpoint.mdAndUp ? "72vh" : "90vh";
      return `calc(${height} - ${this.$vuetify.application.top}px)`;
    },
    canExport() {
      return this.totalItems > 1500;
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
  watch: {
    page: {
      handler: _.debounce(function () {
        this.loadSalesHistory(() => {});
      }, 700),
      deep: true,
    },
    itemsPerPage: {
      handler: _.debounce(function () {
        this.loadSalesHistory(() => {});
      }, 700),
      deep: true,
    },
    filters: {
      handler: _.debounce(function (v) {
        this.loadSalesHistory(() => {});
      }, 700),
      deep: true,
    },
  },
  methods: {
    updateSearchPanel() {
      this.rightDrawer = !this.rightDrawer;
    },
    cancelSearch() {
      this.showSearchPanel = false;
    },
    cleanFilter() {
      this.$refs.filterForm.reset();
      this.itemsPerPage = 10;
      this.page = 1;
    },
    loadSalesHistory(cb) {
      const _this = this;
      let params = {
        product_name: _this.filters.product_name,
        customer_name: _this.filters.customer_name,
        years: _this.filters.years.join(","),
        months: _this.filters.months.join(","),
        agencies: _this.filters.agencies.join(","),
        brands: _this.filters.brands.join(","),
        type_of_sale: _this.filters.type_of_sale.join(","),
        seller: _this.filters.seller.join(","),
        isMXN: _this.filters.isMXN ? true : null,
        isUSD: _this.filters.isUSD ? true : null,
        page: _this.page,
        per_page: _this.itemsPerPage,
      };

      axios
        .get("/admin/marketing/sales_history", { params: params })
        .then(function (response) {
          let Data = response.data.data;
          _this.items = Data.data;
          _this.totalItems = Data.total;
          (cb || Function)();
        });
    },
    loadResources(cb) {
      const _this = this;
      axios
        .get("/admin/marketing/sales_history/resources")
        .then(function (response) {
          let Resources = response.data.data;
          _this.options.years = Resources.years;
          _this.options.months = Resources.months;
          _this.options.type_of_sale = Resources.type_of_sale;
          _this.options.agencies = Resources.agencies;
          _this.options.brands = Resources.brands;
          _this.options.seller = Resources.seller;
          (cb || Function)();
        });
    },
    excelGenerate() {
      const _this = this;
      if (_this.filters.years.length <= 0)
        return _this.$store.commit("showSnackbar", {
          message: "Agregar Filtro por Año",
          color: "warning",
          duration: 3000,
        });
      _this.$store.commit("showLoader");
      let params = {
        product_name: _this.filters.product_name,
        customer_name: _this.filters.customer_name,
        years: _this.filters.years.join(","),
        months: _this.filters.months.join(","),
        agencies: _this.filters.agencies.join(","),
        brands: _this.filters.brands.join(","),
        type_of_sale: _this.filters.type_of_sale.join(","),
        seller: _this.filters.seller.join(","),
        isMXN: _this.filters.isMXN ? true : null,
        isUSD: _this.filters.isUSD ? true : null,
        paginate: "no",
      };

      axios
        .get("/admin/marketing/export", {
          params: params,
          responseType: "blob",
        })
        .then((res) => {
          const url = window.URL.createObjectURL(new Blob([res.data]));
          const link = document.createElement("a");
          link.href = url;
          link.setAttribute("download", "Historial_Ventas.xlsx"); //or any other extension
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
        })
        .finally(function () {
          _this.$store.commit("hideLoader");
        });
    },
    getColor(value) {
      if (value < 20) return "red";
      else if (value < 30) return "orange";
      else return "green";
    },
  },
};
</script>
