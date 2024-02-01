<template>
  <div>
    <v-data-table
      :headers="headers"
      :items="items"
      :options.sync="pagination"
      :server-items-length="totalItems"
      calculate-widths
      fixed-header
      caption
      dense
      class="black--text caption font-weight-bold text-uppercase text-wrap"
    >
      <template #top>
        <search-panel
          :rightDrawer="rightDrawer"
          @cancelSearch="cancelSearch"
          @resetFilter="resetFilter"
        >
          <v-form ref="formFilter">
            <div class="d-flex flex-column ma-2">
              <v-select
                v-model="filters.purchase_type"
                :items="options.purchase_types"
                item-text="name"
                item-value="id"
                label="Tipo de O.C."
                prepend-icon="mdi-filter-variant"
                hide-details
                outlined
                filled
                clearable
                dense
                class="mb-2"
              ></v-select>
              <v-select
                v-model="filters.purchase_concept"
                label="Concepto de Compra"
                :disabled="PurchaseConcept.length == 0"
                :items="PurchaseConcept"
                item-value="id"
                item-text="name"
                prepend-icon="mdi-filter-variant"
                hide-details
                outlined
                filled
                clearable
                dense
                class="mb-2"
                multiple
                chips
                deletable-chips
              ></v-select>
              <v-autocomplete
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
              ></v-autocomplete>
              <v-select
                v-model="filters.agencie"
                :items="options.agencies"
                item-text="title"
                item-value="id"
                label="Cargo Sucursal"
                prepend-icon="mdi-filter-variant"
                hide-details
                outlined
                filled
                clearable
                dense
                class="mb-2"
                multiple
                chips
                deletable-chips
              ></v-select>
              <v-select
                v-model="filters.department"
                :items="options.departments"
                item-text="title"
                item-value="id"
                label="Cargo Departamento"
                prepend-icon="mdi-filter-variant"
                hide-details
                outlined
                filled
                clearable
                dense
                class="mb-2"
                multiple
                chips
                deletable-chips
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
              <v-select
                v-model="filters.ship"
                :items="options.agencies"
                item-text="title"
                item-value="id"
                label="Sucursal de Solicitante"
                prepend-icon="mdi-filter-variant"
                hide-details
                outlined
                filled
                clearable
                dense
                class="mb-2"
                multiple
                chips
                deletable-chips
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
            <v-dialog
              ref="dialog"
              v-model="modalDateRange"
              :return-value.sync="filters.date_range"
              persistent
              width="450px"
            >
              <template #activator="{ on, attrs }">
                <v-text-field
                  v-model="filters.date_range"
                  label="Rango de Fechas"
                  prepend-icon="mdi-calendar"
                  readonly
                  v-bind="attrs"
                  v-on="on"
                  class="pa-2"
                  outlined
                  filled
                  hide-details
                  dense
                  clearable
                ></v-text-field>
              </template>
              <v-date-picker v-model="filters.date_range" scrollable range>
                <v-spacer></v-spacer>
                <v-btn text color="primary" @click="modalDateRange = false">
                  Cancel
                </v-btn>
                <v-btn
                  text
                  color="primary"
                  @click="$refs.dialog.save(filters.date_range)"
                >
                  OK
                </v-btn>
              </v-date-picker>
            </v-dialog>
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
      </template>
      <template #[`item.actions`]="{ item }">
        <v-menu offset-x transition="slide-x-transition" rounded="r-xl">
          <template v-slot:activator="{ on, attrs }">
            <v-btn icon v-bind="attrs" v-on="on">
              <v-icon>mdi-dots-vertical</v-icon>
            </v-btn>
          </template>
          <v-list shaped dense>
            <v-list-item-group>
              <!-- :to="{ name: 'purchase.edit', params: { purchaseId: item.id } }" -->
              <v-list-item
                @click="(dialogs.id = item.id), (dialogs.show = true)"
              >
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
                  <v-list-item-title>Descargar OC PDF</v-list-item-title>
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
                  <v-list-item-title>
                    {{
                      item.payment_condition < 8
                        ? "Solicitar Prog. Pago"
                        : "Enviar a Proveedor"
                    }}
                  </v-list-item-title>
                </v-list-item-content>
              </v-list-item>
            </v-list-item-group>
          </v-list>
        </v-menu>
      </template>
      <template #[`item.purchase_number`]="{ value }">
        <span> #{{ value }} </span>
      </template>
      <template #[`item.supplier`]="{ value }">
        <v-list-item-content>
          <v-list-item-subtitle>{{ value.code_equip }}</v-list-item-subtitle>
          <v-list-item-title
            class="d-block font-weight-semibold text-primary text-wrap"
          >
            {{ value.business_name }}
          </v-list-item-title>
          <v-list-item-subtitle class="text--secondary">
            {{ value.rfc }}
          </v-list-item-subtitle>
        </v-list-item-content>
      </template>
      <template #[`item.elaborated`]="{ item }">
        <v-menu
          bottom
          left
          offset-x
          transition="scale-transition"
          origin="top right"
        >
          <template v-slot:activator="{ on }">
            <v-chip pill color="black" dark v-on="on">
              <v-avatar left>
                <img
                  v-if="item.elaborated.profiable"
                  :src="item.elaborated.profiable.profile_photo_url"
                  :alt="item.elaborated.name"
                />
                <v-icon v-else class="blue--text"> mdi-account </v-icon>
              </v-avatar>
  
              {{
                item.elaborated.profiable
                  ? item.elaborated.profiable.name
                  : item.elaborated.name
              }}
            </v-chip>
          </template>
          <v-card width="400">
            <v-list dark>
              <v-list-item>
                <v-list-item-avatar>
                  <img
                    v-if="item.elaborated.profiable"
                    :src="item.elaborated.profiable.profile_photo_url"
                    :alt="item.elaborated.name"
                  />
                  <v-icon v-else class="blue--text"> mdi-account </v-icon>
                </v-list-item-avatar>
                <v-list-item-content>
                  <v-list-item-title>
                    {{
                      item.elaborated.profiable
                        ? item.elaborated.profiable.full_name
                        : item.elaborated.name
                    }}
                  </v-list-item-title>
                  <v-list-item-subtitle>
                    {{ item.elaborated.email }}
                  </v-list-item-subtitle>
                  <v-list-item-subtitle v-if="item.elaborated.profiable">
                    {{
                      `${item.elaborated.profiable.agency.title} ${item.elaborated.profiable.department.title}`
                    }}
                  </v-list-item-subtitle>
                </v-list-item-content>
                <v-list-item-action>
                  <v-btn icon>
                    <v-icon>mdi-close-circle</v-icon>
                  </v-btn>
                </v-list-item-action>
              </v-list-item>
            </v-list>
          </v-card>
        </v-menu>
      </template>
      <template #[`item.total`]="{ item }">
        <b>{{ item.total | money }}</b>
      </template>
      <template #[`item.estatus`]="{ item }">
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
      <template #[`item.purchaseType`]="{ item }">
        <v-list-item-content>
          <v-list-item-title>
            {{ item.purchase_type.name }}
          </v-list-item-title>
          <v-list-item-subtitle>
            {{ item.purchase_concept.name }}
          </v-list-item-subtitle>
        </v-list-item-content>
      </template>
      <template #[`item.payment_condition`]="{ value }">
        {{
          value == 1
            ? "Requiere Anticipo"
            : value < 8
            ? "Contado"
            : `${value} dias`
        }}
      </template>
      <template #[`item.charges`]="{ value }">
        <div v-if="value.length == 0">Sin Cargos</div>
        <v-menu
          v-else
          :close-on-content-click="false"
          offset-x
          transition="scale-transition"
          origin="top left"
        >
          <template v-slot:activator="{ on, attrs }">
            <v-btn color="indigo" dark v-bind="attrs" small v-on="on">
              Cargos ({{ value.length }})
            </v-btn>
          </template>
  
          <v-card>
            <v-card-title class="white--text text-title text-uppercase indigo">
              Cargos a Sucursales
            </v-card-title>
  
            <v-card-text>
              <v-list>
                <v-list-item
                  v-for="(item, index) in value"
                  :key="`cargo-${index}`"
                >
                  <v-list-item-content>
                    <v-list-item-title>{{ item.agency.title }}</v-list-item-title>
                    <v-list-item-subtitle>
                      {{ item.department.title }}
                    </v-list-item-subtitle>
                  </v-list-item-content>
  
                  <v-list-item-action-text>
                    <v-chip color="blue" dark label>
                      {{ item.percent }} %
                    </v-chip>
                  </v-list-item-action-text>
                </v-list-item>
              </v-list>
            </v-card-text>
          </v-card>
        </v-menu>
      </template>
      <template #[`item.created_at`]="{ value }">
        {{ $appFormatters.formatDate(value, "l") }}
      </template>
      <template #[`item.updated_at`]="{ value }">
        {{ $appFormatters.formatDate(value, "l") }}
      </template>
    </v-data-table>

    <dialog-component
          :show="dialogs.show"
          @close="(dialogs.show = false), (dialogs.id = null)"
          title="Detalle Orden de Compra"
          fullscreen
          closeable
        >
          <EditPurchase
            v-if="dialogs.show && dialogs.id"
            :purchaseId="dialogs.id"
          ></EditPurchase>
        </dialog-component>

  </div>
</template>
<script>
import DialogComponent from "@admin/components/DialogComponent.vue";
import SearchPanel from "@admin/components/shared/SearchPanel.vue";
import TableHeaderButtons from "@admin/components/shared/TableHeaderButtons.vue";
import PurchaseForm from "../components/forms/PurchaseForm.vue";
import Create from "./Create.vue";
import EditPurchase from "./Edit.vue";

export default {
  name: "PurchaseOrderList",
  components: {
    Create,
    SearchPanel,
    EditPurchase,
    PurchaseForm,
    DialogComponent,
    TableHeaderButtons,
  },
  data: () => ({
    dialogs: {
        create: false,
        quote: false,
        show: false,
        id: null,
      },
    chipExpande: false,
    valid: true,
    modalDateRange: false,
    dialogCreate: false,
    dialogEdit: false,
    showSearchPanel: false,
    dialogDelete: false,
    headers: [
      { text: "", value: "actions", width: "100", sortable: false },
      {
        text: "Folio OC",
        align: "center",
        sortable: false,
        value: "purchase_number",
        fixed: true,
      },
      {
        text: "Proveedor",
        value: "supplier",
        fixed: true,
      },
      {
        text: "Cargos a Sucursal",
        value: "charges",
        align: "center",
        fixed: true,
        sortable: false,
      },
      {
        text: "Agencia Solc.",
        value: "ship.title",
        align: "center",
        fixed: true,
        sortable: false,
      },
      { text: "Tipo O.C.", value: "purchaseType", width: 200 },
      {
        text: "Condicion Pago",
        value: "payment_condition",
        align: "center",
        sortable: false,
      },
      { text: "Importe", value: "total", align: "right" },
      { text: "Realizado por", value: "elaborated", width: 175 },
      { text: "Estatus", value: "estatus", align: "center" },
      { text: "Creada", value: "created_at", align: "right" },
      { text: "Ultimo Cambio", value: "updated_at", align: "right" },
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
      date_range: null,
      payment_condition: null,
      purchase_type: null,
      purchase_concept: null,
      ship: null,
    },
    options: {
      suppliers: [],
      agencies: [],
      departments: [],
      metodoPago: [],
      usoCFDI: [],
      formaPago: [],
      estatus: [
        { text: "Todos", value: "todos" },
        { text: "Nueva Creacion", value: "pendiente" },
        { text: "Rechazados", value: "denegar" },
        { text: "Verificados", value: "verificado" },
        { text: "Autorizados", value: "autorizado" },
        { text: "Por Facturar", value: "por_facturar" },
        { text: "Programar Pago", value: "programar_pago" },
        { text: "Pendiente de Pago", value: "por_pagar" },
        { text: "Pagadas, por Facturar", value: "pagada.porFacturar" },
        { text: "Pagadas", value: "pagada" },
      ],
      payment_conditions: [],
      purchase_types: [],
      purchase_concepts: [],
    },
    colors: {
      pendiente: "blue",
      denegar: "red",
      verificado: "purple",
      autorizado: "orange",
      por_facturar: "black",
      programar_pago: "pink",
      por_pagar: "indigo darken-2",
      "pagada.porFacturar": "grey darken-2",
      pagada: "cyan",
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
    PurchaseConcept() {
      const _this = this;
      let concept_by_type = _this.options.purchase_types.find(
        (e) => e.id == _this.filters.purchase_type
      );
      return concept_by_type != undefined
        ? concept_by_type.purchase_concept
        : [];
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
        estatus_key:
          item.payment_condition < 8 ? "programar_pago" : "por_facturar",
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
        autorizado: true,
        programar_pago: true,
        pagada: true,
        por_facturar: true,
      };
      return estatus[estatus_key] ?? false;
    },
    canMarkAsSend(estatus_key) {
      let estatus = {
        autorizado: true,
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
          _this.options.departments = Data.departments;
          _this.options.metodoPago = Data.metodoPago;
          _this.options.usoCFDI = Data.usoCFDI;
          _this.options.formaPago = Data.formaPago;
          _this.options.purchase_types = Data.purchase_types;
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
