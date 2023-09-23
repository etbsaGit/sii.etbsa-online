<template>
  <v-data-table
    :headers="headers"
    :items="items"
    :options.sync="pagination"
    :server-items-length="totalItems"
    class="text-truncate blue--text caption"
    calculate-widths
    fixed-header
    caption
    dense
    dark
  >
    <template v-slot:top>
      <v-toolbar flat>
        <v-toolbar-title>Usuarios</v-toolbar-title>
        <v-divider class="mx-4" inset vertical></v-divider>
        <v-spacer></v-spacer>
        <v-btn
          color="primary"
          class="mb-2"
          @click="$router.push({ name: 'users.create' })"
          rounded
        >
          Registrar nuevo Usuario
        </v-btn>
      </v-toolbar>
      <search-panel
        :rightDrawer="rightDrawer"
        @cancelSearch="cancelSearch"
        @resetFilter="resetFilter"
      >
        <v-form ref="formFilter">
          <v-row class="mr-2 offset-1 overline" dense>
            <v-col cols="12">
              <v-text-field
                filled
                prepend-icon="mdi-magnify"
                label="Filtrar por Nombre"
                v-model="filters.name"
                dense
              ></v-text-field>
            </v-col>
            <v-col cols="12">
              <v-text-field
                filled
                prepend-icon="mdi-magnify"
                label="Filtrar por Email"
                v-model="filters.email"
                dense
              ></v-text-field>
            </v-col>
            <v-col cols="12">
              <v-autocomplete
                v-model="filters.groupId"
                prepend-icon="mdi-filter-variant"
                label="Filtrar por Grupos"
                :items="filters.groupOptions"
                item-text="name"
                item-value="id"
                deletable-chips
                clearable
                multiple
                chips
                filled
              ></v-autocomplete>
            </v-col>
          </v-row>
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

      <v-dialog
        v-model="dialogs.showPermissions.show"
        absolute
        max-width="500px"
      >
        <v-card>
          <v-card-title>
            <div class="headline">
              <v-icon>mdi-key</v-icon> Permisos Especiales
            </div>
          </v-card-title>
          <v-card-text>
            <v-chip
              v-for="(p, key) in dialogs.showPermissions.items"
              :key="key"
              outlined
              :class="{
                green: p.value == 1,
                red: p.value == -1,
                blue: p.value == 0,
              }"
              class="ma-2"
            >
              <v-icon v-if="p.value == -1" left color="red">mdi-cancel</v-icon>
              <v-icon v-if="p.value == 0" left color="blue">
                mdi-swap-horizontal
              </v-icon>
              <v-icon v-if="p.value == 1" left color="green">
                mdi-check-circle
              </v-icon>
              {{ p.title }}
            </v-chip>
            <p v-if="dialogs.showPermissions.items.length == 0">Sin Permisos</p>
          </v-card-text>
        </v-card>
      </v-dialog>
    </template>

    <template v-slot:[`header.email`]="{ header }">
      <v-icon left small>mdi-email</v-icon> {{ header.text }}
    </template>
    <template v-slot:[`item.action`]="{ item }">
      <v-btn
        @click="
          $router.push({
            name: 'users.edit',
            params: { id: item.id },
          })
        "
        small
        icon
        color="info"
      >
        <v-icon small>mdi-pencil</v-icon>
      </v-btn>
      <v-btn @click="trash(item)" small icon color="red">
        <v-icon small>mdi-delete</v-icon>
      </v-btn>
    </template>
    <template v-slot:[`item.permissions`]="{ item }">
      <v-btn
        small
        @click="showDialog('user_permissions', item.permissions)"
        outlined
        rounded
        color="grey"
      >
        <v-icon small left>mdi-eye-outline</v-icon> Mostrar
      </v-btn>
    </template>
    <template v-slot:[`item.groups`]="{ item }">
      <v-chip
        v-for="group in item.groups"
        :key="group.id"
        outlined
        color="secondary"
        text-color="accent"
        small
      >
        {{ group.name }}
      </v-chip>
    </template>
    <template v-slot:[`item.last_login`]="{ item }">
      {{ $appFormatters.formatDate(item.last_login) }}
    </template>
    <template v-slot:[`item.active`]="{ item }">
      <v-avatar outlined>
        <v-icon v-if="item.active != null" class="green--text">
          mdi-check-circle-outline
        </v-icon>
        <v-icon class="grey--text" v-else>mdi-alert-circle-outline</v-icon>
      </v-avatar>
    </template>
  </v-data-table>
</template>

<script>
import SearchPanel from "../../components/shared/SearchPanel.vue";
import TableHeaderButtons from "../../components/shared/TableHeaderButtons.vue";
export default {
  components: { SearchPanel, TableHeaderButtons },
  data() {
    return {
      headers: [
        {
          value: "action",
          align: "center",
          sortable: false,
        },
        {
          text: "Nombre",
          value: "name",
          align: "left",
          sortable: true,
        },
        { text: "Email", value: "email", align: "left", sortable: true },
        {
          text: "Permisos",
          value: "permissions",
          align: "center",
          sortable: false,
        },
        { text: "Grupos", value: "groups", align: "center", sortable: false },
        {
          text: "Ultimo inicio",
          value: "last_login",
          align: "right",
          sortable: true,
        },
        {
          text: "A2F",
          value: "active",
          align: "center",
          width: 10,
          sortable: true,
        },
      ],
      items: [],
      totalItems: 0,
      pagination: {
        itemsPerPage: 10,
      },
      search: null,
      filters: {
        name: "",
        email: "",
        groupId: [],
        groupOptions: [],
      },
      dialogs: {
        showPermissions: {
          items: [],
          show: false,
        },
      },
      dialogCreate: false,
      dialogEdit: false,
      dialogShow: false,
      showSearchPanel: false,
      dialogDelete: false,
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
  mounted() {
    const _this = this;

    _this.loadGroups(() => {});

    _this.$eventBus.$on(
      ["USER_ADDED", "USER_UPDATED", "USER_DELETED", "GROUP_ADDED"],
      () => {
        _this.loadUsers(() => {});
      }
    );

    _this.$store.commit("setBreadcrumbs", [
      { label: "Users", to: { name: "users.list" } },
    ]);
  },
  watch: {
    search: _.debounce(function () {
      const _this = this;
      _this.loadUsers(() => {});
    }, 999),
    pagination: {
      handler() {
        this.loadUsers(() => {});
      },
      deep: true,
    },
    "filters.name": _.debounce(function () {
      const _this = this;
      _this.loadUsers(() => {});
    }, 999),
    "filters.email": _.debounce(function () {
      const _this = this;
      _this.loadUsers(() => {});
    }, 999),
    "filters.groupId": _.debounce(function () {
      const _this = this;
      _this.loadUsers(() => {});
    }, 999),
  },
  methods: {
    updateSearchPanel() {
      this.rightDrawer = !this.rightDrawer;
    },
    reloadTable() {
      this.loadUsers(() => {});
    },
    cancelSearch() {
      this.showSearchPanel = false;
    },
    resetFilter() {
      const _this = this;
      _this.$refs.formFilter.reset();
      _this.pagination.itemsPerPage = 10;
      _this.pagination.page = 1;
    },
    trash(user) {
      const _this = this;

      _this.$store.commit("showDialog", {
        type: "confirm",
        icon: "warning",
        title: "Confirmar Eliminacion",
        message: "¿Esta seguro en eliminar al usuario?",
        okCb: () => {
          axios
            .delete("/admin/users/" + user.id)
            .then(function (response) {
              _this.$store.commit("showSnackbar", {
                message: response.data.message,
                color: "success",
                duration: 3000,
              });

              _this.$eventBus.$emit("USER_DELETED");
            })
            .catch(function (error) {
              _this.$store.commit("hideLoader");

              if (error.response) {
                _this.$store.commit("showSnackbar", {
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
    showDialog(dialog, data) {
      const _this = this;

      switch (dialog) {
        case "user_permissions":
          _this.dialogs.showPermissions.items = data;
          setTimeout(() => {
            _this.dialogs.showPermissions.show = true;
          }, 500);
          break;
      }
    },
    async loadUsers(cb) {
      const _this = this;

      let params = {
        ..._this.filters,
        search: _this.search,
        group_id: _this.filters.groupId.join(","),
        order_sort: _this.pagination.sortDesc[0] ? "desc" : "asc",
        order_by: _this.pagination.sortBy[0] || "name",
        page: _this.pagination.page,
        per_page: _this.pagination.itemsPerPage,
      };

      await axios
        .get("/admin/users", { params: params })
        .then(function (response) {
          _this.items = response.data.data.data;
          _this.totalItems = response.data.data.total;
          _this.pagination.totalItems = response.data.data.total;
          (cb || Function)();
        });
    },
    loadGroups(cb) {
      const _this = this;

      let params = {
        paginate: "no",
      };

      axios.get("/admin/groups", { params: params }).then(function (response) {
        _this.filters.groupOptions = response.data.data;
        cb();
      });
    },
  },
};
</script>
