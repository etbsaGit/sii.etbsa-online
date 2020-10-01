<template>
  <div class="component-wrap">
    <!-- form -->
    <v-card>
      <v-card-title>
        <v-icon>mdi-account</v-icon> Levantar Seguimiento
      </v-card-title>
      <v-divider class="mb-2"></v-divider>
      <v-form v-model="valid" ref="permissionFormEdit" lazy-validation>
        <v-container grid-list-md>
          <v-layout row wrap>
            <v-flex xs12>
              <div class="body-2">Datos de Seguimiento</div>
            </v-flex>
            <v-flex xs12 md8>
              <v-autocomplete
                v-model="prospect"
                :items="options.prospects"
                item-text="full_name"
                item-value="id"
                label="PROSPECTO:"
                placeholder="Seleccionar a un prospecto"
              >
                <template v-slot:prepend-inner>
                  <v-tooltip top>
                    <template v-slot:activator="{ on, attrs }">
                      <v-icon
                        key="icon-add"
                        color="success"
                        @click="$router.push({ name: 'prospect.create' })"
                        v-bind="attrs"
                        v-on="on"
                        >mdi-account-plus
                      </v-icon>
                    </template>
                    <span>Registrar Nuevo</span>
                  </v-tooltip>
                </template>
              </v-autocomplete>
            </v-flex>
            <v-flex xs12 md4>
              <v-autocomplete
                v-model="agency"
                :items="options.agencies"
                item-text="title"
                item-value="id"
                label="AGENCIA ASIGNADA:"
                placeholder="Agencia a quien correponde."
              ></v-autocomplete>
            </v-flex>
            <v-flex xs12 md4>
              <v-autocomplete
                v-model="department"
                :items="options.departments"
                item-text="title"
                item-value="id"
                label="CORRESPONDE A:"
                placeholder="Departamento a quien correponde."
              ></v-autocomplete>
            </v-flex>
            <v-flex xs12 md8>
              <v-autocomplete
                v-model="seller"
                :disabled="availableSeller"
                :items="options.sellers"
                item-text="name"
                item-value="id"
                label="VENDEDOR SUGERIDO:"
                placeholder="Vendedores disponibles:"
              ></v-autocomplete>
            </v-flex>
            <v-flex xs12 md6>
              <v-text-field
                v-model="title"
                label="TITULO DEL SEGUIMIENTO"
                placeholder="Producto de interes."
                counter="80"
                filled
              ></v-text-field>
            </v-flex>
            <v-flex xs12 md2>
              <v-text-field
                v-model="price"
                label="Precio Consultado:"
                prefix="$"
                placeholder="0.00"
                filled
              ></v-text-field>
            </v-flex>
            <v-flex xs12 md4>
              <div><strong>Prospecto contacto en:</strong></div>
              <v-radio-group v-model="row" row hide-details class="mt-0">
                <!-- <template v-slot:label>
                </template> -->
                <v-radio label="Online" value="online"></v-radio>
                <v-radio label="Agencia" value="agencia"></v-radio>
                <v-radio label="Campo" value="campo"></v-radio>
              </v-radio-group>
            </v-flex>
            <v-flex xs12>
              <v-textarea
                v-model="description"
                label="MOTIVO:"
                placeholder="Motivo del Seguimiento, detalle producto, precios, etc"
                counter="500"
                filled
              ></v-textarea>
            </v-flex>
            <v-flex xs12>
              <v-btn
                block
                @click="save()"
                :loading="isLoading"
                :disabled="!valid || isLoading"
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
    propProspectId: {
      required: false,
      type: Number,
    },
  },
  data() {
    return {
      valid: false,
      isLoading: false,
      title: "",
      description: "",
      price: 0.0,
      row: "online",
      prospect: null,
      agency: null,
      department: null,
      seller: null,
      options: {
        prospects: [],
        agencies: [],
        departments: [],
        sellers: [],
      },
    };
  },
  mounted() {
    // this.loadPermission();
    this.loadResources(() => {
      if (this.propProspectId) {
        this.prospect = parseInt(this.propProspectId);
      }
    });
  },
  watch: {
    permissionKey(v) {
      this.permissionKey = v.replace(" ", ".").toLowerCase();
    },
    title(v) {
      this.permissionKey = v.replace(" ", ".").toLowerCase();
    },
    department(v) {
      this.seller = null;
      !!this.agency ? this.loadSellers(() => {}) : false;
    },
    agency(v) {
      this.seller = null;
      !!this.department ? this.loadSellers(() => {}) : false;
    },
  },
  computed: {
    availableSeller() {
      const self = this;
      if (self.agency && self.department) {
        return false;
      } else {
        return true;
      }
    },
  },
  methods: {
    save() {
      const self = this;

      let payload = {
        title: self.title,
        description_topic: self.description,
        price: self.price,
        prospect_id: self.prospect,
        attended_by: self.seller,
        agency_id: self.agency,
        department_id: self.department,
        first_contact: self.row,
      };

      self.isLoading = true;

      axios
        .post("/admin/tracking", payload)
        .then(function(response) {
          self.$store.commit("showSnackbar", {
            message: response.data.message,
            color: "success",
            duration: 3000,
          });

          // reset
          self.isLoading = false;
          self.$router.push({ name: "tracking.list" });
        })
        .catch(function(error) {
          self.isLoading = false;
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
    loadResources(cb) {
      const self = this;
      axios
        .get("/admin/tracking/sales_history/resources")
        .then(function(response) {
          let Data = response.data.data;
          self.options.agencies = Data.agencies;
          self.options.departments = Data.departments;
          self.options.prospects = Data.prospects;
          (cb || Function)();
        });
    },
    loadSellers(cb) {
      const self = this;

      let params = {
        seller_type_id: self.department,
        paginate: "no",
      };
      self.$store.commit("showLoader");
      axios.get("/admin/sellers", { params: params }).then(function(response) {
        self.options.sellers = response.data.data;
        self.$store.commit("hideLoader");
      });

      // return seller;
    },
  },
};
</script>
