<template>
  <v-container class="container--fluid grid-list-md text-center">
    <v-row dense>
      <!-- <v-col cols="12" sm="6" md="4">
          <app-widget title="Bar Chart">
            <option-chart
              slot="widget-content"
              height="300px"
              width="100%"
              :chart-data="getChartOption('bar')"
            />
          </app-widget>
        </v-col>
        <v-col cols="12" sm="6" md="4">
          <app-widget title="Polar Chart">
            <option-chart
              slot="widget-content"
              height="300px"
              width="100%"
              :chart-data="getChartOption('polar')"
            />
          </app-widget>
        </v-col>
        <v-col cols="12" sm="6" md="4">
          <app-widget title="Bar Three Colums Chart">
            <option-chart
              slot="widget-content"
              height="300px"
              width="100%"
              :chart-data="getChartOption('bar_cols')"
            />
          </app-widget>
        </v-col> -->
      <v-col
        cols="12"
        md="6"
        v-for="{ name, yAxis, series } in charData"
        :key="name"
      >
        <app-widget :title="name">
          <option-chart
            slot="widget-content"
            height="400px"
            width="100%"
            :chart-data="
              getChartOption({ type: 'bar_row', data: { yAxis, series } })
            "
          />
        </app-widget>
      </v-col>
    </v-row>
  </v-container>
</template>

<script>
import OptionChart from "./OptionChart.vue";
import AppWidget from "../components/AppWidget.vue";

export default {
  name: "Dashboard",
  components: {
    OptionChart,
    AppWidget,
  },
  data() {
    return {
      category: [],
      series: [],
      dataset: [],
      sucursales: [
        { name: "Celaya", dataset: [], visible: false },
        { name: "Salamanca", dataset: [], visible: false },
        { name: "Abasolo", dataset: [], visible: false },
      ],
    };
  },
  mounted() {
    this.initDataChart();
  },
  computed: {
    charData() {
      return this.dataset;
    },
  },

  methods: {
    async initDataChart() {
      let { data } = await axios.get("/admin/metas_sucursal");
      this.dataset = data;
    },
    getChartOption({ type, data } = {}) {
      const _this = this;
      console.log(type, data);
      switch (type) {
        case "bar":
          return getBarChartOption({ animationDuration: 2000 });
        case "polar":
          return getPolarChatOption({ animationDuration: 2000 });
        case "bar_cols":
          return getBarThereColsChartOption({ animationDuration: 2000 });
        case "bar_row":
          return _this.getBarThereRowChartOption({
            animationDuration: 2000,
            data,
          });
        default:
          return {};
      }
    },
    getBarThereRowChartOption({ animationDuration, data } = {}) {
      const _this = this;
      return {
        title: {
          text: "Ventas vs Meta",
        },
        tooltip: {
          trigger: "axis",
          axisPointer: {
            type: "shadow",
          },
        },
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
        yAxis: data.yAxis,
        series: data.series,
        // yAxis: {
        //   type: "category",
        //   data: ["Brazil", "Indonesia", "USA", "India", "China", "World"],
        // },
        // series: [
        //   {
        //     name: "2011",
        //     type: "bar",
        //     data: [18203, 23489, 29034, 104970, 131744, 30230],
        //   },
        //   {
        //     name: "2012",
        //     type: "bar",
        //     data: [19325, 23438, 31000, 121594, 134141, 81807],
        //   },
        // ],
      };
    },
  },
};
</script>
