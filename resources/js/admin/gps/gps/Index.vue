<template>
  <v-container fluid class="component-wrap">
    <gps-widget-stats></gps-widget-stats>
    <v-data-table
      v-bind:headers="headers"
      :options.sync="pagination"
      :items="items"
      :server-items-length="totalItems"
      dense
      fixed-header
      height="424"
      class="caption elevation-4"
    >
      <template v-slot:top>
        <v-toolbar flat>
          <v-row align="center" class="pa-2">
            <v-btn
              class="primary lighten-1 ml-2"
              small
              dark
              @click="$router.push({ name: 'gps.create' })"
            >
              Registrar Nuevo GPS
              <v-icon small right>mdi-plus</v-icon>
            </v-btn>
            <v-spacer></v-spacer>
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
            <base-tooltip :text="'Buscar'">
              <template #activator="{ on }">
                <v-btn
                  icon
                  color="pink"
                  v-on="on"
                  @click.stop="drawer = !drawer"
                >
                  <v-icon>mdi-magnify</v-icon>
                </v-btn>
              </template>
            </base-tooltip>
            <base-tooltip :text="'Exportar'">
              <template #activator="{ on  }">
                <v-btn icon color="green" v-on="on">
                  <v-icon>mdi-file-excel</v-icon>
                </v-btn>
              </template>
            </base-tooltip>
            <base-tooltip :text="'Actualizar'">
              <template #activator="{ on }">
                <v-btn icon color="grey" v-on="on" @click="loadGps(() => {})">
                  <v-icon>mdi-refresh</v-icon>
                </v-btn>
              </template>
            </base-tooltip>
          </v-row>
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
      <template #[`item.name`]="{ value }">
        <span
          class="caption font-weight-bold text-no-wrap text-uppercase"
          v-text="value"
        />
      </template>
      <template #[`item.gps_group.name`]="{ value }">
        <span class="caption text-no-wrap" v-text="value" />
      </template>
      <template #[`item.chio.sim`]="{ value }">
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
      <template #[`item.amount`]="{ value }">
        <span class="caption font-weight-black text-no-wrap">
          {{ value | currency }}
        </span>
      </template>
      <template #[`item.installation_date`]="{ value }">
        <span class="caption text-uppercase text-no-wrap">
          {{ $appFormatters.formatDate(value, 'MMM YYYY') }}
        </span>
      </template>
      <template #[`item.renew_date`]="{ value }">
        <span class="caption text-uppercase text-no-wrap">
          {{ $appFormatters.formatDate(value, 'MMM YYYY') }}
        </span>
      </template>
      <template #[`item.renew_date_day`]="{ item }">
        <v-chip
          :color="colorDay(item.renew_date)"
          class="caption text-no-wrap"
          dark
          label
        >
          {{ $appFormatters.formatTimeDiffNow(item.renew_date, 'days') }} Dias
        </v-chip>
      </template>
    </v-data-table>
    <drawer-rigth-filter v-model="drawer" @resetFilter="resetFilter">
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
      />
    </drawer-rigth-filter>

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
import BaseTooltip from '../../components/Base/BaseTooltip.vue';
import DrawerRigthFilter from '../../components/shared/DrawerRigthFilter.vue';
import GpsWidgetStats from '@admin/gps/widgets/GpsStats.vue';
import Agencies from '~/api/agencies.json';
import Departments from '~/api/departments.json';
import EditGps from './Edit';
import InvoiceGps from './Invoice';
import CancelGps from './Cancel';

export default {
  components: {
    BaseTooltip,
    DrawerRigthFilter,
    GpsWidgetStats,
    EditGps,
    InvoiceGps,
    CancelGps,
  },
  data() {
    return {
      drawer: null,
      modal: false,
      date: [],
      items: [],
      headers: [
        {
          value: 'action',
          align: 'left',
          divider: true,
          sortable: false,
        },
        {
          text: 'Nombre GPS',
          value: 'name',
          align: 'left',
          class: 'overline',
          class: 'overline blue lighten-4',
          sortable: false,
        },
        {
          text: 'Cliente:',
          value: 'gps_group.name',
          align: 'left',
          class: 'overline blue lighten-4',
          divider: true,
          sortable: false,
        },
        {
          text: 'SIM / CHIP',
          value: 'chip.sim',
          align: 'right',
          sortable: false,
        },
        {
          text: 'Costo:',
          value: 'chip.costo',
          align: 'left',
          sortable: false,
        },
        {
          text: 'Factura:',
          value: 'invoice',
          align: 'center',
          sortable: true,
        },
        {
          text: 'Monto:',
          value: 'amount',
          align: 'right',
          sortable: true,
        },
        {
          text: 'Moneda:',
          value: 'currency',
          align: 'left',
          sortable: false,
        },
        {
          text: 'Instalado en:',
          value: 'installation_date',
          align: 'center',
          sortable: true,
        },
        {
          text: 'Renueva en:',
          value: 'renew_date',
          align: 'center',
          sortable: true,
        },
        {
          text: 'Vence en:',
          value: 'renew_date_day',
          align: 'center',
          sortable: false,
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
        dates: [],
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
        title: '',
        component: '',
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
    _this.$store.commit('setBreadcrumbs', [{ label: 'GPS', name: '' }]);
    _this.$eventBus.$on(['GPS_REFRESH'], () => {
      _this.loadGps(() => {
        _this.dialog.show = false;
      });
    });
  },
  watch: {
    pagination: {
      handler: _.debounce(function() {
        this.loadGps(() => {});
      }, 999),
      deep: true,
    },
    filters: {
      handler: _.debounce(function(v) {
        this.loadGps(() => {});
      }, 999),
      deep: true,
    },
  },
  computed: {
    switches() {
      const self = this;
      return [
        {
          value: self.filters.defeated,
          label: `Por Renovar: ${self.filters.defeated ? 'on' : 'off'}`,
          change: () => {
            self.filters.defeated = !self.filters.defeated;
          },
        },
        {
          value: self.filters.canceled,
          label: `Cancelados: ${self.filters.canceled ? 'on' : 'off'}`,
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
    dateRangeText: {
      get: function() {
        return this.filters.dates.join(',');
      },
      set: function(newValue) {
        newValue ? (this.filters.dates = newValue.split(' ')) : [];
      },
    },
  },
  methods: {
    loadGps(cb) {
      const self = this;
      let params = {
        ...self.filters,
        canceled: self.filters.canceled ? 'on' : null,
        defeated: self.filters.defeated ? 'on' : null,
        order_sort: self.pagination.sortDesc[0] ? 'desc' : 'asc',
        order_by: self.pagination.sortBy[0] || 'name',
        page: self.pagination.page,
        per_page: self.pagination.itemsPerPage,
      };
      axios.get('/admin/gps', { params: params }).then(function(response) {
        let Response = response.data.data;
        self.items = Response.gps.data;
        self.totalItems = Response.gps.total;
        self.pagination.totalItems = Response.gps.total;
        (cb || Function)();
      });
    },
    loadSearchOptions(cb) {
      const self = this;
      axios.get('/admin/gps/search/resources').then(function(response) {
        self.options.chips = response.data.data.chips;
        self.options.groups = response.data.data.groups;
        (cb || Function)();
      });
    },
    colorDay(date) {
      let dateInDays = this.$appFormatters.formatTimeDiffNow(date);
      if (dateInDays < 31) return 'red darken-3';
      else if (dateInDays < 62) return 'orange darken-3';
      else if (dateInDays) return 'green darken-3';
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
      return (
        moment(renew_date).year() <
        moment()
          .add(1, 'y')
          .year()
      );
    },
    dialogEdit(id) {
      this.dialog.title = 'Editar GPS';
      this.dialog.component = 'EditGps';
      this.dialog.props = { propGpsId: id };
      this.dialog.fullscreen = true;
      this.dialog.show = true;
    },
    dialogShow(id) {
      this.dialog.title = 'Informacion GPS';
      this.dialog.component = 'EditGps';
      this.dialog.props = { propGpsId: id };
      this.dialog.fullscreen = true;
      this.dialog.show = true;
    },
    dialogInvoice(id) {
      this.dialog.title = 'Facturar GPS';
      this.dialog.component = 'InvoiceGps';
      this.dialog.props = { propGpsId: id };
      this.dialog.fullscreen = false;
      this.dialog.show = true;
    },
    dialogCancel(id) {
      const _this = this;
      _this.dialog.title = 'Cancelar GPS';
      _this.dialog.component = 'CancelGps';
      _this.dialog.props = { propGpsId: id };
      _this.dialog.fullscreen = false;
      _this.dialog.show = true;
    },
  },
};
</script>

<style></style>
