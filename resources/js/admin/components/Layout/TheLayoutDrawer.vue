<template>
  <v-navigation-drawer
    :value="drawer"
    :expand-on-hover="!$vuetify.breakpoint.mobile"
    app
    dark
    fixed
  >
    <!-- app
    left
    dark
    dense
    fixed
    clipped -->
    <the-layout-drawer-list v-if="nav" dense :routes="nav" icon-show />
  </v-navigation-drawer>
</template>

<script>
import TheLayoutDrawerList from "./TheLayoutDrawerList.vue";

export default {
  name: "TheLayoutDrawer",
  components: {
    TheLayoutDrawerList,
  },
  props: {
    nav: {
      type: Array,
      require: true,
    },
    value: { type: Boolean },
  },
  data: () => ({}),
  computed: {
    drawer: {
      get: function () {
        return this.value;
      },
      set: function (v) {
        this.value = v;
      },
    },
  },
  methods: {
    // stateNavbarShow(state) {
    //   //   this.$store.dispatch('NavbarState', { state });
    // },
  },
};
</script>
