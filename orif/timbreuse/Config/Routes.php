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

        $routes->get('delete/(:num)', '\Timbreuse\Controllers\UserGroups::delete/$1');
        $routes->post('delete/(:num)/(:num)', '\Timbreuse\Controllers\UserGroups::delete/$1/$2');
        
        // Select
        $routes->get('select', '\Timbreuse\Controllers\UserGroups::selectUserGroup');
        $routes->get('select-parent', '\Timbreuse\Controllers\UserGroups::selectUserGroup');
        $routes->get('select-parent/(:num)', '\Timbreuse\Controllers\UserGroups::selectUserGroup/$1');

        // User sync groups link
        $routes->get('(:num)/link-user', '\Timbreuse\Controllers\UserSyncGroups::linkUserList/$1');
        $routes->get('(:num)/link-user/(:num)', '\Timbreuse\Controllers\UserSyncGroups::addLinkUserToGroup/$1/$2');
        $routes->get('(:num)/unlink-user/(:num)', '\Timbreuse\Controllers\UserSyncGroups::deleteLinkToGroup/$1/$2');
    });

    // Event plannings
    $routes->group('event-plannings', function($routes) {
        $routes->get('', '\Timbreuse\Controllers\EventPlannings');

        $routes->get('update/(:num)', '\Timbreuse\Controllers\PersonalEventPlannings::updateRedirect/$1');

        $routes->get('personal/create', '\Timbreuse\Controllers\PersonalEventPlannings::createPersonal');
        $routes->post('personal/create', '\Timbreuse\Controllers\PersonalEventPlannings::createPersonal');
        $routes->get('personal/create/(:num)', '\Timbreuse\Controllers\PersonalEventPlannings::createPersonal/$1');
        $routes->get('personal/update/(:num)', '\Timbreuse\Controllers\PersonalEventPlannings::updatePersonal/$1');
        $routes->post('personal/update/(:num)', '\Timbreuse\Controllers\PersonalEventPlannings::updatePersonal/$1');
        $routes->get('personal/update/(:num)/(:num)', '\Timbreuse\Controllers\PersonalEventPlannings::updatePersonal/$1/$2');
        
        $routes->get('group/create', '\Timbreuse\Controllers\EventPlannings::createGroup');
        $routes->post('group/create', '\Timbreuse\Controllers\EventPlannings::createGroup');
        $routes->get('group/create/(:num)', '\Timbreuse\Controllers\EventPlannings::createGroup/$1');
        $routes->get('group/update/(:num)', '\Timbreuse\Controllers\EventPlannings::updateGroup/$1');
        $routes->post('group/update/(:num)', '\Timbreuse\Controllers\EventPlannings::updateGroup/$1');
        $routes->get('group/update/(:num)/(:num)', '\Timbreuse\Controllers\EventPlannings::updateGroup/$1/$2');

        $routes->get('delete/(:num)', '\Timbreuse\Controllers\EventPlannings::delete/$1');
        $routes->post('delete/(:num)/(:num)', '\Timbreuse\Controllers\EventPlannings::delete/$1/$2');
    });

    $routes->group('event-series', function($routes) {
        $routes->get('', '\Timbreuse\Controllers\EventSeries');
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

// Personal event planning
$routes->group('event-plannings', function($routes) {
    $routes->get('', '\Timbreuse\Controllers\PersonalEventPlannings::index');
    $routes->get('(:num)', '\Timbreuse\Controllers\PersonalEventPlannings::index/$1');

    $routes->get('personal/create', '\Timbreuse\Controllers\PersonalEventPlannings::createPersonal');
    $routes->post('personal/create', '\Timbreuse\Controllers\PersonalEventPlannings::createPersonal');
    $routes->get('personal/create/(:num)', '\Timbreuse\Controllers\PersonalEventPlannings::createPersonal/$1');
    $routes->get('personal/update/(:num)', '\Timbreuse\Controllers\PersonalEventPlannings::updatePersonal/$1');
    $routes->post('personal/update/(:num)', '\Timbreuse\Controllers\PersonalEventPlannings::updatePersonal/$1');
    $routes->get('personal/update/(:num)/(:num)', '\Timbreuse\Controllers\PersonalEventPlannings::updatePersonal/$1/$2');

    $routes->get('delete/(:num)', '\Timbreuse\Controllers\PersonalEventPlannings::delete/$1');
        $routes->post('delete/(:num)/(:num)', '\Timbreuse\Controllers\PersonalEventPlannings::delete/$1/$2');
});

// Event series routes
$routes->get('event-series/html/form', '\Timbreuse\Controllers\EventSeries::getCreateSeriesHTML');

$routes->group('Timbreuse', function($routes) {
    $routes->add('home','\Timbreuse\Controllers\Home');
});

?>