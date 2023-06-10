<template>
  <div>
    <v-simple-table dense>
      <template v-slot:default>
        <tbody>
          <tr>
            <td>
              <v-list-item-content>
                <v-list-item-title>Metodo de Pago</v-list-item-title>
                <v-list-item-subtitle
                  class="text-wrap"
                  style="max-width: 200px"
                >
                  {{ form.metodo_pago ? form.metodo_pago.description : "" }}
                </v-list-item-subtitle>
              </v-list-item-content>
            </td>
            <td class="text-right" style="width: 50px">
              {{ form.metodo_pago ? form.metodo_pago.clave : "" }}
            </td>
          </tr>
          <tr>
            <td>
              <v-list-item-content>
                <v-list-item-title>USO CFDI</v-list-item-title>
                <v-list-item-subtitle
                  class="text-wrap"
                  style="max-width: 200px"
                >
                  {{ form.uso_cfdi ? form.uso_cfdi.description : "" }}
                </v-list-item-subtitle>
              </v-list-item-content>
            </td>
            <td class="text-right" style="width: 50px">
              {{ form.uso_cfdi ? form.uso_cfdi.clave : "" }}
            </td>
          </tr>
          <tr>
            <td>
              <v-list-item-content>
                <v-list-item-title>Forma de Pago</v-list-item-title>
                <v-list-item-subtitle
                  class="text-wrap"
                  style="max-width: 200px"
                >
                  {{ form.forma_pago ? form.forma_pago.description : "" }}
                </v-list-item-subtitle>
              </v-list-item-content>
            </td>
            <td class="text-right" style="width: 50px">
              {{ form.forma_pago ? form.forma_pago.clave : "" }}
            </td>
          </tr>
        </tbody>
      </template>
    </v-simple-table>
    <v-dialog v-model="Dialog" max-width="600px" persistent>
      <v-card>
        <v-card-title>
          <span class="text-h5">Configuracion Facturacion</span>
        </v-card-title>
        <v-card-text>
          <v-container>
            <v-form v-model="valid" ref="formInvoiceConfig" lazy-validation>
              <v-row class="overline">
                <v-col cols="12" md="12">
                  <v-select
                    v-model="form.metodo_pago"
                    :items="options.metodoPago"
                    :hint="
                      `${form.metodo_pago ? form.metodo_pago.description : ''}`
                    "
                    persistent-hint
                    label="*Metodo Pago:"
                    item-text="clave"
                    item-value="clave"
                    return-object
                    outlined
                    :rules="[(v) => !!v || 'Es Requerido']"
                    dense
                  >
                    <template v-slot:item="data">
                      <v-list-item-content>
                        <v-list-item-title>
                          {{ data.item.clave }}
                        </v-list-item-title>
                        <v-list-item-subtitle>
                          {{ data.item.description }}
                        </v-list-item-subtitle>
                      </v-list-item-content>
                    </template>
                  </v-select>
                </v-col>
                <v-col cols="12" md="6">
                  <v-select
                    v-model="form.uso_cfdi"
                    :items="usocfdi"
                    label="*UsoCFDI:"
                    item-text="clave"
                    item-value="clave"
                    :hint="`${form.uso_cfdi ? form.uso_cfdi.description : ''}`"
                    :rules="[(v) => !!v || 'Es Requerido']"
                    persistent-hint
                    return-object
                    outlined
                    dense
                  >
                    <template v-slot:item="data">
                      <v-list-item-content>
                        <v-list-item-title>
                          {{ data.item.clave }}
                        </v-list-item-title>
                        <v-list-item-subtitle>
                          {{ data.item.description }}
                        </v-list-item-subtitle>
                      </v-list-item-content>
                    </template>
                  </v-select>
                </v-col>

                <v-col cols="12" md="6">
                  <v-select
                    v-model="form.forma_pago"
                    :items="options.formaPago"
                    :hint="
                      `${form.forma_pago ? form.forma_pago.description : ''}`
                    "
                    persistent-hint
                    label="*Forma Pago:"
                    item-text="clave"
                    item-value="clave"
                    return-object
                    outlined
                    :rules="[
                      (v) => !!v || 'Es Requerido',
                      (v) =>
                        !isPPD && !!v
                          ? v.clave != '99' || 'No permitido'
                          : true,
                    ]"
                    dense
                    :readonly="isPPD"
                  >
                    <template v-slot:item="data">
                      <v-list-item-content>
                        <v-list-item-title>
                          {{ data.item.clave }}
                        </v-list-item-title>
                        <v-list-item-subtitle>
                          {{ data.item.description }}
                        </v-list-item-subtitle>
                      </v-list-item-content>
                    </template>
                  </v-select>
                </v-col>
              </v-row>
            </v-form>
          </v-container>
          <small>*Indica Campo Obligatorio</small>
        </v-card-text>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn color="blue darken-1" text @click="close"> Confirmar </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </div>
</template>
<script>
export default {
  name: "PurchaseInvoiceConfigTable",
  props: {
    dialogForm: {
      type: Boolean,
      default: false,
    },
    form: {
      type: Object,
      default: () => {
        return {
          metodo_pago: null,
          uso_cfdi: null,
          forma_pago: null,
        };
      },
    },
    usocfdi: {
      type: Array,
      default: () => {
        return [];
      },
    },
  },
  data() {
    return {
      valid: true,
      editedIndex: -1,
      formDefault: {
        uso_cfdi: {},
        metodo_pago: {},
        forma_pago: {},
      },
      options: {
        usoCFDI: [],
        metodoPago: [],
        formaPago: [],
      },
    };
  },
  mounted() {
    this.loadOptions();
  },
  watch: {
    "form.metodo_pago"(value) {
      if (value.clave == "PPD") {
        this.form.forma_pago = {
          clave: "99",
          description: "Por definir",
        };
      }
    },
  },
  computed: {
    isPPD() {
      return this.form.metodo_pago?.clave == "PPD";
    },
    Dialog: {
      get() {
        return this.dialogForm;
      },
      set(v) {
        if (!v) {
          this.$emit("close");
          this.$nextTick(() => {
            this.editedIndex = -1;
          });
        } else {
          return this.$emit("edit");
        }
      },
    },
  },

  methods: {
    async loadOptions(cb) {
      const _this = this;
      await axios
        .get("/admin/purchase-order/resources/options")
        .then(function(response) {
          let { metodoPago, formaPago } = response.data.data;
          _this.options.metodoPago = metodoPago;
          _this.options.usoCFDI = _this.usocfdi;
          _this.options.formaPago = formaPago;
        });
    },
    close() {
      if (!this.$refs.formInvoiceConfig.validate()) return;
      this.Dialog = false;
    },
  },
};
</script>
<style></style>
