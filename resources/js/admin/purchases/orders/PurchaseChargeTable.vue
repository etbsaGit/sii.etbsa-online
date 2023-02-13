<template>
  <div>
    <v-simple-table dense>
      <template v-slot:default>
        <tbody>
          <tr v-for="(item, index) in items" :key="index">
            <td class="pr-1" @click="editItem(item)">
              <v-list-item-content>
                <v-list-item-title>{{ item.agency.title }}</v-list-item-title>
                <v-list-item-subtitle>
                  {{ item.department.title }}
                </v-list-item-subtitle>
              </v-list-item-content>
            </td>
            <td class="text-center px-0" style="width: 70px;">
              <v-chip
                v-if="readOnly"
                color="indigo"
                style="max-width: 75px;"
                small
                label
                dark
              >
                {{ item.percent }} %
              </v-chip>
              <v-edit-dialog
                v-else
                :return-value.sync="item.percent"
                large
                persistent
                save-text="Guardar"
                cancel-text="Cancelar"
              >
                <v-chip small label color="blue" style="max-width: 75px;" dark>
                  {{ item.percent }} %
                </v-chip>

                <template v-slot:input>
                  <div class="mt-4 text-h6">
                    Cambiar Procentaje %
                  </div>
                  <v-text-field
                    v-model="item.percent"
                    label="Ingresar Porcentaje"
                    type="number"
                    suffix="%"
                    outlined
                    solo
                  ></v-text-field>
                </template>
              </v-edit-dialog>
            </td>
            <td
              v-if="!readOnly"
              class="text-center px-0"
              style="max-width: 38px;"
            >
              <v-btn icon @click="deleteItem(item)">
                <v-icon small color="red">mdi-trash-can</v-icon>
              </v-btn>
            </td>
          </tr>
        </tbody>
      </template>
    </v-simple-table>
    <v-dialog v-model="Dialog" max-width="600px">
      <v-card>
        <v-card-title>
          <span class="text-h5">Cargos a Sucursal</span>
        </v-card-title>
        <v-card-text>
          <v-container>
            <v-form v-model="valid" ref="form" lazy-validation>
              <v-row class="overline">
                <v-col cols="12" md="12">
                  <v-select
                    v-model="form.agency"
                    :items="agencies"
                    item-value="id"
                    item-text="title"
                    label="Sucursal*"
                    required
                    return-object
                    outlined
                  ></v-select>
                </v-col>
                <v-col cols="12" md="8">
                  <v-autocomplete
                    v-model="form.department"
                    :items="departments"
                    item-value="id"
                    item-text="title"
                    label="Departamento"
                    return-object
                    outlined
                  ></v-autocomplete>
                </v-col>
                <v-col cols="12" md="4">
                  <v-text-field
                    v-model.number="form.percent"
                    label="Porcentaje"
                    suffix="%"
                    type="number"
                    required
                    outlined
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
            Agregar
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </div>
</template>
<script>
export default {
  name: "PurchaseChargesTable",
  props: {
    dialogForm: {
      default: false,
    },
    agencies: {
      type: Array,
      default: () => {
        return [];
      },
    },
    departments: {
      type: Array,
      default: () => {
        return [];
      },
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
      editedIndex: -1,
      form: {
        agency: {},
        department: {},
        percent: 100,
      },
      formDefault: {
        agency: {},
        department: {},
        percent: 100,
      },
    };
  },

  methods: {
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
  },
};
</script>
<style></style>
