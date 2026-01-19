<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

/*
|--------------------------------------------------------------------
| DEFAULT ROUTE
|--------------------------------------------------------------------
*/
$routes->get('/', 'Auth::login');

/*
|--------------------------------------------------------------------
| AUTH ROUTES
|--------------------------------------------------------------------
*/
$routes->group('auth', function (RouteCollection $routes) {

    $routes->get('login', 'Auth::login');
    $routes->post('process_login', 'Auth::process_login');

    // ONLY SUPER MANAGER SHOULD ACCESS
    $routes->get(
        'register',
        'Auth::register',
        ['filter' => ['auth', 'permission']]
    );

    $routes->post(
        'process_register',
        'Auth::process_register',
        ['filter' => ['auth', 'permission']]
    );

    $routes->get('logout', 'Auth::logout');
});

/*
|--------------------------------------------------------------------
| TEAM BUILDER (PROTECTED + DEPARTMENT + ROLE)
|--------------------------------------------------------------------
*/
$routes->group(
    'team-builder',
    ['filter' => ['auth', 'department']],
    function (RouteCollection $routes) {

        // Dashboard (ALL ROLES)
        $routes->get('/', 'TeamBuilder::index');

        // Department Data (ALL ROLES - READ ONLY FOR INTERN/MEMBER)
        $routes->get(
            'get-data/(:num)',
            'TeamBuilder::get_dept_data/$1'
        );

        // ---------------- WRITE ACTIONS ----------------
        $routes->post('assign', 'TeamBuilder::assign_member', ['filter' => 'permission']);
        $routes->post('update-assignment', 'TeamBuilder::update_assignment', ['filter' => 'permission']);
        $routes->post('remove', 'TeamBuilder::remove_member', ['filter' => 'permission']);

        // Roles
        $routes->post('save-role-only', 'TeamBuilder::save_role_only', ['filter' => 'permission']);

        // Zones
        $routes->post('save-zone-only', 'TeamBuilder::save_zone_only', ['filter' => 'permission']);

        // ---------------- READ-ONLY ----------------
        $routes->get(
            'zone/(:num)/destinations',
            'TeamBuilder::get_zone_destinations/$1'
        );

        /*
        |---------------------------------------------------------------
        | DESTINATIONS MODULE
        |---------------------------------------------------------------
        */
        $routes->get('destinations', 'Destination::index');
        $routes->get('destinations/list', 'Destination::listData');

        // WRITE → MANAGER & SUPER MANAGER ONLY
        $routes->post('destinations/save', 'Destination::save', ['filter' => 'permission']);
        $routes->post('destinations/save-state', 'Destination::saveState', ['filter' => 'permission']);
        $routes->post('destinations/delete', 'Destination::delete', ['filter' => 'permission']);
        $routes->post('destinations/update', 'Destination::update', ['filter' => 'permission']);
    }
);

/*
|--------------------------------------------------------------------
| ZONES → DESTINATION MAPPING
|--------------------------------------------------------------------
*/
$routes->group(
    'zones',
    ['filter' => ['auth']],
    function (RouteCollection $routes) {

        $routes->get('/', 'Zones::index');
        $routes->get('get-destinations', 'Zones::getDestinations');
        $routes->get('get-zone-destinations/(:num)', 'Zones::getZoneDestinations/$1');
        $routes->post('save-mapping', 'Zones::saveMapping');
        $routes->post('update', 'Zones::update');
    }
);

/*
|--------------------------------------------------------------------
| MEMBER VIEW (READ-ONLY)
|--------------------------------------------------------------------
*/
$routes->group(
    'member',
    ['filter' => 'auth'],
    function (RouteCollection $routes) {

        $routes->get('zones', 'MemberZones::index');
        $routes->get('zones/(:num)', 'MemberZones::destinations/$1');
    }
);

/*
|--------------------------------------------------------------------
| PROFILE MODULE
|--------------------------------------------------------------------
*/
$routes->group(
    'profile',
    ['filter' => 'auth'],
    function (RouteCollection $routes) {

        $routes->get('/', 'Profile::index');
        $routes->post('update', 'Profile::update');
        $routes->post('change-password', 'Profile::changePassword');
        $routes->post('upload-image', 'Profile::uploadImage');
    }
);

/*
|--------------------------------------------------------------------
| SETUP MODULE (SUPER MANAGER ONLY)
|--------------------------------------------------------------------
*/
$routes->group(
    'setup',
    ['filter' => ['auth', 'permission']],
    function (RouteCollection $routes) {

        $routes->get('/', 'Setup::index');
        $routes->post('upload-employees', 'Setup::upload_employees');
    }
);
