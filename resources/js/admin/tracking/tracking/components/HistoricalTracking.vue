<template>
  <v-timeline dense clipped>
    <v-timeline-item
      fill-dot
      class="white--text mb-6"
      color="orange"
      large
      v-if="
        Tracking.estatus.key == 'activo' && $gate.auth().id == Tracking.owner
      "
    >
      <template v-slot:icon>
        <v-tooltip top>
          <template v-slot:activator="{ on, attrs }">
            <span v-bind="attrs" v-on="on">{{ initials.init }}</span>
          </template>
          <span>{{ initials.full_name }}</span>
        </v-tooltip>
      </template>
      <historical-form :Tracking="Tracking"></historical-form>
    </v-timeline-item>

    <v-slide-x-transition group>
      <v-timeline-item
        v-for="event in timeline"
        :key="event.id"
        class="mb-2"
        color="pink"
        small
      >
        <v-row class="overline" align="center" no-gutters>
          <v-col cols="12" md="7">
            <div class="d-flex flex-column">
              <div class="blue-grey lighten-5 elevation-2 pa-3">
                <!-- v-text="event.message" -->
                <p v-text="event.message"></p>
                <!-- <v-btn icon><v-icon color="blue">mdi-message</v-icon></v-btn> -->
              </div>
              <span class="overline" v-if="event.date_next_tracking">
                Fecha del seguimiento:
                {{
                  $appFormatters.formatDate(
                    event.date_next_tracking,
                    'LL hh:mm'
                  )
                }}
              </span>
            </div>
          </v-col>
          <v-col cols="12" md="2" class="text-center">
            <v-tooltip left>
              <template v-slot:activator="{ on, attrs }">
                <v-progress-circular
                  v-bind="attrs"
                  v-on="on"
                  :rotate="360"
                  :size="64"
                  :width="10"
                  :value="
                    percenAssertiveness(event.last_assertiveness).value * 100
                  "
                  :color="percenAssertiveness(event.last_assertiveness).color"
                  dark
                >
                  {{
                    percenAssertiveness(event.last_assertiveness).value
                      | percent
                  }}
                </v-progress-circular>
              </template>
              <span>
                {{ percenAssertiveness(event.last_assertiveness).text }}
              </span>
            </v-tooltip>
          </v-col>
          <v-col cols="12" md="3" class="text-right">
            <div v-if="event.invoice" class="caption">
              {{ `Factura/Pedido: ${event.invoice}` }}
            </div>
            <div>
              {{
                $appFormatters.formatDate(event.created_at, 'DD MMM YYYY hh:mm')
              }}
            </div>
            <div class="caption">
              {{ event.last_price | currency() }} {{ event.last_currency }}
            </div>
            <div class="caption">{{ event.user }}</div>
            <div class="caption blue--text">
              {{ event.type_contacted }}
            </div>
          </v-col>
        </v-row>
      </v-timeline-item>
    </v-slide-x-transition>
  </v-timeline>
</template>

<script>
import HistoricalForm from '@admin/tracking/tracking/forms/Historical.vue';
import { mapState } from 'vuex';
export default {
  components: { HistoricalForm },
  props: {
    Tracking: {
      required: true,
    },
    timeline: {
      required: true,
      type: Array,
    },
  },

  computed: {
    ...mapState(['Assertiveness']),
    initials() {
      var names = this.$gate.auth().name.split(' '),
        initials = names[0].substring(0, 1).toUpperCase();

      if (names.length > 1) {
        initials += names[names.length - 1].substring(0, 1).toUpperCase();
      }
      return {
        init: initials,
        full_name: this.$gate.auth().name,
      };
    },
    owner() {
      return this.$gate.auth().id;
    },
  },
  methods: {
    percenAssertiveness(percent) {
      return this.Assertiveness.find((item) => {
        return item.value == percent;
      });
    },
  },
};
</script>

<style></style>
