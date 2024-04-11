<template>
  <v-form v-model="valid" ref="form" lazy-validation>
    <v-row dense class="overline elevation-2 pa-2 mb-4">
      <v-col cols="12">
        <p class="text-14 mb-1">Seleccione a un Prospecto</p>
        <v-autocomplete
          v-model="form.prospect_id"
          :items="options.prospects"
          item-text="full_name"
          item-value="id"
          placeholder="Buscar por Nombre o Telefono o Razon social"
          :rules="[(v) => !!v || 'Es Requerido']"
          :filter="customFilter"
          hide-details
          clearable
          outlined
          dense
          filled
        >
          <template v-slot:prepend-item>
            <v-list dense color="green lighten-3 overline">
              <v-list-item @click="dialog = true">
                <v-list-item-content>
                  <v-list-item-title
                    >REGISTRAR un Nuevo Prospecto</v-list-item-title
                  >
                </v-list-item-content>
                <v-list-item-action>
                  <v-icon color="blue">mdi-account-plus</v-icon>
                </v-list-item-action>
              </v-list-item>
            </v-list>
          </template>
          <template v-slot:item="{ item }">
            <v-list-item-title> {{ item.full_name }} </v-list-item-title>
            <v-list-item-subtitle> {{ item.phone }} </v-list-item-subtitle>
            <v-list-item-subtitle> {{ item.company }} </v-list-item-subtitle>
          </template>
        </v-autocomplete>
      </v-col>
      <v-col cols="12">
        <p class="text-14 mb-1">Categoria del Seguimiento</p>
        <v-autocomplete
          v-model="form.title"
          :items="options.categories"
          item-value="name"
          item-text="name"
          placeholder="Seleccionar"
          :rules="[(v) => !!v || 'Es Requerido']"
          hide-details
          outlined
          filled
          dense
        >
        </v-autocomplete>
      </v-col>
      <v-col cols="12">
        <p class="text-14 mb-1">Producto a Cotizar</p>
        <v-combobox
          v-model="form.product"
          :items="options.products"
          item-value="name"
          item-text="name"
          placeholder="Buscar por Nombre o SKU"
          :rules="[(v) => !!v || 'Es Requerido']"
          :filter="customFilterProducts"
          hide-details
          return-object
          outlined
          filled
          dense
        >
          <template v-slot:item="{ item }">
            <v-list-item-title>{{ item.name }}</v-list-item-title>
            <v-list-item-subtitle>{{ item.sku }}</v-list-item-subtitle>
            <v-list-item-subtitle>
              {{ item.price_1 | money }}{{ item.currency.name }}
            </v-list-item-subtitle>
          </template>
        </v-combobox>
      </v-col>
      <v-col cols="8">
        <p class="text-14 mb-1">Valor del Lead</p>
        <v-text-field
          v-model.number="form.price"
          placeholder="0.00"
          :rules="[(v) => !!v || 'Es Requerido']"
          type="number"
          prefix="$"
          hide-details
          outlined
          filled
          dense
        ></v-text-field>
      </v-col>
      <v-col cols="4">
        <p class="text-14 mb-1">Tipo de Moneda</p>
        <v-select
          v-model="form.currency_id"
          :items="options.currency"
          item-value="id"
          item-text="name"
          placeholder="Placeholder"
          prepend-icon="mdi-cash-usd-outline"
          :rules="[(v) => !!v || 'Es Requerido']"
          hide-details
          outlined
          filled
          dense
        ></v-select>
      </v-col>
    </v-row>
    <v-row dense>
      <v-col cols="12" md="6">
        <p class="text-14 mb-1">Origen de Prospeccion</p>
        <v-select
          v-model="form.first_contact"
          :items="options.origin"
          placeholder="Placeholder"
          :rules="[(v) => !!v || 'Es Requerido']"
          hide-details
          outlined
          filled
          dense
        ></v-select>
      </v-col>
      <v-col cols="12" md="6">
        <p class="text-14 mb-1">Condicion de Pago</p>
        <v-select
          v-model="form.tracking_condition"
          :items="options.payment_conditions"
          placeholder="Placeholder"
          hide-details
          outlined
          filled
          dense
        ></v-select>
      </v-col>
      <v-col cols="12">
        <p class="text-14 mb-1">Etapa del LEAD</p>
        <v-select
          v-model="form.assertiveness"
          :items="options.assertiveness"
          :rules="[(v) => !!v || 'Es Requerido']"
          prepend-inner-icon="mdi-circle-slice-2"
          hide-details
          outlined
          filled
          dense
        >
          <template v-slot:item="{ item }">
            <v-list-item-content>
              <v-list-item-title> {{ item.text }} </v-list-item-title>
            </v-list-item-content>
            <v-list-item-action>
              <v-chip small dark :color="item.color">
                {{ item.value | percent }}
              </v-chip>
            </v-list-item-action>
          </template>
        </v-select>
      </v-col>
    </v-row>
    <v-row dense>
      <v-col cols="12" md="6">
        <p class="text-14 mb-1">Sucursal</p>
        <v-select
          v-model="form.agency_id"
          :items="options.agencies"
          item-text="title"
          item-value="id"
          placeholder="Seleccionar"
          hide-details
          outlined
          filled
          dense
        ></v-select>
      </v-col>
      <v-col cols="12" md="6">
        <p class="text-14 mb-1">Departamento</p>
        <v-select
          v-model="form.department_id"
          :items="options.departments"
          item-text="title"
          item-value="id"
          placeholder="Seleccionar"
          hide-details
          outlined
          filled
          dense
        ></v-select>
      </v-col>
      <v-col cols="12">
        <p class="text-14 mb-1">Vendedor Asignado</p>
        <v-autocomplete
          v-model="form.attended_by"
          :disabled="availableSeller"
          :items="options.sellers"
          item-text="name"
          item-value="id"
          placeholder="Seleccionar"
          hide-details
          outlined
          filled
          dense
        ></v-autocomplete>
      </v-col>
      <v-col cols="12">
        <p class="text-14 mb-1">Motivo del LEAD</p>
        <v-textarea
          v-model="form.description_topic"
          :rules="[(v) => !!v || 'Es requrido']"
          placeholder="Escribir a detalle el motivo del Seguimiento"
          height="100%"
          filled
          hide-details
          outlined
          dense
        ></v-textarea>
      </v-col>
    </v-row>

    <v-dialog v-model="dialog" max-width="600">
      <v-card min-height="400">
        <v-card-title class="grey lighten-4">
          Registrar Nuevo Prospecto
        </v-card-title>
        <v-card-text class="pt-2">
          <prospect-create
            :key="dialog"
            @success="loadOptions(), (dialog = false)"
          ></prospect-create>
        </v-card-text>
      </v-card>
    </v-dialog>
  </v-form>
</template>
<script>
import ProspectCreate from "../prospect/ProspectCreate.vue";
import Assertiveness from "@admin/sales/tracking/resources/assertiveness.json";
export default {
  components: { ProspectCreate },
  name: "TrackingForm",
  props: {
    form: {
      type: Object,
      require: true,
    },
  },
  data() {
    return {
      valid: true,
      dialog: false,
      selectModel: null,
      selectConfig: { hint: "" },
      options: {
        // tractors: Tractors,
        // implementos: Implementos,
        prospects: [],
        agencies: [],
        products: [],
        departments: [],
        sellers: [],
        payment_conditions: [
          "Por definir",
          "Financiamiento",
          "Contado",
          "Renta",
        ],
        origin: ["Online", "Visita en Agencia", "Visita de Campo"],
        categories: [],
        currency: [],
        assertiveness: Assertiveness,
      },
    };
  },
  mounted() {
    this.loadOptions();
  },
  watch: {
    "form.agency_id": function (v) {
      if (!!this.form.department_id && v) this.loadSellers(() => {});
    },
    "form.department_id": function (v) {
      if (!!this.form.agency_id && v) this.loadSellers(() => {});
    },
    "form.title": function (category_name) {
      const _this = this;
      this.form.product = null;
      this.loadProductsByCategory(() => {});
    },
    "form.product": function (v) {
      if (v != null && typeof v != "string") {
        this.form.reference = v.name;
        this.form.price = v.price_1;
        this.form.currency_id = v.currency.id;
      } else {
        this.form.reference = v;
        this.form.price = null;
        this.form.currency_id = null;
      }
    },
  },
  computed: {
    availableSeller() {
      const self = this;
      if (self.form.agency_id && self.form.department_id) {
        return false;
      } else {
        return true;
      }
    },
    // tipos() {
    //   return [...new Set(this.options.tractors.map((item) => item.Tipo))];
    // },
    // tiposImplementos() {
    //   return [
    //     ...new Set(this.options.implementos.map((item) => item.Categoria)),
    //   ];
    // },
    // models() {
    //   const filter = this.options.tractors.filter(
    //     (word) => word.Tipo === "Tractor"
    //   );
    //   return [...new Set(filter.map((item) => `${item.Modelo}`))];
    // },
    // ImplementoConfigurations() {
    //   const result = [];
    //   const map = new Map();
    //   const filter = this.options.implementos.filter(
    //     (item) => `${item.Categoria}` === this.selectModel
    //   );
    //   for (const item of filter) {
    //     console.log(item.Precio);
    //     result.push({
    //       text: `${item.Categoria} ${item.Modelo}`,
    //       price: item.Precio,
    //       currency: "MXN",
    //       hint: `${item.Modelo}`,
    //     });
    //   }
    //   console.log(result);
    //   return result;
    // },
    // TractorConfigurations() {
    //   const result = [];
    //   const map = new Map();
    //   const filter = this.options.tractors.filter(
    //     (item) => `${item.Modelo}` === this.selectModel
    //   );
    //   for (const item of filter) {
    //     if (!map.has(this.configuracion(item))) {
    //       map.set(this.configuracion(item), true); // set any value to Map
    //       result.push({
    //         text: this.configuracion(item).trim(),
    //         price: item.Precio,
    //         currency: item["Tipo Moneda"],
    //         hint: `${item.Modelo} ${item.field4} ${
    //           item["Traccion Sencilla"] === "SI" ? "Traccion Sencilla | " : ""
    //         } ${item["Doble Traccion"] === "SI" ? "Doble Traccion | " : ""} ${
    //           item["Power Reverser"] === "SI" ? "Power Reverser | " : ""
    //         } ${item.Creeper === "SI" ? "Creeper | " : ""} ${
    //           item["Con Cabina"] === "SI" ? "Con Cabina | " : ""
    //         } ${item["Cabina Premium"] === "SI" ? "Cabina Premium | " : ""} ${
    //           item["Doble rodado"] === "SI" ? "Doble rodado | " : ""
    //         } ${item.Ams === "SI" ? "AMS Incluido | " : ""}`.trim(),
    //       });
    //     }
    //   }
    //   return result;
    // },
  },
  methods: {
    async loadOptions() {
      const _this = this;
      await axios
        .get("/admin/tracking/sales_history/resources")
        .then(function (response) {
          let { agencies, departments, prospects, currency, categories } =
            response.data.data;
          _this.options.agencies = agencies;
          _this.options.departments = departments;
          _this.options.prospects = prospects;
          _this.options.currency = currency;
          _this.options.categories = categories;
        });
    },
    async loadSellers(cb) {
      const _this = this;
      // if (!this.editing) _this.form.attended_by = window.LSK_APP.AUTH_USER.id;
      let params = {
        seller_agency_id: _this.form.agency_id,
        seller_type_id: _this.form.department_id,
        paginate: "no",
      };
      _this.$store.commit("showLoader");
      await axios
        .get("/admin/sellers", { params: params })
        .then(function (response) {
          _this.options.sellers = response.data.data;
          _this.$store.commit("hideLoader");
        });
    },
    async loadProductsByCategory(cb) {
      const _this = this;

      let params = {
        category_name: _this.form.title,
        paginate: "no",
      };

      await axios.get("/admin/products", { params }).then((response) => {
        _this.options.products = response.data.data.data;
        (cb || Function)();
      });
    },
    customFilter(item, queryText, itemText) {
      const textName = item.full_name.toLowerCase();
      const textPhone = item.phone.toLowerCase();
      const searchText = queryText.toLowerCase();

      return (
        textName.indexOf(searchText) > -1 || textPhone.indexOf(searchText) > -1
      );
    },
    // customFilter({ item, queryText }) {
    //   const textName = item.full_name.toLowerCase();
    //   const textPhone = item.phone.toLowerCase();
    //   const textCompany = item.company ? item.company.toLowerCase() : "";
    //   const searchText = queryText.toLowerCase();

    //   return (
    //     textName.indexOf(searchText) > -1 ||
    //     textPhone.indexOf(searchText) > -1 ||
    //     textCompany.indexOf(searchText) > -1
    //   );
    // },
    customFilterProducts({ item, queryText }) {
      const textName = item.name.toLowerCase();
      const textSku = item.sku.toLowerCase();
      const searchText = queryText.toLowerCase();

      return (
        textName.indexOf(searchText) > -1 || textSku.indexOf(searchText) > -1
      );
    },
    configuracion(item) {
      return `${item.Modelo} ${item.field4} ${
        item["Traccion Sencilla"] === "SI" ? "TS" : ""
      } ${item["Doble Traccion"] === "SI" ? "DT" : ""} ${
        item["Power Reverser"] === "SI" ? "PR" : ""
      } ${item["Creeper"] === "SI" ? "Creeper" : ""} ${
        item["Con Cabina"] === "SI" ? "c/CAB" : ""
      } ${item["Cabina Premium"] === "SI" ? "CAB/Premium" : ""} ${
        item["Doble rodado"] === "SI" ? "DR" : ""
      } ${item["Ams"] === "SI" ? "AMS Incluido" : ""}`;
    },
  },
};
</script>
<style></style>
