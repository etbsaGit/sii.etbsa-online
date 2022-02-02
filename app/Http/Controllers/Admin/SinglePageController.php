<?php

/**
 * Created by PhpStorm.
 * User: darryldecode
 * Date: 4/2/2018
 * Time: 8:40 AM
 */

namespace App\Http\Controllers\Admin;

use App\Components\Core\Menu\MenuItem;
use App\Components\Core\Menu\MenuManager;
use App\Components\User\Models\User;

class SinglePageController extends AdminController
{
    public function displaySPA()
    {
        /**
         * @var User $currentUser
         */
        $currentUser = \Auth::user();
        $menuManager = new MenuManager();
        $menuManager->setUser($currentUser);
        $menuManager->addMenus([
            new MenuItem([
                'group_requirements' => [],
                'permission_requirements' => [],
                'label' => 'Dashboard',
                'nav_type' => MenuItem::$NAV_TYPE_NAV,
                'route_type' => 'vue',
                'route_name' => 'dashboard',
                'icon' => 'mdi-view-dashboard-variant',
                'visible' => true
            ]),

            new MenuItem([
                'group_requirements' => ['Super User', 'GPS'],
                'permission_requirements' => [],
                'label' => 'GPS',
                'nav_type' => MenuItem::$NAV_TYPE_NAV,
                'icon' => 'mdi-crosshairs-gps',
                'route_type' => 'vue',
                'route_name' => 'gps.list',
                'visible' => true,
            ]),
            // new MenuItem([
            //     'group_requirements' => [],
            //     'permission_requirements' => ['superuser'],
            //     'label' => 'Files',
            //     'nav_type' => MenuItem::$NAV_TYPE_NAV,
            //     'icon' => 'mdi-cloud-circle',
            //     'route_type' => 'vue',
            //     'route_name' => 'files',
            //     'visible' => false,
            // ]),
            // new MenuItem([
            //     'group_requirements' => [],
            //     'permission_requirements' => ['superuser'],
            //     'label' => 'Cotizador',
            //     'nav_type' => MenuItem::$NAV_TYPE_NAV,
            //     'icon' => 'mdi-file-compare',
            //     'route_type' => 'vue',
            //     'route_name' => 'quote',
            //     'visible' => false,
            // ]),
            // new MenuItem([
            //     'group_requirements' => ['Flotilla', 'Super User'],
            //     'permission_requirements' => [],
            //     'label' => 'Flotilla',
            //     'nav_type' => MenuItem::$NAV_TYPE_NAV,
            //     'icon' => 'mdi-car',
            //     'route_type' => 'vue',
            //     'route_name' => 'vehicle.list',
            //     'visible' => true,
            // ]),
            new MenuItem([
                'group_requirements' => ['Flotilla', 'Super User'],
                'permission_requirements' => [],
                'label' => 'Flotilla',
                'nav_type' => MenuItem::$NAV_TYPE_NAV,
                'icon' => 'mdi-car-side',
                'visible' => true,
                'children' => [
                    new MenuItem([
                        'group_requirements' => ['Flotilla', 'Super User'],
                        'permission_requirements' => [],
                        'label' => 'Flotilla',
                        'nav_type' => MenuItem::$NAV_TYPE_NAV,
                        'icon' => 'mdi-car-info',
                        'route_type' => 'vue',
                        'route_name' => 'vehicle.list',
                        'visible' => true,
                    ]),
                    new MenuItem([
                        'group_requirements' => ['Super User'],
                        'permission_requirements' => [],
                        'label' => 'Combustible',
                        'nav_type' => MenuItem::$NAV_TYPE_NAV,
                        'icon' => 'mdi-fuel',
                        'route_type' => 'vue',
                        'route_name' => 'vehicle.fuels.list',
                        'visible' => true,
                    ]),
                    new MenuItem([
                        'group_requirements' => ['Super User'],
                        'permission_requirements' => [],
                        'label' => 'Ticket Cards',
                        'nav_type' => MenuItem::$NAV_TYPE_NAV,
                        'icon' => 'mdi-ticket-confirmation',
                        'route_type' => 'vue',
                        'route_name' => 'vehicle.ticketCard.list',
                        'visible' => true,
                    ]),
                ]
            ]),
            // new MenuItem([
            //     'group_requirements' => ['GERENTE', 'DIRECCION', 'Vendedor', 'Super User'],
            //     'permission_requirements' => [],
            //     'label' => 'Seguimiento Prospectos',
            //     'nav_type' => MenuItem::$NAV_TYPE_NAV,
            //     'icon' => 'mdi-store',
            //     'route_type' => 'vue',
            //     'route_name' => 'tracking.list',
            //     'visible' => true,
            // ]),
            new MenuItem([
                'group_requirements' => ['GERENTE', 'DIRECCION', 'Vendedor', 'Super User'],
                'permission_requirements' => [],
                'label' => 'Ventas',
                'nav_type' => MenuItem::$NAV_TYPE_NAV,
                'icon' => 'mdi-store',
                'visible' => true,
                'children' => [
                    new MenuItem([
                        'group_requirements' => ['GERENTE', 'DIRECCION', 'Vendedor', 'Super User'],
                        'permission_requirements' => [],
                        'label' => 'Seguimientos',
                        'nav_type' => MenuItem::$NAV_TYPE_NAV,
                        'icon' => 'mdi-calendar-check',
                        'route_type' => 'vue',
                        'route_name' => 'tracking.list',
                        'visible' => true,
                    ]),
                    // new MenuItem([
                    //     'group_requirements' => ['Super User'],
                    //     'permission_requirements' => [],
                    //     'label' => 'Pedidos*',
                    //     'nav_type' => MenuItem::$NAV_TYPE_NAV,
                    //     'icon' => 'mdi-book-multiple',
                    //     'route_type' => 'vue',
                    //     'route_name' => 'sale-orders.list',
                    //     'visible' => true,
                    // ]),
                    new MenuItem([
                        'group_requirements' => ['GERENTE', 'DIRECCION', 'Vendedor', 'Super User'],
                        'permission_requirements' => [],
                        'label' => 'Prospectos',
                        'nav_type' => MenuItem::$NAV_TYPE_NAV,
                        'icon' => 'mdi-account-box-multiple',
                        'route_type' => 'vue',
                        'route_name' => 'prospect.list',
                        'visible' => true,
                    ]),
                    new MenuItem([
                        'group_requirements' => ['Super User'],
                        'permission_requirements' => [],
                        'label' => 'Graficas',
                        'nav_type' => MenuItem::$NAV_TYPE_NAV,
                        'icon' => 'mdi-chart-bar',
                        'route_type' => 'vue',
                        'route_name' => 'charts.index',
                        'visible' => true,
                    ]),
                ]
            ]),
            new MenuItem([
                'group_requirements' => ['GERENTE', 'DIRECCION', 'Super User'],
                'permission_requirements' => [],
                'label' => 'Historico Ventas',
                'nav_type' => MenuItem::$NAV_TYPE_NAV,
                'icon' => 'mdi-chart-areaspline',
                'route_type' => 'vue',
                'route_name' => 'marketing',
                'visible' => true,
            ]),
            new MenuItem([
                'group_requirements' => ['RRHH', 'Super User'],
                'permission_requirements' => [],
                'label' => 'RRHH',
                'nav_type' => MenuItem::$NAV_TYPE_NAV,
                'icon' => 'mdi-account-group-outline',
                'visible' => true,
                'children' => [
                    new MenuItem([
                        'group_requirements' => ['RRHH', 'Super User'],
                        'permission_requirements' => [],
                        'label' => 'Requisicion de Personal',
                        'nav_type' => MenuItem::$NAV_TYPE_NAV,
                        'icon' => 'mdi-account-multiple-plus',
                        'route_type' => 'vue',
                        'route_name' => 'rrhh.recruitment.list',
                        'visible' => true,
                    ]),
                    new MenuItem([
                        'group_requirements' => ['RRHH', 'Super User'],
                        'permission_requirements' => ['admin.rrhh'],
                        'label' => 'Empleados',
                        'nav_type' => MenuItem::$NAV_TYPE_NAV,
                        'icon' => 'mdi-sitemap',
                        'route_type' => 'vue',
                        'route_name' => 'rrhh.employees.list',
                        'visible' => true,
                    ]),
                ]
            ]),
            new MenuItem([
                'group_requirements' => ['Compras', 'Super User'],
                'permission_requirements' => [],
                'label' => 'Compras',
                'nav_type' => MenuItem::$NAV_TYPE_NAV,
                'icon' => 'mdi-animation',
                'visible' => true,
                'children' => [
                    new MenuItem([
                        'group_requirements' => ['Compras', 'Super User'],
                        'permission_requirements' => [],
                        'label' => 'Ordenes de Compra',
                        'nav_type' => MenuItem::$NAV_TYPE_NAV,
                        'icon' => 'mdi-animation',
                        'route_type' => 'vue',
                        'route_name' => 'purchase.list',
                        'visible' => true,
                    ]),
                    new MenuItem([
                        'group_requirements' => ['Compras', 'Super User'],
                        'permission_requirements' => [],
                        'label' => 'Facturas por Pagar',
                        'nav_type' => MenuItem::$NAV_TYPE_NAV,
                        'icon' => 'mdi-file',
                        'route_type' => 'vue',
                        'route_name' => 'purchase.invoice.list',
                        'visible' => true,
                    ]),
                    // new MenuItem([
                    //     'group_requirements' => ['Compras', 'Super User'],
                    //     'permission_requirements' => [],
                    //     'label' => 'Fac. por Pagar',
                    //     'nav_type' => MenuItem::$NAV_TYPE_NAV,
                    //     'icon' => 'mdi-file-clock',
                    //     'route_type' => 'vue',
                    //     'route_name' => 'purchase.list',
                    //     'visible' => true,
                    // ]),
                    // new MenuItem([
                    //     'group_requirements' => ['Compras', 'Super User'],
                    //     'permission_requirements' => [],
                    //     'label' => 'Fac. Pagadas',
                    //     'nav_type' => MenuItem::$NAV_TYPE_NAV,
                    //     'icon' => 'mdi-file-check',
                    //     'route_type' => 'vue',
                    //     'route_name' => 'purchase.list',
                    //     'visible' => true,
                    // ]),
                    new MenuItem([
                        'group_requirements' => ['Compras', 'Super User'],
                        'permission_requirements' => [],
                        'label' => 'Proveedores',
                        'nav_type' => MenuItem::$NAV_TYPE_NAV,
                        'icon' => 'mdi-account-box-multiple-outline',
                        'route_type' => 'vue',
                        'route_name' => 'suppliers.list',
                        'visible' => true,
                    ]),
                ]
            ]),
            new MenuItem([
                'group_requirements' => ['Super User', 'Vendedor'],
                'permission_requirements' => [],
                'label' => 'Clientes',
                'nav_type' => MenuItem::$NAV_TYPE_NAV,
                'icon' => 'mdi-account-group',
                'visible' => true,
                'children' => [
                    new MenuItem([
                        'group_requirements' => ['Super User'],
                        'permission_requirements' => [],
                        'label' => 'Lista Clientes',
                        'nav_type' => MenuItem::$NAV_TYPE_NAV,
                        'icon' => 'mdi-folder-multiple',
                        'route_type' => 'vue',
                        'route_name' => 'customers.customers.list',
                        'visible' => true,
                    ]),
                    // new MenuItem([
                    //     'group_requirements' => ['Super User'],
                    //     'permission_requirements' => [],
                    //     'label' => 'Plantilla',
                    //     'nav_type' => MenuItem::$NAV_TYPE_NAV,
                    //     'icon' => 'mdi-file-compare',
                    //     'route_type' => 'vue',
                    //     'route_name' => 'dashboard',
                    //     'visible' => true,
                    // ]),
                    // new MenuItem([
                    //     'group_requirements' => ['Super User'],
                    //     'permission_requirements' => [],
                    //     'label' => 'Requisitos',
                    //     'nav_type' => MenuItem::$NAV_TYPE_NAV,
                    //     'icon' => 'mdi-file-document',
                    //     'route_type' => 'vue',
                    //     'route_name' => 'dashboard',
                    //     'visible' => true,
                    // ]),
                ]
            ]),
            // new MenuItem([
            //     'group_requirements' => ['Super User'],
            //     'permission_requirements' => [],
            //     'label' => 'TI',
            //     'nav_type' => MenuItem::$NAV_TYPE_NAV,
            //     'icon' => 'mdi-server',
            //     'visible' => true,
            //     'children' => [
            //         new MenuItem([
            //             'group_requirements' => ['Super User'],
            //             'permission_requirements' => [],
            //             'label' => 'Inventario',
            //             'nav_type' => MenuItem::$NAV_TYPE_NAV,
            //             'icon' => 'mdi-account-multiple-check',
            //             'route_type' => 'vue',
            //             'route_name' => 'system.list',
            //             'visible' => true,
            //         ]),
            //         new MenuItem([
            //             'group_requirements' => ['Super User'],
            //             'permission_requirements' => [],
            //             'label' => 'Dispositivos',
            //             'nav_type' => MenuItem::$NAV_TYPE_NAV,
            //             'icon' => 'mdi-desktop-tower-monitor',
            //             'route_type' => 'vue',
            //             'route_name' => 'system.list',
            //             'visible' => true,
            //         ]),
            //     ]
            // ]),
            // new MenuItem([
            //     'group_requirements' => [],
            //     'permission_requirements' => ['superuser'],
            //     'label' => 'Configuracion',
            //     'nav_type' => MenuItem::$NAV_TYPE_NAV,
            //     'icon' => 'mdi-settings',
            //     'route_type' => 'vue',
            //     'route_name' => 'settings',
            //     'visible' => false,
            // ]),
            new MenuItem([
                'nav_type' => MenuItem::$NAV_TYPE_DIVIDER,
            ]),
            new MenuItem([
                'group_requirements' => [],
                'permission_requirements' => ['superuser'],
                'label' => 'Administrar',
                'nav_type' => MenuItem::$NAV_TYPE_NAV,
                'icon' => 'mdi-settings',
                'visible' => true,
                'children' => [
                    new MenuItem([
                        'group_requirements' => [],
                        'permission_requirements' => ['superuser'],
                        'label' => 'Usuarios',
                        'nav_type' => MenuItem::$NAV_TYPE_NAV,
                        'icon' => 'mdi-account',
                        'route_type' => 'vue',
                        'route_name' => 'users.list',
                        'visible' => true,
                    ]),
                    new MenuItem([
                        'group_requirements' => [],
                        'permission_requirements' => ['superuser'],
                        'label' => 'Grupos',
                        'nav_type' => MenuItem::$NAV_TYPE_NAV,
                        'icon' => 'mdi-account-group',
                        'route_type' => 'vue',
                        'route_name' => 'users.groups.list',
                        'visible' => true,
                    ]),
                    new MenuItem([
                        'group_requirements' => [],
                        'permission_requirements' => ['superuser'],
                        'label' => 'Permisos',
                        'nav_type' => MenuItem::$NAV_TYPE_NAV,
                        'icon' => 'mdi-key',
                        'route_type' => 'vue',
                        'route_name' => 'users.permissions.list',
                        'visible' => true,
                    ]),
                ]
            ]),
        ]);

        $menus = $menuManager->getFiltered();
        // return dd($menus);
        view()->share('nav', $menus);

        return view('layouts.admin');
    }
}
