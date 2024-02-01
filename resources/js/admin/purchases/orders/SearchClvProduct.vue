<template>
  <v-autocomplete
    v-model="Model"
    @input="
      (newVal) => {
        $emit('input', newVal);
      }
    "
    :items="items"
    :loading="isLoading"
    :search-input.sync="search"
    color="blue"
    hide-no-data
    hide-selected
    item-text="Description"
    item-value="c_ClaveProdServ"
    label="Clave Producto"
    placeholder="Buscar por Clave o Palabras"
    prepend-icon="mdi-database-search"
    persistent-placeholder
    :filter="customFilter"
    :rules="[(v) => !!v || 'Seleccione una Clave de Prodroducto']"
    return-object
    clearable
    outlined
  >
    <template v-slot:selection="{ item }">
      <span class="text-uppercase font-weight-bold">
        {{ ` ${item.c_ClaveProdServ} -  ${item.Description}` }}
      </span>
    </template>
    <template v-slot:item="{ item }">
      <v-list-item-content class="pa-0">
        <v-list-item-title class="text-uppercase font-weight-bold">
          {{ ` ${item.c_ClaveProdServ} -  ${item.Descripción}` }}
        </v-list-item-title>
        <v-list-item-subtitle class="text-subtitle-1">
          {{
            `${
              item.Palabrassimilares
                ? item.Palabrassimilares.slice(0, descriptionLimit)
                : ""
            }...`
          }}
        </v-list-item-subtitle>
      </v-list-item-content>
    </template>
  </v-autocomplete>
</template>

<script>
export default {
  props: {
    value: {
      type: Object,
      default: () => {
        return {};
      },
    },
  },
  data() {
    return {
      descriptionLimit: 60,
      entries: [],
      isLoading: false,
      model: null,
      search: null,
    };
  },

  computed: {
    Model: {
      get() {
        return this.value;
      },
      set(value) {
        console.log("setModel", value);
      },
    },
    fields() {
      if (!this.Model) return [];

      return Object.keys(this.Model).map((key) => {
        return {
          key,
          value: this.Model[key] || "n/a",
        };
      });
    },
    items() {
      return this.entries.map((entry) => {
        const Description =
          entry.Descripción.length > this.descriptionLimit
            ? entry.Descripción.slice(0, this.descriptionLimit) + "..."
            : entry.Descripción;

        return Object.assign({}, entry, { Description });
      });
    },
  },

  watch: {
    value: _.debounce(function(nVal, oVal) {
      const _this = this;
      console.log("Watch Value",nVal,oVal);
      if (nVal == null) return;
      if (
        nVal.c_ClaveProdServ != oVal.c_ClaveProdServ &&
        _this.search == null &&
        !_this.entries.some((e) => e.c_ClaveProdServ === nVal.c_ClaveProdServ)
      ) {
        return (_this.search = nVal.c_ClaveProdServ);
      }
      _this.search = null;
    }, 999),
    search: _.debounce(function(val) {
      const _this = this;
      _this.getClaveProdSat();
    }, 999),
  },
  methods: {
    async getClaveProdSat() {
      const _this = this;
      if (_this.search == "" || _this.search == null) return;
      if (_this.isLoading) return;
      _this.isLoading = true;
      let params = {
        search: _this.search,
      };
      try {
        const { data: response } = await axios.get("/admin/claveProdSat", {
          params: params,
        });
        _this.entries = response.data;
      } catch (error) {
        console.log(err);
      } finally {
        _this.isLoading = false;
      }
    },
    customFilter(item, queryText, itemText) {
      const textClaveProd = item.c_ClaveProdServ ?? "";
      const textDescripcion = item.Descripción
        ? item.Descripción.toLowerCase()
        : "";
      const textPalabraSimilares = item.Palabrassimilares
        ? item.Palabrassimilares.toLowerCase()
        : "";
      const searchText = queryText.toLowerCase();

      return (
        textClaveProd.indexOf(searchText) > -1 ||
        textDescripcion.indexOf(searchText) > -1 ||
        textPalabraSimilares.indexOf(searchText) > -1
      );
    },
  },
};
</script>
