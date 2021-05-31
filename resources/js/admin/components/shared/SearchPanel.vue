<template>
  <v-navigation-drawer
    v-model="rightDrawerVal"
    right
    fixed
    light
    :disable-resize-watcher="true"
    :width="$vuetify.breakpoint.mdAndDown ? '400' : '384'"
  >
    <v-list class="py-0" dense>
      <v-toolbar flat class="my-2 grey lighten-3">
        <v-toolbar-title class="title">Busqueda Avanzada</v-toolbar-title>
        <v-divider class="mx-4" inset vertical></v-divider>
        <v-spacer></v-spacer>
        <!-- <v-btn
          elevation="4"
          class="purple mr-2"
          fab
          small
          dark
          @click.native="$emit('searchData')"
        >
          <v-icon dark>mdi-magnify</v-icon>
        </v-btn> -->
        <v-btn
          elevation="4"
          class="red mr-2"
          fab
          small
          dark
          @click.native="$emit('resetFilter')"
        >
          <v-icon dark>mdi-filter-remove</v-icon>
        </v-btn>
        <v-btn
          elevation="4"
          class="grey"
          fab
          small
          dark
          @click.native="$emit('cancelSearch')"
        >
          <v-icon dark>mdi-close-circle-outline</v-icon>
        </v-btn>
      </v-toolbar>
      <slot></slot>
    </v-list>
  </v-navigation-drawer>
</template>

vue.
<script>
export default {
  props: {
    rightDrawer: {
      type: Boolean,
      require: true,
    },
  },
  computed: {
    rightDrawerVal: {
      get() {
        return this.rightDrawer;
      },
      set(v) {
        this.rightDrawer = v;
      },
    },
  },
};
</script>
