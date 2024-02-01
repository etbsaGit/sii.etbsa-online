<template>
  <v-app id="inspire">
    <!-- v-model="drawer" -->
    <the-layout-drawer id="nav-drawer" class="d-print-none"></the-layout-drawer>
    <v-app-bar app clipped-left flat dark class="d-print-none">
      <v-app-bar-nav-icon @click="toggleNavigation">
        <v-icon>
          {{
            $store.getters.getNavigationDrawer
              ? "mdi-format-indent-decrease"
              : "mdi-format-indent-increase"
          }}
        </v-icon>
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
      <!-- <notification></notification> -->
      <profile></profile>
    </v-app-bar>

    <v-main class="d-print-table">
      <div
        class="app-content-container boxed-container pa-6 blue-grey lighten-5 h-full"
        style="height: 100%;"
      >
        <v-slide-x-transition>
          <router-view></router-view>
        </v-slide-x-transition>
      </div>
    </v-main>
    <portal-target name="modal" multiple></portal-target>

    <v-footer app inset absolute class="d-print-none">
      <v-spacer />
      <div class="overline text-right">
        Equipos y Tractores del Bajio &copy;
        {{ new Date().getFullYear() }}
      </div>
    </v-footer>

    <!-- snackbar -->
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
    <!-- loader -->
    <div v-if="showLoader" class="wask_loader bg_half_transparent">
      <moon-loader color="green"></moon-loader>
    </div>

    <!-- dialog confirm -->
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
          <v-btn color="red" text @click.native="dialogOk">
            Confirmar
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <!-- the progress bar -->
    <vue-progress-bar></vue-progress-bar>
  </v-app>
</template>

<script>
import Profile from "@admin/components/Layout/Profile.vue";
import TheLayoutDrawer from "@admin/components/Layout/TheLayoutDrawer.vue";
import Notification from "@admin/components/shared/Notification.vue";
import { mapGetters, mapMutations } from "vuex";
export default {
  components: { TheLayoutDrawer, Notification, Profile },
  name: "App",
  data() {
    return {
      drawer: true,
      mini: false,
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
    ]),
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
    dialogOk() {
      this.$store.commit("dialogOk");
    },
    dialogCancel() {
      this.$store.commit("dialogCancel");
    },
    ...mapMutations(["toggleNavigation"]),
  },
};
</script>
<style></style>
