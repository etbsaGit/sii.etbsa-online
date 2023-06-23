<template>
  <v-data-table
    :headers="headers"
    :items="items"
    :options.sync="pagination"
    :server-items-length="totalItems"
    class="text-truncate blue--text font-weight-bold text-uppercase caption"
    calculate-widths
    fixed-header
    caption
    dense
  >
    <template #top>
      <search-panel
        :rightDrawer="rightDrawer"
        @cancelSearch="cancelSearch"
        @resetFilter="resetFilter"
      >
        <v-form ref="formFilter">
          <div class="d-flex flex-column ma-2">
            <v-text-field
              v-model="filters.number_employee"
              label="Numero de Empleado"
              prepend-icon="mdi-filter-variant"
              hide-details
              outlined
              filled
              dense
              class="mb-2"
            ></v-text-field>
            <v-text-field
              v-model="filters.name"
              label="Nombre Empleado"
              prepend-icon="mdi-filter-variant"
              hide-details
              outlined
              filled
              dense
              class="mb-2"
            ></v-text-field>
            <v-text-field
              v-model="filters.last_name"
              label="Apellidos"
              prepend-icon="mdi-filter-variant"
              hide-details
              outlined
              filled
              dense
              class="mb-2"
            ></v-text-field>
            <v-text-field
              v-model="filters.job_title"
              label="Puesto"
              prepend-icon="mdi-filter-variant"
              hide-details
              outlined
              filled
              dense
              class="mb-2"
            ></v-text-field>
            <v-select
              v-model="filters.agencies_ids"
              :items="options.agencies"
              item-text="title"
              item-value="id"
              label="Sucursal"
              prepend-icon="mdi-filter-variant"
              hide-details
              outlined
              filled
              clearable
              dense
              class="mb-2"
              multiple
              chips
              deletable-chips
            ></v-select>
            <v-select
              v-model="filters.department_ids"
              :items="options.departments"
              item-text="title"
              item-value="id"
              label="Departamento"
              prepend-icon="mdi-filter-variant"
              hide-details
              outlined
              filled
              clearable
              dense
              class="mb-2"
              multiple
              chips
              deletable-chips
            ></v-select>
          </div>
        </v-form>
      </search-panel>
      <v-card class="d-flex justify-end align-center flex-wrap px-3 py-1" flat>
        <v-card
          flat
          class="d-flex d-flex justify-space-between align-center flex-wrap py-2"
          :class="'flex-grow-1 flex-shrink-0'"
        >
          <v-text-field
            v-model="search"
            label="Buscar #Empleado, Nombre"
            class="pa-2"
            prepend-icon="mdi-magnify"
            hide-details
            clearable
            outlined
            filled
            dense
          ></v-text-field>
        </v-card>
        <v-spacer></v-spacer>
        <v-divider class="mx-2" inset vertical></v-divider>
        <table-header-buttons
          :updateSearchPanel="updateSearchPanel"
          :reloadTable="reloadTable"
        ></table-header-buttons>
      </v-card>
      <v-toolbar flat>
        <v-toolbar-title>Empleados</v-toolbar-title>
        <v-divider class="mx-4" inset vertical></v-divider>
        <v-spacer></v-spacer>
        <v-btn
          color="primary"
          class="mb-2"
          @click="dialogCreate = true"
          rounded
        >
          Registrar nuevo Empleado
        </v-btn>
      </v-toolbar>
      <dialog-component
        :show="dialogCreate"
        @close="dialogCreate = false"
        closeable
        :fullscreen="$vuetify.breakpoint.mobile"
        title="Registrar Nuevo Empleado"
        max-width="600"
      >
        <create-employee
          v-if="dialogCreate"
          @cancel="dialogCreate = false"
        ></create-employee>
      </dialog-component>
      <dialog-component
        :show="dialogShow"
        @close="dialogShow = false"
        closeable
        :fullscreen="$vuetify.breakpoint.mobile"
        title="Informacion del Empleado"
        max-width="600"
      >
        <edit-employee
          v-if="dialogShow"
          @cancel="dialogShow = false"
          :edit-item-id="editedId"
        ></edit-employee>
      </dialog-component>
    </template>
    <template #[`item.action`]="{ item }">
      <v-icon class="green--text" @click="editItem(item)"> mdi-pencil </v-icon>
    </template>
    <template #[`item.agency.title`]="{ item }">
      <v-list-item dense class="pa-0 blue--text">
        <v-list-item-content class="pa-0 caption">
          <v-list-item-title v-if="item.agency">
            {{ item.agency.title }}
          </v-list-item-title>
          <v-list-item-subtitle v-if="item.department">
            {{ item.department.title }}
          </v-list-item-subtitle>
        </v-list-item-content>
      </v-list-item>
    </template>
    <template #[`item.full_name`]="{ item }">
      <div class="d-flex align-center">
        <v-avatar size="34" class="me-3">
          <VImg :src="item.profile_photo_url" />
        </v-avatar>

        <div class="d-flex flex-column">
          <div class="text-h6 font-weight-medium mb-0">
            {{ item.full_name }}
          </div>
          <span v-if="item.user" class="text-caption">
            {{ item.user.email }}
          </span>
        </div>
      </div>
    </template>
    <template #[`item.admission_date`]="{ item }">
      <span v-if="item.admission_date">
        {{ $appFormatters.formatDate(item.admission_date, "ll") }}
      </span>
    </template>
  </v-data-table>
</template>

<script>
import SearchPanel from "@admin/components/shared/SearchPanel.vue";
import TableHeaderButtons from "@admin/components/shared/TableHeaderButtons.vue";
import DialogComponent from "@admin/components/DialogComponent.vue";
import CreateEmployee from "./Create.vue";
import EditEmployee from "./Edit.vue";
export default {
  components: {
    TableHeaderButtons,
    SearchPanel,
    DialogComponent,
    CreateEmployee,
    EditEmployee,
  },
  mounted() {
    this.reloadTable();
    this.$store.commit("setBreadcrumbs", [{ label: "Empleados" }]);
  },
  data() {
    return {
      // menu: false,
      valid: true,
      dialogCreate: false,
      dialogEdit: false,
      dialogShow: false,
      showSearchPanel: false,
      dialogDelete: false,
      headers: [
        {
          text: "Num. Empleado",
          align: "center",
          sortable: false,
          fixed: true,
          value: "number_employee",
        },
        {
          text: "Nombre Completo",
          align: "left",
          sortable: false,
          fixed: true,
          value: "full_name",
        },
        {
          text: "Sucursal / Departamento",
          align: "left",
          sortable: false,
          fixed: true,
          value: "agency.title",
        },
        {
          text: "Puesto",
          align: "left",
          sortable: false,
          fixed: true,
          value: "job_title",
        },
        {
          text: "Fecha de Ingreso",
          align: "center",
          sortable: false,
          fixed: true,
          value: "admission_date",
        },
        {
          align: "center",
          sortable: false,
          fixed: true,
          value: "action",
        },
      ],
      editedId: -1,
      editedItem: {},
      items: [],
      search: null,
      filters: {
        number_employee: null,
        name: null,
        last_name: null,
        job_title: null,
        agencies_ids: null,
        department_ids: null,
      },
      options: {
        agencies: [],
        departments: [],
      },

      totalItems: 0,
      pagination: {
        itemsPerPage: 10,
        page: 1,
      },
    };
  },
  computed: {
    rightDrawer: {
      get() {
        return this.showSearchPanel;
      },
      set(_showSearchPanel) {
        this.showSearchPanel = _showSearchPanel;
      },
    },
  },
  watch: {
    pagination: {
      handler: _.debounce(function () {
        this.reloadTable();
      }, 999),
      deep: true,
    },
    filters: {
      handler: _.debounce(function (v) {
        this.pagination.page = 1;
        this.reloadTable();
      }, 999),
      deep: true,
    },
    search: _.debounce(function (v) {
      this.pagination.page = 1;
      this.reloadTable();
    }, 999),
  },
  methods: {
    updateSearchPanel() {
      this.rightDrawer = !this.rightDrawer;
    },
    reloadTable() {
      this.loadEmployees(() => {});
    },
    cancelSearch() {
      this.showSearchPanel = false;
    },
    editItem(item) {
      this.editedId = item.id;
      this.editedItem = item;
      this.dialogShow = true;
    },
    deleteItem(item) {
      this.editedId = item.id;
      this.editedItem = Object.assign({}, item);
      this.dialogDelete = true;
    },
    closeDelete() {
      this.$nextTick(() => {
        // this.editedItem = Object.assign({}, this.defaultItem);
        this.editedItem = { ...this.defaultItem };
        this.editedId = -1;
      });
      this.dialogDelete = false;
    },

    async loadEmployees(cb) {
      const _this = this;
      let params = {
        ..._this.filters,
        search: _this.search,
        page: _this.pagination.page,
        per_page: _this.pagination.itemsPerPage,
      };
      const {
        data: {
          data: { items, filtersOptions },
          message,
        },
      } = await axios.get("/admin/employees", { params: params });

      _this.items = items.data;
      _this.totalItems = items.total;
      _this.pagination.totalItems = items.total;
      _this.options = filtersOptions;
      _this.$store.commit("showSnackbar", {
        message: message,
        color: "success",
        duration: 3000,
      });
      (cb || Function)();
    },

    resetFilter() {
      const _this = this;
      _this.$refs.formFilter.reset();
      _this.pagination.itemsPerPage = 10;
      _this.pagination.page = 1;
    },
  },
};
</script>

<style scoped></style>
