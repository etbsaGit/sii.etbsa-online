<template>
  <v-data-table
    v-model="selected"
    :headers="headers"
    :items="items"
    :options.sync="pagination"
    :server-items-length="totalItems"
    show-select
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
        </v-card>
        <v-spacer></v-spacer>
        <v-divider class="mx-2" inset vertical></v-divider>
        <table-header-buttons
          :updateSearchPanel="updateSearchPanel"
          :reloadTable="reloadTable"
        ></table-header-buttons>
      </v-card>
      <v-card class="d-flex justify-end align-center flex-wrap px-3 py-1" flat>
        <v-card
          flat
          class="d-flex d-flex justify-start align-center flex-wrap py-2"
        >
          <v-toolbar-title>Cargos Internos</v-toolbar-title>
          <v-divider class="mx-4" inset vertical></v-divider>
          <div
            class="d-flex flex-wrap justify-start"
            v-if="selected.length > 0"
          >
            Acciones de Seleccion:
            <v-btn
              v-can="'gerente'"
              class="mx-2"
              small
              color="orange"
              dark
              @click="UpdateStatus('autorizado')"
            >
              Autorizar
            </v-btn>
            <v-btn
              class="mx-2"
              small
              color="purple"
              dark
              @click="UpdateStatus('por_pagar')"
            >
              Programar Aplicacion
            </v-btn>
            <v-btn small color="red" dark @click="UpdateStatus('denegar')">
              Rechazar
            </v-btn>
          </div>
        </v-card>
        <v-spacer></v-spacer>
        <v-divider class="mx-2" inset vertical></v-divider>
        <v-btn
          v-role-or-permission="'Super User|canCreate'"
          color="primary"
          :to="{ name: 'cargos_internos.create' }"
          rounded
        >
          Solicitar Nuevo Cargo
        </v-btn>
      </v-card>
    </template>

    <template #[`item.amount`]="{ value }">
      {{ value | currency }}
    </template>

    <template #[`item.cargos_sucursal`]="{ value }">
      <div v-if="value.length == 0">Sin Cargos</div>
      <v-dialog v-else width="500">
        <template v-slot:activator="{ on, attrs }">
          <v-btn color="blue lighten-2" small dark v-bind="attrs" v-on="on">
            Mostrar ({{ value.length }})
          </v-btn>
        </template>

        <v-card>
          <v-card-title class="text-title text-uppercase grey lighten-2">
            Cargos a Sucursales
          </v-card-title>

          <v-card-text>
            <v-list dense>
              <v-list-item
                v-for="(item, index) in value"
                :key="`cargo-${index}`"
              >
                <v-list-item-content>
                  <v-list-item-title>{{ item.agencia }}</v-list-item-title>
                  <v-list-item-subtitle>
                    {{ item.departamento }}
                  </v-list-item-subtitle>
                </v-list-item-content>

                <v-list-item-action-text>
                  {{ item.percent }} %
                </v-list-item-action-text>
              </v-list-item>
            </v-list>
          </v-card-text>
        </v-card>
      </v-dialog>
    </template>

    <template #[`item.estatus`]="{ value }">
      <v-chip small class="ma-2"> {{ value }}</v-chip>
    </template>
    <template #[`item.action`]="{ item }">
      <v-btn
        @click="
          $router.push({
            name: 'cargos_internos.edit',
            params: { CargoInternoId: item.id },
          })
        "
        small
        icon
        color="info"
      >
        <v-icon small>mdi-pencil</v-icon>
      </v-btn>
    </template>
  </v-data-table>
</template>

<script>
import SearchPanel from "@admin/components/shared/SearchPanel.vue";
import TableHeaderButtons from "@admin/components/shared/TableHeaderButtons.vue";
import DialogComponent from "@admin/components/DialogComponent.vue";
export default {
  name: "CargosInternosList",
  components: {
    TableHeaderButtons,
    SearchPanel,
    DialogComponent,
  },
  data() {
    return {
      search: "",
      items: [],
      totalItems: 0,
      pagination: {
        itemsPerPage: 10,
        page: 1,
      },
      selected: [],
      headers: [
        {
          text: "Folio C.I.",
          align: "center",
          value: "id",
        },
        {
          text: "OT",
          align: "center",
          value: "ot",
        },

        {
          text: "Monto Total",
          align: "center",
          value: "amount",
        },
        {
          text: "Cargos Sucusal(es)",
          align: "center",
          value: "cargos_sucursal",
          sortable: false,
          width: 250,
        },
        {
          text: "Solicitante",
          align: "center",
          value: "created_by",
        },
        {
          text: "Estatus",
          align: "center",
          value: "estatus",
          sortable: false,
          width: 250,
        },

        {
          align: "center",
          sortable: false,
          fixed: true,
          value: "action",
        },
      ],
      showSearchPanel: false,
      filters: {
        // folio: null,
        // supplier: null,
        // agencie: null,
        // uso_cfdi: null,
        // metodo_pago: null,
        // forma_pago: null,
        // estatus: "todos",
      },
    };
  },
  mounted() {
    this.reloadTable();
    this.$store.commit("setBreadcrumbs", [{ label: "Cargos Internos" }]);
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
    async reloadTable() {
      const _this = this;
      _this.selected = [];
      let params = {
        // ..._this.filter,
        search: _this.search,
        order_by: _this.pagination.sortBy[0] || "id",
        order_sort: _this.pagination.sortDesc[0] ? "asc" : "desc",
        page: _this.pagination.page,
        per_page: _this.pagination.itemsPerPage,
      };
      const { data: data } = await axios.get("/admin/cargos-internos", {
        params,
      });
      // console.log(data.data);
      this.items = data.data.data;
      this.totalItems = data.data.total;
      this.pagination.totalItems = data.data.total;
    },
    cancelSearch() {
      this.showSearchPanel = false;
    },
    updateSearchPanel() {
      this.rightDrawer = !this.rightDrawer;
    },
    resetFilter() {
      const _this = this;
      _this.$refs.formFilter.reset();
      _this.pagination.itemsPerPage = 10;
      _this.pagination.page = 1;
    },
    async UpdateStatus(status_key) {
      const _this = this;
      let payload = {
        cargos_internos_ids: _this.selected.map((item) => item.id),
        status_key,
      };
      const { data } = await axios.post(
        "/admin/cargos-internos/update-status",
        payload
      );
      _this.$store.commit("showSnackbar", {
        message: data.message,
        color: "success",
        duration: 3000,
      });
      _this.reloadTable();
    },
  },
};
</script>

<style>
</style>