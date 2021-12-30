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
                  <v-text-field
                    v-model="form.dis_name"
                    label="Nombre Distribucion"
                    placeholder="Nombre, Ubicacion y/o Rancho"
                    :rules="[(v) => !!v || 'Es Requerido']"
                    required
                  >
                  </v-text-field>
                </v-col>
                <v-col cols="12" md="4">
                  <v-text-field
                    v-model="form.dis_ubicacion"
                    label="Ubicacion*"
                    required
                    outlined
                  ></v-text-field>
                </v-col>
                <v-col cols="12" md="4">
                  <v-text-field
                    v-model="form.dis_propias"
                    label="Propias*"
                    suffix="HAs"
                    required
                    outlined
                  ></v-text-field>
                </v-col>
                <v-col cols="12" md="4">
                  <v-text-field
                    v-model="form.dis_rentadas"
                    label="Rentadas*"
                    suffix="HAs"
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
  name: "MetatableDistribucion",
  mixins: [mixinCustomerInfo],

  computed: {
    Title() {
      return this.editedIndex === -1
        ? "Nueva Distribucion"
        : "Editar Distribucion";
    },
    MetaInfo() {
      return {
        distribucion: this.items,
      };
    },
  },
  data: () => ({
    form: {
      dis_name: "",
      dis_ubicacion: "",
      dis_propias: "",
      dis_rentadas: "",
    },
    formDefault: {
      dis_name: "",
      dis_ubicacion: "",
      dis_propias: "",
      dis_rentadas: "",
    },
    headers: [
      { text: "Nombre Rancho", value: "dis_name" },
      { text: "Ubicacion", value: "dis_ubicacion" },
      { text: "Propias (HA)", value: "dis_propias" },
      { text: "Rentadas (HA)", value: "dis_rentadas" },
      { text: "", value: "action", sortable: false },
    ],
  }),
};
</script>
