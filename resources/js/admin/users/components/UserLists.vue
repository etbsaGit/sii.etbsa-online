<template>
  <div class="component-wrap">
    <!-- search -->
    <v-card class="pt-2">
      <div class="d-flex flex-md-row flex-sm-column flex-wrap align-center">
        <v-btn
          @click="$router.push({ name: 'users.create' })"
          class="primary lighten-1 flex-grow-1 ma-1"
          dark
        >
          Usuario Nuevo
          <v-icon right dark>mdi-plus</v-icon>
        </v-btn>

        <v-btn
          @click="$router.push({ name: 'users.groups.list' })"
          class="primary lighten-1 float-right flex-grow-1 ma-1"
          dark
        >
          Administrar Grupos <v-icon right dark>mdi-account-multiple</v-icon>
        </v-btn>
        <v-btn
          @click="$router.push({ name: 'users.permissions.list' })"
          class="primary lighten-1 float-right mr-2 flex-grow-1 ma-1"
          dark
        >
          Administra Permisos <v-icon right dark>mdi-key</v-icon>
        </v-btn>
        <v-btn
          @click="$router.push({ name: 'sellers.list' })"
          class="orange lighten-1 float-right mr-2 flex-grow-1 ma-1"
          dark
        >
          Administrar Vendedores <v-icon right dark>mdi-account</v-icon>
        </v-btn>
      </div>
      <div class="d-flex flex-lg-row flex-sm-column flex-wrap align-center">
        <div class="flex-grow-1 pa-2">
          <v-text-field
            filled
            prepend-icon="mdi-magnify"
            label="Filtrar por Nombre"
            v-model="filters.name"
          ></v-text-field>
        </div>
        <div class="flex-grow-1 pa-2">
          <v-text-field
            filled
            prepend-icon="mdi-magnify"
            label="Filtrar por Email"
            v-model="filters.email"
          ></v-text-field>
        </div>
        <div class="flex-grow-1 pa-2">
          <v-autocomplete
            filled
            multiple
            chips
            deletable-chips
            clearable
            prepend-icon="mdi-filter-variant"
            label="Filtrar por Grupos"
            :items="filters.groupOptions"
            item-text="name"
            item-value="id"
            v-model="filters.groupId"
          ></v-autocomplete>
        </div>
      </div>
    </v-card>
    <!-- /search -->
    <v-divider class="pb-2" />

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
      <!-- Headers -->
      <template v-slot:[`header.name`]="{ header }" class="align-center">
        <v-icon small>mdi-account</v-icon>{{ header.text }}
      </template>
      <template v-slot:[`header.email`]="{ header }" class="align-center">
        <v-icon small>mdi-email</v-icon>{{ header.text }}
      </template>
      <template v-slot:[`header.permissions`]="{ header }" class="align-center">
        <v-icon small>mdi-key</v-icon>{{ header.text }}
      </template>
      <template v-slot:[`header.groups`]="{ header }" class="align-center">
        <v-icon small>mdi-account-multiple</v-icon>{{ header.text }}
      </template>
      <template v-slot:[`header.last_login`]="{ header }" class="align-center">
        <v-icon small>mdi-av-timer</v-icon>{{ header.text }}
      </template>

      <!-- Body  -->
      <template v-slot:[`item.action`]="{ item }">
        <v-btn
          @click="
            $router.push({
              name: 'users.edit',
              params: { id: item.id }
            })
          "
          x-small
          outlined
          icon
          color="info"
        >
          <v-icon small>mdi-pencil</v-icon>
        </v-btn>
        <v-btn @click="trash(item)" x-small outlined icon color="red">
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
          dark
          >Mostrar</v-btn
        >
      </template>
      <template v-slot:[`item.groups`]="{ item }">
        <v-chip
          v-for="group in item.groups"
          :key="group.id"
          outlined
          color="secondary"
          text-color="accent"
        >
          {{ group.name }}
        </v-chip>
      </template>
      <template v-slot:[`item.last_login`]="{ item }">
        {{ $appFormatters.formatDate(item.last_login) }}
      </template>
      <template v-slot:[`item.active`]="{ item }">
        <v-avatar outlined>
          <v-icon v-if="item.active != null" class="green--text"
            >mdi-check-circle-outline</v-icon
          >
          <v-icon class="grey--text" v-else>mdi-alert-circle-outline</v-icon>
        </v-avatar>
      </template>
    </v-data-table>

    <v-divider class="py-5" />

    <!-- dialog for show permissions -->
    <v-dialog v-model="dialogs.showPermissions.show" absolute max-width="300px">
      <v-card>
        <v-card-title>
          <div class="headline"><v-icon>vpn_key</v-icon> User Permissions</div>
        </v-card-title>
        <v-card-text>
          <v-chip
            v-for="(permission, key) in dialogs.showPermissions.items"
            :key="key"
            class="white--text ma-1"
            :class="{
              green: permission.value == 1,
              red: permission.value == -1,
              blue: permission.value == 0
            }"
          >
            <v-avatar
              v-if="permission.value == -1"
              class="red darken-4"
              title="Deny"
            >
              <v-icon>block</v-icon>
            </v-avatar>
            <v-avatar
              v-if="permission.value == 1"
              class="green darken-4"
              title="Allow"
            >
              <v-icon>check_circle</v-icon>
            </v-avatar>
            <v-avatar
              v-if="permission.value == 0"
              class="blue darken-4"
              title="Inherit"
            >
              <v-icon>swap_horiz</v-icon>
            </v-avatar>
            {{ permission.title }}
          </v-chip>
          <p v-if="dialogs.showPermissions.items.length == 0">No permissions</p>
        </v-card-text>
      </v-card>
    </v-dialog>
  </div>
</template>

<script>
export default {
  data() {
    return {
      headers: [
        {
          text: "Accion",
          value: "action",
          align: "center",
          sortable: false
        },
        {
          text: "Nombre",
          value: "name",
          align: "left",
          sortable: true
        },
        { text: "Email", value: "email", align: "left", sortable: false },
        {
          text: "Permisos",
          value: "permissions",
          align: "center",
          sortable: false
        },
        { text: "Grupos", value: "groups", align: "center", sortable: false },
        {
          text: "Ultimo inicio",
          value: "last_login",
          align: "right",
          sortable: true
        },
        {
          text: "Activo",
          value: "active",
          align: "center",
          width: 10,
          sortable: false
        }
      ],
      items: [],
      totalItems: 0,
      pagination: {
        itemsPerPage: 10
      },

      filters: {
        name: "",
        email: "",
        groupId: [],
        groupOptions: []
      },

      dialogs: {
        showPermissions: {
          items: [],
          show: false
        }
      }
    };
  },
  mounted() {
    const self = this;

    self.loadGroups(() => {});

    self.$eventBus.$on(
      ["USER_ADDED", "USER_UPDATED", "USER_DELETED", "GROUP_ADDED"],
      () => {
        self.loadUsers(() => {});
      }
    );

    self.$store.commit("setBreadcrumbs", [
      { label: "Users", to: { name: "users.list" } }
    ]);
  },
  watch: {
    pagination: {
      handler() {
        this.loadUsers(() => {});
      },
      deep: true
    },
    "filters.name": _.debounce(function() {
      const self = this;
      self.loadUsers(() => {});
    }, 700),
    "filters.email": _.debounce(function() {
      const self = this;
      self.loadUsers(() => {});
    }, 700),
    "filters.groupId": _.debounce(function() {
      const self = this;
      self.loadUsers(() => {});
    }, 700)
  },
  methods: {
    trash(user) {
      const self = this;

      self.$store.commit("showDialog", {
        type: "confirm",
        icon: "warning",
        title: "Confirmar Eliminacion",
        message: "¿Esta seguro en eliminar al usuario?",
        okCb: () => {
          axios
            .delete("/admin/users/" + user.id)
            .then(function(response) {
              self.$store.commit("showSnackbar", {
                message: response.data.message,
                color: "success",
                duration: 3000
              });

              self.$eventBus.$emit("USER_DELETED");
            })
            .catch(function(error) {
              self.$store.commit("hideLoader");

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
    },
    showDialog(dialog, data) {
      const self = this;

      switch (dialog) {
        case "user_permissions":
          self.dialogs.showPermissions.items = data;
          setTimeout(() => {
            self.dialogs.showPermissions.show = true;
          }, 500);
          break;
      }
    },
    loadUsers(cb) {
      const self = this;

      let params = {
        name: self.filters.name,
        email: self.filters.email,
        group_id: self.filters.groupId.join(","),
        order_sort: self.pagination.sortDesc[0] ? "desc" : "asc",
        order_by: self.pagination.sortBy[0] || "name",
        page: self.pagination.page,
        per_page: self.pagination.itemsPerPage
      };

      axios.get("/admin/users", { params: params }).then(function(response) {
        self.items = response.data.data.data;
        self.totalItems = response.data.data.total;
        self.pagination.totalItems = response.data.data.total;
        (cb || Function)();
      });
    },
    loadGroups(cb) {
      const self = this;

      let params = {
        paginate: "no"
      };

      axios.get("/admin/groups", { params: params }).then(function(response) {
        self.filters.groupOptions = response.data.data;
        cb();
      });
    }
  }
};
</script>
