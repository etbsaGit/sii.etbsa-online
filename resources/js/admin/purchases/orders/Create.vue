<template>
  <v-card>
    <v-card-title>
      <v-icon class="mr-2">mdi-file-document</v-icon> Registrar Orden de Compra
      <v-spacer></v-spacer>
    </v-card-title>
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
                  placeholder="Buscar por #Equip | Nombre | RFC | Email"
                  persistent-placeholder
                  persistent-hint
                  :hint="
                    `RFC: ${form.supplier.rfc || ''} 
                    #Equip ${form.supplier.code_equip || ''}`
                  "
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

                <!-- IMPORT CSV COMPONENT -->
                <v-dialog transition="dialog-top-transition" max-width="600">
                  <template v-slot:activator="{ on, attrs }">
                    <v-btn text v-bind="attrs" v-on="on" small class="ml-2">
                      CSV<v-icon right>mdi-file-import-outline</v-icon>
                    </v-btn>
                  </template>
                  <template v-slot:default="dialog">
                    <v-card>
                      <v-card-title
                        class="white--text accent title text-uppercase"
                      >
                        Importar Lista Articulos
                        <v-spacer />
                        <v-icon large color="red" @click="dialog.value = false">
                          mdi-close-circle
                        </v-icon>
                      </v-card-title>
                      <v-card-text class="pa-0">
                        <import-csv-component
                          v-if="dialog.value"
                          @input="inputImportCsv"
                          :map-fields="mapFields"
                          loadBtnText="Cargar CSV"
                          submitBtnText="Importar"
                          :callback="loadImportCb"
                          :catch="loadImportCatch"
                        ></import-csv-component>
                      </v-card-text>
                    </v-card>
                  </template>
                </v-dialog>
                <!-- END CSV IMPOR COMPONENT -->

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
                  :SupplierIdProp="form.supplier.id"
                  :ConceptProductProp="PurchaseConceptProduct"
                ></purchase-products-table>
              </v-card-text>
              <v-card-actions>
                <v-btn text color="blue" @click="dialogSupplierProducts = true">
                  <v-icon left>mdi-plus</v-icon> Agregar Articulo o Servicio
                </v-btn>
                <v-spacer />
                <!-- BTN Adjuntar Cotizacion -->
                <v-dialog transition="dialog-top-transition" max-width="600">
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
                        <v-icon large color="red" @click="dialog.value = false">
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
                  :dialogForm="dialogConfigInvoice"
                  @close="dialogConfigInvoice = false"
                  :form.sync="form.invoice"
                  :usocfdi.sync="form.purchase_concept.usocfdi"
                ></purchase-invoice-table>
              </v-card-text>
              <v-card-title>Monto y Retenciones</v-card-title>
              <v-card-text class="px-0">
                <purchase-amounts-table
                  :items="form.products"
                  :form.sync="form.amounts"
                ></purchase-amounts-table>
              </v-card-text>
              <v-card-actions>
                <v-btn block color="blue" @click="store" dark>
                  Crear OC
                </v-btn>
              </v-card-actions>
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
import ImportCsvComponent from "../../components/ImportCsvComponent.vue";
import PurchaseFilesList from "./PurchaseFilesList.vue";
export default {
  name: "PurchaseOrderCreate",
  components: {
    PurchaseProductsTable,
    PurchaseAmountsTable,
    PurchaseChargeTable,
    PurchaseInvoiceTable,
    ImportCsvComponent,
    PurchaseFilesList,
  },
  data() {
    return {
      mapFields: [
        { key: "name", label: "Grupo Articulo" },
        { key: "description", label: "Descripcion" },
        { key: "qty", label: "Cantidad" },
        { key: "price", label: "Precio U." },
        { key: "discount", label: "Descuento" },
        // { key: "unit", label: "Unidad" },
      ],

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
          with_flete: false,
          tax_flete: 0,
          total: 0,
        },
        supplier: {},
        purchase_concept: null,
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
      { label: "Crear", name: "" },
    ]);
  },
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
    PurchaseConceptProduct() {
      const _this = this;
      if (_this.form.purchase_concept != null)
        return _this.form.purchase_concept.concept_product ?? [];
    },
  },
  watch: {
    "form.purchase_type_id": {
      handler() {
        const _this = this;
        _this.form.purchase_concept = null;
        _this.form.invoice = {
          uso_cfdi: null,
          metodo_pago: null,
          forma_pago: null,
        };
        _this.form.products = [];
      },
      flush: "post",
    },
    "form.supplier": {
      handler() {
        const _this = this;
        _this.form.payment_condition = _this.form.supplier.credit_days || 30;
      },
      flush: "post",
    },
  },
  methods: {
    async store() {
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
      console.log("percenr Cargos", percent);
      if (percent < 100 || percent > 100)
        return this.$store.commit("showSnackbar", {
          message: "La suma de los Porcentaje de cargos debe ser 100%",
          color: "error",
          duration: 3000,
        });

      const _this = this;
      const formData = new FormData();

      let payload = {
        supplier_id: _this.form.supplier.id,
        products: _this.form.products.map((item) => {
          return {
            concept_product_id: item.group.id ?? null,
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
      _this.form.file.forEach((item) => {
        formData.append("file[]", item);
      });
      formData.append("payload", JSON.stringify(payload));

      await axios
        .post("/admin/purchase-order", formData)
        .then(function(response) {
          _this.$store.commit("showSnackbar", {
            message: response.data.message,
            color: "success",
            duration: 3000,
          });
          _this.$eventBus.$emit("ORDERS_REFRESH");
          _this.$router.go(-1);
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
    async loadOptions(cb) {
      const _this = this;
      await axios
        .get("/admin/purchase-order/resources/options")
        .then(function(response) {
          let {
            suppliers,
            agencies,
            departments,
            purchase_types,
          } = response.data.data;
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
    //METHODS CSV IMPORT
    inputImportCsv: function(csv_import) {
      const _this = this;
      // _this.form.concepts = _this.form.concepts.concat(csv_import);
      _this.form.products = _this.form.products.concat(
        csv_import.map((csv) => {
          let group_product =
            _this.PurchaseConceptProduct.find((e) => {
              if (e.name && csv.name)
                return (
                  e.name.toLowerCase().replace(/\s+/g, "") ===
                  csv.name.toLowerCase().replace(/\s+/g, "")
                );
            }) ?? {};
          return {
            group: group_product,
            description: csv.description || "",
            unit: { id: 1, clave: "H87", name: "Pieza" },
            qty: csv.qty || 1,
            price: csv.price || 0,
            discount: !!csv.discount ? csv.discount : 0,
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
