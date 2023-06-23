<template>
  <v-app id="inspire">
    <!-- <v-app-bar app clipped-right flat height="72" class="d-print-none"> -->
    <v-app-bar app dense dark class="d-print-none">
      <v-app-bar-nav-icon @click="drawer = !drawer">
        <v-icon>mdi-menu-open</v-icon>
      </v-app-bar-nav-icon>
      <v-breadcrumbs
        :items="getBreadcrumbs"
        class="overline d-inline-block text-truncate"
      >
        <template v-slot:divider>
          <v-icon>mdi-forward</v-icon>
        </template>
        <template v-slot:item="props">
          <v-breadcrumbs-item
            :to="props.item.to"
            exact
            :key="props.item.label"
            :disabled="props.item.disabled"
          >
            {{ props.item.label }}
          </v-breadcrumbs-item>
        </template>
      </v-breadcrumbs>
      <v-spacer></v-spacer>

      <v-btn v-if="false" icon @click="drawerRight = !drawerRight">
        <v-icon>mdi-backburger</v-icon>
      </v-btn>
    </v-app-bar>

    <v-navigation-drawer
      v-model="drawer"
      app
      :mini-variant="drawerMini"
      :expand-on-hover="drawerMini"
      style="z-index: 99"
      class="d-print-none"
    >
      <v-list>
        <v-list-item class="px-2">
          <v-list-item-avatar>
            <v-img v-if="photo" :src="photo"></v-img>
            <v-icon v-else>mdi-account</v-icon>
          </v-list-item-avatar>
        </v-list-item>

        <v-list-item link>
          <v-list-item-content>
            <v-list-item-title class="title caption">
              {{ name }}
            </v-list-item-title>
            <v-list-item-subtitle>{{ user }}</v-list-item-subtitle>
          </v-list-item-content>
          <v-list-item-action>
            <v-btn icon @click="drawerMini = !drawerMini">
              <v-icon>{{
                drawerMini
                  ? "mdi-format-indent-increase"
                  : "mdi-format-indent-decrease"
              }}</v-icon>
            </v-btn>
          </v-list-item-action>
        </v-list-item>
      </v-list>

      <v-divider></v-divider>
      <the-layout-drawer-list :routes="getNavigation" icon-show dense />
      <template v-slot:append>
        <div class="pa-2">
          <v-btn color="purple" @click="logout('/logout', '/')" block dark>
            <v-icon left>mdi-logout-variant</v-icon>
            <span v-if="!drawerMini">Cerrar Sesion</span>
          </v-btn>
        </div>
      </template>
    </v-navigation-drawer>

    <v-navigation-drawer v-if="false" v-model="drawerRight" app clipped right>
      <v-list>
        <v-list-item v-for="n in 5" :key="n" link>
          <v-list-item-content>
            <v-list-item-title>Item {{ n }}</v-list-item-title>
          </v-list-item-content>
        </v-list-item>
      </v-list>
    </v-navigation-drawer>

    <v-main id="scrolling-techniques-4">
      <v-container
        fluid
        class="overflow-y-auto cyan lighten-5 d-print-table"
        style="height: 100%"
      >
        <v-slide-x-transition>
          <router-view></router-view>
        </v-slide-x-transition>
      </v-container>
    </v-main>

    <v-footer app color="transparent" inset absolute class="d-print-none">
      <v-spacer />
      <div class="overline text-right">
        Equipos y Tractores del Bajio &copy;
        {{ new Date().getFullYear() }}
      </div>
    </v-footer>
    <div
      v-if="showLoader"
      style="z-index: 999"
      class="d-flex justify-center align-center wask_loader bg_half_transparent"
    >
      <moon-loader color="green"></moon-loader>
    </div>
    <v-snackbar
      app
      v-model="showSnackbar"
      :timeout="snackbarDuration"
      :color="snackbarColor"
      bottom
      right
    >
      <span class="overline"> {{ snackbarMessage }} </span>
    </v-snackbar>
    <v-dialog
      v-show="showDialog"
      v-model="showDialog"
      absolute
      max-width="500px"
    >
      <v-card>
        <v-card-title class="headline grey lighten-3 text-uppercase">
          <v-icon left>
            {{ dialogIcon || "mdi-alert-decagram-outline" }}
          </v-icon>
          {{ dialogTitle }}
        </v-card-title>
        <v-card-text class="body-1 text-center py-4">
          {{ dialogMessage }}
        </v-card-text>
        <v-card-actions v-if="dialogType == 'confirm'">
          <v-spacer></v-spacer>
          <v-btn color="blue" dark @click.native="dialogCancel">
            Cancelar
          </v-btn>
          <v-btn color="red" text @click.native="dialogOk"> Confirmar </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
    <vue-progress-bar></vue-progress-bar>
    <portal-target name="modal" multiple></portal-target>
  </v-app>
</template>

<script>
import { mapGetters, mapMutations } from "vuex";
import TheLayoutDrawerList from "./components/Layout/TheLayoutDrawerList.vue";
export default {
  components: { TheLayoutDrawerList },
  data() {
    return {
      drawerMini: null,
      drawer: null,
      drawerRight: false,
    };
  },
  computed: {
    ...mapGetters([
      "getBreadcrumbs",
      "showLoader",
      "snackbarMessage",
      "snackbarColor",
      "snackbarDuration",
      "dialogType",
      "dialogTitle",
      "dialogMessage",
      "dialogIcon",
      "getNavigation",
      "showSnackbar",
      "showDialog",
    ]),
    ...mapGetters("user", ["user", "name", "photo", "status"]),
    showSnackbar: {
      get() {
        return this.$store.getters.showSnackbar;
      },
      set(val) {
        if (!val) this.$store.commit("hideSnackbar");
      },
    },
    // dialog
    showDialog: {
      get() {
        return this.$store.getters.showDialog;
      },
      set(val) {
        if (!val) this.$store.commit("hideDialog");
      },
    },
  },
  methods: {
    logout(logoutUrl, afterLogoutRedirectUrl) {
      axios.post(logoutUrl).then((r) => {
        window.location.href = afterLogoutRedirectUrl;
      });
    },
    dialogOk() {
      this.$store.commit("dialogOk");
    },
    dialogCancel() {
      this.$store.commit("dialogCancel");
    },
  },
};
</script>