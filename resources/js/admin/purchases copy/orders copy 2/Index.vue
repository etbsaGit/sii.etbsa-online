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
    class="blue--text text-uppercase text-wrap"
  >
    <template v-slot:top>
      <search-panel
        :rightDrawer="rightDrawer"
        @cancelSearch="cancelSearch"
        @resetFilter="resetFilter"
      >
        <v-form ref="formFilter">
          <div class="d-flex flex-column ma-2">
            <v-select
              v-model="filters.supplier"
              :items="options.suppliers"
              item-text="business_name"
              item-value="id"
              label="Proveedor:"
              prepend-icon="mdi-filter-variant"
              hide-details
              outlined
              filled
              clearable
              dense
              class="mb-3"
            ></v-select>
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
            <v-select
              v-model="filters.forma_pago"
              :items="options.formaPago"
              label="Forma Pago:"
              item-text="clave"
              item-value="clave"
              prepend-icon="mdi-filter-variant"
              hide-details
              outlined
              filled
              clearable
              dense
              class="mb-2"
            ></v-select>
            <v-select
              v-model="filters.uso_cfdi"
              :items="options.usoCFDI"
              label="UsoCFDI"
              persistent-hint
              item-text="clave"
              item-value="clave"
              prepend-icon="mdi-filter-variant"
              hide-details
              outlined
              filled
              clearable
              dense
              class="mb-2"
            ></v-select>
            <v-select
              v-model="filters.metodo_pago"
              :items="options.metodoPago"
              label="Metodo Pago:"
              persistent-hint
              item-text="clave"
              item-value="clave"
              prepend-icon="mdi-filter-variant"
              hide-details
              outlined
              filled
              clearable
              dense
              class="mb-2"
            ></v-select>
          </div>
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
            label="Search"
            class="pa-2"
            outlined
            filled
            append-icon="mdi-magnify"
            hide-details
            clearable
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
        <v-toolbar-title>Ordenes de Compra</v-toolbar-title>
        <v-divider class="mx-4" inset vertical></v-divider>
        <v-spacer></v-spacer>
        <v-btn
          color="primary"
          rounded
          class="mb-2"
          :to="{ name: 'purchase.create' }"
        >
          Crear Orden de Compra
        </v-btn>
      </v-toolbar>
      <dialog-component
        :show="dialogEdit"
        @close="dialogEdit = false"
        fullscreen
        closeable
        :title="formTitle"
      >
        <edit-purchase v-if="dialogEdit" :purchaseId="editedId"></edit-purchase>
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
                <v-list-item-title>Detalle OC</v-list-item-title>
              </v-list-item-content>
            </v-list-item>
            <v-list-item
              v-if="canPrint(item.estatus.key)"
              icon
              :href="`/admin/purchase-order/${item.id}/resources/print`"
              target="_blank"
            >
              <v-list-item-icon>
                <v-icon class="blue--text">mdi-printer</v-icon>
              </v-list-item-icon>
              <v-list-item-content>
                <v-list-item-title>Imprimir Vista Previa</v-list-item-title>
              </v-list-item-content>
            </v-list-item>
            <v-list-item
              v-if="canMarkAsSend(item.estatus.key)"
              icon
              @click="markAsSend(item)"
            >
              <v-list-item-icon>
                <v-icon class="blue--text">mdi-send-check</v-icon>
              </v-list-item-icon>
              <v-list-item-content>
                <v-list-item-title>Enviar a Proveedor</v-list-item-title>
              </v-list-item-content>
            </v-list-item>
          </v-list-item-group>
        </v-list>
      </v-menu>
    </template>
    <template v-slot:[`item.id`]="{ item }">
      <span> #{{ item.id.toString().padStart(5, 0) }} </span>
    </template>
    <template v-slot:[`item.supplier.business_name`]="{ item }">
      <v-list-item dense class="pa-0">
        <v-list-item-content class="pa-0">
          <v-list-item-title class="caption text-wrap">
            {{ item.supplier.business_name }}
          </v-list-item-title>
          <v-list-item-subtitle>
            {{ item.supplier.rfc }}
          </v-list-item-subtitle>
        </v-list-item-content>
      </v-list-item>
    </template>
    <template v-slot:[`item.elaborated`]="{ item }">
      <v-list-item dense class="pa-0">
        <v-list-item-content class="pa-0">
          <v-list-item-title class="caption text-wrap">
            {{
              item.elaborated.profiable
                ? item.elaborated.profiable.full_name
                : item.elaborated.name
            }}
          </v-list-item-title>
          <v-list-item-subtitle>
            {{
              item.elaborated.profiable
                ? item.elaborated.profiable.agency.title
                : ""
            }}
          </v-list-item-subtitle>
        </v-list-item-content>
      </v-list-item>
    </template>
    <template v-slot:[`item.total`]="{ item }">
      <b>{{ item.total | money }}</b>
    </template>
    <template v-slot:[`item.estatus`]="{ item }">
      <v-chip
        label
        small
        :color="getColorByStatus(item.estatus.key)"
        text-color="white"
        dark
      >
        {{ item.estatus.title }}
      </v-chip>
    </template>

    <template v-slot:[`item.cargos`]="{ item }">
      <v-dialog transition="dialog-bottom-transition" width="325">
        <template v-slot:activator="{ on, attrs }">
          <v-btn text v-bind="attrs" v-on="on">
            Ver <v-icon right>mdi-eye</v-icon>
          </v-btn>
        </template>
        <template v-slot:default>
          <v-sheet elevation="10" rounded="xl">
            <v-sheet class="pa-3 blue title" rounded="t-xl">
              Cargos a Sucursal
            </v-sheet>
            <div class="pa-4">
              <v-chip-group active-class="primary--text" column>
                <v-chip
                  v-for="(tag, index) in item.charges"
                  :key="`${tag.agency}-${index}`"
                  class="overline"
                >
                  {{ tag.agency }} - {{ tag.department }} - {{ tag.percent }}%
                </v-chip>
              </v-chip-group>
            </div>
          </v-sheet>
        </template>
      </v-dialog>
    </template>
    <template v-slot:[`item.created_at`]="{ value }">
      {{ $appFormatters.formatDate(value, "l") }}
    </template>
  </v-data-table>
</template>
<script>
import DialogComponent from "@admin/components/DialogComponent.vue";
import SearchPanel from "@admin/components/shared/SearchPanel.vue";
import TableHeaderButtons from "@admin/components/shared/TableHeaderButtons.vue";
import PurchaseForm from "../components/forms/PurchaseForm.vue";
import Create from "./Create.vue";
import EditPurchase from "./Edit.vue";

export default {
  components: {
    Create,
    SearchPanel,
    EditPurchase,
    PurchaseForm,
    DialogComponent,
    TableHeaderButtons,
  },
  data: () => ({
    valid: true,
    dialogCreate: false,
    dialogEdit: false,
    showSearchPanel: false,
    dialogDelete: false,
    headers: [
      { text: "", value: "actions", sortable: false },
      {
        text: "Folio OC",
        align: "center",
        sortable: false,
        value: "id",
        fixed: true,
      },
      {
        text: "Proveedor",
        value: "supplier.business_name",
        width: 200,
        fixed: true,
      },
      {
        text: "Cargos a Sucursal",
        value: "cargos",
        fixed: true,
        sortable: false,
      },
      { text: "Concepto Compra", value: "purchase_concept.name", width: 200 },
      {
        text: "Articulos",
        value: "concepts.length",
        align: "center",
        sortable: false,
      },
      { text: "Importe", value: "total", align: "right" },
      { text: "Realizado por", value: "elaborated", width: 175 },
      { text: "Estatus", value: "estatus", align: "center" },
      { text: "Creada", value: "created_at", align: "right" },
    ],
    editedId: -1,
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
      suppliers: [],
      agencies: [],
      metodoPago: [],
      usoCFDI: [],
      formaPago: [],
      estatus: [
        { text: "Pendientes", value: "pendiente" },
        { text: "Rechazados", value: "denegar" },
        { text: "Autorizados", value: "autorizado" },
        { text: "Verificados", value: "verificado" },
        { text: "Enviadas", value: "por_facturar" },
        { text: "Facturados", value: "programar_pago" },
        { text: "Por Pagar", value: "por_pagar" },
        { text: "Pagadas", value: "pagada" },
        { text: "Todos", value: "todos" },
      ],
    },
    colors: {
      pendiente: "blue",
      autorizado: "orange",
      denegar: "red",
      verificado: "purple",
      programar_pago: "green",
      por_pagar: "pink",
      pagada: "cyan",
      por_facturar: "brown darken-4",
    },
    totalItems: 0,
    pagination: {
      itemsPerPage: 10,
      page: 1,
    },
  }),
  mounted() {
    const _this = this;
    _this.reloadTable();
    _this.loadOptions();
    _this.$eventBus.$on(["ORDERS_REFRESH"], () => {
      _this.reloadTable();
    });
    _this.$eventBus.$on(["CLOSE_DIALOG"], () => {
      _this.dialogCreate = false;
      _this.dialogEdit = false;
      _this.dialogDelete = false;
      _this.editedId = -1;
    });
    _this.$store.commit("setBreadcrumbs", [
      { label: "Ordenes de Compra", name: "" },
    ]);
  },
  computed: {
    formTitle() {
      return this.editedId === -1
        ? "Nueva Solicitud Orden de Compra"
        : `Detalle de Orden de Compra - #${this.editedId
            .toString()
            .padStart(5, 0)}`;
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
  methods: {
    updateSearchPanel() {
      this.rightDrawer = !this.rightDrawer;
    },
    reloadTable() {
      this.loadPurchaseOrders(() => {});
    },
    cancelSearch() {
      this.showSearchPanel = false;
    },
    editItem(item) {
      this.editedId = item.id;
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

    getColorByStatus(status) {
      return this.colors[status];
    },
    async markAsSend(item) {
      const _this = this;
      let payload = {
        estatus_key: "por_facturar",
      };
      await axios
        .post(`/admin/purchase-order/update-estatus/${item.id}`, payload)
        .then(function (response) {
          _this.$store.commit("showSnackbar", {
            message: response.data.message,
            color: "success",
            duration: 3000,
          });
          _this.$eventBus.$emit("ORDERS_REFRESH");
          _this.loadPurchaseEdit();
          // _this.$eventBus.$emit("CLOSE_DIALOG");
        })
        .catch(function (error) {
          _this.$store.commit("hideLoader");
          if (error.response) {
            _this.$store.commit("showSnackbar", {
              message: error.response.data.message,
              color: "error",
              duration: 3000,
            });
            return false;
          } else if (error.request) {
            console.log(error.request);
          } else {
            console.log("Error", error.message);
          }
        });
    },
    canPrint(estatus_key) {
      let estatus = {
        verificado: true,
        programar_pago: true,
        pagada: true,
        por_facturar: true,
      };
      return estatus[estatus_key] ?? false;
    },
    canMarkAsSend(estatus_key) {
      let estatus = {
        verificado: true,
      };
      return estatus[estatus_key] ?? false;
    },
    async loadPurchaseOrders(cb) {
      const _this = this;
      let params = {
        ..._this.filters,
        search: _this.search,
        page: _this.pagination.page,
        per_page: _this.pagination.itemsPerPage,
      };
      await axios
        .get("/admin/purchase-order", { params: params })
        .then(function (response) {
          // console.log(response.data.data);
          let Response = response.data.data;
          _this.items = Response.data;
          _this.totalItems = Response.total;
          _this.pagination.totalItems = Response.total;
          (cb || Function)();
        });
    },

    async deleteItemConfirm() {
      this.desserts.splice(this.editedIndex, 1);
      this.closeDelete();
    },
    async loadOptions(cb) {
      const _this = this;
      await axios
        .get("/admin/purchase-order/resources/options")
        .then(function (response) {
          let Data = response.data.data;
          _this.options.suppliers = Data.suppliers;
          _this.options.agencies = Data.agencies;
          _this.options.metodoPago = Data.metodoPago;
          _this.options.usoCFDI = Data.usoCFDI;
          _this.options.formaPago = Data.formaPago;
        });
    },
    resetFilter() {
      const _this = this;
      _this.$refs.formFilter.reset();
      _this.pagination.itemsPerPage = 10;
      _this.pagination.page = 1;
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
};
</script>
