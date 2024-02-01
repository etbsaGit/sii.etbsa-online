export const vehicles = [
  {
    path: "/vehicles",
    component: require("@admin/vehicles/Vehicles").default,
    children: [
      {
        path: "list",
        name: "vehicle.list",
        component: require("@admin/vehicles/vehicles/VehicleList").default,
      },
      {
        path: "create",
        name: "vehicle.create",
        component: require("@admin/vehicles/vehicles/Create").default,
      },
      {
        path: "edit/:propVehicleId",
        name: "vehicle.edit",
        component: require("@admin/vehicles/vehicles/Edit").default,
        props: true,
      },
      {
        path: "/dispersion/list",
        name: "vehicle.dispersal.list",
        component: require("@admin/vehicles/dispersals/VehicleDispersalList")
          .default,
      },
      {
        path: "/dispersion/create/:propVehicleId?",
        name: "vehicle.dispersal.create",
        component: require("@admin/vehicles/dispersals/Create").default,
        props: true,
      },
      {
        path: "/dispersion/edit/:propsVehicleDispersalId",
        name: "vehicle.dispersal.edit",
        component: require("@admin/vehicles/dispersals/Edit").default,
        props: true,
      },
      {
        path: "/services/list",
        name: "vehicle.services.list",
        component: require("@admin/vehicles/services/VehicleServiceList")
          .default,
      },
      {
        path: "/services/create/:propVehicleId?",
        name: "vehicle.services.create",
        component: require("@admin/vehicles/services/Create").default,
        props: true,
      },
      {
        path: "/services/edit/:propServiceId",
        name: "vehicle.services.edit",
        component: require("@admin/vehicles/services/Edit").default,
        props: true,
      },
    ],
  },
  {
    path: "/ticket-cards/list",
    name: "vehicle.ticketCard.list",
    component: require("@admin/vehicles/ticketcard/Index").default,
  },
  {
    path: "/fuels/list",
    name: "vehicle.fuels.list",
    component: require("@admin/vehicles/fuels/Index").default,
  },
];
