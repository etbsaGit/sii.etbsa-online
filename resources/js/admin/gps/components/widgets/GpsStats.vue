<template>
  <v-slide-group
    v-model="model"
    class="pa-4"
    show-arrows
    mandatory
    center-active
  >
    <v-slide-item
      v-for="(month, n) in items"
      :key="n"
      v-slot:default="{ toggle }"
    >
      <v-card flat class="mx-2" @click="toggle">
        <div class="text-center overline">Mes {{ months[n] }} {{ year }}</div>
        <v-card-text class="py-0">
          <v-chip-group>
            <v-tooltip top>
              <template v-slot:activator="{ on, attrs }">
                <v-chip
                  dark
                  small
                  v-bind="attrs"
                  v-on="on"
                  @click="emitQuery(n, 'totales')"
                >
                  {{ month.Total }}
                </v-chip>
              </template>
              <span>GPS Totales</span>
            </v-tooltip>
            <v-tooltip top>
              <template v-slot:activator="{ on, attrs }">
                <v-chip
                  color="blue"
                  dark
                  small
                  v-bind="attrs"
                  v-on="on"
                  @click="emitQuery(n, 'nuevos')"
                >
                  {{ month.Nuevos }}
                </v-chip>
              </template>
              <span>GPS Nuevos </span>
            </v-tooltip>
            <v-tooltip top>
              <template v-slot:activator="{ on, attrs }">
                <v-chip
                  color="success"
                  dark
                  small
                  v-bind="attrs"
                  v-on="on"
                  @click="emitQuery(n, 'renovados')"
                >
                  {{ month.Renovados }}
                </v-chip>
              </template>
              <span>GPS Renovados </span>
            </v-tooltip>
            <v-tooltip top>
              <template v-slot:activator="{ on, attrs }">
                <v-chip
                  color="orange"
                  dark
                  small
                  v-bind="attrs"
                  v-on="on"
                  @click="emitQuery(n, 'por_renovar')"
                >
                  {{ month.PorRenovar }}
                </v-chip>
              </template>
              <span>GPS Por Renovar </span>
            </v-tooltip>
            <v-tooltip top>
              <template v-slot:activator="{ on, attrs }">
                <v-chip
                  color="red"
                  dark
                  small
                  v-bind="attrs"
                  v-on="on"
                  @click="emitQuery(n, 'cancelados')"
                >
                  {{ month.Cancelados }}
                </v-chip>
              </template>
              <span>GPS Cancelados </span>
            </v-tooltip>
            <v-tooltip top>
              <template v-slot:activator="{ on, attrs }">
                <v-chip dark small v-bind="attrs" v-on="on">
                  {{ month.Porcentaje | percent() }}
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
export default {
  data: () => ({
    model: null,
    months: [],
    year: "",
    items: [],
  }),
  created() {
    const self = this;
    self.$eventBus.$on(["GPS_ADDED", "GPS_UPDATED", "GPS_DELETED"], () => {
      self.fetchGps(() => {});
    });
    self.fetchGps(() => {});
    self.months = moment.months();
    self.year = moment().year();
  },
  methods: {
    fetchGps(cb) {
      const self = this;
      axios.post("/admin/gps/statsMonths").then(function(response) {
        self.items = response.data.data;
      });
    },
    emitQuery(month, type_query) {
      const self = this;
      self.$eventBus.$emit("STAT_QUERY", { month, type_query });
    },
  },
};
</script>
