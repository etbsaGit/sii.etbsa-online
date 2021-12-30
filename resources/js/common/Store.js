import Vue from "vue";
import Vuex from "vuex";

import Assertiveness from "../api/assertiveness.json";
import user from "./modules/user";

const NAV = window.LSK_APP.NAV;
Vue.use(Vuex);

export default new Vuex.Store({
  modules: {
    user,
  },
  state: {
    // breadcrumbs
    breadcrumbs: [],
    // loader
    showLoader: false,
    // snackbar
    showSnackbar: false,
    snackbarMessage: "",
    snackbarColor: "",
    snackbarDuration: 3000,
    navigationDrawer: true,
    // dialog
    dialogShow: false,
    dialogType: "",
    dialogTitle: "",
    dialogMessage: "",
    dialogIcon: null,
    dialogOkCb: () => {},
    dialogCancelCb: () => {},
    Assertiveness,
    nav: NAV,
  },
  mutations: {
    // breadcrumbs
    setBreadcrumbs(state, items) {
      items.unshift({ label: "Home", to: { name: "dashboard" } });
      state.breadcrumbs = items;
    },
    // loader
    showLoader(state) {
      state.showLoader = true;
    },
    hideLoader(state) {
      state.showLoader = false;
    },
    // snackbar
    showSnackbar(state, data) {
      state.snackbarDuration = data.duration || 3000;
      state.snackbarMessage = data.message || "No message.";
      state.snackbarColor = data.color || "info";
      state.showSnackbar = true;
    },
    hideSnackbar(state) {
      state.showSnackbar = false;
    },
    toggleNavigation(state) {
      state.navigationDrawer = !state.navigationDrawer;
    },
    // dialog
    showDialog(state, data) {
      state.dialogType = data.type || "confirm";
      state.dialogTitle = data.title;
      state.dialogMessage = data.message;
      state.dialogIcon = data.icon || null;
      state.dialogOkCb = data.okCb || function () {};
      state.dialogCancelCb = data.cancelCb || function () {};
      state.dialogShow = true;
    },
    hideDialog(state) {
      state.dialogShow = false;
    },
    dialogOk(state) {
      state.dialogOkCb();
      state.dialogShow = false;
    },
    dialogCancel(state) {
      state.dialogCancelCb();
      state.dialogShow = false;
    },
  },
  getters: {
    getNavigation: (state) => state.nav,
    getNavigationDrawer: (state) => state.navigationDrawer,
    // get breadcrumbs
    getBreadcrumbs: (state) => state.breadcrumbs,
    // loader
    showLoader: (state) => state.showLoader,
    // snackbar
    showSnackbar: (state) => state.showSnackbar,
    snackbarMessage: (state) => state.snackbarMessage,
    snackbarColor: (state) => state.snackbarColor,
    snackbarDuration: (state) => state.snackbarDuration,
    // dialog
    showDialog: (state) => state.dialogShow,
    dialogType: (state) => state.dialogType,
    dialogTitle: (state) => state.dialogTitle,
    dialogMessage: (state) => state.dialogMessage,
    dialogIcon: (state) => state.dialogIcon,
  },
  actions: {
    toggleNavigation(context) {
      context.commit("toggleNavigation");
    },
  },
});
