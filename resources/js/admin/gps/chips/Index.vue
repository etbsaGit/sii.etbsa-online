<template>
  <v-container fluid>
    <!-- data table -->
    <v-data-table
      v-bind:headers="headers"
      :options.sync="pagination"
      :items="items"
      :server-items-length="totalItems"
      class="overline"
      fixed-header
      caption
      dense
    >
      <!-- Top -->
      <template v-slot:top>
        <search-panel
          :rightDrawer="rightDrawer"
          @cancelSearch="cancelSearch"
          @resetFilter="resetFilter"
        >
          <v-form ref="form">
            <v-row class="mr-2 offset-1">
              <v-select
                v-model="filters.month"
                prepend-icon="mdi-calendar-month-outline"
                :items="options.months"
                label="Mes"
                :menu-props="{ offsetY: true }"
                item-text="name"
                item-value="value"
                hide-details
                clearable
              ></v-select>
              <v-select
                prepend-icon="mdi-calendar-month-outline"
                v-model="filters.year"
                :items="YearOption"
                item-text="name"
                item-value="name"
                label="Año"
                clearable
              ></v-select>
            </v-row>
            <v-switch
              v-for="(toggle, i) in switches"
              :key="i"
              hide-details
              color="primary"
              :disabled="toggle.value === null"
              :input-value="toggle.value"
              :label="toggle.label"
              @change="toggle.change"
              class="offset-1"
              dense
            />
          </v-form>
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
            <v-text-field
              v-model="search"
              label="BUSCAR por SIM/IMEI"
              prepend-icon="mdi-magnify"
              hide-details
              clearable
              outlined
              dense
            ></v-text-field>
          </v-card>
          <v-spacer></v-spacer>
          <v-divider class="mx-2" inset vertical></v-divider>
          <table-header-buttons
            :updateSearchPanel="updateSearchPanel"
            :reloadTable="refresh"
            :exportTable="exportTable"
          />
        </v-card>
        <v-toolbar flat>
          <v-toolbar-title>Lista de Chips GPS</v-toolbar-title>
          <v-divider class="mx-4" inset vertical></v-divider>
          <v-spacer></v-spacer>
          <v-btn color="primary" dark class="mb-2" @click="dialogs.show = true">
            Registrar Chip de GPS
          </v-btn>
        </v-toolbar>
      </template>

      <!-- Body  -->
      <template v-slot:[`item.action`]="{ item }">
        <v-menu offset-x transition="slide-x-transition" rounded="r-xl">
          <template v-slot:activator="{ on, attrs }">
            <v-btn icon v-bind="attrs" v-on="on">
              <v-icon>mdi-dots-vertical</v-icon>
            </v-btn>
          </template>
          <v-list shaped dense>
            <v-list-item-chip>
              <v-list-item @click="editItem(item)">
                <v-list-item-icon>
                  <v-icon class="blue--text">mdi-information-outline</v-icon>
                </v-list-item-icon>
                <v-list-item-content>
                  <v-list-tile-title>Detalle</v-list-tile-title>
                </v-list-item-content>
              </v-list-item>
              <!-- <v-list-item @click="trash(item)">
                <v-list-item-icon>
                  <v-icon class="red--text">mdi-cancel</v-icon>
                </v-list-item-icon>
                <v-list-item-content>
                  <v-list-tile-title>Cancelar</v-list-tile-title>
                </v-list-item-content>
              </v-list-item> -->
            </v-list-item-chip>
          </v-list>
        </v-menu>
      </template>
      <template v-slot:[`item.costo`]="{ item }">
        {{ item.costo | money }}
      </template>
      <template v-slot:[`item.fecha_activacion`]="{ item }">
        <span class="overline text-capitalize">
          {{ $appFormatters.formatDate(item.fecha_activacion) }}
        </span>
      </template>
      <template v-slot:[`item.gps.id`]="{ item }">
        <v-avatar outlined>
          <v-icon class="red--text" v-if="item.fecha_cancelacion">
            mdi-cancel
          </v-icon>
          <v-icon class="grey--text" v-else-if="item.gps == null">
            mdi-alert-rhombus-outline
          </v-icon>
          <v-tooltip top v-else-if="item.gps.name">
            <template v-slot:activator="{ on, attrs }">
              <v-icon class="green--text" v-bind="attrs" v-on="on">
                mdi-check-circle-outline
              </v-icon>
            </template>
            <span>{{ item.gps.name }}</span>
          </v-tooltip>
        </v-avatar>
      </template>
    </v-data-table>
    <dialog-component
      :show="dialogs.show"
      @close="(dialogs.show = false), (editedIndex = -1)"
      closeable
      max-width="824"
      :fullscreen="$vuetify.breakpoint.mobile"
      :title="formTitle"
    >
      <gps-chip-add v-if="formAdd"></gps-chip-add>
      <gps-chip-edit
        v-else
        :propGpsChipId="dialogs.gpsChip.sim"
      ></gps-chip-edit>
    </dialog-component>
  </v-container>
</template>

<script>
import GpsChipAdd from "@admin/gps/chips/Create.vue";
import GpsChipEdit from "@admin/gps/chips/Edit.vue";

import optionMonths from "~/api/months.json";
import optionYears from "~/api/years.json";
import SearchPanel from "../../components/shared/SearchPanel.vue";
import TableHeaderButtons from "../../components/shared/TableHeaderButtons.vue";
import DialogComponent from "@admin/components/DialogComponent.vue";
export default {
  components: {
    GpsChipAdd,
    GpsChipEdit,
    SearchPanel,
    TableHeaderButtons,
    DialogComponent,
  },
  data() {
    return {
      showSearchPanel: false,
      headers: [
        {
          value: "action",
          align: "center",
          divider: true,
          width: 10,
          sortable: false,
          class: "blue-grey darken-5",
        },
        {
          text: "SIM",
          value: "sim",
          align: "left",
          sortable: true,
          class: "blue-grey darken-5 white--text overline text-truncate",
        },
        {
          text: "IMEI",
          value: "imei",
          align: "left",
          sortable: true,
          class: "blue-grey darken-5 white--text overline text-truncate",
        },
        {
          text: "Costo",
          value: "costo",
          align: "left",
          sortable: false,
          class: "blue-grey darken-5 white--text overline text-truncate",
        },
        {
          text: "Fecha Activacion",
          value: "fecha_activacion",
          align: "center",
          sortable: true,
          class: "blue-grey darken-5 white--text overline text-truncate",
        },
        {
          text: "Fecha Cancelacion",
          value: "fecha_cancelacion",
          align: "center",
          sortable: true,
          class: "blue-grey darken-5 white--text overline text-truncate",
        },
        {
          text: "Asignado",
          value: "gps.id",
          align: "center",
          sortable: false,
          class: "blue-grey darken-5 white--text overline text-truncate",
        },
      ],
      items: [],
      totalItems: 0,
      pagination: {
        itemsPerPage: 10,
      },
      editedIndex: -1,
      dialogs: {
        title: "",
        show: false,
        gpsChip: null,
      },
      options: {
        months: optionMonths,
        years: new Date().getFullYear(),
      },
      search: "",
      filters: {
        sim: "",
        imei: "",
        month: null,
        year: null,
        assigned: false,
        deallocated: false,
        canceled: false,
      },
    };
  },
  mounted() {
    const _this = this;

    _this.$eventBus.$on(
      ["GPS_CHIP_ADDED", "GPS_CHIP_UPDATED", "GPS_CHIP_DELETED", "GPS_REFRESH"],
      () => {
        _this.loadGpsChips(() => {});
      }
    );
  },
  watch: {
    pagination: {
      handler: _.debounce(function () {
        this.loadGpsChips(() => {});
      }, 700),
      deep: true,
    },
    filters: {
      handler: _.debounce(function (v) {
        this.loadGpsChips(() => {});
      }, 700),
      deep: true,
    },
    search: {
      handler: _.debounce(function (v) {
        this.loadGpsChips(() => {});
      }, 700),
      deep: true,
    },
  },
  computed: {
    formTitle() {
      return this.editedIndex === -1
        ? "Registrar CHIP de GPS"
        : "Editar CHIP de GPS";
    },
    formAdd() {
      return this.editedIndex === -1;
    },
    switches() {
      const _this = this;
      return [
        {
          value: _this.filters.assigned,
          label: `Asigndos: ${_this.filters.assigned ? "on" : "off"}`,
          change: () => {
            _this.filters.assigned = !_this.filters.assigned;
          },
        },
        {
          value: _this.filters.deallocated,
          label: `Sin asignar: ${_this.filters.deallocated ? "on" : "off"}`,
          change: () => {
            _this.filters.deallocated = !_this.filters.deallocated;
          },
        },
        {
          value: _this.filters.canceled,
          label: `Cancelados: ${_this.filters.canceled ? "on" : "off"}`,
          change: () => {
            _this.filters.canceled = !_this.filters.canceled;
          },
        },
      ];
    },
    rightDrawer: {
      get() {
        return this.showSearchPanel;
      },
      set(_showSearchPanel) {
        this.showSearchPanel = _showSearchPanel;
      },
    },
    YearOption() {
      return Array.from(
        { length: 5 },
        (v, i) => this.options.years - 5 + i + 2
      );
    },
  },
  methods: {
    updateSearchPanel() {
      this.rightDrawer = !this.rightDrawer;
    },
    cancelSearch() {
      this.showSearchPanel = false;
    },
    resetFilter() {
      const _this = this;
      _this.$refs.form.reset();
      _this.pagination.itemsPerPage = 10;
      _this.pagination.page = 1;
      _this.filters.assigned = false;
      _this.filters.deallocated = false;
      _this.filters.canceled = false;
    },
    refresh() {
      const _this = this;
      _this.loadGpsChips(() => {});
    },
    editItem(item) {
      const _this = this;
      _this.editedIndex = _this.items.indexOf(item);
      _this.dialogs.gpsChip = item;
      _this.dialogs.show = true;
    },
    async loadGpsChips(cb) {
      const _this = this;

      let params = {
        search: _this.search,
        month: _this.filters.month,
        year: _this.filters.year,
        assigned: _this.filters.assigned,
        deallocated: _this.filters.deallocated,
        canceled: _this.filters.canceled,
        order_sort: _this.pagination.sortDesc[0] ? "desc" : "asc",
        order_by: _this.pagination.sortBy[0] || "sim",
        page: _this.pagination.page,
        per_page: _this.pagination.itemsPerPage,
      };

      await axios
        .get("/admin/chips", { params: params })
        .then(function (response) {
          _this.items = response.data.data.data;
          _this.totalItems = response.data.data.total;
          _this.pagination.totalItems = response.data.data.total;
          (cb || Function)();
        });
    },
    async exportTable() {
      const _this = this;

      let params = {
        search: _this.search,
        month: _this.filters.month,
        year: _this.filters.year,
        assigned: _this.filters.assigned,
        deallocated: _this.filters.deallocated,
        canceled: _this.filters.canceled,
        order_sort: _this.pagination.sortDesc[0] ? "desc" : "asc",
        order_by: _this.pagination.sortBy[0] || "sim",
        page: _this.pagination.page,
        per_page: _this.pagination.itemsPerPage,
      };

      _this.$store.commit("showLoader");
      await axios
        .get("/admin/gps-chips-export", {
          params: params,
          responseType: "blob",
        })
        .then((res) => {
          const url = window.URL.createObjectURL(new Blob([res.data]));
          const link = document.createElement("a");
          link.href = url;
          link.setAttribute("download", "gps-chips.xlsx"); //or any other extension
          document.body.appendChild(link);
          link.click();
        })
        .catch(function (error) {
          if (error.response) {
            _this.$store.commit("showSnackbar", {
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
          _this.$store.commit("hideLoader");
        });
    },
  },
};
</script>
