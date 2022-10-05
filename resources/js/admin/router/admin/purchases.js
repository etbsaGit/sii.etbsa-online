export const purchases = [
  {
    path: "/purchases",
    component: require("@admin/purchases/Main").default,
    children: [
      {
        path: "list",
        name: "purchase.list",
        component: require("@admin/purchases/orders/Index").default,
      },
      {
        path: "create",
        name: "purchase.create",
        component: require("@admin/purchases/orders/Create").default,
      },
      {
        path: "edit",
        name: "purchase.edit",
        component: require("@admin/purchases/orders/Edit").default,
        props: true,
      },
      {
        path: "suppliers",
        name: "suppliers.list",
        component: require("@admin/purchases/suppliers/Index").default,
      },
      {
        path: "suppliers/create",
        name: "suppliers.create",
        component: require("@admin/purchases/suppliers/Create").default,
      },
      {
        path: "suppliers/:supplierId/edit",
        name: "suppliers.edit",
        component: require("@admin/purchases/suppliers/Edit").default,
        props: true,
      },
      {
        path: "purchase-invoices",
        name: "purchase.invoice.list",
        component: require("@admin/purchases/invoices/Index").default,
      },
      {
        path: "purchase-concepts",
        name: "purchase.concepts.index",
        component: require("@admin/purchases/concepts/Index").default,
      },
    ],
  },
];
