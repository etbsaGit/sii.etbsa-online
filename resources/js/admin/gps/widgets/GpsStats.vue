<template>
  <v-slide-group
    v-model="model"
    class="py-2"
    show-arrows
    mandatory
    center-active
    active-class="accent white--text"
    @change="changeMonth"
  >
    <v-slide-item
      v-for="stat in stats"
      :key="stat.Month"
      v-slot:default="{ toggle }"
    >
      <v-card class="ma-2" @click="toggle">
        <div class="text-center overline">
          Mes {{ months[stat.Month - 1] }} {{ year }}
        </div>
        <v-card-text class="py-0">
          <v-chip-group>
            <v-tooltip top>
              <template v-slot:activator="{ on, attrs }">
                <v-chip
                  small
                  v-bind="attrs"
                  v-on="on"
                  @click="emitQuery(stat.Month, 'totales')"
                >
                  {{ stat.Total }}
                </v-chip>
              </template>
              <span>GPS Totales</span>
            </v-tooltip>
            <v-tooltip top>
              <template v-slot:activator="{ on, attrs }">
                <v-chip
                  color="blue"
                  small
                  v-bind="attrs"
                  v-on="on"
                  @click="emitQuery(stat.Month, 'nuevos')"
                >
                  {{ stat.Nuevos }}
                </v-chip>
              </template>
              <span>GPS Nuevos </span>
            </v-tooltip>
            <v-tooltip top>
              <template v-slot:activator="{ on, attrs }">
                <v-chip
                  color="success"
                  small
                  v-bind="attrs"
                  v-on="on"
                  @click="emitQuery(stat.Month, 'renovados')"
                >
                  {{ stat.Renovados }}
                </v-chip>
              </template>
              <span>GPS Renovados </span>
            </v-tooltip>
            <v-tooltip top>
              <template v-slot:activator="{ on, attrs }">
                <v-chip
                  color="orange"
                  small
                  v-bind="attrs"
                  v-on="on"
                  @click="emitQuery(stat.Month, 'por_renovar')"
                >
                  {{ stat.PorRenovar }}
                </v-chip>
              </template>
              <span>GPS Por Renovar </span>
            </v-tooltip>
            <v-tooltip top>
              <template v-slot:activator="{ on, attrs }">
                <v-chip
                  color="red"
                  small
                  v-bind="attrs"
                  v-on="on"
                  @click="emitQuery(stat.Month, 'cancelados')"
                >
                  {{ stat.Cancelados }}
                </v-chip>
              </template>
              <span>GPS Cancelados </span>
            </v-tooltip>
            <v-tooltip top>
              <template v-slot:activator="{ on, attrs }">
                <v-chip small v-bind="attrs" v-on="on">
                  {{ stat.Porcentaje | percent() }}
                </v-chip>
              </template>
              <span>Porcentaje Renovados </span>
            </v-tooltip>
          </v-chip-group>
        </v-card-text>
      </v-card>
    </v-slide-item>
  </v-slide-group>
</template>

<script>
import { months } from "~/api/dates";

export default {
  data: () => ({
    model: null,
    months: months,
    year: "",
    stats: [],
  }),
  created() {
    const _this = this;
    _this.$eventBus.$on(["GPS_ADDED", "GPS_UPDATED", "GPS_DELETED"], () => {
      _this.fetchGps(() => {});
    });
    _this.fetchGps(() => {});
    // _this.months = moment.months();
    _this.year = moment().year();
  },
  methods: {
    fetchGps(cb) {
      const _this = this;
      axios.get("/admin/gps/stats/allYear").then(function (response) {
        _this.stats = response.data.data;
      });
    },
    emitQuery(month, stat_query) {
      const _this = this;
      _this.$eventBus.$emit("STAT_QUERY", { month, stat_query });
    },
    changeMonth(value) {
      console.log(this.months[value]);
    },
  },
};
</script>
