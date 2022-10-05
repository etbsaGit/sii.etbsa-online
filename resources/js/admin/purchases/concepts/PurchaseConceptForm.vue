<template>
  <v-card flat>
    <v-form>
      <v-row>
        <v-col cols="12">
          <v-text-field
            v-model="form.name"
            label="Nombre Concepto"
            filled
          ></v-text-field>
        </v-col>
        <v-col cols="12">
          <v-combobox
            v-model="form.products"
            label="Productos"
            multiple
            chips
          ></v-combobox>
        </v-col>
      </v-row>
      <!-- <v-row>
        <v-col cols="12">
          <h3>Configuracion de USO CFDI</h3>
        </v-col>
        <v-col
          cols="4"
          v-for="(g, k) in options.usoCfdi"
          :key="k"
          class="caption"
        >
          <v-switch
            v-model="form.usocfdi[g.clave]"
            :label="g.clave"
            :hint="g.description"
            persistent-hint
            dense
          ></v-switch>
        </v-col>
      </v-row> -->
    </v-form>
  </v-card>
</template>
<script>
export default {
  name: "PurchaseConceptForm",
  props: {
    form: {
      require: true,
      type: Object,
    },
  },
  data() {
    return {
      usocfdi: [],
      options: {
        usoCfdi: [],
      },
    };
  },
  created() {
    // this.loadOptions();
  },
  methods: {
    // loadForm() {
    //   const _this = this;
    //   _this.loadOptions();
    //   if (_this.form.id) {
    //     _this.getPurchaseConceptById(_this.form.id);
    //   }
    // },
    async getPurchaseConceptById(id) {
      const _this = this;

      await axios
        .get(`/admin/purchase-concept/${id}`)
        .then(function (response) {
          _this.form = response.data.data;
          _.each(_this.form.usocfdi, (g) => {
            _this.usocfdi[g.clave] = true;
          });
        });
    },
    // async loadOptions() {
    //   const _this = this;
    //   await axios.get("/admin/purchase-concept/create").then((res) => {
    //     _this.options.usoCfdi = res.data.data.uso_cfdi;

    //     _.each(_this.options.usoCfdi, (d) => {
    //       d.selected = false;
    //       _this.form.usocfdi[d] = false;
    //     });
    //   });
    // },
  },
};
</script>
<style></style>
