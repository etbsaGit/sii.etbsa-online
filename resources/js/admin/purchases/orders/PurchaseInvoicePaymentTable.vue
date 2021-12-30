<template>
  <div>
    <v-simple-table dense>
      <template v-slot:default>
        <tbody>
          <tr>
            <td class="pr-0">
              <v-list-item-content>
                <v-list-item-title>Folio Fiscal</v-list-item-title>
                <v-list-item-subtitle
                  class="text-wrap blue--text caption subtitle"
                >
                  {{ form.folio_fiscal }}
                </v-list-item-subtitle>
              </v-list-item-content>
            </td>
            <td class="text-right font-weight-bold" style="width: 90px;">
              {{ form.folio }}
            </td>
          </tr>
          <tr>
            <td>
              <v-list-item-content>
                <v-list-item-title>Fecha de Factura</v-list-item-title>
              </v-list-item-content>
            </td>
            <td
              class="text-right font-weight-bold text-uppercase"
              style="width: 125px;"
            >
              <span v-show="form.invoice_date">
                {{
                  $appFormatters.formatDate(
                    form.invoice_date || "",
                    "DD MMM YYYY"
                  )
                }}
              </span>
            </td>
          </tr>
          <tr>
            <td>
              <v-list-item-content>
                <v-list-item-title>Importe Factura</v-list-item-title>
              </v-list-item-content>
            </td>
            <td
              class="text-right font-weight-bold text-uppercase"
              style="width: 75px;"
            >
              {{ form.amount | currency }}
            </td>
          </tr>
          <tr>
            <td>
              <v-list-item-content>
                <v-list-item-title>Fecha para Pago</v-list-item-title>
              </v-list-item-content>
            </td>
            <td class="text-right font-weight-bold" style="width: 50px;">
              <span v-show="form.date_to_payment">
                {{
                  $appFormatters.formatDate(
                    form.date_to_payment || "",
                    "DD MMM YYYY"
                  )
                }}
              </span>
            </td>
          </tr>
          <tr>
            <td>
              <v-list-item-content>
                <v-list-item-title>Fecha de Pago</v-list-item-title>
              </v-list-item-content>
            </td>
            <td class="text-right font-weight-bold" style="width: 50px;">
              <span v-show="form.payment_date">
                {{
                  $appFormatters.formatDate(
                    form.payment_date || "",
                    "DD MMM YYYY"
                  )
                }}
              </span>
            </td>
          </tr>
        </tbody>
      </template>
    </v-simple-table>
    <v-dialog v-model="Dialog" max-width="600px">
      <v-card>
        <v-card-title>
          <span class="text-h5">Facturacion</span>
        </v-card-title>
        <v-card-text>
          <v-container fluid>
            <v-form v-model="valid" ref="form" lazy-validation>
              <v-row dense class="overline">
                <v-col cols="12" md="12">
                  <v-text-field
                    v-model="form.folio_fiscal"
                    label="Folio Fiscal (UUID)*"
                    v-mask="'NNNNNNNN-NNNN-NNNN-NNNN-NNNNNNNNNNNN'"
                    hint="Ex. 3366177x-72fx-4xx0-9502-58091xx3x164"
                    :rules="uuidRules"
                    counter
                    outlined
                    dense
                  ></v-text-field>
                </v-col>
                <v-col cols="12" md="6">
                  <v-text-field
                    v-model="form.folio"
                    label="Serie - Folio *"
                    :rules="[(v) => !!v || 'campo requerido']"
                    hint="requerido*"
                    outlined
                    dense
                  ></v-text-field>
                </v-col>
                <v-col cols="12" md="6">
                  <v-text-field
                    v-model="form.invoice_date"
                    label="Fecha de la Factura"
                    :rules="[(v) => !!v || 'campo requerido']"
                    hint="requerido*"
                    type="date"
                    outlined
                    dense
                  ></v-text-field>
                </v-col>
                <v-col cols="12" md="4">
                  <v-text-field
                    v-model="form.amount"
                    label="Importe Factura"
                    persistent-placeholder
                    :rules="[(v) => !!v || 'campo requerido']"
                    placeholder="0.00"
                    hint="requerido*"
                    type="number"
                    prefix="$"
                    outlined
                    dense
                  ></v-text-field>
                </v-col>
                <!-- <v-col cols="12" md="6">
                  <v-text-field
                    dense
                    outlined
                    type="date"
                    label="Fecha programacion de pago"
                  ></v-text-field>
                </v-col>
                <v-col cols="12" md="6">
                  <v-text-field
                    dense
                    outlined
                    type="date"
                    label="Fecha de pago"
                  ></v-text-field>
                </v-col> -->
              </v-row>
            </v-form>
          </v-container>
          <small>* indica campo obligatorio</small>
        </v-card-text>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn color="red darken-1" text @click="Dialog = false">
            Cancelar
          </v-btn>
          <v-btn color="blue darken-1" text @click="save">
            Guardar
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </div>
</template>
<script>
export default {
  name: "PurchaseInvoiceDetailTable",
  props: {
    purchaseId: {
      type: Number | String,
      require: true,
    },
    dialogForm: {
      type: Boolean,
      default: false,
    },
    form: {
      type: Object,
      default: () => {
        return {
          id: null,
          folio_fiscal: "",
          folio: "",
          invoice_date: "",
          amount: 0,
        };
      },
    },
  },
  mounted() {
    // this.loadOptions();
  },

  computed: {
    Dialog: {
      get() {
        return this.dialogForm;
      },
      set(v) {
        if (!v) {
          this.$emit("close");
          this.$nextTick(() => {
            // this.form = { ...this.formDefault };
            this.editedIndex = -1;
          });
        } else {
          return this.$emit("edit");
        }
      },
    },
  },
  data() {
    return {
      valid: true,
      editedIndex: -1,
      formDefault: {
        folio_fiscal: "",
        folio: "",
        invoice_date: "",
        amount: 0,
      },
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
    editItem(item) {
      this.editedIndex = this.items.indexOf(item);
      this.form = Object.assign({}, item);
      this.Dialog = true;
    },
    deleteItem(item) {
      const _this = this;
      _this.$store.commit("showDialog", {
        type: "confirm",
        title: "Confimacion de eliminacion",
        message: "¿Estás seguro de que quieres eliminar este registro?",
        okCb: () => {
          _this.editedIndex = _this.items.indexOf(item);
          _this.items.splice(_this.editedIndex, 1);
        },
        cancelCb: () => {
          console.log("CANCEL");
        },
      });
    },
    registerInvoice() {},
    registerDateToPayment() {},
    async save() {
      const _this = this;
      if (!_this.$refs.form.validate()) return;
      let payload = {
        id: _this.form.id || null,
        folio_fiscal: _this.form.folio_fiscal,
        folio: _this.form.folio,
        amount: _this.form.amount,
        invoice_date: _this.form.invoice_date,
      };
      await axios
        .post(
          `/admin/purchase-order/store-invoice/${_this.purchaseId}`,
          payload
        )
        .then(function (response) {
          _this.$store.commit("showSnackbar", {
            message: response.data.message,
            color: "success",
            duration: 3000,
          });
          _this.$eventBus.$emit("ORDERS_REFRESH");
          _this.Dialog = false;
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
