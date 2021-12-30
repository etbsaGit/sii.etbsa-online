<template>
  <v-navigation-drawer
    v-model="Drawer"
    width="260"
    class="app-navigation-menu"
    :expand-on-hover="!$vuetify.breakpoint.mobile"
    floating
    clipped
    dark
    app
  >
    <the-layout-drawer-list :routes="Menu" icon-show dense />
  </v-navigation-drawer>
</template>

<script>
import Profile from "./Profile.vue";
import TheLayoutDrawerList from "./TheLayoutDrawerList.vue";
// import { mapMutations } from "vuex";
export default {
  name: "TheLayoutDrawer",
  components: {
    TheLayoutDrawerList,
    Profile,
  },
  computed: {
    Drawer: {
      get() {
        return this.$store.getters.getNavigationDrawer;
      },
      set(val) {
        this.$store.dispatch.toggleNavigation;
      },
    },
    Menu() {
      return this.$store.getters.getNavigation;
    },
  },
};
</script>
