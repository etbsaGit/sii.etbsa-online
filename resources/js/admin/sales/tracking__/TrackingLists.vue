<template>
  <v-card flat color="trasnparent">
    <v-navigation-drawer v-model="drawer" width="250" touchless absolute>
      <v-sheet
        color="grey lighten-4"
        class="d-flex pa-4 justify-left align-center title"
      >
        <v-icon left>mdi-tune</v-icon> Filtros
      </v-sheet>
      <v-divider></v-divider>
      <tracking-form-filter></tracking-form-filter>
    </v-navigation-drawer>
    <v-card
      :style="`margin-left: ${
        drawer && !$vuetify.breakpoint.mobile ? '250px;' : ''
      } `"
      min-height="400"
      color="grey lighten-3"
      flat
    >
      <v-app-bar flat>
        <v-app-bar-nav-icon @click="drawer = !drawer"></v-app-bar-nav-icon>
        <v-icon left>mdi-filter-outline</v-icon> Leads
        <v-spacer />
        <v-btn rounded dark @click="dialogs.create.show = true">
          Crear Lead
        </v-btn>
      </v-app-bar>
      <v-data-table
        v-model="selected"
        show-select
        :headers="headers"
        :items="leads"
        sort-by="calories"
        item-key="id"
        fixed-header
        height="55vh"
        show-expand
        :expanded.sync="expanded"
        dense
      >
        <template #top>
          <v-toolbar flat dense color="white">
            <v-spacer></v-spacer>
            <v-btn color="grey lighten-1" dark class="mb-2 mr-2" text>
              <v-icon left>mdi-upload</v-icon> Excel export</v-btn
            >
          </v-toolbar>
        </template>
        <template #[`item.reference`]="{ item }">
          <div class="d-flex flex-column">
            <span
              class="d-block font-weight-bold text--primary text-wrap"
              style="min-width: 200px;"
            >
              {{ item.reference }}
            </span>
            <small>{{ item.title }}</small>
          </div>
        </template>
        <template #[`item.estatus.title`]="{ value }">
          <v-chip color="purple" small dense dark>{{ value }}</v-chip>
        </template>

        <template #[`item.action`]="{ item }">
          <v-menu left offset-x transition="slide-x-transition" rounded="l-xl">
            <template v-slot:activator="{ on, attrs }">
              <v-btn icon v-bind="attrs" v-on="on">
                <v-icon>mdi-dots-vertical</v-icon>
              </v-btn>
            </template>

            <v-list rounded dense>
              <v-list-item-group>
                <v-list-item>
                  <v-list-item-title>
                    <v-icon color="blue" left>
                      mdi-file-clock-outline
                    </v-icon>
                    Ver Detalle {{ item.id }}
                  </v-list-item-title>
                </v-list-item>
                <v-list-item>
                  <v-list-item-title>
                    <v-icon color="green" left>
                      mdi-pencil
                    </v-icon>
                    Editar {{ item.id }}
                  </v-list-item-title>
                </v-list-item>
                <v-list-item>
                  <v-list-item-title>
                    <v-icon color="red" left>
                      mdi-trash-can
                    </v-icon>
                    Eliminar {{ item.id }}
                  </v-list-item-title>
                </v-list-item>
              </v-list-item-group>
            </v-list>
          </v-menu>
        </template>
        <template #no-data>
          <v-btn color="primary" @click="initialize">Reset</v-btn>
        </template>
        <template v-slot:expanded-item="{ headers, item }">
          <td :colspan="headers.length" class="pa-0">
            <v-container :colspan="headers.length" fluid>
              <v-row>
                <v-col cols="6">
                  <v-card min-height="150">
                    <v-card-text>
                      <div>Vendedor</div>
                      <p class="text-h6 text--primary">
                        {{
                          !!item.attended.profiable
                            ? item.attended.profiable.full_name
                            : item.attended.name
                        }}
                      </p>
                      <p>Motivo</p>
                      <div class="text--primary">
                        {{ item.description_topic }}
                      </div>
                    </v-card-text>
                  </v-card>
                </v-col>
                <v-col cols="6">
                  <v-card height="150" color="grey lighten-3">
                    {{ item.id }}
                  </v-card>
                </v-col>
              </v-row>
            </v-container>
          </td>

          <!-- <td :colspan="headers.length" class="pa-0">
            <v-card flat>
              <v-card-text>
                <div>Vendedor: {{ item.assigned.name }}</div>
                <div>Sucursal: {{ item.agency.title }}</div>
                <div>Departamento: {{ item.department.title }}</div>
              </v-card-text>
            </v-card>
          </td> -->

          <!-- <td :colspan="headers.length - 2"> -->
          <!-- <td v-for="(header, index) in headers" :key="index">
            <span v-if="header.value == 'id'"> {{ item.id }} </span>
            <span v-if="header.value == 'reference'">
              {{ item.reference }}
            </span>
          </td> -->
          <!-- </td> -->
        </template>
      </v-data-table>
    </v-card>

    <v-dialog v-model="dialogs.create.show" max-width="500">
      <v-card height="500"></v-card>
    </v-dialog>
  </v-card>
</template>
<script>
import TrackingFormFilter from "./TrackingFormFilter.vue";
export default {
  components: { TrackingFormFilter },
  name: "trackingLists",
  data() {
    return {
      drawer: null,
      expanded: [],
      singleSelect: false,
      selected: [],
      leads: [],
      dialogs: {
        create: {
          show: false,
        },
        edit: {
          show: false,
          id: null,
        },
      },
      headers: [
        { text: "FOLIO", value: "id", align: "left" },
        { text: "REFERENCIA", value: "reference", align: "left" },
        { text: "PROSPECTO", value: "prospect.full_name" },
        { text: "PROGRESS", value: "assertiveness" },
        { text: "ESTATUS", value: "estatus.title", align: "left" },
        { text: "", value: "action", sortable: false },
      ],
    };
  },
  created() {
    this.initialize();
  },
  methods: {
    async initialize() {
      const _this = this;

      let params = {
        // ..._this.filters,
        // sellers: _this.filters.sellers.join(","),
        // prospect: _this.filters.prospect.join(","),
        // agencies: _this.filters.agencies.join(","),
        // departments: _this.filters.departments.join(","),
        // dates: _this.dateRangeText,
        // order_sort: _this.pagination.sortDesc[0] ? "asc" : "desc",
        // order_by: _this.pagination.sortBy[0] || "id",
        // page: _this.pagination.page,
        // per_page: _this.pagination.itemsPerPage,
      };

      let { data } = await axios.get("/admin/tracking", { params: params });
      _this.leads = data.data.data;
    },
  },
};
</script>
<style></style>
