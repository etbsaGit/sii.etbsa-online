<template>
  <v-card flat>
    <v-container fluid>
      <v-row dense>
        <v-col cols="12" md="5">
          <!-- <v-row dense>
            <v-col cols="12"> -->
          <v-card class="mx-auto pb-4" max-width="450">
            <v-card-title>
              Informacion del Cliente
              <v-spacer></v-spacer>
              <v-btn icon @click="dialogEdit = true">
                <v-icon color="green">mdi-pencil</v-icon>
              </v-btn>
            </v-card-title>
            <v-card-text>
              <h3 class="text-h5 mb-2">
                {{ item.name }}
              </h3>
              <div class="mb-4 text--primary">
                RFC: <span class="blue--text">{{ item.rfc }}</span> CURP:
                <span class="blue--text">{{ item.curp }}</span>
              </div>
            </v-card-text>
            <v-divider></v-divider>
            <v-row class="text-left" tag="v-card-text">
              <v-col class="text-right mr-4 mb-2" tag="strong" cols="5">
                Compañia / Razon Social:
              </v-col>
              <v-col cols="6">{{ item.company }}</v-col>
              <v-col class="text-right mr-4 mb-2" tag="strong" cols="5">
                Domicilio:
              </v-col>
              <v-col cols="6">
                {{ item.address }}
              </v-col>
              <v-col class="text-right mr-4 mb-2" tag="strong" cols="5">
                Estado / Municipio:
              </v-col>
              <v-col cols="6"> {{ item.estado }}, {{ item.municipio }} </v-col>
              <v-col class="text-right mr-4 mb-2" tag="strong" cols="5">
                Telefono:
              </v-col>
              <v-col cols="6">{{ item.phone }}</v-col>
            </v-row>
          </v-card>
          <!-- </v-col>
          </v-row> -->
        </v-col>
        <v-col cols="12" md="7">
          <template>
            <v-card>
              <v-tabs color="deep-purple accent-4" left>
                <v-tab>Expediente</v-tab>
                <v-tab>Referencias y Contactos</v-tab>
                <v-tab>Info. de Campo</v-tab>
                <v-tab-item>
                  <div class="col-12">
                    <media-manager
                      v-if="item.path && !!item.rfc"
                      :base-path="item.path"
                      :show-folders="true"
                      :allow-upload="true"
                      :allow-move="true"
                      :allow-delete="true"
                      :allow-create-folder="true"
                      :allow-rename="true"
                      :allow-crop="false"
                      :details="{}"
                    ></media-manager>
                  </div>
                </v-tab-item>
                <v-tab-item>
                  <customer-contacts :customer-id="itemId"></customer-contacts>
                </v-tab-item>
                <v-tab-item>
                  <metatable-customer-info
                    :customer-id="itemId"
                  ></metatable-customer-info>
                </v-tab-item>
              </v-tabs>
            </v-card>
          </template>
        </v-col>
      </v-row>
    </v-container>
    <dialog-component
      :show="dialogEdit"
      @close="dialogEdit = false"
      closeable
      :fullscreen="$vuetify.breakpoint.mobile"
      :title="`Editar Informacion Cliente`"
      max-width="500"
    >
      <edit-customer
        @cancel="dialogEdit = false"
        :edit-item-id="itemId"
      ></edit-customer>
    </dialog-component>
  </v-card>
</template>
<script>
import MediaManager from "@admin/components/MediaManager.vue";
import DialogComponent from "@admin/components/DialogComponent.vue";
import AppWidget from "@admin/components/AppWidget.vue";
import EditCustomer from "./Edit.vue";
import MetatableCustomerInfo from "./MetatableInfo.vue";
import CustomerContacts from "./MetatbleInfoContacts.vue";
export default {
  components: {
    AppWidget,
    MediaManager,
    DialogComponent,
    EditCustomer,
    MetatableCustomerInfo,
    CustomerContacts,
  },
  props: {
    itemId: {
      type: Number | String,
      require: true,
    },
  },
  mounted() {
    this.loadItem();
  },
  data() {
    return {
      dialogEdit: false,
      item: {},
    };
  },
  computed: {},
  methods: {
    async loadItem() {
      const _this = this;
      await axios.get(`/admin/customers/${_this.itemId}`).then((res) => {
        _this.item = { ...res.data.data };
      });
    },
  },
};
</script>
<style></style>
