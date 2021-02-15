<template>
  <div class="component-wrap">
    <!-- search -->
    <v-card flat class="py-4 px-2 d-flex flex-row align-center">
      <v-text-field
        v-model="filters.full_name"
        prepend-icon="mdi-magnify"
        label="Fitrar por Nombre / Telefono"
        outlined
        dense
        filled
        hide-details
        clearable
      ></v-text-field>
      <v-spacer></v-spacer>
      <v-btn
        @click="$router.push({ name: 'prospect.create' })"
        class="primary"
        dark
      >
        Registrar Prospecto
        <v-icon right>mdi-plus</v-icon>
      </v-btn>
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
          <v-list shaped>
            <v-list-item-group>
              <v-list-item
                @click="
                  $router.push({
                    name: 'prospect.edit',
                    params: { propProspectId: item.id },
                  })
                "
              >
                <v-list-item-icon>
                  <v-icon class="blue--text">mdi-pencil</v-icon>
                </v-list-item-icon>
                <v-list-item-content>
                  <v-list-item-title>Editar Prospecto</v-list-item-title>
                </v-list-item-content>
              </v-list-item>
              <v-list-item
                @click="
                  $router.push({
                    name: 'tracking.create',
                    params: { propProspectId: item.id },
                  })
                "
              >
                <v-list-item-icon>
                  <v-icon class="blue--text">mdi-information-outline</v-icon>
                </v-list-item-icon>
                <v-list-item-content>
                  <v-list-item-title>Crear Seguimiento</v-list-item-title>
                </v-list-item-content>
              </v-list-item>
            </v-list-item-group>
          </v-list>
        </v-menu>
      </template>
      <template v-slot:[`item.full_name`]="{ item }">
        <v-list-item dense class="pa-0">
          <v-list-item-content class="pa-0">
            <v-list-item-title>{{ item.full_name }}</v-list-item-title>
            <v-list-item-subtitle v-if="item.is_moral">
              {{ item.company }}
            </v-list-item-subtitle>
          </v-list-item-content>
        </v-list-item>
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
          text: 'Dirigido a / Razon Social',
          value: 'full_name',
          align: 'left',
          sortable: false,
        },
        { text: 'Telefono', value: 'phone', align: 'left', sortable: false },
        {
          text: 'Seguimientos Activos:',
          value: 'tracking_count',
          align: 'center',
          sortable: false,
        },
        {
          text: 'Proviene:',
          value: 'town',
          align: 'left',
          sortable: false,
        },
      ],
      items: [],
      totalItems: 0,
      pagination: {
        rowsPerPage: 10,
      },

      filters: {
        full_name: '',
      },
    };
  },
  mounted() {
    const self = this;
    self.$store.commit('setBreadcrumbs', [{ label: 'Prospectos', name: '' }]);
  },
  watch: {
    'pagination.page': function() {
      this.loadProspects(() => {});
    },
    'pagination.rowsPerPage': function() {
      this.loadProspects(() => {});
    },
    'filters.full_name': _.debounce(function() {
      const self = this;
      self.loadProspects(() => {});
    }, 700),
  },
  methods: {
    trash(seller) {
      const self = this;

      self.$store.commit('showDialog', {
        type: 'confirm',
        full_name: 'Confirm Deletion',
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

              self.loadProspects(() => {});
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
    loadProspects(cb) {
      const self = this;

      let filters = {
        full_name: self.filters.full_name,
        page: self.pagination.page,
        per_page: self.pagination.rowsPerPage,
      };

      axios.get('/admin/prospects', { params: filters }).then((response) => {
        self.items = response.data.data.data;
        self.totalItems = response.data.data.total;
        self.pagination.totalItems = response.data.data.total;
        (cb || Function)();
      });
    },
  },
};
</script>
