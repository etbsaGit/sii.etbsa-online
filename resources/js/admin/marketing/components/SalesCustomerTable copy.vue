<template >
  <v-card>
    <v-expansion-panels>
      <v-expansion-panel>
        <v-expansion-panel-header>
          <span class="title">Filtro Avanzado</span>
        </v-expansion-panel-header>
        <v-expansion-panel-content>
          <v-row
            class="d-flex align-content-start flex-wrap text-uppercase"
            color="grey lighten-2"
            no-gutters
          >
            <v-col cols="12" md="6" class="pa-2 flex-grow-1 flex-shrink-0">
              <v-combobox
                v-model="filters.clave_cliente"
                label="CLIENTE"
                :items="options.Clientes"
                placeholder="Seleccionar Cliente"
                :filter="customFilterCliente"
                item-value="Clave_Cliente"
                item-text="Clave_Cliente"
                deletable-chips
                hide-details
                small-chips
                clearable
                multiple
                outlined
                dense
                chips
              >
                <template #item="{ item }">
                  <v-list-item-content>
                    <v-list-item-subtitle>
                      {{ item.Clave_Cliente }}
                    </v-list-item-subtitle>
                    <v-list-item-title> {{ item.Cliente }} </v-list-item-title>
                  </v-list-item-content>
                </template>
              </v-combobox>
            </v-col>
            <v-col cols="12" md="6" class="pa-2 flex-grow-1 flex-shrink-0">
              <v-combobox
                v-model="filters.clave_vendedor"
                label="VENDEDOR"
                :items="options.Vendedores"
                item-text="ClaveVendedor"
                item-value="ClaveVendedor"
                placeholder="Buscar Clave Vendedor, Nombre Vendedor"
                :filter="customFilterVendedor"
                clearable
                outlined
                hide-details
                dense
                multiple
                chips
                small-chips
                deletable-chips
              >
                <template #item="{ item }">
                  <v-list-item-content>
                    <v-list-item-subtitle>
                      {{ item.ClaveVendedor }}
                    </v-list-item-subtitle>
                    <v-list-item-title> {{ item.Nombre }} </v-list-item-title>
                  </v-list-item-content>
                </template>
              </v-combobox>
            </v-col>
            <v-col cols="12" md="3" class="pa-2 flex-grow-1 flex-shrink-0">
              <v-select
                v-model="filters.sucursales"
                label="SUCURSAL"
                :items="options.Sucursales"
                item-text="nombre"
                item-value="id"
                placeholder="Seleccionar Sucursal"
                clearable
                outlined
                hide-details
                dense
                multiple
                chips
                deletable-chips
              >
                <template v-slot:selection="{ item }">
                  <v-list-item-content>
                    <v-list-item-subtitle>
                      {{ item.linea }}
                    </v-list-item-subtitle>
                    <v-list-item-title> {{ item.nombre }} </v-list-item-title>
                  </v-list-item-content>
                </template>
                <template #item="{ item }">
                  <v-list-item-content>
                    <v-list-item-subtitle>
                      {{ item.linea }}
                    </v-list-item-subtitle>
                    <v-list-item-title> {{ item.nombre }} </v-list-item-title>
                  </v-list-item-content>
                </template>
              </v-select>
            </v-col>
            <v-col cols="12" md="3" class="pa-2 flex-grow-1 flex-shrink-0">
              <v-select
                v-model="filters.lineas"
                label="Linea"
                :items="options.Linea"
                item-text="linea"
                item-value="linea"
                placeholder="Seleccionar Linea"
                clearable
                outlined
                hide-details
                dense
                multiple
                chips
                deletable-chips
                small-chips
              >
              </v-select>
            </v-col>
            <v-col cols="12" md="2" class="pa-2 flex-grow-1 flex-shrink-0">
              <v-select
                v-model="filters.years"
                label="AÑO"
                :items="options.Años"
                item-text="year"
                item-value="year"
                placeholder="Seleccionar Año"
                clearable
                outlined
                hide-details
                dense
                multiple
                chips
                deletable-chips
                small-chips
              >
              </v-select>
            </v-col>
            <v-col cols="12" md="2" class="pa-2 flex-grow-1 flex-shrink-0">
              <v-select
                v-model="filters.currency"
                label="MONEDA"
                :items="options.Moneda"
                item-text="MONEDA"
                item-value="MONEDA"
                placeholder="Seleccionar Moneda"
                clearable
                outlined
                hide-details
                dense
                multiple
                chips
                deletable-chips
                small-chips
              >
              </v-select>
            </v-col>
            <v-col cols="12" md="2" class="pa-2 flex-grow-1 flex-shrink-0">
              <v-select
                v-model="filters.tipo_venta"
                label="TIPO DE VENTA"
                :items="options.TipoVenta"
                item-text="descripcion"
                item-value="clave"
                placeholder="Seleccionar Tipo de Venta"
                clearable
                outlined
                hide-details
                dense
                multiple
                chips
                deletable-chips
              >
                <template v-slot:selection="{ item }">
                  <v-list-item-content class="d-flex">
                    <v-list-item-subtitle>
                      {{ item.clave }}
                    </v-list-item-subtitle>
                    <v-list-item-title>
                      {{ item.descripcion }}
                    </v-list-item-title>
                  </v-list-item-content>
                </template>
                <template #item="{ item }">
                  <v-list-item-content>
                    <v-list-item-subtitle>
                      {{ item.clave }}
                    </v-list-item-subtitle>
                    <v-list-item-title>
                      {{ item.descripcion }}
                    </v-list-item-title>
                  </v-list-item-content>
                </template>
              </v-select>
            </v-col>
            <v-col cols="12" md="3" class="pa-2 flex-grow-1 flex-shrink-0">
              <v-combobox
                v-model="filters.municipio"
                label="Municipio"
                :items="options.Municipio"
                placeholder="Buscar Municipio"
                clearable
                outlined
                hide-details
                dense
                multiple
                chips
                deletable-chips
                small-chips
              >
              </v-combobox>
            </v-col>
            <v-col cols="12" md="3" class="pa-2 flex-grow-1 flex-shrink-0">
              <v-combobox
                v-model="filters.estado"
                label="Estado"
                :items="options.Estado"
                placeholder="Buscar Estado"
                clearable
                outlined
                hide-details
                dense
                multiple
                chips
                deletable-chips
                small-chips
              >
              </v-combobox>
            </v-col>
          </v-row>
        </v-expansion-panel-content>
      </v-expansion-panel>
    </v-expansion-panels>

    <v-data-table
      :headers="headers"
      :items="items"
      :options.sync="pagination"
      :server-items-length="totalItems"
      fixed-header
      caption
      dense
      class="elevation-3"
      calculate-widths
      :footer-props="{
        showCurrentPage: true,
        showFirstLastPage: true,
        firstIcon: 'mdi-arrow-collapse-left',
        lastIcon: 'mdi-arrow-collapse-right',
        prevIcon: 'mdi-minus',
        nextIcon: 'mdi-plus',
        itemsPerPageOptions: [5, 10, 15, 100, 200, 500, 2000, 5000],
      }"
    >
      <template #top>
        <v-toolbar flat>
          <v-toolbar-title> Facturacion Maquinaria</v-toolbar-title>
          <v-spacer />
          <!-- <v-text-field
            v-model="searchCustomer"
            label="Nombre Cliente"
            placeholder="Nombre, Compañia"
            persistent-placeholder
            outlined
            hide-details
            clearable
            dense
            class="mr-2"
          /> -->
          <v-text-field
            v-model="search"
            label="Buscar"
            placeholder="No Documento,Descripcion Producto, Serie"
            persistent-placeholder
            outlined
            hide-details
            clearable
            dense
          />
          <v-btn icon @click="cleanFilter">
            <v-icon color="red">mdi-filter-remove-outline</v-icon>
          </v-btn>
          <v-btn @click="searchBtn()" dark>
            Buscar
            <v-icon right color="blue">mdi-magnify</v-icon>
          </v-btn>
        </v-toolbar>
      </template>
      <template v-slot:[`item.Sucursal`]="{ item }">
        <div class="caption text--secondary">
          {{ item.SucursalLinea }}
        </div>
        <div class="text-uppercase font-weight-bold">{{ item.Sucursal }}</div>
      </template>
      <template v-slot:[`item.Cliente`]="{ item }">
        <div class="caption text--secondary">
          Clave: {{ item.Clave_Cliente }}
        </div>
        <div class="text-uppercase font-weight-bold">{{ item.Cliente }}</div>
      </template>
      <template v-slot:[`item.Estado`]="{ item, value }">
        <div class="text-uppercase font-weight-bold">{{ value }}</div>
        <div class="caption text--secondary">
          {{ item.Municipio }}
        </div>
      </template>
      <template v-slot:[`item.Vendedor`]="{ item }">
        <div class="caption text--secondary">
          Clave: {{ item.VendedorClave }}
        </div>
        <div class="text-uppercase font-weight-bold">{{ item.Vendedor }}</div>
      </template>
      <template v-slot:[`item.Producto`]="{ item }">
        <div class="caption text--secondary">
          {{ item.TipoVenta }}
        </div>
        <div class="text-uppercase font-weight-bold">{{ item.Producto }}</div>
      </template>

      <template v-slot:[`item.NIP`]="{ value }">
        <div style="max-width: 200px">
          <v-chip label outlined color="black" class="font-weight-bold" dark>
            {{ value }}
          </v-chip>
        </div>
      </template>
      <template v-slot:[`item.year`]="{ value }">
        <v-chip label outlined color="blue" class="font-weight-bold" dark>
          {{ value }}
        </v-chip>
      </template>
      <template v-slot:[`item.TOTAL`]="{ value }">
        <v-chip outlined label color="primary" class="font-weight-bold" dark>
          {{ value | currency }} MX
          <!-- {{ item.currency }} -->
        </v-chip>
      </template>
      <template #footer>
        <v-toolbar flat>
          <v-toolbar-title class="text-left">
            Ultima Carga: {{ lastUpdated }}
          </v-toolbar-title>
          <v-spacer />
          <v-toolbar-title class="text-right">
            Total (MXN):{{ sumTotalVentasAg | currency }}
            <!-- <v-divider inset/>
            Total (MXN):{{ sumTotalVentasAgMX | currency }} -->
          </v-toolbar-title>
        </v-toolbar>
      </template>
    </v-data-table>
  </v-card>
</template>
<script>
export default {
  name: "SalesCustomerHistory",

  mounted() {
    const _this = this;
    _this.$store.commit("setBreadcrumbs", [
      { label: "Historial Ventas Clientes", name: "" },
    ]);
    _this.getOptions();
  },
  data() {
    return {
      headers: [
        {
          text: "No. Documento",
          align: "start",
          filterable: false,
          value: "NO_DOCUMENTO",
          sortable: false,
        },
        {
          text: "Sucursal",
          align: "start",
          filterable: false,
          value: "Sucursal",
          sortable: false,
        },
        { text: "Cliente", value: "Cliente", sortable: false },
        { text: "Estado / Municipio", value: "Estado", sortable: false },
        { text: "Vendedor", value: "Vendedor", sortable: false },
        { text: "Producto / Tipo Venta", value: "Producto", sortable: false },
        { text: "Serie Producto", value: "NIP", width: 100, sortable: false },
        { text: "Año Factura", value: "year", sortable: false },
        { text: "Importe", align: "end", value: "TOTAL", sortable: false },
      ],
      search: "",
      searchCustomer: "",
      items: [],
      sumTotalVentasAg: 0,
      sumTotalVentasAgMX: 0,
      lastUpdated: "",
      totalItems: 0,
      pagination: {
        itemsPerPage: 10,
        page: 1,
      },
      filters: {
        clave_cliente: [],
        clave_vendedor: [],
        sucursales: [],
        lineas: [],
        years: [],
        currency: [],
        tipo_venta: [],
        municipio: [],
        estado: [],
      },
      options: {},
    };
  },
  computed: {
    FilterClienteReduce() {
      return this.filters.clave_cliente.reduce((result, item) => {
        typeof item === "object" && item.hasOwnProperty("Clave_Cliente")
          ? result.push(item.Clave_Cliente)
          : result.push(item);
        return result;
      }, []);
    },
    FilterVendedorReduce() {
      return this.filters.clave_vendedor.reduce((result, item) => {
        typeof item === "object" && item.hasOwnProperty("ClaveVendedor")
          ? result.push(item.ClaveVendedor)
          : result.push(item);
        return result;
      }, []);
    },
  },
  watch: {
    pagination: {
      handler: _.debounce(function (newV, oldV) {
        if (newV !== oldV) {
          this.getData(() => {});
        }
      }, 999),
      deep: true,
      immediate: false,
    },
    search: {
      handler: _.debounce(function (v) {
        this.getData({ page: 1, per_page: 10 });
      }, 1200),
    },
    // searchCustomer: {
    //   handler: _.debounce(function (v) {
    //     this.getData({ page: 1, per_page: 5 });
    //   }, 1200),
    // },
  },
  methods: {
    cleanFilter() {
      const _this = this;

      this.$nextTick(() => {
        _this.filters.clave_cliente = [];
        _this.filters.clave_vendedor = [];
        _this.filters.sucursales = [];
        _this.filters.lineas = [];
        _this.filters.years = [];
        _this.filters.currency = [];
        _this.filters.tipo_venta = [];
        _this.filters.municipio = [];
        _this.filters.estado = [];
        _this.pagination.page = 1;
        _this.pagination = Object.assign(
          {},
          {
            page: 1,
            itemsPerPage: 10,
          }
        );
      });
    },
    searchBtn() {
      const _this = this;
      this.$nextTick(() => {
        _this.pagination = Object.assign(
          {},
          {
            page: 1,
            itemsPerPage: 10,
          }
        );
      });
    },
    async getData({ page, per_page }) {
      const _this = this;
      let params = {
        ..._this.filters,
        clave_cliente: _this.FilterClienteReduce,
        clave_vendedor: _this.FilterVendedorReduce,
        search: _this.search,
        // searchCustomer: _this.searchCustomer,
        // order_by: _this.pagination.sortBy[0] || "id",
        // order_sort: _this.pagination.sortDesc[0] ? "asc" : "desc",
        // order_by: _this.pagination.sortBy[0] || "id",
        page: page ?? this.pagination.page,
        per_page: per_page ?? this.pagination.itemsPerPage,
      };
      const {
        data: {
          data: { items, sumatoriaTotal, lastUpdated },
          message,
        },
      } = await axios.get("/admin/marketing/sales-customer", { params });
      this.$nextTick(() => {
        _this.items = items.data;
        _this.totalItems = items.total;
      });

      this.sumTotalVentasAg = sumatoriaTotal;
      this.lastUpdated = lastUpdated;

      this.$store.commit("showSnackbar", {
        message: message,
        color: "success",
        duration: 3000,
      });
    },
    async getOptions() {
      const {
        data: {
          data: { options },
        },
      } = await axios.get("/admin/marketing/sales-customer/filters");

      this.options = options;
    },
    customFilterCliente(item, queryText, itemText) {
      const words = queryText.toLowerCase().split(" ");

      return words.every((word) => {
        // console.log(item.Cliente.toLowerCase());
        const nameMatch = item.Cliente.toLowerCase().includes(word);
        const claveMatch = item.Clave_Cliente.toLowerCase().includes(word);

        return nameMatch || claveMatch;
      });
    },
    customFilterVendedor(item, queryText, itemText) {
      const words = queryText.toLowerCase().split(" ");

      return words.every((word) => {
        const nameMatch = item.Nombre.toLowerCase().includes(word);
        const claveMatch = item.ClaveVendedor.toLowerCase().includes(word);

        return nameMatch || claveMatch;
      });
    },
    toggle() {
      this.$nextTick(() => {
        if (this.likesAllFruit) {
          this.selectedFruits = [];
        } else {
          this.selectedFruits = this.fruits.slice();
        }
      });
    },
  },
};
</script>
<style >
</style>