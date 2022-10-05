<template>
  <div>
    <h1>Editar Comparativo AMS</h1>
    <v-col cols="12">
      <v-card>
        <v-card-title>Datos del Cliente</v-card-title>
        <v-card-text>
          <form-ams-comparative :form.sync="form"></form-ams-comparative>
        </v-card-text>
        <v-card-actions>
          <v-btn color="accent" @click="preview">
            Preview
          </v-btn>
          <v-btn color="primary" @click="update">
            Actualizar
          </v-btn>
          <v-btn type="reset" outlined class="mx-2">
            Cancel
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-col>
    <v-dialog v-model="amsPreviewDialog" fullscreen>
      <v-card>
        <v-card-title class="d-print-none">
          <v-btn class="secondary" @click="print()">Print</v-btn>
          <v-spacer />
          <v-icon
            @click="
              (amsPreviewDialog = false),
                (tableWithAms = []),
                (tableWithoutAms = []),
                (tableSaveAms = []),
                (tableDiffAms = [])
            "
          >
            mdi-window-close
          </v-icon>
        </v-card-title>
        <ams-preview
          :tableDiffAms="tableDiffAms"
          :tableSaveAms="tableSaveAms"
          :tableWithAms="tableWithAms"
          :tableWithoutAms="tableWithoutAms"
          :hectares="form.hectares"
          :customer="customer"
        ></ams-preview>
      </v-card>
    </v-dialog>
  </div>
</template>
<script>
import AmsPreview from "./components/AmsPreview.vue";
import FormAmsComparative from "./components/FormAmsComparative.vue";
export default {
  components: { FormAmsComparative, AmsPreview },
  props: {
    editItemId: {
      require: true,
      type: Number | String,
    },
  },
  mounted() {
    const _this = this;
    _this.$store.commit("setBreadcrumbs", [
      { label: "AMS Comparativos", to: { name: "nt.comparative.list" } },
      { label: "Actualizar", name: "" },
    ]);
    _this.loadFormEdit();
  },
  data() {
    return {
      form: {
        customer_fullname: "",
        customer_phone: "",
        customer_email: "",
        customer_city: "",
        customer_country: "",
        customer_company: "",
        equipments: [],
        grooves: 0,
        tractor_value: 0,
        tractor_potencia: 0,
        diesel_prepare: 0,
        diesel_work: 0,
        diesel_price: 0,
        hectares: 0,
        cycles: 0,
        ams_value: 0,
      },
      amsPreviewDialog: false,
      tableWithoutAms: [],
      tableWithAms: [],
      tableDiffAms: [],
      tableSaveAms: [],
    };
  },
  computed: {
    customer() {
      return {
        fullname: this.form.customer_fullname,
        email: this.form.customer_email,
        phone: this.form.customer_phone,
        city: this.form.customer_city,
        country: this.form.customer_country,
        company: this.form.customer_company,
      };
    },
  },
  methods: {
    async loadFormEdit() {
      const _this = this;
      await axios
        .get(`/admin/ams-comparative/${_this.editItemId}/edit`)
        .then((response) => {
          _this.form = { ...response.data.data };
          _this.$store.commit("showSnackbar", {
            message: response.data.message,
            color: "success",
            duration: 3000,
          });
        });
    },
    async update() {
      const _this = this;
      if (!_this.$refs.formCreate.$refs.formCustomer.validate()) return;
      if (!_this.$refs.formCreate.$refs.form.validate()) return;
      if (_this.form.equipments.length < 1)
        return _this.$store.commit("showSnackbar", {
          message: "Agregar al Menos un Equipo",
          color: "error",
          duration: 3000,
        });
      let payload = {
        ..._this.form,
        created_by: _this.form.created_by.id,
      };

      await axios
        .put(`/admin/ams-comparative/${_this.editItemId}`, payload)
        .then(function (response) {
          _this.$store.commit("showSnackbar", {
            message: response.data.message,
            color: "success",
            duration: 3000,
          });
          // _this.$emit("submit");
        })
        .catch(function (error) {
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
    preview() {
      const _this = this;
      axios
        .post("/admin/ams-comparative/preview", _this.form)
        .then(function (response) {
          _this.$store.commit("showSnackbar", {
            message: response.data.message,
            color: "success",
            duration: 3000,
          });
          _this.tableWithoutAms = response.data.data.table_without_ams;
          _this.tableWithAms = response.data.data.table_with_ams;
          _this.tableDiffAms = response.data.data.table_diff;
          _this.tableSaveAms = response.data.data.table_save;
          _this.amsPreviewDialog = true;
        })
        .catch(function (error) {
          if (error.response) {
            _this.$store.commit("showSnackbar", {
              message: error.response.data.message,
              color: "error",
              duration: 3000,
            });
            _this.amsPreview = false;
          } else if (error.request) {
            console.log(error.request);
          } else {
            console.log("Error", error.message);
          }
        });
    },
    print() {
      window.print();
    },
  },
};
</script>
<style></style>
