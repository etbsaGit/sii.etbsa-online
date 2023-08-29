import Vue from "vue";
import Router from "vue-router";
import store from "~/common/Store";

import { admin } from "./admin";

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

      // meta: {
      //   authorize: {
      //     policy: 'view-dashboard'
      //   }
      // }
      // meta: {
      //   middleware: auth,
      //   groups: "Vendedor",
      //   permissions: "clientes.list",
      // },
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
      name: "sales.customers",
      path: "/ventas-clientes",
      component: require("@admin/marketing/components/SalesCustomerTable")
        .default,
    },
    {
      name: "sales.agencies",
      path: "/ventas-sucursales",
      component: require("@admin/marketing/components/SalesAgencyTable")
        .default,
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
  if (to.meta.middleware) {
    const middleware = Array.isArray(to.meta.middleware)
      ? to.meta.middleware
      : [to.meta.middleware];

    const context = {
      from,
      next,
      router,
      to,
    };
    const nextMiddleware = nextFactory(context, middleware, 1);

    return middleware[0]({ ...context, next: nextMiddleware });
  }
  next();
});

router.afterEach((to, from) => {
  setTimeout(() => {
    store.commit("hideLoader");
  }, 1000);
});

function nextFactory(context, middleware, index) {
  const subsequentMiddleware = middleware[index];
  // If no subsequent Middleware exists,
  // the default `next()` callback is returned.
  if (!subsequentMiddleware) return context.next;

  return (...parameters) => {
    // Run the default Vue Router `next()` callback first.
    context.next(...parameters);
    // Then run the subsequent Middleware with a new
    // `nextMiddleware()` callback.
    const nextMiddleware = nextFactory(context, middleware, index + 1);
    subsequentMiddleware({ ...context, next: nextMiddleware });
  };
}

function auth({ to, next, router }) {
  store.commit("hideLoader");
  if (
    !this.$gates.hasAnyRole(to.meta.groups) ||
    !this.$gates.hasAnyPermission(to.meta.permissions)
  ) {
    return router.back();
  }

  return next();
}

export default router;
