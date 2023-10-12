<template>
  <div class="component-">
    <!-- form -->
    <v-card>
      <v-card-title>
        <v-icon>mdi-account</v-icon> Configurar Vendedor
      </v-card-title>
      <v-divider class="mb-2"></v-divider>
      <v-card-text>
        <v-form v-model="valid" ref="permissionFormEdit" lazy-validation>
          <v-row dense>
            <v-col cols="12" md="3">
              <div class="text-h6">Vendedor(a): {{ Seller.name }}</div>
              <v-text-field
                label="Clave Vendedor"
                v-model="title"
                filled
                hide-details
              ></v-text-field>
            </v-col>
            <v-col cols="12">
              <v-card>
                <v-card-title>
                  <v-icon>mdi-domain</v-icon> Configuracion Vendedor Sucursal
                </v-card-title>
                <v-divider></v-divider>
                <v-row dense>
                  <v-col
                    cols="12"
                    md="3"
                    v-for="(g, k) in options.agencies"
                    :key="k"
                    class="caption"
                  >
                    <v-switch
                      dense
                      hide-details
                      v-bind:label="g.title"
                      v-model="seller_agency[g.id]"
                    ></v-switch>
                  </v-col>
                </v-row>
              </v-card>
            </v-col>
            <v-col cols="12" mt-4>
              <h1 class="title">
                <v-icon>mdi-office-building</v-icon> Configuracion Vendedor
                Departamentos
              </h1>
              <v-divider></v-divider>
              <v-row dense>
                <v-col
                  cols="12"
                  md="3"
                  v-for="(g, k) in options.departments"
                  :key="k"
                  class="caption"
                >
                  <v-switch
                    dense
                    hide-details
                    v-bind:label="g.title"
                    v-model="seller_type[g.id]"
                  ></v-switch>
                </v-col>
              </v-row>
            </v-col>
            <v-col cols="12" mt-4>
              <h1 class="title">
                <v-icon>mdi-office-building</v-icon> Configuracion Gerente
                Categorias
              </h1>
              <v-divider></v-divider>
              <v-row dense>
                <v-col
                  cols="12"
                  md="3"
                  v-for="(g, k) in options.categories"
                  :key="k"
                  class="caption"
                >
                  <v-switch
                    dense
                    hide-details
                    v-bind:label="g.name"
                    v-model="seller_category[g.id]"
                  ></v-switch>
                </v-col>
              </v-row>
            </v-col>
            <v-col cols="12">
              <v-btn block @click="save()" :disabled="!valid" color="primary">
                Guardar
              </v-btn>
            </v-col>
          </v-row>
        </v-form>
      </v-card-text>
    </v-card>
    <!-- /form -->
  </div>
</template>

<script>
export default {
  props: {
    propSellerId: {
      required: true,
    },
  },
  data() {
    return {
      Seller: { name: "" },
      valid: false,
      isLoading: false,
      title: "",
      seller_type: [],
      seller_agency: [],
      seller_category: [],
      options: {
        departments: [],
        agencies: [],
        categories: [],
      },
    };
  },
  mounted() {
    const self = this;
    self.$store.commit("setBreadcrumbs", [
      { label: "Vendedores", to: { name: "sellers.list" } },
    ]);
  },
  created() {
    this.loadSeller(() => {});
    this.loadResources(() => {});
  },
  methods: {
    save() {
      const self = this;

      let payload = {
        seller_key: self.title,
        seller_type: self.seller_type,
        seller_agency: self.seller_agency,
        seller_category: self.seller_category,
      };

      self.isLoading = true;

      axios
        .put("/admin/sellers/" + self.propSellerId, payload)
        .then(function (response) {
          self.$store.commit("showSnackbar", {
            message: response.data.message,
            color: "success",
            duration: 3000,
          });

          self.$store.commit("hideLoader");
          // self.$router.push({ name: "sellers.list" });
        })
        .catch(function (error) {
          self.$store.commit("hideLoader");

          if (error.response) {
            self.$store.commit("showSnackbar", {
              message: error.response.data.message,
              color: "error",
              duration: 3000,
            });
          } else if (error.request) {
            console.log(error.request);
          } else {
            console.log("Error", error.message);
          }
        });
    },
    loadSeller(cb) {
      const self = this;

      axios
        .get("/admin/sellers/" + self.propSellerId)
        .then(function (response) {
          let Seller = response.data.data;
          self.Seller = Seller;
          self.title = Seller.seller_key;

          _.each(self.Seller.seller_type, (g) => {
            self.seller_type[g.id] = true;
          });
          _.each(self.Seller.seller_agency, (g) => {
            self.seller_agency[g.id] = true;
          });
          _.each(self.Seller.seller_category, (g) => {
            self.seller_category[g.id] = true;
          });

          (cb || Function)();
        });
    },
    loadResources(cb) {
      const self = this;

      let params = {
        paginate: "no",
      };

      axios
        .get("/admin/tracking/sales_history/resources")
        .then(function (response) {
          let Data = response.data.data;
          self.options.departments = Data.departments;
          self.options.agencies = Data.agencies;
          self.options.categories = Data.categories;

          _.each(self.options.departments, (d) => {
            d.selected = false;
          });
          _.each(self.options.agencies, (d) => {
            d.selected = false;
          });
          _.each(self.options.categories, (d) => {
            d.selected = false;
          });

          cb();
          (cb || Function)();
        });
    },
  },
};
</script>
