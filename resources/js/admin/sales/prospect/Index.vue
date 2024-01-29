<template>
  <v-data-table
    v-bind:headers="headers"
    :options.sync="pagination"
    :items="items"
    :server-items-length="totalItems"
    class="elevation-1 text-uppercase font-weight-bold caption ma-2"
    fixed-header
    dense
  >
    <template #top>
      <v-card flat class="py-4 px-2 d-flex flex-row align-center">
        <v-text-field
          v-model="filters.search"
          prepend-icon="mdi-magnify"
          label="Fitrar por Nombre / Telefono"
          hide-details
          clearable
          outlined
          filled
          dense
        ></v-text-field>
        <v-spacer></v-spacer>
        <!-- @click="$router.push({ name: 'prospect.create' })" -->
      </v-card>
      <v-card>
        <v-card-title>
          Prospectos
          <v-spacer />
          <v-btn class="primary" @click="dialogCreate = true">
            Registrar Prospecto
            <v-icon right>mdi-plus</v-icon>
          </v-btn>
          <v-btn class="accent ml-2" @click="loadProspects()">
            <v-icon>mdi-refresh</v-icon>
          </v-btn>
        </v-card-title>
      </v-card>
      <dialog-component
        :show="dialogEdit"
        @close="dialogEdit = false"
        closeable
        :fullscreen="$vuetify.breakpoint.mobile"
        title="Editar Prospecto"
        :maxWidth="600"
      >
        <prospect-edit
          :propProspectId="editedId"
          :key="editedId"
        ></prospect-edit>
      </dialog-component>
      <dialog-component
        :show="dialogCreate"
        @close="dialogCreate = false"
        closeable
        :fullscreen="$vuetify.breakpoint.mobile"
        title="Registrar Nuevo Prospecto"
        :maxWidth="600"
      >
        <prospect-create></prospect-create>
      </dialog-component>
    </template>
    <template #[`item.action`]="{ item }">
      <v-menu offset-x>
        <template #activator="{ on, attrs }">
          <v-btn icon small v-bind="attrs" v-on="on">
            <v-icon>mdi-dots-vertical</v-icon>
          </v-btn>
        </template>
        <v-list shaped>
          <v-list-item-group>
            <v-list-item @click="(editedId = item.id), (dialogEdit = true)">
              <v-list-item-icon>
                <v-icon class="blue--text" left>mdi-pencil</v-icon>
              </v-list-item-icon>
              <v-list-item-content>
                <v-list-item-title>Editar Prospecto</v-list-item-title>
              </v-list-item-content>
            </v-list-item>
          </v-list-item-group>
        </v-list>
      </v-menu>
    </template>
  </v-data-table>
</template>

<script>
import DialogComponent from "../../components/DialogComponent.vue";
import ProspectCreate from "./ProspectCreate.vue";
import ProspectEdit from "./ProspectEdit.vue";
export default {
  components: { DialogComponent, ProspectCreate, ProspectEdit },
  data() {
    return {
      dialogCreate: false,
      dialogEdit: false,
      headers: [
        { text: "Action", value: "action", align: "left", sortable: false },
        {
          text: "Dirigido a / Razon Social",
          value: "full_name",
          align: "left",
          sortable: false,
        },
        {
          text: "Organizacion / Cliente",
          value: "customer.full_name",
          align: "left",
          sortable: false,
        },
        { text: "Telefono", value: "phone", align: "left", sortable: false },
        // {
        //   text: "Email",
        //   value: "email",
        //   align: "left",
        //   sortable: false,
        // },
        // {
        //   text: "Proviene:",
        //   value: "town",
        //   align: "left",
        //   sortable: false,
        // },
        {
          text: "Seguimientos Activos:",
          value: "tracking_count",
          align: "center",
          sortable: false,
        },
      ],
      items: [],
      totalItems: 0,
      pagination: {
        rowsPerPage: 10,
      },
      editedId: null,
      options: {
        customers: {},
      },

      filters: {
        search: "",
      },
    };
  },
  mounted() {
    const self = this;
    self.$store.commit("setBreadcrumbs", [{ label: "Prospectos", name: "" }]);
  },
  watch: {
    pagination: {
      handler: _.debounce(function (v) {
        this.loadProspects(() => {});
      }, 700),
      deep: true,
    },
    filters: {
      handler: _.debounce(function (v) {
        this.loadProspects(() => {});
      }, 700),
      deep: true,
    },
  },
  methods: {
    async loadProspects(cb) {
      const _this = this;

      let params = {
        ..._this.filters,
        order_sort: _this.pagination.sortDesc[0] ? "asc" : "desc",
        order_by: _this.pagination.sortBy[0] || "id",
        page: _this.pagination.page,
        per_page: _this.pagination.itemsPerPage,
      };

      await axios.get("/admin/prospects", { params }).then((response) => {
        _this.items = response.data.data.data;
        _this.totalItems = response.data.data.total;
        _this.pagination.totalItems = response.data.data.total;
        (cb || Function)();
      });
    },
    async getOptions() {
      let res = await axios.get("/admin/options/prospects");
      this.options = res.data.data.options;
    },
  },
};
</script>
