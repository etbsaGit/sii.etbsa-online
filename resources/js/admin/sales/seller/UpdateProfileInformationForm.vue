<template >
  <v-form>
    <v-card-text>
      <v-row>
        <v-col cols="12">
          <v-avatar class="profile" color="grey" size="128">
            <v-img :src="PhotoPreview"></v-img>
          </v-avatar>
        </v-col>
        <v-col cols="12" md="6">
          <v-file-input
            v-model="PhotoInput"
            placeholder="Seleccionar imagen"
            persistent-placeholder
            label="Foto"
            outlined
            dense
          ></v-file-input>
        </v-col>
        <v-col cols="12" md="6">
          <v-btn v-if="!!form.path" color="error" dark @click="deletePhoto">
            Eliminar Imagen
          </v-btn>
        </v-col>
        <v-col cols="12">
          <v-text-field
            v-model="form.seller_key"
            label="Clave Vendedor"
            outlined
            dense
            hide-details
          ></v-text-field>
        </v-col>
        <v-col cols="12">
          <v-text-field
            v-model="form.name"
            label="Nombre"
            outlined
            dense
            hide-details
          ></v-text-field>
        </v-col>
        <v-col cols="12" md="6">
          <v-text-field
            v-model="form.email"
            label="Email"
            outlined
            dense
            hide-details
          ></v-text-field>
        </v-col>
        <v-col cols="12" md="6">
          <v-text-field
            v-model="form.phone"
            label="Telefono Vendedor"
            outlined
            dense
            hide-details
          ></v-text-field>
        </v-col>
      </v-row>
    </v-card-text>
    <v-card-actions>
      <v-btn block dark color="primary" @click="saveProfileInfomation">
        Guardar
      </v-btn>
    </v-card-actions>
  </v-form>
</template>
<script>
export default {
  name: "UpdateProfileForm",
  props: {
    form: {
      type: Object,
      require: true,
    },
  },
  data() {
    return {
      PhotoInput: null,
    };
  },
  computed: {
    PhotoPreview() {
      const _this = this;
      return _this.PhotoInput
        ? URL.createObjectURL(_this.PhotoInput)
        : _this.form.path ?? null;
    },
  },
  methods: {
    saveProfileInfomation() {
      const _this = this;

      let formData = new FormData();
      formData.append("_method", "put");
      if (_this.PhotoInput) {
        formData.append("photo", _this.PhotoInput);
      }
      formData.append("seller_key", _this.form.seller_key);
      formData.append("name", _this.form.name);
      formData.append("email", _this.form.email);
      formData.append("phone", _this.form.phone);

      axios
        .post(`/admin/sellers/${_this.form.id}`, formData)
        .then(function (response) {
          _this.$store.commit("showSnackbar", {
            message: response.data.message,
            color: "success",
            duration: 3000,
          });

          _this.$store.commit("hideLoader");
          _this.form = response.data.data.seller;
          // _this.$router.push({ name: "sellers.list" });
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
    deletePhoto() {
      const _this = this;

      axios
        .delete(`/admin/sellers/${_this.form.id}/deleteSellerPhoto`)
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