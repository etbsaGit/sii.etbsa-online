<template>
  <v-container fluid>
    <v-data-table
      v-bind:headers="headers"
      :items="items"
      :options.sync="pagination"
      :server-items-length="totalItems"
      dense
      caption
      fixed-header
      class="elevation-1"
    >
      <!-- ToTable -->
      <template v-slot:top>
        <search-panel
          :rightDrawer="rightDrawer"
          @cancelSearch="cancelSearch"
          @resetFilter="resetFilter"
        >
          <v-form ref="form">
            <v-row class="mr-2 offset-1 overline" dense>
              <v-text-field
                v-model="filters.folio"
                label="Buscar Folio:"
                prepend-icon="mdi-magnify"
                hide-details
                clearable
              ></v-text-field>
              <v-text-field
                v-model="filters.title"
                label="Fitrar por Titulo Seguimiento"
                prepend-icon="mdi-magnify"
                hide-details
                clearable
              ></v-text-field>
              <v-select
                v-model="filters.category"
                :items="options.categories"
                label="Categoria:"
                prepend-icon="mdi-magnify"
                hide-details
                clearable
                filled
                dense
              ></v-select>
              <v-autocomplete
                v-model="filters.prospect"
                :items="options.prospects"
                label="Filtro Prospecto:"
                prepend-icon="mdi-filter-variant"
                item-text="full_name"
                item-value="id"
                hide-details
                clearable
                multiple
                filled
                dense
              ></v-autocomplete>
              <v-autocomplete
                v-model="filters.agencies"
                :items="options.agencies"
                item-text="title"
                item-value="id"
                label="Filtrar Agencia:"
                prepend-icon="mdi-filter-variant"
                hide-details
                clearable
                multiple
                filled
                dense
              ></v-autocomplete>
              <v-autocomplete
                v-model="filters.departments"
                :items="options.departments"
                item-text="title"
                item-value="id"
                label="Filtrar Depto."
                prepend-icon="mdi-filter-variant"
                hide-details
                clearable
                multiple
                filled
                dense
              ></v-autocomplete>
              <v-autocomplete
                v-model="filters.sellers"
                :items="options.sellers"
                item-text="name"
                item-value="id"
                label="Filtro Vendedor:"
                prepend-icon="mdi-filter-variant"
                hide-details
                clearable
                multiple
                filled
                dense
              ></v-autocomplete>
              <v-select
                v-model="filters.assertiveness"
                :items="options.assertiveness"
                label="Certeza:"
                prepend-icon="mdi-filter-variant"
                clearable
                hide-details
                filled
                dense
              >
                <template v-slot:item="data">
                  <v-list-item-content>
                    <v-list-item-title
                      class="overline"
                      v-text="data.item.text"
                    ></v-list-item-title>
                  </v-list-item-content>
                  <v-list-item-action>
                    <v-btn :color="data.item.color">
                      {{ data.item.value | percent }}
                    </v-btn>
                  </v-list-item-action>
                </template>
              </v-select>
            </v-row>
          </v-form>
        </search-panel>
        <v-card class="d-flex justify-end align-center flex-wrap px-3" flat>
          <v-card
            flat
            class="d-flex d-flex justify-space-between align-center flex-wrap py-2"
            :class="'flex-grow-1 flex-shrink-0'"
          >
            <v-dialog
              ref="dialog"
              v-model="modal"
              :return-value.sync="date"
              persistent
              width="354px"
            >
              <template v-slot:activator="{ on, attrs }">
                <v-text-field
                  v-model="dateRangeText"
                  label="Rango de Fechas"
                  placeholder="Seleccione un Rango de fechas"
                  prepend-inner-icon="mdi-calendar"
                  readonly
                  outlined
                  hide-details
                  dense
                  color="orange"
                  v-bind="attrs"
                  v-on="on"
                  append-outer-icon="mdi-backspace"
                  @click:append-outer="(filters.dates = []), (date = [])"
                ></v-text-field>
              </template>
              <v-date-picker v-model="date" range>
                <v-spacer></v-spacer>
                <v-btn
                  text
                  color="primary"
                  @click="(modal = false), (date = [])"
                >
                  Cancel
                </v-btn>
                <v-btn
                  text
                  color="primary"
                  @click="$refs.dialog.save((filters.dates = date))"
                >
                  OK
                </v-btn>
              </v-date-picker>
            </v-dialog>
            <v-select
              v-model="filters.estatus"
              :items="options.estatus"
              label="Estatus"
              class="ma-2"
              outlined
              filled
              hide-details
              dense
            ></v-select>

            <v-btn
              @click="$router.push({ name: 'tracking.diary' })"
              color="purple"
              class="ma-2"
            >
              Calendario
              <v-icon small right>mdi-calendar-account</v-icon>
            </v-btn>
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
          <v-toolbar-title>Lista de Seguimiento a Prospectos</v-toolbar-title>
          <v-divider class="mx-4" inset vertical></v-divider>
          <v-spacer></v-spacer>
          <v-btn
            @click="$router.push({ name: 'tracking.create' })"
            color="primary"
            class="ml-2"
          >
            Nuevo Seguimiento
            <v-icon small right>mdi-plus-box</v-icon>
          </v-btn>
        </v-toolbar>
      </template>
      <!-- Body -->
      <template v-slot:[`item.action`]="{ item }">
        <v-menu offset-x>
          <template v-slot:activator="{ on, attrs }">
            <v-btn icon small v-bind="attrs" v-on="on">
              <v-icon>mdi-dots-vertical</v-icon>
            </v-btn>
          </template>
          <v-list shaped dense>
            <v-list-item-group>
              <v-list-item
                @click="(dialogs.id = item.id), (dialogs.show = true)"
              >
                <v-list-item-icon>
                  <v-icon class="blue--text">mdi-information-outline</v-icon>
                </v-list-item-icon>
                <v-list-item-content>
                  <v-list-item-title>Ver Seguimiento</v-list-item-title>
                </v-list-item-content>
              </v-list-item>
              <v-list-item
                v-if="$gate.allow('isGerente', 'tracking')"
                @click="resetToActive(item.id)"
              >
                <v-list-item-icon>
                  <v-icon class="blue--text">mdi-undo-variant</v-icon>
                </v-list-item-icon>
                <v-list-item-content>
                  <v-list-item-title>Regresar como Activo</v-list-item-title>
                </v-list-item-content>
              </v-list-item>
            </v-list-item-group>
          </v-list>
        </v-menu>
      </template>
      <template v-slot:[`item.id`]="{ item }">
        <span class="caption"> #{{ item.id.toString().padStart(5, 0) }} </span>
      </template>
      <template v-slot:[`item.title`]="{ item }">
        <v-list-item dense class="pa-0">
          <v-list-item-content class="pa-0">
            <v-list-item-title>{{ item.title }}</v-list-item-title>
            <v-list-item-subtitle>
              <span
                class="d-inline-block text-truncate text-capitalize caption"
                style="max-width: 150px;"
              >
                {{ item.reference }}
              </span>
            </v-list-item-subtitle>
          </v-list-item-content>
        </v-list-item>
      </template>
      <template v-slot:[`item.price`]="{ item }">
        <span
          class="d-inline-block text-truncate text-capitalize caption"
          style="max-width: 150px;"
        >
          {{ item.price | currency }}
        </span>
      </template>
      <template v-slot:[`item.currency`]="{ item }">
        <span
          class="d-inline-block text-truncate text-capitalize caption"
          style="max-width: 150px;"
        >
          {{ item.currency }}
        </span>
      </template>
      <template v-slot:[`item.prospect.full_name`]="{ item }">
        <v-list-item dense class="pa-0">
          <v-list-item-content class="pa-0">
            <v-list-item-title>{{ item.prospect.full_name }}</v-list-item-title>
            <v-list-item-subtitle v-if="item.prospect.is_moral">
              <span
                class="d-inline-block text-truncate text-capitalize caption"
                style="max-width: 200px;"
              >
                {{ item.prospect.company }}
              </span>
            </v-list-item-subtitle>
          </v-list-item-content>
        </v-list-item>
      </template>
      <template v-slot:[`item.attended.name`]="{ item }">
        <span
          class="d-inline-block text-truncate text-capitalize caption"
          style="max-width: 150px;"
        >
          {{ item.attended.name }}
        </span>
      </template>
      <template v-slot:[`item.agency-depto`]="{ item }">
        <v-list-item dense class="pa-0 caption">
          <v-list-item-content class="pa-0 caption">
            <v-list-item-title>{{ item.agency.title }}</v-list-item-title>
            <v-list-item-subtitle>
              {{ item.department.title }}
            </v-list-item-subtitle>
          </v-list-item-content>
        </v-list-item>
      </template>
      <template v-slot:[`item.estatus.title`]="{ item }">
        <v-chip
          small
          label
          :color="getColor(item.estatus.key)"
          class="text-uppercase buttom white--text"
        >
          {{ item.estatus.title }}
        </v-chip>
      </template>
      <template v-slot:[`item.date_next_tracking`]="{ item }">
        <v-chip
          small
          label
          class="text-uppercase buttom white--text"
          :color="getColorDays(item.date_next_tracking)"
        >
          {{
            $appFormatters.formatTimeDiffNow(item.date_next_tracking, "days") ||
            "0"
          }}
          dias
        </v-chip>
      </template>
      <template v-slot:[`item.assertiveness`]="{ item }">
        <v-tooltip left>
          <template v-slot:activator="{ on, attrs }">
            <v-progress-linear
              v-if="getAssertiveness(item.assertiveness)"
              height="25"
              :value="getAssertiveness(item.assertiveness).value * 100"
              :color="getAssertiveness(item.assertiveness).color"
              v-bind="attrs"
              v-on="on"
            >
              <template v-slot:default="{ value }">
                <strong> {{ Math.ceil(value) }}% </strong>
              </template>
            </v-progress-linear>
          </template>
          <span>{{ getAssertiveness(item.assertiveness).text }}</span>
        </v-tooltip>
      </template>
      <template v-slot:[`item.updated_at`]="{ item }">
        {{ $appFormatters.formatDate(item.updated_at, "L") }}
      </template>
    </v-data-table>

    <v-dialog
      v-model="dialogs.show"
      fullscreen
      transition="dialog-bottom-transition"
      :overlay="false"
    >
      <v-card>
        <v-toolbar class="primary">
          <v-toolbar-title class="white--text">
            Detalle Seguimiento
          </v-toolbar-title>
          <v-spacer></v-spacer>
          <v-toolbar-items>
            <v-btn
              icon
              x-large
              color="error"
              @click.native="(dialogs.show = false), (dialogs.id = null)"
            >
              <v-icon>mdi-close-box</v-icon>
            </v-btn>
          </v-toolbar-items>
        </v-toolbar>
        <tracking-prospect
          v-if="dialogs.show && dialogs.id"
          :propTrackingId="dialogs.id"
        ></tracking-prospect>
      </v-card>
    </v-dialog>
  </v-container>
</template>

<script>
import Categories from "@admin/sales/tracking/resources/categories.json";
import Assertiveness from "@admin/sales/tracking/resources/assertiveness.json";
import { mapState } from "vuex";
import TrackingProspect from "./TrackingProspect.vue";
import Notification from "./components/Notification.vue";
import SearchPanel from "@admin/components/shared/SearchPanel.vue";
import TableHeaderButtons from "@admin/components/shared/TableHeaderButtons.vue";

export default {
  components: {
    TrackingProspect,
    Notification,
    SearchPanel,
    TableHeaderButtons,
  },
  data() {
    return {
      showSearchPanel: false,
      dialogs: {
        show: false,
        id: null,
      },
      date: [],
      modal: false,
      headers: [
        {
          text: "",
          value: "action",
          align: "center",
          sortable: false,
        },
        {
          text: "Folio",
          value: "id",
          align: "left",
          sortable: false,
          class: "blue-grey darken-5 white--text overline text-truncate",
        },
        {
          text: "Titulo / Referencia",
          value: "title",
          align: "left",
          sortable: false,
          class: "blue-grey darken-5 white--text overline text-truncate",
        },
        {
          text: "Precio",
          value: "price",
          align: "right",
          sortable: true,
          class: "blue-grey darken-5 white--text overline text-truncate",
        },
        {
          text: "Moneda",
          value: "currency",
          align: "center",
          sortable: true,
          class: "blue-grey darken-5 white--text overline text-truncate",
        },
        {
          text: "Prospecto:",
          value: "prospect.full_name",
          align: "left",
          sortable: false,
          class: "blue-grey darken-5 white--text overline text-truncate",
        },
        {
          text: "Atendido por:",
          value: "attended.name",
          align: "left",
          sortable: false,
          class: "blue-grey darken-5 white--text overline text-truncate",
        },
        {
          text: "Agencia / Departamento",
          value: "agency-depto",
          sortable: false,
          class: "blue-grey darken-5 white--text overline text-truncate",
        },
        {
          text: "Estatus",
          value: "estatus.title",
          align: "center",
          sortable: false,
          class: "blue-grey darken-5 white--text overline text-truncate",
        },
        {
          text: "Sig. Seg.",
          value: "date_next_tracking",
          align: "center",
          sortable: true,
          class: "blue-grey darken-5 white--text overline text-truncate",
        },
        {
          text: "Certeza",
          value: "assertiveness",
          align: "center",
          sortable: true,
          class: "blue-grey darken-5 white--text overline text-truncate",
        },
        {
          text: "Ultimo Cambio",
          value: "updated_at",
          align: "center",
          sortable: true,
          class: "blue-grey darken-5 white--text overline text-truncate",
        },
      ],
      items: [],
      totalItems: 0,
      pagination: {
        rowsPerPage: 10,
      },

      filters: {
        folio: null,
        title: null,
        category: null,
        assertiveness: null,
        prospect: [],
        agencies: [],
        departments: [],
        sellers: [],
        dates: [],
        estatus: "todos",
      },

      options: {
        prospects: [],
        agencies: [],
        departments: [],
        sellers: [],
        categories: Categories,
        assertiveness: Assertiveness,
        estatus: [
          { text: "Activos", value: "activo" },
          { text: "Finalizados", value: "finalizado" },
          { text: "Formalizados", value: "formalizado" },
          { text: "Todos", value: "todos" },
        ],
      },
    };
  },
  mounted() {
    const self = this;

    self.$store.commit("setBreadcrumbs", [{ label: "Seguimientos", name: "" }]);
    self.loadResources(() => {});
  },
  computed: {
    ...mapState(["Assertiveness"]),
    dateRangeText: {
      get: function () {
        return this.filters.dates.join(",");
      },
      set: function (newValue) {
        newValue ? (this.filters.dates = newValue.split(" ")) : [];
      },
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
      handler: _.debounce(function (v) {
        this.loadTrackings(() => {});
      }, 700),
      deep: true,
    },
    filters: {
      handler: _.debounce(function (v) {
        this.loadTrackings(() => {});
      }, 700),
      deep: true,
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
    },
    refresh() {
      const self = this;
      self.loadTrackings(() => {});
    },
    loadTrackings(cb) {
      const self = this;

      let params = {
        ...self.filters,
        sellers: self.filters.sellers.join(","),
        prospect: self.filters.prospect.join(","),
        agencies: self.filters.agencies.join(","),
        departments: self.filters.departments.join(","),
        dates: self.dateRangeText,
        order_sort: self.pagination.sortDesc[0] ? "desc" : "asc",
        order_by: self.pagination.sortBy[0] || "id",
        page: self.pagination.page,
        per_page: self.pagination.itemsPerPage,
      };

      axios
        .get("/admin/tracking", { params: params })
        .then(function (response) {
          self.items = response.data.data.data;
          self.totalItems = response.data.data.total;
          self.pagination.totalItems = response.data.data.total;
          (cb || Function)();
        });
    },
    loadResources(cb) {
      const self = this;

      let params = {
        paginate: "no",
      };
      axios
        .get("/admin/tracking/sales_history/resources")
        .then(function (response) {
          let Data = response.data.data;
          self.options.agencies = Data.agencies;
          self.options.departments = Data.departments;
          self.options.prospects = Data.prospects;
        });

      axios.get("/admin/sellers", { params: params }).then(function (response) {
        self.options.sellers = response.data.data;
        (cb || Function)();
      });
    },
    getColor(value) {
      if (value == "finalizado") return "red";
      else if (value == "formalizado") return "blue";
      else return "primary";
    },
    getColorDays(value) {
      let days = this.$appFormatters.formatTimeDiffNow(value, "days");
      if (days < 0) return "red";
      else return "primary";
    },
    getAssertiveness(value) {
      return this.Assertiveness.find((item) => {
        return item.value == value;
      });
    },
    exportTable() {
      const self = this;
      let params = {
        ...self.filters,
        sellers: self.filters.sellers.join(","),
        prospect: self.filters.prospect.join(","),
        agencies: self.filters.agencies.join(","),
        departments: self.filters.departments.join(","),
        dates: self.dateRangeText,
        page: self.pagination.page,
        per_page: self.pagination.itemsPerPage,
        paginate: "no",
      };
      axios
        .get("/admin/tracking-export", {
          params: params,
          responseType: "blob",
        })
        .then((res) => {
          const url = window.URL.createObjectURL(new Blob([res.data]));
          const link = document.createElement("a");
          link.href = url;
          link.setAttribute("download", "tracking.xlsx"); //or any other extension
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
    reset() {
      this.$eventBus.$emit("NOTIFICATION");
      this.filters.dates = [];
      this.$refs.formSearch.reset();
    },
    resetToActive(id) {
      const self = this;
      axios
        .put("/admin/tracking/resetToActive/" + id)
        .then(function (response) {
          self.$store.commit("showSnackbar", {
            message: response.data.message,
            color: "success",
            duration: 3000,
          });
          self.loadTrackings(() => {});
          cb();
        });
    },
  },
};
</script>
