<template>
  <v-menu
    transition="slide-x-transition"
    origin="center center"
    offset-x
    left
  >
    <template v-slot:activator="{ on }">
      <v-btn icon v-on="on" tile>
        <v-badge
          :content="items.length"
          :value="items.length"
          color="green"
          overlap
        >
          <v-icon>
            mdi-bell-ring
          </v-icon>
        </v-badge>
      </v-btn>
    </template>
    <!-- <v-card min-width="500" class="mx-auto" flat> -->
      <v-list class="pa-0" two-line subheader min-width="400" dense>
        <v-list-item>
          <v-list-item-content>
            <v-list-item-title>Mensajes</v-list-item-title>
          </v-list-item-content>
          <v-list-item-action>
            <div>
              <v-btn icon ripple @click="loadNotification">
                <v-icon color="grey lighten-1">
                  mdi-reload
                </v-icon>
              </v-btn>
              <v-btn
                icon
                ripple
                @click="$router.push({ name: 'notification.list' })"
              >
                <v-icon color="blue lighten-1">
                  mdi-eye-circle
                </v-icon>
              </v-btn>
            </div>
          </v-list-item-action>
        </v-list-item>
        <v-divider />
        <v-list-item
          v-for="item in items"
          :key="item.id"
          @click="markRead(item.id, item.data.messageable_id)"
          dense
        >
          <v-list-item-avatar>
            <v-icon class="blue lighten-1" dark>
              mdi-message
            </v-icon>
          </v-list-item-avatar>

          <v-list-item-content>
            <v-list-item-title>
              {{ item.data.title }}: #{{ item.data.messageable_id }},
              {{ item.data.sender_name }}
            </v-list-item-title>
            <v-list-item-subtitle>
              {{ item.data.body.message }}
            </v-list-item-subtitle>
            <v-list-item-subtitle>
              {{ $appFormatters.formatDate(item.created_at, "LLL") }}
            </v-list-item-subtitle>
          </v-list-item-content>

          <v-list-item-action>
            <v-btn icon>
              <v-icon color="grey lighten-1">mdi-information</v-icon>
            </v-btn>
          </v-list-item-action>
        </v-list-item>
      </v-list>
    <!-- </v-card> -->
  </v-menu>
</template>

<script>
export default {
  name: "Notification",
  data: () => ({
    messages: 4,
    items: [],
  }),
  mounted() {
    this.$eventBus.$on(["NOTIFICATION"], () => {
      this.loadNotification();
    });
    this.loadNotification();
  },
  methods: {
    async loadNotification() {
      const _this = this;
      await axios.get("/admin/notification").then(function (response) {
        _this.items = response.data.data.unReadNotifications;
      });
    },
    async markRead(_notification_id, _messageable_id) {
      const _this = this;
      await axios
        .delete("/admin/notification/" + _notification_id)
        .then(function (response) {
          _this.$store.commit("showSnackbar", {
            message: response.data.message,
            color: "success",
            duration: 3000,
          });
          _this.$eventBus.$emit("NOTIFICATION");
        });
    },
  },
};
</script>
