<template>
  <v-card flat>
    <v-app-bar dark>
      <v-tabs
        v-model="tab"
        background-color="teal lighten-1"
        active-class="teal darken-1"
        icons-and-text
        centered
        grow
        dark
      >
        <v-tabs-slider color="purple"></v-tabs-slider>
        <v-tab
          v-for="item in Tabs.filter((i) => i.show)"
          :key="item.title"
          :to="item.to"
        >
          {{ item.title }} <v-icon>{{ item.icon }}</v-icon>
        </v-tab>
      </v-tabs>
    </v-app-bar>
    <v-sheet
      id="scrolling-techniques-3"
      class="overflow-y-auto"
      :max-height="minHeight"
    >
      <v-slide-x-transition>
        <keep-alive max="2">
          <router-view></router-view>
        </keep-alive>
      </v-slide-x-transition>
    </v-sheet>
  </v-card>
</template>

<script>
export default {
  data() {
    return {
      tab: "",
    };
  },
  computed: {
    minHeight() {
      const height = this.$vuetify.breakpoint.mobile ? "90vh" : "83vh";
      return `calc(${height} - ${this.$vuetify.application.top}px)`;
    },
    Tabs() {
      return [
        {
          title: "Seguimientos",
          icon: "mdi-clipboard-account-outline",
          to: { name: "tracking.list" },
          show: true,
        },
        {
          title: "Prospectos",
          icon: "mdi-account-box-multiple",
          to: { name: "prospect.list" },
          show: true,
        },
        {
          title: "Vendedores",
          icon: "mdi-account",
          to: { name: "sellers.list" },
          show: this.$gate.allow("updateUser", "user"),
        },
      ];
    },
  },
};
</script>
