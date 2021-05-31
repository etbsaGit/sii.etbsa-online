<template>
  <v-card flat color="grey lighten-4">
    <v-card-text>
      <v-card height="300" class="overflow-y-auto" flat>
        <v-slide-x-transition group>
          <user-message
            v-for="(message, index) in Messages"
            :key="index"
            :message-item="message"
          ></user-message>
        </v-slide-x-transition>
      </v-card>
    </v-card-text>
    <v-divider class="my-2"></v-divider>
    <v-form ref="formMessage" v-model="valid" lazy-validation>
      <v-card-actions>
        <v-app-bar color="rgba(0,0,0,0)" flat>
          <v-text-field
            v-model="message"
            :append-outer-icon="message ? 'mdi-send' : 'mdi-reload'"
            clear-icon="mdi-close-circle"
            label="Message"
            type="text"
            :rules="[(v) => !!v || 'requerido']"
            dense
            filled
            rounded
            outlined
            clearable
            @click:append-outer="sendMessage"
            @keyup.enter="sendMessage"
            @click:clear="clearMessage"
          ></v-text-field>
        </v-app-bar>
      </v-card-actions>
    </v-form>
  </v-card>
</template>
<script>
import UserMessage from "../../components/shared/UserMessage.vue";
export default {
  components: { UserMessage },
  props: {
    purchaseId: {
      require: true,
      type: Number,
    },
  },
  data: () => ({
    valid: true,
    items: [],
    message: null,
  }),
  mounted() {
    this.loadPurchaseMessages();
  },
  computed: {
    theme() {
      return this.$vuetify.theme.dark ? "dark" : "light";
    },
    Messages() {
      return this.items.map((item) => {
        return {
          body: { ...item.body },
          user: item.sender_id,
          user_name: item.sender.name,
          times: item.created_at,
        };
      });
    },
  },
  methods: {
    async loadPurchaseMessages() {
      const _this = this;
      await axios
        .get(`/admin/purchase-order/${_this.purchaseId}/message`)
        .then((response) => {
          console.log(response);
          _this.items = response.data.data;
        });
    },
    async sendMessage() {
      const _this = this;
      if (!_this.message) return _this.loadPurchaseMessages();
      if (!_this.$refs.formMessage.validate()) return;
      let payload = {
        body: {
          message: _this.message,
        },
      };
      await axios
        .post(`/admin/purchase-order/${_this.purchaseId}/message`, payload)
        .then((response) => {
          _this.loadPurchaseMessages();
          this.resetIcon();
          this.clearMessage();
        });
    },
    clearMessage() {
      this.$refs.formMessage.reset();
    },
    resetIcon() {
      this.iconIndex = 0;
    },
  },
};
</script>
<style scoped>
.border {
  border-right: 1px solid grey;
}
</style>
