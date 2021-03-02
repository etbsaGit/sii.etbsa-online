<template>
  <v-container fluid>
    <v-row>
      <v-col cols="12" md="6">
        <v-card class="mx-auto">
          <v-toolbar color="light-blue" dark>
            <v-toolbar-title>No Leidas</v-toolbar-title>
          </v-toolbar>
          <v-list subheader two-line>
            <v-list-item v-for="item in unReadNotifications" :key="item.id">
              <v-list-item-avatar>
                <v-icon class="blue lighten-1" dark>
                  mdi-message
                </v-icon>
              </v-list-item-avatar>

              <v-list-item-content>
                <v-list-item-title>
                  Folio: #{{ item.data.tracking_id }},
                  {{ item.data.sender.name }}
                </v-list-item-title>
                <v-list-item-subtitle>
                  {{ item.data.body }}
                </v-list-item-subtitle>
                <v-list-item-subtitle>
                  {{ $appFormatters.formatDate(item.created_at, 'LLL') }}
                </v-list-item-subtitle>
              </v-list-item-content>

              <v-list-item-action>
                <v-btn
                  icon
                  @click="
                    $router.push({
                      name: 'tracking.prospect',
                      params: { propTrackingId: item.data.tracking_id },
                    })
                  "
                >
                  <v-icon color="grey lighten-1">mdi-information</v-icon>
                </v-btn>
              </v-list-item-action>
            </v-list-item>
          </v-list>
        </v-card>
      </v-col>
      <v-col cols="12" md="6">
        <v-card class="mx-auto">
          <v-toolbar color="light-blue" dark>
            <v-toolbar-title>Leidas</v-toolbar-title>
          </v-toolbar>
          <v-list subheader two-line>
            <v-list-item v-for="item in readNotifications" :key="item.id">
              <v-list-item-avatar>
                <v-icon>mdi-message</v-icon>
              </v-list-item-avatar>

              <v-list-item-content>
                <v-list-item-title>
                  Folio: #{{ item.data.tracking_id }},
                  {{ item.data.sender.name }}
                </v-list-item-title>
                <v-list-item-subtitle>
                  {{ item.data.body }}
                </v-list-item-subtitle>
                <v-list-item-subtitle>
                  {{ $appFormatters.formatDate(item.created_at, 'LLL') }}
                </v-list-item-subtitle>
              </v-list-item-content>

              <v-list-item-action>
                <v-btn
                  icon
                  @click="
                    $router.push({
                      name: 'tracking.prospect',
                      params: { propTrackingId: item.data.tracking_id },
                    })
                  "
                >
                  <v-icon color="grey lighten-1">mdi-information</v-icon>
                </v-btn>
              </v-list-item-action>
            </v-list-item>
          </v-list>
        </v-card>
      </v-col>
    </v-row>
  </v-container>
</template>

<script>
export default {
  props: {},
  data: () => ({
    unReadNotifications: [],
    readNotifications: [],
  }),

  mounted() {
    this.loadNotification();
  },
  computed: {},

  methods: {
    loadNotification() {
      const self = this;
      axios.get('/admin/notification').then(function(response) {
        self.unReadNotifications = response.data.data.unReadNotifications;
        self.readNotifications = response.data.data.readNotifications;
      });
    },
  },
};
</script>

<style lang="sass" scoped>
.v-list-item__content
  padding: 0
</style>
