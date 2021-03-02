<template>
  <v-menu
    offset-y
    origin="center center"
    :nudge-bottom="10"
    transition="scale-transition"
  >
    <template v-slot:activator="{ on }">
      <v-btn icon v-on="on">
        <v-badge
          :content="items.length"
          :value="items.length"
          color="green"
          overlap
        >
          <v-icon>
            mdi-bell
          </v-icon>
        </v-badge>
      </v-btn>
    </template>
    <v-list class="pa-0" two-line subheader>
      <v-list-item>
        <v-list-item-content>
          <v-subheader>Mensajes</v-subheader>
        </v-list-item-content>
        <v-list-item-action>
          <v-btn
            icon
            ripple
            @click="$router.push({ name: 'notification.list' })"
          >
            <v-icon color="grey lighten-1">
              mdi-eye-circle
            </v-icon>
          </v-btn>
        </v-list-item-action>
      </v-list-item>
      <v-divider />
      <v-list-item
        v-for="item in items"
        :key="item.id"
        @click="markRead(item.id, item.data.tracking_id)"
      >
        <v-list-item-avatar>
          <v-icon :class="[item.iconClass]" class="blue white--text">
            mdi-message
          </v-icon>
        </v-list-item-avatar>

        <v-list-item-content>
          <v-list-item-title>
            Folio: #{{ item.data.tracking_id }}, {{ item.data.sender.name }}
          </v-list-item-title>
          <v-list-item-subtitle>{{ item.data.body }}</v-list-item-subtitle>
          <v-list-item-subtitle
            >{{ $appFormatters.formatDate(item.created_at, 'LLL') }}
          </v-list-item-subtitle>
        </v-list-item-content>

        <v-list-item-action>
          <v-btn icon ripple>
            <v-icon color="grey lighten-1">
              mdi-information
            </v-icon>
          </v-btn>
        </v-list-item-action>
      </v-list-item>
    </v-list>
  </v-menu>
</template>

<script>
export default {
  name: 'TrackingNotification',
  data: () => ({
    messages: 4,
    items: [],
  }),
  mounted() {
    this.$eventBus.$on(['NOTIFICATION'], () => {
      this.loadNotification();
    });
    this.loadNotification();
  },
  methods: {
    loadNotification() {
      const self = this;
      axios.get('/admin/notification').then(function(response) {
        self.items = response.data.data.unReadNotifications;
      });
    },
    markRead(_notification_id, _tracking_id) {
      const self = this;
      axios
        .delete('/admin/notification/' + _notification_id)
        .then(function(response) {
          self.$store.commit('showSnackbar', {
            message: response.data.message,
            color: 'success',
            duration: 3000,
          });
          self.$router.push({
            name: 'tracking.prospect',
            params: { propTrackingId: _tracking_id },
          });
        });
    },
  },
};
</script>
