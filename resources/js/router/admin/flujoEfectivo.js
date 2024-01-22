export const flujo_efectivo = [
  {
    path: "/flujo",
    component: require("@admin/FlujoEfectivo/Index").default,
    children: [
      {
        path: "ingresos",
        name: "poliza.ingresos.list",
        component: require("@admin/FlujoEfectivo/PolizaIngresos/Index").default,
      },
      {
        path: "egresos",
        name: "poliza.egresos.list",
        component: require("@admin/FlujoEfectivo/PolizaEgresos/Index").default,
      },
      // {
      //   path: ":propGpsId/edit",
      //   name: "gps.edit",
      //   component: require("@admin/gps/gps/Edit").default,
      //   props: true,
      // },
      // {
      //   path: "customes",
      //   name: "gps.customer.list",
      //   component: require("@admin/gps/groups/Index").default,
      //   props: true,
      // },
      // {
      //   path: "chips",
      //   name: "gps.chips.list",
      //   component: require("@admin/gps/chips/Index").default,
      //   props: true,
      // },
    ],
  },
];
