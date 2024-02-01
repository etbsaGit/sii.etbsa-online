<template>
  <v-data-table
    :headers="headers"
    :items="items"
    class="elevation-3 text-uppercase font-weight-bold"
    dense
  >
    <template v-slot:top>
      <v-toolbar flat class="align-center">
        <v-toolbar-title>Polizas Ingresos</v-toolbar-title>
        <v-divider class="mx-4" inset vertical></v-divider>
        <v-spacer></v-spacer>

        <v-text-field
          label="Buscar"
          class="mr-1"
          hide-details
          outlined
          dense
        ></v-text-field>

        <v-dialog v-model="dialog" max-width="500px">
          <template v-slot:activator="{ on, attrs }">
            <v-btn color="primary" dark v-bind="attrs" v-on="on">
              Registrar Poliza
            </v-btn>
          </template>
          <v-card>
            <v-card-title>
              <span class="text-h5">{{ formTitle }}</span>
            </v-card-title>

            <v-card-text>
              <v-container>
                <v-row>
                  <v-col cols="12">
                    <v-select
                      v-model="editedItem.agency_bank_accounts_id"
                      :items="options.agency_bank_accounts"
                      item-value="id"
                      item-text="bank_name"
                      label="Cuenta de Sucursal"
                      outlined
                    ></v-select>
                  </v-col>

                  <v-col cols="12">
                    <v-text-field
                      v-model.number="editedItem.amount"
                      label="Importe"
                      outlined
                    ></v-text-field>
                  </v-col>
                  <v-col cols="12" md="6">
                    <v-select
                      v-model="editedItem.currency_id"
                      :items="options.currencies"
                      item-value="id"
                      item-text="name"
                      label="Moneda"
                      outlined
                    ></v-select>
                  </v-col>
                  <v-col cols="12" md="6">
                    <v-text-field
                      v-model="editedItem.exchange_rate"
                      label="Tipo de Cambio"
                      outlined
                    ></v-text-field>
                  </v-col>
                  <v-col cols="12" md="6">
                    <v-select
                      v-model="editedItem.payment_source"
                      :items="options.payment_source"
                      label="Origen"
                      outlined
                    ></v-select>
                  </v-col>
                  <v-col cols="12" md="6">
                    <v-text-field
                      v-model="editedItem.reference"
                      label="Referencia"
                      hint="Folio, Factura, Numero de Referencia"
                      persistent-hint
                      outlined
                    ></v-text-field>
                  </v-col>
                  <v-col cols="12">
                    <v-text-field
                      v-model="editedItem.date_apply"
                      label="Fecha de Deposito"
                      type="date"
                      outlined
                    ></v-text-field>
                  </v-col>
                  <v-col cols="12">
                    <v-textarea
                      v-model="editedItem.description"
                      label="Descripcion de Poliza"
                      outlined
                    ></v-textarea>
                  </v-col>
                </v-row>
              </v-container>
            </v-card-text>

            <v-card-actions>
              <v-spacer></v-spacer>
              <v-btn color="blue darken-1" text @click="close"> Cancel </v-btn>
              <v-btn color="blue darken-1" text @click="save"> Save </v-btn>
            </v-card-actions>
          </v-card>
        </v-dialog>
        <v-dialog v-model="dialogDelete" max-width="500px">
          <v-card>
            <v-card-title class="text-h5">
              Seguro en Elimar Polizal?
            </v-card-title>
            <v-card-actions>
              <v-spacer></v-spacer>
              <v-btn color="blue darken-1" text @click="closeDelete">
                Cancel
              </v-btn>
              <v-btn color="blue darken-1" text @click="deleteItemConfirm">
                OK
              </v-btn>
              <v-spacer></v-spacer>
            </v-card-actions>
          </v-card>
        </v-dialog>

        <v-btn color="accent" class="ml-2" @click="initialize" dark>
          Actualizar
        </v-btn>
      </v-toolbar>
    </template>
    <template v-slot:[`item.amount`]="{ item, value }">
      {{ value | money }} - {{ item?.currency?.name }}
    </template>
    <template v-slot:[`item.date_apply`]="{ value }">
      {{ $appFormatters.formatDate(value, "LL") }}
    </template>
    <template v-slot:[`item.actions`]="{ item }">
      <v-icon small class="mr-2" @click="editItem(item)"> mdi-pencil </v-icon>
      <v-icon small @click="deleteItem(item)"> mdi-delete </v-icon>
    </template>
    <template v-slot:no-data>
      <v-btn color="primary" class="ml-1" @click="initialize"> Reset </v-btn>
    </template>
  </v-data-table>
</template>

<script>
export default {
  data: () => ({
    dialog: false,
    dialogDelete: false,
    headers: [
      { text: "Referencia", value: "reference" },
      {
        text: "Cuenta Sucursal",
        align: "start",
        sortable: false,
        value: "agency_bank_account.bank_name",
      },
      { text: "Origen Deposito", value: "payment_source" },
      { text: "Importe", value: "amount" },
      { text: "Fecha Deposito", value: "date_apply" },
      { text: "Actions", value: "actions", sortable: false },
    ],
    items: [],
    options: {
      agency_bank_accounts: [],
      currencies: [],
      payment_source: [],
    },
    editedIndex: -1,
    editedItem: {
      agency_bank_accounts_id: null,
      currency_id: 1,
      description: "",
      date_apply: null,
      policy_type: "Ingreso",
      payment_source: null,
      reference: null,
      exchange_rate: 1,
      amount: 0,
    },
    defaultItem: {
      agency_bank_accounts_id: null,
      currency_id: 1,
      description: "",
      date_apply: null,
      policy_type: "Ingreso",
      payment_source: null,
      reference: null,
      exchange_rate: 1,
      amount: 0,
    },
  }),

  computed: {
    formTitle() {
      return this.editedIndex === -1 ? "Registrar Poliza" : "Editar Poliza";
    },
  },

  watch: {
    dialog(val) {
      val || this.close();
    },
    dialogDelete(val) {
      val || this.closeDelete();
    },
  },

  created() {
    this.initialize();
  },

  methods: {
    async initialize() {
      const _this = this;
      try {
        let {
          data: { data, message },
        } = await axios.get("/admin/bank-policy");
        _this.items = data.bank_policies;
        _this.options = data.options;

        _this.$store.commit("showSnackbar", {
          message: message,
          color: "success",
          duration: 3000,
        });
      } catch (error) {
        _this.$store.commit("showSnackbar", {
          message: error.response.data.message,
          color: "error",
          duration: 3000,
        });
      }
    },

    editItem(item) {
      this.editedIndex = item.id;
      this.editedItem = Object.assign({}, item);
      this.dialog = true;
    },

    deleteItem(item) {
      this.editedIndex = item.id;
      this.editedItem = Object.assign({}, item);
      this.dialogDelete = true;
    },

    async deleteItemConfirm() {
      const _this = this;
      try {
        console.log("DELETE", _this.editedIndex);
        await axios.delete(`/admin/bank-policy/${_this.editedIndex}`);
      } catch (error) {}
      this.closeDelete();
      this.initialize();
    },

    close() {
      this.dialog = false;
      this.$nextTick(() => {
        this.editedItem = Object.assign({}, this.defaultItem);
        this.editedIndex = -1;
      });
    },

    closeDelete() {
      this.dialogDelete = false;
      this.$nextTick(() => {
        this.editedItem = Object.assign({}, this.defaultItem);
        this.editedIndex = -1;
      });
    },

    async save() {
      const _this = this;
      if (this.editedIndex > -1) {
        try {
          await axios.put(
            `/admin/bank-policy/${_this.editedIndex}`,
            _this.editedItem
          );
        } catch (error) {}
      } else {
        try {
          await axios.post(`/admin/bank-policy`, _this.editedItem);
        } catch (error) {}
      }
      this.close();
      this.initialize();
    },
  },
};
</script>