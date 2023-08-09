<template>
  <Pie
    :options="chartOptions"
    :data="ChartData"
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
import { Pie } from "vue-chartjs";

import {
  Chart as ChartJS,
  Title,
  Tooltip,
  Legend,
  ArcElement,
  CategoryScale,
} from "chart.js";

ChartJS.register(Title, Tooltip, Legend, ArcElement, CategoryScale);

export default {
  name: "PieChart",
  components: {
    Pie,
  },
  props: {
    chartId: {
      type: String,
      default: "pie-chart",
    },
    datasetIdKey: {
      type: String,
      default: "label",
    },
    width: {
      type: Number,
      default: 200,
    },
    height: {
      type: Number,
      default: 200,
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
    dataValues: {
      type: Array,
      required: true,
    },
  },
  data() {
    return {
      chartOptions: {
        responsive: true,
        maintainAspectRatio: false,
      },
    };
  },
  computed: {
    ChartData() {
      return {
        labels: this.dataLabels,
        datasets: [
          {
            data: this.dataValues,
            backgroundColor: [
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
            ],
          },
        ],
      };
    },
  },
};
</script>