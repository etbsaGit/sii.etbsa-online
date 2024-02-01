<template>
  <v-container fluid>
    <v-data-iterator :items="items" hide-default-footer>
      <template v-slot:header>
        <v-toolbar class="mb-2" color="indigo darken-5" dark flat>
          <v-toolbar-title>Reporte Inventario Sucursal</v-toolbar-title>
          <v-spacer />
          <v-toolbar-items>
            <v-icon @click="getReportByAgency">mdi-refresh</v-icon>
          </v-toolbar-items>
        </v-toolbar>
      </template>

      <template v-slot:default="{ items }">
        <v-row dense>
          <v-col v-for="item in items" :key="item.sucursal" cols="12" md="2">
            <v-card>
              <v-card-title class="font-weight-bold">
                {{ item.sucursal }}
              </v-card-title>

              <v-divider></v-divider>

              <v-list
                v-for="category in Object.keys(item.value)"
                :key="category"
                dense
                class="caption"
                style="line-height: 0px"
              >
                <v-subheader inset>
                  {{ category }}
                </v-subheader>

                <v-list-item
                  v-for="product in item.value[category].products"
                  :key="product.name"
                >
                  <v-list-item-content class="pa-0">
                    <v-list-item-title class="text-wrap">
                      {{ product.name }}
                    </v-list-item-title>
                  </v-list-item-content>

                  <v-list-item-action class="ma-0">
                    <v-list-item-action-text
                      class="font-weight-bold align-center title"
                    >
                      {{ product.count }}
                    </v-list-item-action-text>
                  </v-list-item-action>
                </v-list-item>
                <v-divider />
                <v-list-item color="yellow">
                  <v-list-item-content class="pa-0">
                    <v-list-item-title class="text-wrap">
                      TOTAL {{ category }}
                    </v-list-item-title>
                  </v-list-item-content>

                  <v-list-item-action class="ma-0">
                    <v-list-item-action-text
                      class="font-weight-bold align-center title blue--text"
                    >
                      {{
                        item.value[category].products.reduce(
                          (acc, crr) => acc + crr.count,
                          0
                        )
                      }}
                    </v-list-item-action-text>
                  </v-list-item-action>
                </v-list-item>
              </v-list>
              <!-- <v-list>
                <v-list-item color="yellow">
                  <v-list-item-content class="pa-0">
                    <v-list-item-title class="text-wrap">
                      TOTAL {{ item.sucursal }}
                    </v-list-item-title>
                  </v-list-item-content>

                  <v-list-item-action class="ma-0">
                    <v-list-item-action-text
                      class="font-weight-bold align-center title"
                    >
                      99
                    </v-list-item-action-text>
                  </v-list-item-action>
                </v-list-item>
              </v-list> -->
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