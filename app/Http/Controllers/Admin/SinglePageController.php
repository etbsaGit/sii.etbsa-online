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
                'permission_requirements' => ['superuser'],
                'label' => 'Dashboard',
                'nav_type' => MenuItem::$NAV_TYPE_NAV,
                'icon' => 'mdi-view-dashboard-variant',
                'route_type' => 'vue',
                'route_name' => 'dashboard',
                'visible' => true,
            ]),
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
                'group_requirements' => ['Super User', 'GPS'],
                'permission_requirements' => [],
                'label' => 'GPS',
                'nav_type' => MenuItem::$NAV_TYPE_NAV,
                'icon' => 'mdi-crosshairs-gps',
                'route_type' => 'vue',
                'route_name' => 'gps',
                'visible' => true,
            ]),
            new MenuItem([
                'group_requirements' => [],
                'permission_requirements' => ['superuser'],
                'label' => 'Files',
                'nav_type' => MenuItem::$NAV_TYPE_NAV,
                'icon' => 'mdi-cloud-circle',
                'route_type' => 'vue',
                'route_name' => 'files',
                'visible' => false,
            ]),
            new MenuItem([
                'group_requirements' => [],
                'permission_requirements' => ['superuser'],
                'label' => 'Cotizador',
                'nav_type' => MenuItem::$NAV_TYPE_NAV,
                'icon' => 'mdi-file-compare',
                'route_type' => 'vue',
                'route_name' => 'quote',
                'visible' => false,
            ]),
            new MenuItem([
                'group_requirements' => ['FLOTILLA', 'Super User'],
                'permission_requirements' => ['flotilla.menu'],
                'label' => 'Flotilla de Unidades',
                'nav_type' => MenuItem::$NAV_TYPE_NAV,
                'icon' => 'mdi-car',
                'route_type' => 'vue',
                'route_name' => 'vehicle.list',
                'visible' => true,
            ]),
            new MenuItem([
                'group_requirements' => ['GERENTE', 'DIRECCION', 'Vendedor', 'Super User'],
                'permission_requirements' => [],
                'label' => 'Seguimiento Prospectos',
                'nav_type' => MenuItem::$NAV_TYPE_NAV,
                'icon' => 'mdi-store',
                'route_type' => 'vue',
                'route_name' => 'tracking.list',
                'visible' => true,
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
                'group_requirements' => [],
                'permission_requirements' => ['superuser'],
                'label' => 'Configuracion',
                'nav_type' => MenuItem::$NAV_TYPE_NAV,
                'icon' => 'mdi-settings',
                'route_type' => 'vue',
                'route_name' => 'settings',
                'visible' => false,
            ]),
            new MenuItem([
                'nav_type' => MenuItem::$NAV_TYPE_DIVIDER,
            ])
        ]);

        $menus = $menuManager->getFiltered();

        view()->share('nav', $menus);

        return view('layouts.admin');
    }
}
