export const nt = [
  {
    path: "/nt",
    name: "nt",
    component: require("@admin/nt/NT").default,
    children: [
      {
        path: "list",
        name: "nt.comparative.list",
        component: require("@admin/nt/Comparative/Index").default,
      },
      {
        path: "create",
        name: "nt.comparative.create",
        component: require("@admin/nt/Comparative/Create").default,
      },

      {
        path: "edit/:editItemId",
        name: "nt.comparative.edit",
        component: require("@admin/nt/Comparative/Edit").default,
        props: true,
      },
      {
        path: "equipment/list",
        name: "nt.equipment.list",
        component: require("@admin/nt/AmsEquipment/Index").default,
      },
      {
        path: "equipment/create",
        name: "nt.equipment.create",
        component: require("@admin/nt/AmsEquipment/Create").default,
      },

      {
        path: "equipment/edit/:editItemId",
        name: "nt.equipment.edit",
        component: require("@admin/nt/AmsEquipment/Edit").default,
        props: true,
      },
    ],
  },
];
