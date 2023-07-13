<template>
  <v-sheet>
    <v-toolbar class="mb-4" color="indigo darken-5" flat>
      <v-toolbar-title class="white--text text-uppercase">
        Conteo de Inventario por Sucursal y Categoria
      </v-toolbar-title>
      <v-spacer />
      <v-select
        v-model="search"
        :items="items"
        item-text="sucursal"
        item-value="sucursal"
        label="Filtro"
        placeholder="Seleccione Sucursal"
        class="ma-2"
        deletable-chips
        chips
        hide-details
        clearable
        multiple
        outlined
        filled
        solo
      >
      </v-select>
      <v-toolbar-items>
        <v-icon @click="getReportByAgency" dark>mdi-refresh</v-icon>
      </v-toolbar-items>
    </v-toolbar>
    <v-slide-group v-model="model" class="pa-4" show-arrows center-active>
      <v-slide-item
        v-for="item in Agencies"
        :key="item.sucursal"
        v-slot="{ active, toggle }"
        :value="item"
      >
        <v-card
          :color="active ? 'primary' : 'grey lighten-4'"
          :dark="active"
          class="ma-4"
          width="375"
          @click="toggle"
        >
          <v-card-title class="font-weight-bold">
            {{ item.sucursal }}
          </v-card-title>
          <v-divider></v-divider>

          <v-simple-table
            v-for="category in Object.keys(item.value)"
            :key="category"
            fixed-header
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
                <!-- <tr>
                  <td>Total ({{ category }}):</td>
                  <td class="blue--text text-center font-weight-bold">
                    {{
                      item.value[category].products.reduce(
                        (acc, crr) => acc + crr.count,
                        0
                      )
                    }}
                  </td>
                </tr> -->
              </tbody>
            </template>
          </v-simple-table>
        </v-card>
      </v-slide-item>
    </v-slide-group>

    <v-expand-transition>
      <v-sheet v-if="model != null" tile>
        <v-container fluid>
          <v-row>
            <v-col
              v-for="(total, categoria) in Result.totalPorCategoria"
              :key="categoria"
              cols="12"
              md="6"
              lg="3"
            >
              <v-card>
                <v-card-text>
                  <div class="d-flex align-center">
                    <div class="me-3">
                      <VAvatar
                        color="green"
                        rounded
                        size="42"
                        class="elevation-1"
                      >
                        <VIcon size="24" dark> mdi-shape </VIcon>
                      </VAvatar>
                    </div>

                    <div class="d-flex flex-column">
                      <span class="text-caption">
                        {{ categoria }}
                      </span>
                      <span class="text-h4"> {{ total }}</span>
                    </div>
                  </div>
                </v-card-text>
              </v-card>
            </v-col>
          </v-row>
          <v-row>
            <v-col cols="12" md="6" lg="3">
              <v-card>
                <v-card-text>
                  <div class="d-flex align-center">
                    <div class="me-3">
                      <VAvatar
                        color="green"
                        rounded
                        size="42"
                        class="elevation-1"
                      >
                        <VIcon size="24" dark> mdi-tractor </VIcon>
                      </VAvatar>
                    </div>

                    <div class="d-flex flex-column">
                      <span class="text-caption"> Total General </span>
                      <span class="text-h4">
                        {{ Result.totalGeneral }}
                      </span>
                    </div>
                  </div>
                </v-card-text>
              </v-card>
            </v-col>
          </v-row>
        </v-container>
      </v-sheet>
    </v-expand-transition>
  </v-sheet>
</template>

<script>
export default {
  data: () => ({
    // itemsPerPage: 4,
    items: [],
    model: null,
    search: [],
  }),
  mounted() {
    this.getReportByAgency();
  },
  computed: {
    Agencies() {
      const _this = this;
      return this.items.filter((item) => {
        // return _this.search ? item.sucursal == _this.search : true;
        return _this.search.length > 0
          ? _this.search.some((s) => s == item.sucursal)
          : true;
      });
    },
    Result() {
      const _this = this;

      const result = {
        totalPorCategoria: {},
        totalGeneral: 0,
      };
      if (!_this.model != null) {
        for (const categoria in _this.model.value) {
          const productos = _this.model.value[categoria].products;
          const totalProductos = productos.reduce(
            (total, producto) => total + producto.count,
            0
          );
          result.totalPorCategoria[categoria] = totalProductos;
          result.totalGeneral += totalProductos;
        }
      }

      return result;
    },
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
      // console.log(Object.entries(result), Object.values(result), array);
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