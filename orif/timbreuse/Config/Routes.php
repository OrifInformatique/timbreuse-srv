<?php

$routes->get('admin/user-groups', '\Timbreuse\Controllers\UserGroups');
$routes->get('admin/user-groups/create', '\Timbreuse\Controllers\UserGroups::create');
$routes->post('admin/user-groups/create', '\Timbreuse\Controllers\UserGroups::create');
$routes->get('admin/user-groups/update/(:num)', '\Timbreuse\Controllers\UserGroups::update/$1');
$routes->post('admin/user-groups/update/(:num)', '\Timbreuse\Controllers\UserGroups::update/$1');

$routes->group('Timbreuse', function($routes) {
    $routes->add('home','\Timbreuse\Controllers\Home');
});

?>