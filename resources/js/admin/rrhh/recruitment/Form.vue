<template>
  <v-form v-model="valid" ref="form" lazy-validation>
    <v-expansion-panels>
      <v-expansion-panel>
        <v-expansion-panel-header>
          <template v-slot:default="{ open }">
            <v-row no-gutters class="white--text">
              <v-col cols="4">
                Vacante por cubrir:
              </v-col>
              <v-col cols="8" class="text--secondary">
                <v-fade-transition leave-absolute>
                  <span v-if="open" key="0" class="white--text">
                    Enter a name for the trip
                  </span>
                  <span v-else key="1" class="white--text">
                    {{ trip.name }}
                  </span>
                </v-fade-transition>
              </v-col>
            </v-row>
          </template>
        </v-expansion-panel-header>
        <v-expansion-panel-content>
          <v-text-field
            v-model="trip.name"
            placeholder="Caribbean Cruise"
          ></v-text-field>
        </v-expansion-panel-content>
      </v-expansion-panel>

      <v-expansion-panel>
        <v-expansion-panel-header v-slot="{ open }">
          <v-row no-gutters>
            <v-col cols="4">
              Tipo Vacante
            </v-col>
            <v-col cols="8" class="text--secondary">
              <v-fade-transition leave-absolute>
                <span v-if="open" key="0" class="white--text">
                  Select trip destination
                </span>
                <span v-else key="1" class="white--text">
                  {{ trip.location }}
                </span>
              </v-fade-transition>
            </v-col>
          </v-row>
        </v-expansion-panel-header>
        <v-expansion-panel-content>
          <v-row no-gutters>
            <!-- <v-spacer></v-spacer> -->
            <v-col cols="8">
              <v-select
                v-model="trip.location"
                :items="locations"
                outlined
                chips
                solo
              ></v-select>
            </v-col>

            <v-divider vertical class="mx-4"></v-divider>

            <v-col cols="3">
              Select your destination of choice
              <br />
              <a href="#">Learn more</a>
            </v-col>
          </v-row>

          <v-card-actions>
            <v-spacer></v-spacer>
            <v-btn text color="secondary">
              Cancel
            </v-btn>
            <v-btn text color="primary">
              Save
            </v-btn>
          </v-card-actions>
        </v-expansion-panel-content>
      </v-expansion-panel>

      <v-expansion-panel>
        <v-expansion-panel-header v-slot="{ open }">
          <v-row no-gutters>
            <v-col cols="4">
              Generales del Puesto
            </v-col>
            <v-col cols="8" class="text--secondary">
              <v-fade-transition leave-absolute>
                <span v-if="open" class="white--text"
                  >When do you want to travel?</span
                >
                <v-row
                  v-else
                  no-gutters
                  style="width: 100%;"
                  class="white--text"
                >
                  <v-col cols="6">
                    Start date: {{ trip.start || "Not set" }}
                  </v-col>
                  <v-col cols="6">
                    End date: {{ trip.end || "Not set" }}
                  </v-col>
                </v-row>
              </v-fade-transition>
            </v-col>
          </v-row>
        </v-expansion-panel-header>
        <v-expansion-panel-content>
          <v-row justify="space-around" no-gutters>
            <v-col cols="3">
              <v-menu
                ref="startMenu"
                :close-on-content-click="false"
                :return-value.sync="trip.start"
                offset-y
                min-width="290px"
              >
                <template v-slot:activator="{ on, attrs }">
                  <v-text-field
                    v-model="trip.start"
                    label="Start date"
                    prepend-icon="mdi-calendar"
                    readonly
                    v-bind="attrs"
                    v-on="on"
                  ></v-text-field>
                </template>
                <v-date-picker v-model="date" no-title scrollable>
                  <v-spacer></v-spacer>
                  <v-btn
                    text
                    color="primary"
                    @click="$refs.startMenu.isActive = false"
                  >
                    Cancel
                  </v-btn>
                  <v-btn
                    text
                    color="primary"
                    @click="$refs.startMenu.save(date)"
                  >
                    OK
                  </v-btn>
                </v-date-picker>
              </v-menu>
            </v-col>

            <v-col cols="3">
              <v-menu
                ref="endMenu"
                :close-on-content-click="false"
                :return-value.sync="trip.end"
                offset-y
                min-width="290px"
              >
                <template v-slot:activator="{ on, attrs }">
                  <v-text-field
                    v-model="trip.end"
                    label="End date"
                    prepend-icon="mdi-calendar"
                    readonly
                    v-bind="attrs"
                    v-on="on"
                  ></v-text-field>
                </template>
                <v-date-picker v-model="date" no-title scrollable>
                  <v-spacer></v-spacer>
                  <v-btn
                    text
                    color="primary"
                    @click="$refs.endMenu.isActive = false"
                  >
                    Cancel
                  </v-btn>
                  <v-btn text color="primary" @click="$refs.endMenu.save(date)">
                    OK
                  </v-btn>
                </v-date-picker>
              </v-menu>
            </v-col>
          </v-row>
        </v-expansion-panel-content>
      </v-expansion-panel>
    </v-expansion-panels>
  </v-form>
</template>
<script>
export default {
  props: {
    value: {
      type: Boolean,
      default: true,
    },
    form: {
      require: true,
      type: Object,
      default: () => {
        return {};
      },
    },
  },
  created() {
    // this.loadOptions(() => {});
  },
  data() {
    return {
      options: {
        agencies: [],
        users: [],
      },
      date: null,
      trip: {
        name: "",
        location: null,
        start: null,
        end: null,
      },
      locations: ["Remplazo", "Nueva Creacion", "Temporal", "Permanente"],
    };
  },
  computed: {
    valid: {
      get() {
        return this.value;
      },
      set(value) {
        this.$emit("input", value);
      },
    },
  },
  methods: {
    async loadOptions(cb) {
      const _this = this;
      await axios
        .get(`/admin/vehicles/resources/options`)
        .then(function (response) {
          let Data = response.data.data;
          _this.options.users = Data.users;
          _this.options.agencies = Data.agencies;
          (cb || Function)();
        });
    },
  },
};
</script>
<style lang=""></style>
