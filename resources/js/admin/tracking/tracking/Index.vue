<template>
  <div class="component-wrap">
    <!-- search -->
    <v-expansion-panels>
      <v-expansion-panel>
        <v-expansion-panel-header
          disable-icon-rotate
          color="grey lighten-3"
          class="titlle text-uppercase font-weight-bold"
        >
          Filtros
          <template v-slot:actions>
            <v-icon> mdi-magnify </v-icon>
          </template>
        </v-expansion-panel-header>
        <v-expansion-panel-content class="pa-0">
          <v-form ref="formSearch">
            <v-row class="mx-2 my-0">
              <v-col cols="12" md="4">
                <v-text-field
                  append-icon="mdi-magnify"
                  label="Buscar Folio:"
                  v-model="filters.folio"
                  outlined
                  hide-details
                  clearable
                  dense
                ></v-text-field>
              </v-col>
              <v-col cols="12" md="4">
                <v-text-field
                  append-icon="mdi-magnify"
                  label="Fitrar por Titulo Seguimiento"
                  v-model="filters.title"
                  outlined
                  hide-details
                  clearable
                  dense
                ></v-text-field>
              </v-col>
              <v-col cols="12" md="4">
                <v-select
                  append-icon="mdi-magnify"
                  v-model="filters.category"
                  :items="options.categories"
                  label="Categoria:"
                  hide-details
                  outlined
                  dense
                ></v-select>
              </v-col>
              <v-col cols="12" md="4">
                <v-autocomplete
                  v-model="filters.prospect"
                  :items="options.prospects"
                  item-text="full_name"
                  item-value="id"
                  label="Filtro Prospecto:"
                  hide-details
                  clearable
                  multiple
                  outlined
                  dense
                ></v-autocomplete>
              </v-col>
              <v-col cols="12" md="4">
                <v-autocomplete
                  v-model="filters.agencies"
                  :items="options.agencies"
                  item-text="title"
                  item-value="id"
                  label="Filtrar Agencia:"
                  hide-details
                  clearable
                  multiple
                  outlined
                  dense
                ></v-autocomplete>
              </v-col>
              <v-col cols="12" md="4">
                <v-autocomplete
                  v-model="filters.departments"
                  :items="options.departments"
                  item-text="title"
                  item-value="id"
                  label="Filtrar Depto."
                  hide-details
                  clearable
                  multiple
                  outlined
                  dense
                ></v-autocomplete>
              </v-col>
              <v-col cols="12" md="4">
                <v-autocomplete
                  v-model="filters.sellers"
                  :items="options.sellers"
                  item-text="name"
                  item-value="id"
                  label="Filtro Vendedor:"
                  hide-details
                  clearable
                  multiple
                  outlined
                  dense
                ></v-autocomplete>
              </v-col>
              <v-col cols="12" md="4">
                <v-select
                  v-model="filters.assertiveness"
                  :items="options.assertiveness"
                  label="Certeza:"
                  dense
                  outlined
                  clearable
                  hide-details
                >
                  <template v-slot:item="data">
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
              </v-col>
              <v-col cols="12" md="4">
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
                      prepend-icon="mdi-calendar"
                      readonly
                      outlined
                      hide-details
                      dense
                      color="orange"
                      v-bind="attrs"
                      v-on="on"
                      append-outer-icon="mdi-close"
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
              </v-col>
              <v-col cols="12" md="6">
                <v-radio-group v-model="filters.estatus" row dense>
                  <v-radio label="Activos" value="activo" />
                  <v-radio label="Finalizados" value="finalizado" />
                  <v-radio label="Fomalizados" value="formalizado" />
                  <v-radio label="Todos" value="todos" />
                </v-radio-group>
              </v-col>
            </v-row>
          </v-form>
        </v-expansion-panel-content>
      </v-expansion-panel>
    </v-expansion-panels>

    <!-- data table -->
    <v-data-table
      v-bind:headers="headers"
      :items="items"
      :options.sync="pagination"
      :server-items-length="totalItems"
      dense
      caption
      fixed-header
      class="elevation-1 caption"
    >
      <template v-slot:top>
        <v-toolbar elevation="0">
          <v-btn
            @click="$router.push({ name: 'tracking.create' })"
            class="primary lighten-1"
            small
            dark
          >
            Levantar un Seguimiento
            <v-icon small right>mdi-plus</v-icon>
          </v-btn>
          <v-btn
            @click="$router.push({ name: 'tracking.diary' })"
            class="accent lighten-1 ml-2"
            small
            dark
          >
            Calendario
            <v-icon small right>mdi-calendar-account</v-icon>
          </v-btn>
          <v-spacer></v-spacer>
          <v-tooltip top>
            <template v-slot:activator="{ on, attrs }">
              <v-btn
                icon
                color="green"
                @click="exportTracking()"
                v-bind="attrs"
                v-on="on"
              >
                <v-icon>mdi-file-excel</v-icon>
              </v-btn>
            </template>
            <span>Exportar</span>
          </v-tooltip>
          <v-tooltip top>
            <template v-slot:activator="{ on, attrs }">
              <v-btn
                icon
                color="grey"
                @click="loadTrackings(() => {})"
                v-bind="attrs"
                v-on="on"
              >
                <v-icon>mdi-refresh</v-icon>
              </v-btn>
            </template>
            <span>Refrescar</span>
          </v-tooltip>
          <v-tooltip top>
            <template v-slot:activator="{ on, attrs }">
              <v-btn
                icon
                color="error"
                @click="reset()"
                v-bind="attrs"
                v-on="on"
              >
                <v-icon>mdi-filter-remove-outline</v-icon>
              </v-btn>
            </template>
            <span>Reset Filtro</span>
          </v-tooltip>
        </v-toolbar>
      </template>

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
                style="max-width: 150px"
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
          style="max-width: 150px"
        >
          {{ item.price | currency }}
        </span>
      </template>
      <template v-slot:[`item.currency`]="{ item }">
        <span
          class="d-inline-block text-truncate text-capitalize caption"
          style="max-width: 150px"
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
                style="max-width: 200px"
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
          style="max-width: 150px"
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
          dark
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
            $appFormatters.formatTimeDiffNow(item.date_next_tracking, 'days') ||
              '0'
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
        {{ $appFormatters.formatDate(item.updated_at, 'L') }}
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
  </div>
</template>

<script>
import Categories from '@admin/tracking/tracking/resources/categories.json';
import Assertiveness from '@admin/tracking/tracking/resources/assertiveness.json';
import { mapState } from 'vuex';
import TrackingProspect from './TrackingProspect.vue';
export default {
  components: { TrackingProspect },
  data() {
    return {
      dialogs: {
        show: false,
        id: null,
      },
      date: [],
      modal: false,
      headers: [
        {
          text: '',
          value: 'action',
          align: 'center',
          width: 25,
          sortable: false,
          class: 'blue-grey lighten-5',
        },
        {
          text: 'Folio',
          value: 'id',
          align: 'left',
          sortable: false,
          class: 'blue-grey lighten-5',
        },
        {
          text: 'Titulo / Referencia',
          value: 'title',
          align: 'left',
          width: '150',
          sortable: false,
          class: 'blue-grey lighten-5',
        },
        {
          text: 'Precio',
          value: 'price',
          align: 'right',
          sortable: true,
          class: 'blue-grey lighten-5',
        },
        {
          text: 'Moneda',
          value: 'currency',
          align: 'center',
          width: '100',
          sortable: true,
          class: 'blue-grey lighten-5',
        },
        {
          text: 'Prospecto:',
          value: 'prospect.full_name',
          align: 'left',
          width: '150',
          sortable: false,
          class: 'blue-grey lighten-5',
        },
        {
          text: 'Atendido por:',
          value: 'attended.name',
          align: 'left',
          sortable: false,
          class: 'blue-grey lighten-5',
        },
        {
          text: 'Agencia / Departamento',
          value: 'agency-depto',
          width: 150,
          sortable: false,
          class: 'blue-grey lighten-5',
        },
        {
          text: 'Estatus',
          value: 'estatus.title',
          align: 'center',
          width: 100,
          sortable: false,
          class: 'blue-grey lighten-5',
        },
        {
          text: 'Sig. Seg.',
          value: 'date_next_tracking',
          align: 'center',
          width: 150,
          sortable: true,
          class: 'blue-grey lighten-5',
        },
        {
          text: 'Certeza',
          value: 'assertiveness',
          align: 'center',
          width: 150,
          sortable: true,
          class: 'blue-grey lighten-5',
        },
        {
          text: 'Ultimo Cambio',
          value: 'updated_at',
          align: 'center',
          width: 100,
          sortable: true,
          class: 'blue-grey lighten-5',
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
        estatus: 'activo',
        agencies: [],
        departments: [],
        sellers: [],
        dates: [],
      },

      options: {
        prospects: [],
        agencies: [],
        departments: [],
        sellers: [],
        categories: Categories,
        assertiveness: Assertiveness,
      },
    };
  },
  mounted() {
    const self = this;

    self.$store.commit('setBreadcrumbs', [{ label: 'Seguimientos', name: '' }]);
    self.loadResources(() => {});
  },
  computed: {
    ...mapState(['Assertiveness']),
    dateRangeText: {
      get: function() {
        return this.filters.dates.join(',');
      },
      set: function(newValue) {
        newValue ? (this.filters.dates = newValue.split(' ')) : [];
      },
    },
  },
  watch: {
    pagination: {
      handler: _.debounce(function(v) {
        this.loadTrackings(() => {});
      }, 700),
      deep: true,
    },
    filters: {
      handler: _.debounce(function(v) {
        this.loadTrackings(() => {});
      }, 700),
      deep: true,
    },
  },
  methods: {
    trash(seller) {
      const self = this;

      self.$store.commit('showDialog', {
        type: 'confirm',
        title: 'Confirm Deletion',
        message: 'Are you sure you want to delete this seller?',
        okCb: () => {
          axios
            .delete('/admin/sellers/' + seller.id)
            .then(function(response) {
              self.$store.commit('showSnackbar', {
                message: response.data.message,
                color: 'success',
                duration: 3000,
              });

              self.loadTrackings(() => {});
            })
            .catch(function(error) {
              if (error.response) {
                self.$store.commit('showSnackbar', {
                  message: error.response.data.message,
                  color: 'error',
                  duration: 3000,
                });
              } else if (error.request) {
                console.log(error.request);
              } else {
                console.log('Error', error.message);
              }
            });
        },
        cancelCb: () => {
          console.log('CANCEL');
        },
      });
    },
    loadTrackings(cb) {
      const self = this;
      let params = {
        ...self.filters,
        sellers: self.filters.sellers.join(','),
        prospect: self.filters.prospect.join(','),
        agencies: self.filters.agencies.join(','),
        departments: self.filters.departments.join(','),
        dates: self.dateRangeText,
        order_sort: self.pagination.sortDesc[0] ? 'desc' : 'asc',
        order_by: self.pagination.sortBy[0] || 'id',
        page: self.pagination.page,
        per_page: self.pagination.itemsPerPage,
      };

      axios.get('/admin/tracking', { params: params }).then(function(response) {
        self.items = response.data.data.data;
        self.totalItems = response.data.data.total;
        self.pagination.totalItems = response.data.data.total;
        (cb || Function)();
      });
    },
    loadResources(cb) {
      const self = this;

      let params = {
        paginate: 'no',
      };
      axios
        .get('/admin/tracking/sales_history/resources')
        .then(function(response) {
          let Data = response.data.data;
          self.options.agencies = Data.agencies;
          self.options.departments = Data.departments;
          self.options.prospects = Data.prospects;
        });

      axios.get('/admin/sellers', { params: params }).then(function(response) {
        self.options.sellers = response.data.data;
        (cb || Function)();
      });
    },
    getColor(value) {
      if (value == 'finalizado') return 'red';
      else if (value == 'formalizado') return 'blue';
      else return 'primary';
    },
    getColorDays(value) {
      let days = this.$appFormatters.formatTimeDiffNow(value, 'days');
      if (days < 0) return 'red';
      else return 'primary';
    },
    getAssertiveness(value) {
      return this.Assertiveness.find((item) => {
        return item.value == value;
      });
    },
    exportTracking() {
      const self = this;
      let params = {
        ...self.filters,
        sellers: self.filters.sellers.join(','),
        prospect: self.filters.prospect.join(','),
        agencies: self.filters.agencies.join(','),
        departments: self.filters.departments.join(','),
        dates: self.dateRangeText,
        page: self.pagination.page,
        per_page: self.pagination.itemsPerPage,
        paginate: 'no',
      };
      axios
        .get('/admin/tracking-export', {
          params: params,
          responseType: 'blob',
        })
        .then((res) => {
          const url = window.URL.createObjectURL(new Blob([res.data]));
          const link = document.createElement('a');
          link.href = url;
          link.setAttribute('download', 'tracking.xlsx'); //or any other extension
          document.body.appendChild(link);
          link.click();
        })
        .catch(function(error) {
          if (error.response) {
            self.$store.commit('showSnackbar', {
              message: error.response.data.message,
              color: 'error',
              duration: 3000,
            });
          } else if (error.request) {
            console.log(error.request);
          } else {
            console.log('Error', error.message);
          }
        });
    },
    reset() {
      this.filters.dates = [];
      this.$refs.formSearch.reset();
    },
    resetToActive(id) {
      const self = this;
      axios.put('/admin/tracking/resetToActive/' + id).then(function(response) {
        self.$store.commit('showSnackbar', {
          message: response.data.message,
          color: 'success',
          duration: 3000,
        });
        self.loadTrackings(() => {});
        cb();
      });
    },
  },
};
</script>
