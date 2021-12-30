<template>
  <v-card>
    <v-navigation-drawer v-model="drawer" width="250" touchless absolute>
      <v-sheet
        color="grey lighten-4"
        class="d-flex pa-4 justify-center align-center"
      >
        <v-btn color="primary">Agregar Actividad</v-btn>
      </v-sheet>

      <v-divider></v-divider>
    </v-navigation-drawer>
    <v-card :style="`margin-left: ${drawer ? '250px;' : ''} `" flat>
      <v-app-bar dense flat color="white">
        <!-- <v-app-bar-nav-icon @click="drawer = !drawer"></v-app-bar-nav-icon> -->
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
        <v-toolbar-title v-if="$refs.calendar" class="overline">
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
            <v-list-item @click="type = 'month'">
              <v-list-item-title>Mes</v-list-item-title>
            </v-list-item>
            <v-list-item @click="type = 'week'">
              <v-list-item-title>Semana</v-list-item-title>
            </v-list-item>
            <v-list-item @click="type = 'day'">
              <v-list-item-title>Dia</v-list-item-title>
            </v-list-item>
          </v-list>
        </v-menu>
      </v-app-bar>
      <v-card-text>
        <v-calendar
          ref="calendar"
          v-model="focus"
          :events="events"
          :event-color="getEventColor"
          :type="type"
          @click:event="showEvent"
          @click:more="viewDay"
        ></v-calendar>
        <v-menu
          v-model="selectedOpen"
          :close-on-content-click="false"
          :activator="selectedElement"
          max-width="500"
        >
          <v-card color="grey lighten-4" max-width="500" flat>
            <v-card-title :class="selectedEvent.color">
              <span
                class="white--text"
                v-html="`${selectedEvent.name}<br>${selectedEvent.prospect}`"
              ></span>
              <v-spacer />
              <v-btn
                icon
                @click="
                  $router.push({
                    name: 'tracking.prospect',
                    params: { propTrackingId: selectedEvent.id },
                  })
                "
              >
                <v-icon color="white">mdi-eye-outline</v-icon>
              </v-btn>
            </v-card-title>
            <v-card-text>
              <div>
                <span v-html="selectedEvent.title" class="title"></span>
                <v-chip class="font-weight-black">{{
                  selectedEvent.estatus
                }}</v-chip>
              </div>
              <div
                v-html="selectedEvent.reference"
                class="font-weight-black text-uppercase"
              ></div>
              <p v-html="selectedEvent.details"></p>
              <div
                v-html="selectedEvent.attended"
                class="font-weight-medium"
              ></div>
            </v-card-text>
          </v-card>
        </v-menu>
      </v-card-text>
    </v-card>
  </v-card>
</template>

<script>
export default {
  data() {
    return {
      drawer: false,
      right: null,
      focus: "",
      type: "month",
      typeToLabel: {
        month: "Mes",
        week: "Semana",
        day: "Dia",
      },
      selectedEvent: {},
      selectedElement: null,
      selectedOpen: false,
      events: [],
    };
  },

  mounted() {
    this.$refs.calendar.checkChange();
    this.loadHistorical();
    // this.updateRange({ start, end });
  },
  methods: {
    viewDay({ date }) {
      this.focus = date;
      this.type = "day";
    },
    getEventColor(event) {
      return new Date(event.start) >= new Date() ? event.color : "red darken-4";
    },
    setToday() {
      this.focus = "";
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

    async loadHistorical(cb) {
      const _this = this;

      await axios
        .get("/admin/tracking/historical/diary")
        .then(function (response) {
          _this.events = response.data.data.event;
          (cb || Function)();
        });
    },
  },
  computed: {
    minHeight() {
      const height = "80vh";
      return `calc(${height} - ${this.$vuetify.application.top}px)`;
    },
  },
};
</script>
