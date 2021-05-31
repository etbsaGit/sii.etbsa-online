<template>
  <v-container fluid>
    <!-- <gps-widget-stats></gps-widget-stats> -->
    <v-data-table
      v-bind:headers="headers"
      :options.sync="pagination"
      :items="items"
      :server-items-length="totalItems"
      dense
      fixed-header
      caption
      class="elevation-4"
    >
      <template v-slot:top>
        <search-panel
          :rightDrawer="rightDrawer"
          @cancelSearch="cancelSearch"
          @resetFilter="resetFilter"
        >
          <v-row class="mr-2 offset-1">
            <v-form ref="form">
              <v-text-field
                v-model="filters.name"
                prepend-icon="mdi-magnify"
                label="Filtrar por Nombre de GPS"
                clearable
                hide-details
                class="mt-2"
                outlined
                dense
                filled
              />

              <v-autocomplete
                v-model="filters.sim"
                :items="options.chips"
                label="Buscar CHIP"
                item-text="sim"
                item-value="sim"
                prepend-icon="mdi-filter-variant"
                deletable-chips
                hide-details
                small-chips
                outlined
                filled
                clearable
                class="mt-2"
                dense
              />

              <v-autocomplete
                v-model="filters.customer"
                :items="options.groups"
                label="Filtrar por Clientes"
                item-value="id"
                item-text="name"
                prepend-icon="mdi-filter-variant"
                deletable-chips
                hide-details
                small-chips
                outlined
                filled
                clearable
                class="mt-2"
                dense
              />

              <v-select
                v-model="filters.agency"
                :items="options.agencies"
                label="Sucursal"
                item-text="name"
                item-value="name"
                hide-details
                :menu-props="{ offsetY: true }"
                prepend-icon="mdi-filter-variant"
                class="mt-2"
                clearable
                outlined
                filled
                dense
              />

              <v-select
                v-model="filters.department"
                :items="options.departments"
                label="Departamento"
                item-text="name"
                item-value="name"
                :menu-props="{ offsetY: true }"
                prepend-icon="mdi-filter-variant"
                class="mt-2"
                hide-details
                clearable
                outlined
                filled
                dense
              />
            </v-form>
          </v-row>
          <v-switch
            v-for="(toggle, i) in switches"
            :key="i"
            dense
            hide-details
            color="primary"
            :disabled="toggle.value === null"
            :input-value="toggle.value"
            :label="toggle.label"
            @change="toggle.change"
            class="offset-1"
          />
        </search-panel>
        <v-card
          class="d-flex justify-end align-center flex-wrap px-3"
          dark
          flat
        >
          <v-card
            flat
            class="d-flex d-flex justify-space-between align-center flex-wrap py-2"
            :class="'flex-grow-1 flex-shrink-0'"
          >
            <v-dialog
              ref="dialog"
              v-model="modal_date_install"
              :return-value.sync="date_install"
              persistent
              width="354px"
            >
              <template v-slot:activator="{ on, attrs }">
                <v-text-field
                  v-model="dateInstallRangeText"
                  label="Filtro Fechas de instalacion"
                  placeholder="Seleccione un Rango de Fechas"
                  prepend-icon="mdi-calendar"
                  append-outer-icon="mdi-close"
                  v-bind="attrs"
                  v-on="on"
                  hide-details
                  outlined
                  readonly
                  dense
                  @click:append-outer="
                    (filters.datesInstall = []), (date_install = [])
                  "
                ></v-text-field>
              </template>
              <v-date-picker v-model="date_install" range>
                <v-spacer></v-spacer>
                <v-btn
                  text
                  color="primary"
                  @click="(modal_date_install = false), (date_install = [])"
                >
                  Cancel
                </v-btn>
                <v-btn
                  text
                  color="primary"
                  @click="
                    $refs.dialog.save((filters.datesInstall = date_install))
                  "
                >
                  OK
                </v-btn>
              </v-date-picker>
            </v-dialog>
            <v-dialog
              ref="dialog"
              v-model="modal_date_renew"
              :return-value.sync="date_renew"
              persistent
              width="354px"
            >
              <template v-slot:activator="{ on, attrs }">
                <v-text-field
                  v-model="dateRenewRangeText"
                  label="Filtro Fechas de renovacion"
                  placeholder="Seleccione un Rango de Fechas"
                  prepend-icon="mdi-calendar"
                  append-outer-icon="mdi-close"
                  v-bind="attrs"
                  v-on="on"
                  hide-details
                  outlined
                  readonly
                  dense
                  @click:append-outer="
                    (filters.datesRenew = []), (date_renew = [])
                  "
                ></v-text-field>
              </template>
              <v-date-picker v-model="date_renew" range>
                <v-spacer></v-spacer>
                <v-btn
                  text
                  color="primary"
                  @click="(modal_date_renew = false), (date_renew = [])"
                >
                  Cancel
                </v-btn>
                <v-btn
                  text
                  color="primary"
                  @click="$refs.dialog.save((filters.datesRenew = date_renew))"
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
          >
          </table-header-buttons>
        </v-card>
        <v-toolbar flat>
          <v-toolbar-title>Lista de GPS</v-toolbar-title>
          <v-divider class="mx-4" inset vertical></v-divider>
          <v-spacer></v-spacer>
          <v-btn
            color="primary"
            dark
            class="mb-2"
            @click="$router.push({ name: 'gps.create' })"
          >
            Registrar GPS
          </v-btn>
        </v-toolbar>
      </template>
      <template #[`item.action`]="{ item }">
        <v-menu offset-x transition="slide-x-transition" rounded="r-xl">
          <template v-slot:activator="{ on, attrs }">
            <v-btn icon v-bind="attrs" v-on="on">
              <v-icon>mdi-dots-vertical</v-icon>
            </v-btn>
          </template>
          <v-list shaped dense>
            <v-list-item-group>
              <v-list-item @click="dialogShow(item.id)">
                <v-list-item-icon>
                  <v-icon class="blue--text">mdi-information-outline</v-icon>
                </v-list-item-icon>
                <v-list-item-content>
                  <v-list-item-title>Detalle GPS</v-list-item-title>
                </v-list-item-content>
              </v-list-item>
              <v-list-item
                v-if="invoiced(item.renew_date)"
                @click="dialogInvoice(item.id)"
              >
                <v-list-item-icon>
                  <v-icon class="green--text">mdi-receipt</v-icon>
                </v-list-item-icon>
                <v-list-item-content>
                  <v-list-item-title>Facturar GPS</v-list-item-title>
                </v-list-item-content>
              </v-list-item>
              <v-list-item
                v-if="invoiced(item.renew_date) && !item.cancellation_date"
                @click="dialogCancel(item.id)"
              >
                <v-list-item-icon>
                  <v-icon class="red--text">mdi-cancel</v-icon>
                </v-list-item-icon>
                <v-list-item-content>
                  <v-list-item-title>Cancelar GPS</v-list-item-title>
                </v-list-item-content>
              </v-list-item>
            </v-list-item-group>
          </v-list>
        </v-menu>
      </template>
      <template #[`item.name`]="{ item }">
        <v-list-item dense>
          <v-list-item-content class="py-0">
            <v-list-item-title
              class="caption font-weight-bold text-no-wrap text-uppercase"
            >
              {{ item.name }}
            </v-list-item-title>
            <v-list-item-subtitle
              v-if="item.gps_group"
              class="caption text-no-wrap text-uppercase"
            >
              {{ item.gps_group.name }}
            </v-list-item-subtitle>
          </v-list-item-content>
        </v-list-item>
      </template>
      <template #[`item.chip.sim`]="{ value }">
        <span class="caption text-no-wrap" v-text="value" />
      </template>
      <template #[`item.chip.costo`]="{ value }">
        <span class="caption text-no-wrap">
          {{ value | currency }}
        </span>
      </template>
      <template #[`item.invoice`]="{ item }">
        <base-tooltip
          v-if="!invoiced(item.renew_date)"
          :text="$appFormatters.formatDate(item.invoice_date, 'll')"
        >
          <template #activator="{ on }">
            <span v-on="on" class="overline font-weight-black text-no-wrap">
              {{ item.invoice }}
            </span>
          </template>
        </base-tooltip>
        <v-btn
          v-else
          outlined
          color="grey darken-3"
          small
          @click="dialogInvoice(item.id)"
        >
          <v-icon small left>
            mdi-receipt
          </v-icon>
          facturar
        </v-btn>
      </template>
      <template #[`item.amount`]="{ item }">
        <span
          v-if="!invoiced(item.renew_date)"
          class="caption font-weight-black text-no-wrap"
        >
          {{ item.amount | currency }} {{ item.currency }}
        </span>
        <span v-else class="caption font-weight-black text-no-wrap">
          {{ 0 | currency }} {{ item.currency }}
        </span>
      </template>
      <template #[`item.installation_date`]="{ value }">
        <span class="caption text-uppercase text-no-wrap">
          {{ $appFormatters.formatDate(value, "MMM YYYY") }}
        </span>
      </template>
      <template #[`item.renew_date`]="{ value }">
        <span class="caption text-uppercase text-no-wrap">
          {{ $appFormatters.formatDate(value, "MMM YYYY") }}
        </span>
      </template>
      <template #[`item.renew_date_day`]="{ item }">
        <v-chip
          :color="colorDay(item.renew_date)"
          class="caption text-no-wrap"
          dark
          label
        >
          {{ $appFormatters.formatTimeDiffNow(item.renew_date, "days") }} Dias
        </v-chip>
      </template>
    </v-data-table>

    <v-dialog
      v-if="dialog.show"
      v-model="dialog.show"
      transition="dialog-bottom-transition"
      :fullscreen="dialog.fullscreen"
      max-width="600"
      persistent
    >
      <v-card>
        <v-toolbar class="secondary">
          <v-btn icon @click.native="dialog.show = false">
            <v-icon>mdi-close</v-icon>
          </v-btn>
          <v-toolbar-title class="white--text">
            {{ dialog.title }}
          </v-toolbar-title>
          <v-spacer></v-spacer>
          <v-toolbar-items>
            <v-btn text @click.native="dialog.show = false">
              Done
            </v-btn>
          </v-toolbar-items>
        </v-toolbar>
        <v-container>
          <component :is="dialog.component" v-bind="currentProperties" />
        </v-container>
      </v-card>
    </v-dialog>
  </v-container>
</template>

<script>
import BaseTooltip from "../../components/Base/BaseTooltip.vue";
import DrawerRigthFilter from "../../components/shared/DrawerRigthFilter.vue";
import GpsWidgetStats from "@admin/gps/widgets/GpsStats.vue";
import Agencies from "~/api/agencies.json";
import Departments from "~/api/departments.json";
import EditGps from "./Edit";
import InvoiceGps from "./Invoice";
import CancelGps from "./Cancel";
import SearchPanel from "../../components/shared/SearchPanel.vue";
import TableHeaderButtons from "../../components/shared/TableHeaderButtons.vue";

export default {
  components: {
    BaseTooltip,
    DrawerRigthFilter,
    GpsWidgetStats,
    EditGps,
    InvoiceGps,
    CancelGps,
    SearchPanel,
    TableHeaderButtons,
  },
  data() {
    return {
      showSearchPanel: false,
      modal_date_install: false,
      date_install: [],
      modal_date_renew: false,
      date_renew: [],
      items: [],
      headers: [
        {
          value: "action",
          align: "left",
          divider: true,
          sortable: false,
          cellclass: "blue",
          class: "blue-grey darken-5",
        },
        {
          text: "Nombre GPS / Cliente",
          value: "name",
          align: "left",
          sortable: false,
          class: "blue-grey darken-5 white--text overline text-truncate",
        },
        {
          text: "SIM / CHIP",
          value: "chip.sim",
          align: "right",
          divider: true,
          sortable: false,
          class: "blue-grey darken-5 white--text overline text-truncate",
        },
        {
          text: "Costo:",
          value: "chip.costo",
          align: "left",
          sortable: false,
          class: "blue-grey darken-5 white--text overline text-truncate",
        },
        {
          text: "Factura:",
          value: "invoice",
          align: "center",
          sortable: true,
          class: "blue-grey darken-5 white--text overline text-truncate",
        },
        {
          text: "Monto:",
          value: "amount",
          align: "right",
          sortable: true,
          class: "blue-grey darken-5 white--text overline text-truncate",
        },
        {
          text: "Instalado:",
          value: "installation_date",
          align: "center",
          sortable: true,
          class: "blue-grey darken-5 white--text overline text-truncate",
        },
        {
          text: "Renueva:",
          value: "renew_date",
          align: "center",
          sortable: true,
          class: "blue-grey darken-5 white--text overline text-truncate",
        },
        {
          text: "Vence en:",
          value: "renew_date_day",
          align: "center",
          sortable: false,
          class: "blue-grey darken-5 white--text overline text-truncate",
        },
      ],
      filters: {
        name: null,
        sim: null,
        agency: null,
        department: null,
        customer: null,
        defeated: false,
        canceled: false,
        datesInstall: [],
        datesRenew: [],
      },
      options: {
        chips: [],
        groups: [],
        agencies: Agencies,
        departments: Departments,
      },
      dialog: {
        show: false,
        fullscreen: true,
        props: {},
        title: "",
        component: "",
      },
      totalItems: 0,
      pagination: {
        itemsPerPage: 10,
        page: 1,
      },
    };
  },
  mounted() {
    const _this = this;
    _this.loadGps(() => {});
    _this.loadSearchOptions(() => {});
    _this.$store.commit("setBreadcrumbs", [{ label: "GPS", name: "" }]);
    _this.$eventBus.$on(["GPS_REFRESH"], () => {
      _this.loadGps(() => {
        _this.dialog.show = false;
      });
    });
  },
  watch: {
    pagination: {
      handler: _.debounce(function () {
        this.loadGps(() => {});
      }, 999),
      deep: true,
    },
    filters: {
      handler: _.debounce(function (v) {
        this.loadGps(() => {});
      }, 999),
      deep: true,
    },
  },
  computed: {
    minHeight() {
      const height = this.$vuetify.breakpoint.mdAndUp ? "85vh" : "60vh";

      return `calc(${height} - ${this.$vuetify.application.top}px)`;
    },
    rightDrawer: {
      get() {
        return this.showSearchPanel;
      },
      set(_showSearchPanel) {
        this.showSearchPanel = _showSearchPanel;
      },
    },
    switches() {
      const self = this;
      return [
        {
          value: self.filters.defeated,
          label: `Por Renovar: ${self.filters.defeated ? "on" : "off"}`,
          change: () => {
            self.filters.defeated = !self.filters.defeated;
          },
        },
        {
          value: self.filters.canceled,
          label: `Cancelados: ${self.filters.canceled ? "on" : "off"}`,
          change: () => {
            self.filters.canceled = !self.filters.canceled;
          },
        },
      ];
    },
    currentProperties() {
      // if (this.dialog.component == 'EditGps') {
      return this.dialog.props;
      // }
    },
    dateInstallRangeText: {
      get: function () {
        return this.filters.datesInstall.join(",");
      },
      set: function (newValue) {
        newValue ? (this.filters.datesInstall = newValue.split(" ")) : [];
      },
    },
    dateRenewRangeText: {
      get: function () {
        return this.filters.datesRenew.join(",");
      },
      set: function (newValue) {
        newValue ? (this.filters.datesRenew = newValue.split(" ")) : [];
      },
    },
  },
  methods: {
    loadGps(cb) {
      const self = this;
      let params = {
        ...self.filters,
        canceled: self.filters.canceled ? "on" : null,
        defeated: self.filters.defeated ? "on" : null,
        order_sort: self.pagination.sortDesc[0] ? "desc" : "asc",
        order_by: self.pagination.sortBy[0] || "name",
        page: self.pagination.page,
        per_page: self.pagination.itemsPerPage,
      };
      axios.get("/admin/gps", { params: params }).then(function (response) {
        let Response = response.data.data;
        self.items = Response.gps.data;
        self.totalItems = Response.gps.total;
        self.pagination.totalItems = Response.gps.total;
        (cb || Function)();
      });
    },
    loadSearchOptions(cb) {
      const self = this;
      axios.get("/admin/gps/search/resources").then(function (response) {
        self.options.chips = response.data.data.chips;
        self.options.groups = response.data.data.groups;
        (cb || Function)();
      });
    },
    colorDay(date) {
      let dateInDays = this.$appFormatters.formatTimeDiffNow(date);
      if (dateInDays < 31) return "red darken-3";
      else if (dateInDays < 62) return "orange darken-3";
      else if (dateInDays) return "green darken-3";
    },
    resetFilter() {
      const _this = this;
      _this.$refs.form.reset();
      _this.pagination.itemsPerPage = 10;
      _this.pagination.page = 1;
      _this.filters.defeated = false;
      _this.filters.canceled = false;
    },
    invoiced(renew_date) {
      return moment(renew_date).year() < moment().add(1, "y").year();
    },
    dialogEdit(id) {
      this.dialog.title = "Editar GPS";
      this.dialog.component = "EditGps";
      this.dialog.props = { propGpsId: id };
      this.dialog.fullscreen = true;
      this.dialog.show = true;
    },
    dialogShow(id) {
      this.dialog.title = "Informacion GPS";
      this.dialog.component = "EditGps";
      this.dialog.props = { propGpsId: id };
      this.dialog.fullscreen = true;
      this.dialog.show = true;
    },
    dialogInvoice(id) {
      this.dialog.title = "Facturar GPS";
      this.dialog.component = "InvoiceGps";
      this.dialog.props = { propGpsId: id };
      this.dialog.fullscreen = false;
      this.dialog.show = true;
    },
    dialogCancel(id) {
      const _this = this;
      _this.dialog.title = "Cancelar GPS";
      _this.dialog.component = "CancelGps";
      _this.dialog.props = { propGpsId: id };
      _this.dialog.fullscreen = false;
      _this.dialog.show = true;
    },
    updateSearchPanel() {
      this.rightDrawer = !this.rightDrawer;
    },
    cancelSearch() {
      this.showSearchPanel = false;
    },
    reloadTable() {
      this.loadGps(() => {});
    },
    exportTable() {
      const self = this;

      let params = {
        ...self.filters,
        canceled: self.filters.canceled ? "on" : null,
        defeated: self.filters.defeated ? "on" : null,
        order_sort: self.pagination.sortDesc[0] ? "desc" : "asc",
        order_by: self.pagination.sortBy[0] || "name",
        paginate: "no",
      };

      axios
        .get("/admin/gps-export", {
          params: params,
          responseType: "blob",
        })
        .then((res) => {
          const url = window.URL.createObjectURL(new Blob([res.data]));
          const link = document.createElement("a");
          link.href = url;
          link.setAttribute("download", "gps.xlsx"); //or any other extension
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
        })
        .finally(function () {
          self.$store.commit("hideLoader");
        });
    },
  },
};
</script>

<style></style>
