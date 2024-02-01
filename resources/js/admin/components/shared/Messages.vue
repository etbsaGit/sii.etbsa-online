<template>
  <v-card flat color="grey lighten-4">
    <v-card-text>
      <v-card height="50vh" class="overflow-y-auto" flat>
        <v-slide-x-transition group>
          <user-message
            v-for="(message, index) in items"
            :key="`message-${index}`"
            :message-item="message"
          ></user-message>
        </v-slide-x-transition>
      </v-card>
    </v-card-text>
    <v-row dense class="mx-2">
      <v-col cols="12">
        <v-form ref="formMessage" v-model="valid" lazy-validation>
          <v-text-field
            v-model="message"
            :append-outer-icon="message ? 'mdi-send' : 'mdi-reload'"
            clear-icon="mdi-close-circle"
            placeholder="Escriba su Mensaje"
            type="text"
            :rules="[(v) => !!v || 'requerido']"
            dense
            filled
            rounded
            outlined
            clearable
            @click:append-outer="
              !!message ? sendMessage() : $emit('refreshMessages')
            "
            @keyup.enter="sendMessage"
            @click:clear="clearMessage"
          ></v-text-field>
        </v-form>
      </v-col>
    </v-row>
  </v-card>
</template>
<script>
import UserMessage from "./UserMessage.vue";
export default {
  components: { UserMessage },
  props: {
    items: {
      type: Array,
      default: () => {
        return [];
      },
    },
  },
  data: () => ({
    valid: true,
    message: null,
  }),

  methods: {
    sendMessage() {
      const _this = this;
      if (!_this.message) return;
      if (!_this.$refs.formMessage.validate()) return;
      let payload = {
        body: {
          message: _this.message,
        },
      };
      _this.$emit("SendMessage", payload);
      // _this.resetIcon();
      _this.clearMessage();
    },
    clearMessage() {
      this.$refs.formMessage.reset();
    },
    // resetIcon() {
    //   this.iconIndex = 0;
    // },
  },
};
</script>
<style scoped>
.border {
  border-right: 1px solid grey;
}
</style>
