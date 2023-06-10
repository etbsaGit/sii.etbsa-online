<template>
  <v-dialog v-model="dialog" transition="dialog-top-transition" max-width="600">
    <template v-slot:activator="{ on, attrs }">
      <v-btn text v-bind="attrs" v-on="on" small class="ml-2">
        importat CSV<v-icon right>mdi-file-delimited</v-icon>
      </v-btn>
    </template>
    <v-card v-if="dialog">
      <v-card-title class="white--text accent title text-uppercase">
        Importar Lista de Articulos
        <v-spacer />
        <v-icon large color="red" @click="dialog = false">
          mdi-close-circle
        </v-icon>
      </v-card-title>
      <v-card-text class="pa-0">
        <import-csv-component
          @input="(items) => addProducts(items)"
          :map-fields="mapFields"
          loadBtnText="Cargar CSV"
          submitBtnText="Importar"
        ></import-csv-component>
      </v-card-text>
    </v-card>
  </v-dialog>
</template>
<script>
import ImportCsvComponent from "../../components/ImportCsvComponent.vue";
export default {
  components: { ImportCsvComponent },
  name: "PurchaseImportProductsCsv",
  props: {
    value: {
      type: Array,
      default: () => {
        return [];
      },
    },
  },

  data() {
    return {
      dialog: false,
      mapFields: [
        { key: "clv_prod", label: "Clave Prod." },
        { key: "description", label: "Descripcion" },
        { key: "qty", label: "Cantidad" },
        { key: "price", label: "Precio U." },
        { key: "discount", label: "Descuento" },
      ],
    };
  },
  computed: {
    Model() {
      return this.value;
    },
  },
  methods: {
    async getClvProducts(item) {
      try {
        let ClaveProdServ = {
          c_ClaveProdServ: "1010101",
          Description: "No existe en el catálogo",
        };
        let ClvUnit = { id: 1, clave: "H87", name: "Pieza" };
        let params = {
          ClvProd: item.clv_prod ? item.clv_prod.replace(/\s+/g, "") : "",
        };
        const { data: response } = await axios.get("/admin/byClaveProdSat", {
          params,
        });
        ClaveProdServ = response.data;
        return {
          claveProduct: ClaveProdServ,
          description: item.description || "",
          unit: ClvUnit,
          qty: item.qty || 1,
          price: item.price || 0,
          discount: item.discount || 0,
        };
      } catch (error) {
        console.error(error);
        return Object.assign({}, { ...item }, { claveProduct: ClaveProdServ });
      }
    },
    async addProducts(RowsCSV) {
      const _this = this;
      const promisses = RowsCSV.map((item) => _this.getClvProducts(item));
      const results = await Promise.all(promisses);
      let products = _this.Model.concat(results);

      _this.$store.commit("showSnackbar", {
        message: "Productos Cargados",
        color: "success",
        duration: 3000,
      });
      _this.dialog = false;
      _this.$emit("input", products);
    },
  },
};
</script>
<style></style>
