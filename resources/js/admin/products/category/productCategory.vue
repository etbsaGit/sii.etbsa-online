<template>
  <div>
    <v-toolbar flat class="text-h4">
      <v-icon left x-large>mdi-tractor-variant</v-icon>Categorias
      <v-spacer></v-spacer>
      <v-text-field
        v-model="filters.search"
        outlined
        dense
        prepend-icon="mdi-magnify"
        label="Buscar"
        class="mr-3"
        hide-details
      ></v-text-field>
      <v-btn @click="create" color="primary" dark>
        Agregar Categoria
      </v-btn>
    </v-toolbar>
    <v-spacer />
    <v-data-table
      v-bind:headers="headers"
      :options.sync="pagination"
      :items="items"
      :server-items-length="totalItems"
      fixed-header
      dense
    >
      <template #[`item.models`]="{value}">
        <v-chip v-for="model in value" :key="model.id" small>
          {{ model.name }}
        </v-chip>
      </template>
      <template #[`item.action`]="{ item }">
        <v-icon right class="green--text" @click="editItem(item)">
          mdi-pencil
        </v-icon>

        <v-icon left class="red--text" @click="deleteItem(item.id)">
          mdi-trash-can
        </v-icon>
      </template>
      <template #[`item.price`]="{item}">
        {{ item.price | money }}
      </template>
    </v-data-table>
    <v-dialog v-model="dialog" max-width="500" persistent>
      <v-card>
        <v-card-title>{{ formTitle }}</v-card-title>
        <v-card-text>
          <v-form lazy-validation ref="form" v-model="valid">
            <v-text-field
              v-model="form.name"
              label="Nombre Categoria"
              :rules="[(v) => !!v || 'Es Requerido']"
            >
            </v-text-field>
          </v-form>
        </v-card-text>
        <v-card-actions>
          <v-btn text @click="dialog = false"> Cerrar </v-btn>
          <v-spacer />
          <v-btn dark @click="submit">Guardar</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
    <v-dialog v-model="dialogDelete" max-width="500px">
      <v-card>
        <v-card-title class="text-h5">
          Modelos de Categoria
        </v-card-title>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn color="blue darken-1" text @click="closeDelete">Cancel</v-btn>
          <v-btn color="blue darken-1" text @click="deleteItemConfirm">
            OK
          </v-btn>
          <v-spacer></v-spacer>
        </v-card-actions>
      </v-card>
    </v-dialog>
    <v-dialog v-model="dialogDelete" max-width="500px">
      <v-card>
        <v-card-title class="text-h5">
          Seguro en Eliminar el Registro?
        </v-card-title>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn color="blue darken-1" text @click="closeDelete">Cancel</v-btn>
          <v-btn color="blue darken-1" text @click="deleteItemConfirm">
            OK
          </v-btn>
          <v-spacer></v-spacer>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </div>
</template>
<script>
// import EditAmsEquipment from "./Edit.vue";
// import CreateAmsEquipment from "./Create.vue";
export default {
  // components: { EditAmsEquipment, CreateAmsEquipment },
  data() {
    return {
      form: {
        name: "",
      },
      headers: [
        {
          text: "ID",
          value: "id",
          align: "left",
          sortable: false,
        },
        {
          text: "Nombre Categoria",
          value: "name",
          align: "left",
          sortable: false,
        },
        {
          text: "Catalogo de Modelos",
          value: "models",
          align: "left",
          sortable: false,
        },
        { text: "Action", value: "action", align: "left", sortable: false },
      ],
      items: [],
      totalItems: 0,
      pagination: {
        rowsPerPage: 10,
      },
      valid: true,
      dialog: false,
      dialogShowModel: false,
      dialogDelete: false,
      editedId: -1,

      filters: {
        search: "",
      },
    };
  },

  watch: {
    pagination: {
      handler: _.debounce(function (v) {
        this.loadProductCategory(() => {});
      }, 700),
      deep: true,
    },
    filters: {
      handler: _.debounce(function (v) {
        this.loadProductCategory(() => {});
      }, 700),
      deep: true,
    },
    dialog(val) {
      val || this.close();
    },
    dialogDelete(val) {
      val || this.closeDelete();
    },
  },
  computed: {
    formTitle() {
      return this.editedId === -1 ? "Agregar Nuevo" : "Editar";
    },
  },
  methods: {
    async loadProductCategory(cb) {
      const _this = this;

      let params = {
        ..._this.filters,
        order_sort: _this.pagination.sortDesc[0] ? "asc" : "desc",
        order_by: _this.pagination.sortBy[0] || "id",
        search: _this.filters.search,
        page: _this.pagination.page,
        per_page: _this.pagination.itemsPerPage,
      };

      await axios
        .get("/admin/products/category", { params })
        .then((response) => {
          _this.items = response.data.data.data;
          _this.totalItems = response.data.data.total;
          _this.pagination.totalItems = response.data.data.total;
          (cb || Function)();
        });
    },

    editItem(item) {
      this.dialog = true;
      this.editedId = item.id;
      this.form.name = item.name;
      this.$refs.form.resetValidation();
    },

    deleteItem(item) {
      this.editedId = item;
      this.dialogDelete = true;
    },
    deleteItemConfirm() {
      // Axios Delete
      this.closeDelete();
    },
    close() {
      this.dialog = false;
      this.$nextTick(() => {
        this.editedId = -1;
        this.loadProductCategory();
      });
    },
    closeDelete() {
      this.dialogDelete = false;
      this.$nextTick(() => {
        this.editedId = -1;
        this.loadProductCategory();
      });
    },
    create() {
      this.dialog = true;
      this.editedId = -1;
      this.$refs.form.reset();
      this.$refs.form.resetValidation();
    },
    async submit() {
      const _this = this;
      if (!this.$refs.form.validate()) {
        return;
      }
      if (_this.editedId === -1) {
        //create
        console.log("Store", this.form);
        await axios
          .post("/admin/products/category", _this.form)
          .then((response) => {
            _this.$store.commit("showSnackbar", {
              message: response.data.message,
              color: "success",
              duration: 3000,
            });
            _this.loadProductCategory();
            _this.close();
            // _this.$emit("submit");
          })
          .catch(function (error) {
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
        //update
        console.log("Update", this.form, this.editedId);
        await axios
          .put(`/admin/products/category/${_this.editedId}`, _this.form)
          .then(function (response) {
            _this.$store.commit("showSnackbar", {
              message: response.data.message,
              color: "success",
              duration: 3000,
            });
            _this.loadProductCategory();
            _this.close();
            // _this.$emit("submit");
          })
          .catch(function (error) {
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
      }
    },
  },
};
</script>
<style></style>
