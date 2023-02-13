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
      <template v-slot:[`item.price`]="{ value }">
        <span class="font-weight-medium">{{ value | money }}</span>
      </template>
      <template v-slot:[`item.quantity`]="{ item, value }">
        <span v-if="readOnly">{{ value }}</span>
        <v-edit-dialog
          v-else
          :return-value.sync="item.quantity"
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
            :value="item.quantity"
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
              v-model="item.quantity"
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
    <v-dialog v-model="Dialog" max-width="600px">
      <v-card>
        <v-card-title>
          <span class="text-h5">{{ Title }} Articulo o Servicio</span>
        </v-card-title>
        <v-card-text>
          <v-container>
            <v-form v-model="valid" ref="form" lazy-validation>
              <v-row class="overline">
                <v-col cols="8">
                  <v-text-field
                    v-model="form.name"
                    label="Nombre del Articulo*"
                    outlined
                    dense
                  ></v-text-field>
                </v-col>
                <v-col cols="4">
                  <!-- <v-text-field
                    v-model="form.unit"
                    label="Unidad*"
                    outlined
                    dense
                  ></v-text-field> -->
                  <v-autocomplete
                    v-model="form.unit"
                    label="Unidad"
                    outlined
                    dense
                    :items="options.cat_unit"
                    item-text="name"
                    item-value="clave"
                    :filter="customFilter"
                  >
                    <template v-slot:item="data">
                      <v-list-item-content>
                        <v-list-item-title v-text="data.item.type" />
                        <v-list-item-subtitle v-text="data.item.name" />
                        <v-list-item-subtitle v-text="data.item.clave" />
                      </v-list-item-content>
                    </template>
                  </v-autocomplete>
                </v-col>
                <v-col cols="6">
                  <v-text-field
                    v-model="form.price"
                    label="Precio U.*"
                    type="number"
                    prefix="$"
                    outlined
                    dense
                  ></v-text-field>
                </v-col>
                <v-col cols="6">
                  <v-text-field
                    v-model="form.discount"
                    label="Descuento"
                    type="number"
                    prefix="$"
                    clearable
                    outlined
                    dense
                  ></v-text-field>
                </v-col>
                <v-col cols="12">
                  <v-textarea
                    v-model="form.description"
                    label="Descripcion (opcional)"
                    hide-details
                    outlined
                    dense
                  ></v-textarea>
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
    <v-snackbar v-model="snack" :timeout="3000" :color="snackColor">
      {{ snackText }}

      <template v-slot:action="{ attrs }">
        <v-btn v-bind="attrs" text @click="snack = false">
          Close
        </v-btn>
      </template>
    </v-snackbar>
  </v-card>
</template>
<script>
export default {
  name: "PurchaceConceptTable",
  props: {
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
  data() {
    return {
      valid: true,
      snack: false,
      snackColor: "",
      snackText: "",
      headers: [
        {
          text: "Nombre Articulo",
          align: "start",
          sortable: false,
          value: "name",
          divider: true,
        },
        { text: "Cantidad", value: "quantity", align: "center" },
        { text: "Clave Unidad", value: "unit", sortable: false },
        { text: "Precio", value: "price", align: "right" },
        { text: "Descuento", value: "discount", align: "center" },
        { text: "Total", value: "total", sortable: false, align: "right" },
        { value: "accion", sortable: false },
      ],
      editedIndex: -1,
      options: {
        cat_unit: [],
      },
      form: {
        name: "",
        description: "",
        quantity: 1,
        unit: "H87",
        price: 0,
        discount: 0,
      },
      formDefault: {
        name: "",
        description: "",
        quantity: 1,
        unit: "H87",
        price: 0,
        discount: 0,
      },
    };
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
      return item.quantity * item.price - item.discount;
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
      if (this.editedIndex > -1) {
        Object.assign(this.items[this.editedIndex], this.form);
      } else {
        this.items.push(this.form);
      }
      this.Dialog = false;
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
  },
};
</script>
