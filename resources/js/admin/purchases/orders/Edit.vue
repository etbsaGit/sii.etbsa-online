<template>
  <v-card>
    <v-card-title class="overline align-center">
      <v-icon left>mdi-file-document</v-icon> Detalle Orden de compra #
      {{ purchaseId.toString().padStart(5, 0) }}
      <v-spacer></v-spacer>
      <b>Acciones:</b>
      <v-btn v-if="canSave" class="ml-2" color="green " @click="update" dark>
        Guardar Cambios de OC
        <v-icon right>mdi-content-save</v-icon>
      </v-btn>
      <v-btn
        v-if="canPrint"
        class="ml-2"
        color="green"
        :href="`/admin/purchase-order/${purchaseId}/resources/print`"
        target="_blank"
        dark
      >
        Descargar OC PDF
        <v-icon right>mdi-file-pdf-box</v-icon>
      </v-btn>

      <v-btn
        v-if="canMarkAsSend"
        class="ml-2"
        color="black"
        @click="updateEstatusPurchase('por_facturar')"
        dark
      >
        Marcar OC Como Por Facturar <v-icon right>mdi-file-send-outline</v-icon>
      </v-btn>
      <v-btn
        v-if="canAutorize"
        class="ml-2"
        color="orange"
        @click="updateEstatusPurchase('autorizado')"
        dark
      >
        Marcar OC Autorizada <v-icon right>mdi-check</v-icon>
      </v-btn>
      <v-btn
        v-if="canVerify"
        class="ml-2"
        color="purple"
        @click="updateEstatusPurchase('verificado')"
        dark
      >
        Marcar OC VALIDA <v-icon right>mdi-check-all</v-icon>
      </v-btn>
      <v-btn
        v-if="canReject"
        class="ml-2"
        color="red"
        @click="updateEstatusPurchase('denegar')"
        dark
      >
        Rechazar <v-icon right>mdi-close-octagon</v-icon>
      </v-btn>
      <v-btn class="ml-2" color="blue" @click="dialogMessages = true" dark>
        Mensajes <v-icon right>mdi-forum</v-icon>
      </v-btn>
    </v-card-title>
    <v-card-subtitle>
      <b>Estatus Actual:</b>
      <v-chip label color="primary" class="overline" dark>
        {{ form.estatus ? form.estatus.title : "" }}
      </v-chip>
    </v-card-subtitle>

    <v-divider></v-divider>
    <v-card-text>
      <v-form ref="form_info" v-model="validFormInfo" lazy-validation>
        <v-row dense>
          <v-col cols="12" md="9">
            <v-card color="grey lighten-2">
              <v-alert
                v-if="HasSupplier"
                v-model="alertSupplier"
                text
                outlined
                color="deep-orange"
                icon="mdi-alert"
                border="left"
                dark
              >
                Este Proveedor no Cuenta con <b>Numero de Equip</b>
              </v-alert>
              <v-card-title class="pb-0 overline">
                <v-autocomplete
                  v-model="form.supplier"
                  :items="options.suppliers"
                  item-text="business_name"
                  item-value="id"
                  label="Proveedor:"
                  placeholder="Buscar por #Equip | Nombre | RFC | Email"
                  persistent-placeholder
                  persistent-hint
                  :hint="
                    HasSupplier
                      ? `RFC: ${form.supplier.rfc} #Equip ${form.supplier.code_equip}`
                      : ''
                  "
                  :filter="customFilter"
                  :rules="[(v) => !!v || 'Es Requerido']"
                  return-object
                  clearable
                  outlined
                  filled
                  dense
                >
                  <template v-slot:item="{ item }">
                    <v-list-item-content>
                      <v-list-item-title
                        class="text-uppercase font-weight-bold"
                      >
                        {{ item.business_name }}
                      </v-list-item-title>
                      <v-list-item-subtitle class="text-subtitle-2">
                        {{
                          `#Equip:${item.code_equip || "S/R"}  RFC:${item.rfc}`
                        }}
                      </v-list-item-subtitle>
                      <v-list-item-subtitle>
                        {{ item.email }}
                      </v-list-item-subtitle>
                    </v-list-item-content>
                  </template>
                </v-autocomplete>
              </v-card-title>
              <v-card-text class="pb-0">
                <v-row dense>
                  <v-radio-group v-model="form.purchase_type_id" row mandatory>
                    <template v-slot:label>
                      <div class="title font-weight-bold">
                        Tipo de Orden de Compra:
                      </div>
                    </template>
                    <v-radio
                      v-for="(item, index) in options.purchase_types"
                      :key="index"
                      :label="item.name"
                      :value="item.id"
                    ></v-radio>
                  </v-radio-group>
                </v-row>
                <v-row dense>
                  <v-col cols="12" md="4">
                    <v-select
                      v-model="form.purchase_concept"
                      label="Concepto de Compra"
                      :items="PurchaseConcept"
                      :rules="[(v) => !!v || 'Es requerido']"
                      item-value="id"
                      item-text="name"
                      outlined
                      return-object
                      dense
                    ></v-select>
                  </v-col>
                  <v-col cols="12" md="4">
                    <v-select
                      v-model="form.payment_condition"
                      label="Condiciones de Pago"
                      :items="options.payment_condition"
                      :rules="[(v) => !!v || 'Es requerido']"
                      outlined
                      readonly
                      dense
                    ></v-select>
                  </v-col>
                  <v-col cols="12" md="4">
                    <v-select
                      v-model="form.agency_id"
                      label="Embarque a:"
                      :items="options.agencies"
                      :rules="[(v) => !!v || 'Es requerido']"
                      item-value="id"
                      item-text="title"
                      outlined
                      dense
                    ></v-select>
                  </v-col>
                </v-row>
              </v-card-text>
              <v-card-title class="pt-0">
                Articulos
                <v-spacer></v-spacer>

                <purchase-import-csv-products
                  v-model="form.products"
                ></purchase-import-csv-products>
                <v-btn icon @click="form.products = []">
                  <v-icon color="red">mdi-trash-can</v-icon>
                </v-btn>
              </v-card-title>
              <v-card-text>
                <purchase-products-table
                  :dialogForm="dialogSupplierProducts"
                  @edit="dialogSupplierProducts = true"
                  @close="dialogSupplierProducts = false"
                  :items.sync="form.products"
                ></purchase-products-table>
              </v-card-text>
              <v-card-actions>
                <v-btn text color="blue" @click="dialogSupplierProducts = true">
                  <v-icon left>mdi-plus</v-icon> Agregar Articulo o Servicio
                </v-btn>
                <v-spacer />
                <!-- BTN Adjuntar Cotizacion -->
                <v-dialog
                  transition="dialog-top-transition"
                  max-width="600"
                  persistent
                >
                  <template v-slot:activator="{ on, attrs }">
                    <v-btn dark color="blue" small v-bind="attrs" v-on="on">
                      Adjuntar Cotizacion
                      <v-icon right>mdi-file-pdf-box</v-icon>
                    </v-btn>
                  </template>
                  <template v-slot:default="dialog">
                    <v-card>
                      <v-card-title
                        class="white--text secondary title text-uppercase"
                      >
                        Cargar Cotizacion
                        <v-spacer />
                        <v-icon
                          large
                          color="red"
                          @click="(dialog.value = false), (form.file = [])"
                        >
                          mdi-close-circle
                        </v-icon>
                      </v-card-title>
                      <v-card-text class="pt-4">
                        <v-file-input
                          v-model="form.file"
                          outlined
                          placeholder="Seleccionar Archivo"
                          label="Archvio"
                          persistent-placeholder
                          multiple
                        ></v-file-input>
                      </v-card-text>
                      <v-card-actions>
                        <v-btn
                          block
                          dark
                          color="primary"
                          @click="dialog.value = false"
                        >
                          Adjuntar
                        </v-btn>
                      </v-card-actions>
                    </v-card>
                  </template>
                </v-dialog>
                <!-- END Adjuntar Cotizacion -->
              </v-card-actions>
              <v-card-text v-if="form.file.length > 0">
                <purchase-files-list
                  :files.sync="form.file"
                ></purchase-files-list>
              </v-card-text>
            </v-card>

            <v-card flat>
              <v-row dense>
                <v-col cols="12" md="6">
                  <v-card-title>Justificacion</v-card-title>
                  <v-card-text class="overline">
                    <v-textarea
                      v-model="form.observation"
                      outlined
                      filled
                      placeholder="Describir el motivo de compra"
                      hint="Justificacion de la compra*"
                      :rules="[(v) => !!v || 'Es requerido']"
                      persistent-hint
                    ></v-textarea>
                  </v-card-text>
                </v-col>
                <v-col cols="12" md="6">
                  <v-card-title>Nota a Proveedor</v-card-title>
                  <v-card-text class="overline">
                    <v-textarea
                      v-model="form.note"
                      outlined
                      filled
                      placeholder="Describir a quien van dirigidos los Productos o servicios"
                      hint="Nota que ira en la OC para conocimiento del proveedor*"
                      persistent-hint
                    ></v-textarea>
                  </v-card-text>
                </v-col>
              </v-row>
            </v-card>
          </v-col>
          <v-col cols="12" md="3">
            <v-card color="grey lighten-3">
              <v-card-text class="pa-0">
                <v-card-title>
                  Cargo a Sucursales
                  <v-spacer></v-spacer>
                  <v-btn
                    color="primary"
                    dark
                    @click="dialogAddCharge = true"
                    small
                  >
                    Asignar
                    <v-icon right>mdi-plus-thick</v-icon>
                  </v-btn>
                </v-card-title>
                <purchase-charge-table
                  :dialogForm="dialogAddCharge"
                  @edit="dialogAddCharge = true"
                  @close="dialogAddCharge = false"
                  :items.sync="form.charges"
                  :agencies="options.agencies"
                  :departments="options.departments"
                ></purchase-charge-table>
              </v-card-text>
              <v-card-text class="pa-0">
                <v-card-title>
                  Facturacion
                  <v-spacer></v-spacer>
                  <v-btn
                    @click="dialogConfigInvoice = true"
                    color="purple"
                    dark
                    small
                  >
                    Configurar
                    <v-icon right>mdi-settings</v-icon>
                  </v-btn>
                </v-card-title>
                <purchase-invoice-table
                  v-if="form.purchase_concept"
                  :dialogForm="dialogConfigInvoice"
                  @close="dialogConfigInvoice = false"
                  :form.sync="form.invoice"
                  :usocfdi="PurchaseConceptUsoCfdi"
                ></purchase-invoice-table>
              </v-card-text>
              <v-card-text class="pa-0">
                <v-card-title>Monto y Retenciones</v-card-title>
                <purchase-amounts-table
                  :items="form.products"
                  :form.sync="form.amounts"
                ></purchase-amounts-table>
              </v-card-text>
            </v-card>
          </v-col>
        </v-row>
      </v-form>
    </v-card-text>
  </v-card>
</template>
<script>
import PurchaseAmountsTable from "./PurchaseAmountsTable.vue";
import PurchaseChargeTable from "./PurchaseChargeTable.vue";
import PurchaseProductsTable from "./PurchaseProductsTable.vue";
import PurchaseInvoiceTable from "./PurchaseInvoiceTable.vue";
import PurchaseFilesList from "./PurchaseFilesList.vue";
import SearchClvProduct from "./SearchClvProduct.vue";
import PurchaseImportCsvProducts from "./PurchaseImportCsvProducts.vue";
export default {
  name: "PurchaseOrderEdit",
  components: {
    PurchaseProductsTable,
    PurchaseAmountsTable,
    PurchaseChargeTable,
    PurchaseInvoiceTable,
    PurchaseFilesList,
    SearchClvProduct,
    PurchaseImportCsvProducts,
  },
  props: {
    purchaseId: {
      type: Number | String,
      require: true,
    },
  },
  data() {
    return {
      canUpdate: false,
      alertSupplier: false,
      validFormInfo: true,
      dialogSupplierProducts: false,
      dialogAddCharge: false,
      dialogConfigInvoice: false,
      dialog: false,
      form: {
        file: [],
        charges: [],
        products: [],
        invoice: {
          uso_cfdi: null,
          metodo_pago: null,
          forma_pago: null,
        },
        amounts: {
          subtotal: 0,
          tax: 0,
          discount: 0,
          with_tax: false,
          with_iva_retenido: false,
          tax_iva_retenido: 0,
          with_isr: false,
          tax_isr: 0,
          with_retencion_cedular: false,
          tax_retencion_cedular: 0,
          with_retencion_125: false,
          tax_retencion_125: 0,
          with_flete: false,
          tax_flete: 0,
          total: 0,
        },
        purchase_concept: {},
        supplier: null,
        purchase_type_id: 1,
        payment_condition: 30,
        agency_id: null,
        note: "",
        observation: "",
      },
      options: {
        suppliers: [],
        agencies: [],
        departments: [],
        payment_condition: [
          { text: "Contado", value: 5 },
          { text: "8 Dias", value: 8 },
          { text: "15 Dias", value: 15 },
          { text: "25 Dias", value: 25 },
          { text: "30 Dias", value: 30 },
          { text: "60 Dias", value: 60 },
          { text: "90 Dias", value: 90 },
        ],
        purchase_concept: [],
        purchase_types: [],
      },
    };
  },
  mounted() {
    this.loadOptions();
    this.$store.commit("setBreadcrumbs", [
      { label: "Ordenes de Compra", to: { name: "purchase.list" } },
      { label: "Detalle", name: "" },
    ]);
    this.loadPurchaseEdit();
  },
  watch: {
    form: {
      handler() {
        this.canUpdate = true;
      },
      deep: true,
      flush: "post",
    },
    "form.purchase_type_id": {
      handler(val) {
        const _this = this;
        if (!!_this.form.purchase_concept)
          if (_this.form.purchase_concept.purchase_type_id !== val) {
            _this.form.purchase_concept = null;
          }
      },
      flush: "post",
    },
    "form.purchase_concept": {
      handler(val) {
        const _this = this;
        if (!!val) {
          _this.form.invoice.uso_cfdi = val.usocfdi.some((item) => {
            return _this.form.invoice?.uso_cfdi
              ? item.clave === _this.form.invoice.uso_cfdi.clave
              : false;
          })
            ? _this.form.invoice.uso_cfdi
            : null;
        }
      },
      flush: "post",
    },
    "form.supplier": {
      handler(newVal) {
        const _this = this;
        if (_this.HasSupplier && newVal) {
          _this.form.payment_condition = _this.form.supplier.credit_days || 30;
          _this.alertSupplier = !!!_this.form.supplier.code_equip;
        }
      },
      flush: "post",
    },
  },
  methods: {
    async loadPurchaseEdit() {
      const _this = this;
      let { data: response } = await axios.get(
        `/admin/purchase-order/${_this.purchaseId}/edit`
      );
      _this.form = response.data.purchase;
      _this.canUpdate = false;
    },
    FormValidate() {
      let valid = true;
      let message = "";
      const validations = [
        {
          condition: !this.$refs.form_info.validate(),
          message: "Faltan Campos por Llenar",
        },
        {
          condition: this.form.products.length <= 0,
          message: "Agrege al Menos un Articulo",
        },
        {
          condition: this.form.charges.length <= 0,
          message: "Agrege al Menos un Cargo a sucursal",
        },
        {
          condition:
            this.form.charges.reduce((acc, curr) => acc + curr.percent, 0) !==
            100,
          message: "La suma de los Porcentaje de cargos debe ser 100%",
        },
        {
          condition: this.form.charges
            .map((item) => `${item.agency.id}-${item.department.id}`)
            .some((key, index, keys) => keys.indexOf(key) !== index),
          message: "Duplicidad en Cargos (Agencia-Departamento)",
        },
        {
          condition:
            !this.form.invoice.uso_cfdi ||
            !this.form.invoice.metodo_pago ||
            !this.form.invoice.forma_pago,
          message: "Configure los Datos de Facturacion",
        },
      ];

      validations.some((validation) => {
        if (validation.condition) {
          valid = false;
          message = validation.message;
          return true; // Termina la iteración cuando se encuentra una validación que falla
        }
      });

      if (!valid) {
        this.$store.commit("showSnackbar", {
          message: message,
          color: "error",
          duration: 3000,
        });
      }

      return valid;
    },
    getPayload() {
      const {
        amounts,
        file,
        products,
        charges,
        invoice: {
          uso_cfdi: { clave: uso_cfdi },
          metodo_pago: { clave: metodo_pago },
          forma_pago: { clave: forma_pago },
        },
        supplier: { id: supplier_id },
        purchase_concept: { id: purchase_concept_id },
        ...rest
      } = this.form;
      return {
        charges: charges.map(
          ({
            agency: { id: agency_id },
            department: { id: department_id },
            percent,
          }) => {
            return { agency_id, department_id, percent };
          }
        ),
        products: products.map(
          ({
            claveProduct: { c_ClaveProdServ },
            unit: { id: unit_id },
            ...rest
          }) => {
            return { c_ClaveProdServ, unit_id, ...rest };
          }
        ),
        file,
        supplier_id,
        uso_cfdi,
        metodo_pago,
        forma_pago,
        purchase_concept_id,
        ...amounts,
        ...rest,
      };
    },
    async update() {
      const _this = this;
      if (!_this.FormValidate()) return;
      const formData = new FormData();
      const payload = _this.getPayload();

      console.log(payload.charges);
      payload.file.forEach((item) => {
        formData.append("file[]", item);
      });
      formData.append("payload", JSON.stringify(payload));
      formData.append("_method", "PUT");

      try {
        const {
          data: {
            data: { purchaseOrderUpdated },
            message,
          },
        } = await axios.post(
          `/admin/purchase-order/${_this.purchaseId}`,
          formData
        );
        _this.form = purchaseOrderUpdated;
        _this.$store.commit("showSnackbar", {
          message: message,
          color: "success",
          duration: 3000,
        });
      } catch (error) {
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
      } finally {
        _this.$store.commit("hideLoader");
      }
    },
    async loadOptions(cb) {
      const _this = this;
      await axios
        .get("/admin/purchase-order/resources/options")
        .then(function (response) {
          let { suppliers, agencies, departments, purchase_types } =
            response.data.data;
          _this.options.suppliers = suppliers;
          _this.options.agencies = agencies;
          _this.options.departments = departments;
          _this.options.purchase_types = purchase_types;
        });
    },
    customFilter(item, queryText, itemText) {
      const textEquip = item.code_equip ? item.code_equip.toLowerCase() : "";
      const textName = item.business_name
        ? item.business_name.toLowerCase()
        : "";
      const textRfc = item.rfc ? item.rfc.toLowerCase() : "";
      const textEmail = item.email ? item.email.toLowerCase() : "";
      const searchText = queryText.toLowerCase();

      return (
        textEquip.indexOf(searchText) > -1 ||
        textName.indexOf(searchText) > -1 ||
        textRfc.indexOf(searchText) > -1 ||
        textEmail.indexOf(searchText) > -1
      );
    },
    // Funciones de Cambair Estatus
    async updateEstatusPurchase(estatus_key = "pendiente") {
      const _this = this;
      let payload = {
        estatus_key: estatus_key,
      };
      await axios
        .post(
          `/admin/purchase-order/update-estatus/${_this.purchaseId}`,
          payload
        )
        .then(function (response) {
          _this.$store.commit("showSnackbar", {
            message: response.data.message,
            color: "success",
            duration: 3000,
          });
          _this.$eventBus.$emit("ORDERS_REFRESH");
          _this.loadPurchaseEdit();
          // _this.$eventBus.$emit("CLOSE_DIALOG");
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
    async updateDateToPayment(invoice_id = null) {
      const _this = this;

      let payload = {
        date_to_payment: this.form.invoice_info.date_to_payment,
        payment_date: this.form.invoice_info.payment_date,
      };
      await axios
        .post(`/admin/purchase-order/store-invoice/${invoice_id}`, payload)
        .then(function (response) {
          _this.$store.commit("showSnackbar", {
            message: response.data.message,
            color: "success",
            duration: 3000,
          });
          _this.loadPurchaseEdit();
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
  },
  // Coputed que controlan lo botones de Accion
  computed: {
    minHeight() {
      const height = "82vh";
      return `calc(${height} - ${this.$vuetify.application.top}px)`;
    },
    PurchaseConcept() {
      const _this = this;
      let concept_by_type = _this.options.purchase_types.find(
        (e) => e.id == _this.form.purchase_type_id
      );
      return concept_by_type != undefined
        ? concept_by_type.purchase_concept
        : [];
    },
    PurchaseConceptUsoCfdi() {
      return this.form.purchase_concept.usocfdi ?? [];
    },
    HasSupplier() {
      return this.form.supplier
        ? Object.keys(this.form.supplier).length > 0
        : false;
    },
    canSave() {
      let estatus = {
        pendiente: true,
        denegar: true,
      };
      if (this.$gate.auth().id == this.form.created_by && this.canUpdate) {
        return !!this.form.estatus ? !!estatus[this.form.estatus.key] : false;
      } else {
        return false;
      }
    },
    canPrint() {
      let estatus = {
        autorizado: true,
        programar_pago: true,
        pagada: true,
        por_facturar: true,
        por_pagar: true,
      };
      return this.form.estatus ? estatus[this.form.estatus.key] : false;
    },
    canMarkAsSend() {
      let estatus = {
        autorizado: true,
      };
      return this.form.estatus ? estatus[this.form.estatus.key] : false;
    },
    canAutorize() {
      let estatus = {
        verificado: true,
      };
      if (this.$gate.allow("authorizePurchase", "compras")) {
        return !!this.form.estatus ? !!estatus[this.form.estatus.key] : false;
      } else {
        return false;
      }
    },
    canReject() {
      let estatus = {
        autorizado: false,
        pendiente: true,
        verificado: true,
      };
      if (this.$gate.allow("rejectPurchase", "compras")) {
        return !!this.form.estatus ? !!estatus[this.form.estatus.key] : false;
      } else {
        return false;
      }
    },
    canVerify() {
      let estatus = {
        pendiente: true,
        denegar: true,
      };
      if (this.$gate.allow("validPurchase", "compras")) {
        return !!this.form.estatus ? !!estatus[this.form.estatus.key] : false;
      } else {
        return false;
      }
    },
    InvoiceTableShow() {
      let estatus = {
        programar_pago: true,
        por_pagar: true,
        pagada: true,
        por_facturar: true,
      };
      return !!this.form.estatus ? !!estatus[this.form.estatus.key] : false;
    },
    canShedulePaymentDate() {
      let estatus = {
        programar_pago: true,
        por_pagar: true,
      };
      if (this.$gate.allow("shedulePaymentDate", "compras")) {
        return !!this.form.estatus ? !!estatus[this.form.estatus.key] : false;
      } else {
        return false;
      }
    },
    markAsPaid() {
      let estatus = {
        por_pagar: true,
      };
      if (this.$gate.allow("markAsPaidIvoice", "compras")) {
        return !!this.form.estatus ? !!estatus[this.form.estatus.key] : false;
      } else {
        return false;
      }
    },
    ReadOnly() {
      //  $gate.allow('authorizePurchase', 'compras')
      let estatus = {
        validado: true,
        por_facturar: true,
        programar_pago: true,
        por_pagar: true,
        pagada: true,
      };
      return !!this.form.estatus ? !!estatus[this.form.estatus.key] : false;
    },
  },
};
</script>
