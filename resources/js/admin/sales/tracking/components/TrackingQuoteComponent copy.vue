<template>
  <v-data-table :headers="headers" :items="items" hide-default-footer>
    <template v-slot:top>
      <v-toolbar flat>
        <v-spacer></v-spacer>
        <v-dialog v-model="dialog" max-width="900px" persistent>
          <template v-slot:activator="{ on, attrs }">
            <v-btn color="primary" dark class="mb-2" v-bind="attrs" v-on="on">
              Crear una Cotizacion
            </v-btn>
          </template>
          <v-card>
            <v-card-title>
              <span class="text-h5">{{ formTitle }}</span>
            </v-card-title>

            <v-card-text>
              <v-container>
                <quote-form
                  :editedIndex="editedIndex"
                  :editedItem.sync="editedItem"
                ></quote-form>
              </v-container>
            </v-card-text>

            <v-card-actions>
              <v-spacer></v-spacer>
              <v-btn color="blue darken-1" dark @click="close">
                Cancelar
              </v-btn>
              <v-btn color="blue darken-1" text @click="save">
                Crear Cotizacion
              </v-btn>
            </v-card-actions>
          </v-card>
        </v-dialog>
        <v-dialog v-model="dialogDelete" max-width="500px">
          <v-card>
            <v-card-title class="text-h5">
              Are you sure you want to delete this item?
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
      </v-toolbar>
    </template>
    <template #[`item.total`]="{item}">
      {{ item.total | money }}
    </template>
    <template #[`item.actions`]="{ item }">
      <v-icon small class="mr-2" @click="editItem(item)">
        mdi-pencil
      </v-icon>
      <v-icon small @click="deleteItem(item)">
        mdi-delete
      </v-icon>
    </template>
    <template v-slot:no-data>
      <v-btn color="primary" @click="initialize">
        Reset
      </v-btn>
    </template>
  </v-data-table>
</template>
<script>
import QuoteForm from "../forms/TrackingQuoteForm.vue";
export default {
  components: { QuoteForm },
  data: () => ({
    renderComponent: true,
    dialog: false,
    dialogDelete: false,
    headers: [
      {
        text: "Folio",
        align: "start",
        sortable: false,
        value: "id",
      },
      { text: "Cliente", value: "calories" },
      { text: "Total", value: "total" },
      { text: "F. Creacion", value: "updated_at" },
      { text: "F. Vencimiento", value: "date_due" },
      { text: "Actions", value: "actions", sortable: false },
    ],
    items: [],
    editedIndex: -1,
    editedItem: {
      id: null,
      date_due: null,
      tracking_id: null,
      currency: null,
      subtotal: 0,
      tax: 0.16,
      total: 0,
      observation: "",
      created_at: "",
      updated_at: "",
      products: [],
    },
    defaultItem: {
      id: null,
      date_due: null,
      tracking_id: null,
      currency: null,
      subtotal: 0,
      tax: 0.16,
      total: 0,
      observation: "",
      created_at: "",
      updated_at: "",
      products: [],
    },
  }),

  computed: {
    formTitle() {
      return this.editedIndex === -1
        ? "Crear Cotizacion"
        : "Modificar Cotizacion";
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
    forceRerender() {
      // Removing my-component from the DOM
      this.renderComponent = false;

      this.$nextTick(() => {
        // Adding the component back in
        this.renderComponent = true;
      });
    },
    async initialize() {
      const _this = this;
      let params = {
        paginate: "no",
      };

      _this.items = await axios.get("/admin/quotes", { params }).then((res) => {
        return res.data.data;
      });
    },

    editItem(item) {
      this.editedIndex = this.items.indexOf(item);
      this.editedItem = Object.assign({}, item);
      this.dialog = true;
    },

    deleteItem(item) {
      this.editedIndex = this.items.indexOf(item);
      this.editedItem = Object.assign({}, item);
      this.dialogDelete = true;
    },

    deleteItemConfirm() {
      this.items.splice(this.editedIndex, 1);
      this.closeDelete();
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

    save() {
      if (this.editedIndex > -1) {
        Object.assign(this.items[this.editedIndex], this.editedItem);
      } else {
        this.items.push(this.editedItem);
      }
      this.close();
    },
  },
};
</script>
<style></style>
