<template>
  <v-row class="grey lighten-5 ma-2">
    <v-col cols="12" md="6" class="py-0">
      <v-autocomplete
        v-model="form.prospect_id"
        :items="options.prospects"
        item-text="full_name"
        item-value="id"
        label="BUSCAR PROSPECTO:"
        placeholder="Buscar por Nombre:"
        clearable
        outlined
        dense
        :rules="rules"
        eager
      >
        <template v-slot:prepend-inner>
          <v-tooltip top>
            <template v-slot:activator="{ on, attrs }">
              <v-icon
                key="icon-add"
                color="success"
                @click="$router.push({ name: 'prospect.create' })"
                v-bind="attrs"
                v-on="on"
                >mdi-account-plus
              </v-icon>
            </template>
            <span>Registrar Nuevo</span>
          </v-tooltip>
        </template>
        <template v-slot:item="data">
          <template>
            <v-list-item-content>
              <v-list-item-title
                v-text="`${data.item.full_name}, Tel:${data.item.phone}`"
              />
              <v-list-item-subtitle
                v-text="`${data.item.company || ''} ${data.item.rfc || ''}`"
              />
              <v-list-item-subtitle
                v-text="
                  `${data.item.email || ''} | ${
                    data.item.township ? data.item.township.name : ''
                  }, ${
                    data.item.township ? data.item.township.estate.name : ''
                  } ${data.item.town || ''}`
                "
              />
            </v-list-item-content>
          </template>
        </template>
      </v-autocomplete>
      <v-autocomplete
        v-model="form.agency_id"
        :items="options.agencies"
        item-text="title"
        item-value="id"
        label="AGENCIA ASIGNADA:"
        placeholder="Agencia a quien correponde."
        outlined
        dense
        :rules="rules"
      ></v-autocomplete>
      <v-autocomplete
        v-model="form.department_id"
        :items="options.departments"
        item-text="title"
        item-value="id"
        label="CORRESPONDE A:"
        placeholder="Departamento a quien correponde."
        outlined
        dense
        :rules="rules"
      ></v-autocomplete>
      <v-autocomplete
        v-model="form.attended_by"
        :disabled="availableSeller"
        :items="options.sellers"
        item-text="name"
        item-value="id"
        label="VENDEDOR SUGERIDO:"
        placeholder="Vendedores disponibles:"
        outlined
        dense
        :rules="rules"
      >
        <template v-slot:item="data">
          <v-list-item-avatar>
            <v-icon
              v-if="data.item.groups.some((g) => g.name == 'Gerente')"
              class="green--text"
              >mdi-check-circle-outline</v-icon
            >
            <v-icon v-else class="grey--text">mdi-alert-circle-outline</v-icon>
          </v-list-item-avatar>
          <v-list-item-content>
            <v-list-item-title v-html="data.item.name"></v-list-item-title>
          </v-list-item-content>
        </template>
      </v-autocomplete>
    </v-col>
    <v-col cols="12" md="6" class="py-0">
      <v-select
        v-model="form.title"
        :items="options.categories"
        label="CATEGORIA INTERES:"
        placeholder="describir o seleccionar un opcion"
        filled
        outlined
        dense
        :rules="rules"
      ></v-select>
      <v-text-field
        v-if="form.title != 'Tractores' || editing"
        v-model="form.reference"
        label="REFERENCIA:"
        placeholder="Alguna Referencia del Producto de interes"
        filled
        outlined
        dense
        clearable
        class="title"
        :rules="rules"
      ></v-text-field>
      <v-row v-else-if="!editting" no-gutters>
        <v-col cols="4">
          <v-autocomplete
            v-model="selectModel"
            :items="models"
            label="Modelo:"
            placeholder="Seleccione Modelo"
            required
            outlined
            filled
            dense
            clearable
            :rules="[(v) => (!!v && v.length > 0) || 'Campo Requerido']"
          ></v-autocomplete
        ></v-col>
        <v-col cols="8">
          <v-select
            v-model="selectConfig"
            :items="TractorConfigurations"
            label="Configuracion:"
            :hint="selectConfig.hint"
            persistent-hint
            filled
            outlined
            required
            dense
            prepend-icon="mdi-cogs"
            return-object
            :rules="[(v) => !!v.text || 'Campo Requerido']"
          ></v-select
        ></v-col>
      </v-row>
      <v-row align="center" wrap>
        <v-col cols="12" md="6">
          <v-text-field
            v-model="form.price"
            label="PRECIO A TRATAR:"
            placeholder="0.00"
            filled
            outlined
            dense
            reverse
            class="title"
            type="Number"
            append-icon="mdi-currency-usd"
            :rules="rules"
          >
            <template v-slot:prepend-inner>
              <v-btn text color="success" @click="changeCurrency()">
                {{ form.currency }}
              </v-btn>
            </template>
          </v-text-field>
        </v-col>
        <v-col cols="12" md="6">
          <v-select
            v-model="form.tracking_condition"
            :items="options.condition"
            label="CONDICION del Seguimiento:"
            filled
            outlined
            dense
            class="overline"
            :rules="rules"
          >
          </v-select>
        </v-col>
      </v-row>
    </v-col>
    <v-col cols="12" class="py-0">
      <div class="d-flex d-flex-row align-center overline mx-3">
        <span>El Prospecto fue contactado:</span>
        <v-radio-group v-model="form.first_contact" row dense :rules="rules">
          <v-radio label="ONLINE" value="online"></v-radio>
          <v-radio label="EN AGENCIA" value="agencia"></v-radio>
          <v-radio label="EN CAMPO" value="campo"></v-radio>
        </v-radio-group>
      </div>
      <v-textarea
        v-model="form.description_topic"
        label="MOTIVO:"
        placeholder="Motivo del Seguimiento, detalle producto, precios, etc"
        counter="500"
        filled
        outlined
        class="title"
        :rules="[
          (v) => !!v || 'Motivo Requerido',
          (v) =>
            (v && v.length >= 25) || 'Motivo debe tener al menos 25 caracteres',
        ]"
      />
    </v-col>
  </v-row>
</template>

<script>
import Tractors from '@admin/tracking/tracking/resources/agricola.json';
import Categories from '@admin/tracking/tracking/resources/categories.json';
export default {
  name: 'TrackingForm',
  props: {
    form: {
      required: true,
      type: Object,
    },
    editing: {
      required: false,
      type: Boolean,
      default: false,
    },
    propProspectId: {
      required: false,
      type: String,
    },
  },
  mounted() {
    this.loadResources(() => {
      if (this.$gate.deny('assignSeller', 'tracking')) {
        this.form.agency_id = window.LSK_APP.AUTH_USER.agency_id;
        this.form.department_id = window.LSK_APP.AUTH_USER.department_id;
      }
    });
  },
  data() {
    return {
      rules: [(v) => !!v || 'Campo Requerido'],
      selectModel: null,
      selectConfig: { hint: '' },
      options: {
        categories: Categories,
        prospects: [],
        agencies: [],
        departments: [],
        sellers: [],
        condition: ['Contado', 'Financiamiento', 'Renta'],
        tractors: Tractors,
      },
    };
  },
  watch: {
    'form.agency_id': function(v) {
      if (!!this.form.department_id && v) this.loadSellers(() => {});
    },
    'form.department_id': function(v) {
      if (!!this.form.agency_id && v) this.loadSellers(() => {});
    },
    selectModel: function() {
      this.form.reference = null;
      this.form.price = null;
      this.selectConfig = { hint: '' };
    },
    selectConfig: function({ text, price, currency }) {
      this.form.reference = text;
      this.form.price = price;
      this.form.currency = currency;
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
    tipos() {
      return [...new Set(this.options.tractors.map((item) => item.Tipo))];
    },
    models() {
      const filter = this.options.tractors.filter(
        (word) => word.Tipo === 'Tractor'
      );
      return [...new Set(filter.map((item) => `${item.Modelo}`))];
    },
    TractorConfigurations() {
      const result = [];
      const map = new Map();
      const filter = this.options.tractors.filter(
        (item) => `${item.Modelo}` === this.selectModel
      );
      for (const item of filter) {
        if (!map.has(this.configuracion(item))) {
          map.set(this.configuracion(item), true); // set any value to Map
          result.push({
            text: this.configuracion(item).trim(),
            price: item.Precio,
            currency: item['Tipo Moneda'],
            hint: `${item.Modelo} ${item.field4} ${
              item['Traccion Sencilla'] === 'SI' ? 'Traccion Sencilla | ' : ''
            } ${item['Doble Traccion'] === 'SI' ? 'Doble Traccion | ' : ''} ${
              item['Power Reverser'] === 'SI' ? 'Power Reverser | ' : ''
            } ${item.Creeper === 'SI' ? 'Creeper | ' : ''} ${
              item['Con Cabina'] === 'SI' ? 'Con Cabina | ' : ''
            } ${item['Cabina Premium'] === 'SI' ? 'Cabina Premium | ' : ''} ${
              item['Doble rodado'] === 'SI' ? 'Doble rodado | ' : ''
            } ${item.Ams === 'SI' ? 'AMS Incluido | ' : ''}`.trim(),
          });
        }
      }
      return result;
    },
  },
  methods: {
    loadResources(cb) {
      const self = this;
      axios
        .get('/admin/tracking/sales_history/resources')
        .then(function(response) {
          let Data = response.data.data;
          self.options.agencies = Data.agencies;
          self.options.departments = Data.departments;
          self.options.prospects = Data.prospects;
          (cb || Function)();
        });
    },
    loadSellers(cb) {
      const self = this;
      if (!this.editing) self.form.attended_by = window.LSK_APP.AUTH_USER.id;
      let params = {
        seller_agency_id: self.form.agency_id,
        seller_type_id: self.form.department_id,
        paginate: 'no',
      };
      self.$store.commit('showLoader');
      axios.get('/admin/sellers', { params: params }).then(function(response) {
        self.options.sellers = response.data.data;
        self.$store.commit('hideLoader');
      });
    },
    changeCurrency() {
      if (this.form.currency == 'MXN') {
        this.form.currency = 'USD';
      } else {
        this.form.currency = 'MXN';
      }
    },
    configuracion(item) {
      return `${item.Modelo} ${item.field4} ${
        item['Traccion Sencilla'] === 'SI' ? 'TS' : ''
      } ${item['Doble Traccion'] === 'SI' ? 'DT' : ''} ${
        item['Power Reverser'] === 'SI' ? 'PR' : ''
      } ${item['Creeper'] === 'SI' ? 'Creeper' : ''} ${
        item['Con Cabina'] === 'SI' ? 'c/CAB' : ''
      } ${item['Cabina Premium'] === 'SI' ? 'CAB/Premium' : ''} ${
        item['Doble rodado'] === 'SI' ? 'DR' : ''
      } ${item['Ams'] === 'SI' ? 'AMS Incluido' : ''}`;
    },
  },
};
</script>

<style></style>
