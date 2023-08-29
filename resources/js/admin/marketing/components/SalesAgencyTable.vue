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
            <!-- <v-col cols="12" md="6" class="pa-2 flex-grow-1 flex-shrink-0">
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
            </v-col> -->
            <!-- <v-col cols="12" md="6" class="pa-2 flex-grow-1 flex-shrink-0">
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
            </v-col> -->
            <v-col cols="12" md="3" class="pa-2 flex-grow-1 flex-shrink-0">
              <!-- item-text="nombre"
              item-value="id" -->
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
                <!-- <template v-slot:selection="{ item }">
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
                </template> -->
              </v-select>
            </v-col>
            <!-- <v-col cols="12" md="3" class="pa-2 flex-grow-1 flex-shrink-0">
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
            </v-col> -->
          </v-row>
        </v-expansion-panel-content>
      </v-expansion-panel>
      <v-expansion-panels>
        <v-expansion-panel>
          <v-expansion-panel-header>
            <span class="title">
              Tabla Ventas Sucursal por Departamento y Concepto
            </span>
          </v-expansion-panel-header>
          <v-expansion-panel-content>
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
              show-group-by
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
                  <v-text-field
                    v-model="search"
                    label="Buscar"
                    placeholder="Nombre Producto, Serie Producto, MODELO"
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
                  {{ item.linea_sucursal }}
                </div>
                <div class="text-uppercase font-weight-bold">
                  {{ item.nombre_sucursal }}
                </div>
              </template>

              <template v-slot:[`item.Departamento`]="{ value }">
                <div class="text-uppercase font-weight-bold">
                  {{ value }}
                </div>
              </template>

              <template v-slot:[`item.CONCEPTO`]="{ value }">
                <div style="max-width: 200px">
                  <v-chip
                    label
                    outlined
                    color="black"
                    class="font-weight-bold"
                    dark
                  >
                    {{ value }}
                  </v-chip>
                </div>
              </template>

              <template v-slot:[`item.ACUMULADO_VENTA_MES`]="{ value }">
                <v-chip
                  outlined
                  label
                  color="primary"
                  class="font-weight-bold"
                  dark
                >
                  {{ value | currency }} MX
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
                  </v-toolbar-title>
                </v-toolbar>
              </template>
            </v-data-table>
          </v-expansion-panel-content>
        </v-expansion-panel>
      </v-expansion-panels>
      <v-expansion-panels>
        <v-expansion-panel>
          <v-expansion-panel-header>
            <span class="title"> Resumen por Linea Concepto Departamento </span>
          </v-expansion-panel-header>
          <v-expansion-panel-content>
            <v-data-iterator
              :items="queryGroup"
              :search="searchIterator"
              hide-default-footer
            >
              <!-- <template v-slot:header>
                <v-toolbar dark color="blue darken-3" class="mb-1">
                  <v-text-field
                    v-model="searchIterator"
                    clearable
                    flat
                    solo-inverted
                    hide-details
                    prepend-inner-icon="mdi-magnify"
                    label="Search"
                  ></v-text-field>
                </v-toolbar>
              </template> -->
              <template #default="{ items }">
                <v-row dense>
                  <v-col
                    v-for="item in items"
                    :key="`Linea_${item.LineaDeSucursal}`"
                    cols="12"
                    md="4"
                  >
                    <h3 class="text-uppercase text-center">
                      {{ item.LineaDeSucursal }}
                    </h3>
                    <v-card
                      v-for="concepto in item.CONCEPTOS"
                      :key="`${item.LineaDeSucursal}_${concepto.CONCEPTO}`"
                    >
                      <v-card-title> {{ concepto.CONCEPTO }} </v-card-title>

                      <v-data-table
                        :headers="departmentHeaders"
                        :items="concepto.Departamentos"
                        item-key="DescripcionDepartamento"
                        hide-default-footer
                        dense
                      >
                        <template v-slot:[`item.AcumuladoVenta`]="{ value }">
                          {{ value | currency }}
                        </template>
                        <template v-slot:[`item.AcumuladoActual`]="{ value }">
                          {{ value | currency }}
                        </template>
                        <template #footer>
                          <v-simple-table dense class="font-weight-medium">
                            <template #default>
                              <tbody>
                                <tr>
                                  <td class="green--text">TOTALES:</td>
                                  <td class="text-end blue--text">
                                    {{
                                      concepto.Departamentos.reduce(
                                        (acc, item) =>
                                          acc + Number(item.AcumuladoVenta),
                                        0
                                      ) | currency
                                    }}
                                  </td>
                                  <td class="text-end blue--text">
                                    {{
                                      concepto.Departamentos.reduce(
                                        (acc, item) =>
                                          acc + Number(item.AcumuladoActual),
                                        0
                                      ) | currency
                                    }}
                                  </td>
                                </tr>
                              </tbody>
                            </template>
                          </v-simple-table>
                        </template>
                      </v-data-table>
                    </v-card>
                  </v-col>
                </v-row>
              </template>
            </v-data-iterator>
          </v-expansion-panel-content>
        </v-expansion-panel>
      </v-expansion-panels>
      <v-expansion-panels>
        <v-expansion-panel>
          <v-expansion-panel-header>
            <span class="title">
              Graficas Sucursal-Departamentos SUMA de Venta Mes Actual
            </span>
          </v-expansion-panel-header>
          <v-expansion-panel-content>
            <v-card-text v-if="chartData.length > 0">
              <v-row dense>
                <v-col
                  cols="12"
                  md="4"
                  v-for="{
                    id_sucursal,
                    nombre_sucursal,
                    linea_sucursal,
                    dataLabels,
                    datasets,
                  } in dataRadarCharts"
                  :key="`RadialCol-${id_sucursal}_${nombre_sucursal}`"
                >
                  <!-- <v-subheader>
                    {{ `${nombre_sucursal} ${linea_sucursal}` }}
                  </v-subheader> -->
                  <h3 class="text-uppercase text-center">
                    {{ `${nombre_sucursal} ${linea_sucursal}` }}
                  </h3>

                  <radar-chart
                    :data-labels="dataLabels"
                    :datasets="datasets"
                    :chartId="`${id_sucursal}_${nombre_sucursal}`"
                    :key="`RadialChart-${id_sucursal}_${nombre_sucursal}`"
                    :height="300"
                  />
                </v-col>
              </v-row>
            </v-card-text>
          </v-expansion-panel-content>
        </v-expansion-panel>
      </v-expansion-panels>
    </v-expansion-panels>
  </v-card>
</template>
<script>
import PieChart from "./PieChart.vue";
import BarChart from "./BarChart.vue";
import RadarChart from "./RadarChart.vue";

export default {
  name: "SalesAgencyHistory",
  components: {
    PieChart,
    BarChart,
    RadarChart,
  },
  mounted() {
    const _this = this;
    _this.$store.commit("setBreadcrumbs", [
      { label: "Historial Ventas Sucursal", name: "" },
    ]);
    _this.getOptions();
    // _this.getData({ page: 1, per_page: 10 });
  },
  data() {
    return {
      headers: [
        {
          text: "Sucursal",
          align: "start",
          filterable: false,
          value: "sucursal",
          sortable: false,
        },
        // {
        //   text: "Linea",
        //   align: "start",
        //   filterable: false,
        //   value: "linea_sucursal",
        //   sortable: false,
        // },
        {
          text: "Departamento",
          align: "start",
          filterable: false,
          value: "Departamento",
          sortable: false,
        },
        { text: "Concepto", value: "CONCEPTO", sortable: false },
        {
          text: "Acumulado Venta Mes",
          value: "ACUMULADO_VENTA_MES",
          align: "end",
          sortable: false,
        },
      ],
      search: "",
      searchIterator: "",
      searchCustomer: "",
      items: [],
      groupedData: [],
      departmentHeaders: [
        {
          text: "Departamento",
          value: "DescripcionDepartamento",
          align: "start",
        },
        { text: "Acumulado Venta", value: "AcumuladoVenta", align: "end" },
        { text: "Acumulado Actual", value: "AcumuladoActual", align: "end" },
      ],
      sumTotalVentasAg: 0,
      chartData: 0,
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
    UniqueSucursales() {
      const uniqueSucursales = new Set();

      // Creamos un nuevo array con los objetos únicos de sucursal
      if (this.chartData.length > 0)
        return this.chartData
          .filter((item) => {
            const sucursalKey = `${item.id_sucursal}-${item.nombre_sucursal}-${item.linea_sucursal}`;
            if (!uniqueSucursales.has(sucursalKey)) {
              uniqueSucursales.add(sucursalKey);
              return true;
            }
            return false;
          })
          .map((item) => {
            return {
              id_sucursal: item.id_sucursal,
              nombre_sucursal: item.nombre_sucursal,
              linea_sucursal: item.linea_sucursal,
            };
          });
      else return [];
    },
    dataRadarCharts() {
      const _this = this;
      let charts = [];
      _this.UniqueSucursales.forEach((item) => {
        let { dataLabels, datasets } = _this.getDataRadarChart(
          item.id_sucursal
        );
        charts.push({ dataLabels, datasets, ...item });
      });

      return charts;
    },

    queryGroup() {
      return this.groupedData.reduce((acc, item) => {
        const existingLinea = acc.find(
          (entry) => entry.LineaDeSucursal === item.LineaDeSucursal
        );

        if (existingLinea) {
          const existingConcepto = existingLinea.CONCEPTOS.find(
            (entry) => entry.CONCEPTO === item.CONCEPTO
          );

          if (existingConcepto) {
            existingConcepto.Departamentos.push(item);
          } else {
            existingLinea.CONCEPTOS.push({
              CONCEPTO: item.CONCEPTO,
              Departamentos: [item],
              TotalAcumuladoVenta: [item].reduce(
                (accumulator, depto) =>
                  accumulator + Number(depto.AcumuladoVenta),
                0
              ),
            });
          }
        } else {
          acc.push({
            LineaDeSucursal: item.LineaDeSucursal,
            CONCEPTOS: [
              {
                CONCEPTO: item.CONCEPTO,
                Departamentos: [item],
                TotalAcumuladoVenta: [item].reduce(
                  (accumulator, depto) =>
                    accumulator + Number(depto.AcumuladoVenta),
                  0
                ),
              },
            ],
          });
        }

        return acc;
      }, []);
    },

    // dataLabels() {
    //   return this.chartData.map(
    //     (item) => `${item.Sucursal} ${item.Departamento} ${item.CONCEPTO}`
    //   );
    // },
    // dataValues() {
    //   return this.chartData.map((item) => item.ACUMULADO_VENTA_MES);
    // },

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
    getDataChartPie(sucursal) {
      let sucursalData = this.chartData.filter(
        (item) => item.Sucursal === sucursal
      );

      let labels = sucursalData.map(
        (item) => `${item.Departamento} ${item.CONCEPTO}`
      );
      let dataValues = sucursalData.map((item) => item.ACUMULADO_VENTA_MES);

      return {
        dataLabels: labels,
        dataValues: dataValues,
      };
    },
    getDataRadarChart(id_sucursal) {
      const _this = this;
      let sucursalData = this.chartData.filter(
        (item) => item.id_sucursal === id_sucursal
      );
      let departamentosUnicos = [
        ...new Set(sucursalData.map((item) => item.Departamento)),
      ];

      let datasets = [];
      let conceptos = [...new Set(sucursalData.map((item) => item.CONCEPTO))];

      // departamentosUnicos.forEach((departamento, index) => {
      //   const color = _this.getRandomColor(index);
      //   var dataValues = [];

      //   conceptos.forEach((concepto) => {
      //     const matchingItem = sucursalData.find(
      //       (item) =>
      //         item.Departamento === departamento && item.CONCEPTO === concepto
      //     );
      //     dataValues.push(
      //       matchingItem ? Number(matchingItem.ACUMULADO_VENTA_MES) : 0
      //     );
      //   });

      //   datasets.push({
      //     label: departamento,
      //     data: dataValues,
      //     backgroundColor: "rgba(75, 192, 192, 0.2)",
      //     borderColor: color,
      //     pointBackgroundColor: color,
      //   });
      // });
      conceptos.forEach((concepto, index) => {
        const color = _this.getRandomColor(index);
        var dataValues = [];

        departamentosUnicos.forEach((departamento) => {
          const matchingItem = sucursalData.find(
            (item) =>
              item.Departamento === departamento && item.CONCEPTO === concepto
          );
          dataValues.push(
            matchingItem ? Number(matchingItem.ACUMULADO_VENTA_MES) : 0
          );
        });

        datasets.push({
          label: concepto,
          data: dataValues,
          backgroundColor: "rgba(75, 192, 192, 0.2)",
          borderColor: color,
          pointBackgroundColor: color,
        });
      });

      return { dataLabels: departamentosUnicos, datasets: datasets };
      // return { dataLabels: conceptos, datasets: datasets };
    },
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
          data: { items, sumatoriaTotal, lastUpdated, chart, queryGroup },
          message,
        },
      } = await axios.get("/admin/marketing/sales-agencies", { params });
      this.$nextTick(() => {
        _this.items = items.data;
        _this.totalItems = items.total;
      });

      this.sumTotalVentasAg = sumatoriaTotal;
      this.lastUpdated = lastUpdated;
      this.chartData = chart;
      this.groupedData = queryGroup;

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