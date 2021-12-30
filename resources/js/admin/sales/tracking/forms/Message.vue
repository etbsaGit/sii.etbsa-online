<template>
  <v-form ref="form" v-model="valid" lazy-validation>
    <v-text-field
      v-model="form.message"
      append-outer-icon="mdi-send"
      class="px-4"
      outlined
      filled
      solo
      clear-icon="mdi-close-circle"
      clearable
      label="Mensaje"
      type="text"
      @keyup.enter="sendMessage"
      @click:append-outer="sendMessage"
      @click:clear="clearMessage"
    ></v-text-field>
  </v-form>
</template>

<script>
export default {
  props: ["sellerId", "trackingId"],
  data: () => ({
    valid: false,
    form: {
      message: null,
    },
  }),
  mounted() {},
  methods: {
    clearMessage() {
      this.$refs.form.reset();
    },
    async sendMessage() {
      if (!this.$refs.form.validate()) return;
      const self = this;

      let payload = {
        recipient_id: this.sellerId,
        tracking_id: this.trackingId,
        msg: this.form.message,
      };

      await axios
        .post("/admin/tracking/message", payload)
        .then(() => {
          self.$refs.form.reset();
          self.$eventBus.$emit("MESSAGE_SEND");
          self.$eventBus.$emit("NOTIFICATION");
          self.$store.commit("showSnackbar", {
            message: response.data.message,
            color: "success",
            duration: 3000,
          });
        })
        .catch(function (error) {
          self.$store.commit("hideLoader");

          if (error.response) {
            self.$store.commit("showSnackbar", {
              message: error.response.data.message,
              color: "error",
              duration: 3000,
            });
          } else if (error.request) {
            console.log(error.request);
          } else {
            console.log("Error", error.message);
          }
        });
    },
  },
};
</script>

<style></style>
