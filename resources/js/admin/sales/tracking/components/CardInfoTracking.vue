<template>
  <v-card class="mx-auto" elevation="0" flat>
    <v-list>
      <v-list-item>
        <v-list-item-icon v-show="this.$vuetify.breakpoint.mdAndUp">
          <v-icon color="indigo"> mdi-file-document </v-icon>
        </v-list-item-icon>
        <v-list-item-content class="px-2">
          <v-card
            class="mx-auto"
            outlined
            elevation="4"
            color="light-blue lighten-5"
            rounded="xl"
          >
            <v-toolbar flat dense class="overline">
              {{ info.detail.title }}
              <v-spacer></v-spacer>
              {{ info.detail.first_contact }} |
              {{ info.detail.tracking_condition }}
            </v-toolbar>
            <v-divider></v-divider>
            <v-list-item three-line>
              <v-list-item-content>
                <v-list-item-title class="headline text-wrap mb-1">
                  {{ info.detail.reference }}
                </v-list-item-title>
                <v-list-item-subtitle class="subtitle-1 text-uppercase">
                  {{ created_at_format }}
                </v-list-item-subtitle>
                <v-list-item-subtitle class="subtitle-1 text-uppercase">
                  {{ info.detail.price | currency }} {{ info.currency }} <br />
                  T.C. {{ info.detail.exchange_value | currency }}
                </v-list-item-subtitle>
                <v-list-item-title class="pt-4 text-wrap text-button" />
                {{ info.detail.description_topic }}
              </v-list-item-content>
            </v-list-item>
          </v-card>
        </v-list-item-content>
      </v-list-item>

      <v-list-item>
        <v-list-item-icon v-show="this.$vuetify.breakpoint.mdAndUp">
          <v-icon color="indigo"> mdi-account </v-icon>
        </v-list-item-icon>
        <v-list-item-content class="px-2">
          <v-card
            class="mx-auto"
            outlined
            elevation="4"
            color="light-blue lighten-5"
          >
            <v-toolbar flat dense class="overline">
              Prospecto
              <v-spacer />

              <v-btn color="blue ligthen-3" dark @click="dialogEdit = true">
                Actualizar Info.
              </v-btn>

              <dialog-component
                :show="dialogEdit"
                @close="dialogEdit = false"
                closeable
                :fullscreen="$vuetify.breakpoint.mobile"
                title="Editar Prospecto"
                :maxWidth="600"
              >
                <prospect-edit
                  :propProspectId="info.prospect.id"
                  :key="info.prospect.full_name"
                ></prospect-edit>
              </dialog-component>
            </v-toolbar>
            <v-divider></v-divider>
            <v-list-item three-line>
              <v-list-item-content>
                <v-list-item-title class="headline text-wrap mb-2">
                  {{ info.prospect.full_name }}
                </v-list-item-title>
                <v-list-item-subtitle class="body-1">
                  {{
                    info.prospect?.customer?.full_name ||
                    "Prospecto Sin Cliente Asociado"
                  }}
                </v-list-item-subtitle>
                <v-list-item-subtitle class="body-1">
                  <span class="text--primary">RFC:</span>
                  {{ info.prospect.rfc || "" }}
                </v-list-item-subtitle>
                <v-list-item-subtitle class="body-1">
                  <span class="text--primary">Telefono:</span>
                  {{ info.prospect.phone || "" }}
                </v-list-item-subtitle>
                <v-list-item-subtitle class="body-1">
                  <span class="text--primary">Email:</span>
                  {{ info.prospect.email || "" }}
                </v-list-item-subtitle>
                <v-list-item-subtitle
                  v-if="info.prospect.township"
                  class="body-1"
                >
                  <span class="text--primary">Ciudad:</span>
                  {{ info.prospect.township.name }},
                  {{ info.prospect.township.estate.name }}
                </v-list-item-subtitle>
                <v-list-item-subtitle class="body-1">
                  <span class="text--primary">Rancho o Provincia:</span>
                  {{ info.prospect.town || "" }}
                </v-list-item-subtitle>
              </v-list-item-content>
            </v-list-item>
          </v-card>
        </v-list-item-content>
      </v-list-item>

      <v-list-item>
        <v-list-item-icon v-show="this.$vuetify.breakpoint.mdAndUp">
          <v-icon color="indigo"> mdi-account-box-outline </v-icon>
        </v-list-item-icon>

        <v-list-item-content class="px-2">
          <v-card
            class="mx-auto"
            outlined
            elevation="4"
            color="light-blue lighten-5"
          >
            <v-toolbar flat dense class="overline">
              Promotor
              <v-spacer></v-spacer>
              <v-btn
                v-if="!!info.attended_phone && info.owner !== user_id"
                color="primary"
                dark
                @click="sendWhatsAppAasigned"
              >
                <v-icon left>mdi-whatsapp</v-icon>
                Enviar Mensajes
              </v-btn>
            </v-toolbar>
            <v-divider></v-divider>
            <v-list-item three-line>
              <v-list-item-content>
                <v-list-item-subtitle>
                  Ejecutivo asignado:
                </v-list-item-subtitle>
                <v-list-item-title class="headline mb-1">
                  {{ info.attended_by }}
                </v-list-item-title>
                <v-list-item-subtitle>
                  {{ info.attended_email }}
                </v-list-item-subtitle>
                <v-list-item-subtitle>
                  {{ info.attended_phone }}
                </v-list-item-subtitle>
                <v-list-item-subtitle>
                  {{ info.agency }} - {{ info.department }}
                </v-list-item-subtitle>
              </v-list-item-content>
            </v-list-item>
          </v-card>
        </v-list-item-content>
      </v-list-item>
    </v-list>
  </v-card>
</template>

<script>
import DialogComponent from "@admin/components/DialogComponent.vue";
import Assertiveness from "@admin/sales/tracking/resources/assertiveness.json";
import ProspectEdit from "@admin/sales/prospect/ProspectEdit.vue";
import { mapGetters } from "vuex";
export default {
  components: { DialogComponent, ProspectEdit },
  props: {
    info: {
      required: true,
      type: Object,
    },
  },
  mounted() {
    // this.loadCustomers();
  },
  data() {
    return {
      dialogEdit: false,
      customer_dialog: false,
      customers: [],
      customer_id: null,
    };
  },
  computed: {
    folio() {
      return this.info.detail.id
        ? this.info.detail.id.toString().padStart(5, 0)
        : "";
    },
    percenAssertiveness() {
      const _this = this;
      return Assertiveness.find((item) => {
        return item.value == _this.info?.detail?.assertiveness;
      });
    },
    created_at_format() {
      return this.$appFormatters.formatDate(
        this.info.detail.created_at,
        "LL hh:mm a"
      );
    },

    ...mapGetters("user", ["user_id"]),
  },
  methods: {
    colorEstatus(value) {
      if (value == "finalizado") return "red";
      else if (value == "formalizado") return "blue";
      else return "primary";
    },
    sendWhatsAppAasigned() {
      const _this = this;
      
      let currentUrl = window.location.href;
      let message = `CRM SIIETBSA - Cual es el estatus actual del Seguimiento con Folio:${_this.info.detail.id}?
      \nCliente: ${_this.info.prospect.full_name}, 
      \nVer Seguimiento: ${currentUrl}`;

      let link = `https://wa.me/1${_this.info.attended_phone}?text=${encodeURIComponent(message)}`;

      window.open(link, "_blank");
    },
  },
};
</script>

<style scoped>
.v-progress-circular {
  margin: 1rem;
}
.v-toolbar__content {
  padding: 0;
}
</style>
