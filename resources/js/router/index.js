import Vue from "vue";
import Router from "vue-router";
import store from "~/common/Store";

import { admin } from  "./admin";

Vue.use(Router);

const router = new Router({
  scrollBehavior: () => ({ y: 0 }),
  routes: [
    {
      path: "/",
      redirect: "/dashboard",
    },
    {
      name: "dashboard",
      path: "/dashboard",
      component: require("@admin/dashboard/Home").default,
    },
    {
      name: "quote",
      path: "/cotizador",
      component: require("@admin/quote/Quote").default,
    },
    {
      name: "files",
      path: "/files",
      component: require("@admin/files/Files").default,
    },
    {
      name: "marketing",
      path: "/marketing",
      component: require("@admin/marketing/Marketing").default,
    },
    {
      name: "settings",
      path: "/settings",
      component: require("@admin/settings/Settings").default,
    },
    {
      name: "notification.list",
      path: "/notification",
      component: require("@admin/components/NotificationList").default,
    },
    ...admin,
  ],
});

router.beforeEach((to, from, next) => {
  store.commit("showLoader");
  next();
});

router.afterEach((to, from) => {
  setTimeout(() => {
    store.commit("hideLoader");
  }, 1000);
});

export default router;
