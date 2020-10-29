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
                :hint="`TEL: ${prospect.phone}`"
                persistent-hint
                item-value="id"
                label="BUSCAR PROSPECTO:"
                placeholder="Buscar por Nombre:"
                return-object
                clearable
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
              >
                <template v-slot:item="data">
                  <template v-if="typeof data.item !== 'object'">
                    <v-list-item-content
                      v-text="data.item.name"
                    ></v-list-item-content>
                  </template>
                  <template v-else>
                    <v-list-item-avatar>
                      <v-icon
                        v-if="data.item.groups.some((g) => g.name == 'Gerente')"
                        class="green--text"
                        >mdi-check-circle-outline</v-icon
                      >
                      <v-icon v-else class="grey--text"
                        >mdi-alert-circle-outline</v-icon
                      >
                    </v-list-item-avatar>
                    <v-list-item-content>
                      <v-list-item-title
                        v-html="data.item.name"
                      ></v-list-item-title>
                    </v-list-item-content>
                  </template>
                </template>
              </v-autocomplete>
            </v-flex>
            <v-flex xs12 md4>
              <v-select
                v-model="title"
                :items="categories"
                label="CATEGORIA INTERES:"
                placeholder="describir o seleccionar un opcion"
                hide-details
                filled
                chips
              ></v-select>
            </v-flex>
            <v-flex xs12 md5>
              <v-text-field
                v-model="reference"
                label="REFERENCIA:"
                placeholder="Alguna Referencia del Producto de interes"
                filled
              ></v-text-field>
            </v-flex>
            <v-flex xs12 md3>
              <v-text-field
                v-model="price"
                label="PRECIO A TRATAR:"
                prefix="$"
                placeholder="0.00"
                filled
              ></v-text-field>
            </v-flex>
            <v-flex xs12 md6>
              <div><strong>Prospecto contactado por medio de:</strong></div>
              <v-radio-group v-model="row" row hide-details class="mt-0">
                <!-- <template v-slot:label>
                </template> -->
                <v-radio label="ONLINE" value="online"></v-radio>
                <v-radio label="EN AGENCIA" value="agencia"></v-radio>
                <v-radio label="EN CAMPO" value="campo"></v-radio>
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
      reference: "",
      description: "",
      price: null,
      row: "online",
      prospect: {
        id: null,
        full_name: "",
        phone: "",
      },
      agency: null,
      department: null,
      seller: null,
      categories: [
        "Colección JD",
        "Colección JD Foraneo",
        "Construcción",
        "Construcción Foraneos",
        "Construcción Seminuevos Foraneos",
        "Implementos",
        "Implementos Foraneos",
        "Jardineria",
        "Jardineria Foraneo",
        "Maquinaria Diversa",
        "Maquinaria Diversa Foraneo",
        "Otros productos",
        "Por definir",
        "Refacciones",
        "Refacciones Foraneo",
        "Riego",
        "Riego Foraneo",
        "Seminuevos",
        "Servicio",
        "Tractores",
        "Tractores Foraneos",
        "Tractores Seminuevos",
        "Tractores Seminuevos Foraneos",
        "Trilladora",
        "Venta en Linea",
      ],
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
      if (this.$gate.deny("assignSeller", "tracking")) {
        this.agency = window.LSK_APP.AUTH_USER.agency_id;
        this.department = window.LSK_APP.AUTH_USER.departments_id;
      }
    });
  },
  watch: {
    agency(v) {
      this.seller = window.LSK_APP.AUTH_USER.id;
      !!this.department && v ? this.loadSellers(() => {}) : false;
    },
    department(v) {
      this.seller = window.LSK_APP.AUTH_USER.id;
      !!this.agency && v ? this.loadSellers(() => {}) : false;
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
        reference: self.reference,
        description_topic: self.description,
        price: self.price,
        prospect_id: self.prospect.id,
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
        seller_agency_id: self.agency,
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
