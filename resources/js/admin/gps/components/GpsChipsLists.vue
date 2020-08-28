<template>
  <div class="component-wrap">
    <!-- search -->
    <v-card flat>
      <v-form ref="filterForm">
        <div class="d-flex flex-lg-row flex-xs-column flex-wrap align-center">
          <div class="flex-grow-1 px-2">
            <v-text-field
              prepend-icon="mdi-magnify"
              label="Buscar por SIM"
              v-model="filters.sim"
              clearable
            ></v-text-field>
          </div>
          <div class="flex-grow-1 px-2">
            <v-text-field
              prepend-icon="mdi-magnify"
              label="Buscar por IMEI"
              v-model="filters.imei"
              clearable
            ></v-text-field>
          </div>

          <div class="flex-grow-1 px-2">
            <v-select
              prepend-icon="mdi-calendar-month-outline"
              v-model="filters.month"
              :items="options.months"
              label="Mes Renovacion"
              :menu-props="{ offsetY: true }"
              item-text="name"
              item-value="value"
              clearable
            ></v-select>
          </div>
          <div class="flex-grow-1 px-2">
            <v-select
              prepend-icon="mdi-calendar-month-outline"
              v-model="filters.year"
              :items="options.years"
              item-text="name"
              item-value="name"
              label="Año Renovacion"
              clearable
            ></v-select>
          </div>

          <v-row class="flex-grow-1 px-2" justify="space-around">
            <v-checkbox
              v-model="filters.assigned"
              class="mx-1"
              label="Asignados"
            ></v-checkbox>
            <v-checkbox
              v-model="filters.deallocated"
              class="mx-1"
              label="SIN Asignar"
            ></v-checkbox>
            <v-checkbox
              v-model="filters.expired"
              class="mx-1"
              label="Vencidos"
            ></v-checkbox>
          </v-row>
        </div>
      </v-form>
    </v-card>
    <!-- /search -->

    <!-- data table -->
    <v-data-table
      v-bind:headers="headers"
      :options.sync="pagination"
      :items="items"
      :server-items-length="totalItems"
      dense
      fixed-header
      class="elevation-1 text-uppercase"
    >
      <!-- Top -->
      <template v-slot:top>
        <v-toolbar dense elevation="0">
          <div class="flex-grow-1 overline text-uppercase"></div>
          <v-spacer></v-spacer>
          <v-btn
            icon
            color="secondary"
            @click="$refs.filterForm.reset(), (pagination.itemsPerPage = 10)"
          >
            <v-icon>mdi-filter-remove-outline</v-icon>
          </v-btn>
          <!-- <v-btn icon color="green">
            <v-icon>mdi-file-excel</v-icon>
          </v-btn> -->
          <v-tooltip top>
            <template v-slot:activator="{ on, attrs }">
              <v-btn
                icon
                color="blue"
                @click="dialogs.show = true"
                v-bind="attrs"
                v-on="on"
              >
                <v-icon>mdi-plus-thick</v-icon>
              </v-btn>
            </template>
            <span>Agregar Nuevo</span>
          </v-tooltip>
          <v-btn icon @click="refresh()">
            <v-icon>mdi-refresh</v-icon>
          </v-btn>
        </v-toolbar>
      </template>
      <!-- Body  -->
      <template v-slot:[`item.action`]="{ item }">
        <v-menu offset-x>
          <template v-slot:activator="{ on, attrs }">
            <v-btn icon v-bind="attrs" v-on="on">
              <v-icon>mdi-dots-vertical</v-icon>
            </v-btn>
          </template>
          <v-list shaped>
            <v-list-item-chip>
              <v-list-item @click="editItem(item)">
                <v-list-item-icon>
                  <v-icon class="blue--text">mdi-information-outline</v-icon>
                </v-list-item-icon>
                <v-list-item-content>
                  <v-list-tile-title>Detalle</v-list-tile-title>
                </v-list-item-content>
              </v-list-item>
              <!-- <v-list-item>
                <v-list-item-icon>
                  <v-icon class="grey--text">mdi-crosshairs-question</v-icon>
                </v-list-item-icon>
                <v-list-item-content>
                  <v-list-tile-title>GPS</v-list-tile-title>
                </v-list-item-content>
              </v-list-item> -->
              <v-list-item @click="trash(item)">
                <v-list-item-icon>
                  <v-icon class="red--text">mdi-trash-can</v-icon>
                </v-list-item-icon>
                <v-list-item-content>
                  <v-list-tile-title>Eliminar</v-list-tile-title>
                </v-list-item-content>
              </v-list-item>
            </v-list-item-chip>
          </v-list>
        </v-menu>
      </template>
      <template v-slot:[`item.costo`]="{ item }">
        {{ item.costo | money() }}
      </template>

      <template v-slot:[`item.fecha_activacion`]="{ item }">
        <span class="overline text-capitalize">
          {{ $appFormatters.formatDate(item.fecha_activacion) }}
        </span>
      </template>
      <!-- <template v-slot:[`item.fecha_renovacion`]="{ item }">
        <span class="overline text-capitalize">
          <v-chip
            :color="
              getColor($appFormatters.formatTimeDiffNow(item.fecha_renovacion))
            "
            dark
            small
          >
            {{
              $appFormatters.formatTimeDiffNow(item.fecha_renovacion, "days")
            }}
            Dias
          </v-chip>
        </span>
      </template> -->

      <template v-slot:[`item.gps_id`]="{ item }">
        <v-avatar outlined>
          <v-tooltip top v-if="item.gps != null">
            <template v-slot:activator="{ on, attrs }">
              <v-icon class="green--text" v-bind="attrs" v-on="on"
                >mdi-check-circle-outline</v-icon
              >
            </template>
            <span>{{ item.gps.name }}</span>
          </v-tooltip>
          <v-icon class="grey--text" v-else>mdi-alert-rhombus-outline</v-icon>
        </v-avatar>
      </template>
    </v-data-table>
    <!-- dialog -->
    <v-dialog
      v-model="dialogs.show"
      fullscreen
      transition="dialog-bottom-transition"
      :overlay="false"
    >
      <v-card>
        <v-toolbar class="primary">
          <v-btn
            icon
            @click.native="(dialogs.show = false), (editedIndex = -1)"
          >
            <v-icon>mdi-close</v-icon>
          </v-btn>
          <v-toolbar-title>{{ formTitle }}</v-toolbar-title>
          <v-spacer></v-spacer>
          <v-toolbar-items>
            <v-btn
              text
              @click.native="(dialogs.show = false), (editedIndex = -1)"
              >Done</v-btn
            >
          </v-toolbar-items>
        </v-toolbar>
        <v-card-text>
          <gps-chip-add v-if="formAdd"></gps-chip-add>
          <gps-chip-edit
            v-else
            :propGpsChipId="dialogs.gpsChip.sim"
          ></gps-chip-edit>
        </v-card-text>
      </v-card>
    </v-dialog>
  </div>
</template>

<script>
import GpsChipAdd from "@admin/gps/components/GpsChipAdd.vue";
import GpsChipEdit from "@admin/gps/components/GpsChipEdit.vue";

import optionMonths from "~/api/months.json";
import optionYears from "~/api/years.json";
export default {
  components: {
    GpsChipAdd,
    GpsChipEdit,
  },
  data() {
    return {
      headers: [
        {
          text: "",
          value: "action",
          align: "center",
          divider: true,
          width: 10,
          class: "pa-auto",
          sortable: false,
        },
        {
          text: "SIM",
          value: "sim",
          align: "left",
          sortable: true,
        },
        {
          text: "IMEI",
          value: "imei",
          align: "left",
          sortable: true,
        },
        {
          text: "Costo",
          value: "costo",
          align: "left",
          sortable: false,
        },
        {
          text: "Fecha Activacion",
          value: "fecha_activacion",
          align: "center",
          sortable: true,
        },
        // {
        //   text: "Fecha Renovacion",
        //   value: "fecha_renovacion",
        //   align: "center",
        //   sortable: true,
        // },
        {
          text: "Asignado",
          value: "gps_id",
          align: "center",
          sortable: true,
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
        years: optionYears,
      },
      filters: {
        sim: "",
        imei: "",
        month: null,
        year: null,
        assigned: null,
        deallocated: null,
        expired: null,
      },
    };
  },
  mounted() {
    const self = this;

    self.$eventBus.$on(
      ["GPS_CHIP_ADDED", "GPS_CHIP_UPDATED", "GPS_CHIP_DELETED"],
      () => {
        self.loadGpsChips(() => {});
      }
    );
  },
  watch: {
    pagination: {
      handler: _.debounce(function() {
        this.loadGpsChips(() => {});
      }, 700),
      deep: true,
    },
    filters: {
      handler: _.debounce(function(v) {
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
  },
  methods: {
    refresh() {
      const self = this;
      self.loadGpsChips(() => {});
    },
    editItem(item) {
      const self = this;
      self.editedIndex = self.items.indexOf(item);
      self.dialogs.gpsChip = item;
      self.dialogs.show = true;
    },
    loadGpsChips(cb) {
      const self = this;

      let params = {
        sim: self.filters.sim,
        month: self.filters.month,
        year: self.filters.year,
        imei: self.filters.imei,
        assigned: self.filters.assigned ? self.filters.assigned : null,
        deallocated: self.filters.deallocated ? self.filters.deallocated : null,
        expired: self.filters.expired ? self.filters.expired : null,
        order_sort: self.pagination.sortDesc[0] ? "desc" : "asc",
        order_by: self.pagination.sortBy[0] || "sim",
        page: self.pagination.page,
        per_page: self.pagination.itemsPerPage,
      };

      axios
        .get("/admin/chips", { params: params })
        .then(function(response) {
          self.items = response.data.data.data;
          self.totalItems = response.data.data.total;
          self.pagination.totalItems = response.data.data.total;
          (cb || Function)();
        });
    },
    trash(chip) {
      const self = this;

      self.$store.commit("showDialog", {
        type: "confirm",
        title: "Confirm Deletion",
        message: "Are you sure you want to delete this gps chip?",
        okCb: () => {
          axios
            .delete("/admin/chips/" + chip.id)
            .then(function(response) {
              self.$store.commit("showSnackbar", {
                message: response.data.message,
                color: "success",
                duration: 3000,
              });

              self.$eventBus.$emit("GPS_CHIP_DELETED");
            })
            .catch(function(error) {
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
        cancelCb: () => {
          console.log("CANCEL");
        },
      });
    },
    getColor(date) {
      if (date < 31) return "red";
      else if (date < 62) return "orange";
      else return "green";
    },
  },
};
</script>
