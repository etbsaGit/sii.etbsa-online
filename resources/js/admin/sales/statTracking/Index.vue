<template>
  <v-card>
    <v-card-title>
      DASHBOARD CRM
      <v-spacer />
      <v-btn icon @click="getData()">
        <v-icon>mdi-refresh</v-icon>
      </v-btn>
    </v-card-title>
    <v-card-subtitle class="py-4">
      <v-expansion-panels>
        <v-expansion-panel>
          <v-expansion-panel-header>
            FILTROS DE BUSQUEDA
          </v-expansion-panel-header>
          <v-expansion-panel-content>
            <v-autocomplete
              v-model="filters.sellers"
              :items="options.sellers"
              item-text="name"
              item-value="id"
              label="Filtro Vendedor:"
              prepend-icon="mdi-filter-variant"
              chips
              deletable-chips
              small-chips
              hide-details
              clearable
              multiple
              filled
              dense
            ></v-autocomplete>
            <v-autocomplete
              v-model="filters.agencies"
              :items="options.agencies"
              item-text="title"
              item-value="id"
              label="Filtrar Agencia:"
              prepend-icon="mdi-filter-variant"
              chips
              deletable-chips
              small-chips
              hide-details
              clearable
              multiple
              filled
              dense
            ></v-autocomplete>
            <v-select
              v-model="filters.category"
              :items="options.categories"
              item-value="name"
              item-text="name"
              label="Categoria:"
              prepend-icon="mdi-filter-variant"
              hide-details
              clearable
              filled
              dense
            ></v-select>
            <v-select
              v-model="filters.assertiveness"
              :items="options.assertiveness"
              label="Certeza"
              placeholder="Filtrar por Certeza"
              prepend-icon="mdi-magnify"
              chips
              deletable-chips
              small-chips
              hide-details
              clearable
              multiple
              filled
            >
              <template #item="data">
                <v-list-item-content>
                  <v-list-item-title class="overline">
                    {{ data.item.text }}
                  </v-list-item-title>
                </v-list-item-content>
                <v-list-item-action>
                  <v-btn :color="data.item.color" dark>
                    {{ data.item.value | percent }}
                  </v-btn>
                </v-list-item-action>
              </template>
            </v-select>
            <v-text-field
              v-model="filters.title"
              label="Buscar por Referencia"
              prepend-icon="mdi-magnify"
              hide-details
              clearable
            ></v-text-field>

            <div class="d-flex flex-row mt-4">
              <v-select
                v-model="filters.months"
                :items="options.months"
                label="Filtrar Meses:"
                item-text="name"
                item-value="key"
                prepend-icon="mdi-filter-variant"
                class="overline"
                chips
                deletable-chips
                outlined
                small-chips
                hide-details
                clearable
                multiple
              ></v-select>
              <v-select
                v-model="filters.year"
                :items="[null, 2023, 2022]"
                label="Filtrar Año:"
                prepend-icon="mdi-filter-variant"
                outlined
                hide-details
                clearable
                filled
              ></v-select>
            </div>
          </v-expansion-panel-content>
        </v-expansion-panel>
      </v-expansion-panels>
    </v-card-subtitle>
    <v-card-text>
      <v-row>
        <v-col
          cols="12"
          md="3"
          v-for="(estatus, key) in stats"
          :key="`estatus_${key}`"
        >
          <v-card :color="colors[key]" dark elevation="4">
            <v-list-item>
              <v-list-item-avatar>
                <v-icon :class="['amber white--text']">
                  mdi-checkbox-marked
                </v-icon>
              </v-list-item-avatar>

              <v-list-item-content>
                <v-list-item-subtitle class="uppercase">
                  Seguimientos {{ estatus.title }}
                </v-list-item-subtitle>
                <br />
                <v-list-item-title class="text-h4">
                  {{ estatus.value }}
                </v-list-item-title>
              </v-list-item-content>
            </v-list-item>
          </v-card>
        </v-col>
      </v-row>
    </v-card-text>

    <v-card-text v-if="seguimientosPorVendedorCategoria">
      <v-card-title class="text-wrap">
        Resumen Seguimientos Por Vendedor y Categoria Prospeccion / Venta
      </v-card-title>

      <v-data-iterator
        :items="VendedoresCategoriaGroup"
        hide-default-footer
        disable-pagination
      >
        <template v-slot:default="props">
          <v-row>
            <v-col
              v-for="item in props.items"
              :key="item.vendedor"
              cols="12"
              sm="6"
              md="4"
            >
              <v-card color="indigo lighten-5">
                <v-card-title class="subheading font-weight-bold">
                  <v-avatar size="48px">
                    <img :alt="item.vendedor" :src="item.photo" />
                  </v-avatar>
                  <v-spacer />

                  {{ item.vendedor }}
                </v-card-title>

                <v-simple-table dense class="text-uppercase">
                  <template v-slot:default>
                    <thead>
                      <tr>
                        <th>Categoria</th>
                        <th class="text-right">Activos</th>
                        <th class="text-right">Perdidas</th>
                        <th class="text-right">Ganadas</th>
                      </tr>
                    </thead>

                    <tbody>
                      <tr
                        v-for="(group, category) in item.categorias"
                        :key="category"
                      >
                        <td>{{ category }}</td>
                        <td class="text-right">
                          {{ group.Activo || 0 | currency }} ({{
                            group.Activo_count || 0
                          }})
                        </td>
                        <td class="text-right">
                          {{ group["Venta Perdida"] || 0 | currency }} ({{
                            group["Venta Perdida_count"] || 0
                          }})
                        </td>
                        <td class="text-right">
                          {{ group["Venta Ganada"] || 0 | currency }} ({{
                            group["Venta Ganada_count"] || 0
                          }})
                        </td>
                      </tr>
                    </tbody>
                  </template>
                </v-simple-table>
              </v-card>
            </v-col>
          </v-row>
        </template>
      </v-data-iterator>
    </v-card-text>

    <v-card-text v-if="seguimientosPorVendedor">
      <v-card-title class="text-wrap">
        Resumen Seguimientos Por Vendedor
      </v-card-title>
      <v-simple-table dense class="text-uppercase" fixed-header height="250">
        <template v-slot:default>
          <thead>
            <tr>
              <th>Vendedor</th>
              <th class="text-right">Activos</th>
              <th class="text-right">Perdidas</th>
              <th class="text-right">Ganadas</th>
            </tr>
          </thead>

          <tbody>
            <tr v-for="(group, seller) in vendedoresAgrupadas" :key="seller">
              <td>{{ seller }}</td>
              <td class="text-right">
                {{ group.Activo || 0 | currency }} ({{
                  group.Activo_count || 0
                }})
              </td>
              <td class="text-right">
                {{ group["Venta Perdida"] || 0 | currency }} ({{
                  group["Venta Perdida_count"] || 0
                }})
              </td>
              <td class="text-right">
                {{ group["Venta Ganada"] || 0 | currency }} ({{
                  group["Venta Ganada_count"] || 0
                }})
              </td>
            </tr>
          </tbody>
        </template>
      </v-simple-table>
    </v-card-text>

    <v-card-text
      v-if="seguimientosPorCategoria && seguimientosPorCategoria.length > 0"
    >
      <v-row dense>
        <v-col cols="12" md="6">
          <v-card-title class="text-wrap">
            Resumen Seguimientos Por Categoria
          </v-card-title>
          <v-simple-table
            dense
            class="text-uppercase"
            fixed-header
            height="300"
          >
            <template v-slot:default>
              <thead>
                <tr>
                  <th>Categoria</th>
                  <th class="text-right">Activos</th>
                  <th class="text-right">Perdidas</th>
                  <th class="text-right">Ganadas</th>
                </tr>
              </thead>

              <tbody>
                <tr
                  v-for="(group, category) in categoriasAgrupadas"
                  :key="category"
                >
                  <td>{{ category }}</td>
                  <td class="text-right">
                    {{ group.Activo || 0 | currency }} ({{
                      group.Activo_count || 0
                    }})
                  </td>
                  <td class="text-right">
                    {{ group["Venta Perdida"] || 0 | currency }} ({{
                      group["Venta Perdida_count"] || 0
                    }})
                  </td>
                  <td class="text-right">
                    {{ group["Venta Ganada"] || 0 | currency }} ({{
                      group["Venta Ganada_count"] || 0
                    }})
                  </td>
                </tr>
              </tbody>
            </template>
          </v-simple-table>
        </v-col>
        <v-col cols="12" md="6">
          <v-card-title class="text-wrap">
            Resumen Seguimientos Por Sucursal
          </v-card-title>
          <v-simple-table
            dense
            class="text-uppercase"
            fixed-header
            height="300"
          >
            <template v-slot:default>
              <thead>
                <tr>
                  <th>Sucursal</th>
                  <th class="text-right">Activos</th>
                  <th class="text-right">Perdidas</th>
                  <th class="text-right">Ganadas</th>
                </tr>
              </thead>

              <tbody>
                <tr
                  v-for="(group, agency) in sucursalesAgrupadas"
                  :key="agency"
                >
                  <td>{{ agency }}</td>
                  <td class="text-right">
                    {{ group.Activo || 0 | currency }} ({{
                      group.Activo_count || 0
                    }})
                  </td>
                  <td class="text-right">
                    {{ group["Venta Perdida"] || 0 | currency }} ({{
                      group["Venta Perdida_count"] || 0
                    }})
                  </td>
                  <td class="text-right">
                    {{ group["Venta Ganada"] || 0 | currency }} ({{
                      group["Venta Ganada_count"] || 0
                    }})
                  </td>
                </tr>
              </tbody>
            </template>
          </v-simple-table>
        </v-col>
      </v-row>
    </v-card-text>
    <v-card-text v-if="Object.keys(lineChart).length > 0">
      <v-row dense>
        <v-col cols="12">
          <v-card-title class="text-wrap">
            Historico Seguimientos del Año
            <v-spacer />
            <v-switch v-model="lineChartTotals" label="En Valor"></v-switch>
          </v-card-title>
          <line-chart
            :data-set="ChartLineDatasetSeguimientos"
            :height="400"
            :width="800"
          ></line-chart>
        </v-col>
      </v-row>
    </v-card-text>
  </v-card>
</template>
<script>
import LineChart from "./LineChart.vue";
import Assertiveness from "@admin/sales/tracking/resources/assertiveness.json";
import { months } from "../../../api/dates.js";
export default {
  components: { LineChart },
  name: "StatTracking",
  mounted() {
    this.$store.commit("setBreadcrumbs", [
      { label: "Segumientos", to: { name: "tracking.list" } },
      { label: "Dashboard CRM", to: "" },
    ]);
    this.getData();
    this.loadResources(() => {});
  },
  data() {
    return {
      screenWidth: window.innerWidth,
      stats: {
        activos: {
          title: "Activos",
          value: 0,
        },
        perdidas: {
          title: "Perdidas",
          value: 0,
        },
        ganadas: {
          title: "Ganadas",
          value: 0,
        },
        total: {
          title: "Total",
          value: 0,
        },
      },
      seguimientosPorCategoria: [],
      seguimientosPorSucursal: [],
      seguimientosPorVendedor: [],
      seguimientosPorVendedorCategoria: [],
      lineChart: {},
      lineChartTotals: false,
      filters: {
        // folio: null,
        title: null,
        category: null,
        assertiveness: null,
        // prospect: [],
        agencies: [],
        // departments: [],
        sellers: [],
        // dates: [],
        estatus: "todos",
        year: null,
        months: [],
        // first_contact: null,
      },
      options: {
        prospects: [],
        agencies: [],
        departments: [],
        sellers: [],
        categories: [],
        assertiveness: Assertiveness,
        months: months,
        estatus: [
          { text: "Activos", value: "activo" },
          { text: "Ventas Perdidas", value: "finalizado" },
          { text: "Ventas Ganadas", value: "formalizado" },
          { text: "Todos", value: "todos" },
        ],
        origin: ["Online", "Visita en Agencia", "Visita de Campo", "Expo"],
      },
      colors: {
        activos: "green",
        perdidas: "red",
        ganadas: "blue",
        total: "grey",
      },
    };
  },
  computed: {
    VendedoresCategoriaGroup() {
      return this.seguimientosPorVendedorCategoria.reduce(
        (agrupados, vendedor) => {
          const {
            vendedor: nombreVendedor,
            categoria,
            estatus,
            total_comprado,
            count,
            photo,
          } = vendedor;

          const category = categoria
            .toLowerCase()
            .normalize("NFD")
            .replace(/[\u0300-\u036f]/g, "");

          // Buscar si ya existe un grupo para este vendedor
          let grupoVendedor = agrupados.find(
            (grupo) => grupo.vendedor === nombreVendedor
          );

          if (!grupoVendedor) {
            // Si el vendedor no existe en el arreglo agrupados, agregarlo
            grupoVendedor = {
              vendedor: nombreVendedor,
              photo: photo,
              categorias: {},
            };
            agrupados.push(grupoVendedor);
          }

          // Verificar si ya existe una categoría para este vendedor
          if (!grupoVendedor.categorias[category]) {
            grupoVendedor.categorias[category] = {};
          }

          // Agregar el total comprado y el count para esta categoría y estatus
          grupoVendedor.categorias[category][estatus] =
            (grupoVendedor.categorias[category][estatus] || 0) + total_comprado;
          grupoVendedor.categorias[category][`${estatus}_count`] = count;
          // grupoVendedor.categorias[category][estatus] = {
          //   total_comprado,
          //   count,
          // };

          return agrupados;
        },
        []
      );
    },

    vendedoresAgrupadas() {
      const Sellers = {};
      this.seguimientosPorVendedor.forEach((item) => {
        const { vendedor, estatus, total_comprado, count } = item;
        let seller = vendedor
          .toLowerCase()
          .normalize("NFD")
          .replace(/[\u0300-\u036f]/g, "");
        if (!Sellers[seller]) {
          Sellers[seller] = {};
        }
        Sellers[seller][estatus] =
          (Sellers[seller][estatus] || 0) + total_comprado;
        Sellers[seller][`${estatus}_count`] = count;
      });
      return Sellers;
    },
    categoriasAgrupadas() {
      const categorias = {};
      this.seguimientosPorCategoria.forEach((item) => {
        const { categoria, estatus, total_comprado, count } = item;
        let category = categoria
          .toLowerCase()
          .normalize("NFD")
          .replace(/[\u0300-\u036f]/g, "");
        if (!categorias[category]) {
          categorias[category] = {};
        }
        categorias[category][estatus] =
          (categorias[category][estatus] || 0) + total_comprado;
        categorias[category][`${estatus}_count`] = count;
      });
      return categorias;
    },
    sucursalesAgrupadas() {
      const sucursales = {};
      this.seguimientosPorSucursal.forEach((item) => {
        const { sucursal, estatus, total_comprado, count } = item;
        if (!sucursales[sucursal]) {
          sucursales[sucursal] = {};
        }
        sucursales[sucursal][estatus] =
          (sucursales[sucursal][estatus] || 0) + total_comprado;
        sucursales[sucursal][`${estatus}_count`] = count;
      });
      return sucursales;
    },
    ChartLineDatasetSeguimientos() {
      const _this = this;
      const months = [
        "Enero",
        "Febrero",
        "Marzo",
        "Abril",
        "Mayo",
        "Junio",
        "Julio",
        "Agosto",
        "Septiembre",
        "Octubre",
        "Noviembre",
        "Diciembre",
      ];
      const activos = Object.values(_this.lineChart.activosDelAñoActual).map(
        (item) => (_this.lineChartTotals ? item.total : item.count)
      );
      const perdidas = Object.values(_this.lineChart.perdidasDelAñoActual).map(
        (item) => (_this.lineChartTotals ? item.total : item.count)
      );
      const ganadas = Object.values(_this.lineChart.ganadasDelAñoActual).map(
        (item) => (_this.lineChartTotals ? item.total : item.count)
      );
      return {
        labels: months,
        datasets: [
          {
            label: "Activos",
            data: activos,
            borderColor: "rgba(0, 255, 0, 1)",
            backgroundColor: "rgba(0, 255, 0, 0.5)",
          },
          {
            label: "Perdidas",
            data: perdidas,
            borderColor: "rgba(255, 0, 0, 1)",
            backgroundColor: "rgba(255, 0, 0, 0.5)",
          },
          {
            label: "Ganadas",
            data: ganadas,
            borderColor: "rgba(0, 123, 255, 1)",
            backgroundColor: "rgba(0, 123, 255, 0.5)",
          },
        ],
      };
    },
  },
  methods: {
    async getData() {
      const _this = this;
      let params = {
        ..._this.filters,
        sellers: _this.filters.sellers.join(","),
        agencies: _this.filters.agencies.join(","),
      };
      let {
        data: {
          data: {
            countActivos,
            countPerdidas,
            countGanadas,
            TotalCount,
            seguimientosPorCategoria,
            seguimientosPorSucursal,
            seguimientosPorVendedor,
            seguimientosPorVendedorCategoria,
            lineChart,
          },
        },
      } = await axios.get("/admin/tracking/dashboard/stats", {
        params: params,
      });

      _this.stats.activos.value = countActivos;
      _this.stats.perdidas.value = countPerdidas;
      _this.stats.ganadas.value = countGanadas;
      _this.stats.total.value = TotalCount;
      _this.seguimientosPorCategoria = seguimientosPorCategoria;
      _this.seguimientosPorSucursal = seguimientosPorSucursal;
      _this.seguimientosPorVendedor = seguimientosPorVendedor;
      _this.seguimientosPorVendedorCategoria = seguimientosPorVendedorCategoria;
      _this.lineChart = lineChart;
    },
    async loadResources(cb) {
      const _this = this;

      let params = {
        paginate: "no",
      };
      await axios
        .get("/admin/tracking/sales_history/resources")
        .then(function (response) {
          let Data = response.data.data;
          _this.options.agencies = Data.agencies;
          _this.options.categories = Data.categories;
        });

      await axios
        .get("/admin/sellers", { params: params })
        .then(function (response) {
          _this.options.sellers = response.data.data;
          (cb || Function)();
        });
    },
  },
};
</script>
<style></style>
