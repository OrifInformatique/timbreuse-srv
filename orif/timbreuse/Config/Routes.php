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
        $routes->get('select/(:num)', '\Timbreuse\Controllers\UserGroups::selectUserGroup/$1');
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
        $routes->get('(:num)', '\Timbreuse\Controllers\PersonalEventPlannings::index/$1');

        $routes->get('delete/serie-or-occurence/(:num)', '\Timbreuse\Controllers\PersonalEventPlannings::deleteSerieOrOccurrence/$1');
        $routes->get('ask-delete-type/(:num)', '\Timbreuse\Controllers\PersonalEventPlannings::askDeleteType/$1');
        $routes->post('ask-delete-type/(:num)', '\Timbreuse\Controllers\PersonalEventPlannings::askDeleteType/$1');
        
        $routes->get('update/(:num)', '\Timbreuse\Controllers\PersonalEventPlannings::updateSerieOrOccurrence/$1');
        $routes->get('ask-update-type/(:num)', '\Timbreuse\Controllers\PersonalEventPlannings::askUpdateType/$1');
        $routes->post('ask-update-type/(:num)', '\Timbreuse\Controllers\PersonalEventPlannings::askUpdateType/$1');

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

        $routes->get('delete/(:num)', '\Timbreuse\Controllers\PersonalEventPlannings::delete/$1');
        $routes->post('delete/(:num)/(:num)', '\Timbreuse\Controllers\PersonalEventPlannings::delete/$1/$2');
    });

    $routes->group('event-series', function($routes) {
        $routes->get('update/(:num)', '\Timbreuse\Controllers\EventSeries::update/$1');
        $routes->post('update/(:num)', '\Timbreuse\Controllers\EventSeries::update/$1');
        
        $routes->get('delete/(:num)', '\Timbreuse\Controllers\EventSeries::delete/$1');
        $routes->post('delete/(:num)/(:num)', '\Timbreuse\Controllers\EventSeries::delete/$1/$2');
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

// User groups
$routes->get('user-groups', '\Timbreuse\Controllers\UserGroups::displayByUserId');
$routes->get('user-groups/(:num)', '\Timbreuse\Controllers\UserGroups::displayByUserId/$1');
$routes->get('user-groups/select/(:num)', '\Timbreuse\Controllers\UserGroups::selectGroupsLinkToUser/$1');

// Personal event planning
$routes->group('event-plannings', function($routes) {
    $routes->get('', '\Timbreuse\Controllers\PersonalEventPlannings::index');
    $routes->get('(:num)', '\Timbreuse\Controllers\PersonalEventPlannings::index/$1');

    $routes->get('update/(:num)', '\Timbreuse\Controllers\PersonalEventPlannings::updateSerieOrOccurrence/$1');
    $routes->get('ask-update-type/(:num)', '\Timbreuse\Controllers\PersonalEventPlannings::askUpdateType/$1');
    $routes->post('ask-update-type/(:num)', '\Timbreuse\Controllers\PersonalEventPlannings::askUpdateType/$1');

    $routes->get('delete/serie-or-occurence/(:num)', '\Timbreuse\Controllers\PersonalEventPlannings::deleteSerieOrOccurrence/$1');
    $routes->get('ask-delete-type/(:num)', '\Timbreuse\Controllers\PersonalEventPlannings::askDeleteType/$1');
    $routes->post('ask-delete-type/(:num)', '\Timbreuse\Controllers\PersonalEventPlannings::askDeleteType/$1');

    $routes->get('personal/create', '\Timbreuse\Controllers\PersonalEventPlannings::createPersonal');
    $routes->post('personal/create', '\Timbreuse\Controllers\PersonalEventPlannings::createPersonal');
    $routes->get('personal/create/(:num)', '\Timbreuse\Controllers\PersonalEventPlannings::createPersonal/$1');
    $routes->get('personal/update/(:num)', '\Timbreuse\Controllers\PersonalEventPlannings::updatePersonal/$1');
    $routes->post('personal/update/(:num)', '\Timbreuse\Controllers\PersonalEventPlannings::updatePersonal/$1');
    $routes->get('personal/update/(:num)/(:num)', '\Timbreuse\Controllers\PersonalEventPlannings::updatePersonal/$1/$2');

    $routes->get('delete/(:num)', '\Timbreuse\Controllers\EventPlannings::delete/$1');
    $routes->post('delete/(:num)/(:num)', '\Timbreuse\Controllers\EventPlannings::delete/$1/$2');
});

// Event series routes
$routes->group('event-series', function($routes) {
    $routes->get('html/form', '\Timbreuse\Controllers\EventSeries::getCreateSeriesHTML');

    $routes->get('update/(:num)', '\Timbreuse\Controllers\EventSeries::update/$1');
    $routes->post('update/(:num)', '\Timbreuse\Controllers\EventSeries::update/$1');

    $routes->get('delete/(:num)', '\Timbreuse\Controllers\EventSeries::delete/$1');
    $routes->post('delete/(:num)/(:num)', '\Timbreuse\Controllers\EventSeries::delete/$1/$2');
});

$routes->group('Timbreuse', function($routes) {
    $routes->add('home','\Timbreuse\Controllers\Home');
});

?>