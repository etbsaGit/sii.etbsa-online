<template>
  <div class="component-wrap">
    <!-- search -->
    <v-card>
      <v-row class="mx-2 my-0">
        <v-col cols="6" md="4">
          <v-text-field
            prepend-icon="mdi-magnify"
            label="Fitrar por Titulo seguimeinto"
            v-model="filters.title"
            hide-details
            clearable
          ></v-text-field>
        </v-col>
        <v-col cols="6" md="4">
          <v-autocomplete
            v-model="filters.prospect"
            :items="options.prospects"
            item-text="full_name"
            item-value="id"
            label="Filtro Prospecto:"
            prepend-icon="mdi-magnify"
            hide-details
            clearable
            multiple
          ></v-autocomplete>
        </v-col>
        <v-col cols="6" md="4">
          <v-autocomplete
            v-model="filters.agencies"
            :items="options.agencies"
            item-text="title"
            item-value="id"
            label="Filtrar Agencia:"
            prepend-icon="mdi-magnify"
            hide-details
            clearable
            multiple
          ></v-autocomplete>
        </v-col>
        <v-col cols="6" md="4">
          <v-autocomplete
            v-model="filters.departments"
            :items="options.departments"
            item-text="title"
            item-value="id"
            label="Filtrar Depto."
            prepend-icon="mdi-magnify"
            hide-details
            clearable
            multiple
          ></v-autocomplete>
        </v-col>
        <!-- <v-col cols="6" md="4">
          <v-autocomplete
            v-model="filters.sellers"
            :items="options.sellers"
            item-text="name"
            item-value="id"
            label="Filtro Vendedor:"
            prepend-icon="mdi-magnify"
            hide-details
            clearable
            multiple
          ></v-autocomplete>
        </v-col> -->
        <v-row class="justify-space-around">
          <v-checkbox
            v-model="filters.estatus"
            label="Activo"
            value="activo"
            hide-details
          ></v-checkbox>
          <v-checkbox
            v-model="filters.estatus"
            label="Finalizado"
            value="finalizado"
            hide-details
          ></v-checkbox>
          <v-checkbox
            v-model="filters.estatus"
            label="Formalizado"
            value="formalizado"
            hide-details
          ></v-checkbox>
        </v-row>
      </v-row>
      <div class="flex-grow-1 pa-2 text-right">
        <v-btn
          @click="$router.push({ name: 'tracking.create' })"
          class="primary lighten-1"
          small
          dark
        >
          Levantar un Seguimiento
          <v-icon small right>mdi-plus-box</v-icon>
        </v-btn>
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
      class="elevation-1 caption"
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
                    name: 'tracking.prospect',
                    params: { propTrackingId: item.id },
                  })
                "
              >
                <v-list-item-icon>
                  <v-icon class="blue--text">mdi-information-outline</v-icon>
                </v-list-item-icon>
                <v-list-item-content>
                  <v-list-item-title>Ver Seguimiento</v-list-item-title>
                </v-list-item-content>
              </v-list-item>
            </v-list-item-group>
          </v-list>
        </v-menu>
      </template>
      <template v-slot:[`item.id`]="{ item }">
        {{ item.id.toString().padStart(5, 0) }}
      </template>
      <template v-slot:[`item.title`]="{ item }">
        <span
          class="d-inline-block text-truncate text-uppercase"
          style="max-width: 200px;"
        >
          {{ item.title }}
        </span>
      </template>
      <template v-slot:[`item.prospect.full_name`]="{ item }">
        <span class="d-inline-block text-truncate text-uppercase">
          {{ item.prospect.full_name }}
        </span>
      </template>
      <template v-slot:[`item.agency-depto`]="{ item }">
        <!-- <v-list two-line dense flat > -->
        <v-list-item dense class="pa-0">
          <v-list-item-content class="pa-0">
            <v-list-item-title>{{ item.agency.title }}</v-list-item-title>
            <v-list-item-subtitle>{{
              item.department.title
            }}</v-list-item-subtitle>
          </v-list-item-content>
        </v-list-item>
        <!-- </v-list> -->
      </template>
      <template v-slot:[`item.estatus.title`]="{ item }">
        <v-chip
          dark
          small
          :color="getColor(item.estatus.key)"
          class="text-uppercase"
        >
          {{ item.estatus.title }}
        </v-chip>
      </template>
      <template v-slot:[`item.date_next_tracking`]="{ item }">
        <v-btn small text>
          {{
            `${$appFormatters.formatTimeDiffNow(
              item.date_next_tracking,
              "days"
            ) || "0"} dias`
          }}
        </v-btn>
      </template>
      <template v-slot:[`item.updated_at`]="{ item }">
        {{ $appFormatters.formatDate(item.updated_at, "l hh:mm a") }}
      </template>
    </v-data-table>
  </div>
</template>

<script>
export default {
  data() {
    return {
      headers: [
        { text: "Action", value: "action", align: "center", sortable: false },
        { text: "Folio", value: "id", align: "left", sortable: false },
        {
          text: "Titulo Seguimiento",
          value: "title",
          align: "left",
          sortable: false,
        },
        {
          text: "Prospecto:",
          value: "prospect.full_name",
          align: "left",
          sortable: false,
        },
        {
          text: "Atendido por:",
          value: "attended_by.name",
          align: "left",
          sortable: false,
        },
        {
          text: "Agencia / Departamento",
          value: "agency-depto",
          width: 150,
          sortable: false,
        },
        {
          text: "Estatus",
          value: "estatus.title",
          align: "center",
          width: 100,
          sortable: false,
        },
        {
          text: "Sig. Seg.",
          value: "date_next_tracking",
          align: "center",
          width: 100,
          sortable: false,
        },
        {
          text: "Ultimo Cambio",
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
        title: "",
        estatus: [],
        agencies: [],
        departments: [],
        sellers: [],
        prospect: [],
      },

      options: {
        prospects: [],
        agencies: [],
        departments: [],
        sellers: [],
      },
    };
  },
  mounted() {
    const self = this;
    self.$store.commit("showLoader");

    self.$store.commit("setBreadcrumbs", [
      { label: "Users", to: { name: "users.list" } },
      { label: "Vendedores", name: "" },
    ]);
    self.loadResources(() => {
      self.$store.commit("hideLoader");
    });
  },
  watch: {
    "pagination.page": function() {
      this.loadTrackings(() => {});
    },
    "pagination.rowsPerPage": function() {
      this.loadTrackings(() => {});
    },
    filters: {
      handler: _.debounce(function(v) {
        this.loadTrackings(() => {
          this.$store.commit("hideLoader");
        });
      }, 700),
      deep: true,
    },
  },
  methods: {
    trash(seller) {
      const self = this;

      self.$store.commit("showDialog", {
        type: "confirm",
        title: "Confirm Deletion",
        message: "Are you sure you want to delete this seller?",
        okCb: () => {
          axios
            .delete("/admin/sellers/" + seller.id)
            .then(function(response) {
              self.$store.commit("showSnackbar", {
                message: response.data.message,
                color: "success",
                duration: 3000,
              });

              self.loadTrackings(() => {});
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
    loadTrackings(cb) {
      const self = this;
      self.$store.commit("showLoader");
      let params = {
        title: self.filters.title,
        estatus_keys: self.filters.estatus.join(","),
        agencies_id: self.filters.agencies.join(","),
        departments_id: self.filters.departments.join(","),
        prospects_id: self.filters.prospect.join(","),
        sellers_id: self.filters.sellers.join(","),
        page: self.pagination.page,
        per_page: self.pagination.rowsPerPage,
      };

      axios.get("/admin/tracking", { params: params }).then(function(response) {
        self.items = response.data.data.data;
        self.totalItems = response.data.data.total;
        self.pagination.totalItems = response.data.data.total;
        (cb || Function)();
      });
    },
    loadResources(cb) {
      const self = this;

      let params = {
        paginate: "no",
      };
      axios
        .get("/admin/tracking/sales_history/resources")
        .then(function(response) {
          let Data = response.data.data;
          self.options.agencies = Data.agencies;
          self.options.departments = Data.departments;
          self.options.prospects = Data.prospects;
        });

      axios.get("/admin/sellers", { params: params }).then(function(response) {
        self.options.sellers = response.data.data;
        (cb || Function)();
      });

      // return seller;
    },
    getColor(value) {
      if (value == "finalizado") return "red";
      else if (value == "formalizado") return "blue";
      else return "primary";
    },
  },
};
</script>
