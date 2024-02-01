<template>
  <v-card flat>
    <v-form>
      <v-row>
        <v-col cols="12">
          <v-radio-group v-model="form.purchase_type_id" row>
            <v-radio
              v-for="item in options.purchaseTypes"
              :key="item.id"
              :label="item.name"
              :value="item.id"
            ></v-radio>
          </v-radio-group>
        </v-col>
        <v-col cols="12">
          <v-text-field
            v-model="form.name"
            label="Nombre Concepto de Compra"
            filled
          ></v-text-field>
        </v-col>
      </v-row>
    </v-form>
  </v-card>
</template>
<script>
export default {
  name: "PurchaseConceptForm",
  props: {
    form: {
      require: true,
      type: Object,
    },
  },
  data() {
    return {
      options: {
        purchaseTypes: [],
      },
    };
  },
  
  created() {
    this.loadOptions();
  },
  methods: {
    async loadOptions() {
      const _this = this;
      await axios
        .get("/admin/purchase-concept/create")
        .then(function (response) {
          let Data = response.data.data;
          _this.options.purchaseTypes = Data.purchase_types;
        });
    },
    // async getPurchaseConceptById(id) {
    //   const _this = this;

    //   await axios
    //     .get(`/admin/purchase-concept/${id}`)
    //     .then(function (response) {
    //       _this.form = response.data.data;
    //       _.each(_this.form.usocfdi, (g) => {
    //         _this.usocfdi[g.clave] = true;
    //       });
    //     });
    // },
  },
};
</script>
<style></style>
