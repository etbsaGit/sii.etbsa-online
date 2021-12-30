<template>
  <v-container fluid>
    <v-data-table
      :headers="headers"
      :items="items"
      :options.sync="pagination"
      :server-items-length="totalItems"
      class="text-truncate blue--text overline elevation-1"
      calculate-widths
      fixed-header
      caption
      dense
    >
      <template v-slot:top>
        <v-toolbar flat>
          <v-toolbar-title>Combustibles</v-toolbar-title>
          <v-divider class="mx-4" inset vertical></v-divider>
          <v-spacer></v-spacer>
          <v-dialog v-model="dialog" max-width="500px">
            <template v-slot:activator="{ on, attrs }">
              <v-btn color="primary" class="mb-2" v-bind="attrs" v-on="on">
                Nuevo Registro
              </v-btn>
            </template>
            <v-card>
              <v-card-title>
                <span class="text-h5">{{ formTitle }}</span>
              </v-card-title>

              <v-card-text>
                <v-container>
                  <v-row>
                    <v-col cols="12" sm="6" md="6">
                      <v-text-field
                        v-model="editedItem.name"
                        label="Combustible"
                      ></v-text-field>
                    </v-col>
                    <v-col cols="12" sm="6" md="6">
                      <v-text-field
                        v-model="editedItem.cost_lt"
                        label="Costo x Litro"
                      ></v-text-field>
                    </v-col>
                  </v-row>
                </v-container>
              </v-card-text>

              <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn color="blue darken-1" text @click="close">
                  Cancel
                </v-btn>
                <v-btn color="blue darken-1" text @click="save">
                  Guardar
                </v-btn>
              </v-card-actions>
            </v-card>
          </v-dialog>
          <v-dialog v-model="dialogDelete" max-width="500px">
            <v-card>
              <v-card-title class="text-h5">
                Are you sure you want to delete this item?
              </v-card-title>
              <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn color="blue darken-1" text @click="closeDelete"
                  >Cancel</v-btn
                >
                <v-btn color="blue darken-1" text @click="deleteItemConfirm"
                  >OK</v-btn
                >
                <v-spacer></v-spacer>
              </v-card-actions>
            </v-card>
          </v-dialog>
        </v-toolbar>
      </template>
      <template v-slot:[`item.cost_lt`]="{ value }">
        {{ value | money }}
      </template>
      <template v-slot:[`item.updated_at`]="{ value }">
        {{ $appFormatters.formatDate(value, "lll") }}
      </template>
      <template v-slot:[`item.actions`]="{ item }">
        <v-icon small class="mr-2" @click="editItem(item)">
          mdi-pencil
        </v-icon>
        <!-- <v-icon small @click="deleteItem(item)">
          mdi-delete
        </v-icon> -->
      </template>
    </v-data-table>
  </v-container>
</template>

<script>
export default {
  data: () => ({
    dialog: false,
    dialogDelete: false,
    headers: [
      {
        text: "Nombre Combustible",
        align: "start",
        sortable: false,
        value: "name",
      },
      { text: "Costo x Litro", align: "center", value: "cost_lt" },
      { text: "Ultimo Cambio", align: "right", value: "updated_at" },
      { text: "Actions", align: "right", value: "actions", sortable: false },
    ],
    items: [],
    totalItems: 0,
    pagination: {
      itemsPerPage: 10,
      page: 1,
    },
    editedIndex: -1,
    editedItem: {
      name: "",
      cost_lt: 0,
    },
    defaultItem: {
      name: "",
      cost_lt: 0,
    },
  }),

  computed: {
    formTitle() {
      return this.editedIndex === -1
        ? "Nuevo Combustible"
        : "Editar Combustible";
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

  created() {
    this.initialize();
  },

  methods: {
    async initialize() {
      const _this = this;
      let params = {};
      _this.items = await axios
        .get("/admin/vehicles-fuels", { params: params })
        .then(({ data }) => {
          _this.totalItems = data.data.total;
          _this.pagination.totalItems = data.data.total;
          return data.data.data;
        });
    },

    editItem(item) {
      this.editedIndex = this.items.indexOf(item);
      this.editedItem = Object.assign({}, item);
      this.dialog = true;
    },

    deleteItem(item) {
      this.editedIndex = this.items.indexOf(item);
      this.editedItem = Object.assign({}, item);
      this.dialogDelete = true;
    },

    deleteItemConfirm() {
      this.items.splice(this.editedIndex, 1);
      this.closeDelete();
    },

    close() {
      this.dialog = false;
      this.$nextTick(() => {
        this.editedItem = Object.assign({}, this.defaultItem);
        this.editedIndex = -1;
      });
    },

    closeDelete() {
      // delete
      this.dialogDelete = false;
      this.$nextTick(() => {
        this.editedItem = Object.assign({}, this.defaultItem);
        this.editedIndex = -1;
      });
    },

    save() {
      const _this = this;
      if (this.editedIndex > -1) {
        // Object.assign(this.items[this.editedIndex], this.editedItem);
        _this.$store.commit("showDialog", {
          type: "confirm",
          icon: "mdi-pencil",
          title: "Actualizacion de Combustible",
          message: "Se Actualizara el Registro",
          okCb: () => {
            let payload = {
              name: _this.editedItem.name,
              cost_lt: _this.editedItem.cost_lt,
            };
            axios
              .put(`/admin/vehicles-fuels/${_this.editedItem.id}`, payload)
              .then((response) => {
                _this.$store.commit("showSnackbar", {
                  message: response.data.message,
                  color: "success",
                  duration: 3000,
                });
                _this.close();
                _this.initialize();
              });
          },
          cancelCb: () => {
            console.log("CANCEL");
          },
        });
      } else {
        // this.items.push(this.editedItem);
        _this.$store.commit("showDialog", {
          type: "confirm",
          icon: "mdi-credit-card-plus-outline",
          title: "Nuevo Combustible",
          message: "Se agregara un nuevo Registro",
          okCb: () => {
            let payload = {
              name: _this.editedItem.name,
              cost_lt: _this.editedItem.cost_lt,
            };
            axios.post(`/admin/vehicles-fuels`, payload).then((response) => {
              _this.$store.commit("showSnackbar", {
                message: response.data.message,
                color: "success",
                duration: 3000,
              });
              _this.close();
              _this.initialize();
            });
          },
          cancelCb: () => {
            console.log("CANCEL");
          },
        });
      }
    },
  },
};
</script>
