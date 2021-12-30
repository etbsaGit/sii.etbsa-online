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
                >
                  <template v-slot:item="{ item }">
                    <v-list-item-title v-text="item.business_name" />
                    <v-list-item-subtitle v-html="item.rfc" />
                    <v-list-item-subtitle v-html="item.email" />
                  </template>
                </v-autocomplete>
              </v-card-title>
              <v-card-title class="pt-0">
                Articulos
                <v-spacer></v-spacer>
                <v-dialog transition="dialog-top-transition" max-width="600">
                  <template v-slot:activator="{ on, attrs }">
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
                <v-btn icon @click="form.concepts = []">
                  <v-icon color="red">mdi-delete-empty-outline</v-icon>
                </v-btn>
              </v-card-title>
              <v-card-text>
                <purchase-concept-table
                  :dialogForm="dialogConcepts"
                  @edit="dialogConcepts = true"
                  @close="dialogConcepts = false"
                  :items.sync="form.concepts"
                ></purchase-concept-table>
              </v-card-text>
              <v-card-actions>
                <v-btn text color="blue" @click="dialogConcepts = true">
                  <v-icon left>mdi-plus</v-icon> Agregar Articulo o Servicio
                </v-btn>
              </v-card-actions>
            </v-card>
            <v-card flat>
              <v-card-title>Otros Datos</v-card-title>
              <v-card-text class="pb-0">
                <v-row dense>
                  <v-col cols="12" md="4">
                    <v-combobox
                      v-model="form.reason"
                      label="Concepto de Compra"
                      :items="[
                        'COMPRA DE MAQUINARIA DIVERSA',
                        'COMPRA DE LLANTAS PARA VENTA',
                        'SERVICIO DE MANTENIMIENTO',
                        'COMPRA DE LLANTAS PARA UNIDADES',
                        'SANITIZACION',
                        'REFACCIONES',
                        'PUBLICIDAD',
                        'PAPELERIA',
                        'LIMPIEZA',
                        'IMPRENTA',
                        'RIEGO',
                        'SEGUROS',
                        'STOCK',
                      ]"
                      :rules="[(v) => !!v || 'Es requerido']"
                      outlined
                      dense
                    ></v-combobox>
                  </v-col>
                  <v-col cols="12" md="4">
                    <v-text-field
                      v-model="form.payment_condition"
                      label="Condiciones de Pago"
                      :rules="[(v) => !!v || 'Es requerido']"
                      outlined
                      dense
                    ></v-text-field>
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
            </v-card>
          </v-col>
          <v-col cols="12" md="3">
            <v-card color="grey lighten-3" min-height="200">
              <v-card-title>
                Cargos a Sucursal
                <v-spacer></v-spacer>
                <v-btn icon @click="dialogAddCharge = true">
                  <v-icon color="primary">mdi-plus-thick</v-icon>
                </v-btn>
              </v-card-title>
              <v-card-text class="px-0">
                <purchase-charge-table
                  :dialogForm="dialogAddCharge"
                  @edit="dialogAddCharge = true"
                  @close="dialogAddCharge = false"
                  :items.sync="form.charges"
                ></purchase-charge-table>
              </v-card-text>
              <v-card-title>
                Facturacion
                <v-spacer></v-spacer>
                <v-btn icon @click="dialogConfigInvoice = true">
                  <v-icon>mdi-settings</v-icon>
                </v-btn>
              </v-card-title>
              <v-card-text class="px-0">
                <purchase-invoice-table
                  :dialogForm="dialogConfigInvoice"
                  @close="dialogConfigInvoice = false"
                  :form.sync="form.invoice"
                ></purchase-invoice-table>
              </v-card-text>
              <v-card-title>Resumen</v-card-title>
              <v-card-text class="px-0">
                <purchase-amounts-table
                  :items="form.concepts"
                  :form.sync="form.amounts"
                ></purchase-amounts-table>
              </v-card-text>
              <v-card-actions>
                <v-btn block color="blue" @click="createPurchase">
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
import ImportCsvComponent from "../../components/ImportCsvComponent.vue";
import PurchaseAmountsTable from "./PurchaseAmountsTable.vue";
import PurchaseChargeTable from "./PurchaseChargeTable.vue";
import PurchaseConceptTable from "./PurchaseConceptTable.vue";
import PurchaseInvoiceTable from "./PurchaseInvoiceTable.vue";
export default {
  name: "PurchaseOrderCreate",
  components: {
    PurchaseConceptTable,
    PurchaseAmountsTable,
    PurchaseChargeTable,
    PurchaseInvoiceTable,
    ImportCsvComponent,
  },
  data() {
    return {
      validFormInfo: true,
      parseCsv: null,
      mapFields: [
        { key: "name", label: "Nombre Articulo" },
        { key: "description", label: "Descripcion" },
        { key: "unit", label: "Unidad" },
        { key: "price", label: "Precio" },
        { key: "tax", label: "Impuesto" },
      ],
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
          total: 0,
        },
        check_tax: false,
        supplier_id: null,
        supplier: {},
        estatus: { title: "Pendiente" },
        estatus_id: null,
        reason: null,
        deliver_date: new Date().toISOString().substr(0, 10),
        payment_condition: "30 dias",
        agency_id: null,
      },
      options: {
        suppliers: [],
        agencies: [],
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
  },
  methods: {
    async createPurchase() {
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
        charges: _this.form.charges,
        metodo_pago: _this.form.invoice.metodo_pago_id,
        uso_cfdi: _this.form.invoice.uso_cfdi_id,
        forma_pago: _this.form.invoice.forma_pago_id,
        payment_condition: _this.form.payment_condition,
        reason: _this.form.reason,
        observation: _this.form.observation,
        agency_id: _this.form.agency_id,
        subtotal: _this.form.amounts.subtotal,
        tax: _this.form.amounts.tax,
        total: _this.form.amounts.total,
      };
      await axios
        .post("/admin/purchase-order", payload)
        .then(function (response) {
          _this.$store.commit("showSnackbar", {
            message: response.data.message,
            color: "success",
            duration: 3000,
          });
          // _this.$eventBus.$emit("ORDERS_REFRESH");
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
          let { suppliers, agencies } = response.data.data;
          _this.options.suppliers = suppliers;
          _this.options.agencies = agencies;
        });
    },
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
            tax: item.tax || 0,
          };
        })
      );
      _this.$store.commit("showDialog", {
        type: "accept",
        icon: "success",
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
