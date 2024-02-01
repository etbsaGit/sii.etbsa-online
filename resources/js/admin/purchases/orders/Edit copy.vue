<template>
  <v-card flat>
    <v-card-title class="overline">
      <v-icon left>mdi-file-document</v-icon> Acciones

      <v-spacer></v-spacer>
      <v-btn class="ml-2" color="blue" @click="dialogMessages = true" dark>
        Mensajes <v-icon right>mdi-forum</v-icon>
      </v-btn>

      <v-btn
        v-if="canSave"
        class="ml-2"
        color="green "
        @click="updatePurchase"
        dark
      >
        Guardar Cambios
        <v-icon right>mdi-content-save</v-icon>
      </v-btn>

      <v-btn
        v-if="canAutorize"
        class="ml-2"
        color="orange"
        @click="updateEstatusPurchase('autorizado')"
        dark
      >
        Autorizar <v-icon right>mdi-check</v-icon>
      </v-btn>

      <v-btn
        v-if="canReject"
        class="ml-2"
        color="red"
        @click="updateEstatusPurchase('denegar')"
        dark
      >
        Rechazar <v-icon right>mdi-window-close</v-icon>
      </v-btn>

      <v-btn
        v-if="canVerify"
        class="ml-2"
        color="purple"
        @click="updateEstatusPurchase('verificado')"
        dark
      >
        Validar OC <v-icon right>mdi-check-all</v-icon>
      </v-btn>

      <!-- <v-btn
        v-if="form.estatus.key == 'por_facturar'"
        class="ml-2"
        color="indigo"
        @click="dialogInvoice = true"
        dark
      >
        Registrar Factura <v-icon right>mdi-receipt</v-icon>
      </v-btn> -->

      <!-- <v-dialog
        ref="dialog_calendar"
        v-model="dialogCalendar"
        :return-value.sync="form.invoice_info.date_to_payment"
        persistent
        transition="scale-transition"
        width="324px"
        v-if="canShedulePaymentDate"
      >
        <template v-slot:activator="{ on, attrs }">
          <v-btn color="pink" v-bind="attrs" v-on="on" class="ml-2" dark>
            {{
              form.invoice_info.date_to_payment
                ? `Pagar el: ${$appFormatters.formatDate(
                    form.invoice_info.date_to_payment,
                    "ll"
                  )}`
                : "Programar Fecha de Pago"
            }}
            <v-icon right>mdi-clock-check-outline</v-icon>
          </v-btn>
        </template>
        <v-date-picker
          v-model="form.invoice_info.date_to_payment"
          scrollable
          :min="new Date().toISOString().substr(0, 10)"
        >
          <v-spacer></v-spacer>
          <v-btn text color="primary" @click="dialogCalendar = false">
            Cancel
          </v-btn>
          <v-btn
            text
            color="primary"
            @click="
              $refs.dialog_calendar.save(form.invoice_info.date_to_payment),
                updateDateToPayment()
            "
          >
            Confirmar
          </v-btn>
        </v-date-picker>
      </v-dialog> -->

      <!-- <v-dialog
        ref="dialog_payment"
        v-model="dialogPayment"
        :return-value.sync="form.invoice_info.payment_date"
        persistent
        transition="scale-transition"
        width="324px"
        v-if="markAsPaid"
      >
        <template v-slot:activator="{ on, attrs }">
          <v-btn color="cyan" v-bind="attrs" v-on="on" class="ml-2" dark>
            {{
              form.invoice_info.payment_date
                ? `Pagada el: ${$appFormatters.formatDate(
                    form.invoice_info.payment_date,
                    "ll"
                  )}`
                : "Fecha Pago Factura"
            }}
            <v-icon right>mdi-file-check-outline</v-icon>
          </v-btn>
        </template>
        <v-date-picker
          v-model="form.invoice_info.payment_date"
          scrollable
          :min="new Date().toISOString().substr(0, 10)"
        >
          <v-spacer></v-spacer>
          <v-btn text color="primary" @click="dialogPayment = false">
            Cancel
          </v-btn>
          <v-btn
            text
            color="primary"
            @click="
              $refs.dialog_payment.save(form.invoice_info.payment_date),
                updateDateToPayment()
            "
          >
            Confirmar
          </v-btn>
        </v-date-picker>
      </v-dialog> -->
    </v-card-title>
    <v-card-subtitle>
      Estatus:
      <v-chip label>
        {{ form.estatus.title }}
      </v-chip>
    </v-card-subtitle>

    <v-divider></v-divider>
    <v-card-text>
      <v-form ref="form_info" v-model="validFormInfo" lazy-validation>
        <v-row dense>
          <v-col cols="12" md="9">
            <v-card color="grey lighten-3" min-height="200">
              <v-card-title class="pb-0">
                <v-autocomplete
                  v-model="form.supplier"
                  :items="options.suppliers"
                  item-text="business_name"
                  item-value="id"
                  label="Proveedor:"
                  placeholder="Buscar por Nombre | RFC | Email"
                  persistent-placeholder
                  persistent-hint
                  :hint="`RFC: ${form.supplier.rfc || ''}`"
                  :filter="customFilter"
                  :rules="[(v) => !!v || 'Es Requerido']"
                  return-object
                  filled
                  outlined
                  dense
                  :readonly="ReadOnly"
                >
                  <template v-slot:item="data">
                    <v-list-item-content>
                      <v-list-item-title v-text="data.item.business_name" />
                      <v-list-item-subtitle v-text="data.item.rfc" />
                      <v-list-item-subtitle
                        v-text="`Email:${data.item.email}`"
                      />
                    </v-list-item-content>
                  </template>
                </v-autocomplete>
              </v-card-title>
              <v-card-title>Otros Datos</v-card-title>
              <v-card-text class="pb-0">
                <v-row dense>
                  <v-col cols="12" md="4">
                    <v-combobox
                      v-model="form.purchase_concept"
                      label="Concepto de Compra"
                      :items="options.purchase_concept"
                      item-value="id"
                      item-text="name"
                      outlined
                      dense
                      :readonly="ReadOnly"
                    ></v-combobox>
                  </v-col>
                  <v-col cols="12" md="4">
                    <v-select
                      v-model="form.payment_condition"
                      label="Condiciones de Pago"
                      :items="[
                        { text: '8 Dias', value: 8 },
                        { text: '15 Dias', value: 15 },
                        { text: '30 Dias', value: 30 },
                        { text: '60 Dias', value: 60 },
                        { text: '90 Dias', value: 90 },
                      ]"
                      outlined
                      dense
                      :readonly="ReadOnly"
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
                <v-dialog transition="dialog-top-transition" max-width="600">
                  <template v-if="!ReadOnly" v-slot:activator="{ on, attrs }">
                    <v-btn icon v-bind="attrs" v-on="on">
                      <v-icon>mdi-file-import-outline</v-icon>
                    </v-btn>
                  </template>
                  <template v-slot:default="dialog">
                    <v-card>
                      <v-card-title
                        class="white--text primary title text-uppercase"
                      >
                        Importar Articulos
                      </v-card-title>
                      <v-container fluid>
                        <import-csv-component
                          v-if="dialog.value"
                          @input="inputImportCsv"
                          :map-fields="mapFields"
                          loadBtnText="Cargar CSV"
                          submitBtnText="Importar"
                          :callback="loadImportCb"
                          :catch="loadImportCatch"
                        ></import-csv-component>
                      </v-container>
                      <v-card-actions class="justify-end">
                        <v-btn text @click="dialog.value = false">Cerrar</v-btn>
                      </v-card-actions>
                    </v-card>
                  </template>
                </v-dialog>
                <v-btn :disabled="ReadOnly" icon @click="form.concepts = []">
                  <v-icon color="red">mdi-delete-empty-outline</v-icon>
                </v-btn>
              </v-card-title>
              <v-card-text>
                <purchase-concept-table
                  :dialogForm="dialogConcepts"
                  @edit="dialogConcepts = true"
                  @close="dialogConcepts = false"
                  :items.sync="form.concepts"
                  :read-only="ReadOnly"
                ></purchase-concept-table>
              </v-card-text>
              <v-card-actions>
                <v-btn
                  :disabled="ReadOnly"
                  text
                  color="blue"
                  @click="dialogConcepts = true"
                >
                  <v-icon left>mdi-plus</v-icon> Agregar Articulo o Servicio
                </v-btn>
              </v-card-actions>
            </v-card>
            <v-card flat>
              <v-row dense>
                <v-col cols="12" md="6">
                  <v-card-title>Observaciones</v-card-title>
                  <v-card-text class="overline">
                    <v-textarea
                      v-model="form.observation"
                      outlined
                      filled
                      placeholder="Describir a quien van dirigidos los Productos o servicios"
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
                      placeholder="Instrucciones para el Proveedor"
                      hint="Nota que ira en la OC para conocimiento del proveedor*"
                      persistent-hint
                    ></v-textarea>
                  </v-card-text>
                </v-col>
              </v-row>
              <!-- Invoices Table -->
              <v-row v-if="InvoiceTableShow">
                <v-col cols="12">
                  <invoice-table
                    :purchase-id="purchaseId"
                    :items="form.invoice_info"
                  ></invoice-table>
                </v-col>
              </v-row>
            </v-card>
          </v-col>
          <v-col cols="12" md="3">
            <v-card color="grey lighten-3" min-height="200">
              <v-card-title>
                Cargos a Sucursal
                <v-spacer></v-spacer>
                <v-btn
                  v-if="!ReadOnly"
                  icon
                  @click="!ReadOnly ? (dialogAddCharge = true) : null"
                >
                  <v-icon color="primary">mdi-plus-thick</v-icon>
                </v-btn>
              </v-card-title>
              <v-card-text class="px-0">
                <purchase-charge-table
                  :dialogForm="dialogAddCharge"
                  @edit="!ReadOnly ? (dialogAddCharge = true) : null"
                  @close="dialogAddCharge = false"
                  :items.sync="form.charges"
                  :read-only="ReadOnly"
                  :agencies="options.agencies"
                  :departments="options.departments"
                ></purchase-charge-table>
              </v-card-text>
              <v-card-title>
                Datos Facturacion
                <v-spacer></v-spacer>
                <v-btn
                  v-if="!ReadOnly"
                  icon
                  @click="!ReadOnly ? (dialogConfigInvoice = true) : null"
                >
                  <v-icon>mdi-settings</v-icon>
                </v-btn>
              </v-card-title>
              <v-card-text class="px-0">
                <purchase-invoice-table
                  v-if="form.purchase_concept"
                  :dialogForm="dialogConfigInvoice"
                  @close="dialogConfigInvoice = false"
                  :form.sync="form.invoice"
                  :usocfdi.sync="form.purchase_concept.usocfdi"
                ></purchase-invoice-table>
              </v-card-text>

              <v-card-title>Resumen Orden</v-card-title>
              <v-card-text class="px-0">
                <purchase-amounts-table
                  :items="form.concepts"
                  :form.sync="form.amounts"
                ></purchase-amounts-table>
              </v-card-text>
            </v-card>
          </v-col>
        </v-row>
      </v-form>
    </v-card-text>

    <dialog-component
      :show="dialogMessages"
      @close="dialogMessages = false"
      :maxWidth="600"
      closeable
      title="Mensajes"
    >
      <purchase-order-messages
        :purchaseId="purchaseId"
      ></purchase-order-messages>
    </dialog-component>
  </v-card>
</template>
<script>
import DialogComponent from "../../components/DialogComponent.vue";
import ImportCsvComponent from "../../components/ImportCsvComponent.vue";
import PurchaseOrderMessages from "../components/PurchaseOrderMessages.vue";
import PurchaseAmountsTable from "./PurchaseAmountsTable.vue";
import PurchaseChargeTable from "./PurchaseChargeTable.vue";
import PurchaseConceptTable from "./PurchaseProductsTable.vue";
import PurchaseInvoicePaymentTable from "./PurchaseInvoicePaymentTable.vue";
import PurchaseInvoiceTable from "./PurchaseInvoiceTable.vue";
import InvoiceTable from "./InvoiceTable.vue";
export default {
  name: "PurchaseOrderShow",
  components: {
    PurchaseConceptTable,
    PurchaseAmountsTable,
    PurchaseChargeTable,
    PurchaseInvoiceTable,
    ImportCsvComponent,
    PurchaseOrderMessages,
    PurchaseInvoicePaymentTable,
    InvoiceTable,
    DialogComponent,
  },
  props: {
    purchaseId: {
      require: true,
      type: Number,
    },
  },
  data() {
    return {
      dialogPayment: false,
      dialogCalendar: false,
      validFormInfo: true,
      parseCsv: null,
      mapFields: [
        { key: "name", label: "Nombre Articulo" },
        { key: "description", label: "Descripcion" },
        { key: "unit", label: "Unidad" },
        { key: "price", label: "Precio" },
        { key: "discount", label: "Descuento" },
      ],
      dialogMessages: false,
      dialogInvoice: false,
      dialogConcepts: false,
      dialogAddCharge: false,
      dialogAddCharge: false,
      dialogConfigInvoice: false,
      dialog: false,
      valid: true,
      form: {
        charges: [],
        concepts: [],
        invoice: {
          uso_cfdi: {},
          metodo_pago: {},
          forma_pago: {},
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
          total: 0,
        },
        check_tax: false,
        supplier_id: null,
        supplier: {},
        estatus: { title: "Pendiente" },
        estatus_id: null,
        purchase_concept: null,
        deliver_date: new Date().toISOString().substr(0, 10),
        payment_condition: "30 dias",
        agency_id: null,
        invoice_info: {
          folio_fiscal: "",
          folio: "",
          invoice_date: "",
          amount: 0,
          date_to_payment: null,
          payment_date: null,
        },
        colors: {
          pendiente: "blue",
          autorizado: "orange",
          denegar: "red",
          verificado: "purple",
          programar_pago: "green",
          por_pagar: "pink",
          pagada: "cyan",
          por_facturar: "brown darken-4",
        },
      },
      options: {
        suppliers: [],
        agencies: [],
        departments: [],
        purchase_concept: [],
      },
    };
  },
  mounted() {
    const _this = this;
    this.loadOptions();
    this.$store.commit("setBreadcrumbs", [
      { label: "Ordenes de Compra", to: { name: "purchase.list" } },
      { label: "Detalle", name: "" },
    ]);
    _this.loadPurchaseEdit();
    _this.$eventBus.$on("ORDERS_REFRESH", () => {
      _this.loadPurchaseEdit();
    });
  },
  computed: {
    minHeight() {
      const height = "82vh";
      return `calc(${height} - ${this.$vuetify.application.top}px)`;
    },
    canSave() {
      let estatus = {
        pendiente: true,
        denegar: true,
      };
      if (this.$gate.auth().id == this.form.created_by) {
        return !!this.form.estatus ? !!estatus[this.form.estatus.key] : false;
      } else {
        return false;
      }
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
        autorizado: true,
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
  methods: {
    async loadPurchaseEdit() {
      this.form = await axios
        .get(`/admin/purchase-order/${this.purchaseId}/edit`)
        .then((response) => {
          return { ...response.data.data };
        });
    },

    async updatePurchase() {
      if (!this.$refs.form_info.validate()) return;
      if (this.form.concepts.length <= 0)
        return this.$store.commit("showSnackbar", {
          message: "Agrege al Menos un Articulo",
          color: "error",
          duration: 3000,
        });
      if (this.form.charges.length <= 0)
        return this.$store.commit("showSnackbar", {
          message: "Agrege al Menos un Cargo a sucursal",
          color: "error",
          duration: 3000,
        });
      const _this = this;
      let payload = {
        supplier_id: _this.form.supplier.id,
        concepts: _this.form.concepts,
        estatus: _this.form.estatus,
        charges: _this.form.charges.map((item) => {
          return {
            agency_id: item.agency.id,
            depto_id: item.department.id,
            percent: item.percent,
          };
        }),
        metodo_pago: _this.form.invoice.metodo_pago_id,
        uso_cfdi: _this.form.invoice.uso_cfdi_id,
        forma_pago: _this.form.invoice.forma_pago_id,
        payment_condition: _this.form.payment_condition,
        purchase_concept_id: _this.form.purchase_concept.id,
        observation: _this.form.observation,
        agency_id: _this.form.agency_id,
        subtotal: _this.form.amounts.subtotal,
        with_tax: _this.form.amounts.with_tax,
        tax: _this.form.amounts.tax,
        with_isr: _this.form.amounts.with_isr,
        tax_isr: _this.form.amounts.tax_isr,
        with_iva_retenido: _this.form.amounts.with_iva_retenido,
        tax_iva_retenido: _this.form.amounts.tax_iva_retenido,
        with_retencion_cedular: _this.form.amounts.with_retencion_cedular,
        tax_retencion_cedular: _this.form.amounts.tax_retencion_cedular,
        with_retencion_125: _this.form.amounts.with_retencion_125,
        tax_retencion_125: _this.form.amounts.tax_retencion_125,
        discount: _this.form.amounts.discount,
        total: _this.form.amounts.total,
      };
      await axios
        .put(`/admin/purchase-order/${_this.purchaseId}`, payload)
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
    async loadOptions(cb) {
      const _this = this;
      await axios
        .get("/admin/purchase-order/resources/options")
        .then(function (response) {
          let {
            suppliers,
            agencies,
            departments,
            purchase_concept,
          } = response.data.data;
          _this.options.suppliers = suppliers;
          _this.options.agencies = agencies;
          _this.options.departments = departments;
          _this.options.purchase_concept = purchase_concept;
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
    // async updateDateToPayment() {
    //   const _this = this;
    //   let payload = {
    //     date_to_payment: this.form.invoice_info.date_to_payment,
    //     payment_date: this.form.invoice_info.payment_date,
    //   };
    //   await axios
    //     .post(
    //       `/admin/purchase-order/store-invoice/${_this.purchaseId}`,
    //       payload
    //     )
    //     .then(function (response) {
    //       _this.$store.commit("showSnackbar", {
    //         message: response.data.message,
    //         color: "success",
    //         duration: 3000,
    //       });
    //       _this.$eventBus.$emit("ORDERS_REFRESH");
    //     })
    //     .catch(function (error) {
    //       _this.$store.commit("hideLoader");
    //       if (error.response) {
    //         _this.$store.commit("showSnackbar", {
    //           message: error.response.data.message,
    //           color: "error",
    //           duration: 3000,
    //         });
    //         return false;
    //       } else if (error.request) {
    //         console.log(error.request);
    //       } else {
    //         console.log("Error", error.message);
    //       }
    //     });
    // },
    save() {
      this.snack = true;
      this.snackColor = "success";
      this.snackText = "Data saved";
    },
    cancel() {
      this.snack = true;
      this.snackColor = "error";
      this.snackText = "Canceled";
    },
    open() {
      this.snack = true;
      this.snackColor = "info";
      this.snackText = "Dialog opened";
    },
    close() {
      console.log("Dialog closed");
    },
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
    inputImportCsv: function (csv_import) {
      const _this = this;
      // _this.form.concepts = _this.form.concepts.concat(csv_import);
      _this.form.concepts = _this.form.concepts.concat(
        csv_import.map((item) => {
          return {
            name: item.name || "",
            description: item.description || "",
            unit: item.unit || "pz",
            quantity: 1,
            price: item.price || 0,
            discount: !!item.discount ? item.discount : 0,
          };
        })
      );
      _this.$store.commit("showDialog", {
        type: "accept",
        icon: "mdi-import",
        title: "Articulos Importados",
        message: "Articulos Cargados Correctamente ",
      });
    },
    loadImportCb(response) {
      const self = this;
      console.log("importCB", response);
    },
    loadImportCatch(response) {
      this.$store.commit("showDialog", {
        type: "accept",
        icon: "warning",
        title: "Error CSV",
        message: "Ocurrio un error al importar CSV ",
      });
    },
  },
};
</script>
