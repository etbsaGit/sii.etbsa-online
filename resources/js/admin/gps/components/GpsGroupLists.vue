<template>
  <div class="component-wrap">
    <!-- search -->
    <v-card flat>
      <v-form ref="filterForm">
        <div class="d-flex flex-row align-center px-4">
          <div class="flex-grow-1">
            <v-text-field
              prepend-icon="mdi-magnify"
              label="Filtar por Nombre"
              v-model="filters.name"
              clearable
            ></v-text-field>
          </div>
          <div class="flex-grow-1 text-right"></div>
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
          <div class="flex-grow-1 overline text-uppercase">
            Ultima Actualizacion de Datos: YYYY/MM/DD
          </div>
          <v-spacer></v-spacer>
          <v-btn icon color="secondary" @click="$refs.filterForm.reset()">
            <v-icon>mdi-filter-remove-outline</v-icon>
          </v-btn>
          <v-btn icon color="green">
            <v-icon>mdi-file-excel</v-icon>
          </v-btn>
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
          <v-btn icon>
            <v-icon>mdi-refresh</v-icon>
          </v-btn>
        </v-toolbar>
      </template>
      <!-- Body  -->
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
                  <v-icon class="grey--text">mdi-crosshairs-question</v-icon>
                </v-list-item-icon>
                <v-list-item-content>
                  <v-list-tile-title>GPS</v-list-tile-title>
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
    </v-data-table>
    <!-- dialogs -->
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
          <gps-group-add v-if="formAdd"></gps-group-add>
          <gps-group-edit
            v-else
            :propGpsGroupId="dialogs.gpsGroup.id"
          ></gps-group-edit>
        </v-card-text>
      </v-card>
    </v-dialog>
  </div>
</template>

<script>
import GpsGroupAdd from "@admin/gps/components/GpsGroupAdd.vue";
import GpsGroupEdit from "@admin/gps/components/GpsGroupEdit.vue";
export default {
  components: {
    GpsGroupAdd,
    GpsGroupEdit
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
          sortable: false
        },
        {
          text: "Nombre Grupo",
          value: "name",
          align: "left",
          sortable: true
        },
        {
          text: "Sucursal",
          value: "agency",
          align: "left",
          sortable: true
        },
        {
          text: "Cargo",
          value: "department",
          align: "left",
          sortable: true
        },
        {
          text: "Total GPS",
          value: "gps_count",
          align: "center",
          sortable: false
        }
      ],
      items: [],
      totalItems: 0,
      pagination: {
        itemsPerPage: 10
      },
      editedIndex: -1,
      dialogs: {
        title: "",
        show: false,
        gpsGroup: null
      },
      filters: {
        name: ""
      }
    };
  },
  mounted() {
    const self = this;

    self.$eventBus.$on(
      ["GPS_GROUP_ADDED", "GPS_GROUP_UPDATED", "GPS_GROUP_DELETED"],
      () => {
        self.loadGpsGroup(() => {});
      }
    );
  },
  watch: {
    pagination: {
      handler: _.debounce(function() {
        this.loadGpsGroup(() => {});
      }, 700),
      deep: true
    },
    "filters.name": _.debounce(function(v) {
      this.loadGpsGroup(() => {});
    }, 700)
  },
  computed: {
    formTitle() {
      return this.editedIndex === -1
        ? "Registrar Grupo GPS"
        : "Editar Grupo GPS";
    },
    formAdd() {
      return this.editedIndex === -1;
    }
  },
  methods: {
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
        order_sort: self.pagination.sortDesc[0] ? "desc" : "asc",
        order_by: self.pagination.sortBy[0] || "name",
        page: self.pagination.page,
        per_page: self.pagination.itemsPerPage
      };

      axios
        .get("/admin/gps-groups", { params: params })
        .then(function(response) {
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
            .delete("/admin/gps-groups/" + group.id)
            .then(function(response) {
              self.$store.commit("showSnackbar", {
                message: response.data.message,
                color: "success",
                duration: 3000
              });

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
    }
  }
};
</script>