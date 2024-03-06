<?php



// Admin routes
$routes->group('admin', function($routes) {
    // User groups routes
    $routes->group('user-groups', function($routes) {
        $routes->get('', '\Timbreuse\Controllers\UserGroups');
        $routes->get('create', '\Timbreuse\Controllers\UserGroups::create');
        $routes->get('create/(:num)', '\Timbreuse\Controllers\UserGroups::create/$1');
        $routes->post('create', '\Timbreuse\Controllers\UserGroups::create');
        $routes->get('update/(:num)', '\Timbreuse\Controllers\UserGroups::update/$1');
        $routes->get('update/(:num)/(:num)', '\Timbreuse\Controllers\UserGroups::update/$1/$2');
        $routes->post('update/(:num)', '\Timbreuse\Controllers\UserGroups::update/$1');
        $routes->get('select-parent', '\Timbreuse\Controllers\UserGroups::selectUserGroup');
        $routes->get('select', '\Timbreuse\Controllers\UserGroups::selectUserGroup');
        $routes->get('select-parent/(:num)', '\Timbreuse\Controllers\UserGroups::selectUserGroup/$1');
    });

    // Event plannings
    $routes->group('event-plannings', function($routes) {
        $routes->get('', '\Timbreuse\Controllers\EventPlannings');
        $routes->get('personal/create', '\Timbreuse\Controllers\EventPlannings::createPersonal');
        $routes->post('personal/create', '\Timbreuse\Controllers\EventPlannings::createPersonal');
        $routes->get('personal/create/(:num)', '\Timbreuse\Controllers\EventPlannings::createPersonal/$1');
        $routes->get('group/create', '\Timbreuse\Controllers\EventPlannings::createGroup');
        $routes->get('group/create/(:num)', '\Timbreuse\Controllers\EventPlannings::createGroup/$1');
        $routes->get('delete/(:num)', '\Timbreuse\Controllers\EventPlannings::delete/$1');
        $routes->post('delete/(:num)/(:num)', '\Timbreuse\Controllers\EventPlannings::delete/$1/$2');
    });

    // Event types routes
    $routes->group('event-types', function($routes) {
        $routes->get('', '\Timbreuse\Controllers\EventTypes');
        $routes->get('create', '\Timbreuse\Controllers\EventTypes::create');
        $routes->post('create', '\Timbreuse\Controllers\EventTypes::create');
        $routes->get('update/(:num)', '\Timbreuse\Controllers\EventTypes::update/$1');
        $routes->post('update/(:num)', '\Timbreuse\Controllers\EventTypes::update/$1');
        $routes->get('delete/(:num)', '\Timbreuse\Controllers\EventTypes::delete/$1');
        $routes->post('delete/(:num)/(:num)', '\Timbreuse\Controllers\EventTypes::delete/$1/$2');
    });

    $routes->group('users', function($routes) {
        $routes->get('select', '\Timbreuse\Controllers\Users::selectUser');
    });
});

// Event series routes
$routes->get('event-series/html/form', '\Timbreuse\Controllers\EventSeries::getCreateSeriesHTML');

$routes->group('Timbreuse', function($routes) {
    $routes->add('home','\Timbreuse\Controllers\Home');
});

?>