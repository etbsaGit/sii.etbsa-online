<template>
  <v-card class="mx-auto" elevation="0">
    <v-toolbar flat class="overline">
      <span class="text-primary text-md-h4 text-sm-overline">
        Folio: #{{ folio }}</span
      >
      <v-spacer></v-spacer>
      <v-tooltip left>
        <template v-slot:activator="{ on, attrs }">
          <v-progress-circular
            v-bind="attrs"
            v-on="on"
            :rotate="360"
            :size="56"
            :width="5"
            :value="percenAssertiveness.value * 100"
            :color="percenAssertiveness.color"
          >
            {{ percenAssertiveness.value | percent }}
          </v-progress-circular>
        </template>
        <span>{{ percenAssertiveness.text }}</span>
      </v-tooltip>
      <v-chip
        small
        :color="colorEstatus(info.estatus.key)"
        class="mr-2 text-uppercase"
      >
        {{ info.estatus.title }}
      </v-chip>
      <v-btn
        v-if="$gate.allow('isGerente', 'tracking')"
        icon
        small
        @click="
          $router.push({
            name: 'tracking.edit',
            params: { propTrackingId: info.detail.id },
          })
        "
      >
        <v-icon>mdi-pencil</v-icon>
      </v-btn>
    </v-toolbar>
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
                  {{ info.detail.price | currency }} {{ info.detail.currency }}
                </v-list-item-subtitle>
                <v-list-item-title
                  class="pt-4 text-wrap text-button"
                  v-text="info.detail.description_topic"
                />
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
            color="blue-grey lighten-5"
          >
            <v-toolbar flat dense class="overline">
              Dirigido
            </v-toolbar>
            <v-divider></v-divider>
            <v-list-item three-line>
              <v-list-item-content>
                <v-list-item-title class="headline text-wrap mb-2">
                  {{ info.prospect.full_name }}
                </v-list-item-title>
                <v-list-item-subtitle class="body-1">
                  {{ info.prospect.company || "" }}
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
          <v-card class="mx-auto" outlined elevation="4" color="cyan lighten-5">
            <v-toolbar flat dense class="overline">
              Ejecutivo y Sucursal
              <v-spacer></v-spacer>
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
import Assertiveness from "@admin/sales/tracking/resources/assertiveness.json";
export default {
  props: {
    info: {
      required: true,
      type: Object,
    },
  },
  computed: {
    folio() {
      return this.info.detail.id
        ? this.info.detail.id.toString().padStart(5, 0)
        : "";
    },
    percenAssertiveness() {
      return Assertiveness.find((item) => {
        return item.value == this.info.detail.assertiveness;
      });
    },
    created_at_format() {
      return this.$appFormatters.formatDate(
        this.info.detail.created_at,
        "LL hh:mm a"
      );
    },
  },
  methods: {
    colorEstatus(value) {
      if (value == "finalizado") return "red";
      else if (value == "formalizado") return "blue";
      else return "primary";
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
