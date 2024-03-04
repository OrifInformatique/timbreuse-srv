<?php

// User groups routes
$routes->get('admin/user-groups', '\Timbreuse\Controllers\UserGroups');
$routes->get('admin/user-groups/create', '\Timbreuse\Controllers\UserGroups::create');
$routes->get('admin/user-groups/create/(:num)', '\Timbreuse\Controllers\UserGroups::create/$1');
$routes->post('admin/user-groups/create', '\Timbreuse\Controllers\UserGroups::create');
$routes->get('admin/user-groups/update/(:num)', '\Timbreuse\Controllers\UserGroups::update/$1');
$routes->get('admin/user-groups/update/(:num)/(:num)', '\Timbreuse\Controllers\UserGroups::update/$1/$2');
$routes->post('admin/user-groups/update/(:num)', '\Timbreuse\Controllers\UserGroups::update/$1');
$routes->get('admin/user-groups/select-parent', '\Timbreuse\Controllers\UserGroups::selectParent');
$routes->get('admin/user-groups/select-parent/(:num)', '\Timbreuse\Controllers\UserGroups::selectParent/$1');

// Event types routes
$routes->get('admin/event-types', '\Timbreuse\Controllers\EventTypes');
$routes->get('admin/event-types/create', '\Timbreuse\Controllers\EventTypes::create');
$routes->post('admin/event-types/create', '\Timbreuse\Controllers\EventTypes::create');
$routes->get('admin/event-types/update/(:num)', '\Timbreuse\Controllers\EventTypes::update/$1');
$routes->post('admin/event-types/update/(:num)', '\Timbreuse\Controllers\EventTypes::update/$1');
$routes->get('admin/event-types/delete/(:num)', '\Timbreuse\Controllers\EventTypes::delete/$1');
$routes->post('admin/event-types/delete/(:num)/(:num)', '\Timbreuse\Controllers\EventTypes::delete/$1/$2');

$routes->group('Timbreuse', function($routes) {
    $routes->add('home','\Timbreuse\Controllers\Home');
});

?>