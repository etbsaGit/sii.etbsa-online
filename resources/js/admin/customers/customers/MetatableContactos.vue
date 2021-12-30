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
                    v-model="form.contacto_nombre"
                    label="Nombre Completo"
                    hint="nombre del Contacto"
                    persistent-hint
                    :rules="[(v) => !!v || 'es requerido']"
                  ></v-text-field>
                </v-col>
                <v-col cols="12" md="6">
                  <v-text-field
                    v-model="form.contacto_telefono"
                    label="Telefono*"
                    required
                    outlined
                    :rules="[(v) => !!v || 'es requerido']"
                  ></v-text-field>
                </v-col>
                <v-col cols="12" md="6">
                  <v-text-field
                    v-model="form.contacto_email"
                    label="Correo Electronico"
                    required
                    outlined
                  ></v-text-field>
                </v-col>

                <v-col cols="12" md="6">
                  <v-combobox
                    v-model="form.contacto_parentesco"
                    label="Parentesco"
                    :items="[
                      'Padre',
                      'Madre',
                      'Hermano',
                      'Tio',
                      'Primo',
                      'Abuelo',
                      'Hijo(a)',
                      'Esposo(a)',
                      'Conyuge',
                      'Conocido',
                      'Amigo(a)',
                    ]"
                    :rules="[(v) => !!v || 'es requerido']"
                  ></v-combobox>
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
  name: "MetatableComposicion",
  mixins: [mixinCustomerInfo],

  computed: {
    Title() {
      return this.editedIndex === -1 ? "Nueva Contacto" : "Editar Contacto";
    },
    MetaInfo() {
      return {
        contactos: this.items,
      };
    },
  },
  data: () => ({
    form: {
      contacto_nombre: "",
      contacto_telefono: "",
      contacto_parentesco: "",
      contacto_email: "",
    },
    formDefault: {
      contacto_nombre: "",
      contacto_telefono: "",
      contacto_parentesco: "",
      contacto_email: "",
    },
    headers: [
      { text: "Nombre", value: "contacto_nombre" },
      { text: "Telefono", value: "contacto_telefono" },
      { text: "Parentesco", value: "contacto_parentesco" },
      { text: "Email", value: "contacto_email" },
      { text: "", value: "action", sortable: false },
    ],
  }),
};
</script>
