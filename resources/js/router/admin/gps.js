export const gps = [
  {
    path: "/gps",
    component: require("@admin/gps/Gps").default,
    children: [
      {
        path: "list",
        name: "gps.list",
        component: require("@admin/gps/gps/Index").default,
      },
      {
        path: "create",
        name: "gps.create",
        component: require("@admin/gps/gps/Create").default,
      },
      {
        path: ":propGpsId/edit",
        name: "gps.edit",
        component: require("@admin/gps/gps/Edit").default,
        props: true,
      },
      {
        path: "customes",
        name: "gps.customer.list",
        component: require("@admin/gps/groups/Index").default,
        props: true,
      },
      {
        path: "chips",
        name: "gps.chips.list",
        component: require("@admin/gps/chips/Index").default,
        props: true,
      },
    ],
  },
];
