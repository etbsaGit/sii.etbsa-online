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
          <template v-slot:[`item.equipo_valor`]="{ value }">
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
                <v-col cols="12" sm="6" md="8">
                  <v-combobox
                    v-model="form.equipo_tipo"
                    :items="[
                      'Tractor',
                      'Aspersora',
                      'Arado',
                      'Cultivadora',
                      'Rastra',
                      'Arado',
                    ]"
                    label="Tipo de Equipo*"
                    placeholder="Escibir Nombre del Equipo"
                    required
                    filled
                    outlined
                    :rules="[(v) => !!v || 'Es Requerido']"
                  ></v-combobox>
                </v-col>
                <v-col cols="12" sm="6" md="4">
                  <v-combobox
                    v-model="form.equipo_marca"
                    :items="[
                      'John Deere',
                      'New Holland',
                      'Massey Ferguson',
                      'International',
                      'McCormick',
                      'S/Marca',
                    ]"
                    label="Marca*"
                    required
                    filled
                    outlined
                    :rules="[(v) => !!v || 'Es Requerido']"
                  ></v-combobox>
                </v-col>

                <v-col cols="12" md="4">
                  <v-text-field
                    v-model="form.equipo_serie"
                    label="Serie"
                    required
                  ></v-text-field>
                </v-col>
                <v-col cols="12" md="4">
                  <v-combobox
                    v-model="form.equipo_condicion"
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
                <v-col cols="12" md="4">
                  <v-text-field
                    v-model="form.equipo_valor"
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
  name: "MetatableEquipos",
  mixins: [mixinCustomerInfo],
  computed: {
    Title() {
      return this.editedIndex === -1 ? "Nueva Maquinaria" : "Editar Maquinaria";
    },
    MetaInfo() {
      return {
        equipos: this.items,
      };
    },
  },
  data: () => ({
    form: {
      equipo_tipo: "",
      equipo_marca: "",
      equipo_serie: "",
      equipo_condicion: "",
      equipo_valor: 0,
    },
    formDefault: {
      equipo_tipo: "",
      equipo_marca: "",
      equipo_serie: "",
      equipo_condicion: "",
      equipo_valor: 0,
    },
    headers: [
      { text: "Tipo", value: "equipo_tipo" },
      { text: "Marca", value: "equipo_marca" },
      { text: "Serie o Modelo", value: "equipo_serie", sortable: false },
      { text: "Condicion", value: "equipo_condicion" },
      { text: "Valor", value: "equipo_valor", sortable: true },
      { text: "", value: "action", sortable: false },
    ],
  }),
};
</script>
