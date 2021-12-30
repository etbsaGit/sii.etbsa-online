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
          <template v-slot:[`item.cultivado_precio`]="{ value }">
            {{ value | money }}
          </template>
          <template v-slot:[`item.cultivado_costo`]="{ value }">
            {{ value | money }}
          </template>
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
                <v-col cols="12" sm="6" md="6">
                  <v-text-field
                    v-model="form.cultivado_ciclo"
                    label="Ciclo"
                    required
                  ></v-text-field>
                </v-col>
                <v-col cols="12" sm="6" md="6">
                  <v-combobox
                    v-model="form.cultivado_cultivo"
                    :items="[
                      'Ajo',
                      'Alfalfa',
                      'Apio',
                      'Avena',
                      'Brócoli',
                      'Calabaza',
                      'Cebada',
                      'Cebada',
                      'Cebolla',
                      'Chile Poblano',
                      'Cilantro',
                      'Col',
                      'Coliflor',
                      'Esparrago',
                      'Espinaca',
                      'Fresa',
                      'Frijol',
                      'Lechuga',
                      'Maiz',
                      'Papa',
                      'Radicchios',
                      'Sorgo',
                      'Trigo',
                      'Zanahoria',
                      'Zarzamora',
                      'Jicama',
                      'Hortalizas',
                      'Jitomate',
                      'Chile Jalapeño',
                      'Chile Chilaca',
                      'Tomate',
                    ]"
                    label="Cultivo*"
                    required
                    filled
                    outlined
                    :rules="[(v) => !!v || 'Es Requerido']"
                  ></v-combobox>
                </v-col>

                <v-col cols="12" md="4">
                  <v-text-field
                    v-model="form.cultivado_hectarias"
                    label="Hectarias"
                    suffix="Has"
                    type="number"
                    required
                  ></v-text-field>
                </v-col>
                <v-col cols="12" md="4">
                  <v-text-field
                    v-model="form.cultivado_rendimiento"
                    label="Rendimiento Tonelada / Ha"
                    required
                  ></v-text-field>
                </v-col>
                <v-col cols="12" md="4">
                  <v-text-field
                    v-model="form.cultivado_precio"
                    label="Precio Unitario / Tonelada*"
                    required
                    type="number"
                    outlined
                    prefix="$"
                    :rules="[(v) => !!v || 'Es Requerido']"
                  ></v-text-field>
                </v-col>
                <v-col cols="12" md="4">
                  <v-text-field
                    v-model="form.cultivado_costo"
                    label="Costo por Hectaria*"
                    required
                    type="number"
                    outlined
                    prefix="$"
                  ></v-text-field>
                </v-col>
                <v-col cols="12" md="4">
                  <v-text-field
                    v-model="form.cultivado_mes"
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
  name: "MetatableCultivado",
  mixins: [mixinCustomerInfo],

  computed: {
    Title() {
      return this.editedIndex === -1 ? "Nueva Cultivo" : "Editar Cultivo";
    },
    MetaInfo() {
      return {
        cultivado: this.items,
      };
    },
  },
  data: () => ({
    form: {
      cultivado_ciclo: "",
      cultivado_cultivo: "",
      cultivado_hectarias: 0,
      cultivado_rendimiento: 0,
      cultivado_precio: 0,
      cultivado_costo: 0,
      cultivado_mes: "",
    },
    formDefault: {
      cultivado_ciclo: "",
      cultivado_cultivo: "",
      cultivado_hectarias: 0,
      cultivado_rendimiento: 0,
      cultivado_precio: 0,
      cultivado_costo: 0,
      cultivado_mes: "",
    },
    headers: [
      { text: "Ciclo", value: "cultivado_ciclo" },
      { text: "Cultivo", value: "cultivado_cultivo" },
      { text: "Has", value: "cultivado_hectarias" },
      { text: "Rend Ton/Has", value: "cultivado_rendimiento" },
      { text: "Precio Unitario / Ton", value: "cultivado_precio" },
      { text: "Costo /Ha", value: "cultivado_costo" },
      { text: "Mes de Cosecha", value: "cultivado_mes" },
      { text: "", value: "action", sortable: false },
    ],
  }),
};
</script>
