<template >
  <v-card>
    <v-card-title> Configurar Vendedor </v-card-title>
    <v-divider />
    <v-card-text>
      <v-card flat>
        <v-row dense>
          <v-col cols="12" md="4">
            <v-card-title>Informacion Vendedor</v-card-title>
            <v-card-subtitle>
              Actualiza su informacion como su Foto de Perfil, Clave de Venedor,
              Nombre y Correo electronico.
            </v-card-subtitle>
          </v-col>
          <v-col cols="12" md="8" class="elevation-2">
            <update-profile-information-form
              :form="Seller"
            ></update-profile-information-form>
          </v-col>
        </v-row>
      </v-card>
    </v-card-text>

    <v-card-text>
      <v-card flat>
        <v-row dense>
          <v-col cols="12" md="4">
            <v-card-title>Configuracion Sucursales Vendedor</v-card-title>
            <v-card-subtitle>
              Sucusales en la Cuales Pertenece el Vendedor.
            </v-card-subtitle>
          </v-col>
          <v-col cols="12" md="8" class="elevation-2">
            <update-seller-agency-form
              :prop-seller-agency="seller_agency"
              :prop-seller-id="propSellerId"
            ></update-seller-agency-form>
          </v-col>
        </v-row>
      </v-card>
    </v-card-text>
    <v-card-text>
      <v-card flat>
        <v-row dense>
          <v-col cols="12" md="4">
            <v-card-title>Configuracion Departamentos Vendedor</v-card-title>
            <v-card-subtitle>
              Departamentos en la Cuales Pertenece el Vendedor.
            </v-card-subtitle>
          </v-col>
          <v-col cols="12" md="8" class="elevation-2">
            <update-seller-type-form
              :prop-seller-type="seller_type"
              :prop-seller-id="propSellerId"
            ></update-seller-type-form>
            <!-- <v-card-text>
              <v-row dense>
                <v-col
                  cols="4"
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
            </v-card-text>
            <v-card-actions>
              <v-btn color="primary" dark block> Guardar </v-btn>
            </v-card-actions> -->
          </v-col>
        </v-row>
      </v-card>
    </v-card-text>
    <v-card-text>
      <v-card flat>
        <v-row dense>
          <v-col cols="12" md="4">
            <v-card-title
              >Configuracion Categorias Vendedor (Gerente)</v-card-title
            >
            <v-card-subtitle>
              Categorias en la Cuales Pertenece el Vendedor.
            </v-card-subtitle>
          </v-col>
          <v-col cols="12" md="8" class="elevation-2">
            <update-seller-category-form
              :prop-seller-category="seller_category"
              :prop-seller-id="propSellerId"
            ></update-seller-category-form>
            <!-- <v-card-text>
              <v-row dense>
                <v-col
                  cols="4"
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
            </v-card-text>
            <v-card-actions>
              <v-btn color="primary" dark block> Guardar </v-btn>
            </v-card-actions> -->
          </v-col>
        </v-row>
      </v-card>
    </v-card-text>
    <v-card-text>
      <v-card flat>
        <v-row dense>
          <v-col cols="12" md="4">
            <v-card-title
              >Configuracion Metas del Vendedor (Gerente)</v-card-title
            >
            <v-card-subtitle>
              Metas por Categoria y Periodo asignadas al Vendedor
            </v-card-subtitle>
          </v-col>
          <v-col cols="12" md="8" class="elevation-2">
           <update-metas-seller propSellerId="propSellerId"></update-metas-seller>
          </v-col>
        </v-row>
      </v-card>
    </v-card-text>
  </v-card>
</template>
<script>
import UpdateMetasSeller from "./UpdateMetasSeller.vue";
import UpdateProfileInformationForm from "./UpdateProfileInformationForm.vue";
import UpdateSellerAgencyForm from "./UpdateSellerAgencyForm.vue";
import UpdateSellerCategoryForm from "./UpdateSellerCategoryForm.vue";
import UpdateSellerTypeForm from "./UpdateSellerTypeForm.vue";

export default {
  components: {
    UpdateProfileInformationForm,
    UpdateSellerAgencyForm,
    UpdateSellerTypeForm,
    UpdateSellerCategoryForm,
    UpdateMetasSeller
},
  name: "SellerProfileConfig",
  props: {
    propSellerId: {
      required: true,
    },
  },
  data() {
    return {
      // panel: [0, 1, 2],
      Seller: {},
      PhotoInput: null,

      // snack: false,
      // snackColor: "",
      // snackText: "",
      // max25chars: (v) => v.length <= 25 || "Input too long!",
      // pagination: {},
      seller_type: [],
      seller_agency: [],
      seller_category: [],
      options: {
        // departments: [],
        // agencies: [],
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
    // this.loadResources(() => {});
  },
  computed: {
    PhotoPreview() {
      return this.PhotoInput ? URL.createObjectURL(this.PhotoInput) : null;
    },
  },
  methods: {
    loadSeller(cb) {
      const _this = this;

      axios
        .get("/admin/sellers/" + _this.propSellerId)
        .then(function (response) {
          let Seller = response.data.data;
          _this.Seller = Seller;
          _this.title = Seller.seller_key;

          _.each(_this.Seller.seller_type, (g) => {
            _this.seller_type[g.id] = true;
          });
          _.each(_this.Seller.seller_agency, (g) => {
            _this.seller_agency[g.id] = true;
          });
          _.each(_this.Seller.seller_category, (g) => {
            _this.seller_category[g.id] = true;
          });

          (cb || Function)();
        });
    },
    // loadResources(cb) {
    //   const _this = this;

    //   let params = {
    //     paginate: "no",
    //   };

    //   axios
    //     .get("/admin/tracking/sales_history/resources")
    //     .then(function (response) {
    //       let Data = response.data.data;
    //       // _this.options.departments = Data.departments;
    //       // _this.options.agencies = Data.agencies;
    //       // _this.options.categories = Data.categories;

    //       // _.each(_this.options.departments, (d) => {
    //       //   d.selected = false;
    //       // });
    //       // _.each(_this.options.agencies, (d) => {
    //       //   d.selected = false;
    //       // });
    //       // _.each(_this.options.categories, (d) => {
    //       //   d.selected = false;
    //       // });

    //       cb();
    //       (cb || Function)();
    //     });
    // },
  },
};
</script>
<style >
</style>