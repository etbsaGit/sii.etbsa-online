<template>
  <v-card>
    <v-card-text class="pa-0">
      <template>
        <v-data-table
          :headers="headers"
          :items="items"
          :sort-desc="true"
          class="elevation-0 table-striped caption"
          hide-default-footer
          dense
        >
          <template v-slot:[`item.action`]="{ item }">
            <v-row dense>
              <v-btn icon>
                <v-icon color="blue" small @click="editItem(item)">
                  mdi-pencil
                </v-icon>
              </v-btn>
              <v-btn icon @click="deleteItem">
                <v-icon color="red" small>mdi-trash-can</v-icon>
              </v-btn>
            </v-row>
          </template>
        </v-data-table>
      </template>
    </v-card-text>
    <v-dialog v-model="dialog" max-width="600px">
      <v-card flat>
        <v-card-title>
          <span class="text-h5">{{ Title }}</span>
        </v-card-title>
        <v-card-text>
          <v-container>
            <v-form v-model="valid" ref="form" lazy-validation>
              <v-row dense align="center">
                <v-col cols="12">
                  <v-combobox
                    v-model="form.abastecimiento_fuente"
                    label="Fuente de abastecimiento"
                    :items="[
                      'Precipitacion Promedio (mm)',
                      'Pozo Profundo (LPS)',
                      'Superficial (LPS)',
                      'Otros (LPS)',
                      'Pozo Profundo (HAS)',
                      'Superficial (HAS)',
                      'Otros (HAS)',
                    ]"
                    outlined
                  ></v-combobox>
                </v-col>
                <v-col cols="12">
                  <v-text-field
                    v-model="form.abastecimiento_cantidad"
                    label="Mes(es)*"
                    required
                    outlined
                  ></v-text-field>
                </v-col>
              </v-row>
            </v-form>
          </v-container>
          <small>*indica campo obligatorio</small>
        </v-card-text>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn color="blue darken-1" text @click="dialog = false">
            Cerrar
          </v-btn>
          <v-btn color="blue darken-1" @click="submit">
            Guardar
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </v-card>
</template>

<script>
import { mixinCustomerInfo } from "./MixinCustomerInfo.js";
export default {
  name: "Metatableabastecimiento",
  mixins: [mixinCustomerInfo],

  computed: {
    Title() {
      return this.editedIndex === -1
        ? "Nueva Abastecimiento"
        : "Editar Abastecimiento";
    },
    MetaInfo() {
      return {
        abastecimiento: this.items,
      };
    },
  },
  data: () => ({
    form: {
      abastecimiento_fuente: "",
      abastecimiento_cantidad: "",
    },
    formDefault: {
      abastecimiento_fuente: "",
      abastecimiento_cantidad: "",
    },
    headers: [
      { text: "Tipo Abastecimiento", value: "abastecimiento_fuente" },
      { text: "Cantidad", value: "abastecimiento_cantidad" },
      { text: "", value: "action", sortable: false },
    ],
  }),
};
</script>
