<template>
  <v-data-table
    v-model="selected"
    :headers="headers"
    :items="items"
    :options.sync="pagination"
    :server-items-length="totalItems"
    :single-expand="true"
    show-selectitem-key="folio"
    show-expand
    item-key="folio"
    show-select
    calculate-widths
    fixed-header
    caption
    dense
    class="blue--text text-uppercase text-truncate"
  >
    <template v-slot:expanded-item="{ headers, item }">
      <td :colspan="headers.length" class="pa-0">
        <v-simple-table dense class="elevation-2 ma-1">
          <template v-slot:default>
            <tbody>
              <tr>
                <td class="caption">
                  Orden de Compra:
                </td>
                <td class="d-flex align-center">
                  <v-btn
                    dark
                    small
                    color="primary"
                    :to="{
                      name: 'purchase.edit',
                      params: { purchaseId: item.invoiceable.id },
                    }"
                  >
                    #{{ item.invoiceable.id.toString().padStart(5, 0) }}
                    <v-icon right>mdi-eye-outline</v-icon>
                  </v-btn>
                </td>
              </tr>
              <tr>
                <td class="caption">
                  Concepto de Compra:
                </td>
                <td class="d-flex align-center">
                  {{ item.invoiceable.purchase_concept.name }}
                </td>
              </tr>
              <tr>
                <td class="caption">Cargos:</td>
                <td class="d-flex align-center">
                  <v-chip-group active-class="primary--text" column>
                    <v-chip
                      v-for="(tag, index) in item.invoiceable.charges"
                      :key="`${tag.agency}-${index}`"
                      class="overline"
                      dark
                      small
                    >
                      {{ tag.agency.title }} - {{ tag.department.title }} -
                      {{ tag.percent }}%
                    </v-chip>
                  </v-chip-group>
                </td>
              </tr>
            </tbody>
          </template>
        </v-simple-table>
      </td>
    </template>
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
          :exportTable="exportTable"
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
        <!-- btn Programar Fecha de Pago -->
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
        <!-- Btn Reset Programacion de Pago -->
        <v-btn
          v-if="selected.length > 0 && filters.estatus == 'por_pagar'"
          color="red"
          class="ml-2"
          dark
          @click="resetDateToPayment"
        >
          Resetiar fecha de Programacion de Pago
        </v-btn>
        <!-- Brn Reset de Fecha de Pago -->
        <v-btn
          v-if="selected.length > 0 && filters.estatus == 'pagada'"
          color="red"
          class="ml-2"
          dark
          @click="resetDatePayment"
        >
          Resetiar fecha de Pago
        </v-btn>
      </v-toolbar>
    </template>
    <template v-slot:[`item.folio`]="{ item }">
      <v-list-item dense class="pa-0 caption">
        <v-list-item-content class="pa-0 text-wrap">
          <v-list-item-subtitle class="button">
            {{ item.folio }}-{{ item.serie }}
          </v-list-item-subtitle>
          <v-list-item-title class="caption">
            {{ item.folio_fiscal }}
          </v-list-item-title>
        </v-list-item-content>
      </v-list-item>
    </template>
    <template v-slot:[`item.invoiceable.supplier`]="{ value }">
      <v-list-item dense class="pa-0 caption" style="min-width: 200px;">
        <v-list-item-content class="pa-0 caption">
          <v-list-item-title class="font-weight-bold caption text-wrap">
            {{ value.business_name }}
          </v-list-item-title>
          <v-list-item-subtitle>
            {{ value.rfc }}
          </v-list-item-subtitle>
        </v-list-item-content>
      </v-list-item>
    </template>
    <template v-slot:[`item.amount`]="{ value }">
      <span class="font-weight-bold"> {{ value | currency }} MXN </span>
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
    <template v-slot:[`item.invoice_date`]="{ value }">
      {{ $appFormatters.formatDate(value, "L") }}
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
    expanded: [],
    singleExpand: false,
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
        text: "Folio Fiscal",
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
      { text: "Importe", value: "amount", align: "right" },
      {
        text: "Condicion (Dias)",
        value: "invoiceable.payment_condition",
        align: "center",
      },
      { text: "Fecha Factura", value: "invoice_date", align: "center" },
      { text: "Pagar el", value: "date_to_pay", align: "center" },
      { text: "Pagada el", value: "payment_date", align: "center" },
      { text: "Estatus", value: "invoiceable.estatus", align: "center" },
      { text: "", value: "data-table-expand" },
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
        { text: "Todo", value: "todos" },
        { text: "Programar Pago", value: "programar_pago" },
        { text: "Pendiente de Pago", value: "por_pagar" },
        { text: "Facturas Pagadas", value: "pagada" },
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

    async resetDateToPayment() {
      const _this = this;

      let payload = {
        date_to_pay: _this.date_to_pay,
      };
      _this.selected.forEach(async (element) => {
        await axios
          .post(
            `/admin/purchase-invoice/reset_date_to_payment/${element.id}`,
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
    async resetDatePayment() {
      const _this = this;

      let payload = {
        payment_date: _this.payment_date,
      };
      _this.selected.forEach(async (element) => {
        //
        await axios
          .post(
            `/admin/purchase-invoice/reset_date_payment/${element.id}`,
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
        //
      });
      _this.selected = [];
    },

    async exportTable() {
      const _this = this;
      let params = {
        ..._this.filters,
        search: _this.search,
        page: _this.pagination.page,
        per_page: _this.pagination.itemsPerPage,
        paginate: "no",
      };
      await axios
        .get("/admin/purchase-invoice-export", {
          params: params,
          responseType: "blob",
        })
        .then((res) => {
          const url = window.URL.createObjectURL(new Blob([res.data]));
          const link = document.createElement("a");
          link.href = url;
          link.setAttribute("download", "invoices.xlsx"); //or any other extension
          document.body.appendChild(link);
          link.click();
        })
        .catch(function (error) {
          if (error.response) {
            self.$store.commit("showSnackbar", {
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
