export const customers = [
  {
    path: "/customers",
    component: require("@admin/customers/Customers").default,
    children: [
      {
        path: "customers/list",
        name: "customers.customers.list",
        component: require("@admin/customers/customers/Index").default,
      },
      {
        path: "customers/create",
        name: "customers.customers.create",
        component: require("@admin/customers/customers/Create").default,
      },
      {
        path: "customers/show/:itemId",
        name: "customers.customers.show",
        component: require("@admin/customers/customers/Show").default,
        props: true,
      },

      {
        path: "customers/portfolio",
        name: "customers.portfolio",
        component: require("@admin/customers/reports/DuePortFolio").default,
      },
      {
        path: "customers/last-invoice",
        name: "customers.last-invoice",
        component: require("@admin/customers/customers/Index").default,
      },
      {
        path: "reports/info-geografica",
        name: "reports.geografica",
        component: require("@admin/customers/reports/MapsTabs").default,
      },
      {
        path: "reports/adr",
        name: "reports.adr",
        component: require("@admin/customers/reports/MapsADRTabs").default,
      },
    ],
  },
];
