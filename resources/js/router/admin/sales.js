export const sales = [
  {
    path: "/ventas",
    name: "sales",
    component: require("@admin/sales/Sales").default,
    children: [
      // {
      //   path: 'notification',
      //   name: 'notification.list',
      //   component: require('@admin/sales/tracking/Notification').default,
      // },
      // SALE_ORDERS
      {
        path: "sale-orders",
        name: "sale-orders.list",
        component: require("@admin/sales/sale_orders/SaleOrderList").default,
      },
      {
        path: "sale-orders/create",
        name: "sale-orders.create",
        component: require("@admin/sales/sale_orders/SaleOrderCreate").default,
        props: true,
      },
      {
        path: "sale-order/:propSaleOrderId/edit",
        name: "sale-orders.edit",
        component: require("@admin/sales/sale_orders/SaleOrderEdit").default,
        props: true,
      },
      // SELLERS
      {
        path: "sellers",
        name: "sellers.list",
        component: require("@admin/sales/seller/SellerLists").default,
      },
      {
        path: "sellers/create",
        name: "sellers.create",
        component: require("@admin/sales/seller/SellerFormAdd").default,
      },
      {
        path: "seller/edit/:propSellerId",
        name: "sellers.edit",
        component: require("@admin/sales/seller/SellerProfileConfig").default,
        // component: require("@admin/sales/seller/SellerFormEdit").default,
        props: true,
      },
      {
        path: "prospect",
        name: "prospect.list",
        component: require("@admin/sales/prospect/Index").default,
      },
      {
        path: "prospect/create",
        name: "prospect.create",
        component: require("@admin/sales/prospect/Create").default,
      },
      {
        path: "prospect/edit/:propProspectId",
        name: "prospect.edit",
        component: require("@admin/sales/prospect/Edit").default,
        props: true,
      },
      {
        path: "tracking",
        name: "tracking.list",
        component: require("@admin/sales/tracking/TrackingLists").default,
      },
      {
        path: "tracking/create/:propProspectId?",
        name: "tracking.create",
        component: require("@admin/sales/tracking/TrackingCreate").default,
        props: true,
      },
      {
        path: "tracking/edit/:propTrackingId",
        name: "tracking.edit",
        component: require("@admin/sales/tracking/TrackingEdit").default,
        props: true,
      },
      {
        path: "tracking/prospect/:propTrackingId",
        name: "tracking.prospect",
        component: require("@admin/sales/tracking/TrackingProspect").default,
        props: true,
      },
      {
        path: "tracking/diary",
        name: "tracking.diary",
        component: require("@admin/sales/diary/Index").default,
        props: true,
      },
      {
        path: "tracking/stat",
        name: "tracking.stat",
        component: require("@admin/sales/statTracking/Index").default,
        props: true,
      },
      {
        path: "charts",
        name: "charts.index",
        component: require("@admin/sales/charts/Index").default,
        props: true,
      },
    ],
  },
];
