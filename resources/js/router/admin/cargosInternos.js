export const cargosInternos = [
  {
    path: "/cargos-internos",
    component: require("@admin/cargosInternos/Main").default,
    children: [
      {
        path: "/",
        name: "cargos_internos.index",
        component: require("@admin/cargosInternos/List").default,
      },
      {
        path: "create",
        name: "cargos_internos.create",
        component: require("@admin/cargosInternos/Create").default,
      },
      {
        path: ":CargoInternoId/edit",
        name: "cargos_internos.edit",
        component: require("@admin/cargosInternos/Edit").default,
        props: true,
      },
    ],
  },
];
