import Vue from 'vue';
import Router from 'vue-router';
import store from '~/common/Store';

Vue.use(Router);

const router = new Router({
    routes: [
        {
            path: '/',
            redirect: '/dashboard',
        },
        {
            name: 'dashboard',
            path: '/dashboard',
            component: require('@admin/dashboard/Home').default,
        },
        {
            path: '/users',
            component: require('@admin/users/Users').default,
            children: [
                {
                    path:'/',
                    name:'users.list',
                    component: require('@admin/users/components/UserLists').default
                },
                {
                    path:'create',
                    name:'users.create',
                    component: require('@admin/users/components/UserFormAdd').default
                },
                {
                    path:'edit/:id',
                    name:'users.edit',
                    component: require('@admin/users/components/UserFormEdit').default,
                    props: (route) => ({propUserId: route.params.id}),
                },
                {
                    path:'groups',
                    name:'users.groups.list',
                    component: require('@admin/users/components/GroupLists').default
                },
                {
                    path:'groups/create',
                    name:'users.groups.create',
                    component: require('@admin/users/components/GroupFromAdd').default
                },
                {
                    path:'groups/edit/:id',
                    name:'users.groups.edit',
                    component: require('@admin/users/components/GroupFromEdit').default,
                    props: (route) => ({propGroupId: route.params.id}),
                },
                {
                    path:'permissions',
                    name:'users.permissions.list',
                    component: require('@admin/users/components/PermissionLists').default
                },
                {
                    path:'permissions/create',
                    name:'users.permissions.create',
                    component: require('@admin/users/components/PermissionFormAdd').default
                },
                {
                    path:'permissions/edit/:id',
                    name:'users.permissions.edit',
                    component: require('@admin/users/components/PermissionFormEdit').default,
                    props: (route) => ({propPermissionId: route.params.id}),
                },
            ]
        },
        {
            name: 'gps',
            path: '/gps',
            component: require('@admin/gps/Gps').default,
        },
        {
            name: 'quote',
            path: '/cotizador',
            component: require('@admin/quote/Quote').default,
        },
        {
            name: 'files',
            path: '/files',
            component: require('@admin/files/Files').default,
        },
        {
            name: 'settings',
            path: '/settings',
            component: require('@admin/settings/Settings').default,
        }
    ],
});

router.beforeEach((to, from, next) => {
    store.commit('showLoader');
    next();
});

router.afterEach((to, from) => {
    setTimeout(()=>{
        store.commit('hideLoader');
    },1000);
});

export default router;