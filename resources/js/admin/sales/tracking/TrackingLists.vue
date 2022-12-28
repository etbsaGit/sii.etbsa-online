<template>
  <v-card flat class="table-to-print">
    <v-data-table
      v-bind:headers="headers"
      :items="items"
      :options.sync="pagination"
      :server-items-length="totalItems"
      fixed-header
      item-key="id"
      class="table-rounded elevation-2 text-uppercase d-print-table"
      dense
    >
      <!-- ToTable -->
      <template #top>
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
                <template #item="data">
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
              <template #activator="{ on, attrs }">
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
              dark
            >
              Calendario
              <v-icon small right>mdi-calendar-account</v-icon>
            </v-btn>
            <v-btn
              v-show="false"
              @click="$router.push({ name: 'tracking.stat' })"
              color="blue"
              class="ma-2"
              dark
            >
              Estadisticas
              <v-icon small right>mdi-chart-arc</v-icon>
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
          <v-toolbar-title>Seguimiento a Prospectos</v-toolbar-title>
          <v-divider class="mx-4" inset vertical></v-divider>
          <v-spacer></v-spacer>
          <v-btn color="primary" class="ml-2" @click="dialogs.create = true">
            <!-- @click="$router.push({ name: 'tracking.create' })" -->
            Nuevo Seguimiento
            <v-icon small right>mdi-plus-box</v-icon>
          </v-btn>
          <!-- <v-btn
            color="primary"
            class="ml-2"
            @click="dialogCreateStepper = !dialogCreateStepper"
          >
            Nuevo Seguimiento
            <v-icon small right>mdi-plus-box</v-icon>
          </v-btn> -->
        </v-toolbar>

        <!-- Dialog -->
        <dialog-component
          :show="dialogs.show"
          @close="(dialogs.show = false), (dialogs.id = null)"
          fullscreen
          closeable
          title="Detalle Seguimiento"
        >
          <tracking-prospect
            v-if="dialogs.show && dialogs.id"
            :propTrackingId="dialogs.id"
          ></tracking-prospect>
        </dialog-component>

        <v-dialog v-model="dialogs.create" scrollable max-width="600">
          <v-card flat>
            <v-toolbar flat dense height="24">
              <v-spacer />
              <v-icon small color="red" @click="dialogs.create = false"
                >mdi-close</v-icon
              >
            </v-toolbar>
            <tracking-create
              v-if="dialogs.create"
              @success="(dialogs.create = false), reset()"
            ></tracking-create>
          </v-card>
        </v-dialog>
      </template>

      <!-- body.prepend -->

      <template #[`body.prepend`] v-if="!$vuetify.breakpoint.mobile">
        <tr>
          <td></td>
          <td>
            <div class="d-flex flex-column py-2">
              <v-text-field
                class="d-block font-weight-semibold text--primary text-truncate"
                v-model="filters.folio"
                placeholder="Buscar"
                hide-details
                outlined
                filled
                dense
                solo
              ></v-text-field>
            </div>
          </td>
          <td>
            <div class="d-flex flex-column py-2">
              <v-select
                v-model="filters.estatus"
                :items="options.estatus"
                hide-details
                outlined
                filled
                dense
                solo
              ></v-select>
            </div>
          </td>
          <td>
            <div class="d-flex flex-column py-2">
              <v-text-field
                class="d-block font-weight-semibold text--primary text-truncate"
                v-model="filters.title"
                placeholder="Buscar"
                dense
                hide-details
                filled
                solo
                outlined
              ></v-text-field>
            </div>
          </td>
          <td></td>
          <td>
            <div class="d-flex flex-column py-2">
              <v-autocomplete
                class="d-block font-weight-semibold text--primary text-truncate"
                v-model="filters.prospect"
                :items="options.prospects"
                item-text="full_name"
                item-value="id"
                placeholder="Buscar"
                hide-details
                clearable
                multiple
                solo
                outlined
                filled
                dense
              ></v-autocomplete>
            </div>
          </td>
          <td>
            <div class="d-flex flex-column py-2">
              <v-autocomplete
                class="d-block font-weight-semibold text--primary text-truncate"
                v-model="filters.sellers"
                :items="options.sellers"
                placeholder="Buscar"
                item-text="name"
                item-value="id"
                hide-details
                outlined
                clearable
                multiple
                filled
                dense
                solo
              ></v-autocomplete>
            </div>
          </td>
          <td>
            <div class="d-flex flex-column py-2">
              <v-autocomplete
                v-model="filters.agencies"
                :items="options.agencies"
                item-text="title"
                item-value="id"
                placeholder="Buscar"
                hide-details
                clearable
                multiple
                outlined
                filled
                dense
                solo
              ></v-autocomplete>
            </div>
          </td>

          <td></td>
          <td>
            <v-select
              v-model="filters.assertiveness"
              :items="options.assertiveness"
              placeholder="Buscar"
              hide-details
              outlined
              clearable
              multiple
              filled
              dense
              solo
            >
              <template #item="data">
                <v-list-item-content>
                  <v-list-item-title
                    class="overline"
                    v-text="data.item.text"
                  ></v-list-item-title>
                </v-list-item-content>
                <v-list-item-action>
                  <v-btn :color="data.item.color" dark>
                    {{ data.item.value | percent }}
                  </v-btn>
                </v-list-item-action>
              </template>
            </v-select>
          </td>
          <td></td>
        </tr>
      </template>

      <!--  -->
      <!-- Body -->
      <template #[`item.action`]="{ item }">
        <v-menu offset-x>
          <template #activator="{ on, attrs }">
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
                  <v-icon class="blue--text" left>
                    mdi-information-outline
                  </v-icon>
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
                  <v-icon class="blue--text" left>mdi-undo-variant</v-icon>
                </v-list-item-icon>
                <v-list-item-content>
                  <v-list-item-title>Regresar como Activo</v-list-item-title>
                </v-list-item-content>
              </v-list-item>
              <v-list-item
                :href="`/admin/tracking/${item.id}/resources/print`"
                target="_blank"
              >
                <v-list-item-icon>
                  <v-icon class="blue--text" left>mdi-file-pdf-box</v-icon>
                </v-list-item-icon>
                <v-list-item-content>
                  <v-list-item-title>PDF</v-list-item-title>
                </v-list-item-content>
              </v-list-item>
            </v-list-item-group>
          </v-list>
        </v-menu>
      </template>
      <template #[`item.id`]="{ value }">
        <span> #{{ value.toString().padStart(5, 0) }} </span>
      </template>
      <template #[`item.title`]="{ item }">
        <div class="d-flex flex-column">
          <span
            class="d-block font-weight-bold text--primary text-wrap"
            style="min-width: 200px;"
          >
            {{ item.reference }}
          </span>
          <small>{{ item.title }}</small>
        </div>
      </template>
      <template #[`item.price`]="{ item }">
        <span class="d-block font-weight-semibold text-truncate">
          {{ item.price | currency }} {{ item.moneda.name }}
        </span>
      </template>
      <template #[`item.prospect_id`]="{ item }">
        <div class="d-flex flex-column">
          <span class="d-block font-weight-semibold text--primary">
            {{ item.prospect.full_name }}</span
          >
          <small>{{ item.prospect.company }}</small>
        </div>
      </template>
      <template #[`item.attended_by`]="{ item }">
        <div class="d-flex flex-column">
          <span class="d-block font-weight-semibold text-truncate blue--text">
            {{
              !!item.attended.profiable
                ? item.attended.profiable.full_name
                : item.attended.name
            }}
          </span>
          <small>
            {{
              !!item.attended.profiable
                ? item.attended.profiable.agency.title
                : ""
            }}
          </small>
        </div>
      </template>
      <template #[`item.agency_id`]="{ item }">
        <div class="d-flex flex-column">
          <span
            class="d-block font-weight-semibold text-truncate blue--text blue--text"
          >
            {{ item.agency.title }}
          </span>
          <small>
            {{ item.department.title }}
          </small>
        </div>
      </template>
      <template #[`item.estatus_id`]="{ item }">
        <!-- :color="getColor(item.estatus.key)" -->
        <v-chip
          :color="colors[item.estatus.key]"
          class="font-weight-medium"
          small
          dark
        >
          {{ item.estatus.title }}
        </v-chip>
      </template>
      <template #[`item.date_next_tracking`]="{ item }">
        <v-chip
          class="font-weight-medium"
          :color="getColorDays(item.date_next_tracking)"
          small
          dark
        >
          {{
            $appFormatters.formatTimeDiffNow(item.date_next_tracking, "days") ||
            "0"
          }}
          dias
        </v-chip>
      </template>
      <template #[`item.assertiveness`]="{ item }">
        <v-tooltip left>
          <template #activator="{ on, attrs }">
            <v-progress-linear
              v-if="getAssertiveness(item.assertiveness)"
              height="25"
              :value="getAssertiveness(item.assertiveness).value * 100"
              :color="getAssertiveness(item.assertiveness).color"
              v-bind="attrs"
              v-on="on"
              style="width: 75px;"
            >
              <template #default="{ value }">
                <strong> {{ Math.ceil(value) }}% </strong>
              </template>
            </v-progress-linear>
          </template>
          <span>{{ getAssertiveness(item.assertiveness).text }}</span>
        </v-tooltip>
      </template>
      <template #[`item.updated_at`]="{ item }">
        {{ $appFormatters.formatDate(item.updated_at, "L") }}
      </template>
    </v-data-table>
  </v-card>
</template>

<script>
import Categories from "@admin/sales/tracking/resources/categories.json";
import Assertiveness from "@admin/sales/tracking/resources/assertiveness.json";
import { mapState } from "vuex";
import TrackingProspect from "./TrackingProspect.vue";
import Notification from "./components/Notification.vue";
import SearchPanel from "@admin/components/shared/SearchPanel.vue";
import TableHeaderButtons from "@admin/components/shared/TableHeaderButtons.vue";
import DialogComponent from "@admin/components/DialogComponent.vue";
import TrackingCreate from "./TrackingCreate.vue";

export default {
  components: {
    TrackingProspect,
    Notification,
    SearchPanel,
    TableHeaderButtons,
    DialogComponent,
    TrackingCreate,
  },
  data() {
    return {
      dialogCreateStepper: false,
      showSearchPanel: false,
      dialogs: {
        create: false,
        show: false,
        id: null,
      },
      date: [],
      modal: false,
      headers: [
        {
          text: "",
          value: "action",
          sortable: false,
        },
        {
          text: "Folio",
          value: "id",
          sortable: true,
        },
        {
          text: "Estatus",
          value: "estatus_id",
          sortable: true,
        },
        {
          text: "Referencia",
          value: "title",
          sortable: true,
          width: 200,
        },
        {
          text: "Valor",
          value: "price",
          sortable: true,
        },

        {
          text: "Prospecto",
          value: "prospect_id",
          sortable: true,
          width: 200,
        },
        {
          text: "Vendedor",
          value: "attended_by",
          sortable: true,
        },
        {
          text: "Sucursal",
          value: "agency_id",
          sortable: true,
        },

        {
          text: "Sig. Seg.",
          value: "date_next_tracking",
          sortable: true,
        },
        {
          text: "Certeza",
          value: "assertiveness",
          sortable: true,
        },
        {
          text: "Ultimo Cambio",
          value: "updated_at",
          sortable: true,
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
          { text: "Ventas Perdidas", value: "finalizado" },
          { text: "Ventas Ganadas", value: "formalizado" },
          { text: "Todos", value: "todos" },
        ],
      },
      colors: {
        activo: "green",
        finalizado: "red",
        formalizado: "blue",
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
    async loadTrackings(cb) {
      const self = this;

      let params = {
        ...self.filters,
        sellers: self.filters.sellers.join(","),
        prospect: self.filters.prospect.join(","),
        agencies: self.filters.agencies.join(","),
        departments: self.filters.departments.join(","),
        dates: self.dateRangeText,
        order_sort: self.pagination.sortDesc[0] ? "asc" : "desc",
        order_by: self.pagination.sortBy[0] || "id",
        page: self.pagination.page,
        per_page: self.pagination.itemsPerPage,
      };

      // axios
      //   .get("/admin/tracking", { params: params })
      //   .then(function (response) {
      //     self.items = response.data.data.data;
      //     self.totalItems = response.data.data.total;
      //     self.pagination.totalItems = response.data.data.total;
      //     (cb || Function)();
      //   });

      let { data } = await axios.get("/admin/tracking", { params: params });
      self.items = data.data.data;
      self.totalItems = data.data.total;
      self.pagination.totalItems = data.data.total;
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

    getColorDays(value) {
      let days = this.$appFormatters.formatTimeDiffNow(value, "days");

      if (days < 0) {
        return "red";
      } else if (days > 0) {
        return "green";
      } else {
        return "grey";
      }
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
        });
    },
    print(id) {
      const self = this;
      axios.get(`/admin/tracking/${id}/resources/print`).then((res) => {
        self.$store.commit("showSnackbar", {
          message: response.data.message,
          color: "success",
          duration: 3000,
        });
      });
    },
  },
};
</script>

<style scoped>
@media only print {
  body {
    visibility: hidden;
    width: 100vh;
    height: 100vh;
  }

  #nav-drawer {
    display: none;
    visibility: hidden;
  }

  #table-to-print {
    visibility: visible;
    width: 100vh;
    min-height: calc(100vh - 58px);
  }
}

@media only screen and (max-width: 600px) {
  #table-to-print {
    width: 100%;
    display: block;
    text-align: center;
  }
}
@page {
  margin: 0px 0px;
}
</style>
