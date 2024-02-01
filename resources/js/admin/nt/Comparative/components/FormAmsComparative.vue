<template>
  <div>
    <v-form
      ref="formCustomer"
      v-model="valid"
      lazy-validation
      class="multi-col-validation"
    >
      <v-row dense>
        <v-col cols="12" md="6">
          <v-text-field
            v-model="form.customer_fullname"
            label="Nombre Completo"
            :rules="rules.nameRules"
            outlined
            dense
            placeholder="Nombre"
          ></v-text-field>
        </v-col>

        <v-col cols="12" md="6">
          <v-text-field
            v-model="form.customer_phone"
            label="Telefono"
            :rules="rules.phoneRules"
            outlined
            dense
            placeholder="10 Digitos"
          ></v-text-field>
        </v-col>

        <v-col cols="12" md="6">
          <v-text-field
            v-model="form.customer_email"
            label="Email"
            :rules="rules.emailRules"
            outlined
            dense
            placeholder="Email"
          ></v-text-field>
        </v-col>

        <v-col cols="12" md="6">
          <v-text-field
            v-model="form.customer_city"
            label="Ciudad o Municipio"
            :rules="[(v) => !!v || 'Requerido']"
            outlined
            dense
            placeholder="Ciudad o Municipio"
          ></v-text-field>
        </v-col>

        <v-col cols="12" md="6">
          <v-text-field
            v-model="form.customer_country"
            label="Estado"
            :rules="[(v) => !!v || 'Requerido']"
            outlined
            dense
            placeholder="Estado"
          ></v-text-field>
        </v-col>

        <v-col cols="12" md="6">
          <v-text-field
            v-model="form.customer_company"
            label="Compañia (opcional)"
            outlined
            dense
          ></v-text-field>
        </v-col>
      </v-row>
    </v-form>
    <v-row>
      <v-col cols="12" md="6">
        <v-card color="grey lighten-4">
          <v-card-title>Datos Adicionales</v-card-title>
          <v-card-text>
            <v-form
              ref="form"
              v-model="valid"
              lazy-validation
              class="multi-col-validation"
            >
              <v-row>
                <v-col cols="12" sm="12">
                  <v-text-field
                    v-model="form.grooves"
                    label="Surcada"
                    type="number"
                    hint="A cuanto siembra entre surco y surco"
                    :rules="[(v) => !!v || 'Requerido']"
                    persistent-hint
                    outlined
                    dense
                    placeholder="Metros"
                    suffix="Mts"
                  ></v-text-field>
                </v-col>
                <v-col cols="12" sm="6">
                  <v-text-field
                    v-model="form.tractor_value"
                    label="Valor del Tractor"
                    type="number"
                    outlined
                    dense
                    placeholder="Valor"
                    prefix="$"
                    suffix="MXN"
                    hide-details
                  ></v-text-field>
                </v-col>
                <v-col cols="12" sm="6">
                  <v-text-field
                    v-model="form.tractor_potencia"
                    label="Potencia Tractor"
                    type="number"
                    outlined
                    dense
                    placeholder="Caballos de Potencia"
                    persistent-placeholder
                    suffix="HP"
                    hide-details
                  ></v-text-field>
                </v-col>
                <v-col cols="12" sm="6">
                  <v-text-field
                    v-model="form.diesel_prepare"
                    label="Diesel"
                    type="number"
                    hint="Cuanto gasta por dia de diesel preparado-litros"
                    persistent-hint
                    outlined
                    dense
                    placeholder="Litros"
                    suffix="Lts"
                  ></v-text-field>
                  <v-text-field
                    v-model="form.diesel_work"
                    label="Diesel"
                    type="number"
                    hint="Cuanto gasta por dia de diesel trabajo ligero-litros"
                    persistent-hint
                    outlined
                    dense
                    placeholder="Litros"
                    suffix="Lts"
                  ></v-text-field>
                </v-col>
                <v-col cols="12" md="6" class="text-center">
                  <p class="title">Precio Diesel</p>
                  <v-edit-dialog
                    :return-value.sync="form.diesel_price"
                    large
                    persistent
                    save-text="Cambiar"
                  >
                    <span class="text-h3">
                      {{ form.diesel_price | money }} MXN
                      <v-icon right color="primary">mdi-pencil</v-icon>
                    </span>
                    <template v-slot:input>
                      <div class="mt-4 title">
                        Indicar Precio Diesel del dia
                      </div>
                      <v-text-field v-model="form.diesel_price" type="number">
                      </v-text-field>
                    </template>
                  </v-edit-dialog>
                </v-col>
                <v-col cols="12" sm="6">
                  <v-text-field
                    v-model="form.hectares"
                    label="Hectarias"
                    type="number"
                    outlined
                    dense
                    placeholder="HA"
                    suffix="HAs"
                    hide-details
                  ></v-text-field>
                </v-col>
                <v-col cols="12" sm="6">
                  <v-text-field
                    v-model="form.cycles"
                    label="Numero de Ciclos"
                    type="number"
                    outlined
                    dense
                    placeholder="Ciclos"
                    suffix="Ciclos"
                    hide-details
                  ></v-text-field>
                </v-col>
                <v-col cols="12" sm="6">
                  <v-text-field
                    v-model="form.ams_value"
                    label="Valor del AMS"
                    type="number"
                    outlined
                    dense
                    placeholder="Precio"
                    prefix="$"
                    suffix="MXN"
                    hide-details
                  ></v-text-field>
                </v-col>
              </v-row>
            </v-form>
          </v-card-text>
        </v-card>
      </v-col>
      <v-col cols="12" md="6">
        <v-card color="grey lighten-4">
          <v-card-title>Agregar Equipos a Comparar</v-card-title>
          <v-card-text>
            <v-form v-model="validAddEquip" ref="formAddEquip" lazy-validation>
              <v-row dense>
                <v-col cols="12" md="6">
                  <v-select
                    v-model="equipment.category"
                    label="Seleccionar un Equipo"
                    :items="equipment_types"
                    outlined
                    dense
                  ></v-select>
                </v-col>
                <v-col cols="12" md="6">
                  <v-select
                    v-model="equipment"
                    :items="equipment_list"
                    item-value="name"
                    item-text="name"
                    label="Modelo"
                    return-object
                    outlined
                    dense
                  ></v-select>
                </v-col>
                <v-col cols="12" md="6">
                  <v-text-field
                    v-model="equipment.unit"
                    label="Unidad de medida"
                    :items="units"
                    readonly
                    outlined
                    dense
                  ></v-text-field>
                </v-col>
                <v-col cols="12" md="6">
                  <v-text-field
                    v-model="equipment.value"
                    label="Cantidad"
                    type="number"
                    outlined
                    dense
                  ></v-text-field>
                </v-col>
                <v-col cols="12" md="6">
                  <v-text-field
                    v-model="equipment.price"
                    label="Precio"
                    type="number"
                    prefix="$"
                    suffix="MXN"
                    outlined
                    dense
                  ></v-text-field>
                </v-col>
                <v-col cols="12" md="6">
                  <v-text-field
                    v-model="equipment.steps"
                    label="Numero de Pasadas"
                    type="number"
                    :rules="[(v) => !!v || 'Es Requerido numero de Pasadas']"
                    outlined
                    dense
                  ></v-text-field>
                </v-col>
                <v-col cols="12">
                  <v-btn color="primary" block dark @click="addEquipment">
                    Agregar <v-icon right>mdi-plus-thick</v-icon>
                  </v-btn>
                </v-col>
              </v-row>
            </v-form>

            <v-simple-table class="mt-2">
              <template v-slot:default>
                <thead>
                  <tr>
                    <th class="text-left">
                      Equipo
                    </th>
                    <th class="text-left">
                      Nombre
                    </th>
                    <!-- <th class="text-left">
                      Unidad Medida
                    </th> -->
                    <th class="text-left">
                      Parametro
                    </th>
                    <th class="text-left">
                      Precio
                    </th>
                    <th class="text-left">
                      Pasadas
                    </th>
                    <th></th>
                  </tr>
                </thead>
                <tbody class="overline">
                  <tr v-for="(item, index) in form.equipments" :key="index">
                    <td>{{ item.category }}</td>
                    <td>{{ item.name }}</td>
                    <td>{{ item.value }} {{ item.unit }}</td>
                    <td>{{ item.price | money }}</td>
                    <td>{{ item.steps }}</td>
                    <td class="text-right" style="width: 50px;">
                      <v-icon color="red" small @click="deleteEquipment(index)">
                        mdi-delete
                      </v-icon>
                    </td>
                  </tr>
                </tbody>
              </template>
            </v-simple-table>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>
  </div>
</template>

<script>
export default {
  props: {
    form: {
      require: true,
      type: Object,
      default: () => {
        return {};
      },
    },
  },
  data() {
    const defaultItem = Object.freeze({
      category: "",
      name: "",
      value: 0,
      unit: "",
      price: 0,
      steps: 0,
    });
    return {
      valid: true,
      validAddEquip: true,
      rules: {
        nameRules: [
          (v) => !!v || "Nombre es Requerido",
          (v) =>
            (v && v.length >= 10) || "Nombre debe tener al menos 10 Caracteres",
        ],
        phoneRules: [
          (v) =>
            (v && v.length >= 10) || "Telefono debe tener al menos 10 Digitos",
        ],
        emailRules: [
          (v) => !!v || "E-mail es Requerido",
          (v) => /.+@.+\..+/.test(v) || "E-mail debe ser valido",
        ],
      },
      amsPreview: false,
      tableWithoutAms: [],
      tableWithAms: [],
      tableDiffAms: [],
      tableSaveAms: [],

      equipment_list: [],
      equipment_types: [
        "Arado",
        "Rastra",
        "Cultivadora",
        "Niveladora",
        "Sembradora",
        "Fumigadora",
      ],
      units: [
        "Discos",
        "Surcos",
        "Timones",
        "Cuchillas",
        "Cuerpo",
        "Aguilon",
        "Metros",
      ],
      equipment: Object.assign({}, defaultItem),
      indexitem: -1,
    };
  },
  watch: {
    "equipment.category": {
      handler: _.debounce(function (v) {
        if (v) this.getEquipment(v);
      }, 700),
      // deep: true,
    },
  },
  methods: {
    addEquipment() {
      const _this = this;
      if (!_this.$refs.formAddEquip.validate()) return;
      _this.form.equipments.push(_this.equipment);
      _this.equipment = Object.assign({}, _this.defaulItem);
      _this.$refs.formAddEquip.resetValidation();
    },
    async submit() {
      // if (!!this.$refs.form.validate()) return false;
      const _this = this;
      _this.$emit("submit");
    },
    deleteEquipment(index) {
      this.form.equipments.splice(index, 1);
    },
    preview() {
      const _this = this;

      // axios({
      //   method: "post",
      //   url: "/admin/ams-comparative/preview",
      //   params: _this.form,
      //   responseType: "blob",
      // })
      //   .then(function (response) {
      //     _this.downloadFile(response, "AMS");
      //     _this.$store.commit("hideLoader");
      //   })
      //   .catch((error) => {
      //     _this.$store.commit("hideLoader");
      //     if (error.response) {
      //       _this.$store.commit("showSnackbar", {
      //         message: error.response.data.message,
      //         color: "error",
      //         duration: 3000,
      //       });
      //     } else if (error.request) {
      //       console.log(error.request);
      //     } else {
      //       console.log("Error", error.message);
      //     }
      //   });

      axios
        .post("/admin/ams-comparative/preview", _this.form)
        .then(function (response) {
          _this.$store.commit("showSnackbar", {
            message: response.data.message,
            color: "success",
            duration: 3000,
          });
          _this.tableWithoutAms = response.data.data.table_without_ams;
          _this.tableWithAms = response.data.data.table_with_ams;
          _this.tableDiffAms = response.data.data.table_diff;
          _this.tableSaveAms = response.data.data.table_save;
          _this.amsPreview = true;
        })
        .catch(function (error) {
          if (error.response) {
            _this.$store.commit("showSnackbar", {
              message: error.response.data.message,
              color: "error",
              duration: 3000,
            });
            _this.amsPreview = false;
          } else if (error.request) {
            console.log(error.request);
          } else {
            console.log("Error", error.message);
          }
        });
    },
    downloadFile(response, filename = null) {
      // It is necessary to create a new blob object with mime-type explicitly set
      // otherwise only Chrome works like it should
      let newBlob = new Blob([response.data], {
        type: "application/pdf",
      });
      // IE doesn't allow using a blob object directly as link href
      // instead it is necessary to use msSaveOrOpenBlob
      if (window.navigator && window.navigator.msSaveOrOpenBlob) {
        window.navigator.msSaveOrOpenBlob(newBlob);
        return;
      }
      // For other browsers:
      // Create a link pointing to the ObjectURL containing the blob.
      // const data = window.URL.createObjectURL(newBlob);
      const data = window.URL.createObjectURL(response.data);
      if (filename) {
        let link = document.createElement("a");
        link.href = data;
        link.target = "_blank";
        link.download = filename + "_" + moment().format("L") + ".pdf";
        link.click();
      } else {
        window.open(data, "_blank");
      }
      setTimeout(function () {
        // For Firefox it is necessary to delay revoking the ObjectURL
        window.URL.revokeObjectURL(data);
      }, 100);
    },
    print() {
      window.print();
    },
    async getEquipment(category) {
      const _this = this;
      console.log(category, "get equip category");
      let params = {
        category: category,
        paginate: "no",
      };

      await axios.get("/admin/ams-equipment", { params }).then((response) => {
        _this.equipment_list = response.data.data;
      });
    },
  },
};
</script>
