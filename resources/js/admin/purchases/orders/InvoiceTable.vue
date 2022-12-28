<template>
  <v-card>
    <v-card-title>
      Facturas Relacionadas
      <v-spacer />
      <v-btn color="primary" large @click="dialogForm = true" small dark>
        Registrar Factura <v-icon right>mdi-plus-thick</v-icon>
      </v-btn>
    </v-card-title>
    <v-card-text>
      <v-simple-table dense>
        <template v-slot:default>
          <thead>
            <tr>
              <th class="text-left">
                Folio Factura (UUID)
              </th>
              <th class="text-left">
                Importe
              </th>
              <th class="text-left">
                Fecha de Facturacion
              </th>
              <th class="text-left">
                Programacion de Pago
              </th>
              <th class="text-left">
                Fecha en que se Pago
              </th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="item in items" :key="item.id">
              <td>
                <div>{{ item.folio_fiscal }}</div>
                <div class="text--secondary">{{ item.folio }}</div>
              </td>
              <td>{{ item.amount | money }}</td>
              <td>
                {{
                  $appFormatters.formatDate(
                    item.invoice_date || "",
                    "DD MMM YYYY"
                  )
                }}
              </td>
              <td>
                <v-dialog
                  :ref="`dialog-${item.id}`"
                  v-model="modal"
                  :return-value.sync="date_to_pay"
                  persistent
                  width="290px"
                  v-if="item.date_to_pay == null"
                >
                  <template v-slot:activator="{ on, attrs }">
                    <v-btn
                      color="pink"
                      v-bind="attrs"
                      v-on="on"
                      class="ml-2"
                      dark
                    >
                      {{
                        item.date_to_pay
                          ? `Pagar el: ${$appFormatters.formatDate(
                              item.date_to_pay,
                              "ll"
                            )}`
                          : "Programar Fecha de Pago"
                      }}
                      <v-icon right>mdi-clock-check-outline</v-icon>
                    </v-btn>
                  </template>
                  <v-date-picker v-model="date_to_pay" scrollable>
                    <v-spacer></v-spacer>
                    <v-btn text color="primary" @click="modal = false">
                      Cancel
                    </v-btn>
                    <v-btn
                      text
                      color="primary"
                      @click="updateDateToPayment(item.id), (modal = false)"
                    >
                      OK
                    </v-btn>
                  </v-date-picker>
                </v-dialog>
                <span v-else>
                  {{
                    $appFormatters.formatDate(
                      item.date_to_pay || "",
                      "DD MMM YYYY"
                    )
                  }}
                </span>
              </td>
              <td>
                <!-- <v-btn v-if="item.payment_date == null" color="blue" dark small>
                  Fecha de Pago
                </v-btn> -->
                <v-dialog
                  ref="dialog_payment"
                  v-model="dialogPayment"
                  :return-value.sync="payment_date"
                  persistent
                  transition="scale-transition"
                  width="324px"
                  v-if="item.payment_date == null"
                >
                  <template v-slot:activator="{ on, attrs }">
                    <v-btn
                      color="cyan"
                      v-bind="attrs"
                      v-on="on"
                      class="ml-2"
                      dark
                    >
                      {{
                        payment_date
                          ? `Pagada el: ${$appFormatters.formatDate(
                              payment_date,
                              "ll"
                            )}`
                          : "Fecha Pago Factura"
                      }}
                      <v-icon right>mdi-file-check-outline</v-icon>
                    </v-btn>
                  </template>
                  <v-date-picker v-model="payment_date" scrollable>
                    <v-spacer></v-spacer>
                    <v-btn text color="primary" @click="dialogPayment = false">
                      Cancel
                    </v-btn>
                    <v-btn
                      text
                      color="primary"
                      @click="
                        updateDatePayment(item.id), (dialogPayment = false)
                      "
                    >
                      Confirmar
                    </v-btn>
                  </v-date-picker>
                </v-dialog>
                <span v-else>
                  {{
                    $appFormatters.formatDate(
                      item.payment_date || "",
                      "DD MMM YYYY"
                    )
                  }}
                </span>
              </td>
              <td>
                <v-btn icon color="green" @click="editItem(item)">
                  <v-icon>mdi-pencil </v-icon>
                </v-btn>
                <v-btn icon color="red" @click="deleteItem(item)">
                  <v-icon>mdi-trash-can </v-icon>
                </v-btn>
              </td>
            </tr>
          </tbody>
        </template>
      </v-simple-table>
    </v-card-text>
    <v-dialog v-model="dialogForm" max-width="700px" persistent>
      <v-card>
        <v-card-title>
          <span class="text-h5">Facturacion</span>
        </v-card-title>
        <v-card-text>
          <v-container fluid>
            <v-form v-model="valid" ref="form" lazy-validation>
              <v-row>
                <v-col cols="8">
                  <v-file-input
                    v-model="file_xml"
                    label="Seleccionar XML"
                    accept="text/xml"
                    outlined
                    dense
                  ></v-file-input>
                </v-col>
                <v-col cols="4">
                  <v-btn color="primary" dark @click="loadXml()">
                    Validar XML
                  </v-btn>
                </v-col>
              </v-row>
              <div>
                <v-simple-table
                  v-if="DATAXMLKeys"
                  fixed-header
                  height="300px"
                  class="text-uppercase"
                  dense
                >
                  <template v-slot:default>
                    <thead>
                      <tr>
                        <th class="text-left">
                          Campo XML
                        </th>
                        <th class="text-left">
                          Valor
                        </th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr v-for="item in DATAXMLKeys" :key="item">
                        <td class="font-weight-bold">{{ item }}</td>
                        <td>
                          <span
                            class="d-inline-block text-truncate"
                            style="max-width: 400px;"
                          >
                            {{ data_file_xml[item][0] }}
                          </span>
                        </td>
                      </tr>
                    </tbody>
                  </template>
                </v-simple-table>
              </div>
            </v-form>
          </v-container>
          <small>* indica campo obligatorio</small>
        </v-card-text>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn
            color="red darken-1"
            text
            @click="
              (dialogForm = false), (file_xml = null), (data_file_xml = [])
            "
          >
            Cancelar
          </v-btn>
          <v-btn
            color="blue darken-1"
            :disabled="!valid_invoice"
            text
            @click="save"
          >
            Guardar
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </v-card>
</template>
<script>
export default {
  name: "InvoicesTable",
  props: {
    purchaseId: {
      type: Number | String,
      require: true,
    },
    // dialogForm: {
    //   type: Boolean,
    //   default: false,
    // },
    items: {
      type: Array,
      default: () => {
        return [];
      },
    },
  },
  mounted() {
    // this.loadOptions();
  },

  computed: {
    DATAXMLKeys() {
      return Object.keys(this.data_file_xml);
    },
  },
  data() {
    return {
      modal: false,
      dialogPayment: false,
      dialogCalendar: false,
      dialogForm: false,
      valid: true,
      editedIndex: -1,
      file_xml: null,
      data_file_xml: [],
      valid_invoice: false,
      formDefault: {
        folio_fiscal: "",
        folio: "",
        invoice_date: "",
        amount: 0,
      },
      date_to_pay: new Date(Date.now() - new Date().getTimezoneOffset() * 60000)
        .toISOString()
        .substr(0, 10),
      payment_date: "",
      formEdit: {},
      uuidRules: [
        (v) => !!v || "UUID es Requerido",
        (v) =>
          /^[0-9a-fA-F]{8}-[0-9a-fA-F]{4}-[0-9a-fA-F]{4}-[0-9a-fA-F]{4}-[0-9a-fA-F]{12}$/.test(
            v
          ) || "UUID debe ser Valido.",
      ],
      options: {
        // usoCFDI: [],
        // metodoPago: [],
        // formaPago: [],
      },
    };
  },
  methods: {
    async loadXml() {
      const _this = this;
      let formData = new FormData();
      formData.append("file", _this.file_xml);

      await axios
        .post(
          `/admin/purchase-order/valid-invoice/${_this.purchaseId}`,
          formData
        )
        .then(function (response) {
          _this.$store.commit("showSnackbar", {
            message: response.data.message,
            color: response.data.data.valid_invoice ? "success" : "error",
            duration: 3000,
          });
          _this.data_file_xml = response.data.data.data_xml;
          _this.valid_invoice = response.data.data.valid_invoice;
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

      // console.log(this.file_xml, "load xml");
    },
    editItem(item) {
      const _this = this;
      _this.editedIndex = _this.items.indexOf(item);
      _this.formEdit = Object.assign({}, item);
      _this.dialogForm = true;
    },
    deleteItem(item) {
      const _this = this;
      _this.$store.commit("showDialog", {
        type: "confirm",
        title: "Eliminar Factura",
        message: "¿Seguro de que desea eliminar este registro?",
        okCb: () => {
          _this.editedIndex = _this.items.indexOf(item);
          _this.items.splice(_this.editedIndex, 1);
        },
        cancelCb: () => {
          console.log("CANCEL");
        },
      });
    },
    async save() {
      const _this = this;
      if (!_this.$refs.form.validate()) return;
      let formData = new FormData();
      formData.append("file", _this.file_xml);
      await axios
        .post(
          `/admin/purchase-order/store-invoice/${_this.purchaseId}`,
          formData
        )
        .then(function (response) {
          _this.$store.commit("showSnackbar", {
            message: response.data.message,
            color: "success",
            duration: 3000,
          });
          _this.$eventBus.$emit("ORDERS_REFRESH");
          _this.dialogForm = false;
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
        date_to_pay: _this.date_to_pay,
        // payment_date: _this.payment_date,
      };
      await axios
        .post(`/admin/purchase-invoice/date_to_payment/${invoice_id}`, payload)
        .then(function (response) {
          _this.$store.commit("showSnackbar", {
            message: response.data.message,
            color: "success",
            duration: 3000,
          });
          _this.date_to_pay = "";
          _this.payment_date = "";
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
    async updateDatePayment(invoice_id = null) {
      const _this = this;

      let payload = {
        payment_date: _this.payment_date,
      };
      await axios
        .post(`/admin/purchase-invoice/date_payment/${invoice_id}`, payload)
        .then(function (response) {
          _this.$store.commit("showSnackbar", {
            message: response.data.message,
            color: "success",
            duration: 3000,
          });
          _this.date_to_pay = "";
          _this.payment_date = "";
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
  },
};
</script>
<style></style>
