<template>
  <v-list two-line dense>
    <v-sheet
      id="scrolling-techniques-3"
      class="overflow-y-auto"
      :height="minHeight"
    >
      <v-list class="transparent" dense>
        <v-list-item class="overline" two-line>
          <v-list-item-content>
            <v-list-item-title v-text="'Razon Social:'"></v-list-item-title>

            <v-list-item-subtitle
              class="text-right"
              v-text="supplier.business_name"
            ></v-list-item-subtitle>
          </v-list-item-content>
        </v-list-item>
        <v-list-item class="overline" two-line>
          <v-list-item-content>
            <v-list-item-title v-text="'Razon Social:'"></v-list-item-title>

            <v-list-item-subtitle
              class="text-right"
              v-text="supplier.business_name"
            ></v-list-item-subtitle>
          </v-list-item-content>
        </v-list-item>
        <v-list-item class="overline" two-line>
          <v-list-item-content>
            <v-list-item-title v-text="'Razon Social:'"></v-list-item-title>

            <v-list-item-subtitle
              class="text-right"
              v-text="supplier.business_name"
            ></v-list-item-subtitle>
          </v-list-item-content>
        </v-list-item>
        <v-list-item class="overline" two-line>
          <v-list-item-content>
            <v-list-item-title v-text="'Razon Social:'"></v-list-item-title>

            <v-list-item-subtitle
              class="text-right"
              v-text="supplier.business_name"
            ></v-list-item-subtitle>
          </v-list-item-content>
        </v-list-item>
        <v-list-group class="grey lighten-1">
          <template v-slot:activator>
            <v-list-item-content>
              <v-list-item-title
                class="overline"
                v-text="'Datos Bancarios:'"
              ></v-list-item-title>
            </v-list-item-content>
          </template>
          <v-list-item class="overline" two-line>
            <v-list-item-content>
              <v-list-item-title v-text="'Cuenta:'"></v-list-item-title>
              <v-list-item-subtitle
                class="text-right"
                v-text="supplier.billing_data.account"
              ></v-list-item-subtitle>
            </v-list-item-content>
          </v-list-item>
          <v-list-item class="overline" two-line>
            <v-list-item-content>
              <v-list-item-title v-text="'Cuenta:'"></v-list-item-title>
              <v-list-item-subtitle
                class="text-right"
                v-text="supplier.billing_data.account"
              ></v-list-item-subtitle>
            </v-list-item-content>
          </v-list-item>
          <v-list-item class="overline" two-line>
            <v-list-item-content>
              <v-list-item-title v-text="'Cuenta:'"></v-list-item-title>
              <v-list-item-subtitle
                class="text-right"
                v-text="supplier.billing_data.account"
              ></v-list-item-subtitle>
            </v-list-item-content>
          </v-list-item>
        </v-list-group>
        <!--  -->
        <v-list-item class="overline" two-line>
          <v-list-item-content>
            <v-list-item-title v-text="'Razon Social:'"></v-list-item-title>

            <v-list-item-subtitle
              class="text-right"
              v-text="supplier.business_name"
            ></v-list-item-subtitle>
          </v-list-item-content>
        </v-list-item>
        <v-list-item class="overline" two-line>
          <v-list-item-content>
            <v-list-item-title v-text="'Razon Social:'"></v-list-item-title>

            <v-list-item-subtitle
              class="text-right"
              v-text="supplier.business_name"
            ></v-list-item-subtitle>
          </v-list-item-content>
        </v-list-item>
        <v-list-item class="overline" two-line>
          <v-list-item-content>
            <v-list-item-title v-text="'Razon Social:'"></v-list-item-title>

            <v-list-item-subtitle
              class="text-right"
              v-text="supplier.business_name"
            ></v-list-item-subtitle>
          </v-list-item-content>
        </v-list-item>
        <v-list-item class="overline" two-line>
          <v-list-item-content>
            <v-list-item-title v-text="'Razon Social:'"></v-list-item-title>

            <v-list-item-subtitle
              class="text-right"
              v-text="supplier.business_name"
            ></v-list-item-subtitle>
          </v-list-item-content>
        </v-list-item>
        <v-list-group class="grey lighten-1">
          <template v-slot:activator>
            <v-list-item-content>
              <v-list-item-title
                class="overline"
                v-text="'Datos Bancarios:'"
              ></v-list-item-title>
            </v-list-item-content>
          </template>
          <v-list-item class="overline" two-line>
            <v-list-item-content>
              <v-list-item-title v-text="'Cuenta:'"></v-list-item-title>
              <v-list-item-subtitle
                v-text="supplier.billing_data.account"
              ></v-list-item-subtitle>
            </v-list-item-content>
          </v-list-item>
          <v-list-item class="overline" two-line>
            <v-list-item-content>
              <v-list-item-title v-text="'Cuenta:'"></v-list-item-title>
              <v-list-item-subtitle
                v-text="supplier.billing_data.account"
              ></v-list-item-subtitle>
            </v-list-item-content>
          </v-list-item>
          <v-list-item class="overline" two-line>
            <v-list-item-content>
              <v-list-item-title v-text="'Cuenta:'"></v-list-item-title>
              <v-list-item-subtitle
                v-text="supplier.billing_data.account"
              ></v-list-item-subtitle>
            </v-list-item-content>
          </v-list-item>
        </v-list-group>

        <!-- <v-expansion-panels flat>
          <v-expansion-panel>
            <v-expansion-panel-header class="px-4 py-0 overline grey lighten-2">
              Datos Bancarios:
            </v-expansion-panel-header>
            <v-expansion-panel-content class="text-uppercase">
              <v-list-item class="overline">
                <v-list-item-title class="blue--text">
                  Cuenta:
                </v-list-item-title>
                <v-list-item-subtitle class="text-right">
                  {{ supplier.billing_data.account }}
                </v-list-item-subtitle>
              </v-list-item>
            </v-expansion-panel-content>
          </v-expansion-panel>
        </v-expansion-panels> -->
      </v-list>
    </v-sheet>
  </v-list>
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
        billing_data: [],
      },
    };
  },
  mounted() {
    const _this = this;
    _this.loadSupplierEdit(() => {});
  },
  computed: {
    minHeight() {
      const height = this.$vuetify.breakpoint.mobile ? "99vh" : "65vh";
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
