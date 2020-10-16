<template>
  <div class="component-wrap">
    <!-- form -->
    <v-card>
      <v-card-title>
        <v-icon>mdi-account</v-icon> Configurar Vendedor
      </v-card-title>
      <v-divider class="mb-2"></v-divider>
      <v-form v-model="valid" ref="permissionFormEdit" lazy-validation>
        <v-container grid-list-md>
          <v-layout row wrap>
            <v-flex xs12 md3>
              <div class="text-h6">Vendedor(a): {{ Seller.name }}</div>
              <v-text-field
                label="Clave Vendedor"
                v-model="title"
                filled
                hide-details
              ></v-text-field>
            </v-flex>
            <v-flex xs12>
              <h1 class="title">
                <v-icon>mdi-domain</v-icon> Configuracion Vendedor Sucursal
              </h1>
              <v-divider></v-divider>
            </v-flex>
            <v-layout wrap mx-2 class="caption">
              <v-flex
                xs12
                md3
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
              </v-flex>
            </v-layout>
            <v-flex xs12 mt-4>
              <h1 class="title">
                <v-icon>mdi-office-building</v-icon> Configuracion Vendedor Departamentos
              </h1>
              <v-divider></v-divider>
            </v-flex>
            <v-layout wrap mx-2 class="caption">
              <v-flex
                xs12
                md3
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
              </v-flex>
            </v-layout>
            <v-flex xs12>
              <v-btn
                block
                @click="save()"
                :disabled="!valid"
                color="primary"
                dark
                >Guardar</v-btn
              >
            </v-flex>
          </v-layout>
        </v-container>
      </v-form>
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
      options: {
        departments: [],
        agencies: [],
      },
    };
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
      };

      self.isLoading = true;

      axios
        .put("/admin/sellers/" + self.propSellerId, payload)
        .then(function(response) {
          self.$store.commit("showSnackbar", {
            message: response.data.message,
            color: "success",
            duration: 3000,
          });

          self.$store.commit("hideLoader");
          self.$router.push({ name: "sellers.list" });
        })
        .catch(function(error) {
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

      axios.get("/admin/sellers/" + self.propSellerId).then(function(response) {
        let Seller = response.data.data;
        self.Seller = Seller;
        self.title = Seller.seller_key;

        _.each(self.Seller.seller_type, (g) => {
          self.seller_type[g.id] = true;
        });
        _.each(self.Seller.seller_agency, (g) => {
          self.seller_agency[g.id] = true;
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
        .then(function(response) {
          let Data = response.data.data;
          self.options.departments = Data.departments;
          self.options.agencies = Data.agencies;

          _.each(self.options.departments, (d) => {
            d.selected = false;
          });
          _.each(self.options.agencies, (d) => {
            d.selected = false;
          });

          cb();
          (cb || Function)();
        });
    },
  },
};
</script>
