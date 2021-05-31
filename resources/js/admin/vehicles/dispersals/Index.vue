<template>
  <v-data-table
    :headers="headers"
    :items="items"
    :options.sync="pagination"
    :server-items-length="totalItems"
    class="text-truncate blue--text overline"
    calculate-widths
    fixed-header
    caption
    dense
    dark
  >
    <template v-slot:top>
      <search-panel
        :rightDrawer="rightDrawer"
        @cancelSearch="cancelSearch"
        @resetFilter="resetFilter"
      >
        <v-form ref="formFilter"> </v-form>
      </search-panel>
      <v-card
        class="d-flex justify-end align-center flex-wrap px-3 py-1"
        dark
        flat
      >
        <v-card
          flat
          class="d-flex d-flex justify-space-between align-center flex-wrap py-2"
          :class="'flex-grow-1 flex-shrink-0'"
        >
          <v-text-field
            v-model="search"
            label="Buscar"
            class="pa-2 flex-grow-1 flex-shrink-0"
            prepend-icon="mdi-magnify"
            hide-details
            clearable
            outlined
            filled
            dense
          ></v-text-field>
          <v-select
            v-model="filters.estatus"
            :items="options.estatus"
            label="Estatus"
            class="pa-2"
            outlined
            filled
            hide-details
            dense
          ></v-select>
        </v-card>
        <v-spacer></v-spacer>
        <v-divider class="mx-2" inset vertical></v-divider>
        <table-header-buttons
          :updateSearchPanel="updateSearchPanel"
          :reloadTable="reloadTable"
        ></table-header-buttons>
      </v-card>
      <v-toolbar flat>
        <v-toolbar-title>Disperciones de Combustible</v-toolbar-title>
        <v-divider class="mx-4" inset vertical></v-divider>
        <v-spacer></v-spacer>
        <v-btn
          color="primary"
          class="mb-2"
          :to="{ name: 'vehicle.dispersal.create' }"
          rounded
          dark
        >
          Solicitar Dispercion
        </v-btn>
      </v-toolbar>
      <dialog-component
        :show="dialogEdit"
        @close="dialogEdit = false"
        closeable
        max-width="725"
        :fullscreen="$vuetify.breakpoint.mobile"
        :title="`Matricula: ${editedItem.vehicle.matricula}`"
      >
        <template #actions>
          <v-chip
            label
            small
            :color="getColorByStatus(editedItem.estatus.key)"
            text-color="white"
          >
            {{ editedItem.estatus.title }}
          </v-chip>
        </template>
        <show-dispersal :dispersalId="editedId"></show-dispersal>
      </dialog-component>
    </template>
    <template v-slot:[`item.actions`]="{ item }">
      <v-menu offset-x transition="slide-x-transition" rounded="r-xl">
        <template v-slot:activator="{ on, attrs }">
          <v-btn icon v-bind="attrs" v-on="on">
            <v-icon>mdi-dots-vertical</v-icon>
          </v-btn>
        </template>
        <v-list shaped dense>
          <v-list-item-group>
            <v-list-item @click="editItem(item)">
              <v-list-item-icon>
                <v-icon class="blue--text">mdi-information-outline</v-icon>
              </v-list-item-icon>
              <v-list-item-content>
                <v-list-item-title>Info Dispercion</v-list-item-title>
              </v-list-item-content>
            </v-list-item>
          </v-list-item-group>
        </v-list>
      </v-menu>
    </template>
    <!-- <template v-slot:[`item.mileages`]="{ item }">
      <v-list-item dense class="pa-0">
        <v-list-item-content class="pa-0 caption">
          <v-list-item-title>
            {{ item.actual_mileage }} kms - actual
          </v-list-item-title>
          <v-list-item-subtitle>
            {{ item.last_mileage }} kms - ultimo
          </v-list-item-subtitle>
        </v-list-item-content>
      </v-list-item>
    </template> -->
    <template v-slot:[`item.fuel_lts`]="{ item }">
      {{ item.fuel_lts }} - {{ item.vehicle.fuel }}
    </template>
    <template v-slot:[`item.amount`]="{ value }">
      {{ value | money }}
    </template>
    <template v-slot:[`item.estatus`]="{ item }">
      <v-chip
        label
        small
        :color="getColorByStatus(item.estatus.key)"
        text-color="white"
      >
        {{ item.estatus.title }}
      </v-chip>
    </template>
    <template v-slot:[`item.created_at`]="{ value }">
      {{ $appFormatters.formatDate(value, "DD MMM YYYY") }}
    </template>
  </v-data-table>
</template>

<script>
import SearchPanel from "@admin/components/shared/SearchPanel.vue";
import TableHeaderButtons from "@admin/components/shared/TableHeaderButtons.vue";
import DialogComponent from "@admin/components/DialogComponent.vue";
import ShowDispersal from "./Edit.vue";
export default {
  components: {
    TableHeaderButtons,
    SearchPanel,
    DialogComponent,
    ShowDispersal,
  },
  mounted() {
    const _this = this;
    _this.reloadTable();
    _this.loadOptions();
    _this.$eventBus.$on(["VEHICLE_REFRESH"], () => {
      _this.reloadTable();
    });
    _this.$eventBus.$on(["CLOSE_DIALOG"], () => {
      _this.closeDialog();
    });
  },
  beforeUpdate() {
    this.$store.commit("setBreadcrumbs", [
      { label: "Flotilla", to: { name: "vehicle.list" } },
      { label: "Disperciones" },
    ]);
  },
  data() {
    return {
      valid: true,
      dialogCreate: false,
      dialogEdit: false,
      showSearchPanel: false,
      dialogDelete: false,
      headers: [
        { text: "", value: "actions", sortable: false },
        {
          text: "Folio",
          align: "left",
          sortable: false,
          value: "id",
          fixed: true,
        },
        {
          text: "Vehiculo",
          align: "left",
          sortable: false,
          value: "vehicle.matricula",
          fixed: true,
        },
        {
          text: "Motivo",
          align: "left",
          sortable: false,
          value: "reason",
          fixed: true,
        },
        {
          text: "Litros / Combus.",
          align: "center",
          sortable: false,
          value: "fuel_lts",
          fixed: true,
        },
        {
          text: "Importe",
          align: "right",
          sortable: false,
          value: "amount",
          fixed: true,
        },
        {
          text: "Solicitante",
          align: "center",
          sortable: false,
          value: "solicitante.name",
          fixed: true,
        },
        {
          text: "Estatus",
          align: "center",
          sortable: false,
          value: "estatus",
          fixed: true,
        },
        {
          text: "Fecha Creacion",
          align: "center",
          sortable: false,
          value: "created_at",
          fixed: true,
        },
      ],
      editedId: -1,
      editedItem: {
        vehicle: {
          matricula: "",
        },
        estatus: {
          key: "",
          title: "",
        },
      },
      items: [],
      search: null,
      filters: {
        estatus: "todos",
      },
      options: {
        users: [],
        agencies: [],
        estatus: [
          { text: "Pendientes", value: "pendiente" },
          { text: "Dispersados", value: "flotilla.dispersado" },
          { text: "Todos", value: "todos" },
        ],
      },
      colors: {
        pendiente: "blue",
        autorizado: "green",
        "flotilla.dispersado": "purple",
        denegar: "red",
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
    getColorByStatus(status) {
      return this.colors[status];
    },
    updateSearchPanel() {
      this.rightDrawer = !this.rightDrawer;
    },
    reloadTable() {
      this.loadVehicleDispersals(() => {});
    },
    cancelSearch() {
      this.showSearchPanel = false;
    },
    editItem(item) {
      this.editedId = item.id;
      this.editedItem = item;
      this.dialogEdit = true;
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
    closeDialog() {
      this.dialogEdit = false;
      this.dialogDelete = false;
    },
    async loadVehicleDispersals(cb) {
      const _this = this;
      let params = {
        ..._this.filters,
        search: _this.search,
        page: _this.pagination.page,
        per_page: _this.pagination.itemsPerPage,
      };
      await axios
        .get("/admin/vehicle-dispersal", { params: params })
        .then(function (response) {
          let Response = response.data.data;
          _this.items = Response.data;
          _this.totalItems = Response.total;
          _this.pagination.totalItems = Response.total;
          (cb || Function)();
        });
    },
    async loadOptions(cb) {
      const _this = this;
      await axios
        .get(`/admin/vehicles/resources/options`)
        .then(function (response) {
          let Data = response.data.data;
          _this.options.users = Data.users;
          _this.options.agencies = Data.agencies;
          (cb || Function)();
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
