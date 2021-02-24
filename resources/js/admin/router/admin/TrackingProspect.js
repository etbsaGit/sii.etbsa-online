export const TrackingProspect = [
  {
    path: '/tracking-prospect',
    name: 'trackingProspect',
    component: require('@admin/tracking/Sellers').default,
    children: [
      {
        path: 'sellers',
        name: 'sellers.list',
        component: require('@admin/tracking/seller/SellerLists').default,
      },
      {
        path: 'sellers/create',
        name: 'sellers.create',
        component: require('@admin/tracking/seller/SellerFormAdd').default,
      },
      {
        path: 'seller/edit/:propSellerId',
        name: 'sellers.edit',
        component: require('@admin/tracking/seller/SellerFormEdit').default,
        props: true,
      },
      {
        path: 'prospect',
        name: 'prospect.list',
        component: require('@admin/tracking/prospect/Index').default,
      },
      {
        path: 'prospect/create',
        name: 'prospect.create',
        component: require('@admin/tracking/prospect/Create').default,
      },
      {
        path: 'prospect/edit/:propProspectId',
        name: 'prospect.edit',
        component: require('@admin/tracking/prospect/Edit').default,
        props: true,
      },
      {
        path: 'tracking',
        name: 'tracking.list',
        component: require('@admin/tracking/tracking/Index').default,
      },
      {
        path: 'tracking/create/:propProspectId?',
        name: 'tracking.create',
        component: require('@admin/tracking/tracking/Create').default,
        props: true,
      },
      {
        path: 'tracking/edit/:propTrackingId',
        name: 'tracking.edit',
        component: require('@admin/tracking/tracking/Edit').default,
        props: true,
      },
      {
        path: 'tracking/prospect/:propTrackingId',
        name: 'tracking.prospect',
        component: require('@admin/tracking/tracking/TrackingProspect').default,
        props: true,
      },
      {
        path: 'tracking/diary',
        name: 'tracking.diary',
        component: require('@admin/tracking/diary/Index').default,
        props: true,
      },
    ],
  },
];
