import Vue from "vue";
import Vuetify from "vuetify";
import es from "vuetify/es5/locale/es";
// import preset from "./default-preset/preset";

Vue.use(Vuetify);

export default new Vuetify({
  // preset,
  theme: {
    // options: {
    //   customProperties: true,
    //   variations: false,
    // },
    dark: false,
    themes: {
      dark: {
        primary: "#3f51b5",
        info: "#4c86b5",
        success: "#17b535",
        secondary: "#b0bec5",
        accent: "#8c9eff",
        error: "#b71c1c",
      },
      light: {
        primary: "#43A047",
        info: "#4c86b5",
        success: "#17b535",
        secondary: "#b0bec5",
        accent: "#8c9eff",
        error: "#b71c1c",
      },
    },
  },
  icons: {
    iconfont: "mdi",
  },
  lang: {
    locales: { es },
    current: "es",
  },
});
