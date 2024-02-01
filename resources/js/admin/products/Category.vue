<template>
  <v-data-table
    :headers="headers"
    :items="categories"
    :options.sync="pagination"
    :server-items-length="totalItems"
    class="elevation-1 text-uppercase caption"
    dense
  >
    <template v-slot:top>
      <div class="d-flex flex-row justify-end pa-2">
        <v-dialog v-model="dialog" max-width="750px">
          <template v-slot:activator="{ on, attrs }">
            <v-btn color="primary" dark class="mb-2" v-bind="attrs" v-on="on">
              Nueva Categoria
            </v-btn>
          </template>
          <v-card>
            <v-card-title>
              <span class="text-h5">{{ formTitle }}</span>
            </v-card-title>

            <v-card-text>
              <v-container>
                <v-row>
                  <v-col cols="12">
                    <v-text-field
                      v-model="editedItem.name"
                      label="Nombre Categoria"
                      placeholder="Nombre Categoria"
                      outlined
                    ></v-text-field>
                  </v-col>
                  <v-col cols="12">
                    <v-select
                      v-model="editedItem.parent"
                      label="Categoria Padre"
                      placeholder="Selecciones una Categoria"
                      :items="options.categories"
                      item-text="name"
                      item-value="id"
                      outlined
                    >
                      <template v-slot:selection="{ item }">
                        <v-breadcrumbs
                          :items="item.breadcrumbs_category"
                          class="pa-0 overline"
                        >
                          <template v-slot:divider>
                            <v-icon>mdi-chevron-right</v-icon>
                          </template>
                        </v-breadcrumbs>
                      </template>
                      <template v-slot:item="{ item }">
                        <v-breadcrumbs
                          :items="item.breadcrumbs_category"
                          class="pa-0 overline"
                        >
                          <template v-slot:divider>
                            <v-icon>mdi-chevron-right</v-icon>
                          </template>
                        </v-breadcrumbs>
                      </template>
                    </v-select>
                  </v-col>
                </v-row>
              </v-container>
            </v-card-text>

            <v-card-actions>
              <v-spacer></v-spacer>
              <v-btn color="blue darken-1" text @click="close"> Cancel </v-btn>
              <v-btn color="blue darken-1" text @click="save"> Save </v-btn>
            </v-card-actions>
          </v-card>
        </v-dialog>
        <v-dialog v-model="dialogDelete" max-width="500px">
          <v-card>
            <v-card-title class="text-h5"
              >Are you sure you want to delete this item?</v-card-title
            >
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
      </div>
      <v-toolbar flat>
        <v-toolbar-title>Categorias Productos</v-toolbar-title>
        <v-divider class="mx-4" inset vertical></v-divider>
        <v-spacer></v-spacer>

        <v-toolbar-items>
          <table-header-buttons
            :reloadTable="initialize"
          ></table-header-buttons>
        </v-toolbar-items>
      </v-toolbar>
    </template>
    <template v-slot:item.breadcrumbs_category="{ value }">
      <v-breadcrumbs :items="value" class="pa-0">
        <template v-slot:divider>
          <v-icon>mdi-chevron-right</v-icon>
        </template>
      </v-breadcrumbs>
    </template>
    <template v-slot:item.actions="{ item }">
      <v-icon small class="mr-2" @click="editItem(item)"> mdi-pencil </v-icon>
      <v-icon small @click="deleteItem(item)"> mdi-delete </v-icon>
    </template>
    <template v-slot:no-data>
      <h4>Sin Datos</h4>
    </template>
  </v-data-table>
</template>
<script>
import TableHeaderButtons from "../components/shared/TableHeaderButtons.vue";
export default {
  components: { TableHeaderButtons },
  data: () => ({
    dialog: false,
    dialogDelete: false,
    headers: [
      {
        text: "Categoria",
        align: "start",
        sortable: false,
        value: "breadcrumbs_category",
      },

      { text: "Actions", value: "actions", align: "center", sortable: false },
    ],
    categories: [],
    editedIndex: -1,
    editedItem: {
      name: "",
      parent: null,
    },
    defaultItem: {
      name: "",
      parent: null,
    },
    options: {
      categories: [],
    },
    filters: {},
    search: "",
    options: {
      products: [],
      agencies: [],
    },
    totalItems: 0,
    pagination: {
      itemsPerPage: 10,
      page: 1,
    },
  }),

  computed: {
    formTitle() {
      return this.editedIndex === -1 ? "Nueva Categoria" : "Editar Categoria";
    },
  },

  watch: {
    dialog(val) {
      val || this.close();
    },
    dialogDelete(val) {
      val || this.closeDelete();
    },
    pagination: {
      handler: _.debounce(function () {
        this.initialize();
      }, 999),
      deep: true,
    },
    filters: {
      handler: _.debounce(function (v) {
        this.pagination.page = 1;
        this.initialize();
      }, 999),
      deep: true,
    },
    search: _.debounce(function (v) {
      this.pagination.page = 1;
      this.initialize();
    }, 999),
  },

  mounted() {
    const _this = this;
    _this.initialize();
    _this.$store.commit("setBreadcrumbs", [{ label: "Categorias", name: "" }]);
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
          data: { categories, options },
          message,
        },
      } = await axios.get("/admin/products/categories", { params });
      _this.categories = categories.data;
      _this.options = options;
      _this.totalItems = categories.total;
      _this.pagination.totalItems = categories.total;

      _this.$store.commit("showSnackbar", {
        message: message,
        color: "success",
        duration: 3000,
      });
    },

    editItem(item) {
      this.editedId = item.id;
      this.editedItem = Object.assign({}, item);
      this.dialog = true;
    },

    deleteItem(item) {
      this.editedId = item.id;
      this.dialogDelete = true;
    },

    async deleteItemConfirm() {
      const {
        data: { message },
      } = await axios.delete(`/admin/products/categories/${this.editedId}`);
      this.$store.commit("showSnackbar", {
        message: message,
        color: "error",
        duration: 5000,
      });
      this.initialize();
      this.closeDelete();
    },

    close() {
      this.dialog = false;
      this.$nextTick(() => {
        this.editedItem = Object.assign({}, this.defaultItem);
        this.editedId = -1;
      });
    },

    closeDelete() {
      this.dialogDelete = false;
      this.$nextTick(() => {
        this.editedItem = Object.assign({}, this.defaultItem);
        this.editedId = -1;
      });
    },

    async save() {
      // if (!this.$refs.form.$refs.formProductInventory.validate()) return;
      let payload = {
        ...this.editedItem,
      };
      if (this.editedId > -1) {
        try {
          const { data: message } = await axios.put(
            `/admin/products/categories/${this.editedId}`,
            payload
          );
          _this.$store.commit("showSnackbar", {
            message: message,
            color: "success",
            duration: 3000,
          });
        } catch (error) {
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
        }
      } else {
        try {
          const { data: message } = await axios.post(
            "/admin/products/categories",
            payload
          );
          _this.$store.commit("showSnackbar", {
            message: message,
            color: "success",
            duration: 3000,
          });
        } catch (error) {
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
        }
      }
      this.initialize();
      this.close();
    },
  },
};
</script>