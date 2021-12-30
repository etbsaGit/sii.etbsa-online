<template>
  <div :class="className" :style="{ height: height, width: width }" />
</template>

<script>
import * as echarts from "echarts/core";
require("echarts/theme/jazz"); // echarts theme

export default {
  name: "MetasChart",
  props: {
    className: {
      type: String,
      default: "chart",
    },
    width: {
      type: String,
      default: "100%",
    },
    height: {
      type: String,
      default: "300px",
    },
    chartData: {
      type: Object,
      required: true,
    },
  },
  data: () => ({
    chart: null,
  }),
  watch: {
    chartData: {
      deep: true,
      handler(val) {
        this.setOptions(val);
      },
    },
  },
  mounted() {
    this.initChart();
    this.resizeHandler = _.debounce(() => {
      if (this.chart) {
        this.chart.resize();
      }
    }, 100);
    window.addEventListener("resize", this.resizeHandler);
  },
  beforeDestroy() {
    if (!this.chart) {
      return;
    }
    window.removeEventListener("resize", this.resizeHandler);
    this.chart.dispose();
    this.chart = null;
  },
  methods: {
    setOptions(option = {}) {
      this.chart.setOption(option);
    },
    initChart() {
      this.chart = echarts.init(this.$el, "macarons");
      this.setOptions(this.chartData);
    },
  },
};
// sucursal,departamento,venta_mes_actual,venta_mes_año_pasado,meta_mes_actual,meta_anual,meta_enero_mes_actual,venta_enero_mes_actual,	meta_enero_mes_actual_año_pasado
</script>
