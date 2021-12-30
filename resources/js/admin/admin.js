require("../bootstrap");

import Vue from "vue";
import vuetify from "../plugins/vuetify"; // path to vuetify export
import App from "./App";
import router from "./router";

import VueProgressBar from "vue-progressbar";
import PortalVue from "portal-vue";
import Gate from "~/common/Gate";
import { VueMaskDirective } from "v-mask";
// import "~/plugins/lodash";
// import Vuetify from "vuetify";
// import es from "vuetify/es5/locale/es";

import store from "~/common/Store";
import eventBus from "~/common/Event";
import formatters from "~/common/Formatters";
import AxiosAjaxDetct from "~/common/AxiosAjaxDetect";
import VueCurrencyFilter from "vue-currency-filter";

import lodash from "lodash";

Vue.prototype._ = lodash;

Vue.config.productionTip = false;
Vue.use(PortalVue);
Vue.use(VueProgressBar, {
  color: "#3f51b5",
  failedColor: "#b71c1c",
  thickness: "5px",
  transition: {
    speed: "0.2s",
    opacity: "0.6s",
  },
  autoRevert: true,
  inverse: false,
  autoFinish: false,
});
Vue.use(formatters);
Vue.use(eventBus);
Vue.use(VueCurrencyFilter, [
  {
    // default name 'currency'
    symbol: "$",
    thousandsSeparator: ",",
    fractionCount: 2,
    fractionSeparator: ".",
    symbolPosition: "front",
    symbolSpacing: false,
    avoidEmptyDecimals: "00",
  },
  {
    // default name 'currency_2'
    name: "kms",
    symbol: "KMS",
    thousandsSeparator: ",",
    fractionCount: 2,
    fractionSeparator: ".",
    symbolPosition: "back",
    symbolSpacing: true,
    avoidEmptyDecimals: "",
  },
]);

// Gate
Vue.prototype.$gate = new Gate();
// Vue.prototype.$gate = new Gate(window.LSK_APP.AUTH_USER);

// Components Globals
Vue.component("moon-loader", require("vue-spinner/src/MoonLoader.vue").default);
Vue.component(
  "gauge",
  require("@chrisheanan/vue-gauge/src/components/Gauge.vue").default
);

Vue.directive("mask", VueMaskDirective);
Vue.filter("money", (value, type = "MXN") =>
  new Intl.NumberFormat("es-MX", {
    style: "currency",
    currency: type,
    maximumFractionDigits: 2,
    minimumFractionDigits: 2,
  }).format(value)
);
Vue.filter("percent", (value) =>
  new Intl.NumberFormat("es-MX", {
    style: "percent",
  }).format(value)
);

Vue.use(eventBus);

new Vue({
  eventBus,
  router,
  store,
  vuetify,
  render: (h) => h(App),
  mounted() {
    const _this = this;
    // progress bar top
    AxiosAjaxDetct.init(
      () => {
        _this.$Progress.start();
        store.commit("showLoader");
      },
      () => {
        _this.$Progress.finish();
        setTimeout(() => {
          store.commit("hideLoader");
        }, 1000);
      }
    );
  },
}).$mount("#admin");
