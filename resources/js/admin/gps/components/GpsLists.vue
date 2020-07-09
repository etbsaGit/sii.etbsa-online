<template>
  <div class="component-wrap">
    <!-- search -->
    <v-card flat>
      <v-form ref="filterForm">
        <div class="d-flex flex-lg-row flex-sm-column flex-wrap align-center">
          <div class="flex-grow-1 px-2">
            <v-text-field
              prepend-icon="mdi-magnify"
              label="Filtar por Nombre"
              v-model="filters.name"
              clearable
            ></v-text-field>
          </div>
          <div class="flex-grow-1 px-2">
            <v-select
              v-model="filters.month"
              :items="options.months"
              label="Mes Vencimiento"
              :menu-props="{ offsetY: true }"
              item-text="name"
              item-value="value"
              clearable
            ></v-select>
          </div>
          <div class="flex-grow-1 px-2">
            <v-select
              v-model="filters.year"
              :items="options.years"
              item-text="name"
              item-value="name"
              label="Año Vencimiento"
              clearable
            ></v-select>
          </div>
          <div class="flex-grow-1 px-2">
            <v-autocomplete
              v-model="filters.groupId"
              filled
              multiple
              chips
              deletable-chips
              clearable
              prepend-icon="mdi-filter-variant"
              label="Filtrar por Grupos"
              :items="filters.groupOptions"
              item-text="name"
              item-value="id"
            ></v-autocomplete>
          </div>
        </div>
      </v-form>

      <!-- Buttons -->
      <div class="d-flex flex-md-row flex-sm-column flex-wrap align-center">
        <!-- <div class="flex-grow-1 px-2 text-right"> -->
        <div class="d-flex flex-grow-1 overline text-uppercase">
          Ultima Actualizacion de Datos: YYYY/MM/DD
        </div>
        <v-spacer></v-spacer>
        <v-btn
          small
          color="secondary"
          @click="$refs.filterForm.reset()"
          class="ma-1"
        >
          Limpiar Filtro
          <v-icon right>mdi-filter-remove</v-icon>
        </v-btn>
        <v-btn small color="accent" class="ma-1">
          EXCEL
          <v-icon right>mdi-file-excel-outline</v-icon>
        </v-btn>
        <v-dialog
          v-model="dialogs.show"
          fullscreen
          transition="dialog-bottom-transition"
          :overlay="false"
        >
          <template v-slot:activator="{ on, attrs }">
            <v-btn small color="primary" v-bind="attrs" v-on="on" class="ma-1">
              Nuevo GPS
              <v-icon right>mdi-plus</v-icon>
            </v-btn>
          </template>
          <v-card>
            <v-toolbar class="primary">
              <v-btn
                icon
                @click.native="(dialogs.show = false), (editedIndex = -1)"
              >
                <v-icon>mdi-close</v-icon>
              </v-btn>
              <v-toolbar-title class="white--text">{{
                formTitle
              }}</v-toolbar-title>
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
              <gps-edit v-if="formEdit" :propGpsId="dialogs.gps.id"></gps-edit>
              <gps-add v-else-if="dialogs.show"></gps-add>
            </v-card-text>
          </v-card>
        </v-dialog>
        <!-- </div> -->
      </div>
    </v-card>

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
      <!-- Body  -->
      <template v-slot:item.gps_group="{ item }">
        {{ item.gps_group.name }}
      </template>
      <template v-slot:item.action="{ item }">
        <v-menu offset-x>
          <template v-slot:activator="{ on, attrs }">
            <v-btn icon v-bind="attrs" v-on="on">
              <v-icon>mdi-dots-vertical</v-icon>
            </v-btn>
          </template>
          <v-list shaped>
            <v-list-item-group>
              <v-list-item @click="editItem(item)">
                <v-list-item-icon>
                  <v-icon class="blue--text">mdi-information-outline</v-icon>
                </v-list-item-icon>
                <v-list-item-content>
                  <v-list-tile-title>Detalle</v-list-tile-title>
                </v-list-item-content>
              </v-list-item>
              <v-list-item>
                <v-list-item-icon>
                  <v-icon class="grey--text">mdi-history</v-icon>
                </v-list-item-icon>
                <v-list-item-content>
                  <v-list-tile-title>Historico</v-list-tile-title>
                </v-list-item-content>
              </v-list-item>
              <v-list-item @click="trash(item)">
                <v-list-item-icon>
                  <v-icon class="red--text">mdi-trash-can</v-icon>
                </v-list-item-icon>
                <v-list-item-content>
                  <v-list-tile-title>Eliminar</v-list-tile-title>
                </v-list-item-content>
              </v-list-item>
            </v-list-item-group>
          </v-list>
        </v-menu>
      </template>
      <template v-slot:item.gps_group="{ item }">
        <template v-if="item.gps_group">
          {{ item.gps_group.name }}
        </template>
        <template v-else>
          <v-edit-dialog
            :return-value.sync="item.gps_group_id"
            large
            persistent
            @save="saveInLine(item)"
            @cancel="cancel"
          >
            <v-btn outlined small color="primary" pa-0>Asignar a Grupo</v-btn>
            <template v-slot:input>
              <div class="mt-4 title">Asignar a Grupo</div>
              <v-autocomplete
                v-model="item.gps_group_id"
                filled
                clearable
                prepend-icon="mdi-filter-variant"
                label="Buscar Grupo"
                :items="filters.groupOptions"
                item-text="name"
                item-value="id"
              ></v-autocomplete>
            </template>
          </v-edit-dialog>
        </template>
      </template>

      <template v-slot:item.cost="{ item }">
        <v-edit-dialog
          :return-value.sync="item.cost"
          large
          persistent
          @save="saveInLine(item)"
          @cancel="cancel"
        >
          <v-btn outlined small pa-0>{{ item.cost | money() }}</v-btn>
          <template v-slot:input>
            <div class="mt-4 title">Modificar Costo:</div>
            <v-text-field
              v-model.lazy="item.cost"
              label="Valor"
              type="tel"
              prefix="$"
              single-line
              counter
              autofocus
            ></v-text-field>
          </template>
        </v-edit-dialog>
      </template>
      <template v-slot:item.amount="{ item }">
        <v-edit-dialog
          :return-value.sync="item.amount"
          large
          persistent
          @save="saveInLine(item)"
          @cancel="cancel"
        >
          <v-btn outlined small pa-0>{{ item.amount | money() }}</v-btn>
          <template v-slot:input>
            <div class="mt-4 title">Modificar Costo:</div>
            <v-text-field
              v-model.lazy="item.amount"
              label="Valor"
              type="tel"
              prefix="$"
              single-line
              counter
              autofocus
            ></v-text-field>
          </template>
        </v-edit-dialog>
      </template>

      <template v-slot:item.activation_date="{ item }">
        <span class="overline text-capitalize">
          {{ $appFormatters.formatDate(item.activation_date) }}
        </span>
      </template>
      <template v-slot:item.due_date="{ item }">
        <span class="overline text-capitalize">
          <v-chip
            :color="getColor($appFormatters.formatTimeDiffNow(item.due_date))"
            dark
            small
          >
            {{ $appFormatters.formatTimeDiffNow(item.due_date, "days") }} Dias
          </v-chip>
          <!-- |{{ $appFormatters.formatDate(item.due_date) }} -->
        </span>
      </template>
    </v-data-table>
  </div>
</template>

<script>
import GpsAdd from "@admin/gps/components/GpsAdd.vue";
import GpsEdit from "@admin/gps/components/GpsEdit.vue";

import optionMoths from "~/api/moths.json";
import optionYears from "~/api/years.json";
export default {
  components: {
    GpsAdd,
    GpsEdit
  },
  data() {
    return {
      headers: [
        {
          text: "Accion",
          value: "action",
          align: "center",
          divider: true,
          width: 10,
          class: "pa-0",
          sortable: false
        },
        {
          text: "Nombre GPS",
          value: "name",
          align: "left",
          sortable: true
        },
        {
          text: "SIM",
          value: "sim",
          align: "right",
          sortable: true
        },
        {
          text: "Grupo",
          value: "gps_group",
          align: "left",
          sortable: false
        },
        {
          text: "Costo",
          value: "cost",
          align: "right",
          sortable: false
        },
        {
          text: "Facturado",
          value: "amount",
          align: "right",
          sortable: false
        },
        {
          text: "Fecha de Activacion",
          value: "activation_date",
          align: "center",
          width: 135,
          sortable: false
        },
        {
          text: "Vence en (dias)",
          value: "due_date",
          align: "center",
          width: 125,
          class: "pa-0",
          sortable: true
        }
      ],
      items: [],
      totalItems: 0,
      pagination: {
        itemsPerPage: 10
      },
      editedIndex: -1,
      dialogs: {
        show: false,
        gps: null
      },
      options: {
        months: optionMoths,
        years: optionYears,
        gpsGroup: []
      },
      filters: {
        name: null,
        month: null,
        year: null,
        groupId: [],
        groupOptions: []
      }
    };
  },
  mounted() {
    const self = this;

    self.$eventBus.$on(["GPS_ADDED", "GPS_UPDATED", "GPS_DELETED"], () => {
      self.loadGps(() => {});
    });
    self.loadGpsGroup(() => {});
  },
  watch: {
    pagination: {
      handler: _.debounce(function() {
        this.loadGps(() => {});
      }, 700),
      deep: true
    },
    filters: {
      handler: _.debounce(function(v) {
        this.loadGps(() => {});
      }, 700),
      deep: true
    }
  },
  computed: {
    formTitle() {
      return this.editedIndex === -1 ? "Registrar Nuevo GPS" : "Editar GPS";
    },
    formEdit() {
      return this.editedIndex !== -1;
    }
    // filterMonth(){
    //   return this.options.months.findIndex(v => v == this.filters.month) + 1
    // }
  },
  methods: {
    editItem(item) {
      const self = this;
      self.editedIndex = self.items.indexOf(item);
      self.dialogs.gps = item;
      self.dialogs.show = true;
    },
    loadGps(cb) {
      const self = this;

      let params = {
        name: self.filters.name,
        month: self.filters.month,
        year: self.filters.year,
        group_id: self.filters.groupId.join(","),
        order_sort: self.pagination.sortDesc[0] ? "desc" : "asc",
        order_by: self.pagination.sortBy[0] || "name",
        page: self.pagination.page,
        per_page: self.pagination.itemsPerPage
      };

      axios.get("/admin/gps", { params: params }).then(function(response) {
        self.items = response.data.data.data;
        self.totalItems = response.data.data.total;
        self.pagination.totalItems = response.data.data.total;
        (cb || Function)();
      });
    },
    loadGpsGroup(cb) {
      const self = this;
      let params = {
        per_page: -1
      };

      axios
        .get("/admin/gps-groups", { params: params })
        .then(function(response) {
          self.filters.groupOptions = response.data.data.data;
          cb();
        });
    },
    customFilter(item, queryText, itemText) {
      const textOne = item.name.toLowerCase();
      const searchText = queryText.toLowerCase();
      return textOne.indexOf(searchText) > -1;
    },
    trash(gps) {
      const self = this;

      self.$store.commit("showDialog", {
        type: "confirm",
        title: "Confirm Deletion",
        message: "Are you sure you want to delete this GPS?",
        okCb: () => {
          axios
            .delete("/admin/gps/" + gps.id)
            .then(function(response) {
              self.$store.commit("showSnackbar", {
                message: response.data.message,
                color: "success",
                duration: 3000
              });

              self.$eventBus.$emit("GPS_DELETED");
              self.$eventBus.$emit("GPS_GROUP_DELETED");
            })
            .catch(function(error) {
              if (error.response) {
                self.$store.commit("showSnackbar", {
                  message: error.response.data.message,
                  color: "error",
                  duration: 3000
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
        }
      });
    },
    saveInLine(item) {
      const self = this;
      axios
        .put("/admin/gps/" + item.id, item)
        .then(function(response) {
          self.$store.commit("showSnackbar", {
            message: response.data.message,
            color: "success",
            duration: 3000
          });

          self.$eventBus.$emit("GPS_UPDATED");
          self.$eventBus.$emit("GPS_GROUP_UPDATED");
        })
        .catch(function(error) {
          if (error.response) {
            self.$store.commit("showSnackbar", {
              message: error.response.data.message,
              color: "error",
              duration: 3000
            });
          } else if (error.request) {
            console.log(error.request);
          } else {
            console.log("Error", error.message);
          }
        });
    },
    cancel() {
      const self = this;
      self.$store.commit("showSnackbar", {
        message: "Cancel",
        color: "error lighten-1",
        duration: 3000
      });
    },
    getColor(date) {
      if (date < 31) return "red";
      else if (date < 62) return "orange";
      else return "green";
    }
  }
};
</script>