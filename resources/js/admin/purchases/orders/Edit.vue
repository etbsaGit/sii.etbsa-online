<template>
  <v-card flat>
    <v-card-title class="overline align-center">
      <v-icon left>mdi-file-document</v-icon> Detalle Orden de compra #
      {{ purchaseId.toString().padStart(5, 0) }}
      <v-spacer></v-spacer>
      <b>Acciones:</b>
      <v-btn
        v-if="canSave"
        class="ml-2"
        color="green "
        @click="updatePurchase"
        dark
      >
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
        {{ form.estatus.title }}
      </v-chip>
    </v-card-subtitle>

    <v-divider></v-divider>
    <v-card-text>
      <v-form ref="form_info" v-model="validFormInfo" lazy-validation>
        <v-row dense>
          <v-col cols="12" md="9">
            <v-card color="grey lighten-2">
              <v-card-title class="pb-0 overline">
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
                <v-btn icon @click="form.products = []">
                  <v-icon color="red">mdi-delete-empty-outline</v-icon>
                </v-btn>
              </v-card-title>
              <v-card-text>
                <purchase-products-table
                  :dialogForm="dialogSupplierProducts"
                  @edit="dialogSupplierProducts = true"
                  @close="dialogSupplierProducts = false"
                  :items.sync="form.products"
                  :ConceptProductProp.sync="PurchaseConceptProduct"
                ></purchase-products-table>
              </v-card-text>
              <v-card-actions>
                <v-btn text color="blue" @click="dialogSupplierProducts = true">
                  <v-icon left>mdi-plus</v-icon> Agregar Articulo o Servicio
                </v-btn>
              </v-card-actions>
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
          <!-- Second Col -->
          <v-col cols="12" md="3">
            <v-card color="grey lighten-3" min-height="200">
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
              <v-card-text class="px-0">
                <purchase-charge-table
                  :dialogForm="dialogAddCharge"
                  @edit="dialogAddCharge = true"
                  @close="dialogAddCharge = false"
                  :items.sync="form.charges"
                  :agencies="options.agencies"
                  :departments="options.departments"
                ></purchase-charge-table>
              </v-card-text>
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
              <v-card-text class="px-0">
                <purchase-invoice-table
                  v-if="form.purchase_concept"
                  :form.sync="form.invoice"
                  :dialogForm="dialogConfigInvoice"
                  :usocfdi.sync="form.purchase_concept.usocfdi"
                  @close="dialogConfigInvoice = false"
                ></purchase-invoice-table>
              </v-card-text>
              <v-card-title>Monto y Retenciones</v-card-title>
              <v-card-text class="px-0">
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
import PurchaseOrderMessages from "../components/PurchaseOrderMessages.vue";
import PurchaseAmountsTable from "./PurchaseAmountsTable.vue";
import PurchaseChargeTable from "./PurchaseChargeTable.vue";
import PurchaseProductsTable from "./PurchaseProductsTable.vue";
import PurchaseInvoicePaymentTable from "./PurchaseInvoicePaymentTable.vue";
import PurchaseInvoiceTable from "./PurchaseInvoiceTable.vue";
import InvoiceTable from "./InvoiceTable.vue";
export default {
  name: "PurchaseOrderUpdate",
  components: {
    PurchaseProductsTable,
    PurchaseAmountsTable,
    PurchaseChargeTable,
    PurchaseInvoiceTable,
    PurchaseOrderMessages,
    PurchaseInvoicePaymentTable,
    InvoiceTable,
    DialogComponent,
  },
  props: {
    purchaseId: {
      require: true,
      type: Number | String,
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
      dialogSupplierProducts: false,
      dialogAddCharge: false,
      dialogConfigInvoice: false,
      form: {},
      PurchaseItem: {},
      forUpdate: false,
      options: {
        suppliers: [],
        agencies: [],
        departments: [],
        payment_condition: [],
        purchase_concept: [],
        purchase_types: [],
      },
    };
  },
  mounted() {
    const _this = this;
    _this.$eventBus.$on("ORDERS_REFRESH", () => {
      _this.loadPurchaseEdit();
    });
    _this.$store.commit("setBreadcrumbs", [
      { label: "Ordenes de Compra", to: { name: "purchase.list" } },
      { label: "Detalle", name: "" },
    ]);
    _this.loadOptions();
    _this.loadPurchaseEdit();
  },
  computed: {
    PurchaseConcept() {
      const _this = this;
      return _this.options.purchase_types.length > 0
        ? _this.options.purchase_types.find(
            (e) => e.id === _this.form.purchase_type_id
          ).purchase_concept
        : [];
    },
    PurchaseConceptProduct() {
      const _this = this;
      return _this.PurchaseConcept.length > 0
        ? _this.PurchaseConcept.find(
            (e) => e.id === _this.form.purchase_concept.id
          ).concept_product
        : [];
    },
    canSave() {
      let estatus = {
        pendiente: true,
        denegar: true,
      };
      if (this.$gate.auth().id == this.form.created_by && this.forUpdate) {
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
      return estatus[this.form.estatus.key] ?? false;
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
  watch: {
    form: {
      handler() {
        this.forUpdate = true;
      },
      deep: true,
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
      _this.forUpdate = false;
    },
    async loadOptions() {
      const _this = this;
      let { data: response } = await axios.get(
        "/admin/purchase-order/resources/options"
      );
      _this.options = { ...response.data };
    },
    async updatePurchase() {
      if (!this.$refs.form_info.validate()) return;
      if (this.form.products.length <= 0)
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
      let percent = this.form.charges
        .map((i) => i.percent)
        .reduce((acc, crr) => acc + crr, 0);
      if (percent != 100)
        return this.$store.commit("showSnackbar", {
          message: "La suma de los Porcentaje de cargos debe ser 100%",
          color: "error",
          duration: 3000,
        });
      const _this = this;
      let payload = {
        supplier_id: _this.form.supplier.id,
        estatus: _this.form.estatus,
        products: _this.form.products.map((item) => {
          return {
            concept_product_id: item.group.id,
            description: item.description,
            unit_id: item.unit.id,
            unit: item.unit,
            qty: item.qty,
            price: item.price,
            discount: item.discount,
            subtotal: item.subtotal,
          };
        }),
        metodo_pago: _this.form.invoice.metodo_pago.clave,
        uso_cfdi: _this.form.invoice.uso_cfdi.clave,
        forma_pago: _this.form.invoice.forma_pago.clave,
        payment_condition: _this.form.payment_condition,
        purchase_concept_id: _this.form.purchase_concept.id,
        purchase_type_id: _this.form.purchase_type_id,
        observation: _this.form.observation,
        note: _this.form.note,
        agency_id: _this.form.agency_id,
        charges: _this.form.charges.map((item) => {
          return {
            agency_id: item.agency.id,
            depto_id: item.department.id,
            percent: item.percent,
          };
        }),
        //Amount
        subtotal: _this.form.amounts.subtotal,
        discount: _this.form.amounts.discount,
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
        with_flete: _this.form.amounts.with_flete,
        tax_flete: _this.form.amounts.tax_flete,
        total: _this.form.amounts.total,
      };
      await axios
        .put(`/admin/purchase-order/${_this.purchaseId}`, payload)
        .then(function(response) {
          _this.$store.commit("showSnackbar", {
            message: response.data.message,
            color: "success",
            duration: 3000,
          });
          _this.loadPurchaseEdit();
          // _this.$eventBus.$emit("ORDERS_REFRESH");
          // _this.$eventBus.$emit("CLOSE_DIALOG");
        })
        .catch(function(error) {
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
        .then(function(response) {
          _this.$store.commit("showSnackbar", {
            message: response.data.message,
            color: "success",
            duration: 3000,
          });
          _this.$eventBus.$emit("ORDERS_REFRESH");
          _this.loadPurchaseEdit();
          // _this.$eventBus.$emit("CLOSE_DIALOG");
        })
        .catch(function(error) {
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
        .then(function(response) {
          _this.$store.commit("showSnackbar", {
            message: response.data.message,
            color: "success",
            duration: 3000,
          });
          _this.loadPurchaseEdit();
        })
        .catch(function(error) {
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
  },
};
</script>
