<template>
  <v-data-table
    :headers="headers"
    :items="items"
    :options.sync="pagination"
    :server-items-length="totalItems"
    calculate-widths
    fixed-header
    caption
    dense
    class="elevation-1 text-uppercase"
  >
    <template #top>
      <v-toolbar flat>
        <v-toolbar-title>Conceptos de Compra</v-toolbar-title>
        <v-divider class="mx-4" inset vertical></v-divider>
        <v-spacer></v-spacer>
        <v-dialog v-model="dialog" max-width="650px" persistent>
          <template #activator="{ on, attrs }">
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
              <v-btn color="blue darken-1" text @click="close"> Cancel </v-btn>
              <v-btn color="blue darken-1" text @click="save"> Save </v-btn>
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
      <v-toolbar flat>
        <v-text-field
          v-model="search"
          label="Buscar Nombre Concepto"
          outlined
          dense
          hide-details
          clearable
        ></v-text-field>
        <v-select
          v-model="filters.purchase_type_id"
          :items="options.purchaseTypes"
          item-value="id"
          item-text="name"
          label="Tipo de Compra"
          prepend-icon="mdi-filter-variant"
          hide-details
          outlined
          filled
          clearable
          dense
          class="mx-2"
          style="max-width: 350px"
        ></v-select>
        <v-select
          v-model="filters.uso_cfdi_ids"
          :items="options.usocfdi"
          item-value="id"
          item-text="clave"
          label="USO de CFDI"
          prepend-icon="mdi-filter-variant"
          hide-details
          outlined
          filled
          clearable
          dense
          multiple
          chips
          deletable-chips
        >
          <template #item="{ item, attrs }">
            <v-list-item-action>
              <v-checkbox :input-value="attrs.inputValue"></v-checkbox>
            </v-list-item-action>
            <v-list-item-content>
              <v-list-item-title>
                {{ item.clave }}
              </v-list-item-title>
              <v-list-item-subtitle>
                {{ item.description }}
              </v-list-item-subtitle>
            </v-list-item-content>
          </template>
        </v-select>
        <v-spacer />
        <v-btn icon @click="initialize()">
          <v-icon>mdi-refresh</v-icon>
        </v-btn>
      </v-toolbar>
    </template>
    <template #[`item.usocfdi`]="{ item }">
      <v-btn rounded color="purple" small dark @click="updateUsoCfdi(item)">
        Configuracion
      </v-btn>
    </template>
    <template #[`item.actions`]="{ item }">
      <v-icon small class="mr-2" @click="editItem(item)"> mdi-pencil </v-icon>
      <v-icon small @click="deleteItem(item)"> mdi-delete </v-icon>
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
      { text: "Tipo de Compra", value: "purchase_type.name", sortable: false },
      {
        text: "Nombre Concepto",
        align: "start",
        sortable: false,
        value: "name",
      },
      { text: "Uso CFDI", value: "usocfdi", sortable: false },
      { text: "Actions", value: "actions", sortable: false },
    ],
    items: [],
    totalItems: 0,
    pagination: {
      rowsPerPage: 10,
    },
    search: "",
    filters: {
      name: "",
      purchase_type_id: "",
      uso_cfdi_ids: [],
    },
    editedItemId: -1,
    editedItem: {
      name: "",
      purchase_type_id: 1,
    },
    defaultItem: {
      name: "",
      purchase_type_id: 1,
    },
    options: {
      usocfdi: [],
      purchaseTypes: [],
    },
    usocfdi: [],
  }),

  computed: {
    formTitle() {
      return this.editedItemId === -1 ? "Agregar Concepto" : "Editar Concepto";
    },
  },

  watch: {
    pagination: {
      handler: _.debounce(function (v) {
        this.initialize(() => {});
      }, 999),
      deep: true,
    },
    filters: {
      handler: _.debounce(function (v) {
        this.initialize(() => {});
      }, 999),
      deep: true,
    },
    search: {
      handler: _.debounce(function (v) {
        this.initialize(() => {});
      }, 999),
      deep: true,
    },
    dialog(val) {
      val || this.close();
    },
    dialogDelete(val) {
      val || this.closeDelete();
    },
    dialogUpdateUsoCfdi(val) {
      val || this.closeUpdateUsoCfdi();
    },
  },

  mounted() {
    const _this = this;
    _this.$store.commit("setBreadcrumbs", [
      { label: "Conceptos de Compra", name: "" },
    ]);
    // this.initialize();
  },

  methods: {
    async initialize() {
      const _this = this;
      let params = {
        ..._this.filters,
        search: _this.search,
        page: _this.pagination.page,
        per_page: _this.pagination.itemsPerPage,
      };

      const {
        data: {
          data: { items, optionsfilters },
          message,
        },
      } = await axios.get("/admin/purchase-concept", { params: params });

      _this.items = items.data;
      _this.totalItems = items.total;
      _this.pagination.totalItems = items.total;
      _this.options.usocfdi = optionsfilters.uso_cfdi;
      _this.options.purchaseTypes = optionsfilters.purchase_types;

      _this.$store.commit("showSnackbar", {
        message: message,
        color: "success",
        duration: 3000,
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
      _.each(item.usocfdi, (g) => {
        self.usocfdi[g.id] = true;
      });
      this.loadUsoCFDI();
      this.dialogUpdateUsoCfdi = true;
    },

    async updateUsoCfdiItemConfirm() {
      // this.items.splice(this.editedItemId, 1);
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

    async save() {
      const _this = this;
      if (this.editedItemId != -1) {
        console.log("Editar id", this.editedItemId, this.editedItem);
        await axios
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
            _this.$eventBus.$emit("SAVE_PURCHASE_CONCEPT");
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
        await axios
          .post("/admin/purchase-concept", _this.editedItem)
          .then(function (response) {
            self.$store.commit("showSnackbar", {
              message: response.data.message,
              color: "success",
              duration: 3000,
            });
            _this.initialize();
            _this.$eventBus.$emit("SAVE_PURCHASE_CONCEPT");
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
      const _this = this;
      await axios
        .get("/admin/purchase-concept/create")
        .then(function (response) {
          let Data = response.data.data;
          _this.options.usocfdi = Data.uso_cfdi;
          _this.options.purchaseTypes = Data.purchase_types;

          _.each(_this.options.usocfdi, (d) => {
            d.selected = false;
          });
        });
    },
  },
};
</script>
<style></style>
