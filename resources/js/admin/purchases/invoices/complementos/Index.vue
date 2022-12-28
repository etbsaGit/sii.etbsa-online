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
        <v-toolbar-title>Complementos de Pago</v-toolbar-title>
        <v-divider class="mx-4" inset vertical></v-divider>
        <v-spacer></v-spacer>
        <!-- btn Registrar Complemento de Pago -->
        <v-dialog
          ref="dialog_upload_xml"
          v-model="dialogUploadXML"
          :return-value.sync="file_xml"
          persistent
          transition="scale-transition"
          width="628px"
        >
          <template v-slot:activator="{ on, attrs }">
            <v-btn color="purple" v-bind="attrs" v-on="on" class="ml-2" dark>
              Subir XML de Complemento
              <v-icon right>mdi-file-code</v-icon>
            </v-btn>
          </template>
          <v-card>
            <v-card-title>Subir CFDI Complemento de Pago (.xml)</v-card-title>
            <v-card-text>
              <v-row>
                <v-col cols="8">
                  <v-file-input
                    v-model="file_xml"
                    outlined
                    color="green"
                    dense
                    hide-details
                  ></v-file-input>
                </v-col>
                <v-col cols="4">
                  <v-btn color="green" dark>Validar XML</v-btn>
                </v-col>
              </v-row>
            </v-card-text>
            <v-card-actions>
              <div class="d-flex text--center">
                <v-btn text @click="dialogUploadXML = false"> Cancelar</v-btn>
                <v-btn> Confirmar</v-btn>
              </div>
            </v-card-actions>
          </v-card>
        </v-dialog>
      </v-toolbar>
    </template>
    <!-- <template v-slot:[`item.folio`]="{ item }">
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
    </template> -->
  </v-data-table>
</template>
<script>
import DialogComponent from "@admin/components/DialogComponent.vue";
import SearchPanel from "@admin/components/shared/SearchPanel.vue";
import TableHeaderButtons from "@admin/components/shared/TableHeaderButtons.vue";
export default {
  name: "Complementos",
  components: {
    SearchPanel,
    TableHeaderButtons,
    DialogComponent,
  },
  mounted() {
    const _this = this;
    // _this.reloadTable();
    _this.loadOptions();
    // _this.$eventBus.$on(["INVOICE_REFRESH"], () => {
    //   _this.reloadTable();
    // });
    // _this.$store.commit("setBreadcrumbs", [
    //   { label: "Facturas por pagar", name: "" },
    // ]);
  },
  data() {
    return {
      headers: [
        {
          text: "Folio UUID",
          align: "left",
          sortable: false,
          value: "folio",
          fixed: true,
        },
        {
          text: "Proveedor",
          value: "invoiceable.supplier",
          fixed: true,
          sortable: false,
        },
        {
          text: "Documento Relacionado",
          value: "invoiceable.id",
          fixed: true,
          sortable: false,
        },
        {
          text: "Num. Parcialidad",
          value: "parcialiad",
          fixed: true,
        },
        {
          text: "Importe",
          value: "amount",
          fixed: true,
        },
        {
          text: "Fecha Complemento",
          value: "date_complemento",
          fixed: true,
          sortable: false,
        },
      ],
      items: [],
      search: null,
      valid: true,
      modalDateRange: false,
      selected: [],
      showSearchPanel: false,
      dialogUploadXML: false,
      file_xml: null,
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
          { text: "Facturados", value: "facturado" },
          { text: "Pendiente de Pago", value: "por_pagar" },
          { text: "Pagadas", value: "pagada" },
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
        this.selected = [];
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
      this.loadComplementos(() => {});
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
    async loadComplementos(cb) {
      const _this = this;
      let params = {
        ..._this.filters,
        search: _this.search,
        page: _this.pagination.page,
        per_page: _this.pagination.itemsPerPage,
      };
      // await axios
      //   .get("/admin/purchase-invoice", { params: params })
      //   .then(function (response) {
      //     // console.log(response.data.data);
      //     let Response = response.data.data;
      //     _this.items = Response.data;
      //     _this.totalItems = Response.total;
      //     _this.pagination.totalItems = Response.total;
      //     (cb || Function)();
      //   });
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
<style></style>
