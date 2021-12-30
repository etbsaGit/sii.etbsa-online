<template>
  <modal
    :show="show"
    :max-width="maxWidth"
    :closeable="closeable"
    :fullscreen="fullscreen"
    @close="close"
  >
    <v-card>
      <v-toolbar color="primary" class="text--primary" flat dense>
        <v-btn icon dark @click="close()">
          <v-icon>mdi-close</v-icon>
        </v-btn>
        <v-toolbar-title>
          <slot name="title"></slot>
        </v-toolbar-title>
        <v-spacer></v-spacer>
        <v-toolbar-items class="align-center">
          <slot name="actions"></slot>
        </v-toolbar-items>
      </v-toolbar>
      <v-card-text class="pt-4">
        <slot></slot>
      </v-card-text>
      <div style="flex: 1 1 auto;"></div>
      <!-- <v-card-title
        class="overline"
        :class="$vuetify.breakpoint.mobile ? 'pl-0' : ''"
      >
        <span class="d-inline-block text-truncate" style="max-width: 400px;">
          <slot name="title"></slot>
        </span>
        <v-spacer></v-spacer>
        <v-btn icon tile @click="close">
          <v-icon color="red">mdi-close</v-icon>
        </v-btn>
      </v-card-title>
      <v-card-text class="pt-4">
        <slot></slot>
      </v-card-text> -->
    </v-card>
  </modal>
</template>

<script>
import Modal from "./Modal";

export default {
  emits: ["close"],
  components: {
    Modal,
  },
  props: {
    show: {
      default: false,
    },
    maxWidth: {
      default: 500,
    },
    closeable: {
      default: false,
    },
    fullscreen: {
      default: false,
    },
  },

  methods: {
    close() {
      this.$emit("close");
      this.$eventBus.$emit("CLOSE_DIALOG");
    },
  },
};
</script>
