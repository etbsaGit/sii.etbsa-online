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
            class="pa-2"
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
        <v-toolbar-title>Flotilla de Vehiculos</v-toolbar-title>
        <v-divider class="mx-4" inset vertical></v-divider>
        <v-spacer></v-spacer>
        <v-btn
          color="primary"
          class="mb-2"
          :to="{ name: 'vehicle.create' }"
          rounded
          dark
        >
          Registrar Vehiculo
        </v-btn>
      </v-toolbar>
      <dialog-component
        :show="dialogEdit"
        @close="dialogEdit = false"
        fullscreen
        closeable
        :title="`Detalle del Vehiculo: ${editedItem.matricula}`"
      >
        <edit-vehicle :vehicleId="editedId"></edit-vehicle>
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
                <v-list-item-title>Info Unidad</v-list-item-title>
              </v-list-item-content>
            </v-list-item>
            <v-list-item
              v-if="item.can_dispersal"
              :to="{
                name: 'vehicle.dispersal.create',
                params: {
                  propVehicleId: item.id,
                },
              }"
            >
              <v-list-item-icon>
                <v-icon class="blue--text">mdi-gas-station-outline</v-icon>
              </v-list-item-icon>
              <v-list-item-content>
                <v-list-item-title>Solicitar Disperciones</v-list-item-title>
              </v-list-item-content>
            </v-list-item>
            <v-list-item
              :to="{
                name: 'vehicle.services.create',
                params: {
                  propVehicleId: item.id,
                },
              }"
            >
              <v-list-item-icon>
                <v-icon class="blue--text">mdi-tools</v-icon>
              </v-list-item-icon>
              <v-list-item-content>
                <v-list-item-title>Solicitar Servicios</v-list-item-title>
              </v-list-item-content>
            </v-list-item>
          </v-list-item-group>
        </v-list>
      </v-menu>
    </template>
    <template v-slot:[`item.model`]="{ item }">
      <v-list-item dense class="pa-0">
        <v-list-item-content class="pa-0 caption">
          <v-list-item-title>{{ item.model }}</v-list-item-title>
          <v-list-item-subtitle>
            {{ item.year }}
          </v-list-item-subtitle>
        </v-list-item-content>
      </v-list-item>
    </template>
    <template v-slot:[`item.fuel_odometer`]="{ value }">
      <v-progress-linear :value="value" height="25">
        <strong>{{ value }}%</strong>
      </v-progress-linear>
    </template>
    <template v-slot:[`item.responsable`]="{ item }">
      <v-btn v-if="item.responsable" text small>
        {{ item.responsable.name }}
      </v-btn>
      <v-edit-dialog
        v-else
        large
        persistent
        save-text="asignar"
        @save="save(item)"
        @cancel="cancel"
        @open="open"
      >
        <v-btn color="purple" small>Asignar Responsable</v-btn>
        <template v-slot:input>
          <v-form v-model="valid" ref="formUserAssigned" lazy-validation>
            <div class="mt-4 title">
              Seleccione a un Usuario: {{ item.matricula }}
            </div>
            <v-autocomplete
              v-model="item.user_id"
              :items="options.users"
              :rules="[(v) => !!v || 'Es Requerido']"
              item-text="name"
              item-value="id"
              placeholder="Buscar por nombre"
              autofocus
            ></v-autocomplete>
          </v-form>
        </template>
      </v-edit-dialog>
    </template>
  </v-data-table>
</template>

<script>
import SearchPanel from "@admin/components/shared/SearchPanel.vue";
import TableHeaderButtons from "@admin/components/shared/TableHeaderButtons.vue";
import DialogComponent from "@admin/components/DialogComponent.vue";
import EditVehicle from "./Edit.vue";
export default {
  components: { TableHeaderButtons, SearchPanel, DialogComponent, EditVehicle },
  mounted() {
    const _this = this;
    _this.reloadTable();
    _this.loadOptions();
    _this.$eventBus.$on(["VEHICLE_REFRESH"], () => {
      _this.reloadTable();
    });
  },
  beforeUpdate() {
    this.$store.commit("setBreadcrumbs", [{ label: "Flotilla", name: "" }]);
  },
  data() {
    return {
      // menu: false,
      valid: true,
      dialogCreate: false,
      dialogEdit: false,
      showSearchPanel: false,
      dialogDelete: false,
      headers: [
        { text: "", value: "actions", sortable: false },
        {
          text: "Matricula",
          align: "left",
          sortable: false,
          value: "matricula",
          fixed: true,
        },
        {
          text: "Modelo / Año",
          align: "left",
          sortable: false,
          value: "model",
          fixed: true,
        },
        {
          text: "u. Kilometraje",
          align: "right",
          sortable: false,
          value: "last_mileage",
          fixed: true,
        },
        {
          text: "Gasolina %",
          align: "center",
          sortable: false,
          value: "fuel_odometer",
          fixed: true,
        },
        {
          text: "Sucursal",
          align: "left",
          sortable: false,
          value: "sucursal.title",
          fixed: true,
        },
        {
          text: "Responsable",
          align: "center",
          sortable: false,
          value: "responsable",
          fixed: true,
        },
        {
          text: "Ticket Card",
          align: "center",
          sortable: false,
          value: "ticket_card",
          fixed: true,
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
        users: [],
        agencies: [],
        estatus: [
          { text: "Pendientes", value: "pendiente" },
          { text: "Autorizados", value: "autorizado" },
          { text: "Verificados", value: "verificado" },
          { text: "Rechazados", value: "denegar" },
          { text: "Facturados", value: "facturado" },
          { text: "Todos", value: "todos" },
        ],
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
      this.loadVehicles(() => {});
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

    async loadVehicles(cb) {
      const _this = this;
      let params = {
        ..._this.filters,
        search: _this.search,
        page: _this.pagination.page,
        per_page: _this.pagination.itemsPerPage,
      };
      await axios
        .get("/admin/vehicles", { params: params })
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

    async save(item) {
      const _this = this;
      if (!this.$refs.formUserAssigned.validate()) return;
      await axios
        .put("/admin/vehicles/" + item.id, item)
        .then(function (response) {
          _this.$store.commit("showSnackbar", {
            message: "Usuario Asigando",
            color: "success",
            duration: 3000,
          });
          _this.reloadTable();
        })
        .catch(function (error) {
          if (error.response) {
            this.$store.commit("showSnackbar", {
              message: `Usuario Asignado`,
              color: "success",
              duration: 2000,
            });
          } else if (error.request) {
            console.log(error.request);
          } else {
            console.log("Error", error.message);
          }
        });
    },
    cancel() {
      this.$store.commit("showSnackbar", {
        message: "Cancelado",
        color: "error",
        duration: 2000,
      });
    },
    open() {
      this.$store.commit("showSnackbar", {
        message: "Asignar Responsable a la Unidad",
        color: "primary",
        duration: 3000,
      });
    },
  },
};
</script>

<style scoped></style>
