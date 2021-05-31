<template>
  <modal
    :show="show"
    :max-width="maxWidth"
    :closeable="closeable"
    :fullscreen="fullscreen"
    @close="close"
  >
    <v-card flat>
      <v-card-title
        class="white--text py-0 black overline"
        :class="$vuetify.breakpoint.mobile ? 'pl-0' : ''"
      >
        <v-btn v-if="$vuetify.breakpoint.mobile" icon tile @click="close">
          <v-icon color="white">mdi-chevron-left</v-icon>
        </v-btn>
        <span class="d-inline-block text-truncate" style="max-width: 400px;">
          <slot name="title"></slot>
        </span>
        <v-spacer></v-spacer>
        <v-btn v-if="!$vuetify.breakpoint.mobile" icon tile @click="close">
          <v-icon color="red">mdi-close</v-icon>
        </v-btn>
      </v-card-title>
      <v-card-text class="pt-4">
        <slot></slot>
      </v-card-text>
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
      // this.$emit("close");
      this.$eventBus.$emit("CLOSE_DIALOG");
    },
  },
};
</script>
