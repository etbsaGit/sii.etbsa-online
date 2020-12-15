<template>
  <div class="component-wrap">
    <!-- search -->
    <v-card flat>
      <div class="d-flex flex-row align-center">
        <div class="flex-grow-1 pa-2">
          <v-text-field
            v-model="filters.title"
            prepend-icon="mdi-magnify"
            label="Fitrar por Folio"
            clearable
          ></v-text-field>
        </div>
        <div class="flex-grow-1 pa-2">
          <v-btn :to="{ name: 'vehicle.services.create' }">
            Solicitar Servicio
          </v-btn>
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
      class="elevation-1 button"
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
                    name: 'vehicle.services.edit',
                    params: { propsVehicleServiceId: item.id },
                  })
                "
              >
                <v-list-item-icon>
                  <v-icon class="blue--text">mdi-information-outline</v-icon>
                </v-list-item-icon>
                <v-list-item-content>
                  <v-list-item-title>Ver Detalles Dispercion</v-list-item-title>
                </v-list-item-content>
              </v-list-item>
              <v-list-item @click="trash(item)">
                <v-list-item-icon>
                  <v-icon class="red--text">mdi-delete</v-icon>
                </v-list-item-icon>
                <v-list-item-content>
                  <v-list-item-title>Eliminar Dispercion</v-list-item-title>
                </v-list-item-content>
              </v-list-item>
            </v-list-item-group>
          </v-list>
        </v-menu>
      </template>
      <template v-slot:[`item.folio`]="{ item }">
        {{ item.id.toString().padStart(5, 0) }}
      </template>

      <template v-slot:[`item.solicitante`]="{ item }">
        <v-list-item dense class="pa-0">
          <v-list-item-content class="pa-0">
            <v-list-item-title>{{ item.user.name }}</v-list-item-title>
            <v-list-item-subtitle>
              {{ item.user.agency.title }}
            </v-list-item-subtitle>
          </v-list-item-content>
        </v-list-item>
      </template>
      <template v-slot:[`item.cargoA`]="{ item }">
        <v-list-item dense class="pa-0">
          <v-list-item-content class="pa-0">
            <v-list-item-title>{{ item.cargo_a.title }}</v-list-item-title>
            <v-list-item-subtitle>
              {{ item.con_cargo_a }}
            </v-list-item-subtitle>
          </v-list-item-content>
        </v-list-item>
      </template>
      <template v-slot:[`item.kms`]="{ item }">
        <v-list-item dense class="pa-0">
          <v-list-item-content class="pa-0">
            <v-list-item-title
              >{{ item.kilometraje_servicio }} kms</v-list-item-title
            >
          </v-list-item-content>
        </v-list-item>
      </template>
      <template v-slot:[`item.created_at`]="{ item }">
        <span class="caption">
          {{ $appFormatters.formatDate(item.created_at, "DD-MMM-yyyy") }}
        </span>
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
          text: "Folio",
          value: "folio",
          align: "left",
          sortable: false,
        },
        {
          text: "Matricula",
          value: "vehicle.matricula",
          align: "left",
          sortable: false,
        },
        {
          text: "Solicitante y Sucursal",
          value: "solicitante",
          align: "left",
          sortable: false,
        },
        {
          text: "Cargo Suc y/o Depto",
          value: "cargoA",
          align: "center",
          sortable: false,
        },
        {
          text: "Kilometraje Servicio",
          value: "kms",
          align: "center",
          sortable: false,
        },
        {
          text: "Estatus",
          value: "estatus.title",
          align: "center",
          sortable: false,
        },
        {
          text: "Fecha Solicitud",
          value: "created_at",
          align: "center",
          sortable: false,
        },
      ],
      items: [],
      totalItems: 0,
      pagination: {
        rowsPerPage: 10,
      },

      filters: {
        title: "",
      },
    };
  },
  mounted() {
    const self = this;
    this.$store.commit("setBreadcrumbs", [
      { label: "Flotilla", to: { name: "vehicle.list" } },
      { label: "Servicios", name: "" },
    ]);
  },
  watch: {
    "pagination.page": function() {
      this.loadServices(() => {});
    },
    "pagination.rowsPerPage": function() {
      this.loadServices(() => {});
    },
     filters: {
      handler: _.debounce(function(v) {
        this.loadServices(() => {
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
        title: "Confimacion",
        message: "¿Seguron en Eliminar esta Servicio ?",
        okCb: () => {
          axios
            .delete("/admin/vehicle-services/" + vehicle.id)
            .then(function(response) {
              self.$store.commit("showSnackbar", {
                message: response.data.message,
                color: "success",
                duration: 3000,
              });

              self.loadServices(() => {});
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
    loadServices(cb) {
      const self = this;

      let params = {
        matricula: self.filters.title,
        page: self.pagination.page,
        per_page: self.pagination.rowsPerPage,
      };

      axios
        .get("/admin/vehicle-services", { params: params })
        .then(function(response) {
          self.items = response.data.data.data;
          self.totalItems = response.data.data.total;
          self.pagination.totalItems = response.data.data.total;
          (cb || Function)();
        });
    },
  },
};
</script>
