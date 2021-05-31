<template>
  <Portal to="modal">
    <v-dialog
      transition="dialog-bottom-transition"
      v-model="show"
      :width="maxWidth"
      :fullscreen="fullscreen"
      :persistent="!closeable"
      scrollable
    >
      <!-- <div v-show="show"> -->
      <slot v-if="show"></slot>
      <!-- </div> -->
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
