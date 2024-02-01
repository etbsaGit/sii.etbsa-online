<template>
  <v-data-table
    :headers="headers"
    :items="items"
    :options.sync="pagination"
    :server-items-length="totalItems"
    class="blue--text overline"
    fixed-header
    height="49vh"
    caption
    dense
  >
    <template #top>
      <search-panel
        :rightDrawer="rightDrawer"
        @cancelSearch="cancelSearch"
        @resetFilter="resetFilter"
      >
        <v-form ref="formFilter"> </v-form>
      </search-panel>
      <v-card class="d-flex justify-end align-center flex-wrap px-3 py-1" flat>
        <v-card
          flat
          class="d-flex d-flex justify-space-between align-center flex-wrap py-2"
          :class="'flex-grow-1 flex-shrink-0'"
        >
          <v-text-field
            v-model="search"
            label="Buscar"
            prepend-icon="mdi-magnify"
            hide-details
            clearable
            outlined
            filled
            dense
          ></v-text-field>
        </v-card>
        <v-spacer></v-spacer>
        <v-divider class="mx-2" inset vertical></v-divider>
        <table-header-buttons
          :updateSearchPanel="updateSearchPanel"
          :reloadTable="reloadTable"
        ></table-header-buttons>
      </v-card>
      <v-toolbar flat>
        <v-toolbar-title>Clientes</v-toolbar-title>
        <v-divider class="mx-4" inset vertical></v-divider>
        <v-spacer></v-spacer>
        <v-btn
          color="primary"
          class="mb-2"
          @click="dialogCreate = true"
          rounded
        >
          Registrar nuevo Cliente
        </v-btn>
      </v-toolbar>
      <dialog-component
        :show="dialogShow"
        @close="dialogShow = false"
        closeable
        fullscreen
        title="Detalle Cliente:"
      >
        <show-customer v-if="dialogShow" :item-id="editedId"></show-customer>
      </dialog-component>
      <dialog-component
        :show="dialogCreate"
        @close="dialogCreate = false"
        closeable
        :fullscreen="$vuetify.breakpoint.mobile"
        :title="`Registrar Cliente`"
        max-width="600"
      >
        <create-customer @cancel="dialogCreate = false"></create-customer>
      </dialog-component>
    </template>
    <template #[`item.action`]="{ item }">
      <v-icon class="blue--text" @click="editItem(item)">
        mdi-information-outline
      </v-icon>
    </template>
    <template #[`item.full_name`]="{ value }">
      <span style="font-size: smaller;">{{ value }}</span>
    </template>
    <template #[`item.is_moral`]="{ value }">
      <span style="font-size: smaller;">
        Persona {{ value ? "Moral" : "Fisica" }}
      </span>
    </template>
  </v-data-table>
</template>

<script>
import SearchPanel from "@admin/components/shared/SearchPanel.vue";
import TableHeaderButtons from "@admin/components/shared/TableHeaderButtons.vue";
import DialogComponent from "@admin/components/DialogComponent.vue";
import ShowCustomer from "./Show.vue";
import CreateCustomer from "./Create.vue";
export default {
  name: "IndexCustomers",
  components: {
    TableHeaderButtons,
    SearchPanel,
    DialogComponent,
    ShowCustomer,
    CreateCustomer,
  },
  mounted() {
    // this.loadCustomers(() => {});
    // this.$store.commit("setBreadcrumbs", [
    //   { label: "Requision de Personal", name: "" },
    // ]);
  },
  data() {
    return {
      // menu: false,
      valid: true,
      dialogCreate: false,
      dialogEdit: false,
      dialogShow: false,
      showSearchPanel: false,
      dialogDelete: false,
      headers: [
        {
          text: "Nombre Cliente",
          align: "left",
          sortable: false,
          fixed: true,
          value: "full_name",
        },
        {
          text: "RFC",
          align: "left",
          sortable: false,
          fixed: true,
          value: "rfc",
        },
        {
          text: "Tipo de Pesona",
          align: "left",
          sortable: false,
          fixed: true,
          value: "is_moral",
        },
        {
          align: "center",
          sortable: false,
          fixed: true,
          value: "action",
        },
      ],
      editedId: -1,
      editedItem: {},
      items: [],
      search: null,
      filters: {
        folio: null,
        supplier: null,
        agencie: null,
        uso_cfdi: null,
        metodo_pago: null,
        forma_pago: null,
        estatus: "todos",
      },
      options: {
        estatus: [
          { text: "Pendientes", value: "pendiente" },
          { text: "Autorizados", value: "autorizado" },
          { text: "En Reclutamiento", value: "reclutamiento" },
          { text: "Cubierta", value: "cubierta" },
          { text: "Todos", value: "todos" },
        ],
      },
      colors: {
        pendiente: "green",
        reclutamiento: "pink",
        cubierta: "blue",
      },
      totalItems: 0,
      pagination: {
        itemsPerPage: 10,
        page: 1,
      },
    };
  },
  computed: {
    rightDrawer: {
      get() {
        return this.showSearchPanel;
      },
      set(_showSearchPanel) {
        this.showSearchPanel = _showSearchPanel;
      },
    },
  },
  watch: {
    pagination: {
      handler: _.debounce(function () {
        this.reloadTable();
      }, 999),
      deep: true,
    },
    filters: {
      handler: _.debounce(function (v) {
        this.reloadTable();
      }, 999),
      deep: true,
    },
    search: _.debounce(function (v) {
      this.reloadTable();
    }, 999),
  },
  methods: {
    updateSearchPanel() {
      this.rightDrawer = !this.rightDrawer;
    },
    reloadTable() {
      this.loadCustomers(() => {});
    },
    cancelSearch() {
      this.showSearchPanel = false;
    },
    editItem(item) {
      this.editedId = item.id;
      this.editedItem = item;
      this.dialogShow = true;
    },
    deleteItem(item) {
      this.editedId = item.id;
      this.editedItem = Object.assign({}, item);
      this.dialogDelete = true;
    },

    closeDelete() {
      this.$nextTick(() => {
        // this.editedItem = Object.assign({}, this.defaultItem);
        this.editedItem = { ...this.defaultItem };
        this.editedId = -1;
      });
      this.dialogDelete = false;
    },

    async loadCustomers() {
      const _this = this;
      let params = {
        ..._this.filters,
        search: _this.search,
        page: _this.pagination.page,
        per_page: _this.pagination.itemsPerPage,
      };
      await axios
        .get("/admin/customers", { params: params })
        .then(function (response) {
          let { data, total } = response.data.data;
          _this.items = data;
          _this.totalItems = total;
          _this.pagination.totalItems = total;
        });
    },

    resetFilter() {
      const _this = this;
      _this.$refs.formFilter.reset();
      _this.pagination.itemsPerPage = 10;
      _this.pagination.page = 1;
    },
  },
};
</script>

<style scoped></style>
