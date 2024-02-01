<template>
  <v-container fluid>
    <v-data-iterator :items="items" hide-default-footer>
      <template v-slot:header>
        <v-toolbar class="mb-2" color="indigo darken-5" dark flat>
          <v-toolbar-title>
            Conteo de Inventario por Sucursal y Categoria
          </v-toolbar-title>
          <v-spacer />
          <v-toolbar-items>
            <v-icon @click="getReportByAgency">mdi-refresh</v-icon>
          </v-toolbar-items>
        </v-toolbar>
      </template>

      <template v-slot:default="{ items }">
        <v-row dense>
          <v-col v-for="item in items" :key="item.sucursal" cols="12" md="3">
            <v-card>
              <v-card-title class="font-weight-bold">
                {{ item.sucursal }}
              </v-card-title>

              <v-divider></v-divider>

              <v-simple-table
                v-for="category in Object.keys(item.value)"
                :key="category"
                dense
              >
                <template #default>
                  <caption class="my-1">
                    {{
                      category
                    }}
                  </caption>
                  <thead>
                    <tr>
                      <th class="text-left">Producto</th>
                      <th></th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr
                      v-for="product in item.value[category].products"
                      :key="product.name"
                    >
                      <td>{{ product.name }}</td>
                      <td class="text-center font-weight-bold">
                        {{ product.count }}
                      </td>
                    </tr>
                    <tr>
                      <td>Total ({{ category }}):</td>
                      <td class="blue--text text-center font-weight-bold">
                        {{
                          item.value[category].products.reduce(
                            (acc, crr) => acc + crr.count,
                            0
                          )
                        }}
                      </td>
                    </tr>
                  </tbody>
                </template>
              </v-simple-table>
            </v-card>
          </v-col>
        </v-row>
      </template>
    </v-data-iterator>
  </v-container>
</template>

<script>
export default {
  data: () => ({
    // itemsPerPage: 4,
    items: [],
  }),
  mounted() {
    this.getReportByAgency();
  },
  methods: {
    async getReportByAgency() {
      const _this = this;
      const {
        data: {
          data: { result },
          message,
        },
      } = await axios.get("admin/products/inventory/create");
      const array = Object.keys(result).map((key) => ({
        sucursal: key,
        value: result[key],
      }));
      console.log(Object.entries(result), Object.values(result), array);
      _this.items = array;

      _this.$store.commit("showSnackbar", {
        message: message,
        color: "success",
        duration: 3000,
      });
    },
  },
};
</script>