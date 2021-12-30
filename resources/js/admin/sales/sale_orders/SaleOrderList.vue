<template>
  <div>
    <v-skeleton-loader
      v-if="loading"
      class="mx-auto"
      type="table"
    ></v-skeleton-loader>
    <v-data-table
      v-else
      :headers="headers"
      :options.sync="pagination"
      :items="items"
      :server-items-length="totalItems"
      class="elevation-4 text-uppercase caption"
      fixed-header
      dense
    >
      <template #top>
        <v-toolbar>
          <v-toolbar-title>
            Pedidos de Venta
          </v-toolbar-title>
          <v-spacer />
          <v-btn color="primary" :to="{ name: 'sale-orders.create' }">
            Crear Nuevo Pedido
          </v-btn>
        </v-toolbar>
      </template>
    </v-data-table>
  </div>
</template>
<script>
export default {
  name: "SaleOrderList",
  data() {
    return {
      loading: true,
      headers: [
        {
          text: "Folio",
          value: "full_name",
          align: "left",
          sortable: false,
        },
        {
          text: "Cliente",
          value: "town",
          align: "left",
          sortable: false,
        },
        {
          text: "Importe",
          value: "amount",
          align: "left",
          sortable: false,
        },
      ],
      items: [],
      totalItems: 0,
      pagination: {
        rowsPerPage: 10,
      },

      filters: {
        search: "",
      },
    };
  },
  mounted() {
    this.loadSaleOrder();
  },
  methods: {
    loadSaleOrder() {
      setTimeout(() => {
        this.loading = false;
      }, 1000);
    },
  },
};
</script>
<style></style>
