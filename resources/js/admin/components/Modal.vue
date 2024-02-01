<template>
  <Portal to="modal">
    <v-dialog
      v-model="show"
      :width="maxWidth"
      :fullscreen="fullscreen"
      :persistent="!closeable"
      transition="dialog-bottom-transition"
      scrollable
    >
      <slot></slot>
    </v-dialog>
  </Portal>
</template>

<script>
export default {
  emits: ["close"],
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
  mounted() {
    const _this = this;
    document.addEventListener("keydown", this.closeOnEscape);
  },
  unmounted() {
    const _this = this;
    document.removeEventListener("keydown", this.closeOnEscape);
  },
  methods: {
    close() {
      if (this.closeable) {
        emit("close");
        // this.$eventBus.$emit("close");
      }
    },
    closeOnEscape(e) {
      if (e.key === "Escape" && this.show) {
        this.close();
      }
    },
  },
};
</script>
