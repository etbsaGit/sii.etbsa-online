export const products = [
  {
    path: "/productos",
    name: "products",
    component: require("@admin/products/Products").default,
    children: [
      {
        path: "categorias",
        name: "products.category.index",
        component: require("@admin/products/category/Index").default,
      },
      {
        path: "productos",
        name: "products.index",
        component: require("@admin/products/product/Index").default,
      },
    ],
  },
];
