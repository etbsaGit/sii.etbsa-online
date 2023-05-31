<template>
  <v-data-table
    :headers="headers"
    :items="items"
    :options.sync="pagination"
    :server-items-length="totalItems"
    class="text-uppercase caption"
    calculate-widths
    fixed-header
    caption
    dense
  >
    <template #top>
      <v-toolbar flat>
        <v-text-field
          v-model="search"
          label="Buscar"
          append-icon="mdi-magnify"
          hide-details
          clearable
          outlined
          filled
          dense
        ></v-text-field>
        <v-divider class="mx-4" inset vertical></v-divider>
        <v-spacer></v-spacer>
        <table-header-buttons
          :reloadTable="reloadTable"
          :exportTable="exportTable"
        ></table-header-buttons>
      </v-toolbar>
      <v-toolbar flat>
        <v-toolbar-title>Lista de Proveedores</v-toolbar-title>
        <v-divider class="mx-4" inset vertical></v-divider>
        <v-spacer></v-spacer>
        <v-btn
          v-if="$gate.allow('createSupplier', 'compras')"
          color="primary"
          class="mb-2"
          :to="{ name: 'suppliers.create' }"
        >
          Registrar Proveedor
        </v-btn>
        <dialog-component
          :show="dialogShow"
          @close="dialogShow = false"
          :maxWidth="600"
          :fullscreen="$vuetify.breakpoint.mobile"
          closeable
          :title="formTitle"
        >
          <show v-if="dialogShow" :supplierId="editedId"></show>
        </dialog-component>
      </v-toolbar>
    </template>
    <template #[`item.isActive`]="{ value }">
      <v-icon v-if="value == 1" color="green">mdi-check-circle</v-icon>
      <v-icon v-else color="grey">mdi-alert-circle</v-icon>
    </template>
    <template #[`item.actions`]="{ item }">
      <!-- <v-icon @click="editItem(item)">
        mdi-information-outline
      </v-icon> -->
      <v-btn
        icon
        :to="{ name: 'suppliers.edit', params: { supplierId: item.id } }"
      >
        <v-icon>
          mdi-pencil
        </v-icon>
      </v-btn>
    </template>
  </v-data-table>
</template>
<script>
import DialogComponent from "@admin/components/DialogComponent.vue";
import DialogModal from "@admin/components/DialogModal.vue";
import TableHeaderButtons from "@admin/components/shared/TableHeaderButtons.vue";
import CreateSupplier from "./Create.vue";
import EditSupplier from "./Edit.vue";
import Show from "./Show.vue";
export default {
  components: {
    CreateSupplier,
    EditSupplier,
    DialogModal,
    TableHeaderButtons,
    DialogComponent,
    Show,
  },
  data() {
    return {
      dialogCreate: false,
      dialogShow: false,
      dialogDelete: false,
      editedId: -1,
      headers: [
        { value: "actions", align: "center", sortable: false },
        {
          text: "Num. Equip",
          align: "start",
          sortable: false,
          value: "code_equip",
        },
        {
          text: "Proveedor",
          align: "start",
          value: "business_name",
        },
        { text: "RFC", value: "rfc" },
        {
          text: "Validado",
          value: "isActive",
          align: "center",
          sortable: false,
        }
        // { text: "Email", value: "email", sortable: false },
      ],
      editedId: -1,
      items: [],
      search: null,
      totalItems: 0,
      pagination: {
        itemsPerPage: 10,
        page: 1,
      },
    };
  },
  mounted() {
    const _this = this;
    // _this.reloadTable();
    _this.$eventBus.$on(["SUPPLIER_REFRESH"], () => {
      _this.reloadTable();
    });
    _this.$eventBus.$on(["CLOSE_DIALOG"], () => {
      _this.dialogCreate = false;
      _this.dialogShow = false;
      _this.dialogDelete = false;
      _this.editedId = -1;
    });
    _this.$store.commit("setBreadcrumbs", [{ label: "Proveedores", name: "" }]);
  },
  computed: {
    formTitle() {
      return this.editedId === -1 ? "Nuevo Proveedor" : "Detalle Proveedor";
    },
  },

  methods: {
    reloadTable() {
      this.loadSupplier(() => {});
    },
    editItem(item) {
      this.editedId = item.id;
      this.dialogShow = true;
    },

    deleteItem(item) {
      this.editedId = this.desserts.indexOf(item);
      this.editedItem = Object.assign({}, item);
      this.dialogDelete = true;
    },

    deleteItemConfirm() {
      this.desserts.splice(this.editedId, 1);
      this.closeDelete();
    },

    closeDelete() {
      this.dialogDelete = false;
      this.$nextTick(() => {
        this.editedItem = Object.assign({}, this.defaultItem);
        this.editedId = -1;
      });
    },

    async loadSupplier(cb) {
      const _this = this;
      let params = {
        search: _this.search,
        order_sort: _this.pagination.sortDesc[0] ? "asc" : "desc",
        order_by: _this.pagination.sortBy[0] || "id",
        page: _this.pagination.page,
        per_page: _this.pagination.itemsPerPage,
      };
      await axios
        .get("/admin/suppliers", { params: params })
        .then(function(response) {
          let Response = response.data.data;
          _this.items = Response.data;
          _this.totalItems = Response.total;
          _this.pagination.totalItems = Response.total;
          (cb || Function)();
        });
    },
    exportTable() {
      const _this = this;
      let params = {
        search: _this.search,
        paginate: "no",
      };
      axios
        .get("/admin/supplier-export", {
          params: params,
          responseType: "blob",
        })
        .then((res) => {
          const url = window.URL.createObjectURL(new Blob([res.data]));
          const link = document.createElement("a");
          link.href = url;
          link.setAttribute("download", "proveedores.xlsx"); //or any other extension
          document.body.appendChild(link);
          link.click();
        })
        .catch(function(error) {
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
  },
  watch: {
    pagination: {
      handler: _.debounce(function() {
        this.reloadTable();
      }, 999),
      deep: true,
    },
    search: _.debounce(function(v) {
      this.reloadTable();
    }, 999),
  },
};
</script>
