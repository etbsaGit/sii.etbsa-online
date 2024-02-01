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
                    v-model="form.riego_tipo"
                    label="Tipo de Riego"
                    :items="[
                      'Gravedad o Rodado (Has)',
                      'Aspersión (Has)',
                      'Goteo (Has)',
                      'Otros Bombeo** (Has)',
                      'Goteo y Acolchado',
                      'Otro Compuertas',
                      'Pozo',
                    ]"
                    outlined
                    :rules="[(v) => !!v || 'Es Reqeerido']"
                  ></v-combobox>
                </v-col>
                <v-col cols="12" md="6">
                  <v-text-field
                    v-model="form.riego_propias"
                    label="Hectarias Propias"
                    required
                    outlined
                  ></v-text-field>
                </v-col>
                <v-col cols="12" md="6">
                  <v-text-field
                    v-model="form.riego_rentadas"
                    label="Hectarias Rentadas"
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
  name: "MetatableRiego",
  mixins: [mixinCustomerInfo],
  computed: {
    Title() {
      return this.editedIndex === -1
        ? "Nueva Tipo de Riego"
        : "Editar Tipo de Riego";
    },
    MetaInfo() {
      return {
        tipo_riego: this.items,
      };
    },
  },
  data: () => ({
    form: {
      riego_tipo: "",
      riego_propias: 0,
      riego_rentadas: 0,
    },
    formDefault: {
      riego_tipo: "",
      riego_propias: 0,
      riego_rentadas: 0,
    },
    headers: [
      { text: "Tipo de Riego", value: "riego_tipo" },
      { text: "Propias (HA)", value: "riego_propias" },
      { text: "Rentadas (HA)", value: "riego_rentadas" },
      { text: "Total (HA)", value: "total" },
      { text: "", value: "action", sortable: false },
    ],
  }),
};
</script>
