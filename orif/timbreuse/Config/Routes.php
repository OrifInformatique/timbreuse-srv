<?php

$routes->get('admin/user-groups', '\Timbreuse\Controllers\UserGroups');
$routes->get('admin/user-groups/create', '\Timbreuse\Controllers\UserGroups::create');
$routes->get('admin/user-groups/create/(:num)', '\Timbreuse\Controllers\UserGroups::create/$1');
$routes->post('admin/user-groups/create', '\Timbreuse\Controllers\UserGroups::create');
$routes->get('admin/user-groups/update/(:num)', '\Timbreuse\Controllers\UserGroups::update/$1');
$routes->get('admin/user-groups/update/(:num)/(:num)', '\Timbreuse\Controllers\UserGroups::update/$1/$2');
$routes->post('admin/user-groups/update/(:num)', '\Timbreuse\Controllers\UserGroups::update/$1');
$routes->get('admin/user-groups/select-parent', '\Timbreuse\Controllers\UserGroups::selectParent');
$routes->get('admin/user-groups/select-parent/(:num)', '\Timbreuse\Controllers\UserGroups::selectParent/$1');

$routes->group('Timbreuse', function($routes) {
    $routes->add('home','\Timbreuse\Controllers\Home');
});

?>