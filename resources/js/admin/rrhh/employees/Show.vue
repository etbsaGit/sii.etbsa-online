<template>
  <v-card flat min-height="100vh">
    <v-container fluid>
      <v-row dense>
        <v-col cols="12" md="5">
          <v-card class="mx-auto pb-4" max-width="450">
            <v-sheet height="200px" color="purple">
              <v-app-bar flat color="rgba(0, 0, 0, 0)">
                <v-toolbar-title class="text-h6 white--text pl-0">
                  Perfil Empleado
                </v-toolbar-title>
                <v-spacer></v-spacer>
                <v-btn color="white" icon @click="dialogEdit = true">
                  <v-icon>mdi-pencil</v-icon>
                </v-btn>
              </v-app-bar>
              <v-card-title class="white--text mt-6">
                <v-row dense align="center">
                  <v-col cols="2">
                    <v-avatar size="60" @click="dialogUploadPhoto = true">
                      <img alt="user" :src="item.photo" />
                    </v-avatar>
                  </v-col>
                  <v-col class="my-2 ml-3">
                    <v-list-item dense>
                      <v-list-item-content>
                        <v-list-item-title class="overline white--text">
                          {{ item.name }}
                        </v-list-item-title>
                        <v-list-item-subtitle class="white--text">
                          Num. Empleado: #{{ item.number_employee }}
                        </v-list-item-subtitle>
                      </v-list-item-content>
                    </v-list-item>
                  </v-col>
                </v-row>
              </v-card-title>
            </v-sheet>
            <v-row class="text-left" tag="v-card-text" dense>
              <v-col class="text-right mr-4 mb-2" tag="strong" cols="5">
                Sucursal / Departamento:
              </v-col>
              <v-col cols="6">
                <v-list-item dense class="pa-0">
                  <v-list-item-content class="pa-0">
                    <v-list-item-title>
                      {{ item.agency }}
                    </v-list-item-title>
                    <v-list-item-subtitle>
                      {{ item.department }}
                    </v-list-item-subtitle>
                  </v-list-item-content>
                </v-list-item>
              </v-col>
              <v-col class="text-right mr-4 mb-2" tag="strong" cols="5">
                Puesto:
              </v-col>
              <v-col cols="6">
                {{ item.job }}
              </v-col>
              <v-col class="text-right mr-4 mb-2" tag="strong" cols="5">
                Domicilio:
              </v-col>
              <v-col cols="6">
                {{ item.address }}
              </v-col>
              <v-col class="text-right mr-4 mb-2" tag="strong" cols="5">
                Telefono:
              </v-col>
              <v-col cols="6">
                {{ item.phone }}
              </v-col>
              <v-col class="text-right mr-4 mb-2" tag="strong" cols="5">
                Jefe Directo:
              </v-col>
              <v-col cols="6">
                {{ item.boss }}
              </v-col>
              <v-col cols="12">
                <v-divider></v-divider>
              </v-col>
              <v-col class="text-right mr-4 mb-2" tag="strong" cols="5">
                Usuario SIIETBSA:
              </v-col>
              <v-col cols="6">
                <v-edit-dialog
                  large
                  persistent
                  save-text="asignar"
                  @save="assignedUser(item)"
                >
                  <v-btn color="purple" x-small> {{ item.user }}</v-btn>
                  <template v-slot:input>
                    <v-form
                      v-model="valid"
                      ref="formUserAssigned"
                      lazy-validation
                    >
                      <div class="mt-4 title">
                        Seleccione a un Usuario:
                      </div>
                      <v-autocomplete
                        v-model="item.user_id"
                        :items="options.users"
                        :rules="[(v) => !!v || 'Es Requerido']"
                        item-text="email"
                        item-value="id"
                        placeholder="Buscar por EMAIL"
                        autofocus
                      ></v-autocomplete>
                    </v-form>
                  </template>
                </v-edit-dialog>
              </v-col>
            </v-row>
          </v-card>
        </v-col>
        <!-- <v-col cols="12" md="7">
          <template>
            <v-card>
              <v-tabs color="deep-purple accent-4" left>
                <v-tab>Referencias y Contactos</v-tab>
                <v-tab>Info. de Campo</v-tab>

                <v-tab-item v-for="n in 1" :key="n">
                  <v-container fluid>
                    <v-row>
                      <v-col v-for="i in 6" :key="i" cols="12" md="4">
                        <v-card color="grey" width="200" height="200"></v-card>
                      </v-col>
                    </v-row>
                  </v-container>
                </v-tab-item>
                <v-tab-item>
                  <v-card flat>
                    <v-container fluid>
                      <v-expansion-panels multiple>
                        <v-expansion-panel v-for="(item, i) in 5" :key="i">
                          <v-expansion-panel-header>
                            Item
                          </v-expansion-panel-header>
                          <v-expansion-panel-content>
                            Lorem ipsum dolor sit amet, consectetur adipiscing
                            elit, sed do eiusmod tempor incididunt ut labore et
                            dolore magna aliqua. Ut enim ad minim veniam, quis
                            nostrud exercitation ullamco laboris nisi ut aliquip
                            ex ea commodo consequat.
                          </v-expansion-panel-content>
                        </v-expansion-panel>
                      </v-expansion-panels>
                    </v-container>
                  </v-card>
                </v-tab-item>
              </v-tabs>
            </v-card>
          </template>
        </v-col> -->
      </v-row>
    </v-container>

    <dialog-component
      :show="dialogEdit"
      @close="dialogEdit = false"
      closeable
      :fullscreen="$vuetify.breakpoint.mobile"
      :title="`Editar Informacion Empleado`"
      max-width="500"
    >
      <edit-employee
        v-if="dialogEdit"
        @cancel="dialogEdit = false"
        :edit-item-id="item.id"
      ></edit-employee>
    </dialog-component>
    <dialog-confirm
      :show="dialogUploadPhoto"
      @close="(dialogUploadPhoto = false), (photo_profile = null)"
      @agree="uploadPhoto()"
      max-width="525"
    >
      <template #title>
        Subir Foto de Perfil
      </template>
      <template #body>
        <v-container fluid>
          <v-form v-model="photoValid" ref="FormPhoto" lazy-validation>
            <v-file-input
              v-model="photo_profile"
              prepend-icon="mdi-camera"
              accept="image/*"
              :rules="[(v) => !!v || 'Es Requerido']"
              label="Foto del Empleado"
              outlined
              dense
            />
          </v-form>
        </v-container>
      </template>
    </dialog-confirm>
  </v-card>
</template>
<script>
import EditEmployee from "./Edit.vue";
import DialogComponent from "@admin/components/DialogComponent.vue";
import DialogConfirm from "@admin/components/DialogConfirm.vue";
export default {
  components: { EditEmployee, DialogComponent, DialogConfirm },
  props: {
    itemId: {
      type: Number | String,
      require: true,
    },
  },
  data() {
    return {
      dialogEdit: false,
      dialogUploadPhoto: false,
      item: {},
      photoValid: true,
      valid: true,
      photo_profile: null,
      options: {
        users: [],
      },
    };
  },
  mounted() {
    this.loadItem();
    this.loadOptions();
  },
  methods: {
    refresh() {
      this.loadItem();
    },
    async loadItem() {
      const _this = this;
      await axios.get(`/admin/employees/${_this.itemId}`).then((res) => {
        _this.item = { ...res.data.data };
      });
    },
    async uploadPhoto() {
      const _this = this;
      if (!_this.$refs.FormPhoto.validate()) return;
      let formData = new FormData();
      formData.append("photo", _this.photo_profile);
      formData.append("_method", "put");
      await axios
        .post(`/admin/employees/${_this.itemId}`, formData, {
          headers: {
            "Content-Type": "multipart/form-data",
          },
        })
        .then(function (response) {
          _this.$store.commit("showSnackbar", {
            message: response.data.message,
            color: "success",
            duration: 3000,
          });
          _this.photo_profile = null;
          _this.dialogUploadPhoto = false;
          _this.refresh();
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
    async assignedUser() {
      const _this = this;
      if (!this.$refs.formUserAssigned.validate()) return;

      await axios
        .post(`/admin/employees/${_this.itemId}/user/${_this.item.user_id}`)
        .then(function (response) {
          _this.$store.commit("showSnackbar", {
            message: response.data.message,
            color: "success",
            duration: 3000,
          });
          _this.refresh();
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

    async loadOptions(cb) {
      const _this = this;
      await axios
        .get(`/admin/employees/resources/options`)
        .then(function (response) {
          let Data = response.data.data;
          _this.options.users = Data.users;
          (cb || Function)();
        });
    },
  },
};
</script>
<style></style>
