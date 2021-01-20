<template>
  <div class="component-wrap">
    <!-- search -->
    <v-card flat>
      <div class="d-flex flex-row align-center">
        <div class="flex-grow-1 pa-2">
          <v-text-field
            v-model="filters.matricula"
            prepend-icon="mdi-magnify"
            label="Fitrar por Matricula"
            clearable
          ></v-text-field>
        </div>
        <div class="flex-grow-1 pa-2">
          <v-btn :to="{ name: 'vehicle.create' }">Registrar Vehiculo</v-btn>
        </div>
      </div>
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
                @click="
                  $router.push({
                    name: 'vehicle.dispersal.create',
                    params: { propsVehicleId: item.id },
                  })
                "
              >
                <v-list-item-icon>
                  <v-icon class="blue--text">mdi-gas-station-outline</v-icon>
                </v-list-item-icon>
                <v-list-item-content>
                  <v-list-item-title>Solcitar Dispercion</v-list-item-title>
                </v-list-item-content>
              </v-list-item>
              <v-list-item
                @click="
                  $router.push({
                    name: 'vehicle.services.create',
                    params: { propsVehicleServiceId: item.id },
                  })
                "
              >
                <v-list-item-icon>
                  <v-icon class="blue--text">mdi-settings</v-icon>
                </v-list-item-icon>
                <v-list-item-content>
                  <v-list-item-title>Solcitar Servicio</v-list-item-title>
                </v-list-item-content>
              </v-list-item>
              <v-list-item
                @click="
                  $router.push({
                    name: 'vehicle.edit',
                    params: { propVehicleId: item.id },
                  })
                "
              >
                <v-list-item-icon>
                  <v-icon class="blue--text">mdi-information-outline</v-icon>
                </v-list-item-icon>
                <v-list-item-content>
                  <v-list-item-title>Editar Vehiculo</v-list-item-title>
                </v-list-item-content>
              </v-list-item>
              <v-list-item @click="trash(item)">
                <v-list-item-icon>
                  <v-icon class="red--text">mdi-delete</v-icon>
                </v-list-item-icon>
                <v-list-item-content>
                  <v-list-item-title>Eliminar Vehiculo</v-list-item-title>
                </v-list-item-content>
              </v-list-item>
            </v-list-item-group>
          </v-list>
        </v-menu>
      </template>
      <template v-slot:[`item.assigned`]="{ item }">
        <v-list-item dense class="pa-0">
          <v-list-item-content class="pa-0">
            <v-list-item-title>{{ item.user.name }}</v-list-item-title>
            <v-list-item-subtitle>
              {{ item.agency.title }}
            </v-list-item-subtitle>
          </v-list-item-content>
        </v-list-item>
      </template>
      <template v-slot:[`item.ultimo_kilometraje`]="{ item }">
        {{ item.ultimo_kilometraje | kms }}
      </template>
      <template v-slot:[`item.updated_at`]="{ item }">
        {{ $appFormatters.formatDate(item.updated_at, "MMM DD,YYYY") }}
      </template>
    </v-data-table>
  </div>
</template>

<script>
export default {
  data() {
    return {
      headers: [
        { text: "Action", value: "action", align: "left", sortable: false },
        {
          text: "Matricula",
          value: "matricula",
          align: "left",
          sortable: false,
        },
        {
          text: "Responsable y Sucursal",
          value: "assigned",
          align: "left",
          sortable: false,
        },
        {
          text: "Modelo y Marca",
          value: "modelo",
          align: "center",
          sortable: false,
        },
        {
          text: "Ultimo Kilometraje",
          value: "ultimo_kilometraje",
          align: "center",
          sortable: false,
        },
        {
          text: "Ultima Actualizacion",
          value: "updated_at",
          align: "right",
          sortable: false,
        },
      ],
      items: [],
      totalItems: 0,
      pagination: {
        rowsPerPage: 10,
      },

      filters: {
        matricula: "",
      },
    };
  },
  mounted() {
    const self = this;
    self.$store.commit("setBreadcrumbs", [
      { label: "Flotilla de Unidades", name: "" },
    ]);
  },
  watch: {
    "pagination.page": function() {
      this.loadVehicles(() => {});
    },
    "pagination.rowsPerPage": function() {
      this.loadVehicles(() => {});
    },
    filters: {
      handler: _.debounce(function(v) {
        this.loadVehicles(() => {
          this.$store.commit("hideLoader");
        });
      }, 700),
      deep: true,
    },
  },
  methods: {
    trash(vehicle) {
      const self = this;

      self.$store.commit("showDialog", {
        type: "confirm",
        title: "Confirm Deletion",
        message: "Are you sure you want to delete this vehicle?",
        okCb: () => {
          axios
            .delete("/admin/vehicles/" + vehicle.id)
            .then(function(response) {
              self.$store.commit("showSnackbar", {
                message: response.data.message,
                color: "success",
                duration: 3000,
              });

              self.loadVehicles(() => {});
            })
            .catch(function(error) {
              self.$store.commit("hideLoader");

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
    loadVehicles(cb) {
      const self = this;

      let params = {
        matricula: self.filters.matricula,
        page: self.pagination.page,
        per_page: self.pagination.rowsPerPage,
      };

      axios.get("/admin/vehicles", { params: params }).then(function(response) {
        self.items = response.data.data.data;
        self.totalItems = response.data.data.total;
        self.pagination.totalItems = response.data.data.total;
        (cb || Function)();
      });
    },
  },
};
</script>
