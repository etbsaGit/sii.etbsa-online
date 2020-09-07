<template>
  <v-card elevation="0">
    <v-container>
      <v-form ref="filterForm">
        <v-row>
          <v-col cols="12" xs="12" md="6" class="py-0">
            <v-text-field
              v-model="filters.product_name"
              prepend-icon="mdi-magnify"
              type="text"
              label="Descripcion Producto"
              clearable
            ></v-text-field>
          </v-col>
          <v-col cols="12" xs="12" md="6" class="py-0">
            <v-text-field
              v-model="filters.customer_name"
              prepend-icon="mdi-magnify"
              type="text"
              label="Nombre Cliente"
              clearable
            ></v-text-field>
          </v-col>
          <v-col cols="12" xs="12" md="3" class="py-0">
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
          <v-col cols="12" xs="12" md="3" class="py-0">
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
          <v-col cols="12" xs="12" md="3" class="py-0">
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
          <v-col cols="12" xs="12" md="3" class="py-0">
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
          <v-col cols="12" xs="12" md="3" class="py-0">
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
          <v-col cols="12" xs="12" md="3" class="py-0">
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
    </v-container>
    <v-data-table
      :headers="headers"
      :items="items"
      :page.sync="page"
      :items-per-page="itemsPerPage"
      :server-items-length="totalItems"
      @page-count="pageCount = $event"
      class="elevation-2 mx-2 text-uppercase"
      hide-default-footer
      :height="minHeight"
      fixed-header
      dense
    >
      <template v-slot:top>
        <v-container fluid py-0>
          <v-row>
            <span class="d-flex overline align-center pl-4">
              historico de ventas
            </span>
            <div
              class="cols-4 d-flex flex-grow-1 justify-end align-center mx-2"
            >
              <span class="overline pr-2">
                total Registros: {{ totalItems }}
              </span>
              <div style="max-width:90px;">
                <v-select
                  v-model="itemsPerPage"
                  :items="[5, 10, 25, 50, 100]"
                  label="Items Pagina"
                  class="my-2"
                  reverse
                  filled
                  solo
                  hide-details
                  dense
                ></v-select>
              </div>
              <v-btn icon color="error" @click="cleanFilter()">
                <v-icon>mdi-filter-remove-outline</v-icon>
              </v-btn>
              <v-tooltip top>
                <template v-slot:activator="{ on, attrs }">
                  <v-btn
                    icon
                    color="green"
                    v-bind="attrs"
                    v-on="on"
                    @click="excelGenerate()"
                  >
                    <v-icon>mdi-file-excel</v-icon>
                  </v-btn>
                </template>
                <span>Exportar</span>
              </v-tooltip>
              <v-btn
                icon
                @click="loadResources(), (itemsPerPage = 10), (page = 1)"
              >
                <v-icon>mdi-refresh</v-icon>
              </v-btn>
            </div>
          </v-row>
        </v-container>
      </template>

      <template v-slot:[`item.MES`]="{ item }">
        <span class="caption text-truncate"
          >{{ item.MES }} {{ item.ANIO }}</span
        >
      </template>
      <template v-slot:[`item.MARCA`]="{ item }">
        <span class="caption text-truncate">{{ item.MARCA }}</span>
      </template>
      <template v-slot:[`item.DESCRIPCION_PRODUCTO`]="{ item }">
        <span class="caption text-truncate">{{
          item.DESCRIPCION_PRODUCTO
        }}</span>
      </template>
      <template v-slot:[`item.NOMBRE_CLIENTE`]="{ item }">
        <span class="caption text-truncate">{{ item.NOMBRE_CLIENTE }}</span>
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
              {{ item.TOTAL | money(item.MONEDA || "MXN") }}
            </span>
          </template>
          <span>Folio Fiscal: {{ item.FOLIO_FISCAL }}</span>
        </v-tooltip>
      </template>
      <template v-slot:[`item.PORCENTAJE_MARGEN`]="{ item }">
        <v-chip :color="getColor(item.PORCENTAJE_MARGEN * 100)" dark small>
          {{ item.PORCENTAJE_MARGEN | percent() }}
        </v-chip>
      </template>
    </v-data-table>
    <div class="text-center pt-2">
      <v-pagination
        dark
        v-model="page"
        :page="page"
        :length="pageCount"
        :total-visible="16"
        prev-icon="mdi-arrow-left"
        next-icon="mdi-arrow-right"
      ></v-pagination>
    </div>
  </v-card>
</template>

<script>
export default {
  mounted() {
    const self = this;
    self.loadSalesHistory(() => {});
    self.loadResources(() => {});
  },
  data() {
    return {
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
          width: "100",
        },
        {
          text: "Producto",
          align: "left",
          sortable: false,
          value: "DESCRIPCION_PRODUCTO",
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
      const height = this.$vuetify.breakpoint.mdAndUp ? "70vh" : "60vh";
      return `calc(${height} - ${this.$vuetify.application.top}px)`;
    },
    canExport() {
      return this.totalItems > 1500;
    },
  },
  watch: {
    page: {
      handler: _.debounce(function() {
        this.loadSalesHistory(() => {});
      }, 700),
      deep: true,
    },
    itemsPerPage: {
      handler: _.debounce(function() {
        this.loadSalesHistory(() => {});
      }, 700),
      deep: true,
    },
    filters: {
      handler: _.debounce(function(v) {
        this.loadSalesHistory(() => {});
      }, 700),
      deep: true,
    },
  },
  methods: {
    cleanFilter() {
      this.$refs.filterForm.reset();
      this.itemsPerPage = 10;
      this.page = 1;
    },
    loadSalesHistory(cb) {
      const self = this;
      let params = {
        product_name: self.filters.product_name,
        customer_name: self.filters.customer_name,
        years: self.filters.years.join(","),
        months: self.filters.months.join(","),
        agencies: self.filters.agencies.join(","),
        brands: self.filters.brands.join(","),
        type_of_sale: self.filters.type_of_sale.join(","),
        seller: self.filters.seller.join(","),
        isMXN: self.filters.isMXN ? true : null,
        isUSD: self.filters.isUSD ? true : null,
        page: self.page,
        per_page: self.itemsPerPage,
      };

      axios
        .get("/admin/marketing/sales_history", { params: params })
        .then(function(response) {
          let Data = response.data.data;
          self.items = Data.data;
          self.totalItems = Data.total;
          (cb || Function)();
        });
    },
    loadResources(cb) {
      const self = this;
      axios
        .get("/admin/marketing/sales_history/resources")
        .then(function(response) {
          let Resources = response.data.data;
          self.options.years = Resources.years;
          self.options.months = Resources.months;
          self.options.type_of_sale = Resources.type_of_sale;
          self.options.agencies = Resources.agencies;
          self.options.brands = Resources.brands;
          self.options.seller = Resources.seller;
          (cb || Function)();
        });
    },
    excelGenerate() {
      const self = this;
      if (self.filters.years.length <= 0)
        return self.$store.commit("showSnackbar", {
          message: "Agregar Filtro por Año",
          color: "warning",
          duration: 3000,
        });
      self.$store.commit("showLoader");
      let params = {
        product_name: self.filters.product_name,
        customer_name: self.filters.customer_name,
        years: self.filters.years.join(","),
        months: self.filters.months.join(","),
        agencies: self.filters.agencies.join(","),
        brands: self.filters.brands.join(","),
        type_of_sale: self.filters.type_of_sale.join(","),
        seller: self.filters.seller.join(","),
        isMXN: self.filters.isMXN ? true : null,
        isUSD: self.filters.isUSD ? true : null,
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
        .catch(function(error) {
          if (error.response) {
            self.$store.commit("showSnackbar", {
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
        .finally(function() {
          self.$store.commit("hideLoader");
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
