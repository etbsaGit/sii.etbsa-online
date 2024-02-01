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
    ],
  },
  {
    path: "/cuentas-sucursal",
    name: "cuentas.agencias",
    component: require("@admin/FlujoEfectivo/agencyBankAccounts/Index").default,
  },
];
