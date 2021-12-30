<template>
  <v-navigation-drawer
    :value="value"
    absolute
    right
    fixed
    temporary
    disable-resize-watcher
    :width="$vuetify.breakpoint.mdAndDown ? '256' : '512'"
  >
    <v-app-bar dense color="accent">
      <v-toolbar-title>
        Filtro
      </v-toolbar-title>
      <v-tooltip left>
        <template v-slot:activator="{ on }">
          <v-btn absolute fab bottom right small color="primary" v-on="on">
            <v-icon>mdi-magnify</v-icon>
          </v-btn>
        </template>
        <span>Buscar</span>
      </v-tooltip>
    </v-app-bar>

    <v-subheader>
      <base-tooltip :text="'Limpiar Filtro'">
        <template #activator="{ on }">
          <v-btn icon color="error" v-on="on" @click="$emit('resetFilter')">
            <v-icon>mdi-filter-remove</v-icon>
          </v-btn>
        </template>
      </base-tooltip>
    </v-subheader>

    <v-divider></v-divider>
    <v-container fluid>
      <slot></slot>
    </v-container>
  </v-navigation-drawer>
</template>

<script>
import BaseTooltip from "../Base/BaseTooltip.vue";
export default {
  components: { BaseTooltip },
  props: {
    value: Boolean,
  },
  computed: {},
};
</script>

<style></style>
