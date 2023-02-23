<template>
  <v-card flat>
    <v-data-table
      :headers="headers"
      :items="items"
      :items-per-page="-1"
      fixed-header
      hide-default-footer
      mobile-breakpoint="0"
      dense
    >
      <template v-slot:[`item.name`]="{ item }">
        <v-list-item dense class="pa-0">
          <v-list-item-content>
            <v-list-item-title v-text="item.name" />
            <v-list-item-subtitle
              v-text="item.description"
              class="text-truncate"
              style="max-width: 300px;"
            />
          </v-list-item-content>
        </v-list-item>
      </template>
      <template v-slot:[`item.unit`]="{ value }">
        <span class="font-weight-medium">
          {{ `${value.name} - ${value.clave}` }}
        </span>
      </template>
      <template v-slot:[`item.price`]="{ value }">
        <span class="font-weight-medium">{{ value | money }}</span>
      </template>
      <template v-slot:[`item.qty`]="{ item, value }">
        <span v-if="readOnly">{{ value }}</span>
        <v-edit-dialog
          v-else
          :return-value.sync="item.qty"
          large
          persistent
          @save="save"
          @cancel="cancel"
          @open="open"
          @close="close"
          save-text="Guardar"
          cancel-text="Cancelar"
        >
          <v-text-field
            readonly
            outlined
            dense
            :value="item.qty"
            style="max-width: 50px;"
            hide-details
            reverse
            single-line
            class="py-1"
          ></v-text-field>
          <template v-slot:input>
            <div class="mt-4 text-h6">
              Actualizar Cantidad
            </div>
            <v-text-field
              v-model="item.qty"
              label="Edit"
              hide-details
              single-line
              counter
              autofocus
              outlined
              type="number"
            ></v-text-field>
          </template>
        </v-edit-dialog>
      </template>
      <template v-slot:[`item.discount`]="{ item, value }">
        <span v-if="readOnly">{{ value || 0 | money }}</span>
        <v-edit-dialog
          v-else
          :return-value.sync="item.discount"
          large
          persistent
          @save="save"
          @cancel="cancel"
          @open="open"
          @close="close"
          save-text="Guardar"
          cancel-text="Cancelar"
        >
          <v-text-field
            readonly
            outlined
            dense
            :value="item.discount"
            style="max-width: 100px;"
            suffix="$"
            hide-details
            single-line
            reverse
            class="py-1"
          ></v-text-field>
          <template v-slot:input>
            <div class="mt-4 text-h6">
              Modificar Descuento
            </div>
            <v-text-field
              v-model="item.discount"
              label="Edit"
              reverse
              suffix="$"
              single-line
              hide-details
              counter
              autofocus
              outlined
              type="number"
            ></v-text-field>
          </template>
        </v-edit-dialog>
      </template>
      <template v-slot:[`item.total`]="{ item }">
        {{ Amount(item) | money }}
      </template>
      <template v-slot:[`item.accion`]="{ item }">
        <div v-if="!readOnly" class="d-flex flex-row">
          <v-icon small left color="blue" @click="editItem(item)">
            mdi-pencil
          </v-icon>
          <v-icon small righ color="red" @click="deleteItem(item)">
            mdi-trash-can
          </v-icon>
        </div>
      </template>
    </v-data-table>
    <v-dialog v-model="Dialog" max-width="800">
      <v-card flat width="800">
        <v-card-title>
          <span class="text-h5">{{ Title }} Articulo(s) y/o Servicio(s)</span>
        </v-card-title>
        <v-card-text>
          <v-container>
            <v-form v-model="valid" ref="form" lazy-validation>
              <v-row class="overline">
                <v-col cols="12">
                  <v-select
                    v-model="form.group"
                    label="Grupo del Articulo*"
                    :items="ConceptProductProp"
                    item-text="name"
                    item-value="id"
                    :rules="[(v) => !!v || 'Valor Requerido']"
                    return-object
                    outlined
                    dense
                  ></v-select>
                </v-col>
                <v-col cols="12">
                  <v-textarea
                    v-model="form.description"
                    label="Descripcion"
                    :rules="[(v) => !!v || 'Valor Requerido']"
                    hide-details
                    outlined
                    dense
                  ></v-textarea>
                </v-col>
                <v-col cols="4">
                  <v-autocomplete
                    v-model.number="form.unit"
                    label="Clave Unidad*"
                    :items="options.cat_unit"
                    item-text="name"
                    item-value="clave"
                    :rules="[(v) => !!v || 'Item is required']"
                    return-object
                    :filter="customFilter"
                    outlined
                    dense
                  >
                    <template v-slot:item="{ item }">
                      <v-list-item-content>
                        <v-list-item-title v-text="item.type" />
                        <v-list-item-subtitle v-text="item.name" />
                        <v-list-item-subtitle v-text="item.clave" />
                      </v-list-item-content>
                    </template>
                  </v-autocomplete>
                </v-col>
                <v-col cols="4">
                  <v-text-field
                    v-model.number="form.price"
                    label="Precio Unitario*"
                    type="number"
                    :rules="[(v) => !!v || 'Valor Requerido']"
                    prefix="$"
                    outlined
                    dense
                  ></v-text-field>
                </v-col>
                <v-col cols="4">
                  <v-text-field
                    v-model.number="form.discount"
                    label="Descuento"
                    type="number"
                    prefix="$"
                    clearable
                    outlined
                    dense
                  ></v-text-field>
                </v-col>
              </v-row>
            </v-form>
          </v-container>
          <small>*indicates required field</small>
        </v-card-text>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn color="blue darken-1" text @click="Dialog = false">
            Cerrar
          </v-btn>
          <v-btn color="blue darken-1" text @click="saveItem">
            {{ Title }}
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
    <!-- <v-snackbar v-model="snack" :timeout="3000" :color="snackColor">
      {{ snackText }}

      <template v-slot:action="{ attrs }">
        <v-btn v-bind="attrs" text @click="snack = false">
          Close
        </v-btn>
      </template>
    </v-snackbar> -->
  </v-card>
</template>
<script>
import SupplierProductList from "../supplierProducts/SupplierProductList.vue";
export default {
  name: "PurchaceSupplierProductsTable",
  components: { SupplierProductList },
  props: {
    SupplierIdProp: {
      require: false,
      type: Number | String,
    },
    ConceptProductProp: {
      require: false,
      type: Array,
    },
    dialogForm: {
      default: false,
    },
    items: {
      type: Array,
      default: () => {
        return [];
      },
    },
    readOnly: {
      type: Boolean,
      default: false,
    },
  },
  data() {
    return {
      valid: true,
      snack: false,
      snackColor: "",
      snackText: "",
      headers: [
        {
          text: "Grupo de Articulo",
          align: "start",
          sortable: false,
          value: "group.name",
          divider: true,
        },
        {
          text: "Descripcion Articulo",
          align: "start",
          sortable: false,
          value: "description",
          divider: true,
        },
        { text: "Clave Unidad", value: "unit", sortable: false },
        { text: "Cantidad", value: "qty", align: "center" },
        { text: "Precio Unitario", value: "price", align: "right" },
        { text: "Descuento", value: "discount", align: "center" },
        { text: "Total", value: "total", sortable: false, align: "right" },
        { value: "accion", sortable: false },
      ],
      editedIndex: -1,
      options: {
        cat_unit: [],
      },
      form: {
        group: {},
        description: "",
        unit: { id: 1, clave: "H87", name: "Pieza" },
        qty: 1,
        price: 0,
        discount: 0,
        subtotal: 0,
      },
      formDefault: {
        group: {},
        description: "",
        unit: { id: 1, clave: "H87", name: "Pieza" },
        qty: 1,
        price: 0,
        discount: 0,
        subtotal: 0,
      },
    };
  },
  computed: {
    Title() {
      return this.editedIndex > -1 ? "Editar" : "Agregar";
    },
    Dialog: {
      get() {
        return this.dialogForm;
      },
      set(v) {
        if (!v) {
          this.$emit("close");
          this.$nextTick(() => {
            this.form = { ...this.formDefault };
            this.editedIndex = -1;
          });
        } else {
          return this.$emit("edit");
        }
      },
    },
  },

  mounted() {
    this.loadOptions();
  },
  methods: {
    async loadOptions(cb) {
      const _this = this;
      await axios
        .get("/admin/purchase-order/resources/options")
        .then(function (response) {
          let { unitSat } = response.data.data;
          _this.options.cat_unit = unitSat;
        });
    },
    Amount(item) {
      return (item.subtotal = item.qty * item.price - item.discount);
    },
    save() {
      this.snack = true;
      this.snackColor = "success";
      this.snackText = "Data saved";
    },
    cancel() {
      this.snack = true;
      this.snackColor = "error";
      this.snackText = "Canceled";
    },
    open() {
      this.snack = true;
      this.snackColor = "info";
      this.snackText = "Dialog opened";
    },
    close() {
      console.log("Dialog closed");
    },
    editItem(item) {
      this.editedIndex = this.items.indexOf(item);
      this.form = Object.assign({}, item);
      this.Dialog = true;
    },
    deleteItem(item) {
      const _this = this;
      _this.$store.commit("showDialog", {
        type: "confirm",
        title: "Confirmacion de eliminacion",
        message: "¿Estás seguro de que quieres eliminar este registro?",
        okCb: () => {
          _this.editedIndex = _this.items.indexOf(item);
          _this.items.splice(_this.editedIndex, 1);
          _this.editedIndex = -1;
        },
        cancelCb: () => {
          _this.editedIndex = -1;
          console.log("CANCEL");
        },
      });
    },
    saveItem() {
      if (this.$refs.form.validate()) {
        if (this.editedIndex > -1) {
          Object.assign(this.items[this.editedIndex], this.form);
        } else {
          this.items.push(this.form);
        }
        this.Dialog = false;
      }
    },
    customFilter(item, queryText, itemText) {
      const textClave = item.clave.toLowerCase();
      const textName = item.name.toLowerCase();
      const textType = item.type.toLowerCase();
      const searchText = queryText.toLowerCase();

      return (
        textClave.indexOf(searchText) > -1 ||
        textName.indexOf(searchText) > -1 ||
        textType.indexOf(searchText) > -1
      );
    },
    unitDescription(unit) {
      const _this = this;
      const array1 = _this.options.cat_unit;
      if (unit) {
        const found = array1.find((e) => {
          e.clave === unit;
        });
        console.log(array1, found, unit);
        // return array;
        // return `${unidad.clave} - ${unidad.name}`;
      }
    },
  },
};
</script>