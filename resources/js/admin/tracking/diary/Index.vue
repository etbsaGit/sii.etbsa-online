<template>
  <v-row class="fill-height">
    <v-col>
      <v-sheet height="64">
        <v-toolbar flat>
          <v-btn outlined class="mr-4" color="grey darken-2" @click="setToday">
            HOY
          </v-btn>
          <v-btn fab text small color="grey darken-2" @click="prev">
            <v-icon small>
              mdi-chevron-left
            </v-icon>
          </v-btn>
          <v-btn fab text small color="grey darken-2" @click="next">
            <v-icon small>
              mdi-chevron-right
            </v-icon>
          </v-btn>
          <v-toolbar-title v-if="$refs.calendar">
            {{ $refs.calendar.title }}
          </v-toolbar-title>
          <v-spacer></v-spacer>
          <v-menu bottom right>
            <template v-slot:activator="{ on, attrs }">
              <v-btn outlined color="grey darken-2" v-bind="attrs" v-on="on">
                <span>{{ typeToLabel[type] }}</span>
                <v-icon right>
                  mdi-menu-down
                </v-icon>
              </v-btn>
            </template>
            <v-list>
              <v-list-item @click="type = 'day'">
                <v-list-item-title>Dia</v-list-item-title>
              </v-list-item>
              <v-list-item @click="type = 'week'">
                <v-list-item-title>Semana</v-list-item-title>
              </v-list-item>
              <v-list-item @click="type = 'month'">
                <v-list-item-title>Mes</v-list-item-title>
              </v-list-item>
              <v-list-item @click="type = '4day'">
                <v-list-item-title>4 Dias</v-list-item-title>
              </v-list-item>
            </v-list>
          </v-menu>
        </v-toolbar>
      </v-sheet>
      <v-sheet height="500">
        <v-calendar
          ref="calendar"
          v-model="focus"
          color="primary"
          :events="events"
          :event-color="getEventColor"
          :type="type"
          @click:event="showEvent"
          @click:more="viewDay"
          @click:date="viewDay"
        ></v-calendar>
        <v-menu
          v-model="selectedOpen"
          :close-on-content-click="false"
          :activator="selectedElement"
          offset-x
        >
          <v-card color="grey lighten-4" min-width="424px" flat>
            <v-toolbar :color="selectedEvent.color" dark>
              <v-btn
                icon
                @click="
                  $router.push({
                    name: 'tracking.prospect',
                    params: { propTrackingId: selectedEvent.id },
                  })
                "
              >
                <v-icon>mdi-eye-outline</v-icon>
              </v-btn>
              <v-toolbar-title
                v-html="`${selectedEvent.name}<br>${selectedEvent.prospect}`"
              ></v-toolbar-title>
              <v-spacer></v-spacer>
            </v-toolbar>
            <v-card-text style="max-width: 424px;">
              <!-- <p v-html="selectedEvent.estatus" class="font-weight-black"></p> -->
              <span v-html="selectedEvent.title" class="title"></span>
              <span
                v-html="selectedEvent.reference"
                class="font-weight-black text-uppercase"
              ></span>
              <p v-html="selectedEvent.attended" class="font-weight-medium"></p>
              <span v-html="selectedEvent.details"></span>
            </v-card-text>
          </v-card>
        </v-menu>
      </v-sheet>
    </v-col>
  </v-row>
</template>

<script>
export default {
  data: () => ({
    // items: [],
    dialog: false,
    focus: '',
    type: 'month',
    typeToLabel: {
      month: 'Mes',
      week: 'Semana',
      day: 'Dia',
      '4day': '4 Dias',
    },
    selectedEvent: {},
    selectedElement: null,
    selectedOpen: false,
    events: [],
  }),
  mounted() {
    this.$refs.calendar.checkChange();
    this.loadHistorical();
    // this.updateRange({ start, end });
  },
  methods: {
    viewDay({ date }) {
      this.focus = date;
      this.type = 'day';
    },
    getEventColor(event) {
      return new Date(event.start) >= new Date() ? event.color : 'red darken-4';
    },
    setToday() {
      this.focus = '';
    },
    prev() {
      this.$refs.calendar.prev();
    },
    next() {
      this.$refs.calendar.next();
    },
    showEvent({ nativeEvent, event }) {
      const open = () => {
        this.selectedEvent = event;
        this.selectedElement = nativeEvent.target;
        setTimeout(() => {
          this.selectedOpen = true;
        }, 10);
      };

      if (this.selectedOpen) {
        this.selectedOpen = false;
        setTimeout(open, 10);
      } else {
        open();
      }

      nativeEvent.stopPropagation();
    },

    loadHistorical(cb) {
      const self = this;

      axios.get('/admin/tracking/historical/diary').then(function(response) {
        self.events = response.data.data.event;
        (cb || Function)();
      });
    },
  },
};
</script>
