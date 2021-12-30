<template>
  <v-card color="grey lighten-4" class="pa-2">
    <v-col cols="12" class="border">
      <v-sheet class="overflow-y-auto" max-height="400">
        <v-slide-x-transition group>
          <v-app-bar
            v-for="message in messages"
            color="rgba(0,0,0,0)"
            flat
            class="mb-16"
            :key="message.id"
          >
            <v-spacer v-if="message.sender_id == $gate.auth().id"></v-spacer>
            <v-tooltip top v-else>
              <template v-slot:activator="{ on, attrs }">
                <v-avatar color="indigo" size="40" elevation="10">
                  <span class="white--text overline" v-bind="attrs" v-on="on">
                    {{ initials(message.sender.name).init }}
                  </span>
                </v-avatar>
              </template>
              <span>{{ initials(message.sender.name).full_name }}</span>
            </v-tooltip>

            <v-card
              class="mt-10 mx-2"
              max-width="350px"
              :color="
                message.sender_id == $gate.auth().id ? 'blue' : 'grey darken-3'
              "
            >
              <v-list-item three-line>
                <v-list-item-content>
                  <div class="mb-4">
                    {{ message.msg }}
                  </div>
                  <v-list-item-subtitle>
                    hace
                    {{ $appFormatters.formatTimeFromNow(message.created_at) }}
                  </v-list-item-subtitle>
                </v-list-item-content>
              </v-list-item>
            </v-card>
            <v-tooltip top v-if="message.sender_id == $gate.auth().id">
              <template v-slot:activator="{ on, attrs }">
                <v-avatar color="indigo" size="40" elevation="10">
                  <span class="white--text overline" v-bind="attrs" v-on="on">
                    {{ initials(message.sender.name).init }}
                  </span>
                </v-avatar>
              </template>
              <span>{{ initials(message.sender.name).full_name }}</span>
            </v-tooltip>
          </v-app-bar>
        </v-slide-x-transition>
      </v-sheet>
    </v-col>
    <message :seller-id="sellerId" :tracking-id="trackingId"></message>
  </v-card>
</template>

<script>
import Message from "../forms/Message.vue";
export default {
  name: "MessageTracking",
  components: { Message },
  props: ["sellerId", "trackingId"],
  data: () => ({
    messages: [],
  }),
  mounted() {
    this.loadMessages();
    this.$eventBus.$on(["MESSAGE_SEND"], () => {
      this.loadMessages();
    });
  },
  methods: {
    initials(sender) {
      var names = sender.split(" "),
        initials = names[0].substring(0, 1).toUpperCase();

      if (names.length > 1) {
        initials += names[names.length - 1].substring(0, 1).toUpperCase();
      }
      return {
        init: initials,
        full_name: sender,
      };
    },
    loadMessages() {
      const self = this;
      axios
        .get("/admin/tracking/messages/" + this.trackingId)
        .then(function (response) {
          self.messages = response.data.data.messages;
        });
    },
  },
};
</script>

<style></style>
