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
        path: "customers/show",
        name: "customers.customers.show",
        component: require("@admin/customers/customers/Show").default,
      },
      
      {
        path: "customers/portfolio",
        name: "customers.portfolio",
        component: require("@admin/customers/reports/DuePortFolio").default,
      },
    ],
  },
];
