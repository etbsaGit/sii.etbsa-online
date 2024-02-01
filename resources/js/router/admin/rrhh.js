export const rrhh = [
  {
    path: "/rrhh",
    component: require("@admin/rrhh/RRHH").default,
    children: [
      {
        path: "catalogos",
        name: "rrhh.catalogs",
        component: require("@admin/rrhh/catalogs/Index").default,
      },
      {
        path: "employees/list",
        name: "rrhh.employees.list",
        component: require("@admin/rrhh/employees/Index").default,
      },
      {
        path: "recruitment/list",
        name: "rrhh.recruitment.list",
        component: require("@admin/rrhh/recruitment/Index").default,
      },
      {
        path: "recruitment/create",
        name: "rrhh.recruitment.create",
        component: require("@admin/rrhh/recruitment/Create").default,
      },
    ],
  },
];
