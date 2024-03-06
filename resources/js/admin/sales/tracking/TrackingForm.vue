<template>
  <v-form v-model="valid" ref="form" lazy-validation>
    <v-row dense class="overline mb-4">
      <v-col cols="12" class="d-flex align-stretch">
        <v-card class="mx-0" width="inherit">
          <div class="d-flex align-center">
            <v-subheader>DATOS DEL Seguimiento:</v-subheader>
            <v-spacer />
            <template v-if="form.withQuote != undefined">
              <v-switch
                v-model="form.withQuote"
                label="Crear Cotizacion"
                class="pr-4"
                dense
              ></v-switch>
            </template>
          </div>
          <v-divider> </v-divider>
          <v-card-text>
            <v-row dense>
              <v-col cols="12" class="d-flex align-stretch">
                <v-card class="mx-auto" width="inherit">
                  <div class="d-flex flex-wrap">
                    <v-subheader>CON ATENCION A:</v-subheader>
                    <v-spacer />
                    <v-btn @click="dialog = true" color="green" dark>
                      Registrar Prospecto
                      <v-icon right>mdi-account-plus </v-icon>
                    </v-btn>
                  </div>
                  <v-divider> </v-divider>
                  <v-card-text class="pa-4">
                    <v-autocomplete
                      v-model="form.prospect_id"
                      :items="options.prospects"
                      item-text="full_name"
                      item-value="id"
                      placeholder="Buscar por Nombre o Telefono o Razon social"
                      :rules="[(v) => !!v || 'Es Requerido']"
                      :filter="customFilter"
                      hide-details
                      clearable
                      outlined
                      filled
                      class="overline align-center"
                    >
                      <template v-slot:prepend-item>
                        <v-btn
                          @click="dialog = true"
                          color="green"
                          class="ma-2"
                          dark
                          block
                        >
                          Registrar Prospecto
                          <v-icon right>mdi-account-plus </v-icon>
                        </v-btn>
                      </template>

                      <template #item="{ item }">
                        <v-list-item-content>
                          <v-list-item-subtitle>
                            {{ item.company }}
                          </v-list-item-subtitle>
                          <v-list-item-title
                            class="text-uppercase font-weight-bold"
                          >
                            {{ item.full_name }}
                          </v-list-item-title>
                          <v-list-item-subtitle>
                            Tel: {{ item.phone }}
                          </v-list-item-subtitle>
                        </v-list-item-content>
                      </template>
                    </v-autocomplete>
                    <v-subheader class="pl-0">DATOS DEL PROSPECTO</v-subheader>

                    <v-divider></v-divider>
                    <v-scroll-y-transition mode="out-in">
                      <div
                        v-if="!SelectedProspect"
                        class="text-h6 grey--text text--lighten-1 font-weight-light"
                        style="align-self: center"
                      >
                        Selecciona a un Prospecto
                      </div>
                      <v-card v-else :key="SelectedProspect.id" flat>
                        <v-row dense>
                          <v-col
                            class="text-left mr-4 mb-2"
                            tag="strong"
                            cols="6"
                          >
                            Nombre:
                          </v-col>
                          <v-col class="overline text-right">
                            {{ SelectedProspect.full_name }}
                          </v-col>
                          <v-col
                            class="text-left mr-4 mb-2"
                            tag="strong"
                            cols="6"
                          >
                            Razon Social:
                          </v-col>
                          <v-col class="overline text-right">
                            {{ SelectedProspect.company }}
                          </v-col>
                          <v-col
                            class="text-left mr-4 mb-2"
                            tag="strong"
                            cols="6"
                          >
                            RFC:
                          </v-col>
                          <v-col class="overline text-right">
                            {{ SelectedProspect.rfc }}
                          </v-col>
                          <v-col
                            class="text-left mr-4 mb-2"
                            tag="strong"
                            cols="6"
                          >
                            Telefono:
                          </v-col>
                          <v-col class="overline text-right">
                            {{ SelectedProspect.phone }}
                          </v-col>
                          <v-col
                            class="text-left mr-4 mb-2"
                            tag="strong"
                            cols="6"
                          >
                            Domicilio:
                          </v-col>
                          <v-col class="overline text-right">
                            {{ SelectedProspect.town }}
                          </v-col>
                        </v-row>
                      </v-card>
                    </v-scroll-y-transition>
                  </v-card-text>
                  <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn
                      :disabled="!SelectedProspect"
                      color="blue"
                      dark
                      @click="dialogEdit = true"
                    >
                      Editar Prospecto
                      <v-icon right> mdi-pencil </v-icon>
                    </v-btn>
                  </v-card-actions>
                </v-card>
              </v-col>
            </v-row>
          </v-card-text>
          <v-card-text v-if="!form.withQuote">
            <v-row dense>
              <v-col cols="12" md="8">
                <p class="text-14 mb-1">
                  Titulo del Seguimiento (Modelo o Nombre Proyecto)
                </p>
                <v-text-field
                  v-model="form.reference"
                  placeholder="(Nombre Proyecto, Modelo del Equipo o Algo Referente a la Oportunidad)"
                  :rules="[(v) => !!v || 'Es Requerido']"
                  clearable
                  hide-details
                  outlined
                  filled
                  dense
                >
                </v-text-field>
              </v-col>
              <v-col cols="12" md="4">
                <p class="text-14 mb-1">Categoria de Seguimiento</p>
                <v-autocomplete
                  v-model="form.title"
                  :items="options.categories"
                  item-value="name"
                  item-text="name"
                  placeholder="Seleccionar"
                  hide-details
                  outlined
                  filled
                  dense
                >
                </v-autocomplete>
              </v-col>
              <!-- </v-row> -->
              <!-- <v-row> -->
              <v-col cols="12" md="4">
                <p class="text-14 mb-1">Valor Estimado</p>
                <v-currency-field
                  v-model.number="form.price"
                  :default-value="form.price"
                  placeholder="0.00"
                  :rules="[(v) => !!v || 'Es Requerido']"
                  type="number"
                  prefix="$"
                  hide-details
                  outlined
                  filled
                  dense
                ></v-currency-field>
              </v-col>
              <v-col cols="12" md="4">
                <p class="text-14 mb-1">Moneda</p>
                <v-select
                  v-model.number="form.currency_id"
                  :items="options.currency"
                  item-value="id"
                  item-text="name"
                  placeholder="Moneda"
                  :rules="[(v) => !!v || 'Es Requerido']"
                  hide-details
                  outlined
                  filled
                  dense
                ></v-select>
              </v-col>
              <v-col cols="12" md="4">
                <p class="text-14 mb-1">T.C.</p>
                <v-currency-field
                  v-model.number="ExchangeRate"
                  :default-value="form.exchange_value"
                  placeholder="0.00"
                  :rules="[(v) => !!v || 'Es Requerido']"
                  type="number"
                  prefix="$"
                  hide-details
                  readonly
                  outlined
                  filled
                  dense
                  disabled
                ></v-currency-field>
              </v-col>
              <!-- </v-row> -->

              <!-- <v-row> -->
              <v-col cols="12" md="6">
                <p class="text-14 mb-1">Origen del Seguimiento</p>
                <v-select
                  v-model="form.first_contact"
                  :items="options.origin"
                  :rules="[(v) => !!v || 'Es Requerido']"
                  hide-details
                  outlined
                  filled
                  dense
                ></v-select>
              </v-col>
              <v-col cols="12" md="6">
                <p class="text-14 mb-1">Condicion de Pago</p>
                <v-select
                  v-model="form.tracking_condition"
                  :items="PaymentConditionConfig"
                  placeholder="Placeholder"
                  hide-details
                  outlined
                  filled
                  dense
                ></v-select>
              </v-col>
            </v-row>
          </v-card-text>
          <v-card-text v-else class="px-0">
            <v-card-text>
              <v-row dense>
                <v-col cols="12" md="6">
                  <p class="text-14 mb-1">
                    Titulo del Seguimiento (Modelo o Nombre Proyecto)
                  </p>
                  <v-text-field
                    v-model="form.reference"
                    placeholder="(Nombre Proyecto, Modelo del Equipo o Algo Referente a la Oportunidad)"
                    :rules="[(v) => !!v || 'Es Requerido']"
                    clearable
                    hide-details
                    outlined
                    filled
                    dense
                  >
                  </v-text-field>
                </v-col>
                <v-col cols="12" md="6">
                  <p class="text-14 mb-1">Origen del Seguimiento</p>
                  <v-select
                    v-model="form.first_contact"
                    :items="options.origin"
                    :rules="[(v) => !!v || 'Es Requerido']"
                    hide-details
                    outlined
                    filled
                    dense
                  ></v-select>
                </v-col>
              </v-row>
            </v-card-text>
            <v-card-text class="pa-0">
              <quote-concept-table
                :dialogForm="dialogQuote"
                @edit="dialogQuote = true"
                @close="dialogQuote = false"
                :items.sync="form.products"
                :paymentCondition="form.tracking_condition"
                :Category_id="Category ? Category.id : null"
                :propCurrency="form.currency"
                @payment="(v) => (v ? (form.tracking_condition = v) : '')"
                @currency="(v) => (form.currency = v)"
                @SELECTED_CATEGRORY="
                  (v) => {
                    if (v) {
                      form.title = options.categories.find(
                        (c) => c.id == v
                      ).name;
                    }
                  }
                "
                :optionsPaymentcondition="PaymentConditionConfig"
              ></quote-concept-table>
            </v-card-text>
            <v-card-actions>
              <v-btn dark color="blue" block @click="ShowProducts">
                <v-icon left>mdi-plus</v-icon> Ver Lista de Precios
              </v-btn>
            </v-card-actions>
            <v-card-text>
              <v-row>
                <v-col cols="12" md="4">
                  <v-textarea
                    v-model="form.observation"
                    label="Nota de Vendedor"
                    outlined
                    filled
                    hide-details
                  ></v-textarea>
                </v-col>
                <v-col cols="12" md="8" class="px-0">
                  <v-simple-table dense class="pa-0">
                    <template #default>
                      <tbody>
                        <tr>
                          <td>Subtotal:</td>
                          <td class="text-right text-h6">
                            {{ Subtotal | money }} {{ form.currency.name }}
                          </td>
                        </tr>
                        <tr>
                          <td>IVA:</td>
                          <td class="d-flex justify-end">
                            <v-checkbox
                              v-model="CheckedTax"
                              class="ma-0"
                              hide-details
                            >
                              <template v-slot:label>
                                <div>IVA: {{ form.tax | percent }}</div>
                              </template>
                            </v-checkbox>
                          </td>
                        </tr>
                        <tr>
                          <td>T.C.:</td>
                          <td class="text-right text-h6">
                            <!-- {{ form.exchange_value | currency }} MXN -->
                            {{ ExchangeRate | currency }} MXN
                          </td>
                        </tr>
                        <tr
                          style="height: 50px"
                          v-if="$gate.allow('isGerente', 'tracking')"
                        >
                          <td>Descuento:</td>
                          <td>
                            <span class="d-flex justify-end px-0">
                              <v-currency-field
                                v-model.number="form.discount"
                                :default-value="form.discount"
                                type="number"
                                outlined
                                suffix="$"
                                hide-details
                                :prefix="form.currency.name"
                                reverse
                                style="max-width: 250px"
                                dense
                              ></v-currency-field>
                            </span>
                          </td>
                        </tr>
                        <tr>
                          <td>Total:</td>
                          <td class="text-right text-h6">
                            {{ Total | money }} {{ form.currency.name }}
                          </td>
                        </tr>
                      </tbody>
                    </template>
                  </v-simple-table>
                </v-col>
              </v-row>
            </v-card-text>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>

    <v-row dense class="overline">
      <v-col cols="12" md="6" class="d-flex align-stretch">
        <v-card class="mx-auto" width="inherit">
          <div class="d-flex">
            <v-subheader>VENDEDOR ASIGNADO:</v-subheader>
          </div>
          <v-divider> </v-divider>
          <v-card-text class="pa-4">
            <v-row>
              <v-col cols="12" md="6">
                <p class="text-14 mb-1">Sucursal</p>
                <v-select
                  v-model="form.agency_id"
                  :items="options.agencies"
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
              <v-col cols="12" md="6">
                <p class="text-14 mb-1">Departamento</p>
                <v-select
                  v-model="form.department_id"
                  :items="options.departments"
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
            </v-row>
            <v-row>
              <v-col cols="12">
                <p class="text-14 mb-1">Vendedor Asigando</p>
                <v-autocomplete
                  v-model="form.attended_by"
                  :disabled="availableSeller"
                  :items="options.sellers"
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
          </v-card-text>
        </v-card>
      </v-col>
      <v-col cols="12" md="6" class="d-flex align-stretch">
        <v-card class="mx-auto" width="inherit">
          <div class="d-flex">
            <v-subheader>Etapa y Motivo del Seguimiento:</v-subheader>
          </div>
          <v-divider> </v-divider>
          <v-card-text class="pt-0">
            <v-col cols="12" class="px-0">
              <p class="text-14 mb-1">Certeza del Seguimiento</p>
              <v-select
                v-model="form.assertiveness"
                :items="options.assertiveness"
                :rules="[(v) => !!v || 'Es Requerido']"
                prepend-inner-icon="mdi-circle-slice-2"
                hide-details
                outlined
                filled
                dense
              >
                <template v-slot:item="{ item }">
                  <v-list-item-content>
                    <v-list-item-title> {{ item.text }} </v-list-item-title>
                  </v-list-item-content>
                  <v-list-item-action>
                    <v-chip small dark :color="item.color">
                      {{ item.value | percent }}
                    </v-chip>
                  </v-list-item-action>
                </template>
              </v-select>
            </v-col>
            <v-col cols="12" class="px-0">
              <v-dialog
                ref="dialog"
                v-model="modal"
                :return-value.sync="form.date_next_tracking"
                persistent
                width="290px"
              >
                <template v-slot:activator="{ on, attrs }">
                  <v-text-field
                    v-model="form.date_next_tracking"
                    label="Fecha Proximo Seguimiento"
                    prepend-icon="mdi-calendar"
                    readonly
                    v-bind="attrs"
                    v-on="on"
                    hide-details
                    outlined
                    filled
                    dense
                  ></v-text-field>
                </template>
                <v-date-picker v-model="form.date_next_tracking" scrollable>
                  <v-spacer></v-spacer>
                  <v-btn text color="primary" @click="modal = false">
                    Cancel
                  </v-btn>
                  <v-btn
                    text
                    color="primary"
                    @click="$refs.dialog.save(form.date_next_tracking)"
                  >
                    OK
                  </v-btn>
                </v-date-picker>
              </v-dialog>
            </v-col>
            <v-col cols="12" class="px-0">
              <p class="text-14 mb-1">Motivo del Seguimiento</p>
              <v-textarea
                v-model="form.description_topic"
                :rules="[(v) => !!v || 'Es requrido']"
                placeholder="Escribir a detalle el motivo del Seguimiento"
                height="100%"
                filled
                hide-details
                outlined
                dense
              />
            </v-col>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>
    <dialog-component
      :show="dialog"
      @close="(dialog = false), loadOptions()"
      :fullscreen="$vuetify.breakpoint.mobile"
      title="Registrar Nuevo Prospecto"
      :maxWidth="600"
      closeable
      key="create"
    >
      <prospect-create
        @success="
          (prospect_id) => {
            form.prospect_id = prospect_id;
            $nextTick(() => {
              dialog = false;
              loadOptions();
            });
          }
        "
      ></prospect-create>
    </dialog-component>
    <dialog-component
      v-if="dialogEdit && SelectedProspect.id"
      :show="dialogEdit"
      @close="(dialogEdit = false), loadOptions()"
      :fullscreen="$vuetify.breakpoint.mobile"
      title="Editar Prospecto"
      :maxWidth="600"
      closeable
      key="edit"
    >
      <prospect-edit
        v-if="!!SelectedProspect"
        :propProspectId="SelectedProspect.id"
        :key="`edit-${SelectedProspect.id}`"
      ></prospect-edit>
    </dialog-component>
  </v-form>
</template>
<script>
import DialogComponent from "../../components/DialogComponent.vue";
import ProspectCreate from "../prospect/ProspectCreate.vue";
import ProspectEdit from "../prospect/ProspectEdit.vue";
import QuoteConceptTable from "@admin/sales/tracking/forms/QuoteConceptTable.vue";
import Assertiveness from "@admin/sales/tracking/resources/assertiveness.json";

const _paymentCondition = [
  { text: "Por Definir", value: "por_definir", config: [18] },
  {
    text: "P. Lista",
    value: "precio_lista",
    config: [5, 6, 11, 14, 16, 9, 17],
  },
  {
    text: "Contado",
    value: "contado",
    config: [1, 2, 3, 10, 5, 6, 11, 16, 9, 17, 14, 8],
  },
  { text: "JDF 2 años", value: "jdf_2y", config: [1, 2, 3, 10, 17] },
  { text: "JDF 5 años", value: "jdf_5y", config: [1] },
  {
    text: "Financiamiento 3 Años S/I",
    value: "jdf_3y_si",
    config: [1, 2, 3, 10, 17],
  },
  { text: "Expo", value: "precio_expo", config: [1, 5] },
  { text: "Precio Volumen", value: "por_volumen", config: [5, 14] },
  { text: "Arrendamiento", value: "renta_1", config: [6, 15] },
  { text: "Arrendamiento 2 meses", value: "renta_2", config: [15] },
  { text: "Arrendamiento +3 meses", value: "renta_3", config: [15] },
  { text: "Credito 30 Dias", value: "credito_30d", config: [5, 17] },
  { text: "Arrendadoras", value: "arrendadoras", config: [6] },
];

export default {
  components: {
    ProspectCreate,
    ProspectEdit,
    DialogComponent,
    QuoteConceptTable,
  },
  name: "TrackingForm",
  props: {
    form: {
      type: Object,
      require: true,
    },
  },
  data() {
    return {
      //
      valid: true,
      dialogQuote: false,
      modal: false,
      dialog: false,
      dialogEdit: false,
      options: {
        prospects: [],
        agencies: [],
        products: [],
        departments: [],
        sellers: [],
        price_types: _paymentCondition,
        origin: [
          "Online",
          "Visita en Agencia",
          "Visita de Campo",
          "Expo",
          "Apoyo",
        ],
        categories: [],
        currency: [],
        exchange_value: 1,
        assertiveness: Assertiveness,
      },
    };
  },
  mounted() {
    this.loadOptions();
  },
  watch: {
    "form.agency_id": function (v) {
      if (!!this.form.department_id && v) this.loadSellers(() => {});
    },
    "form.department_id": function (v) {
      if (!!this.form.agency_id && v) this.loadSellers(() => {});
    },
    "form.withQuote": function (v) {
      const _this = this;
      _this.form.products = [];
    },
  },
  computed: {
    ExchangeRate: {
      get() {
        const _this = this;

        return (_this.form.exchange_value = _this.options.exchange_value);
      },
    },
    SelectedProspect() {
      const _this = this;
      if (_this.form.prospect_id) {
        return this.options.prospects.find(
          (e) => e.id == this.form.prospect_id
        );
      } else return null;
    },
    availableSeller() {
      const self = this;
      if (self.form.agency_id && self.form.department_id) {
        return false;
      } else {
        return true;
      }
    },
    Category() {
      const _this = this;
      if (_this.form.title) {
        return _this.options.categories.find((c) => c.name == _this.form.title);
      } else {
        return null;
      }
    },
    PaymentConditionConfig() {
      const _this = this;
      let Default = [{ text: "Por Definir", value: "por_definir" }];
      let result = [];
      if (_this.form.title) {
        let category = _this.options.categories.find(
          (c) => c.name == _this.form.title
        );
        const even = (e) => e == category.id;
        result = _this.options.price_types.filter((option) =>
          option.config.some(even)
        );
      }
      return Default.concat(result);
    },
    Currency() {
      const _this = this;
      let currency = { currency: { id: 1, name: "MXN" } };
      if (_this.form.products.length > 0) {
        currency = _this.form.products.values().next().value;
      }
      _this.form.currency_id = currency.currency.id;
      return (_this.form.currency = currency.currency);
    },
    CheckedTax: {
      get() {
        return this.form.tax == 0.16;
      },
      set(val) {
        val ? (this.form.tax = 0.16) : (this.form.tax = 0);
      },
    },
    Subtotal: {
      get() {
        const _this = this;
        if (!!_this.form.products && _this.form.products.length > 0)
          return (_this.form.subtotal = _this.form.products.reduce(
            (acc, crr) => (acc += parseFloat(crr.subtotal)),
            0
          ));
        else return 0;
        // .map((item) => parseFloat(item.subtotal))
      },
      set(v) {
        console.log(v, "SET Subtotal");
      },
    },
    Total: {
      get() {
        const _this = this;
        let amountTax = _this.Subtotal + _this.Subtotal * _this.form.tax;
        return (_this.form.total = amountTax - _this.form.discount);
      },
    },
  },
  methods: {
    ShowProducts() {
      const _this = this;
      return _this.form.tracking_condition !== "por_definir"
        ? (_this.dialogQuote = true)
        : _this.$store.commit("showSnackbar", {
            message: "Seleccionar una Condicion de Pago",
            color: "warning",
            duration: 3000,
          });
    },
    async loadOptions() {
      const _this = this;
      await axios
        .get("/admin/tracking/sales_history/resources")
        .then(function (response) {
          let {
            agencies,
            departments,
            prospects,
            currency,
            categories,
            exchange_value,
          } = response.data.data;
          _this.options.agencies = agencies;
          _this.options.departments = departments;
          _this.options.prospects = prospects;
          _this.options.currency = currency;
          _this.options.categories = categories;
          _this.options.exchange_value = exchange_value;
        });
    },
    async loadSellers(cb) {
      const _this = this;
      // if (!this.editing) _this.form.attended_by = window.LSK_APP.AUTH_USER.id;
      let params = {
        seller_agency_id: _this.form.agency_id,
        seller_type_id: _this.form.department_id,
        paginate: "no",
      };
      _this.$store.commit("showLoader");
      await axios
        .get("/admin/sellers", { params: params })
        .then(function (response) {
          _this.options.sellers = response.data.data;
          _this.$store.commit("hideLoader");
        });
    },
    customFilter(item, queryText, itemText) {
      const words = queryText.toLowerCase().split(" ");

      return words.every((word) => {
        const nameMatch = item.full_name.toLowerCase().includes(word);
        const phoneMatch = item.phone
          ? item.phone.toLowerCase().includes(word)
          : false;
        const companyMatch = item.company
          ? item.company.toLowerCase().includes(word)
          : false;

        return nameMatch || phoneMatch || companyMatch;
      });

      // const textName = item.full_name.toLowerCase();
      // const textPhone = item.phone.toLowerCase();
      // const searchText = queryText.toLowerCase();

      // return (
      //   textName.indexOf(searchText) > -1 || textPhone.indexOf(searchText) > -1
      // );
    },
  },
};
</script>
<style></style>
