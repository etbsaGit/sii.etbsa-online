<template>
  <v-card>
    <v-card-title>
      <v-icon left>mdi-account</v-icon> Registrar Nuevo Usuario
    </v-card-title>
    <v-divider></v-divider>
    <v-card-text>
      <v-form v-model="valid" ref="form" lazy-validation>
        <v-row dense align="center">
          <v-col cols="12" md="6">
            <v-text-field
              label="Nombre de Usuario"
              v-model="form.name"
              :rules="[(v) => !!v || 'Nombre es Requerido']"
              outlined
              dense
            ></v-text-field>
          </v-col>
          <v-col cols="12" md="6">
            <v-text-field
              label="Email"
              v-model="form.email"
              :rules="emailRules"
              outlined
              dense
            ></v-text-field
          ></v-col>
          <v-col cols="12" md="6">
            <v-text-field
              v-model="form.password"
              label="Password"
              type="password"
              :rules="passwordRules"
              outlined
              dense
            ></v-text-field>
          </v-col>
          <v-col cols="12" md="6">
            <v-text-field
              label="Confirmar Password"
              type="password"
              v-model="form.passwordConfirm"
              :rules="[
                (v) => !!v || 'Confirmacion es requerida',
                (v) => v == form.password || 'Contraseña no son iguales',
              ]"
              outlined
              dense
            ></v-text-field>
          </v-col>
          <v-col cols="12" md="4">
            <v-switch
              v-model="form.active"
              label="Pre-Activar Cuenta"
              dense
            ></v-switch>
          </v-col>
          <v-col cols="12">
            <h1 class="title">
              <v-icon left>mdi-key</v-icon> Permisos Especiales
            </h1>
            <v-divider></v-divider>
            <v-row dense align="start">
              <v-col cols="12" md="12">
                <v-alert
                  color="info"
                  icon="mdi-information-outline"
                  class="white--text mt-2 caption"
                >
                  Los permisos especiales son permisos exclusivos para este
                  usuario. Permisos definidos aquí. Son más superiores que
                  cualquier permiso que tenga en su grupo. Así que si el usuario
                  pertenece a un grupo que tiene permiso para "hacer algo" pero
                  luego se le niega "hacer algo" aquí, al usuario se le negará
                  ese permiso. En definitiva, permisos especiales.Tiene alta
                  prioridad que los permisos de grupo.
                </v-alert>
              </v-col>
              <v-col cols="12" md="12">
                <v-row dense class="mt-2">
                  <v-col cols="6" md="4">
                    <v-autocomplete
                      label="Seleccionar Permiso"
                      v-bind:items="options.permissions"
                      v-model="selectedPermission"
                      item-text="title"
                      item-value="key"
                      outlined
                      dense
                      hide-detail
                    ></v-autocomplete>
                  </v-col>
                  <v-col cols="6" md="4">
                    <v-select
                      v-model="selectedPermissionValue"
                      label="Valor del Permiso"
                      v-bind:items="options.permissionValues"
                      item-text="label"
                      item-value="value"
                      outlined
                      dense
                      hide-details
                    ></v-select>
                  </v-col>
                  <v-col cols="12" md="4">
                    <v-btn
                      @click="addSpecialPermission()"
                      class="primary lighten-1"
                      block
                    >
                      Agregar Permiso
                      <v-icon right>mdi-plus</v-icon>
                    </v-btn>
                  </v-col>
                  <v-col cols="12">
                    <v-chip
                      v-for="(p, k) in form.permissions"
                      :key="k"
                      @click:close="removePermission(k)"
                      close
                      close-icon="mdi-close"
                      outlined
                      :color="getColorPermission(p.value)"
                      class="ma-2"
                    >
                      <v-icon v-if="p.value == -1" left>mdi-cancel</v-icon>
                      <v-icon v-if="p.value == 0" left>
                        mdi-swap-horizontal
                      </v-icon>
                      <v-icon v-if="p.value == 1" left>mdi-check-circle</v-icon>
                      {{ p.title }}
                    </v-chip>
                    <v-banner
                      v-if="form.permissions.length === 0"
                      single-line
                      elevation="4"
                      icon="mdi-key-remove"
                    >
                      Sin Permisos Especiales Asigandos
                    </v-banner>
                  </v-col>
                </v-row>
              </v-col>
            </v-row>
          </v-col>
          <v-col cols="12">
            <h1 class="title"><v-icon>mdi-account-multiple</v-icon> Grupos</h1>
            <v-divider></v-divider>
          </v-col>
          <v-col
            cols="6"
            md="3"
            v-for="(g, k) in options.groups"
            :key="k"
            class="overline"
          >
            <v-switch v-model="form.groups[g.id]" :label="g.name"></v-switch>
          </v-col>
        </v-row>
      </v-form>
    </v-card-text>
    <v-card-actions>
      <v-btn block @click="save()" :disabled="!valid" color="primary">
        Guardar
      </v-btn>
    </v-card-actions>
  </v-card>
</template>

<script>
export default {
  data() {
    const _this = this;
    return {
      valid: false,
      form: {
        name: "",
        email: "",
        password: "",
        passwordConfirm: "",
        permissions: [],
        groups: [],
        active: "",
      },
      emailRules: [
        (v) => !!v || "E-mail is required",
        (v) =>
          /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test(v) ||
          "E-mail must be valid",
      ],
      passwordRules: [
        (v) => !!v || "Contraseña es Requerida",
        (v) => (v && v.length >= 8) || "Debe tener al menos 8 Caracteres",
      ],
      passwordConfirmRules: [
        (v) => !(v !== _this.form.password) || "Password no es igual",
      ],
      colors: {
        "-1": "red",
        "0": "grey",
        "1": "green",
      },
      options: {
        permissions: [],
        permissionValues: [
          { label: "Perimitir", value: 1 },
          { label: "Denegar", value: -1 },
          { label: "Heredar", value: 0 },
        ],
        groups: [],
      },
      selectedPermission: {},
      selectedPermissionValue: 0,
      alert: {
        show: false,
        icon: "",
        color: "",
        message: "",
      },
    };
  },
  mounted() {
    const _this = this;
    _this.loadPermissions((cb) => {});
    _this.loadGroups((cb) => {});
    _this.$store.commit("setBreadcrumbs", [
      { label: "Usuarios", to: { name: "users.list" } },
      { label: "Registrar Nuevo", to: "" },
    ]);
  },
  methods: {
    getColorPermission(value) {
      return this.colors[value];
    },
    removePermission(i) {
      const _this = this;
      _this.form.permissions.splice(i, 1);
    },
    addSpecialPermission() {
      const _this = this;

      _.each(_this.options.permissions, (p) => {
        if (_this.selectedPermission === p.key) {
          if (!_this.existsInPermissions(_this.selectedPermission)) {
            p.value = _this.selectedPermissionValue;
            _this.form.permissions.push(p);
          }
        }
      });
    },
    existsInPermissions(permissionKey) {
      const _this = this;
      let found = false;
      _.each(_this.form.permissions, (p) => {
        if (p.key === permissionKey) found = true;
      });
      return found;
    },
    loadPermissions(cb) {
      const _this = this;

      let params = {
        paginate: "no",
      };

      axios
        .get("/admin/permissions", { params: params })
        .then(function (response) {
          _this.options.permissions = response.data.data;
          cb();
        });
    },
    loadGroups(cb) {
      const _this = this;
      let params = {
        paginate: "no",
      };
      axios.get("/admin/groups", { params: params }).then(function (response) {
        _this.options.groups = response.data.data;
        _.each(_this.options.groups, (g) => {
          g.selected = false;
        });
        cb();
      });
    },
    async save() {
      const _this = this;
      if (!_this.$refs.form.validate()) return;
      let payload = {
        ..._this.form,
        active: _this.form.active ? moment().format("YYYY-MM-DD") : null,
      };
      _this.$store.commit("showLoader");
      await axios
        .post("/admin/users", payload)
        .then(function (response) {
          _this.$store.commit("showSnackbar", {
            message: response.data.message,
            color: "success",
            duration: 3000,
          });

          _this.$eventBus.$emit("USER_ADDED");
          _this.$store.commit("hideLoader");

          // reset
          _this.$refs.userFormAdd.reset();
          _this.form.permissions = [];
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
