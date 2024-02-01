<template>
  <v-container>
    <v-card flat>
      <v-card-text>
        <v-row dense>
          <v-col cols="12">
            <v-combobox
              v-model="items.cultivos"
              label="Cultivos"
              :items="cultivos"
              multiple
              chips
              deletable-chips
              outlined
              filled
              hint="Lo que usualmente cultiva"
              persistent-hint
            ></v-combobox>
          </v-col>
          <v-col cols="12">
            <v-textarea
              v-model="items.reporte_campo"
              label="Rep. Campo (Descripcion)"
              filled
            ></v-textarea>
          </v-col>
        </v-row>
      </v-card-text>
    </v-card>
  </v-container>
</template>
<script>
export default {
  name: "MetatableReporteCampo",
  props: {
    customerId: {
      type: Number | String,
      require: true,
    },
    items: {
      type: Object,
      default: () => {
        return {
          cultivos: [],
          reporte_campo: "",
        };
      },
    },
  },

  data() {
    return {
      cultivos: [
        "Ajo",
        "Alfalfa",
        "Apio",
        "Avena",
        "Brócoli",
        "Calabaza",
        "Cebada",
        "Cebada",
        "Cebolla",
        "Chile Poblano",
        "Cilantro",
        "Col",
        "Coliflor",
        "Esparrago",
        "Espinaca",
        "Fresa",
        "Frijol",
        "Lechuga",
        "Maiz",
        "Papa",
        "Radicchios",
        "Sorgo",
        "Trigo",
        "Zanahoria",
        "Zanahoria",
        "Zarzamora",
        "Jicama",
        "Hortalizas",
        "Fresa",
        "Jitomate",
        "Chile Jalapeño",
        "Chile Chilaca",
        "Tomate",
      ],
    };
  },
  mounted() {
    const _this = this;
    this.$eventBus.$on(["SAVE_REPORTE_CAMPO"], () => {
      _this.updateMeta();
    });
  },
  methods: {
    async updateMeta() {
      const _this = this;
      let payload = {
        cultivos: _this.items.cultivos,
        reporte_campo: _this.items.reporte_campo,
      };
      await axios
        .post(`/admin/customer/${_this.customerId}/updateMeta`, payload)
        .then((response) => {
          console.log(response);
          _this.dialog = false;
        });
    },
  },
};
</script>
<style></style>
