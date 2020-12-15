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
        component: require("@admin/vehicles/vehicles/VehiclesFormCreate")
          .default,
      },
      {
        path: "edit/:propVehicleId",
        name: "vehicle.edit",
        component: require("@admin/vehicles/vehicles/VehiclesFormEdit").default,
        props: true,
      },
      {
        path: "/dispersion/list",
        name: "vehicle.dispersion.list",
        component: require("@admin/vehicles/dispersals/DispersalList").default,
      },
      {
        path: "/dispersion/create/:propsVehicleId?",
        name: "vehicle.dispersal.create",
        component: require("@admin/vehicles/dispersals/DispersalCreate")
          .default,
        props: true,
      },
      {
        path: "/dispersion/edit/:propsVehicleDispersalId",
        name: "vehicle.dispersion.edit",
        component: require("@admin/vehicles/dispersals/DispersalShow").default,
        props: true,
      },
      {
        path: "/services/list",
        name: "vehicle.services.list",
        component: require("@admin/vehicles/services/ServicesVehicleList")
          .default,
      },
      {
        path: "/services/create/:propsVehicleId?",
        name: "vehicle.services.create",
        component: require("@admin/vehicles/services/ServicesCreate").default,
        props: true,
      },
      {
        path: "/services/edit/:propsVehicleServiceId",
        name: "vehicle.services.edit",
        component: require("@admin/vehicles/services/ServicesShow").default,
        props: true,
      },
    ],
  },
];
