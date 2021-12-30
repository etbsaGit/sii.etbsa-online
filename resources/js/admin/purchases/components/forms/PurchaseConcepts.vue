<template>
  <v-data-table
    :headers="headers"
    :items="concepts"
    class="blue--text text-uppercase elevation-4 caption"
    hide-default-footer
    mobile-breakpoint="0"
    calculate-widths
    :height="'35vh'"
    fixed-header
    caption
    dense
  >
    <template v-slot:top>
      <v-toolbar flat dense>
        <v-toolbar-title>Conceptos Orden de Compra</v-toolbar-title>
        <v-divider class="mx-4" inset vertical></v-divider>
        <v-spacer></v-spacer>
        <v-dialog
          v-model="dialog"
          scrollable
          max-width="600px"
          :fullscreen="$vuetify.breakpoint.mobile"
        >
          <template v-slot:activator="{ on, attrs }">
            <v-btn color="primary" v-bind="attrs" v-on="on">
              Agregar
            </v-btn>
          </template>
          <v-card>
            <v-card-title class="secondary">
              <span class="headline">{{ formTitle }}</span>
              <v-spacer></v-spacer>
              <v-btn icon color="red darken-1" @click="close">
                <v-icon>mdi-close</v-icon>
              </v-btn>
            </v-card-title>
            <v-divider></v-divider>
            <v-card-text style="height: 300px;">
              <v-form
                v-if="dialog"
                v-model="valid"
                ref="formConcept"
                lazy-validation
              >
                <v-row>
                  <v-col cols="12">
                    <v-text-field
                      v-model="editedItem.name"
                      label="Concepto"
                      placeholder="Escribir nombre o descripcion del producto"
                      counter="100"
                      :rules="[(v) => !!v || 'Es Requerido']"
                    ></v-text-field>
                  </v-col>
                  <v-col cols="6" sm="6" md="4">
                    <v-text-field
                      v-model="editedItem.qty"
                      label="Cantidad"
                      reverse
                      :rules="[(v) => !!v || 'Es Requerido']"
                      type="number"
                    ></v-text-field>
                  </v-col>
                  <v-col cols="6" sm="6" md="4">
                    <v-text-field
                      v-model="editedItem.unit"
                      label="Unidad:"
                      reverse
                      :rules="[(v) => !!v || 'Es Requerido']"
                    ></v-text-field>
                  </v-col>
                  <v-col cols="12" sm="6" md="4">
                    <v-text-field
                      v-model="editedItem.price_unit"
                      label="Precio Unitario:"
                      :rules="[(v) => !!v || 'Es Requerido']"
                      type="number"
                      suffix="$"
                      reverse
                    ></v-text-field>
                  </v-col>
                </v-row>
                <v-list-item>
                  <v-list-item-content class="py-0 overline text-h6">
                    IVA:
                  </v-list-item-content>
                  <v-list-item-action-text class="text-h5 green--text">
                    {{ editedItem.tax | currency }}
                  </v-list-item-action-text>
                </v-list-item>
                <v-list-item>
                  <v-list-item-content class="py-0 overline text-h6">
                    Costo Total:
                  </v-list-item-content>
                  <v-list-item-action-text class="text-h5 green--text">
                    {{ (Total + editedItem.tax) | currency }}
                  </v-list-item-action-text>
                </v-list-item>
                <v-divider></v-divider>
                <div class="mt-2 overline">Con Cargo a:</div>
                <v-row dense>
                  <v-col cols="12" md="6" class="py-0">
                    <v-select
                      v-model="editedItem.department"
                      :items="options.departments"
                      item-text="name"
                      item-value="name"
                      hint="Campo Opcional"
                      persistent-hint
                      label="Departamento*"
                    ></v-select>
                  </v-col>
                  <v-col cols="12" md="6" class="py-0">
                    <v-text-field
                      v-model="editedItem.user"
                      label="Usuario*:"
                      hint="Campo Opcional"
                      persistent-hint
                    ></v-text-field>
                  </v-col>
                </v-row>
              </v-form>
            </v-card-text>
            <v-divider></v-divider>
            <v-card-actions>
              <v-spacer></v-spacer>
              <v-btn color="blue darken-1" text @click="close">
                Cancelar
              </v-btn>
              <v-btn color="blue darken-1" @click="save" :disabled="!valid">
                Guardar
              </v-btn>
            </v-card-actions>
          </v-card>
        </v-dialog>

        <v-dialog v-model="dialogDelete" max-width="500px">
          <v-card>
            <v-card-title class="headline">
              Se Eliminara el Concepto de la Lista...
            </v-card-title>
            <v-card-actions>
              <v-spacer></v-spacer>
              <v-btn color="blue darken-1" @click="closeDelete">
                Cancelar
              </v-btn>
              <v-btn color="blue darken-1" text @click="deleteItemConfirm">
                OK
              </v-btn>
              <v-spacer></v-spacer>
            </v-card-actions>
          </v-card>
        </v-dialog>
      </v-toolbar>
    </template>
    <template v-slot:[`item.actions`]="{ item }">
      <div class="text-truncate">
        <v-icon small class="mr-2" @click="editItem(item)">
          mdi-pencil
        </v-icon>
        <v-icon small @click="deleteItem(item)" color="red darken-5">
          mdi-delete
        </v-icon>
      </div>
    </template>
    <template v-slot:[`item.partida`]="{ item }">
      {{ concepts.indexOf(item) + 1 }}
    </template>
    <template v-slot:[`item.user-depto`]="{ item }">
      <v-list-item dense class="pa-0 caption">
        <v-list-item-content class="pa-0 caption">
          <v-list-item-title>{{ item.department }}</v-list-item-title>
          <v-list-item-subtitle>
            {{ item.user }}
          </v-list-item-subtitle>
        </v-list-item-content>
      </v-list-item>
    </template>
    <template v-slot:[`item.price_unit`]="{ item }">
      {{ item.price_unit | currency }}
    </template>
    <template v-slot:[`item.name`]="{ item }">
      <v-tooltip
        left
        max-width="500"
        :open-on-click="$vuetify.breakpoint.mobile"
        :open-on-hover="!$vuetify.breakpoint.mobile"
      >
        <template v-slot:activator="{ on, attrs }">
          <span
            class="d-inline-block text-truncate caption"
            style="max-width: 290px;"
            v-bind="attrs"
            v-on="on"
          >
            {{ item.name }}
          </span>
        </template>
        <div class="caption text-uppercase">{{ item.name }}</div>
      </v-tooltip>
    </template>
    <template v-slot:[`item.tax`]="{ item }">
      {{ item.tax | currency }}
    </template>
    <template v-slot:[`item.cost`]="{ item }">
      {{ item.cost | currency }}
    </template>

    <template v-slot:no-data>
      <v-row class="pt-6" justify="center">
        <v-btn x-large color="primary" @click="dialog = true">
          Agregar Concepto
        </v-btn>
      </v-row>
    </template>
  </v-data-table>
</template>
<script>
import optionDepartments from "~/api/departments.json";
export default {
  props: {
    concepts: {
      require: true,
      type: Array,
      default: () => {
        return [];
      },
    },
  },
  data() {
    return {
      valid: true,
      dialog: false,
      dialogDelete: false,
      headers: [
        { text: "#", value: "partida", sortable: false },
        { text: "Cant.", value: "qty", align: "center" },
        { text: "Unidad", value: "unit", align: "center", sortable: false },
        {
          text: "Concepto",
          align: "start",
          sortable: false,
          value: "name",
          width: 290,
        },
        { text: "Precio U.", value: "price_unit", align: "end" },
        { text: "IVA", value: "tax", align: "end" },
        { text: "Importe Bruto", value: "cost", align: "end" },
        { text: "Cargo a:", value: "user-depto", sortable: false },
        { value: "actions", sortable: false },
      ],
      options: {
        departments: optionDepartments,
      },
      editedIndex: -1,
      editedItem: {
        user: "",
        deparment: "",
        name: "",
        price_unit: 0,
        qty: 1,
        unit: "pz",
        tax: 0,
        cost: 0,
        total: 0,
      },
      defaultItem: {
        user: "",
        deparment: "",
        name: "",
        price_unit: 0,
        qty: 1,
        unit: "pz",
        tax: 0,
        cost: 0,
        total: 0,
      },
    };
  },

  computed: {
    formTitle() {
      return this.editedIndex === -1 ? "Nuevo Concepto" : "Editar Concepto";
    },
    Tax: {
      get: function () {
        return this.Amount * 0.16;
      },
      set: function (value) {
        this.editedItem.tax = value;
      },
    },
    Amount() {
      return (
        parseFloat(this.editedItem.qty) * parseFloat(this.editedItem.price_unit)
      );
    },
    Total: {
      get: function () {
        return this.Amount;
        // return this.Amount + this.Tax;
      },
      set: function (value) {
        this.editedItem.cost = value;
      },
    },
  },

  watch: {
    dialog(val) {
      val || this.close();
    },
    dialogDelete(val) {
      val || this.closeDelete();
    },
  },

  methods: {
    editItem(item) {
      this.editedIndex = this.concepts.indexOf(item);
      this.editedItem = { ...item };
      this.dialog = true;
    },

    deleteItem(item) {
      this.editedIndex = this.concepts.indexOf(item);
      this.editedItem = { ...item };
      this.dialogDelete = true;
    },

    deleteItemConfirm() {
      this.concepts.splice(this.editedIndex, 1);
      this.closeDelete();
    },

    close() {
      this.dialog = false;
      this.$nextTick(() => {
        this.editedItem = { ...this.defaultItem };
        this.editedIndex = -1;
      });
    },

    closeDelete() {
      this.dialogDelete = false;
      this.$nextTick(() => {
        this.editedItem = { ...this.defaultItem };
        this.editedIndex = -1;
      });
    },
    async save() {
      const _this = this;
      if (!_this.$refs.formConcept.validate()) return;
      let IVA = new Promise((resolve) => {
        _this.$store.commit("showDialog", {
          type: "confirm",
          icon: "mdi-currency-usd",
          title: "Agregar IVA",
          message: "¿El concepto debe incluir IVA?",
          okCb: () => {
            return resolve(_this.Tax);
          },
          cancelCb: () => {
            return resolve(0);
          },
        });
      });
      _this.editedItem.tax = await IVA.then((tax) => {
        return tax;
      });
      _this.editedItem.cost = _this.Total;

      if (_this.editedIndex > -1) {
        Object.assign(_this.concepts[_this.editedIndex], _this.editedItem);
      } else {
        _this.concepts.push(_this.editedItem);
      }
      _this.close();
    },
  },
};
</script>
