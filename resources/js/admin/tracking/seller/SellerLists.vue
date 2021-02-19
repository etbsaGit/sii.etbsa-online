<template>
  <div class="component-wrap">
    <!-- search -->
    <v-card flat>
      <div class="d-flex flex-row align-center">
        <div class="flex-grow-1 pa-2">
          <v-text-field
            v-model="filters.title"
            prepend-icon="mdi-magnify"
            label="Fitrar por Nombre"
            clearable
            outlined
          ></v-text-field>
        </div>
        <div class="flex-grow-1 pa-2">
          <v-text-field
            v-model="filters.email"
            prepend-icon="mdi-magnify"
            label="Fitrar por Email"
            clearable
            outlined
          ></v-text-field>
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
                    name: 'sellers.edit',
                    params: { propSellerId: item.id },
                  })
                "
              >
                <v-list-item-icon>
                  <v-icon class="blue--text">mdi-information-outline</v-icon>
                </v-list-item-icon>
                <v-list-item-content>
                  <v-list-item-title>Configurar Vendedor</v-list-item-title>
                </v-list-item-content>
              </v-list-item>
            </v-list-item-group>
          </v-list>
        </v-menu>
      </template>
      <template v-slot:[`item.seller_type`]="{ item }">
        <v-dialog width="500">
          <template v-slot:activator="{ on, attrs }">
            <v-btn
              x-small
              rounded
              color="grey lighten-2"
              v-bind="attrs"
              v-on="on"
            >
              Mostrar
            </v-btn>
          </template>

          <v-card>
            <v-card-title>
              <div class="headline">
                <v-icon>mdi-cog</v-icon> Configuracion Vendedor
              </div>
            </v-card-title>
            <v-card-text>
              <div>Agencias:</div>
              <v-chip
                v-for="(type, key) in item.seller_agency"
                :key="`suc-${key}`"
                class="ma-1"
              >
                {{ type.title }}
              </v-chip>
              <div>Departamentos:</div>
              <v-chip
                v-for="(type, key) in item.seller_type"
                :key="`depto-${key}`"
                class="ma-1"
              >
                {{ type.title }}
              </v-chip>
            </v-card-text>
          </v-card>
        </v-dialog>
      </template>
      <template v-slot:[`item.groups`]="{ item }">
        <v-avatar outlined>
          <v-icon
            v-if="item.groups.some((g) => g.name == 'Gerente')"
            class="green--text"
            >mdi-check-circle-outline</v-icon
          >
          <v-icon v-else class="grey--text">mdi-alert-circle-outline</v-icon>
        </v-avatar>
      </template>
      <template v-slot:[`item.updated_at`]="{ item }">
        {{ $appFormatters.formatDate(item.updated_at, 'MMM DD,YYYY') }}
      </template>
    </v-data-table>
  </div>
</template>

<script>
export default {
  data() {
    return {
      headers: [
        { text: 'Action', value: 'action', align: 'left', sortable: false },
        {
          text: 'Clave Vendedor',
          value: 'seller_key',
          align: 'left',
          sortable: false,
        },
        { text: 'Nombre', value: 'name', align: 'left', sortable: false },
        { text: 'Email', value: 'email', align: 'left', sortable: false },
        {
          text: 'Configuracion:',
          value: 'seller_type',
          align: 'center',
          sortable: false,
        },
        {
          text: 'Es Gerente:',
          value: 'groups',
          align: 'center',
          sortable: false,
        },
        {
          text: 'Ultimo Cambio',
          value: 'updated_at',
          align: 'right',
          sortable: false,
        },
      ],
      items: [],
      totalItems: 0,
      pagination: {
        rowsPerPage: 10,
      },

      filters: {
        title: '',
      },
    };
  },
  mounted() {
    const self = this;
    self.$store.commit('setBreadcrumbs', [{ label: 'Vendedores', name: '' }]);
  },
  watch: {
    'pagination.page': function() {
      this.loadSellers(() => {});
    },
    'pagination.rowsPerPage': function() {
      this.loadSellers(() => {});
    },
    'filters.title': _.debounce(function() {
      const self = this;
      self.loadSellers(() => {});
    }, 700),
    'filters.email': _.debounce(function() {
      const self = this;
      self.loadSellers(() => {});
    }, 700),
  },
  methods: {
    trash(seller) {
      const self = this;

      self.$store.commit('showDialog', {
        type: 'confirm',
        title: 'Confirm Deletion',
        message: 'Are you sure you want to delete this seller?',
        okCb: () => {
          axios
            .delete('/admin/sellers/' + seller.id)
            .then(function(response) {
              self.$store.commit('showSnackbar', {
                message: response.data.message,
                color: 'success',
                duration: 3000,
              });

              self.loadSellers(() => {});
            })
            .catch(function(error) {
              self.$store.commit('hideLoader');

              if (error.response) {
                self.$store.commit('showSnackbar', {
                  message: error.response.data.message,
                  color: 'error',
                  duration: 3000,
                });
              } else if (error.request) {
                console.log(error.request);
              } else {
                console.log('Error', error.message);
              }
            });
        },
        cancelCb: () => {
          console.log('CANCEL');
        },
      });
    },
    loadSellers(cb) {
      const self = this;

      let params = {
        name: self.filters.title,
        email: self.filters.email,
        page: self.pagination.page,
        per_page: self.pagination.rowsPerPage,
      };

      axios.get('/admin/sellers', { params: params }).then(function(response) {
        self.items = response.data.data.data;
        self.totalItems = response.data.data.total;
        self.pagination.totalItems = response.data.data.total;
        (cb || Function)();
      });
    },
  },
};
</script>
