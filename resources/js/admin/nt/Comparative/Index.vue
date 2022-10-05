<template>
  <div>
    <v-toolbar flat class="text-h4 align-center">
      <v-icon left x-large>mdi-compare</v-icon>Mis Comparativos
      <v-spacer></v-spacer>
      <v-text-field
        v-model="filters.search"
        outlined
        dense
        prepend-icon="mdi-magnify"
        label="Buscar"
        class="mr-3"
        hide-details
      ></v-text-field>
      <v-btn color="primary" dark :to="{ name: 'nt.comparative.create' }">
        Crear Nuevo Comparativo AMS
      </v-btn>
    </v-toolbar>
    <v-spacer />
    <v-data-table
      v-bind:headers="headers"
      :options.sync="pagination"
      :items="items"
      :server-items-length="totalItems"
      fixed-header
      dense
    >
      <template #[`item.action`]="{ item }">
        <v-icon
          class="green--text"
          @click="
            $router.push({
              name: 'nt.comparative.edit',
              params: { editItemId: item.id },
            })
          "
        >
          mdi-pencil
        </v-icon>
        <v-icon class="blue--text" @click="preview(item)">
          mdi-file-find
        </v-icon>
        <v-icon class="red--text" @click="deleteItem(item)">
          mdi-trash-can
        </v-icon>
      </template>
      <template #[`item.created_at`]="{item}">
        {{ $appFormatters.formatDate(item.created_at) }}
      </template>
    </v-data-table>
    <v-dialog v-model="amsPreviewDialog" fullscreen>
      <v-card>
        <v-toolbar flat dense class="d-print-none">
          <v-btn class="secondary" @click="print()" small>Print</v-btn>
          <v-spacer />
          <v-icon
            @click="
              (amsPreviewDialog = false),
                (tableWithAms = []),
                (tableWithoutAms = []),
                (tableSaveAms = []),
                (tableDiffAms = []),
                (hectares = 0),
                (customer = {})
            "
          >
            mdi-window-close
          </v-icon>
        </v-toolbar>
        <ams-preview
          :tableDiffAms="tableDiffAms"
          :tableSaveAms="tableSaveAms"
          :tableWithAms="tableWithAms"
          :tableWithoutAms="tableWithoutAms"
          :hectares="hectares"
          :customer="customer"
          :folio="folio"
        ></ams-preview>
      </v-card>
    </v-dialog>
  </div>
</template>
<script>
import AmsPreview from "./components/AmsPreview.vue";
export default {
  components: { AmsPreview },
  data() {
    return {
      headers: [
        {
          text: "Folio",
          value: "id",
          align: "left",
          sortable: false,
        },
        {
          text: "Dirigido a / Razon Social",
          value: "customer_fullname",
          align: "left",
          sortable: false,
        },
        {
          text: "Telefono cliente",
          value: "customer_phone",
          align: "left",
          sortable: false,
        },
        {
          text: "Creado por",
          value: "created_by.email",
          align: "left",
          sortable: false,
        },
        {
          text: "Fecha Creacion",
          value: "created_at",
          align: "left",
          sortable: false,
        },
        { text: "Action", value: "action", align: "left", sortable: false },
      ],
      items: [],
      totalItems: 0,
      pagination: {
        rowsPerPage: 10,
      },
      editedId: null,

      filters: {
        search: "",
      },

      amsPreviewDialog: false,
      tableWithoutAms: [],
      tableWithAms: [],
      tableDiffAms: [],
      tableSaveAms: [],
      hectares: 0,
      customer: 0,
      folio: 0,
    };
  },
  mounted() {
    const _this = this;
    _this.$store.commit("setBreadcrumbs", [
      { label: "AMS Comparativos", name: "" },
    ]);
  },
  watch: {
    pagination: {
      handler: _.debounce(function (v) {
        this.loadAmsComparatives(() => {});
      }, 700),
      deep: true,
    },
    filters: {
      handler: _.debounce(function (v) {
        this.loadAmsComparatives(() => {});
      }, 700),
      deep: true,
    },
  },
  methods: {
    async loadAmsComparatives(cb) {
      const _this = this;

      let params = {
        ..._this.filters,
        order_sort: _this.pagination.sortDesc[0] ? "asc" : "desc",
        order_by: _this.pagination.sortBy[0] || "id",
        page: _this.pagination.page,
        per_page: _this.pagination.itemsPerPage,
      };

      await axios.get("/admin/ams-comparative", { params }).then((response) => {
        _this.items = response.data.data.data;
        _this.totalItems = response.data.data.total;
        _this.pagination.totalItems = response.data.data.total;
        (cb || Function)();
      });
    },
    async preview(item) {
      const _this = this;
      _this.hectares = item.hectares;
      _this.folio = item.id;
      _this.customer = {
        fullname: item.customer_fullname,
        email: item.customer_email,
        phone: item.customer_phone,
        city: item.customer_city,
        country: item.customer_country,
        company: item.customer_company,
      };
      await axios
        .post("/admin/ams-comparative/preview", item)
        .then(function (response) {
          _this.$store.commit("showSnackbar", {
            message: response.data.message,
            color: "success",
            duration: 3000,
          });
          _this.tableWithoutAms = response.data.data.table_without_ams;
          _this.tableWithAms = response.data.data.table_with_ams;
          _this.tableDiffAms = response.data.data.table_diff;
          _this.tableSaveAms = response.data.data.table_save;
          _this.amsPreviewDialog = true;
        })
        .catch(function (error) {
          if (error.response) {
            _this.$store.commit("showSnackbar", {
              message: error.response.data.message,
              color: "error",
              duration: 3000,
            });
            _this.amsPreview = false;
          } else if (error.request) {
            console.log(error.request);
          } else {
            console.log("Error", error.message);
          }
        });
    },
    print() {
      window.print();
    },
    editItem() {},
    deleteItem() {},
  },
};
</script>
<style></style>
