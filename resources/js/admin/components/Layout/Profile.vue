<template>
  <v-menu
    origin="center center"
    transition="slide-y-transition"
    :nudge-bottom="0"
    offset-x
    offset-y
  >
    <template v-slot:activator="{ on }">
      <v-avatar v-on="on">
        <img v-if="photo" :src="photo" :alt="name" />
        <v-icon v-else x-large class="blue--text">
          mdi-account
        </v-icon>
      </v-avatar>
    </template>
    <v-list dense>
      <v-list-item>
        <v-list-item-avatar>
          <img v-if="photo" :src="photo" :alt="name" />
          <v-icon v-else x-large class="blue--text">
            mdi-account
          </v-icon>
        </v-list-item-avatar>
        <v-list-item-content>
          <v-list-item-title>{{ name }}</v-list-item-title>
          <v-list-item-subtitle>{{ user }}</v-list-item-subtitle>
        </v-list-item-content>
      </v-list-item>
      <v-divider />
      <v-list-item
        v-for="(item, index) in menuitems"
        :key="index"
        :to="!item.href ? { name: item.name } : null"
        :href="item.href"
        ripple="ripple"
        :disabled="item.disabled"
        :target="item.target"
        rel="noopener"
        @click="item.click"
      >
        <v-list-item-action v-if="item.icon">
          <v-icon>{{ item.icon }}</v-icon>
        </v-list-item-action>
        <v-list-item-content>
          <v-list-item-title>{{ item.title }}</v-list-item-title>
        </v-list-item-content>
      </v-list-item>
    </v-list>
  </v-menu>
</template>

<script>
import { mapGetters } from "vuex";

export default {
  name: "AppProfile",
  data() {
    return {
      menuitems: [
        // {
        //   icon: "mdi-account",
        //   href: "#",
        //   title: "Mi Perfil",
        //   click: (e) => {
        //     this.profile();
        //   },
        // },
        // {
        //   icon: "mdi-settings",
        //   href: "#",
        //   title: "Configuraciones",
        //   click: () => {
        //     this.toggleSettingsPanel();
        //   },
        // },
        {
          icon: "mdi-exit-to-app",
          href: "#",
          title: "Cerrar Session",
          click: () => {
            this.logout("/logout", "/");
          },
        },
      ],
    };
  },
  computed: {
    ...mapGetters("user", ["user", "name", "photo", "status"]),
  },
  methods: {
    logout(logoutUrl, afterLogoutRedirectUrl) {
      axios.post(logoutUrl).then((r) => {
        window.location.href = afterLogoutRedirectUrl;
      });
    },
    toggleSettingsPanel() {
      console.log("this.toggleSettingsPanel()");
      // this.$vuetify.goTo(0);
      // this.$store.dispatch('SettingsPanelToggle');
    },
    profile() {
      this.$router.push({
        name: "users.edit",
        params: { id: this.$gate.auth().id },
      });
    },
  },
};
</script>
