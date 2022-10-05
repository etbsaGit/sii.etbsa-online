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
  >
    <template v-slot:top>
      <search-panel
        :rightDrawer="rightDrawer"
        @cancelSearch="cancelSearch"
        @resetFilter="resetFilter"
      >
        <v-form ref="formFilter">
          <v-row class="mr-2 offset-1 overline" dense>
            <v-col cols="12">
              <v-dialog
                ref="dialog"
                v-model="modal"
                :return-value.sync="filters.dates"
                persistent
                width="290px"
              >
                <template v-slot:activator="{ on, attrs }">
                  <v-text-field
                    v-model="filters.dates"
                    label="Fecha de Dispercion"
                    prepend-icon="mdi-calendar"
                    readonly
                    v-bind="attrs"
                    v-on="on"
                    clearable
                  ></v-text-field>
                </template>
                <!-- <v-date-picker v-model="date" scrollable> -->
                <v-date-picker v-model="dates" range scrollable>
                  <v-spacer></v-spacer>
                  <v-btn text color="primary" @click="modal = false">
                    Cancel
                  </v-btn>
                  <v-btn text color="primary" @click="$refs.dialog.save(dates)">
                    OK
                  </v-btn>
                </v-date-picker>
              </v-dialog>
            </v-col>
            <!-- <v-col cols="12" xs="12" class="py-0">
              <v-text-field
                v-model="filters.matricula"
                prepend-icon="mdi-magnify"
                type="text"
                label="Matricula"
                clearable
              ></v-text-field>
            </v-col>
            <v-col cols="12" xs="12" class="py-0">
              <v-text-field
                v-model="filters.serie"
                prepend-icon="mdi-magnify"
                type="text"
                label="Serie"
                clearable
              ></v-text-field>
            </v-col> -->
            <v-col cols="12" xs="12" class="py-0">
              <v-text-field
                v-model="filters.ticket_card"
                prepend-icon="mdi-magnify"
                type="text"
                label="TicketCard"
                clearable
              ></v-text-field>
            </v-col>
            <v-col cols="12" xs="12" class="py-0">
              <v-select
                v-model="filters.agencie"
                :items="options.agencies"
                item-text="title"
                item-value="id"
                label="Sucursal"
                prepend-icon="mdi-filter-variant"
                hide-details
                outlined
                filled
                clearable
                dense
                class="mb-2"
              ></v-select>
            </v-col>
            <v-col cols="12" xs="12" class="py-0">
              <v-select
                v-model="filters.reason_dispersal"
                :items="options.reason_dispersal"
                label="Motivo"
                prepend-icon="mdi-filter-variant"
                hide-details
                outlined
                filled
                clearable
                dense
                class="mb-2"
              ></v-select>
            </v-col>
            <v-col cols="12">
              <!-- <v-autocomplete
                v-model="filters.responsable"
                :items="options.employees"
                item-text="full_name"
                item-value="user.id"
                placeholder="Responsable"
                prepend-icon="mdi-filter-variant"
                hide-details
                outlined
                filled
                clearable
                dense
                class="mb-2"
              ></v-autocomplete> -->
            </v-col>
          </v-row>
        </v-form>
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
        >
          Solicitar Dispercion
        </v-btn>
      </v-toolbar>
      <dialog-component
        :show="dialogShow"
        @close="dialogShow = false"
        closeable
        fullscreen
        :title="`Matricula: ${editedItem.vehicle.matricula}`"
      >
        <template #actions>
          <span class="overline">Estatus:</span>
          <v-chip
            :color="getColorByStatus(editedItem.estatus.key)"
            class="overline ml-2"
          >
            {{ editedItem.estatus.title }}
          </v-chip>
        </template>
        <dispersal-show :item-id="editedId"></dispersal-show>
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
                <v-list-item-title>Detalle Dispercion</v-list-item-title>
              </v-list-item-content>
            </v-list-item>
          </v-list-item-group>
        </v-list>
      </v-menu>
    </template>
    <template v-slot:[`item.vehicle.matricula`]="{ item }">
      <v-list-item dense class="pa-0">
        <v-list-item-content class="pa-0 caption">
          <v-list-item-title class="blue--text">{{
            item.vehicle.matricula
          }}</v-list-item-title>
          <v-list-item-subtitle>
            {{ item.vehicle.serie }}
          </v-list-item-subtitle>
        </v-list-item-content>
      </v-list-item>
    </template>
    <template v-slot:[`item.fuel`]="{ item }">
      <v-list-item dense class="pa-0">
        <v-list-item-content class="pa-0 caption">
          <v-list-item-title>{{ item.fuel_liters }} Lts</v-list-item-title>
          <v-list-item-subtitle>
            {{ item.fuel.name }}
          </v-list-item-subtitle>
        </v-list-item-content>
      </v-list-item>
    </template>
    <template v-slot:[`item.ticket_card`]="{ value }">
      {{ value }}
    </template>
    <template v-slot:[`item.amount`]="{ item }">
      {{ (item.fuel_liters * item.fuel_cost_liter) | money }}
    </template>
    <template v-slot:[`item.dispatched_amount`]="{ item }">
      {{ item.dispatched_amount | money }}
    </template>
    <template v-slot:[`item.vehicle.ticket.account_balance`]="{ value }">
      {{ value | money }}
    </template>
    <template v-slot:[`item.cargo`]="{ item }">
      <v-list-item dense class="pa-0">
        <v-list-item-content class="pa-0 caption">
          <v-list-item-title>{{ item.agency.title }}</v-list-item-title>
          <v-list-item-subtitle>
            {{ item.department.title }}
          </v-list-item-subtitle>
        </v-list-item-content>
      </v-list-item>
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
    <template v-slot:[`item.date_to_disperse`]="{ value }" class="caption">
      {{ $appFormatters.formatDate(value, "DD MMM YYYY") }}
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
// import ShowDispersal from "./Edit.vue";
import DispersalShow from "./DispersalShow.vue";
export default {
  name: "VehicleDispersalList",
  components: {
    TableHeaderButtons,
    SearchPanel,
    DialogComponent,
    DispersalShow,
  },
  mounted() {
    const _this = this;
    _this.reloadTable();
    _this.loadOptions();
    _this.$eventBus.$on(["DISPERSAL_LIST_REFRESH"], () => {
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
      dates: [],
      modal: false,
      valid: true,
      dialogCreate: false,
      dialogEdit: false,
      dialogShow: false,
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
          text: "Matricula",
          align: "left",
          sortable: false,
          value: "vehicle.matricula",
          fixed: true,
        },
        {
          text: "Motivo",
          align: "left",
          sortable: false,
          value: "reason_dispersal",
          fixed: true,
        },
        {
          text: "Combustible",
          align: "left",
          sortable: false,
          value: "fuel",
          fixed: true,
        },

        {
          text: "Estatus",
          align: "center",
          sortable: false,
          value: "estatus",
          fixed: true,
          divider: true,
        },
        {
          text: "Ticket Card",
          align: "center",
          sortable: false,
          value: "ticket_card",
          fixed: true,
        },
        {
          text: "Importe a cargar",
          align: "right",
          sortable: false,
          value: "amount",
          fixed: true,
        },
        {
          text: "Saldo Despachado",
          align: "right",
          sortable: false,
          value: "dispatched_amount",
          fixed: true,
        },
        {
          text: "Saldo Actual",
          align: "right",
          sortable: false,
          value: "vehicle.ticket.account_balance",
          fixed: true,
          divider: true,
        },
        {
          text: "Cargo",
          align: "left",
          sortable: false,
          value: "cargo",
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
          text: "Fecha para Dispersar",
          align: "center",
          sortable: false,
          value: "date_to_disperse",
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
        matricula: null,
        agencie: null,
        department: null,
        dates: [],
        reason_dispersal: null,
      },
      options: {
        users: [],
        agencies: [],
        estatus: [
          { text: "Pendientes", value: "pendiente" },
          { text: "Autorizados", value: "autorizado" },
          { text: "Dispersados", value: "flotilla.dispersado" },
          { text: "Despachados", value: "flotilla.despachado" },
          { text: "Denegados", value: "denegar" },
          { text: "Todos", value: "todos" },
        ],
        reason_dispersal: [
          "Solicitud Semanal",
          "Servicio",
          "Viaje",
          "Adicional",
          "Otro",
        ],
      },
      colors: {
        pendiente: "blue",
        autorizado: "green",
        "flotilla.despachado": "purple",
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
    dateRangeText() {
      return this.filters.dates.join(" ~ ");
    },
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
