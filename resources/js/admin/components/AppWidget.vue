<template>
  <v-card :dark="dark">
    <v-app-bar v-if="!headerHide" flat dense color="transparent">
      <v-toolbar-title class="text-h5">
        <v-icon v-if="iconTitle">{{ iconTitle }}</v-icon>
        {{ title }}
      </v-toolbar-title>
      <v-spacer />
      <slot name="widget-header-action">
        <v-btn icon>
          <v-icon>{{ icon }}</v-icon>
        </v-btn>
      </slot>
    </v-app-bar>
    <v-divider v-if="!headerHide" />

    <v-card-text v-if="!paddingHide">
      <slot name="widget-content" />
    </v-card-text>
    <slot v-else name="widget-content" />
  </v-card>
</template>

<script>
export default {
  name: "AppWidget",
  props: {
    title: {
      type: String,
      default: "",
    },
    icon: {
      type: String,
      default: "mdi-dots-vertical",
    },
    iconTitle: {
      type: String,
    },
    headerHide: Boolean,
    paddingHide: Boolean,
    dark: Boolean,
  },
  data: () => ({}),
};
</script>
