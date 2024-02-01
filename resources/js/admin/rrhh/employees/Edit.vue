<template>
  <v-card flat>
    <v-toolbar flat>
      <v-tabs v-model="tabs" fixed-tabs icons-and-text>
        <v-tabs-slider></v-tabs-slider>
        <v-tab href="#mobile-tabs-5-1">
          Informacion Personal<v-icon>mdi-account-box</v-icon>
        </v-tab>

        <v-tab href="#mobile-tabs-5-2">
          Unidad Negocio <v-icon>mdi-store</v-icon>
        </v-tab>
      </v-tabs>
    </v-toolbar>

    <v-card-text style="height: 50vh" class="overflow-auto">
      <v-tabs-items v-model="tabs">
        <v-tab-item value="mobile-tabs-5-1">
          <v-card flat>
            <v-card-text>
              <form-employee
                ref="FormEmployee"
                :value="valid"
                @input="(v) => (valid = v)"
                :form.sync="form"
              ></form-employee>
            </v-card-text>
          </v-card>
        </v-tab-item>
        <v-tab-item value="mobile-tabs-5-2">
          <v-card flat>
            <v-card-text>
              <form-employee-tab-2
                ref="FormEmployeeTab2"
                :value="validTab2"
                @input="(v) => (validTab2 = v)"
                :form.sync="form"
              ></form-employee-tab-2>
            </v-card-text>
          </v-card>
        </v-tab-item>
      </v-tabs-items>
    </v-card-text>

    <v-card-actions class="justify-center">
      <v-row dense>
        <v-col cols="6">
          <v-btn color="red" block @click="$emit('cancel')" dark>
            Cancelar
          </v-btn>
        </v-col>
        <v-col cols="6">
          <v-btn
            color="primary"
            text
            block
            @click="submit"
            dark
            :disabled="!(valid && validTab2)"
          >
            Modificar
          </v-btn>
        </v-col>
      </v-row>
    </v-card-actions>
  </v-card>
</template>
<script>
import FormEmployee from "./FormEmployee.vue";
import FormEmployeeTab2 from "./FormEmployeeTab2.vue";
export default {
  name: "EditEmployee",
  components: { FormEmployee, FormEmployeeTab2 },
  props: {
    editItemId: {
      type: Number | String,
      require: true,
    },
  },
  data() {
    return {
      tabs: null,
      valid: true,
      validTab2: false,
      form: {
        active: false,
      },
    };
  },
  mounted() {
    this.loadFormEdit();
  },
  methods: {
    async submit() {
      const _this = this;
      if (!_this.$refs.FormEmployee.$refs.form.validate()) return;
      if (!this.$refs.FormEmployeeTab2.$refs.form.validate()) return;
      let payload = {
        ..._this.form,
        active: _this.form.active ? moment().format("YYYY-MM-DD") : null,
        user_email: _this.form.user_id
          ? _this.form.user_id.email
            ? _this.form.user_id.emai
            : _this.form.user_id
          : '',
      };
      console.log(payload);
      await axios
        .put(`/admin/employees/${_this.editItemId}`, payload)
        .then(function (response) {
          _this.$store.commit("showSnackbar", {
            message: response.data.message,
            color: "success",
            duration: 3000,
          });
          _this.$emit("cancel");
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
    async loadFormEdit() {
      const _this = this;
      await axios
        .get(`/admin/employees/${_this.editItemId}/edit`)
        .then((res) => {
          _this.form = { ...res.data.data };
          _this.form.user_id = res.data.data.user.email ?? null;
        });
    },
  },
};
</script>
<style></style>
