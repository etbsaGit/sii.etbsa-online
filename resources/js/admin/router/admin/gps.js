export const gps = [
  {
    path: '/gps',
    name: 'gps',
    component: require('@admin/gps/Gps').default,
  },
  {
    path: '/gps/create',
    name: 'gps.create',
    component: require('@admin/gps/gps/Create').default,
  },
  {
    path: '/gps/:propGpsId/edit',
    name: 'gps.edit',
    component: require('@admin/gps/gps/Edit').default,
    props: true,
  },
];
