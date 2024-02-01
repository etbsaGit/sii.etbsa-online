<template>
  <v-container fluid>
    <v-row>
      <v-col cols="12">
        <app-widget
          padding-hide
          title="Contactos"
          icon-title="mdi-account-box-multiple"
        >
          <template #widget-header-action>
            <v-btn icon @click="dialogContactos = true">
              <v-icon color="green">
                mdi-plus-thick
              </v-icon>
            </v-btn>
          </template>
          <metatable-contactos
            slot="widget-content"
            :customer-id="customerId"
            :items="metatable.contactos || []"
            :dialogForm="dialogContactos"
            @close="dialogContactos = false"
            @edit="dialogContactos = true"
          />
        </app-widget>
      </v-col>
      <v-col cols="12"></v-col>
    </v-row>
  </v-container>
</template>
<script>
import AppWidget from "../../components/AppWidget.vue";
import MetatableContactos from "./MetatableContactos.vue";
export default {
  components: { MetatableContactos, AppWidget },
  naem: "CustomerContacs",
  props: {
    customerId: {
      type: Number | String,
      require: true,
    },
  },
  mounted() {
    this.loadItem();
  },
  data() {
    return {
      dialogContactos: false,
      metatable: {},
    };
  },
  methods: {
    async loadItem() {
      const _this = this;
      await axios.get(`/admin/customers/${_this.customerId}`).then((res) => {
        _this.metatable = { ...res.data.data.metatable };
      });
    },
  },
};
</script>
<style></style>
