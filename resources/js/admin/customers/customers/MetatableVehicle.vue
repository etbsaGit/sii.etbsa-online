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
          <template v-slot:[`item.vehiculo_valor`]="{ value }">
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
                <v-col cols="12" sm="6" md="5">
                  <v-combobox
                    v-model="form.vehiculo_tipo"
                    :items="[
                      'Automovil',
                      'Camion',
                      'Camion de Volteo',
                      'Camioneta Tipo Torton',
                      'Camioneta Pick kUp',
                    ]"
                    label="Tipo de Vehiculo*"
                    placeholder="Escribir Nombre del Vehiculo"
                    required
                    filled
                    outlined
                    :rules="[(v) => !!v || 'Es Requerido']"
                  ></v-combobox>
                </v-col>
                <v-col cols="12" sm="6" md="4">
                  <v-text-field
                    v-model="form.vehiculo_marca"
                    label="Marca"
                  ></v-text-field>
                </v-col>

                <v-col cols="12" md="3">
                  <v-text-field
                    v-model="form.vehiculo_modelo"
                    label="Modelo"
                    required
                  ></v-text-field>
                </v-col>
                <v-col cols="12" sm="6">
                  <v-combobox
                    v-model="form.vehiculo_condicion"
                    :items="[
                      'Muy Mala Condicion',
                      'Mala Condicion',
                      'Regular',
                      'Buena Condicion',
                      'Muy Buena Condicion',
                    ]"
                    label="Condicion*"
                    required
                    :rules="[(v) => !!v || 'Es Requerido']"
                  ></v-combobox>
                </v-col>
                <v-col cols="12" md="6">
                  <v-text-field
                    v-model="form.vehiculo_valor"
                    label="Precio (valor)*"
                    required
                    type="number"
                    outlined
                    prefix="$"
                    :rules="[(v) => !!v || 'Es Requerido']"
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
  name: "MetatableVehiculos",
  mixins: [mixinCustomerInfo],
  computed: {
    Title() {
      return this.editedIndex === -1 ? "Nuevo Vehiculo" : "Editar Vehiculo";
    },
    MetaInfo() {
      return {
        vehiculos: this.items,
      };
    },
  },
  data: () => ({
    form: {
      vehiculo_tipo: "",
      vehiculo_marca: "",
      vehiculo_modelo: "",
      vehiculo_condicion: "",
      vehiculo_valor: 0,
    },
    formDefault: {
      vehiculo_tipo: "",
      vehiculo_marca: "",
      vehiculo_modelo: "",
      vehiculo_condicion: "",
      vehiculo_valor: 0,
    },
    headers: [
      {
        text: "Tipo",
        align: "left",
        value: "vehiculo_tipo",
        sortable: false,
      },
      { text: "Marca", value: "vehiculo_marca" },
      { text: "Modelo", value: "vehiculo_modelo", sortable: false },
      { text: "Condicion", value: "vehiculo_condicion", sortable: false },
      { text: "Valor", value: "vehiculo_valor", sortable: true },
      { text: "", value: "action", sortable: false },
    ],
  }),
};
</script>
