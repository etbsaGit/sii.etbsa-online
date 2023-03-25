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
      {
        path: "marcas-proveedores",
        name: "products.brands.index",
        component: require("@admin/products/brands/Index").default,
      },
      {
        path: "categorias-productos",
        name: "products.categories.index",
        component: require("@admin/products/Category").default,
      },
      {
        path: "attributos",
        name: "products.attributes.index",
        component: require("@admin/products/attributes/Index").default,
      },
      {
        path: "exchanges",
        name: "products.exchanges.index",
        component: require("@admin/products/exchanges/Index").default,
      },
    ],
  },
];
