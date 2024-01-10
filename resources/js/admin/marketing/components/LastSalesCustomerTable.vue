<template >
  <v-card>
    <v-expansion-panels>
      <v-expansion-panel>
        <v-expansion-panel-header>
          <span class="title">Filtro Avanzado</span>
        </v-expansion-panel-header>
        <v-expansion-panel-content>
          <v-row
            class="d-flex align-content-start flex-wrap text-uppercase"
            color="grey lighten-2"
            no-gutters
          >
            <v-col cols="12" md="6" class="pa-2 flex-grow-1 flex-shrink-0">
              <v-combobox
                v-model="filters.clave_cliente"
                label="CLIENTE"
                :items="options.Clientes"
                placeholder="Seleccionar Cliente"
                :filter="customFilterCliente"
                item-value="Clave_Cliente"
                item-text="Clave_Cliente"
                deletable-chips
                hide-details
                small-chips
                clearable
                multiple
                outlined
                dense
                chips
              >
                <template #item="{ item }">
                  <v-list-item-content>
                    <v-list-item-subtitle>
                      {{ item.Clave_Cliente }}
                    </v-list-item-subtitle>
                    <v-list-item-title> {{ item.Cliente }} </v-list-item-title>
                  </v-list-item-content>
                </template>
              </v-combobox>
            </v-col>
            <v-col cols="12" md="6" class="pa-2 flex-grow-1 flex-shrink-0">
              <v-combobox
                v-model="filters.clave_vendedor"
                label="VENDEDOR"
                :items="options.Vendedores"
                item-text="ClaveVendedor"
                item-value="ClaveVendedor"
                placeholder="Buscar Clave Vendedor, Nombre Vendedor"
                :filter="customFilterVendedor"
                clearable
                outlined
                hide-details
                dense
                multiple
                chips
                small-chips
                deletable-chips
              >
                <template #item="{ item }">
                  <v-list-item-content>
                    <v-list-item-subtitle>
                      {{ item.ClaveVendedor }}
                    </v-list-item-subtitle>
                    <v-list-item-title> {{ item.Nombre }} </v-list-item-title>
                  </v-list-item-content>
                </template>
              </v-combobox>
            </v-col>
            <v-col cols="12" md="3" class="pa-2 flex-grow-1 flex-shrink-0">
              <!-- item-text="nombre"
              item-value="id" -->
              <v-select
                v-model="filters.sucursales"
                label="SUCURSAL"
                :items="options.Sucursales"
                placeholder="Seleccionar Sucursal"
                clearable
                outlined
                hide-details
                dense
                multiple
                chips
                small-chips
                deletable-chips
              >
                <!-- <template v-slot:selection="{ item }">
                  <v-list-item-content>
                    <v-list-item-subtitle>
                      {{ item.linea }}
                    </v-list-item-subtitle>
                    <v-list-item-title> {{ item.nombre }} </v-list-item-title>
                  </v-list-item-content>
                </template>
                <template #item="{ item }">
                  <v-list-item-content>
                    <v-list-item-subtitle>
                      {{ item.linea }}
                    </v-list-item-subtitle>
                    <v-list-item-title> {{ item.nombre }} </v-list-item-title>
                  </v-list-item-content>
                </template> -->
              </v-select>
            </v-col>
            <!--  -->
            <!-- <v-col cols="12" md="3" class="pa-2 flex-grow-1 flex-shrink-0">
              <v-select
                v-model="filters.lineas"
                label="Linea"
                :items="options.Linea"
                item-text="linea"
                item-value="linea"
                placeholder="Seleccionar Linea"
                clearable
                outlined
                hide-details
                dense
                multiple
                chips
                deletable-chips
                small-chips
              >
              </v-select>
            </v-col> -->
            <v-col cols="12" md="2" class="pa-2 flex-grow-1 flex-shrink-0">
              <v-select
                v-model="filters.years"
                label="AÑO(s)"
                :items="OptionsYears"
                item-text="year"
                item-value="year"
                placeholder="Seleccionar Año"
                clearable
                outlined
                hide-details
                dense
                multiple
                chips
                deletable-chips
                small-chips
              >
              </v-select>
            </v-col>
            <!-- <v-col cols="12" md="2" class="pa-2 flex-grow-1 flex-shrink-0">
              <v-select
                v-model="filters.currency"
                label="MONEDA"
                :items="options.Moneda"
                item-text="MONEDA"
                item-value="MONEDA"
                placeholder="Seleccionar Moneda"
                clearable
                outlined
                hide-details
                dense
                multiple
                chips
                deletable-chips
                small-chips
              >
              </v-select>
            </v-col> -->
            <!-- <v-col cols="12" md="2" class="pa-2 flex-grow-1 flex-shrink-0">
              <v-select
                v-model="filters.tipo_venta"
                label="TIPO DE VENTA"
                :items="options.TipoVenta"
                item-text="descripcion"
                item-value="clave"
                placeholder="Seleccionar Tipo de Venta"
                clearable
                outlined
                hide-details
                dense
                multiple
                chips
                deletable-chips
              >
                <template v-slot:selection="{ item }">
                  <v-list-item-content class="d-flex">
                    <v-list-item-subtitle>
                      {{ item.clave }}
                    </v-list-item-subtitle>
                    <v-list-item-title>
                      {{ item.descripcion }}
                    </v-list-item-title>
                  </v-list-item-content>
                </template>
                <template #item="{ item }">
                  <v-list-item-content>
                    <v-list-item-subtitle>
                      {{ item.clave }}
                    </v-list-item-subtitle>
                    <v-list-item-title>
                      {{ item.descripcion }}
                    </v-list-item-title>
                  </v-list-item-content>
                </template>
              </v-select>
            </v-col> -->
            <!-- <v-col cols="12" md="3" class="pa-2 flex-grow-1 flex-shrink-0">
              <v-combobox
                v-model="filters.municipio"
                label="Municipio"
                :items="options.Municipio"
                placeholder="Buscar Municipio"
                clearable
                outlined
                hide-details
                dense
                multiple
                chips
                deletable-chips
                small-chips
              >
              </v-combobox>
            </v-col> -->
            <!-- <v-col cols="12" md="3" class="pa-2 flex-grow-1 flex-shrink-0">
              <v-combobox
                v-model="filters.estado"
                label="Estado"
                :items="options.Estado"
                placeholder="Buscar Estado"
                clearable
                outlined
                hide-details
                dense
                multiple
                chips
                deletable-chips
                small-chips
              >
              </v-combobox>
            </v-col> -->
          </v-row>
        </v-expansion-panel-content>
      </v-expansion-panel>
    </v-expansion-panels>

    <v-data-table
      :headers="headers"
      :items="items"
      :options.sync="pagination"
      :server-items-length="totalItems"
      fixed-header
      caption
      dense
      class="elevation-3"
      calculate-widths
      :footer-props="{
        showCurrentPage: true,
        showFirstLastPage: true,
        firstIcon: 'mdi-arrow-collapse-left',
        lastIcon: 'mdi-arrow-collapse-right',
        prevIcon: 'mdi-minus',
        nextIcon: 'mdi-plus',
        itemsPerPageOptions: [5, 10, 15, 100, 200, 500, 2000, 5000],
      }"
      single-expand
      :expanded.sync="expanded"
      item-key="cliente"
      show-expand
      @update:expanded="
        () => {
          invoicesByCustomer = [];
          trackingsByCustomer = [];
        }
      "
    >
      <template #top>
        <v-toolbar flat>
          <v-toolbar-title>Asignar y Ultima Compra Clientes</v-toolbar-title>
          <v-spacer />

          <v-text-field
            v-model="search"
            label="Buscar"
            placeholder="Nombre Producto, Serie Producto, MODELO"
            persistent-placeholder
            outlined
            hide-details
            clearable
            dense
          />
          <v-btn icon @click="cleanFilter">
            <v-icon color="red">mdi-filter-remove-outline</v-icon>
          </v-btn>
          <v-btn @click="searchBtn()" dark>
            Buscar
            <v-icon right color="blue">mdi-magnify</v-icon>
          </v-btn>
        </v-toolbar>
      </template>
      <template v-slot:[`item.sucursal`]="{ item }">
        <div class="caption text--secondary">
          {{ item.linea }}
        </div>
        <div class="text-uppercase font-weight-bold">{{ item.sucursal }}</div>
      </template>
      <template v-slot:[`item.cliente`]="{ item }">
        <div class="caption text--secondary">
          Clave: {{ item.clave_cliente }}
        </div>
        <div class="d-flex flex-wrap text-wrap text-uppercase font-weight-bold">
          {{ item.cliente }}
        </div>
      </template>
      <template v-slot:[`item.addresses`]="{ value }">
        <div class="d-flex flex-wrap text-wrap caption text--secondary">
           {{ value }}
        </div>
      </template>

      <!-- <template v-slot:[`item.nombre_vendedor`]="{ item }">
        <div class="caption text--secondary">
          Clave: {{ item.clave_vendedor }}
        </div>
        <div class="text-uppercase font-weight-bold">
          {{ item.nombre_vendedor }}
        </div>
      </template> -->
      <!-- <template v-slot:[`item.tipo_venta`]="{ item }">
        <div class="caption text--secondary">
          <v-chip x-small dark color="green darken-4" label outlined>
            {{ `${item.tipo_venta}-${item.tipo_venta_nombre}` }}
          </v-chip>
        </div>
        <div class="text-uppercase font-weight-bold">
          {{ item.producto }}
        </div>
      </template> -->

      <!-- <template v-slot:[`item.NIP`]="{ value }">
        <div style="max-width: 200px">
          <v-chip label outlined color="black" class="font-weight-bold" dark>
            {{ value }}
          </v-chip>
        </div>
      </template> -->
      <!-- <template v-slot:[`item.invoice_date`]="{ value }">
        <v-chip label outlined color="blue" class="font-weight-bold" dark>
          {{ value }}
        </v-chip>
      </template> -->
      <!-- <template v-slot:[`item.precio_venta`]="{ value }">
        <v-chip outlined label color="primary" class="font-weight-bold" dark>
          {{ value | currency }} MX
        </v-chip>
      </template> -->

      <!-- <template v-slot:expanded-item="{ headers, item }"> -->
      <template v-slot:expanded-item="{ headers, item }">
        <td :colspan="headers.length" class="pa-0">
          <v-card-text>
            <v-row>
              <v-col>
                <v-dialog v-model="dialog" max-width="600px">
                  <template v-slot:activator="{ on, attrs }">
                    <v-btn color="primary" block dark v-bind="attrs" v-on="on">
                      Asignar y Crear un Nuevo Seguimiento
                    </v-btn>
                  </template>
                  <v-card>
                    <v-card-title>
                      <span class="text-h5">
                        <div>Crear Nuevo Seguimiento para</div>
                        <div>{{ item.cliente }}</div>
                      </span>
                    </v-card-title>
                    <v-card-text>
                      <v-form ref="formCreate" v-model="valid" lazy-validation>
                        <v-container>
                          <span class="text-h5"> Asignar Vendedor: </span>
                          <v-row>
                            <v-col cols="12" sm="6">
                              <p class="text-14 mb-1">Sucursal</p>
                              <v-select
                                v-model="form.agency_id"
                                :items="optionsForm.agencies"
                                item-text="title"
                                item-value="id"
                                placeholder="Seleccionar"
                                :rules="[(v) => !!v || 'Es Requerido']"
                                hide-details
                                outlined
                                filled
                                dense
                              ></v-select>
                            </v-col>
                            <v-col cols="12" sm="6">
                              <p class="text-14 mb-1">Departamento</p>
                              <v-select
                                v-model="form.department_id"
                                :items="optionsForm.departments"
                                item-text="title"
                                item-value="id"
                                placeholder="Seleccionar"
                                :rules="[(v) => !!v || 'Es Requerido']"
                                hide-details
                                outlined
                                filled
                                dense
                              ></v-select>
                            </v-col>
                            <v-col>
                              <p class="text-14 mb-1">Vendedor Asigando</p>
                              <v-autocomplete
                                v-model="form.attended_by"
                                :disabled="availableSeller"
                                :items="optionsForm.sellers"
                                item-text="name"
                                item-value="id"
                                placeholder="Seleccionar"
                                :rules="[(v) => !!v || 'Es Requerido']"
                                hide-details
                                outlined
                                filled
                                dense
                              ></v-autocomplete>
                            </v-col>
                          </v-row>
                        </v-container>
                      </v-form>
                      <small>*indicates required field</small>
                    </v-card-text>
                    <v-card-actions>
                      <v-spacer></v-spacer>
                      <v-btn color="blue darken-1" text @click="dialog = false">
                        Cerrar
                      </v-btn>
                      <v-btn
                        color="blue darken-1"
                        text
                        @click="createNewTracking()"
                      >
                        Asignar
                      </v-btn>
                    </v-card-actions>
                  </v-card>
                </v-dialog>
              </v-col>
            </v-row>
            <v-row dense>
              <v-col cols="12" md="6">
                <v-simple-table dense height="180px" fixed-header>
                  <template v-slot:default>
                    <caption>
                      Ultimas Ventas
                    </caption>
                    <thead>
                      <tr>
                        <th class="text-left">Sucursal</th>
                        <th class="text-left">Producto</th>
                        <th class="text-left">Vendedor</th>
                        <th class="text-left">Precio Venta</th>
                        <th class="text-left">Fecha</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr
                        v-for="invoice in invoicesByCustomer"
                        :key="invoice.NIP"
                      >
                        <td>
                          <div class="caption text--secondary">
                            {{ invoice.linea }}
                          </div>
                          <div class="text-uppercase font-weight-bold">
                            {{ invoice.sucursal }}
                          </div>
                        </td>
                        <td>
                          <div class="caption text--secondary">
                            <v-chip
                              x-small
                              dark
                              color="green darken-4"
                              label
                              outlined
                            >
                              {{
                                `${invoice.tipo_venta}-${invoice.tipo_venta_nombre}`
                              }}
                            </v-chip>
                          </div>
                          <div class="text-uppercase font-weight-bold">
                            {{ invoice.producto }}
                          </div>
                        </td>
                        <td>
                          <div class="caption text--secondary">
                            Clave: {{ invoice.clave_vendedor }}
                          </div>
                          <div class="text-uppercase font-weight-bold">
                            {{ invoice.nombre_vendedor }}
                          </div>
                        </td>
                        <td>
                          <v-chip
                            outlined
                            label
                            color="primary"
                            class="font-weight-bold"
                            dark
                          >
                            {{ invoice.precio_venta | currency }} MX
                          </v-chip>
                        </td>
                        <td>
                          <v-chip
                            outlined
                            label
                            color="primary"
                            class="font-weight-bold"
                            dark
                          >
                            {{ invoice.invoice_date }}
                          </v-chip>
                        </td>
                      </tr>
                    </tbody>
                  </template>
                </v-simple-table>
              </v-col>
              <v-col cols="12" md="6">
                <v-simple-table dense height="180px" fixed-header>
                  <template v-slot:default>
                    <caption>
                      Ultimo Seguimiento
                    </caption>
                    <thead>
                      <tr>
                        <th class="text-left">Folio Seguimiento</th>
                        <th class="text-left">Titulo:</th>
                        <th class="text-left">Atendido Por:</th>
                        <th class="text-left">Estatus:</th>
                        <th class="text-left">Fecha:</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr
                        v-for="tracking in trackingsByCustomer"
                        :key="tracking.id"
                      >
                        <td>
                          <v-btn
                            text
                            color="primary"
                            @click="
                              (dialogs.id = tracking.id), (dialogs.show = true)
                            "
                          >
                            {{ tracking.id }}
                            <v-icon right>mdi-eye</v-icon>
                          </v-btn>
                        </td>
                        <td>
                          <div class="d-flex flex-column">
                            <span
                              class="d-block font-weight-bold text--primary text-wrap"
                              style="min-width: 200px"
                            >
                              {{ tracking.reference }}
                            </span>
                            <small>{{ tracking.title }}</small>
                          </div>
                        </td>
                        <td>{{ tracking.attended.name }}</td>
                        <td>{{ tracking.estatus.title }}</td>
                        <td>
                          {{
                            $appFormatters.formatDate(tracking.updated_at, "L")
                          }}
                        </td>
                        <td></td>
                      </tr>
                    </tbody>
                  </template>
                </v-simple-table>
              </v-col>
            </v-row>
          </v-card-text>
        </td>
      </template>
    </v-data-table>

    <dialog-component
      :show="dialogs.show"
      @close="(dialogs.show = false), (dialogs.id = null)"
      title="Detalle Seguimiento"
      fullscreen
      closeable
    >
      <tracking-prospect
        v-if="dialogs.show && dialogs.id"
        :propTrackingId="dialogs.id"
      ></tracking-prospect>
    </dialog-component>
  </v-card>
</template>
<script>
import { mapGetters } from "vuex";
import DialogComponent from "@admin/components/DialogComponent.vue";
import TrackingProspect from "@admin/sales/tracking/TrackingProspect.vue";
export default {
  name: "LastSalesCustomerHistory",
  components: {
    TrackingProspect,
    DialogComponent,
  },

  mounted() {
    const _this = this;
    _this.$store.commit("setBreadcrumbs", [
      { label: "Historial Ventas Clientes", name: "" },
    ]);
    _this.getOptions();
    _this.form.attended_by = _this.user_id;
    _this.form.seller_id = _this.user_id;
  },
  data() {
    return {
      valid: true,
      dialogs: {
        show: false,
        id: null,
      },
      dialog: false,
      expanded: [],
      dialog: false,
      form: {
        prospect_name: null,
        agency_id: null,
        department_id: null,
        seller_id: null,
        attended_by: null,
      },
      headers: [
        {
          text: "Cliente",
          align: "start",
          filterable: false,
          value: "cliente",
          sortable: false,
        },
        {
          text: "RFC",
          align: "start",
          filterable: false,
          value: "rfc_cliente",
          sortable: false,
        },
        {
          text: "Domicilio",
          align: "start",
          filterable: false,
          value: "addresses",
          sortable: false,
        },
        {
          text: "Telefonos",
          align: "start",
          filterable: false,
          value: "phones",
          sortable: false,
        },
      ],
      search: "",
      searchCustomer: "",
      items: [],
      sumTotalVentasAg: 0,
      sumTotalVentasAgMX: 0,
      invoicesByCustomer: [],
      trackingsByCustomer: [],

      totalItems: 0,
      pagination: {
        itemsPerPage: 10,
        page: 1,
      },
      filters: {
        clave_cliente: [],
        clave_vendedor: [],
        sucursales: [],
        lineas: [],
        years: [],
        currency: [],
        tipo_venta: [],
        municipio: [],
        estado: [],
      },
      options: {},
      optionsForm: {
        agencies: [],
        department: [],
        sellers: [],
      },
    };
  },
  computed: {
    ...mapGetters("user", ["user_id"]),
    availableSeller() {
      const self = this;
      if (self.form.agency_id && self.form.department_id) {
        return false;
      } else {
        return true;
      }
    },
    FilterClienteReduce() {
      return this.filters.clave_cliente.reduce((result, item) => {
        typeof item === "object" && item.hasOwnProperty("Clave_Cliente")
          ? result.push(item.Clave_Cliente)
          : result.push(item);
        return result;
      }, []);
    },
    FilterVendedorReduce() {
      return this.filters.clave_vendedor.reduce((result, item) => {
        typeof item === "object" && item.hasOwnProperty("ClaveVendedor")
          ? result.push(item.ClaveVendedor)
          : result.push(item);
        return result;
      }, []);
    },
    OptionsYears() {
      const _this = this;
      return _this.options.Años && _this.options.Años.length > 0
        ? _this.options.Años.sort()
        : [];
    },
  },
  watch: {
    pagination: {
      handler: _.debounce(function (newV, oldV) {
        if (newV !== oldV) {
          this.getData(() => {});
        }
      }, 999),
      deep: true,
      immediate: false,
    },
    search: {
      handler: _.debounce(function (v) {
        this.getData({ page: 1, per_page: 10 });
      }, 1200),
    },
    expanded: {
      handler: _.debounce(function (newV, oldV) {
        if (newV[0] !== oldV[0]) {
          this.getInvoicesByCustomer(newV[0]);
          this.getTrackingByCustomer(newV[0]);
        }
      }, 999),
      deep: true,
      immediate: false,
    },
    "form.agency_id": function (v) {
      if (!!this.form.department_id && v) this.loadSellers(() => {});
    },
    "form.department_id": function (v) {
      if (!!this.form.agency_id && v) this.loadSellers(() => {});
    },
  },
  methods: {
    getRandomColor(index) {
      // Función para generar colores aleatorios
      const colors = [
        "#41B883",
        "#E46651",
        "#00D8FF",
        "#DD1B16",
        "#F4FF81",
        "#FF9800",
        "#90A4AE",
        "#D84315",
        "#B9F6CA",
        "#01579B",
        // Puedes agregar más colores aquí para más clientes
      ];
      return colors[index % colors.length];
    },
    cleanFilter() {
      const _this = this;

      this.$nextTick(() => {
        _this.filters.clave_cliente = [];
        _this.filters.clave_vendedor = [];
        _this.filters.sucursales = [];
        _this.filters.lineas = [];
        _this.filters.years = [];
        _this.filters.currency = [];
        _this.filters.tipo_venta = [];
        _this.filters.municipio = [];
        _this.filters.estado = [];
        _this.pagination.page = 1;
        _this.pagination = Object.assign(
          {},
          {
            page: 1,
            itemsPerPage: 10,
          }
        );
      });
    },
    searchBtn() {
      const _this = this;
      this.$nextTick(() => {
        _this.pagination = Object.assign(
          {},
          {
            page: 1,
            itemsPerPage: 10,
          }
        );
      });
    },
    async getData({ page, per_page }) {
      const _this = this;
      let params = {
        ..._this.filters,
        clave_cliente: _this.FilterClienteReduce,
        clave_vendedor: _this.FilterVendedorReduce,
        search: _this.search,
        // searchCustomer: _this.searchCustomer,
        // order_by: _this.pagination.sortBy[0] || "id",
        // order_sort: _this.pagination.sortDesc[0] ? "asc" : "desc",
        // order_by: _this.pagination.sortBy[0] || "id",
        page: page ?? this.pagination.page,
        per_page: per_page ?? this.pagination.itemsPerPage,
      };
      const {
        data: {
          data: { items },
          message,
        },
      } = await axios.get("/admin/marketing/sales-customer-latest", { params });
      this.$nextTick(() => {
        _this.items = items.data;
        _this.totalItems = items.total;
      });
      this.$store.commit("showSnackbar", {
        message: message,
        color: "success",
        duration: 3000,
      });
    },
    async getInvoicesByCustomer(customer) {
      const _this = this;
      if (customer) {
        let params = {
          nombre: customer.cliente,
          clave_cliente: customer.clave_cliente,
          rfc_cliente: customer.rfc_cliente,
        };

        const {
          data: {
            data: { items },
          },
        } = await axios.get("/admin/marketing/invoices-by-customer", {
          params,
        });
        this.$nextTick(() => {
          _this.invoicesByCustomer = items;
        });
      }
    },
    async getTrackingByCustomer(customer) {
      const _this = this;
      if (customer) {
        let params = {
          nombre: customer.cliente,
          clave_cliente: customer.clave_cliente,
          rfc_cliente: customer.rfc_cliente,
        };

        const {
          data: {
            data: { items },
          },
        } = await axios.get("/admin/marketing/trackings-by-customer", {
          params,
        });
        this.$nextTick(() => {
          _this.trackingsByCustomer = items;
        });
      }
    },
    async createNewTracking() {
      const _this = this;
      if (!_this.$refs.formCreate.validate()) return;
      const params = { ..._this.form, ..._this.expanded[0] };
      const {
        data: { message },
      } = await axios.post(
        "/admin/marketing/create-tracking-to-customer",
        params
      );

      _this.$store.commit("showSnackbar", {
        message: message,
        color: "success",
        duration: 3000,
      });

      _this.dialog = false;
      _this.getTrackingByCustomer(_this.expanded[0]);
    },
    async getOptions() {
      const _this = this;
      const {
        data: {
          data: { options },
        },
      } = await axios.get("/admin/marketing/sales-customer/filters");

      _this.options = options;
      await axios
        .get("/admin/tracking/sales_history/resources")
        .then(function (response) {
          let { agencies, departments } = response.data.data;
          _this.optionsForm.agencies = agencies;
          _this.optionsForm.departments = departments;
        });
    },
    async loadSellers(cb) {
      const _this = this;
      _this.form.attended_by = null;
      let params = {
        seller_agency_id: _this.form.agency_id,
        seller_type_id: _this.form.department_id,
        paginate: "no",
      };
      _this.$store.commit("showLoader");
      await axios
        .get("/admin/sellers", { params: params })
        .then(function (response) {
          _this.optionsForm.sellers = response.data.data;
          _this.$store.commit("hideLoader");
        });
    },
    customFilterCliente(item, queryText, itemText) {
      const words = queryText.toLowerCase().split(" ");

      return words.every((word) => {
        // console.log(item.Cliente.toLowerCase());
        const nameMatch = item.Cliente.toLowerCase().includes(word);
        const claveMatch = item.Clave_Cliente.toLowerCase().includes(word);

        return nameMatch || claveMatch;
      });
    },
    customFilterVendedor(item, queryText, itemText) {
      const words = queryText.toLowerCase().split(" ");

      return words.every((word) => {
        const nameMatch = item.Nombre.toLowerCase().includes(word);
        const claveMatch = item.ClaveVendedor.toLowerCase().includes(word);

        return nameMatch || claveMatch;
      });
    },
    // toggle() {
    //   this.$nextTick(() => {
    //     if (this.likesAllFruit) {
    //       this.selectedFruits = [];
    //     } else {
    //       this.selectedFruits = this.fruits.slice();
    //     }
    //   });
    // },
  },
};
</script>
<style >
</style>