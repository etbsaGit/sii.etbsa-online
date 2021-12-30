<template>
  <v-card flat color="grey lighten-3">
    <v-card-text class="pt-0">
      <v-row v-if="!sample" dense>
        <v-col cols="12" class="py-0">
          <v-checkbox
            v-model="hasHeaders"
            id="checkbox"
            label="El Archivo tiene Encabezado"
          ></v-checkbox>
        </v-col>
        <v-col cols="12" class="py-0">
          <v-file-input
            v-model="file"
            outlined
            label="Seleccionar Archivo CSV"
            hide-details
            dense
          ></v-file-input>
        </v-col>
        <v-col cols="12">
          <v-btn class="mt-4" @click.prevent="getSample" type="submit" block>
            {{ loadBtnText }}
          </v-btn>
        </v-col>
      </v-row>
      <v-row v-if="sample" dense>
        <v-col cols="12">
          <v-simple-table
            dense
            class="elevation-2 caption text-uppercase mt-4 pa-2"
          >
            <template v-slot:default>
              <thead>
                <tr>
                  <th>Campo</th>
                  <th>Columna Csv</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="(field, key) in fieldsToMap" :key="key">
                  <td>{{ field.label }}</td>
                  <td class="py-1">
                    <select
                      v-model="map[field.key]"
                      class="overline"
                      style="
                        border-style: groove;
                        width: 100%;
                        background: lightgrey;
                        padding-left: 1em;
                      "
                    >
                      <option :value="null" class="overline">
                        (Vacio)
                      </option>
                      <option
                        v-for="(column, key) in firstRow"
                        :key="key"
                        :value="key"
                        class="overline"
                      >
                        {{ column }}
                      </option>
                    </select>
                  </td>
                </tr>
              </tbody>
            </template>
          </v-simple-table>
        </v-col>
        <v-col cols="12">
          <v-btn class="mt-4" type="submit" @click.prevent="submit" block>
            {{ submitBtnText }}
          </v-btn>
        </v-col>
      </v-row>
    </v-card-text>
  </v-card>
</template>

<script>
export default {
  name: "ImportCsvComponet",
  props: {
    value: Array,
    url: {
      type: String,
    },
    mapFields: {
      required: true,
    },
    callback: {
      type: Function,
      default: () => {},
    },
    catch: {
      type: Function,
      default: () => {},
    },
    finally: {
      type: Function,
      default: () => {},
    },
    loadBtnText: {
      type: String,
      default: "Cargar",
    },
    submitBtnText: {
      type: String,
      default: "Importar",
    },
  },
  data: () => ({
    form: {
      csv: null,
    },
    file: null,
    fieldsToMap: [],
    map: {},
    hasHeaders: true,
    csv: null,
    sample: null,
  }),

  created() {
    if (_.isArray(this.mapFields)) {
      this.fieldsToMap = _.map(this.mapFields, (item) => {
        return {
          key: item.key,
          label: item.label,
        };
      });
    } else {
      this.fieldsToMap = _.map(this.mapFields, (label, key) => {
        return {
          key: key,
          label: label,
        };
      });
    }
  },

  methods: {
    submit() {
      const _this = this;
      _this.form.csv = _this.buildMappedCsv();
      _this.$emit("input", _this.form.csv);

      if (_this.url) {
        axios
          .post(_this.url, _this.form)
          .then((response) => {
            _this.callback(response);
          })
          .catch((response) => {
            _this.catch(response);
          })
          .finally((response) => {
            _this.finally(response);
            _this.sample = null;
          });
      }
    },
    buildMappedCsv() {
      const _this = this;
      let csv = _this.hasHeaders ? _.drop(_this.csv) : _this.csv;
      return _.map(csv, (row) => {
        let newRow = {};
        _.forEach(_this.map, (column, field) => {
          _.set(newRow, field, _.get(row, column));
        });
        return newRow;
      });
    },
    getSample() {
      const _this = this;

      this.readFile((output) => {
        _this.sample = _.get(Papa.parse(output, { preview: 2 }), "data");
        _this.csv = _.get(Papa.parse(output), "data");
      });
    },
    readFile(callback) {
      if (this.file) {
        let reader = new FileReader();
        reader.readAsText(this.file, "UTF-8");
        reader.onload = function (e) {
          callback(e.target.result);
        };
        reader.onerror = function () {};
      }
    },
  },
  watch: {
    map: {
      handler: function (newVal) {
        if (!this.url) {
          var hasAllKeys = this.mapFields.every(function (item) {
            return newVal.hasOwnProperty(item);
          });

          if (hasAllKeys) {
            this.submit();
          }
        }
      },
      deep: true,
    },
  },
  computed: {
    firstRow() {
      return _.get(this, "sample.0");
    },
  },
};
</script>
