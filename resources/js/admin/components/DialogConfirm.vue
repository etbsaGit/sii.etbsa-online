<template>
  <modal
    :show="show"
    :max-width="maxWidth"
    :closeable="closeable"
    :fullscreen="fullscreen"
    @close="close"
  >
    <v-card>
      <v-card-title class="headline">
        <slot name="title"></slot>
      </v-card-title>
      <v-divider></v-divider>
      <v-sheet
        id="scrolling-techniques-3"
        class="overflow-y-auto"
        :max-height="minHeight"
      >
        <slot name="body"></slot>
      </v-sheet>
      <v-card-actions>
        <v-spacer></v-spacer>
        <v-btn color="red darken-1" text @click="close">
          {{ textCancel }}
        </v-btn>
        <v-btn color="primary darken-1" @click="agree">
          {{ textOK }}
        </v-btn>
      </v-card-actions>
    </v-card>
  </modal>
</template>

<script>
import Modal from "./Modal";

export default {
  emits: ["close", "agree"],
  components: {
    Modal,
  },
  props: {
    show: {
      default: false,
    },
    maxWidth: {
      default: 350,
    },
    closeable: {
      default: false,
    },
    fullscreen: {
      default: false,
    },
    textCancel: {
      default: "Cancelar",
    },
    textOK: {
      default: "Aceptar",
    },
  },
  computed: {
    minHeight() {
      const height = "100vh";
      return `calc(${height} - ${this.$vuetify.application.top}px)`;
    },
  },
  methods: {
    close() {
      this.$emit("close");
    },
    agree() {
      this.$emit("agree");
    },
  },
};
</script>
