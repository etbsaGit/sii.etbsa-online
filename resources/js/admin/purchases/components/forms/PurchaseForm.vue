<template>
  <v-card flat>
    <v-form v-model="valid" lazy-validation ref="formPurchase">
      <v-row class="caption text-uppercase">
        <v-col cols="12" md="4">
          <v-row dense>
            <v-col cols="12">
              <v-autocomplete
                v-model="form.supplier_id"
                :items="options.suppliers"
                item-text="business_name"
                item-value="id"
                label="Proveedor:"
                placeholder="Buscar por Nombre | RFC | Email"
                persistent-placeholder
                :filter="customFilter"
                :rules="[(v) => !!v || 'Es Requerido']"
                outlined
                dense
              >
                <template v-slot:item="data">
                  <v-list-item-content>
                    <v-list-item-title v-text="data.item.business_name" />
                    <v-list-item-subtitle v-text="data.item.rfc" />
                    <v-list-item-subtitle v-text="`Email:${data.item.email}`" />
                  </v-list-item-content>
                </template>
              </v-autocomplete>
            </v-col>
            <v-col cols="12" md="6">
              <v-menu
                v-model="menu"
                close-on-content-click
                transition="scale-transition"
                offset-y
                min-width="auto"
              >
                <template v-slot:activator="{ on, attrs }">
                  <v-text-field
                    v-model="form.deliver_date"
                    label="Fecha Entrega:"
                    prepend-icon="mdi-calendar"
                    readonly
                    outlined
                    dense
                    v-bind="attrs"
                    v-on="on"
                  ></v-text-field>
                </template>
                <v-date-picker
                  v-model="form.deliver_date"
                  @input="menu = false"
                ></v-date-picker>
              </v-menu>
            </v-col>
            <v-col cols="12" md="6">
              <v-select
                v-model="form.payment_condition"
                label="condicion de pago:"
                outlined
                :rules="[(v) => !!v || 'Es Requerido']"
                :items="['8 Dias', '15 Dias', '30 Dias', '60 Dias', '90 Dias']"
                dense
              ></v-select>
            </v-col>
            <v-subheader>Datos Facturacion</v-subheader>
            <v-row dense>
              <v-col cols="12" md="6">
                <v-select
                  v-model="form.metodo_pago"
                  :items="options.metodoPago"
                  :hint="`${
                    form.metodo_pago ? form.metodo_pago.description : ''
                  }`"
                  persistent-hint
                  label="Metodo Pago:"
                  item-text="clave"
                  item-value="clave"
                  return-object
                  outlined
                  :rules="[(v) => !!v || 'Es Requerido']"
                  dense
                >
                  <template v-slot:item="data">
                    <v-list-item-content>
                      <v-list-item-title v-text="data.item.clave" />
                      <v-list-item-subtitle v-text="data.item.description" />
                    </v-list-item-content>
                  </template>
                </v-select>
              </v-col>
              <v-col cols="12" md="6">
                <v-select
                  v-model="form.uso_cfdi"
                  :items="options.usoCFDI"
                  label="UsoCFDI"
                  :hint="`${form.uso_cfdi ? form.uso_cfdi.description : ''}`"
                  persistent-hint
                  item-text="clave"
                  item-value="clave"
                  return-object
                  outlined
                  :rules="[(v) => !!v || 'Es Requerido']"
                  dense
                >
                  <template v-slot:item="data">
                    <v-list-item-content>
                      <v-list-item-title v-text="data.item.clave" />
                      <v-list-item-subtitle v-text="data.item.description" />
                    </v-list-item-content>
                  </template>
                </v-select>
              </v-col>
              <v-col cols="12">
                <v-select
                  v-model="form.forma_pago"
                  :items="options.formaPago"
                  :hint="`${
                    form.forma_pago ? form.forma_pago.description : ''
                  }`"
                  persistent-hint
                  label="Forma Pago:"
                  item-text="clave"
                  item-value="clave"
                  return-object
                  outlined
                  :rules="[
                    (v) => !!v || 'Es Requerido',
                    (v) =>
                      !isPPD && !!v ? v.clave != '99' || 'No permitido' : true,
                  ]"
                  dense
                  :readonly="isPPD"
                >
                  <template v-slot:item="data">
                    <v-list-item-content>
                      <v-list-item-title v-text="data.item.clave" />
                      <v-list-item-subtitle v-text="data.item.description" />
                    </v-list-item-content>
                  </template>
                </v-select>
              </v-col>
            </v-row>
            <v-subheader>Asignar Cargos</v-subheader>
            <v-row dense>
              <v-col cols="12">
                <v-select
                  v-model="form.agency_id"
                  :items="options.agencies"
                  item-text="title"
                  item-value="id"
                  label="Sucursal"
                  outlined
                  class="mt-3"
                  :rules="[(v) => !!v || 'Es Requerido']"
                  dense
                ></v-select>
              </v-col>
              <v-col cols="12">
                <v-select
                  v-model="form.department_id"
                  :items="options.departments"
                  item-text="title"
                  item-value="id"
                  label="Departamento:(opcional)"
                  outlined
                  clearable
                  dense
                ></v-select>
              </v-col>
            </v-row>
            <v-subheader>Concepto y Motivo</v-subheader>
            <v-divider></v-divider>
            <v-row dense>
              <v-col cols="12">
                <v-combobox
                  label="Concepto"
                  :items="[
                    'COMPRA DE MAQUINARIA DIVERSA',
                    'PAPELERIA',
                    'LIMPIEZA',
                    'COMPRA DE LLANTAS PARA VENTA',
                    'PUBLICIDAD',
                    'IMPRENTA',
                    'SERVICIO DE MANTENIMIENTO',
                    'REFACCIONES',
                    'RIEGO',
                    'COMPRA DE LLANTAS PARA UNIDADES',
                    'SANITIZACION',
                    'SEGUROS',
                    'STOCK',
                    'VARIOS',
                    'OTRO',
                  ]"
                  dense
                  filled
                  outlined
                ></v-combobox>
              </v-col>
              <v-col cols="12">
                <v-textarea
                  v-model="form.reason"
                  label="Motivo de Solicitud:"
                  outlined
                  :rules="[(v) => !!v || 'Es Requerido']"
                  dense
                ></v-textarea>
              </v-col>
            </v-row>
          </v-row>
        </v-col>
        <v-col cols="12" md="8">
          <v-tabs
            v-model="tab"
            background-color="deep-purple accent-4"
            icons-and-text
            fixed-tabs
          >
            <v-tabs-slider></v-tabs-slider>
            <v-tab href="#concepts">
              Conceptos
              <v-icon>mdi-format-columns</v-icon>
            </v-tab>
            <v-tab
              href="#invoice"
              v-if="checkEstatus('verificado') || checkEstatus('facturado')"
            >
              Factura
              <v-icon>mdi-file-document</v-icon>
            </v-tab>
            <v-tab href="#messages" v-if="form.id">
              Mensajes
              <v-icon>mdi-message</v-icon>
            </v-tab>
          </v-tabs>
          <v-tabs-items v-model="tab" touchless>
            <v-tab-item value="concepts">
              <purchase-concepts :concepts.sync="form.concepts">
              </purchase-concepts>
              <v-list
                class="overline"
                color="grey lighten-3"
                elevation="1"
                dense
              >
                <v-subheader>Importes</v-subheader>
                <v-list-item dense>
                  <v-list-item-content class="pa-0">
                    Subtotal:
                  </v-list-item-content>
                  <v-list-item-action-text class="text-h6">
                    {{ Subtotal | currency }}
                  </v-list-item-action-text>
                </v-list-item>
                <v-list-item dense>
                  <v-list-item-content class="pa-0">
                    IVA:
                  </v-list-item-content>
                  <v-list-item-action-text class="text-h6">
                    <!-- <div class="d-flex flex-row align-center"> -->
                    <!-- <v-checkbox v-model="form.check_tax"></v-checkbox> -->
                    {{ Tax | currency }}
                    <!-- </div> -->
                  </v-list-item-action-text>
                </v-list-item>
                <v-list-item dense>
                  <v-list-item-content class="pa-0">
                    Total:
                  </v-list-item-content>
                  <v-list-item-action-text class="blue--text text-h5">
                    {{ Total | currency }}
                  </v-list-item-action-text>
                </v-list-item>
                <v-list-item dense>
                  <v-list-item-content class="pa-0">
                    Estatus Actual:
                  </v-list-item-content>
                  <v-list-item-action-text class="text-h5 green--text">
                    <div class="d-flex align-center">
                      {{ form.estatus ? form.estatus.title : "Sin Estatus" }}
                    </div>
                  </v-list-item-action-text>
                </v-list-item>
                <v-list-item dense>
                  <v-list-item-content class="pa-0">
                    Acciones:
                  </v-list-item-content>
                  <v-list-item-action-text>
                    <div
                      class="d-flex flex-wrap-reverse align-center justify-end"
                    >
                      <v-btn
                        v-show="Owner"
                        small
                        color="green"
                        class="ml-4"
                        @click="save"
                        :disabled="!valid"
                      >
                        Guardar
                      </v-btn>
                      <v-btn
                        v-show="
                          (checkEstatus('pendiente') ||
                            checkEstatus('denegar')) &&
                          $gate.allow('authorizePurchase', 'compras')
                        "
                        small
                        color="orange"
                        class="ml-4"
                        @click="changeEstatus('autorizado')"
                      >
                        Autorizar
                      </v-btn>
                      <v-btn
                        v-show="
                          (checkEstatus('pendiente') ||
                            checkEstatus('autorizado')) &&
                          $gate.allow('authorizePurchase', 'compras')
                        "
                        small
                        color="red darken-5"
                        class="ml-4"
                        @click="changeEstatus('denegar')"
                      >
                        Rechazar
                      </v-btn>
                      <v-btn
                        v-show="
                          checkEstatus('autorizado') &&
                          $gate.allow('validPurchase', 'compras')
                        "
                        small
                        color="blue darken-5"
                        class="ml-4"
                        @click="changeEstatus('verificado')"
                      >
                        Generar Consecutivo
                      </v-btn>
                    </div>
                  </v-list-item-action-text>
                </v-list-item>
              </v-list>
            </v-tab-item>
            <v-tab-item value="invoice">
              <purchase-order-document
                :purchaseId="form.id"
              ></purchase-order-document>
            </v-tab-item>
            <v-tab-item value="messages" v-if="form.id">
              <purchase-order-messages
                :purchaseId="form.id"
              ></purchase-order-messages>
            </v-tab-item>
          </v-tabs-items>
        </v-col>
      </v-row>
    </v-form>
  </v-card>
</template>

<script>
import PurchaseConcepts from "./PurchaseConcepts.vue";
import PurchaseOrderDocument from "../PurchaseOrderDocument.vue";
import PurchaseOrderMessages from "../PurchaseOrderMessages.vue";
export default {
  props: {
    value: {
      type: Boolean,
      default: true,
    },
    form: {
      require: true,
      type: Object,
      default: () => {
        return {};
      },
    },
  },
  mounted() {
    this.loadOptions(() => {});
  },
  components: {
    PurchaseConcepts,
    PurchaseOrderDocument,
    PurchaseOrderMessages,
  },
  data() {
    return {
      date: new Date().toISOString().substr(0, 10),
      menu: false,
      tab: null,
      condicion: "30 dias",
      fecha: "",
      options: {
        suppliers: [],
        agencies: [],
        departments: [],
        metodoPago: [],
        usoCFDI: [],
        formaPago: [],
      },
    };
  },
  computed: {
    isPPD() {
      return this.form.metodo_pago?.clave == "PPD";
    },
    valid: {
      get() {
        return this.value;
      },
      set(value) {
        this.$emit("input", value);
      },
    },
    Subtotal() {
      return (this.form.subtotal = this.form.concepts
        .map((item) => parseFloat(item.cost))
        .reduce((acc, crr) => acc + crr, 0));
    },
    Tax() {
      return (this.form.tax = this.form.concepts
        .map((item) => parseFloat(item.tax))
        .reduce((acc, crr) => acc + crr, 0));
      // return (this.form.tax = this.form.check_tax ? this.Subtotal * 0.16 : 0);
    },
    Total: {
      get: function () {
        return (this.form.total = this.Subtotal + this.Tax);
      },
      set: function (value) {},
    },
    Owner() {
      if (!this.form.id) {
        return true;
      } else {
        return this.form.elaborated
          ? this.form.elaborated.id == this.$gate.auth().id
          : true;
      }
    },
  },
  methods: {
    customFilter(item, queryText, itemText) {
      const textName = item.business_name.toLowerCase();
      const textRfc = item.rfc.toLowerCase();
      const textEmail = item.email.toLowerCase();
      const searchText = queryText.toLowerCase();

      return (
        textName.indexOf(searchText) > -1 ||
        textRfc.indexOf(searchText) > -1 ||
        textEmail.indexOf(searchText) > -1
      );
    },
    async loadOptions(cb) {
      const _this = this;
      await axios
        .get("/admin/purchase-order/resources/options")
        .then(function (response) {
          let {
            suppliers,
            agencies,
            departments,
            metodoPago,
            usoCFDI,
            formaPago,
          } = response.data.data;
          _this.options.suppliers = suppliers;
          _this.options.agencies = agencies;
          _this.options.departments = departments;
          _this.options.metodoPago = metodoPago;
          _this.options.usoCFDI = usoCFDI;
          _this.options.formaPago = formaPago;
        });
    },
    async changeEstatus(key = "pendiente") {
      const _this = this;
      await axios
        .put("/admin/purchase-order/" + _this.form.id, { estatus_key: key })
        .then(function (response) {
          _this.$store.commit("showSnackbar", {
            message: response.data.message,
            color: "success",
            duration: 3000,
          });
          _this.$eventBus.$emit("CHANGE_ESTATUS");
          _this.$eventBus.$emit("ORDERS_REFRESH");
        })
        .catch(function (error) {
          _this.$store.commit("hideLoader");
          if (error.response) {
            _this.$store.commit("showSnackbar", {
              message: error.response.data.message,
              color: "error",
              duration: 3000,
            });
            return false;
          } else if (error.request) {
            console.log(error.request);
          } else {
            console.log("Error", error.message);
          }
        });
    },
    save() {
      this.$emit("submit");
    },
    checkEstatus(estatus_key) {
      return this.form.estatus ? this.form.estatus.key == estatus_key : false;
    },
  },
  watch: {
    "form.uso_cfdi"(value) {
      this.form.uso_cfdi_id = value.clave;
    },
    "form.metodo_pago"(value) {
      if (value.clave == "PPD") {
        this.form.forma_pago = {
          clave: "99",
          description: "Por definir",
        };
      }
      this.form.metodo_pago_id = value.clave;
    },
    "form.forma_pago"(value) {
      this.form.forma_pago_id = value.clave;
    },
  },
};
</script>

<style></style>
