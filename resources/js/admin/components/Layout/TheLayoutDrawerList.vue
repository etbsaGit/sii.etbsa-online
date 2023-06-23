<template>
  <v-list :dense="dense" rounded class="layout-drawer">
    <div
      v-for="(nav, i) in routes.filter((item) => !!item.visible)"
      :key="`nav-${i}`"
    >
      <v-divider v-if="nav.navType == 'divider'"></v-divider>
      <v-list-item
        v-else-if="isVisibleItem(nav)"
        :to="{ name: nav.routeName }"
        :exact="false"
        dense
      >
        <v-list-item-icon>
          <v-icon> {{ nav.icon }} </v-icon>
        </v-list-item-icon>
        <v-list-item-content>
          <v-list-item-title> {{ nav.label }} </v-list-item-title>
        </v-list-item-content>
      </v-list-item>
      <v-list-group
        v-else
        :prepend-icon="nav.icon"
        :key="`nav-${i}`"
        :value="false"
      >
        <template v-slot:activator>
          <v-list-item-content>
            <v-list-item-title> {{ nav.label }} </v-list-item-title>
          </v-list-item-content>
        </template>

        <the-layout-drawer-list
          :dense="dense"
          :routes="nav.children"
          icon-show
        />
      </v-list-group>
    </div>
  </v-list>
</template>

<script>
// import { resolve } from "path";

export default {
  name: "TheLayoutDrawerList",
  props: {
    dense: Boolean,
    iconShow: Boolean,
    isNest: Boolean,
    routes: {
      type: Array,
      default: () => {},
    },
    basePath: {
      type: String,
      default: "",
    },
  },
  data() {
    this.onlyOneChild = null;
    return {};
  },
  methods: {
    isExternal(path) {
      return /^(https?:|mailto:|tel:)/.test(path);
    },
    isVisibleItem(item) {
      return item.visible && !item.children;
    },
  },
};
</script>

<style>
.layout-drawer {
  padding-bottom: 0px;
  padding-top: 0px;
}
.layout-drawer__icon {
  margin-bottom: 8px;
  margin-top: 8px;
}
</style>
