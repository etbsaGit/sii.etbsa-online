<template>
  <v-data-table
    v-model="selected"
    :headers="headers"
    :items="items"
    :options.sync="pagination"
    :server-items-length="totalItems"
    show-select
    calculate-widths
    fixed-header
    caption
    dense
    class="blue--text text-uppercase text-truncate"
  >
    <template v-slot:top>
      <search-panel
        :rightDrawer="rightDrawer"
        @cancelSearch="cancelSearch"
        @resetFilter="resetFilter"
      >
        <v-form ref="formFilter">
          <div class="d-flex flex-column ma-2">
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
              label="Sucursal"
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
              label="Departamento"
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
            label="Buscar Folio Fiscal"
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
            <template v-slot:activator="{ on, attrs }">
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
        <v-toolbar-title>Facturas Por Pagar</v-toolbar-title>
        <v-divider class="mx-4" inset vertical></v-divider>
        <v-spacer></v-spacer>
        <!-- btn Registrar Pago -->
        <v-dialog
          v-if="selected.length > 0 && filters.estatus == 'por_pagar'"
          ref="dialog_payment"
          v-model="dialogPayment"
          :return-value.sync="payment_date"
          persistent
          transition="scale-transition"
          width="324px"
        >
          <template v-slot:activator="{ on, attrs }">
            <v-btn color="blue" v-bind="attrs" v-on="on" class="ml-2" dark>
              {{
                payment_date
                  ? `Factura se Pago: ${$appFormatters.formatDate(
                      payment_date,
                      "ll"
                    )}`
                  : "Fecha de Pago"
              }}
              <v-icon right>mdi-update</v-icon>
            </v-btn>
          </template>
          <v-date-picker v-model="payment_date" scrollable>
            <!-- :min="new Date().toISOString().substr(0, 10)" -->
            <v-spacer></v-spacer>
            <v-btn text color="primary" @click="dialogPayment = false">
              Cancel
            </v-btn>
            <v-btn
              text
              color="primary"
              @click="
                $refs.dialog_payment.save(payment_date), updateDatePayment()
              "
            >
              Confirmar
            </v-btn>
          </v-date-picker>
        </v-dialog>
        <!-- btn Prg Pago -->
        <v-dialog
          v-if="selected.length > 0 && filters.estatus == 'programar_pago'"
          ref="dialog_pay"
          v-model="dialogDayToPayment"
          :return-value.sync="date_to_pay"
          persistent
          transition="scale-transition"
          width="324px"
        >
          <template v-slot:activator="{ on, attrs }">
            <v-btn color="pink" v-bind="attrs" v-on="on" class="ml-2" dark>
              {{
                date_to_pay
                  ? `Se Pagara(n) el: ${$appFormatters.formatDate(
                      date_to_pay,
                      "ll"
                    )}`
                  : "Programar fecha de Pago"
              }}
              <v-icon right>mdi-update</v-icon>
            </v-btn>
          </template>
          <v-date-picker v-model="date_to_pay" scrollable>
            <!-- :min="new Date().toISOString().substr(0, 10)" -->
            <v-spacer></v-spacer>
            <v-btn text color="primary" @click="dialogDayToPayment = false">
              Cancel
            </v-btn>
            <v-btn
              text
              color="primary"
              @click="$refs.dialog_pay.save(date_to_pay), updateDateToPayment()"
            >
              Confirmar
            </v-btn>
          </v-date-picker>
        </v-dialog>
      </v-toolbar>
    </template>
    <template v-slot:[`item.folio`]="{ item }">
      <v-list-item dense class="pa-0 caption">
        <v-list-item-content class="pa-0 caption text-wrap">
          <v-list-item-title class="caption">
            {{ item.folio_fiscal }}
          </v-list-item-title>
          <v-list-item-subtitle class="caption">
            {{ item.folio }}{{ item.serie }}
          </v-list-item-subtitle>
        </v-list-item-content>
      </v-list-item>
    </template>
    <template v-slot:[`item.invoiceable.supplier`]="{ value }">
      <v-list-item dense class="pa-0 caption">
        <v-list-item-content class="pa-0 caption">
          <v-list-item-title class="caption">
            {{ value.business_name }}
          </v-list-item-title>
          <v-list-item-subtitle>
            {{ value.rfc }}
          </v-list-item-subtitle>
        </v-list-item-content>
      </v-list-item>
    </template>
    <template v-slot:[`item.invoiceable.id`]="{ value }">
      <span> #{{ value.toString().padStart(5, 0) }} </span>
    </template>
    <template v-slot:[`item.amount`]="{ value }">
      {{ value | currency }}
    </template>
    <template v-slot:[`item.invoiceable.estatus`]="{ value }">
      <v-chip
        label
        small
        :color="getColorByStatus(value.key)"
        text-color="white"
      >
        {{ value.title }}
      </v-chip>
    </template>

    <template v-slot:[`item.cargos`]="{ item }">
      <v-dialog transition="dialog-bottom-transition" width="600">
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
                  v-for="(tag, index) in item.invoiceable.charges"
                  :key="`${tag.agency}-${index}`"
                  class="overline"
                  dark
                >
                  {{ tag.agency.title }} - {{ tag.department.title }} -
                  {{ tag.percent }}%
                </v-chip>
              </v-chip-group>
            </div>
          </v-sheet>
        </template>
      </v-dialog>
    </template>
    <template v-slot:[`item.invoice_date`]="{ value }">
      {{ $appFormatters.formatDate(value, "l") }}
    </template>
    <template v-slot:[`item.date_to_pay`]="{ value }">
      {{ $appFormatters.formatDate(value, "l") }}
    </template>
    <template v-slot:[`item.payment_date`]="{ value }">
      {{ $appFormatters.formatDate(value, "l") }}
    </template>
  </v-data-table>
</template>
<script>
import DialogComponent from "@admin/components/DialogComponent.vue";
import SearchPanel from "@admin/components/shared/SearchPanel.vue";
import TableHeaderButtons from "@admin/components/shared/TableHeaderButtons.vue";

export default {
  components: {
    SearchPanel,
    TableHeaderButtons,
    DialogComponent,
  },
  data: () => ({
    valid: true,
    modalDateRange: false,
    selected: [],
    showSearchPanel: false,
    dialogPayment: false,
    dialogDayToPayment: false,
    payment_date: null,
    date_to_pay: null,
    headers: [
      // { text: "", value: "actions", sortable: false },
      {
        text: "Folio",
        align: "left",
        sortable: false,
        value: "folio",
        fixed: true,
      },
      {
        text: "Proveedor",
        value: "invoiceable.supplier",
        fixed: true,
      },
      {
        text: "Orden de Compra",
        value: "invoiceable.id",
        fixed: true,
      },
      {
        text: "Cargos",
        value: "cargos",
        fixed: true,
        sortable: false,
      },
      { text: "Concepto", value: "invoiceable.purchase_concept.name" },
      // {
      //   text: "Productos",
      //   value: "invoiceable.concepts.length",
      //   align: "center",
      //   sortable: false,
      // },
      { text: "Importe", value: "amount", align: "right" },
      // { text: "Realizado por", value: "invoiceable.elaborated" },
      { text: "Estatus", value: "invoiceable.estatus", align: "center" },
      { text: "Fecha Factura", value: "invoice_date", align: "center" },
      { text: "Pagar el", value: "date_to_pay", align: "center" },
      { text: "Pagada el", value: "payment_date", align: "center" },
    ],
    items: [],
    search: null,
    filters: {
      folio: null,
      supplier: null,
      agencie: null,
      department: null,
      uso_cfdi: null,
      metodo_pago: null,
      forma_pago: null,
      estatus: "todos",
      date_range: null,
    },
    options: {
      suppliers: [],
      agencies: [],
      departments: [],
      metodoPago: [],
      usoCFDI: [],
      formaPago: [],
      estatus: [
        { text: "Facturados", value: "programar_pago" },
        { text: "Pendiente de Pago", value: "por_pagar" },
        { text: "Pagadas", value: "pagada" },
        { text: "Todos", value: "todos" },
      ],
    },
    colors: {
      por_pagar: "pink",
      pagada: "cyan",
      programar_pago: "indigo",
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
    _this.$eventBus.$on(["INVOICE_REFRESH"], () => {
      _this.reloadTable();
    });
    _this.$store.commit("setBreadcrumbs", [
      { label: "Facturas por pagar", name: "" },
    ]);
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
  methods: {
    updateSearchPanel() {
      this.rightDrawer = !this.rightDrawer;
    },
    reloadTable() {
      this.loadPurchaseOrders(() => {});
      this.date_to_pay = null;
      this.payment_date = null;
      this.selected = [];
    },
    cancelSearch() {
      this.showSearchPanel = false;
    },

    getColorByStatus(status) {
      return this.colors[status];
    },

    checkEstatus(estatus_key, item_estatus) {
      return item_estatus == estatus_key;
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
        .get("/admin/purchase-invoice", { params: params })
        .then(function (response) {
          // console.log(response.data.data);
          let Response = response.data.data;
          _this.items = Response.data;
          _this.totalItems = Response.total;
          _this.pagination.totalItems = Response.total;
          (cb || Function)();
        });
    },

    // async deleteItemConfirm() {
    //   this.desserts.splice(this.editedIndex, 1);
    //   this.closeDelete();
    // },
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
        });
    },
    resetFilter() {
      const _this = this;
      _this.$refs.formFilter.reset();
      _this.pagination.itemsPerPage = 10;
      _this.pagination.page = 1;
    },

    async updateDateToPayment() {
      const _this = this;

      let payload = {
        date_to_pay: _this.date_to_pay,
      };
      _this.selected.forEach(async (element) => {
        await axios
          .post(
            `/admin/purchase-invoice/date_to_payment/${element.id}`,
            payload
          )
          .then(function (response) {
            _this.$store.commit("showSnackbar", {
              message: response.data.message,
              color: "success",
              duration: 3000,
            });
            _this.date_to_pay = "";
            _this.payment_date = "";
            _this.reloadTable();
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
      });
      this.selected = [];
    },
    async updateDatePayment() {
      const _this = this;

      let payload = {
        payment_date: _this.payment_date,
      };
      _this.selected.forEach(async (element) => {
        //
        await axios
          .post(`/admin/purchase-invoice/date_payment/${element.id}`, payload)
          .then(function (response) {
            _this.$store.commit("showSnackbar", {
              message: response.data.message,
              color: "success",
              duration: 3000,
            });
            _this.date_to_pay = "";
            _this.payment_date = "";
            _this.reloadTable();
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
        //
      });
      _this.selected = [];
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
        this.selected = [];
      }, 999),
      deep: true,
    },
    search: _.debounce(function (v) {
      this.reloadTable();
    }, 999),
  },
};
</script>
