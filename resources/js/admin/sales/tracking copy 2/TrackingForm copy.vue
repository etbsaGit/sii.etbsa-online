<template>
  <v-form v-model="valid" ref="form" lazy-validation>
    <v-row dense class="overline elevation-2 pa-2 mb-4">
      <v-col cols="12" md="6">
        <p class="text-14 mb-1">
          Prospecto
        </p>
        <v-autocomplete
          v-model="form.prospect_id"
          :items="options.prospects"
          item-text="full_name"
          item-value="id"
          placeholder="Buscar por Nombre | Telefono:"
          :rules="[(v) => !!v || 'Es Requerido']"
          :filter="customFilter"
          hide-details
          clearable
          outlined
          dense
        >
          <template v-slot:prepend-item>
            <v-list dense color="grey lighten-3">
              <v-list-item @click="dialog = true">
                <v-list-item-content>
                  <v-list-item-title v-text="'Registar Nuevo Propecto'" />
                </v-list-item-content>
                <v-list-item-action>
                  <v-icon>mdi-account-plus</v-icon>
                </v-list-item-action>
              </v-list-item>
            </v-list>
          </template>
          <template v-slot:item="{ item }">
            <v-list-item-title v-html="item.full_name"></v-list-item-title>
            <v-list-item-subtitle v-html="item.phone"></v-list-item-subtitle>
          </template>
        </v-autocomplete>
      </v-col>
      <v-col cols="12" md="6">
        <p class="text-14 mb-1">
          Categoria de la referencia
        </p>
        <v-select
          v-model="form.title"
          :items="options.categories"
          item-value="title"
          item-text="title"
          placeholder="Seleccionar"
          :rules="[(v) => !!v || 'Es Requerido']"
          hide-details
          outlined
          dense
        ></v-select>
      </v-col>
      <v-col cols="12" md="7">
        <p class="text-14 mb-1">
          Producto de Interes
        </p>
        <v-text-field
          v-model="form.reference"
          placeholder="Describa el Producto"
          :rules="[(v) => !!v || 'Es Requerido']"
          hide-details
          outlined
          dense
        ></v-text-field>
      </v-col>
      <v-col cols="12" md="3">
        <p class="text-14 mb-1">
          Precio a tratar
        </p>
        <v-text-field
          v-model="form.price"
          placeholder="0.00"
          :rules="[(v) => !!v || 'Es Requerido']"
          type="number"
          prefix="$"
          hide-details
          outlined
          dense
        ></v-text-field>
      </v-col>
      <v-col cols="12" md="2">
        <p class="text-14 mb-1">
          Tipo de Moneda
        </p>
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
          dense
        ></v-select>
      </v-col>
      <v-col cols="12" md="4">
        <p class="text-14 mb-1">
          Condicion de Pago
        </p>
        <v-select
          v-model="form.tracking_condition"
          :items="options.payment_conditions"
          placeholder="Placeholder"
          hide-details
          outlined
          dense
        ></v-select>
      </v-col>
      <v-col cols="12" md="4">
        <p class="text-14 mb-1">
          Etapa
        </p>
        <v-select
          v-model="form.assertiveness"
          :items="options.assertiveness"
          :rules="[(v) => !!v || 'Es Requerido']"
          prepend-inner-icon="mdi-circle-slice-2"
          hide-details
          outlined
          dense
        >
          <template v-slot:item="data">
            <v-list-item-content>
              <v-list-item-title v-text="data.item.text"></v-list-item-title>
            </v-list-item-content>
            <v-list-item-action>
              <v-chip small dark :color="data.item.color">
                {{ data.item.value | percent }}
              </v-chip>
            </v-list-item-action>
          </template>
        </v-select>
      </v-col>
      <v-col cols="12" md="4">
        <p class="text-14 mb-1">
          Origen de Prospeccion
        </p>
        <v-select
          v-model="form.first_contact"
          :items="options.origin"
          placeholder="Placeholder"
          :rules="[(v) => !!v || 'Es Requerido']"
          hide-details
          outlined
          dense
        ></v-select>
      </v-col>
    </v-row>
    <v-row dense class="overline elevation-2 pa-2">
      <v-col cols="12" md="6">
        <p class="text-14 mb-1">
          Sucursal
        </p>
        <v-select
          v-model="form.agency_id"
          :items="options.agencies"
          item-text="title"
          item-value="id"
          placeholder="Seleccionar"
          hide-details
          outlined
          dense
        ></v-select>
        <p class="text-14 mb-1">
          Departamento
        </p>
        <v-select
          v-model="form.department_id"
          :items="options.departments"
          item-text="title"
          item-value="id"
          placeholder="Seleccionar"
          hide-details
          outlined
          dense
        ></v-select>
        <p class="text-14 mb-1">
          Vendedor Sugerido
        </p>
        <v-autocomplete
          v-model="form.attended_by"
          :disabled="availableSeller"
          :items="options.sellers"
          item-text="name"
          item-value="id"
          placeholder="Seleccionar"
          hide-details
          outlined
          dense
        ></v-autocomplete>
      </v-col>
      <v-col cols="12" md="6">
        <p class="text-14 mb-1">
          Descripcion del Segumiento
        </p>
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
        <v-card-title>Registrar Nuevo Prospecto</v-card-title>
        <v-card-text>
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
// import Categories from "@admin/sales/tracking/resources/categories.json";
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
      options: {
        prospects: [],
        agencies: [],
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
  },
  methods: {
    async loadOptions() {
      const _this = this;
      await axios
        .get("/admin/tracking/sales_history/resources")
        .then(function (response) {
          let {
            agencies,
            departments,
            prospects,
            currency,
            categories,
          } = response.data.data;
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
    customFilter(item, queryText, itemText) {
      const textName = item.full_name.toLowerCase();
      const textPhone = item.phone.toLowerCase();
      const searchText = queryText.toLowerCase();

      return (
        textName.indexOf(searchText) > -1 || textPhone.indexOf(searchText) > -1
      );
    },
  },
};
</script>
<style></style>
