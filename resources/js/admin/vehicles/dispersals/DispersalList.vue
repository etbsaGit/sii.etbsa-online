<template>
  <div class="component-wrap">
    <!-- search -->
    <v-card flat>
      <v-row class="align-center">
        <v-col cols="6">
          <v-text-field
            v-model="filters.title"
            prepend-icon="mdi-magnify"
            label="Fitrar por Folio"
            clearable
          ></v-text-field>
        </v-col>
        <!-- <v-col cols="6"> -->
          <v-autocomplete
            v-model="filters.agencies"
            :items="options.agencies"
            item-text="title"
            item-value="id"
            label="Filtrar Cargo A Sucursal:"
            prepend-icon="mdi-magnify"
            hide-details
            clearable
            multiple
            filled
          ></v-autocomplete>
          <!-- </v-col> -->
        <v-row class="justify-space-around">
          <v-checkbox
            v-model="filters.estatus"
            label="Pendientes"
            value="dispersal:pendiente"
            hide-details
          ></v-checkbox>
          <v-checkbox
            v-model="filters.estatus"
            label="Autorizados"
            value="dispersal:autorizado"
            hide-details
          ></v-checkbox>
          <v-checkbox
            v-model="filters.estatus"
            label="Rechazados"
            value="dispersal:rechazada"
            hide-details
          ></v-checkbox>
          <v-checkbox
            v-model="filters.estatus"
            label="Dispersados"
            value="dispersal:dispersado"
            hide-details
          ></v-checkbox>
        </v-row>
        <v-col cols="12" class="flex-grow-0 pa-2">
          <v-btn :to="{ name: 'vehicle.dispersal.create' }">
            Solicitar Dispersion
          </v-btn>
        </v-col>
      </v-row>
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
                    name: 'vehicle.dispersion.edit',
                    params: { propsVehicleDispersalId: item.id },
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
              >{{ item.kilometraje_actual }} kms</v-list-item-title
            >
            <v-list-item-subtitle>
              {{ item.kilometraje_anterior }} kms
            </v-list-item-subtitle>
          </v-list-item-content>
        </v-list-item>
      </template>
      <template v-slot:[`item.tank`]="{ item }">
        <v-progress-linear
          color="green lighten-1"
          height="15"
          :value="item.odometro_percent_gas"
        >
          <strong>{{ item.odometro_percent_gas }}%</strong>
        </v-progress-linear>
      </template>
      <template v-slot:[`item.created_at`]="{ item }">
        <span class="caption">
          {{ $appFormatters.formatDate(item.created_at, "DD-MMM-yyyy") }}
          <!-- {{ $appFormatters.formatTimeFromNow(item.created_at) }} -->
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
          text: "Litros Solicitados",
          value: "gas_lts",
          align: "center",
          sortable: false,
        },
        {
          text: "Cargo Suc y/o Depto",
          value: "cargoA",
          align: "center",
          sortable: false,
        },
        {
          text: "Actual/Anterior Kilometraje",
          value: "kms",
          align: "center",
          sortable: false,
        },
        {
          text: "Tanque Gasolina",
          value: "tank",
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
          sortable: true,
        },
      ],
      items: [],
      totalItems: 0,
      pagination: {
        rowsPerPage: 10,
      },
       options: {

        agencies: [],
      },
      filters: {
        title: "",
        estatus: ['dispersal:pendiente'],
        agencies: [],
      },
    };
  },
  mounted() {
    const self = this;
    this.$store.commit("setBreadcrumbs", [
      { label: "Flotilla", to: { name: "vehicle.list" } },
      { label: "Dispercion", name: "" },
    ]);
    this.loadResource(()=>{})
  },
  watch: {
    "pagination.page": function() {
      this.loadDispersals(() => {});
    },
    "pagination.rowsPerPage": function() {
      this.loadDispersals(() => {});
    },
    filters: {
      handler: _.debounce(function(v) {
        this.loadDispersals(() => {
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
        message: "¿Seguron en Eliminar esta Dispercion?",
        okCb: () => {
          axios
            .delete("/admin/vehicle-dispersal/" + vehicle.id)
            .then(function(response) {
              self.$store.commit("showSnackbar", {
                message: response.data.message,
                color: "success",
                duration: 3000,
              });

              self.loadDispersals(() => {});
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
    loadDispersals(cb) {
      const self = this;

      let params = {
        folio: self.filters.title,
        estatus_keys: self.filters.estatus.join(","),
        agencies_id: self.filters.agencies.join(","),
        order_sort: self.pagination.sortDesc[0] ? "desc" : "asc",
        order_by: self.pagination.sortBy[0] || "id",
        page: self.pagination.page,
        per_page: self.pagination.rowsPerPage,
      };

      axios
        .get("/admin/vehicle-dispersal", { params: params })
        .then(function(response) {
          self.items = response.data.data.data;
          self.totalItems = response.data.data.total;
          self.pagination.totalItems = response.data.data.total;
          (cb || Function)();
        });
    },
        loadResource(cb) {
      const self = this;
      axios.get("/admin/resource/agencies").then(function(response) {
        let Data = response.data.data;
        self.options.agencies = Data.agencies;
        (cb || Function)();
      });
    },
  },
};
</script>
