<template>
  <div>
    <v-toolbar flat class="text-h4">
      <v-icon left x-large>mdi-tractor-variant</v-icon>Catalogo de Equipos
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
      <v-btn @click="dialog = true" color="primary" dark>
        Agregar Implemento
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
      <template #[`item.action`]="{ item }">
        <v-icon right class="green--text" @click="editItem(item.id)">
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
          <edit-ams-equipment
            v-if="editedId != -1"
            :editedId="editedId"
            @submit="close"
          ></edit-ams-equipment>
          <create-ams-equipment v-else @submit="close"></create-ams-equipment>
        </v-card-text>
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
import EditAmsEquipment from "./Edit.vue";
import CreateAmsEquipment from "./Create.vue";
export default {
  components: { EditAmsEquipment, CreateAmsEquipment },
  data() {
    return {
      headers: [
        {
          text: "Categoria",
          value: "category",
          align: "left",
          sortable: false,
        },
        {
          text: "Nombre o Modelo",
          value: "name",
          align: "left",
          sortable: false,
        },
        {
          text: "U. Medida",
          value: "unit",
          align: "left",
          sortable: false,
        },
        { text: "Valor", value: "value", align: "left", sortable: false },
        { text: "Precio", value: "price", align: "left", sortable: false },
        { text: "Action", value: "action", align: "left", sortable: false },
      ],
      items: [],
      totalItems: 0,
      pagination: {
        rowsPerPage: 10,
      },
      dialog: false,
      dialogDelete: false,
      editedId: -1,

      filters: {
        search: "",
      },
    };
  },
  mounted() {
    const _this = this;
    _this.$store.commit("setBreadcrumbs", [
      { label: "AMS Implementos", name: "" },
    ]);
  },
  watch: {
    pagination: {
      handler: _.debounce(function (v) {
        this.loadAmsEquipments(() => {});
      }, 700),
      deep: true,
    },
    filters: {
      handler: _.debounce(function (v) {
        this.loadAmsEquipments(() => {});
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
    async loadAmsEquipments(cb) {
      const _this = this;

      let params = {
        ..._this.filters,
        order_sort: _this.pagination.sortDesc[0] ? "asc" : "desc",
        order_by: _this.pagination.sortBy[0] || "id",
        search: _this.filters.search,
        page: _this.pagination.page,
        per_page: _this.pagination.itemsPerPage,
      };

      await axios.get("/admin/ams-equipment", { params }).then((response) => {
        _this.items = response.data.data.data;
        _this.totalItems = response.data.data.total;
        _this.pagination.totalItems = response.data.data.total;
        (cb || Function)();
      });
    },
    editItem(item) {
      this.editedId = item;
      this.dialog = true;
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
        this.loadAmsEquipments();
      });
    },
    closeDelete() {
      this.dialogDelete = false;
      this.$nextTick(() => {
        this.editedId = -1;
        this.loadAmsEquipments();
      });
    },
  },
};
</script>
<style></style>
