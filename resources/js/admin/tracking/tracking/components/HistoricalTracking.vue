<template>
  <v-timeline dense clipped>
    <v-timeline-item
      fill-dot
      class="white--text mb-6"
      color="orange"
      large
      v-if="Tracking.estatus.key == 'activo'"
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
        <v-row class="overline" align="center">
          <v-col
            cols="12" md="7"
            v-text="event.message"
            class="blue-grey lighten-5 elevation-2"
          ></v-col>
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
              <span>{{
                percenAssertiveness(event.last_assertiveness).text
              }}</span>
            </v-tooltip>
          </v-col>
          <v-col cols="12" md="3" class="text-right">
            <div class="caption">{{ `Factura/Pedido: ${event.invoice || 'S/R'}` }}</div>
            <div>
              {{
                $appFormatters.formatDate(event.created_at, 'DD MMM YYYY hh:mm')
              }}
            </div>
            <div class="caption">
              {{ event.last_price | money() }} {{ event.last_currency }}
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
      var names = window.LSK_APP.AUTH_USER.name.split(' '),
        initials = names[0].substring(0, 1).toUpperCase();

      if (names.length > 1) {
        initials += names[names.length - 1].substring(0, 1).toUpperCase();
      }
      return {
        init: initials,
        full_name: window.LSK_APP.AUTH_USER.name,
      };
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
