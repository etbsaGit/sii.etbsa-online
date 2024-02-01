<template>
  <messages @SendMessage="sendMessage" :items="Messages"></messages>
</template>
<script>
import Messages from "@admin/components/shared/Messages.vue";
export default {
  components: { Messages },
  props: {
    trackingId: {
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
    this.loadTrackingMessages();
  },
  computed: {
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
    async loadTrackingMessages() {
      const _this = this;
      await axios
        .get(`/admin/tracking/${_this.trackingId}/message`)
        .then((response) => {
          console.log(response);
          _this.items = response.data.data;
        });
    },
    async sendMessage(payload) {
      const _this = this;
      await axios
        .post(`/admin/tracking/${_this.trackingId}/message`, payload)
        .then((response) => {
          _this.loadTrackingMessages();
          this.resetIcon();
          this.clearMessage();
        });
    },
  },
};
</script>
