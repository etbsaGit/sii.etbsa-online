<template>
  <Radar
    :options="chartOptions"
    :data="chartData"
    :chart-id="chartId"
    :dataset-id-key="datasetIdKey"
    :plugins="plugins"
    :css-classes="cssClasses"
    :styles="styles"
    :width="width"
    :height="height"
  />
</template>
  
  <script>
import { Radar } from "vue-chartjs";

import {
  Chart as ChartJS,
  Title,
  Tooltip,
  Legend,
  PointElement,
  LineElement,
  RadialLinearScale,
  CategoryScale,
} from "chart.js";

ChartJS.register(
  Title,
  Tooltip,
  Legend,
  PointElement,
  RadialLinearScale,
  LineElement,
  CategoryScale
);

export default {
  name: "RadarChart",
  components: {
    Radar,
  },
  props: {
    chartId: {
      type: String,
      default: "radar-chart",
    },
    datasetIdKey: {
      type: String,
      default: "label",
    },
    width: {
      type: Number,
      default: 400,
    },
    height: {
      type: Number,
      default: 400,
    },
    cssClasses: {
      default: "",
      type: String,
    },
    styles: {
      type: Object,
      default: () => {},
    },
    plugins: {
      type: Array,
      default: () => [],
    },
    dataLabels: {
      type: Array,
      required: true,
    },
    // dataValues: {
    //   type: Array,
    //   required: true,
    // },
    datasets: {
      type: Array,
      required: true,
    },
  },
  data() {
    return {
      chartData: {
        labels: this.dataLabels,
        datasets: this.datasets,
      },
      chartOptions: {
        responsive: true,
        // maintainAspectRatio: true,
      },
    };
  },
};
</script>