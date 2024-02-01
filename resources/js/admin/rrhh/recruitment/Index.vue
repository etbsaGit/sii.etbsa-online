<template>
  <v-data-table
    :headers="headers"
    :items="items"
    :options.sync="pagination"
    :server-items-length="totalItems"
    class="text-truncate blue--text text-uppercase caption"
    calculate-widths
    fixed-header
    caption
    dense
  >
    <template v-slot:top>
      <search-panel
        :rightDrawer="rightDrawer"
        @cancelSearch="cancelSearch"
        @resetFilter="resetFilter"
      >
        <v-form ref="formFilter"> </v-form>
      </search-panel>
      <v-card class="d-flex justify-end align-center flex-wrap px-3 py-1" flat>
        <v-card
          flat
          class="d-flex d-flex justify-space-between align-center flex-wrap py-2"
          :class="'flex-grow-1 flex-shrink-0'"
        >
          <v-text-field
            v-model="search"
            label="Buscar"
            class="pa-2"
            prepend-icon="mdi-magnify"
            hide-details
            clearable
            outlined
            filled
            dense
          ></v-text-field>
          <v-select
            v-model="filters.estatus"
            :items="options.estatus"
            label="Estatus"
            class="pa-2"
            outlined
            filled
            hide-details
            dense
          ></v-select>
        </v-card>
        <v-spacer></v-spacer>
        <v-divider class="mx-2" inset vertical></v-divider>
        <table-header-buttons
          :updateSearchPanel="updateSearchPanel"
          :reloadTable="reloadTable"
        ></table-header-buttons>
      </v-card>
      <v-toolbar flat>
        <v-toolbar-title>Requisiciones de Personal</v-toolbar-title>
        <v-divider class="mx-4" inset vertical></v-divider>
        <v-spacer></v-spacer>
        <v-btn
          color="primary"
          class="mb-2"
          :to="{ name: 'rrhh.recruitment.create' }"
          rounded
        >
          Crear Solicitud
        </v-btn>
      </v-toolbar>
      <dialog-component
        :show="dialogEdit"
        @close="dialogEdit = false"
        closeable
        fullscreen
        :title="`Detalle de la Requisicion:`"
        max-width="900"
      >
        <show-recruitment :recruitmentId="editedId"></show-recruitment>
      </dialog-component>
    </template>
    <template v-slot:[`item.action`]="{ item }">
      <v-icon class="blue--text" @click="editItem(item)">
        mdi-information-outline
      </v-icon>
    </template>
    <template v-slot:[`item.sucursal_solcitante`]="{ item }">
      <v-list-item dense class="pa-0">
        <v-list-item-content class="pa-0 caption">
          <v-list-item-title>{{ item.sucursal_solcitante }}</v-list-item-title>
          <v-list-item-subtitle>
            {{ item.area_solicitante }}
          </v-list-item-subtitle>
        </v-list-item-content>
      </v-list-item>
    </template>
    <template v-slot:[`item.puesto`]="{ item }">
      <v-list-item dense class="pa-0">
        <v-list-item-content class="pa-0 caption">
          <v-list-item-title>{{ item.puesto }}</v-list-item-title>
          <v-list-item-subtitle>
            {{ item.tipo_vacante }}
          </v-list-item-subtitle>
        </v-list-item-content>
      </v-list-item>
    </template>
    <template v-slot:[`item.estatus.title`]="{ item, value }">
      <!-- <v-btn v-if="item.responsable" text small>
        {{ item.responsable.name }}
      </v-btn> -->
      <v-edit-dialog
        large
        persistent
        save-text="Cambiar"
        @save="save(item)"
        @cancel="cancel"
        @open="open"
      >
        <v-btn :color="colors[item.estatus.key]" small text>
          {{ value }}
        </v-btn>
        <template v-slot:input>
          <v-form v-model="valid" ref="formChangeEstatus" lazy-validation>
            <div class="mt-4 title">Seleccione Estatus: {{ value }}</div>
            <v-autocomplete
              v-model="item.estatus_key"
              :items="options.estatus"
              :rules="[(v) => !!v || 'Es Requerido']"
              autofocus
            ></v-autocomplete>
          </v-form>
        </template>
      </v-edit-dialog>
    </template>
    <template v-slot:[`item.created_at`]="{ value }">
      {{ $appFormatters.formatDate(value, "DD MMM YYYY") }}
    </template>
  </v-data-table>
</template>

<script>
import SearchPanel from "@admin/components/shared/SearchPanel.vue";
import TableHeaderButtons from "@admin/components/shared/TableHeaderButtons.vue";
import DialogComponent from "@admin/components/DialogComponent.vue";
import ShowRecruitment from "./Show.vue";
export default {
  components: {
    TableHeaderButtons,
    SearchPanel,
    DialogComponent,
    ShowRecruitment,
  },
  mounted() {
    this.loadEployeeRecruitment(() => {});
    this.$store.commit("setBreadcrumbs", [
      { label: "Requision de Personal", name: "" },
    ]);
  },
  data() {
    return {
      // menu: false,
      valid: true,
      dialogCreate: false,
      dialogEdit: false,
      showSearchPanel: false,
      dialogDelete: false,
      headers: [
        {
          text: "Solicitante",
          align: "left",
          sortable: false,
          fixed: true,
          value: "solicitante.name",
        },
        {
          text: "Sucursal / Area Solicitante",
          align: "left",
          sortable: false,
          fixed: true,
          value: "sucursal_solcitante",
        },
        {
          text: "Puesto / Vacante",
          align: "left",
          sortable: false,
          fixed: true,
          value: "puesto",
        },
        {
          text: "Estatus",
          align: "center",
          sortable: false,
          fixed: true,
          value: "estatus.title",
        },
        {
          text: "Fecha Creacion",
          align: "center",
          sortable: false,
          fixed: true,
          value: "created_at",
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
        folio: null,
        supplier: null,
        agencie: null,
        uso_cfdi: null,
        metodo_pago: null,
        forma_pago: null,
        estatus: "todos",
      },
      options: {
        estatus: [
          { text: "Pendientes", value: "pendiente" },
          { text: "Autorizados", value: "autorizado" },
          { text: "En Reclutamiento", value: "reclutamiento" },
          { text: "Cubierta", value: "cubierta" },
          { text: "Todos", value: "todos" },
        ],
      },
      colors: {
        pendiente: "green",
        reclutamiento: "pink",
        cubierta: "blue",
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
        this.reloadTable();
      }, 999),
      deep: true,
    },
    search: _.debounce(function (v) {
      this.reloadTable();
    }, 999),
  },
  methods: {
    updateSearchPanel() {
      this.rightDrawer = !this.rightDrawer;
    },
    reloadTable() {
      this.loadEployeeRecruitment(() => {});
    },
    cancelSearch() {
      this.showSearchPanel = false;
    },
    editItem(item) {
      this.editedId = item.id;
      this.editedItem = item;
      this.dialogEdit = true;
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

    async loadEployeeRecruitment(cb) {
      const _this = this;
      let params = {
        ..._this.filters,
        search: _this.search,
        page: _this.pagination.page,
        per_page: _this.pagination.itemsPerPage,
      };
      await axios
        .get("/admin/recruitment", { params: params })
        .then(function (response) {
          let Response = response.data.data;
          _this.items = Response.data;
          _this.totalItems = Response.total;
          _this.pagination.totalItems = Response.total;
          (cb || Function)();
        });
    },

    // async loadOptions(cb) {
    //   const _this = this;
    //   await axios
    //     .get(`/admin/recruitment-options`)
    //     .then(function (response) {
    //       let Data = response.data.data;
    //       _this.options.agencies = Data.agencies;
    //       (cb || Function)();
    //     });
    // },

    resetFilter() {
      const _this = this;
      _this.$refs.formFilter.reset();
      _this.pagination.itemsPerPage = 10;
      _this.pagination.page = 1;
    },

    async save(item) {
      const _this = this;
      if (!this.$refs.formChangeEstatus.validate()) return;
      let payload = {
        estatus_key: item.estatus_key,
      };
      await axios
        .put("/admin/recruitment/" + item.id, payload)
        .then(function (response) {
          _this.$store.commit("showSnackbar", {
            message: "Cambio de Estatus Correcto",
            color: "success",
            duration: 3000,
          });
          _this.reloadTable();
        })
        .catch(function (error) {
          if (error.response) {
            this.$store.commit("showSnackbar", {
              message: `Usuario Asignado`,
              color: "success",
              duration: 2000,
            });
          } else if (error.request) {
            console.log(error.request);
          } else {
            console.log("Error", error.message);
          }
        });
    },
    cancel() {
      this.$store.commit("showSnackbar", {
        message: "Cancelado",
        color: "error",
        duration: 2000,
      });
    },
    open() {
      this.$store.commit("showSnackbar", {
        message: "Asignar Responsable a la Unidad",
        color: "primary",
        duration: 3000,
      });
    },
  },
};
</script>

<style scoped></style>
