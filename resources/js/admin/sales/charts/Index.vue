<template>
  <v-container class="container--fluid grid-list-md text-center">
    <v-row dense>
      <v-col
        cols="12"
        md="6"
        v-for="options in chartBarSaleAgencyOptions"
        :key="options.name"
      >
        <app-widget :title="options.name">
          <option-chart
            slot="widget-content"
            height="400px"
            width="100%"
            :chart-data="getChartOption({ type: 'bar_row', options: options })"
          />
        </app-widget>
      </v-col>
      <v-col
        cols="12"
        md="4"
        v-for="(options, index) in chartPolarSellerOptions"
        :key="index"
      >
        <app-widget :title="`${options.vendedor} - ${options.sucursal}`">
          <option-chart
            slot="widget-content"
            height="300px"
            width="100%"
            :chart-data="getChartOption({ type: 'polar', options: options })"
          />
        </app-widget>
      </v-col>
    </v-row>
  </v-container>
</template>

<script>
import OptionChart from "./OptionChart.vue";
import AppWidget from "../../components/AppWidget.vue";

export default {
  name: "Charts",
  components: {
    OptionChart,
    AppWidget,
  },
  data() {
    return {
      category: [],
      series: [],
      dataset: [],
      dataset_vendedores: [],
      sucursales: [
        { name: "Celaya", dataset: [], visible: false },
        { name: "Salamanca", dataset: [], visible: false },
        { name: "Abasolo", dataset: [], visible: false },
      ],
    };
  },
  mounted() {
    this.initDataChart();
    this.initDataChartPolar();
  },
  computed: {
    chartBarSaleAgencyOptions() {
      return this.dataset;
    },
    chartPolarSellerOptions() {
      return this.dataset_vendedores;
    },
  },

  methods: {
    async initDataChart() {
      let { data } = await axios.get("/admin/metas_sucursal");
      this.dataset = data;
    },
    async initDataChartPolar() {
      let { data } = await axios.get("/admin/ventas_vendedores");
      this.dataset_vendedores = data;
    },
    getChartOption({ type, options } = {}) {
      const _this = this;
      console.log(type, options);
      switch (type) {
        case "bar":
          return getBarChartOption({ animationDuration: 2000 });
        case "polar":
          return _this.getPolarChatOption({ animationDuration: 2000, options });
        case "bar_cols":
          return getBarThereColsChartOption({ animationDuration: 2000 });
        case "bar_row":
          return _this.getBarThereRowChartOption({
            animationDuration: 2000,
            options,
          });
        default:
          return {};
      }
    },
    getBarThereRowChartOption({ animationDuration, options } = {}) {
      const _this = this;
      return {
        ...options,
        legend: {},
        grid: {
          left: "2%",
          right: "4%",
          bottom: "3%",
          containLabel: true,
        },
        xAxis: {
          type: "value",
          boundaryGap: [0, 0.01],
        },
      };
    },
    getPolarChatOption({ animationDuration, options } = {}) {
      const _this = this;
      return {
        ...options,
        angleAxis: {
          startAngle: 75,
        },
        radiusAxis: {
          type: "category",
          data: ["Gasto", "Margen", "Venta"],
        },
        polar: { radius: [10, "70%"] },
        tooltip: {
          trigger: "axis",
          axisPointer: {
            type: "shadow",
          },
        },
        // series: options.series,
        // legend: {
        //   show: true,
        //   data: ["Dinero"],
        // },
      };
    },
  },
};
</script>
