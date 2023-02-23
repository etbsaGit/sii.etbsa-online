export const system = [
  {
    path: "/system",
    component: require("@admin/systems/Main").default,
    children: [
      {
        path: "list",
        name: "system.list",
        component: require("@admin/systems/devices/Index").default,
      },
    ],
  },
];
