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

            <v-col cols="12" md="3" class="pa-2 flex-grow-1 flex-shrink-0">
              <v-select
                v-model="filters.sucursales"
                label="SUCURSAL"
                :items="options.Sucursales"
                placeholder="Seleccionar Sucursal"
                clearable
                outlined
                hide-details
                dense
                multiple
                chips
                small-chips
                deletable-chips
              >
              </v-select>
            </v-col>
            <v-col cols="12" md="3" class="pa-2 flex-grow-1 flex-shrink-0">
              <v-select
                v-model="filters.lineas"
                label="Linea"
                :items="options.Linea"
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
            <v-col cols="12" md="3" class="pa-2 flex-grow-1 flex-shrink-0">
              <v-select
                v-model="filters.modulos"
                label="Modulo"
                :items="options.Modulo"
                placeholder="Seleccionar Modulo"
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
          </v-row>
        </v-expansion-panel-content>
      </v-expansion-panel>
    </v-expansion-panels>

    <v-card-text>
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
            <v-toolbar-title> Cartera Clientes </v-toolbar-title>
            <v-spacer />
            <v-text-field
              v-model="search"
              label="Buscar"
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
        <template v-slot:[`item.sucursal`]="{ item }">
          <div class="caption text--secondary">
            {{ item.Linea }}
          </div>
          <div class="text-uppercase font-weight-bold">{{ item.Sucursal }}</div>
        </template>
        <template v-slot:[`item.cliente`]="{ item }">
          <div class="caption text--secondary">
            Clave: {{ item.Clave_Cliente }}
          </div>
          <div class="text-uppercase font-weight-bold">
            {{ item.Cliente }}
          </div>
        </template>

        <template v-slot:[`item.v_total`]="{ value }">
          <v-chip outlined label color="grey" class="font-weight-bold" dark>
            {{ value | currency }}
          </v-chip>
        </template>
        <template v-slot:[`item.v_total_vencido`]="{ value }">
          <v-chip outlined label color="red" class="font-weight-bold" dark>
            {{ value | currency }}
          </v-chip>
        </template>
        <template v-slot:[`item.v_total_neto`]="{ value }">
          <v-chip outlined label color="primary" class="font-weight-bold" dark>
            {{ value | currency }}
          </v-chip>
        </template>
        <template #footer>
          <v-toolbar flat>
            <v-toolbar-title class="text-left">
              Ultima Carga: {{ lastUpdated }}
            </v-toolbar-title>
            <v-spacer />
            <v-toolbar-title class="text-right">
              Total (MXN):{{ sumatoriaTotal | currency }}
            </v-toolbar-title>
          </v-toolbar>
        </template>
      </v-data-table>
    </v-card-text>
    <v-card-text>
      <v-card-title>Total Cartera por Modulo </v-card-title>
      <v-data-table
        :headers="headersPorModulo"
        :items="carteraPorModulo"
        disable-pagination
        hide-default-footer
        dense
        dark
      >
        <template v-slot:[`item.total_v_mas90`]="{ value }">
          {{ value | currency }}
        </template>
        <template v-slot:[`item.total_v_mas60`]="{ value }">
          {{ value | currency }}
        </template>
        <template v-slot:[`item.total_v_mas30`]="{ value }">
          {{ value | currency }}
        </template>
        <template v-slot:[`item.total_v_mas15`]="{ value }">
          {{ value | currency }}
        </template>
        <template v-slot:[`item.total_v_mas1`]="{ value }">
          {{ value | currency }}
        </template>
        <template v-slot:[`item.total_v_por_vencer`]="{ value }">
          {{ value | currency }}
        </template>
        <template v-slot:[`item.total_v_total`]="{ value }">
          {{ value | currency }}
        </template>
        <template v-slot:[`item.total_v_total_vencido`]="{ value }">
          {{ value | currency }}
        </template>
        <template v-slot:[`item.total_v_total_neto`]="{ value }">
          {{ value | currency }}
        </template>
      </v-data-table>
    </v-card-text>
    <v-card-text>
      <v-card-title>Total Cartera por Sucursal </v-card-title>
      <v-data-table
        :headers="headersPorScursal"
        :items="carteraPorSucursal"
        disable-pagination
        hide-default-footer
        dense
        dark
      >
        <template v-slot:[`item.total_v_mas90`]="{ value }">
          {{ value | currency }}
        </template>
        <template v-slot:[`item.total_v_mas60`]="{ value }">
          {{ value | currency }}
        </template>
        <template v-slot:[`item.total_v_mas30`]="{ value }">
          {{ value | currency }}
        </template>
        <template v-slot:[`item.total_v_mas15`]="{ value }">
          {{ value | currency }}
        </template>
        <template v-slot:[`item.total_v_mas1`]="{ value }">
          {{ value | currency }}
        </template>
        <template v-slot:[`item.total_v_por_vencer`]="{ value }">
          {{ value | currency }}
        </template>
        <template v-slot:[`item.total_v_total`]="{ value }">
          {{ value | currency }}
        </template>
        <template v-slot:[`item.total_v_total_vencido`]="{ value }">
          {{ value | currency }}
        </template>
        <template v-slot:[`item.total_v_total_neto`]="{ value }">
          {{ value | currency }}
        </template>
      </v-data-table>
    </v-card-text>
  </v-card>
</template>
  <script>
import PieChart from "@admin/marketing/components/PieChart.vue";
import BarChart from "@admin/marketing/components/BarChart.vue";

export default {
  name: "CustomerPortfolio",
  components: {
    // SalesCustomerPie,
    // SalesCustomerBar,
    PieChart,
    BarChart,
  },
  mounted() {
    const _this = this;
    _this.$store.commit("setBreadcrumbs", [
      { label: "Historial Cartera de Clientes", name: "" },
    ]);
    _this.getOptions();
  },
  data() {
    return {
      headers: [
        {
          text: "Cliente",
          align: "start",
          filterable: false,
          value: "cliente",
          sortable: false,
        },
        {
          text: "Sucursal",
          align: "start",
          filterable: false,
          value: "sucursal",
          sortable: false,
        },
        {
          text: "Modulo",
          align: "start",
          filterable: false,
          value: "v_nombre_modulo2",
          sortable: false,
        },
        {
          text: "Total",
          align: "end",
          value: "v_total",
          sortable: false,
        },
        {
          text: "Total Vencido",
          align: "end",
          value: "v_total_vencido",
          sortable: false,
        },
        {
          text: "Total Neto",
          align: "end",
          value: "v_total_neto",
          sortable: false,
        },
      ],
      headersPorModulo: [
        {
          text: "Modulo",
          align: "start",
          value: "modulo",
          sortable: true,
        },
        {
          text: "Mas 90 Dias",
          align: "start",
          value: "total_v_mas90",
          sortable: true,
        },
        {
          text: "Mas 60 Dias",
          align: "start",
          value: "total_v_mas60",
          sortable: true,
        },
        {
          text: "Mas 30 Dias",
          align: "start",
          value: "total_v_mas30",
          sortable: true,
        },
        {
          text: "Mas 15 Dias",
          align: "start",
          value: "total_v_mas15",
          sortable: true,
        },
        {
          text: "Mas 1 Dias",
          align: "start",
          value: "total_v_mas1",
          sortable: true,
        },
        {
          text: "Por Vencer",
          align: "start",
          value: "total_v_por_vencer",
          sortable: true,
        },
        {
          text: "Total",
          align: "start",
          value: "total_v_total",
          sortable: true,
        },
        {
          text: "Total Vencido",
          align: "start",
          value: "total_v_total_vencido",
          sortable: true,
        },
        {
          text: "Total Neto",
          align: "start",
          value: "total_v_total_neto",
          sortable: true,
        },
      ],
      headersPorScursal: [
        {
          text: "Sucursal",
          align: "start",
          value: "sucursal",
          sortable: true,
        },
        {
          text: "Mas 90 Dias",
          align: "start",
          value: "total_v_mas90",
          sortable: true,
        },
        {
          text: "Mas 60 Dias",
          align: "start",
          value: "total_v_mas60",
          sortable: true,
        },
        {
          text: "Mas 30 Dias",
          align: "start",
          value: "total_v_mas30",
          sortable: true,
        },
        {
          text: "Mas 15 Dias",
          align: "start",
          value: "total_v_mas15",
          sortable: true,
        },
        {
          text: "Mas 1 Dias",
          align: "start",
          value: "total_v_mas1",
          sortable: true,
        },
        {
          text: "Por Vencer",
          align: "start",
          value: "total_v_por_vencer",
          sortable: true,
        },
        {
          text: "Total",
          align: "start",
          value: "total_v_total",
          sortable: true,
        },
        {
          text: "Total Vencido",
          align: "start",
          value: "total_v_total_vencido",
          sortable: true,
        },
        {
          text: "Total Neto",
          align: "start",
          value: "total_v_total_neto",
          sortable: true,
        },
      ],
      search: "",
      searchCustomer: "",
      items: [],
      carteraPorModulo: [],
      carteraPorSucursal: [],
      sumatoriaTotal: 0,

      lastUpdated: "",
      totalItems: 0,
      pagination: {
        itemsPerPage: 10,
        page: 1,
      },
      filters: {
        clave_cliente: [],
        sucursales: [],
        lineas: [],
        modulos: [],
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
  },
  methods: {
    getRandomColor(index) {
      // Función para generar colores aleatorios
      const colors = [
        "#41B883",
        "#E46651",
        "#00D8FF",
        "#DD1B16",
        "#F4FF81",
        "#FF9800",
        "#90A4AE",
        "#D84315",
        "#B9F6CA",
        "#01579B",
        // Puedes agregar más colores aquí para más clientes
      ];
      return colors[index % colors.length];
    },
    cleanFilter() {
      const _this = this;

      this.$nextTick(() => {
        _this.filters.clave_cliente = [];
        _this.filters.sucursales = [];
        _this.filters.lineas = [];
        _this.filters.modulos = [];
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
        page: page ?? this.pagination.page,
        per_page: per_page ?? this.pagination.itemsPerPage,
      };
      const {
        data: {
          data: {
            items,
            sumatoriaTotal,
            lastUpdated,
            carteraPorModulo,
            carteraPorSucursal,
          },
          message,
        },
      } = await axios.get("/admin/customers/reports/portfolio", { params });

      this.$nextTick(() => {
        _this.items = items.data;
        _this.totalItems = items.total;
      });

      this.sumatoriaTotal = sumatoriaTotal;
      this.lastUpdated = lastUpdated;
      this.carteraPorModulo = carteraPorModulo;
      this.carteraPorSucursal = carteraPorSucursal;

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
      } = await axios.get("/admin/customers/reports/resources");

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
  },
};
</script>
  <style >
</style>