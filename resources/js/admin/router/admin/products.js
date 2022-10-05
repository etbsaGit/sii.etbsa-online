export const products = [
  {
    path: "/productos",
    name: "products",
    component: require("@admin/products/Products").default,
    children: [
      {
        path: "tractores",
        name: "products.tractors.index",
        component: require("@admin/products/tractors/Index").default,
      },
    ],
  },
];
