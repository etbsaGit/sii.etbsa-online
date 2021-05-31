<template>
  <modal
    :show="show"
    :max-width="maxWidth"
    :closeable="closeable"
    :fullscreen="fullscreen"
    @close="close"
  >
    <div>
      <v-toolbar dense dark>
        <v-btn small icon @click="close()">
          <v-icon>mdi-arrow-left</v-icon>
        </v-btn>

        <v-toolbar-title v-text="title" class="overline" />

        <v-spacer></v-spacer>
        <slot name="actions"></slot>
      </v-toolbar>
      <v-sheet
        id="scrolling-techniques-3"
        class="overflow-y-auto"
        :max-height="maxHeight"
      >
        <slot></slot>
      </v-sheet>
    </div>
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
    title: {
      type: String,
      default: "Title dialog",
    },
  },
  computed: {
    maxHeight() {
      const height = this.$vuetify.breakpoint.mobile ? "100vh" : "100vh";
      return `calc(${height} - ${this.$vuetify.application.top}px)`;
    },
  },
  methods: {
    close() {
      this.$emit("close");
    },
  },
};
</script>
