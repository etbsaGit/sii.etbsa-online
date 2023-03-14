<template>
  <v-form v-model="valid" ref="form" lazy-validation>
    <v-row dense class="overline mb-4">
      <v-col cols="12" md="6" class="d-flex align-stretch">
        <v-card class="mx-auto" width="inherit">
          <div class="d-flex">
            <v-subheader>ATENCION A:</v-subheader>
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
              dense
              filled
            >
              <!-- @change="
                (id) =>
                  (selectedProspect = options.prospects.find((e) => e.id == id))
              " -->
              <template v-slot:append-outer>
                <v-btn
                  v-if="!$vuetify.breakpoint.mobile"
                  @click="dialog = true"
                  color="green"
                  dark
                >
                  Registrar Nuevo
                  <v-icon right>mdi-account-plus </v-icon>
                </v-btn>
              </template>
              <template v-slot:prepend-item>
                <v-list dense color="green lighten-3 overline">
                  <v-list-item @click="dialog = true">
                    <v-list-item-content>
                      <v-list-item-title>
                        REGISTRAR un Nuevo Prospecto
                      </v-list-item-title>
                    </v-list-item-content>
                    <v-list-item-action>
                      <v-icon color="blue">mdi-account-plus</v-icon>
                    </v-list-item-action>
                  </v-list-item>
                </v-list>
              </template>
              <template v-slot:item="{ item }">
                <v-list-item-title> {{ item.full_name }} </v-list-item-title>
                <v-list-item-subtitle> {{ item.phone }} </v-list-item-subtitle>
                <v-list-item-subtitle>
                  {{ item.company }}
                </v-list-item-subtitle>
              </template>
            </v-autocomplete>
            <v-subheader class="pl-0">DATOS DEL PROSPECTO</v-subheader>
            <v-divider></v-divider>
            <v-scroll-y-transition mode="out-in">
              <!-- v-if="!selectedProspect" -->
              <div
                v-if="!SelectedProspect"
                class="text-h6 grey--text text--lighten-1 font-weight-light"
                style="align-self: center;"
              >
                Selecciona a un Prospecto
              </div>
              <v-card v-else :key="SelectedProspect.id" flat>
                <v-row dense>
                  <v-col class="text-left mr-4 mb-2" tag="strong" cols="6">
                    Nombre:
                  </v-col>
                  <v-col class="overline text-right">
                    {{ SelectedProspect.full_name }}
                  </v-col>
                  <v-col class="text-left mr-4 mb-2" tag="strong" cols="6">
                    Razon Social:
                  </v-col>
                  <v-col class="overline text-right">
                    {{ SelectedProspect.company }}
                  </v-col>
                  <v-col class="text-left mr-4 mb-2" tag="strong" cols="6">
                    RFC:
                  </v-col>
                  <v-col class="overline text-right">
                    {{ SelectedProspect.rfc }}
                  </v-col>
                  <v-col class="text-left mr-4 mb-2" tag="strong" cols="6">
                    Telefono:
                  </v-col>
                  <v-col class="overline text-right">
                    {{ SelectedProspect.phone }}
                  </v-col>
                  <v-col class="text-left mr-4 mb-2" tag="strong" cols="6">
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
      <v-col cols="12" md="6" class="d-flex align-stretch">
        <v-card class="mx-auto" width="inherit">
          <div class="d-flex">
            <v-subheader>DATOS DEL LEAD:</v-subheader>
            <v-spacer />
            <template v-if="form.withQuote != undefined">
              <v-switch
                v-model="form.withQuote"
                label="Con Cotizacion"
                class="pr-4"
                dense
              ></v-switch>
            </template>
          </div>
          <v-divider> </v-divider>
          <v-card-text class="pt-0">
            <v-col cols="12" class="px-0">
              <p class="text-14 mb-1">Categoria del LEAD</p>
              <v-autocomplete
                v-model="form.title"
                :items="options.categories"
                item-value="name"
                item-text="name"
                placeholder="Seleccionar"
                :rules="[(v) => !!v || 'Es Requerido']"
                hide-details
                outlined
                filled
                dense
              >
              </v-autocomplete>
            </v-col>
            <v-col cols="12" class="px-0">
              <p class="text-14 mb-1">Titulo del LEAD</p>
              <v-text-field
                v-model="form.reference"
                placeholder="Buscar por Nombre o SKU"
                :rules="[(v) => !!v || 'Es Requerido']"
                clearable
                hide-details
                outlined
                filled
                dense
              >
              </v-text-field>
              <!-- <v-combobox
                v-model="form.product"
                :items="options.products"
                item-value="name"
                item-text="name"
                placeholder="Buscar por Nombre o SKU"
                :rules="[(v) => !!v || 'Es Requerido']"
                clearable
                hide-details
                outlined
                filled
                dense
              >
                <template v-slot:item="{ item }">
                  <v-list-item-title>{{ item.name }}</v-list-item-title>
                  <v-list-item-subtitle>{{ item.sku }}</v-list-item-subtitle>
                  <v-list-item-subtitle>
                    {{ item.price_1 | money }}{{ item.currency.name }}
                  </v-list-item-subtitle>
                </template>
              </v-combobox> -->
            </v-col>
            <v-row v-if="!form.withQuote">
              <v-col cols="12" md="8">
                <p class="text-14 mb-1">Valor del Lead</p>
                <v-text-field
                  v-model.number="form.price"
                  placeholder="0.00"
                  :rules="[(v) => !!v || 'Es Requerido']"
                  type="number"
                  prefix="$"
                  hide-details
                  outlined
                  filled
                  dense
                ></v-text-field>
              </v-col>
              <v-col cols="12" md="4">
                <p class="text-14 mb-1">Moneda</p>
                <v-select
                  v-model="form.currency_id"
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
            </v-row>
            <v-row>
              <v-col cols="12" md="6">
                <p class="text-14 mb-1">Origen del Lead</p>
                <v-select
                  v-model="form.first_contact"
                  :items="options.origin"
                  placeholder="Placeholder"
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
                  :items="options.payment_conditions"
                  placeholder="Placeholder"
                  hide-details
                  outlined
                  filled
                  dense
                ></v-select>
              </v-col>
            </v-row>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>
    <v-scroll-y-transition mode="out-in">
      <v-row v-if="form.withQuote" class="overline">
        <v-col cols="12" class="d-flex align-stretch">
          <v-card
            class="mx-auto"
            width="inherit"
            min-height="200"
            color="grey lighten-3"
          >
            <div class="d-flex">
              <v-subheader>Partidas a Cotizar:</v-subheader>
              <v-spacer />
              <v-btn text color="blue" @click="dialogQuote = true" class="ma-2">
                <v-icon left>mdi-plus</v-icon> Agregar Producto
              </v-btn>
            </div>
            <v-card-text>
              <quote-concept-table
                :dialogForm="dialogQuote"
                @edit="dialogQuote = true"
                @close="dialogQuote = false"
                :items.sync="form.products"
              ></quote-concept-table>
            </v-card-text>
            <v-card-actions>
              <v-btn text color="blue" @click="dialogQuote = true">
                <v-icon left>mdi-plus</v-icon> Agregar Producto
              </v-btn>
            </v-card-actions>
            <!-- Amounts -->
            <v-col cols="12">
              <v-simple-table dense class="pa-4">
                <tr class="py-3">
                  <td>Subtotal:</td>
                  <th class="text-right pr-2">
                    {{ Subtotal | money }} {{ Currency.name }}
                  </th>
                </tr>
                <tr class="py-3">
                  <td>IVA:</td>
                  <th class="d-flex justify-end mb-3">
                    <v-checkbox
                      v-model="CheckedTax"
                      hide-details
                      class="shrink mr-2"
                    >
                      <template v-slot:label>
                        <div>Con IVA: {{ form.tax | percent }}</div>
                      </template>
                    </v-checkbox>
                  </th>
                </tr>
                <tr class="py-3">
                  <td>Descuento:</td>
                  <th class="d-flex justify-end">
                    <v-text-field
                      v-model="form.discount"
                      type="number"
                      outlined
                      suffix="$"
                      hide-details
                      :prefix="Currency.name"
                      reverse
                      style="max-width: 200px;"
                      dense
                    ></v-text-field>
                  </th>
                </tr>
                <tr>
                  <v-divider class="my-2" />
                </tr>
                <tr>
                  <td>Total:</td>
                  <th class="text-right pr-2 text-h4">
                    {{ Total | money }} {{ Currency.name }}
                  </th>
                </tr>
              </v-simple-table>
            </v-col>
          </v-card>
        </v-col>
      </v-row>
    </v-scroll-y-transition>
    <v-row dense class="overline mb-4">
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
            <v-subheader>ETAPA Y DESCRIPCION DEL LEAD:</v-subheader>
          </div>
          <v-divider> </v-divider>
          <v-card-text class="pt-0">
            <v-col cols="12" class="px-0">
              <p class="text-14 mb-1">Etapa del LEAD</p>
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
      <prospect-create></prospect-create>
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
import QuoteConceptTable from "./forms/QuoteConceptTable.vue";
import Assertiveness from "@admin/sales/tracking/resources/assertiveness.json";
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
      dialogQuote: false,
      modal: false,
      editedItem: {
        subtotal: 0,
        discount: 0,
        tax: 0.16,
        total: 0,
        currency: null,
        products: [],
      },
      //
      selectedProspect: null,
      valid: true,
      dialog: false,
      dialogEdit: false,
      selectModel: null,
      selectConfig: { hint: "" },
      options: {
        // tractors: Tractors,
        // implementos: Implementos,
        prospects: [],
        agencies: [],
        products: [],
        departments: [],
        sellers: [],
        payment_conditions: [
          "Por definir",
          "Financiamiento",
          "Contado",
          "Renta",
        ],
        origin: ["Online", "Visita en Agencia", "Visita de Campo"],
        categories: [],
        currency: [],
        assertiveness: Assertiveness,
      },
    };
  },
  mounted() {
    // this.$eventBus.$on(["PROSPECT-FORM-SUBMIT"], () => {
    //   this.loadOptions();
    // });
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
    // "form.title": function(category_name) {
    //   const _this = this;
    //   if (_this.form.reference) {
    //     _this.form.product = {
    //       name: _this.form.reference,
    //       price_1: _this.form.price,
    //       currency: {
    //         id: _this.form.currency_id,
    //       },
    //     };
    //   } else {
    //     _this.form.product = null;
    //   }
    //   _this.loadProductsByCategory(() => {});
    // },
    // "form.product": {
    //   handler(v) {
    //     const _this = this;
    //     console.log("before", v);
    //     if (v != null && typeof v != "string") {
    //       console.log("watch product object", v);
    //       _this.form.reference = v.name;
    //       _this.form.price = v.price_1;
    //       _this.form.currency_id = v.currency.id;
    //     } else {
    //       console.log("watch product string", v);
    //       _this.form.reference = v;
    //       _this.form.price = null;
    //       _this.form.currency_id = null;
    //     }
    //   },
    //   deep: true,
    //   flush: "post",
    //   immediate: true,
    // },
  },
  computed: {
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
    //
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
        return (_this.form.subtotal = _this.form.products
          .map((item) => parseFloat(item.subtotal))
          .reduce((acc, crr) => acc + crr, 0));
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
          } = response.data.data;
          _this.options.agencies = agencies;
          _this.options.departments = departments;
          _this.options.prospects = prospects;
          _this.options.currency = currency;
          _this.options.categories = categories;
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
    async loadProductsByCategory(cb) {
      const _this = this;

      let params = {
        category_name: _this.form.title,
        paginate: "no",
      };

      await axios.get("/admin/products", { params }).then((response) => {
        _this.options.products = response.data.data.data;
        (cb || Function)();
      });
    },
    customFilter(item, queryText, itemText) {
      const textName = item.full_name.toLowerCase();
      const textPhone = item.phone.toLowerCase();
      const searchText = queryText.toLowerCase();

      return (
        textName.indexOf(searchText) > -1 || textPhone.indexOf(searchText) > -1
      );
    },
    customFilterProducts({ item, queryText }) {
      const textName = item.name.toLowerCase();
      const textSku = item.sku.toLowerCase();
      const searchText = queryText.toLowerCase();

      return (
        textName.indexOf(searchText) > -1 || textSku.indexOf(searchText) > -1
      );
    },
  },
};
</script>
<style></style>
