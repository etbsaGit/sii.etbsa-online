<template>
  <v-container fluid>
    <v-data-table
      v-bind:headers="headers"
      :options.sync="pagination"
      :items="items"
      :server-items-length="totalItems"
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
              <v-text-field
                prepend-icon="mdi-magnify"
                label="Filtar por Nombre"
                v-model="filters.name"
                hide-details
                clearable
              ></v-text-field>
              <v-select
                v-model="filters.agency"
                :items="options.agencies"
                label="Sucursal"
                :menu-props="{ offsetY: true }"
                item-text="name"
                item-value="name"
                prepend-icon="mdi-filter-variant"
                hide-details
                clearable
              ></v-select>
              <v-select
                v-model="filters.department"
                :items="options.departments"
                label="Departamento"
                :menu-props="{ offsetY: true }"
                item-text="name"
                item-value="name"
                hide-details
                prepend-icon="mdi-filter-variant"
                clearable
              ></v-select>
            </v-row>
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
              v-model="filters.name"
              label="Search"
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
          <v-toolbar-title>Lista de Clientes GPS</v-toolbar-title>
          <v-divider class="mx-4" inset vertical></v-divider>
          <v-spacer></v-spacer>
          <v-btn color="primary" dark class="mb-2" @click="dialogs.show = true">
            Registrar Cliente GPS
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
            <v-list-item-group>
              <v-list-item @click="editItem(item)">
                <v-list-item-icon>
                  <v-icon class="blue--text">mdi-information-outline</v-icon>
                </v-list-item-icon>
                <v-list-item-content>
                  <v-list-item-title>Detalle</v-list-item-title>
                </v-list-item-content>
              </v-list-item>

              <v-list-item @click="trash(item)">
                <v-list-item-icon>
                  <v-icon class="red--text">mdi-trash-can</v-icon>
                </v-list-item-icon>
                <v-list-item-content>
                  <v-list-item-title>Eliminar</v-list-item-title>
                </v-list-item-content>
              </v-list-item>
            </v-list-item-group>
          </v-list>
        </v-menu>
      </template>
      <template v-slot:[`item.agency`]="{ item }">
        <v-list-item dense>
          <v-list-item-content class="py-0">
            <v-list-item-title
              class="font-weight-bold text-no-wrap text-uppercase"
            >
              {{ item.agency }}
            </v-list-item-title>
            <v-list-item-subtitle
              v-if="item.department"
              class="text-no-wrap text-uppercase"
            >
              {{ item.department }}
            </v-list-item-subtitle>
          </v-list-item-content>
        </v-list-item>
      </template>
    </v-data-table>
    <!-- dialogs -->
    <v-dialog
      v-model="dialogs.show"
      fullscreen
      transition="dialog-bottom-transition"
      :overlay="false"
    >
      <v-card>
        <v-toolbar class="accent">
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
          <gps-group-add v-if="formAdd"></gps-group-add>
          <gps-group-edit
            v-else
            :propGpsGroupId="dialogs.gpsGroup.id"
          ></gps-group-edit>
        </v-card-text>
      </v-card>
    </v-dialog>
  </v-container>
</template>

<script>
import GpsGroupAdd from "@admin/gps/groups/Create.vue";
import GpsGroupEdit from "@admin/gps/groups/Edit.vue";
import optionAgencies from "~/api/agencies.json";
import optionDepartments from "~/api/departments.json";
import SearchPanel from "../../components/shared/SearchPanel.vue";
import TableHeaderButtons from "../../components/shared/TableHeaderButtons.vue";

export default {
  components: {
    GpsGroupAdd,
    GpsGroupEdit,
    TableHeaderButtons,
    SearchPanel,
  },
  data() {
    return {
      showSearchPanel: false,
      headers: [
        {
          text: "",
          value: "action",
          align: "center",
          divider: true,
          width: 10,
          class: "pa-auto",
          sortable: false,
          class: "blue-grey darken-5",
        },
        {
          text: "Nombre Cliente",
          value: "name",
          align: "left",
          sortable: true,
          class: "blue-grey darken-1 white--text overline text-truncate",
        },
        {
          text: "Sucursal - Cargo",
          value: "agency",
          align: "left",
          sortable: true,
          class: "blue-grey darken-1 white--text overline text-truncate",
        },
        {
          text: "Telefono",
          value: "phone",
          align: "left",
          sortable: false,
          class: "blue-grey darken-1 white--text overline text-truncate",
        },
        {
          text: "Total GPS",
          value: "gps_count",
          align: "center",
          sortable: false,
          class: "blue-grey darken-1 white--text overline text-truncate",
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
        gpsGroup: null,
      },
      options: {
        agencies: optionAgencies,
        departments: optionDepartments,
      },
      filters: {
        name: "",
        agency: null,
        department: null,
      },
    };
  },
  mounted() {
    const self = this;

    self.$eventBus.$on(
      [
        "GPS_GROUP_ADDED",
        "GPS_GROUP_UPDATED",
        "GPS_GROUP_DELETED",
        "GPS_REFRESH",
      ],
      () => {
        self.loadGpsGroup(() => {});
      }
    );
  },
  watch: {
    pagination: {
      handler: _.debounce(function () {
        this.loadGpsGroup(() => {});
      }, 700),
      deep: true,
    },
    filters: {
      handler: _.debounce(function (v) {
        this.loadGpsGroup(() => {});
      }, 700),
      deep: true,
    },
  },
  computed: {
    formTitle() {
      return this.editedIndex === -1
        ? "Registrar Cliente GPS"
        : "Editar Cliente GPS";
    },
    formAdd() {
      return this.editedIndex === -1;
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
      self.loadGpsGroup(() => {});
    },
    editItem(item) {
      const self = this;
      self.editedIndex = self.items.indexOf(item);
      self.dialogs.gpsGroup = item;
      self.dialogs.show = true;
    },
    loadGpsGroup(cb) {
      const self = this;

      let params = {
        name: self.filters.name,
        agency: self.filters.agency,
        department: self.filters.department,
        order_sort: self.pagination.sortDesc[0] ? "desc" : "asc",
        order_by: self.pagination.sortBy[0] || "name",
        page: self.pagination.page,
        per_page: self.pagination.itemsPerPage,
      };

      axios
        .get("/admin/gpsCustomers", { params: params })
        .then(function (response) {
          self.items = response.data.data.data;
          self.totalItems = response.data.data.total;
          self.pagination.totalItems = response.data.data.total;
          (cb || Function)();
        });
    },
    trash(group) {
      const self = this;

      self.$store.commit("showDialog", {
        type: "confirm",
        title: "Confirm Deletion",
        message: "Are you sure you want to delete this gps group?",
        okCb: () => {
          axios
            .delete("/admin/gpsCustomers/" + group.id)
            .then(function (response) {
              self.$store.commit("showSnackbar", {
                message: response.data.message,
                color: "success",
                duration: 3000,
              });

              self.$eventBus.$emit("GPS_GROUP_DELETED");
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
        cancelCb: () => {
          console.log("CANCEL");
        },
      });
    },
    exportTable() {
      const self = this;

      let params = {
        name: self.filters.name,
        agency: self.filters.agency,
        department: self.filters.department,
        order_sort: self.pagination.sortDesc[0] ? "desc" : "asc",
        order_by: self.pagination.sortBy[0] || "name",
        paginate: "no",
      };

      axios
        .get("/admin/gps-groups-export", {
          params: params,
          responseType: "blob",
        })
        .then((res) => {
          const url = window.URL.createObjectURL(new Blob([res.data]));
          const link = document.createElement("a");
          link.href = url;
          link.setAttribute("download", "clientes-gps.xlsx"); //or any other extension
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
