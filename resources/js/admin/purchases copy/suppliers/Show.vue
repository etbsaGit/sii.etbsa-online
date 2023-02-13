<template>
  <v-card flat>
    <div class="d-flex flex-column grey lighten-3">
      <v-card-title class="pl-6 pt-3">
        <div class="text-h5 pl-6 pt-3">
          {{ supplier.business_name }}
        </div>
      </v-card-title>
      <v-card-subtitle class="pl-6">
        <div class="text-h6 text-secondary pl-6">
          {{ supplier.alias }}
        </div>
      </v-card-subtitle>
    </div>
    <v-sheet
      id="scrolling-techniques-3"
      class="overflow-y-auto"
      :height="minHeight"
    >
      <v-list two-line>
        <v-list-item>
          <v-list-item-icon>
            <v-icon color="indigo">
              mdi-account-box-outline
            </v-icon>
          </v-list-item-icon>

          <v-list-item-content>
            <v-list-item-title v-text="supplier.rfc" />
            <v-list-item-subtitle>RFC</v-list-item-subtitle>
          </v-list-item-content>

          <v-list-item-icon>
            <v-icon>mdi-information-variant</v-icon>
          </v-list-item-icon>
        </v-list-item>
        <v-list-item>
          <v-list-item-icon>
            <v-icon color="indigo">
              mdi-phone
            </v-icon>
          </v-list-item-icon>

          <v-list-item-content>
            <v-list-item-title v-text="supplier.phone" />
            <v-list-item-subtitle>Telefono</v-list-item-subtitle>
          </v-list-item-content>

          <v-list-item-icon>
            <v-icon>mdi-message-text</v-icon>
          </v-list-item-icon>
        </v-list-item>

        <v-divider inset></v-divider>

        <v-list-item>
          <v-list-item-icon>
            <v-icon color="indigo">
              mdi-email
            </v-icon>
          </v-list-item-icon>

          <v-list-item-content>
            <v-list-item-title v-text="supplier.email" />
            <v-list-item-subtitle>Email</v-list-item-subtitle>
          </v-list-item-content>
        </v-list-item>

        <v-divider inset></v-divider>
        <v-list-item>
          <v-list-item-icon>
            <v-icon color="indigo">
              mdi-map-marker
            </v-icon>
          </v-list-item-icon>

          <v-list-item-content>
            <v-list-item-title
              v-text="supplier.address"
              class="text-uppercase"
            />
            <v-list-item-subtitle v-if="supplier.township">{{
              ` ${supplier.township.name}, ${supplier.township.estate.name}`
            }}</v-list-item-subtitle>
          </v-list-item-content>
        </v-list-item>
        <v-divider inset></v-divider>
        <v-list-item>
          <v-list-item-icon>
            <v-icon color="indigo">
              mdi-bank
            </v-icon>
          </v-list-item-icon>

          <v-list-item-content>
            <v-list-item-title v-text="supplier.billing_data.bank" />
            <v-list-item-subtitle
              v-text="supplier.billing_data.account"
            ></v-list-item-subtitle>
          </v-list-item-content>
        </v-list-item>
      </v-list>
    </v-sheet>
  </v-card>
</template>
<script>
export default {
  name: "ShowSupplier",
  props: {
    supplierId: {
      require: true,
      type: [Number, String],
    },
  },
  data() {
    return {
      valid: true,
      supplier: {
        id: null,
        alias: null,
        business_name: null,
        rfc: null,
        address: null,
        email: null,
        phone: null,
        contact: null,
        billing_data: {
          bank: "",
          account: "",
        },
      },
    };
  },
  mounted() {
    const _this = this;
    _this.loadSupplierEdit(() => {});
  },
  computed: {
    minHeight() {
      const height = this.$vuetify.breakpoint.mobile ? "90vh" : "65vh";
      return `calc(${height} - ${this.$vuetify.application.top}px)`;
    },
  },
  methods: {
    async updateSupplier() {
      if (!this.$refs.form.$refs.formSupplier.validate()) return;
      const _this = this;
      await axios
        .put(`/admin/suppliers/${_this.supplierId}`, _this.supplier)
        .then(function (response) {
          _this.$store.commit("showSnackbar", {
            message: response.data.message,
            color: "success",
            duration: 3000,
          });
          _this.$eventBus.$emit("SUPPLIER_REFRESH");
        })
        .catch(function (error) {
          _this.$store.commit("hideLoader");
          if (error.response) {
            _this.$store.commit("showSnackbar", {
              message: error.response.data.message,
              color: "error",
              duration: 3000,
            });
            // return false;
          } else if (error.request) {
            console.log(error.request);
          } else {
            console.log("Error", error.message);
          }
        });
    },
    async loadSupplierEdit(cb) {
      this.supplier = await axios
        .get(`/admin/suppliers/${this.supplierId}/edit`)
        .then((response) => {
          return { ...response.data.data };
        });
      (cb || Function)();
    },
  },
};
</script>
<style scoped>
div.v-expansion-panel-content__wrap {
  padding: unset;
}
</style>
