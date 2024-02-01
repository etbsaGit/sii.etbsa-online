<template>
  <v-container fluid>
    <v-data-table
      v-model="selected"
      :headers="headers"
      :items="items"
      :options.sync="pagination"
      :server-items-length="totalItems"
      item-key="ticket_card"
      show-select
      class="text-truncate blue--text caption text-uppercase elevation-1"
      calculate-widths
      fixed-header
      caption
      dense
    >
      <template v-slot:top>
        <v-card
          class="d-flex justify-end align-center flex-wrap px-3 py-1"
          flat
        >
          <v-card
            flat
            class="d-flex d-flex justify-space-between align-center flex-wrap py-2"
            :class="'flex-grow-1 flex-shrink-0'"
          >
            <v-text-field
              v-model="search"
              label="Buscar"
              class="pa-2"
              prepend-icon="mdi-magnify"
              hide-details
              clearable
              outlined
              filled
              dense
            ></v-text-field>
          </v-card>
          <v-spacer></v-spacer>
          <v-divider class="mx-2" inset vertical></v-divider>
          <table-header-buttons
            :reloadTable="initialize"
          ></table-header-buttons>
        </v-card>
        <v-toolbar flat>
          <v-toolbar-title>Ticket Cards</v-toolbar-title>
          <v-divider class="mx-4" inset vertical></v-divider>
          <v-spacer></v-spacer>
          <v-chip
            v-if="selected.length > 0"
            color="orange"
            filter
            label
            outlined
            class="mr-2"
            @click="resetBalance()"
          >
            {{ selected.length }} - Reset Balance
          </v-chip>
          <v-dialog v-model="dialog" max-width="500px">
            <template v-slot:activator="{ on, attrs }">
              <v-btn color="primary" v-bind="attrs" v-on="on">
                Nuevo Registro
              </v-btn>
            </template>
            <v-card>
              <v-card-title>
                <span class="text-h5">{{ formTitle }}</span>
              </v-card-title>

              <v-card-text>
                <v-container>
                  <v-row>
                    <v-col cols="12" sm="6" md="6">
                      <v-text-field
                        v-model="editedItem.ticket_card"
                        label="Ticket Card"
                        prepend-icon="mdi-ticket-confirmation"
                      ></v-text-field>
                    </v-col>
                    <v-col cols="12" sm="6" md="6">
                      <v-text-field
                        v-model="editedItem.account_balance"
                        label="Balance"
                        prefix="$"
                      ></v-text-field>
                    </v-col>
                  </v-row>
                </v-container>
              </v-card-text>

              <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn color="blue darken-1" text @click="close">
                  Cancel
                </v-btn>
                <v-btn color="blue darken-1" text @click="save">
                  Guardar
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
      <template v-slot:[`item.account_balance`]="{ value }">
        {{ value | money }}
      </template>
      <template v-slot:[`item.updated_at`]="{ value }">
        {{ $appFormatters.formatDate(value, "lll") }}
      </template>
      <template v-slot:[`item.actions`]="{ item }">
        <v-icon small class="mr-2" @click="editItem(item)">
          mdi-pencil
        </v-icon>
        <!-- <v-icon small @click="deleteItem(item)">
          mdi-delete
        </v-icon> -->
      </template>
    </v-data-table>
  </v-container>
</template>

<script>
import TableHeaderButtons from "@admin/components/shared/TableHeaderButtons.vue";
export default {
  name: "TicketCards",
  components: { TableHeaderButtons },
  data: () => ({
    dialog: false,
    dialogDelete: false,
    selected: [],
    headers: [
      {
        text: "Ticket Card",
        align: "start",
        sortable: false,
        value: "ticket_card",
      },
      { text: "Balance", align: "right", value: "account_balance" },
      {
        text: "Matricula Asignada",
        align: "center",
        value: "vehicle.matricula",
      },
      {
        text: "Ultimo Cambio",
        align: "center",
        value: "updated_at",
      },
      { text: "Actions", align: "right", value: "actions", sortable: false },
    ],
    items: [],
    search: "",
    totalItems: 0,
    pagination: {
      itemsPerPage: 10,
      page: 1,
    },
    editedIndex: -1,
    editedItem: {
      ticket_card: "",
      account_balance: 0,
    },
    defaultItem: {
      ticket_card: "",
      account_balance: 0,
    },
  }),

  computed: {
    formTitle() {
      return this.editedIndex === -1
        ? "Nueva ticket Card"
        : "Editar Ticket Card";
    },
    Ids() {
      return this.selected
        .map((e) => {
          return e.ticket_card;
        })
        .join(",");
    },
  },

  watch: {
    dialog(val) {
      val || this.close();
    },
    dialogDelete(val) {
      val || this.closeDelete();
    },
    pagination: {
      handler: _.debounce(function () {
        this.initialize();
      }, 999),
      deep: true,
    },
    search: _.debounce(function (v) {
      this.initialize();
    }, 999),
  },

  created() {
    this.initialize();
  },

  methods: {
    async initialize() {
      const _this = this;
      let params = {
        search: _this.search,
        page: _this.pagination.page,
        per_page: _this.pagination.itemsPerPage,
      };
      this.items = await axios
        .get("/admin/vehicles-ticket-card", { params: params })
        .then((response) => {
          let Response = response.data.data;
          _this.totalItems = Response.total;
          _this.pagination.totalItems = Response.total;
          return Response.data;
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
      const _this = this;
      if (this.editedIndex > -1) {
        // Object.assign(this.items[this.editedIndex], this.editedItem);
        _this.$store.commit("showDialog", {
          type: "confirm",
          icon: "mdi-pencil",
          title: "Actualizacion de TicketCard",
          message: "Se Actualizara el Registro",
          okCb: () => {
            let payload = {
              account_balance: _this.editedItem.account_balance,
            };
            axios
              .put(
                `/admin/vehicles-ticket-card/${_this.editedItem.ticket_card}`,
                payload
              )
              .then((response) => {
                _this.$store.commit("showSnackbar", {
                  message: response.data.message,
                  color: "success",
                  duration: 3000,
                });
                _this.close();
                _this.initialize();
              });
          },
          cancelCb: () => {
            console.log("CANCEL");
          },
        });
      } else {
        // this.items.push(this.editedItem);
        _this.$store.commit("showDialog", {
          type: "confirm",
          icon: "mdi-credit-card-plus-outline",
          title: "Reseteo de Balance",
          message: "Se agregara un nuevo Registro",
          okCb: () => {
            let payload = {
              ticket_card: _this.editedItem.ticket_card,
              account_balance: _this.editedItem.account_balance,
            };
            axios
              .post(`/admin/vehicles-ticket-card`, payload)
              .then((response) => {
                _this.$store.commit("showSnackbar", {
                  message: response.data.message,
                  color: "success",
                  duration: 3000,
                });
                _this.close();
                _this.initialize();
              });
          },
          cancelCb: () => {
            console.log("CANCEL");
          },
        });
      }
    },
    resetBalance() {
      const _this = this;

      _this.$store.commit("showDialog", {
        type: "confirm",
        icon: "warning",
        title: "Reseteo de Balance",
        message: "¿Esta seguro en Resetiar el Balance?",
        okCb: () => {
          axios
            .post("/admin/vehicle-ticket-card/reset", { id: _this.Ids })
            .then((response) => {
              _this.$store.commit("showSnackbar", {
                message: response.data.message,
                color: "success",
                duration: 3000,
              });
              _this.initialize();
            });
        },
        cancelCb: () => {
          console.log("CANCEL");
        },
      });
    },
  },
};
</script>
