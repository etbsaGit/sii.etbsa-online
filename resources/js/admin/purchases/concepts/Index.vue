<template>
  <v-data-table :headers="headers" :items="items" class="elevation-1">
    <template v-slot:top>
      <v-toolbar flat>
        <v-toolbar-title>Admo. Conceptos de Compra</v-toolbar-title>
        <v-divider class="mx-4" inset vertical></v-divider>
        <v-spacer></v-spacer>
        <v-dialog v-model="dialog" max-width="650px" persistent>
          <template v-slot:activator="{ on, attrs }">
            <v-btn color="primary" dark class="mb-2" v-bind="attrs" v-on="on">
              Agregar Nuevo Concepto
            </v-btn>
          </template>
          <v-card>
            <v-card-title>
              <span class="text-h5">{{ formTitle }}</span>
            </v-card-title>

            <v-card-text>
              <v-container>
                <purchase-concept-form :form.sync="editedItem" />
              </v-container>
            </v-card-text>

            <v-card-actions>
              <v-spacer></v-spacer>
              <v-btn color="blue darken-1" text @click="close">
                Cancel
              </v-btn>
              <v-btn color="blue darken-1" text @click="save">
                Save
              </v-btn>
            </v-card-actions>
          </v-card>
        </v-dialog>
        <v-dialog v-model="dialogUpdateUsoCfdi" max-width="650px" persistent>
          <v-card>
            <v-card-title class="text-h5">
              Configuracion USO CFDI
            </v-card-title>
            <v-card-text>
              <v-row>
                <v-col
                  cols="4"
                  v-for="(g, k) in options.usocfdi"
                  :key="k"
                  class="caption"
                >
                  <v-switch
                    v-model="usocfdi[g.id]"
                    :label="g.clave"
                    :hint="g.description"
                    persistent-hint
                    dense
                  ></v-switch>
                </v-col>
              </v-row>
            </v-card-text>
            <v-card-actions>
              <v-spacer></v-spacer>
              <v-btn color="blue darken-1" text @click="closeUpdateUsoCfdi">
                CERRAR
              </v-btn>
              <v-btn
                color="blue darken-1"
                text
                @click="updateUsoCfdiItemConfirm"
              >
                GUARDAR
              </v-btn>
              <v-spacer></v-spacer>
            </v-card-actions>
          </v-card>
        </v-dialog>
        <v-dialog v-model="dialogDelete" max-width="600px">
          <v-card>
            <v-card-title class="text-h5">
              Seguro en Eliminar Concepto de Compra?
            </v-card-title>
            <v-card-actions>
              <v-spacer></v-spacer>
              <v-btn color="blue darken-1" text @click="closeDelete">
                Cancel
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
    <template v-slot:[`item.usocfdi`]="{ item }">
      <v-btn rounded color="purple" dark @click="updateUsoCfdi(item)">
        Configuracion
      </v-btn>
    </template>
    <template v-slot:[`item.actions`]="{ item }">
      <v-icon small class="mr-2" @click="editItem(item)">
        mdi-pencil
      </v-icon>
      <v-icon small @click="deleteItem(item)">
        mdi-delete
      </v-icon>
    </template>
    <template v-slot:no-data>
      <v-btn color="primary" @click="initialize">
        Reset
      </v-btn>
    </template>
  </v-data-table>
</template>
<script>
import PurchaseConceptForm from "./PurchaseConceptForm.vue";
export default {
  name: "PurchaseConceptIndex",
  components: { PurchaseConceptForm },
  data: () => ({
    dialog: false,
    dialogDelete: false,
    dialogUpdateUsoCfdi: false,
    headers: [
      {
        text: "Nombre",
        align: "start",
        sortable: false,
        value: "name",
      },
      { text: "Articulos", value: "products", sortable: false },
      { text: "Uso CFDI", value: "usocfdi", sortable: false },
      { text: "Actions", value: "actions", sortable: false },
    ],
    items: [],
    totalItems: 0,
    pagination: {
      rowsPerPage: 10,
    },
    filters: {
      name: "",
    },
    editedItemId: -1,
    editedItem: {
      name: "",
      products: [],
    },
    defaultItem: {
      name: "",
      products: [],
    },
    options: {
      usocfdi: [],
    },
    usocfdi: [],
  }),

  computed: {
    formTitle() {
      return this.editedItemId === -1 ? "Agregar Concepto" : "Editar Concepto";
    },
  },

  watch: {
    dialog(val) {
      val || this.close();
    },
    dialogDelete(val) {
      val || this.closeDelete();
    },
    dialogUpdateUsoCfdi(val) {
      val || this.closeUpdateUsoCfdi();
    },
    "pagination.page": function () {
      this.initialize(() => {});
    },
    "pagination.rowsPerPage": function () {
      this.initialize(() => {});
    },
    "filters.name": _.debounce(function () {
      this.initialize(() => {});
    }, 700),
  },

  mounted() {
    const _this = this;
    _this.$store.commit("setBreadcrumbs", [
      { label: "Conceptos de Compra", name: "" },
    ]);
    this.initialize();
  },

  methods: {
    async initialize() {
      const _this = this;
      await axios.get("/admin/purchase-concept").then((res) => {
        _this.items = res.data.data.data;
        _this.totalItems = res.data.data.total;
        _this.pagination.totalItems = res.data.data.total;
      });
    },

    editItem(item) {
      this.editedItemId = item.id;
      this.editedItem = Object.assign({}, item);
      this.dialog = true;
    },

    updateUsoCfdi(item) {
      const self = this;
      this.editedItemId = item.id;
      console.log(item);
      _.each(item.usocfdi, (g) => {
        self.usocfdi[g.id] = true;
      });
      this.loadUsoCFDI();
      this.dialogUpdateUsoCfdi = true;
    },

    async updateUsoCfdiItemConfirm() {
      // this.items.splice(this.editedItemId, 1);
      console.log("Actualiza USO CFDI", this.editedItemId, this.usocfdi);
      const _this = this;

      let payload = {
        usocfdi: this.usocfdi,
      };

      await axios
        .put(
          `/admin/purchase-concept/${_this.editedItemId}/update-uso-cfdi`,
          payload
        )
        .then(function (response) {
          _this.$store.commit("showSnackbar", {
            message: response.data.message,
            color: "success",
            duration: 3000,
          });
          _this.initialize();
          _this.closeUpdateUsoCfdi();
          _this.$store.commit("hideLoader");
        })
        .catch(function (error) {
          _this.$store.commit("hideLoader");

          if (error.response) {
            _this.$store.commit("showSnackbar", {
              message: error.response.data.message,
              color: "error",
              duration: 3000,
            });
          } else if (error.request) {
            console.log(error.request);
          } else {
            console.log("Error", error.message);
          }
        });
    },

    deleteItem(item) {
      this.editedItemId = item.id;
      this.editedItem = Object.assign({}, item);
      this.dialogDelete = true;
    },

    deleteItemConfirm() {
      // this.items.splice(this.editedItemId, 1);
      console.log("Eliminar Registro", this.editedItemId);
      this.closeDelete();
    },

    close() {
      this.dialog = false;
      this.$nextTick(() => {
        this.editedItem = Object.assign({}, this.defaultItem);
        this.editedItemId = -1;
      });
    },

    closeDelete() {
      this.dialogDelete = false;
      this.$nextTick(() => {
        this.editedItem = Object.assign({}, this.defaultItem);
        this.editedItemId = -1;
      });
    },
    closeUpdateUsoCfdi() {
      this.dialogUpdateUsoCfdi = false;
      this.$nextTick(() => {
        this.usocfdi = [];
        this.editedItemId = -1;
      });
    },

    save() {
      const _this = this;
      if (this.editedItemId != -1) {
        console.log("Editar id", this.editedItemId, this.editedItem);
        axios
          .put(
            `/admin/purchase-concept/${_this.editedItemId}`,
            _this.editedItem
          )
          .then(function (response) {
            _this.$store.commit("showSnackbar", {
              message: response.data.message,
              color: "success",
              duration: 3000,
            });
            _this.initialize();
          })
          .catch(function (error) {
            _this.$store.commit("hideLoader");

            if (error.response) {
              _this.$store.commit("showSnackbar", {
                message: error.response.data.message,
                color: "error",
                duration: 3000,
              });
            } else if (error.request) {
              console.log(error.request);
            } else {
              console.log("Error", error.message);
            }
          });
      } else {
        console.log("Create new", this.editedItem);
        const self = this;
        axios
          .post("/admin/purchase-concept", _this.editedItem)
          .then(function (response) {
            self.$store.commit("showSnackbar", {
              message: response.data.message,
              color: "success",
              duration: 3000,
            });
            _this.initialize();
          })
          .catch(function (error) {
            self.$store.commit("hideLoader");

            if (error.response) {
              self.$store.commit("showSnackbar", {
                message: error.response.data.message,
                color: "error",
                duration: 3000,
              });
            } else if (error.request) {
              console.log(error.request);
            } else {
              console.log("Error", error.message);
            }
          });
      }
      this.close();
    },

    async loadUsoCFDI() {
      const self = this;
      await axios
        .get("/admin/purchase-concept/create")
        .then(function (response) {
          let Data = response.data.data;
          self.options.usocfdi = Data.uso_cfdi;

          _.each(self.options.usocfdi, (d) => {
            d.selected = false;
          });
        });
    },
  },
};
</script>
<style></style>
