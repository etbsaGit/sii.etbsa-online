<template >
  <v-form>
    <v-card-text>
      <v-row dense>
        <v-col
          cols="4"
          md="3"
          v-for="(g, k) in options.departments"
          :key="k"
          class="caption"
        >
          <v-switch
            v-model="seller_type[g.id]"
            v-bind:label="g.title"
            hide-details
            dense
          ></v-switch>
        </v-col>
      </v-row>
    </v-card-text>
    <v-card-actions>
      <v-btn color="primary" dark block @click="save"> Guardar </v-btn>
    </v-card-actions>
  </v-form>
</template>
<script>
export default {
  name: "UpdateSellerTypeForm",
  props: {
    propSellerType: {
      type: Array,
      require: true,
    },
    propSellerId: {
      type: String | Number,
      require: true,
    },
  },
  data() {
    return {
      seller_type: this.propSellerType,
      options: {
        departments: [],
      },
    };
  },
  mounted() {
    this.loadResources(() => {});
  },
  methods: {
    loadResources(cb) {
      const _this = this;
      axios
        .get("/admin/tracking/sales_history/resources")
        .then(function (response) {
          let Data = response.data.data;
          _this.options.departments = Data.departments;

          _.each(_this.options.departments, (d) => {
            d.selected = false;
          });

          cb();
          (cb || Function)();
        });
    },
    save() {
      const _this = this;

      let payload = {
        seller_type: _this.seller_type,
      };

      _this.isLoading = true;

      axios
        .put(`/admin/sellers/${_this.propSellerId}/updateSellerType`, payload)
        .then(function (response) {
          _this.$store.commit("showSnackbar", {
            message: response.data.message,
            color: "success",
            duration: 3000,
          });

          _this.$store.commit("hideLoader");
        })
        .catch(function (error) {
          _this.$store.commit("hideLoader");

          if (error.response) {
            _this.$store.commit("showSnackbar", {
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
  },
};
</script>
<style >
</style>