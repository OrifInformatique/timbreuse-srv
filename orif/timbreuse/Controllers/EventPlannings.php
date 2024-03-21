<?php

namespace Timbreuse\Controllers;

use CodeIgniter\HTTP\RedirectResponse;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;
use Timbreuse\Models\EventPlanningsModel;
use Timbreuse\Models\EventTypesModel;
use Timbreuse\Models\UserGroupsModel;

class EventPlannings extends PersonalEventPlannings
{
    // Class properties
    private EventPlanningsModel $eventPlanningsModel;
    private EventTypesModel $eventTypesModel;
    private UserGroupsModel $userGroupsModel;

    /**
     * Constructor
     */
    public function initController(
        RequestInterface $request,
        ResponseInterface $response,
        LoggerInterface $logger
    ): void {
        // Set Access level before calling parent constructor
        // Accessibility reserved to admin users
        $this->access_level = config('\User\Config\UserConfig')->access_lvl_admin;
        parent::initController($request, $response, $logger);

        // Load required helpers
        helper('form');

        // Load required models
        $this->eventPlanningsModel = new EventPlanningsModel();
        $this->eventTypesModel = new EventTypesModel();
        $this->userGroupsModel = new UserGroupsModel();
    }

    /**
     * Display the event plannings list
     *
     * @return string
     */
    #[\Override]
    public function index(?int $timUserId = null) : string {
        $data['title'] = lang('tim_lang.event_plannings_list');
        $data['list_title'] = ucfirst(lang('tim_lang.event_plannings_list'));
        $data['isVisible'] = true;
        $data['route'] = '';

        $data['columns'] = [
            'group_or_user_name' => ucfirst(lang('tim_lang.group_or_user_name')),
            'event_type_name' => ucfirst(lang('tim_lang.event_type')),
            'event_type_name' => ucfirst(lang('tim_lang.event_type')),
            'event_date' => ucfirst(lang('tim_lang.field_event_date')),
            'start_time' => ucfirst(lang('tim_lang.field_start_time')),
            'end_time' => ucfirst(lang('tim_lang.field_end_time')),
            'is_work_time' => ucfirst(lang('tim_lang.field_is_work_time_short')),
        ];

        $eventPlannings = $this->eventPlanningsModel
            ->select('
                event_planning.id,
                event_date,
                start_time,
                end_time,
                is_work_time,
                event_type.name AS event_type_name,
                user_group.name AS user_group_name,
                user_sync.name AS user_lastname,
                user_sync.surname AS user_firstname'    
            )
            ->join('event_type', 'event_type.id = fk_event_type_id', 'left')
            ->join('user_sync', 'user_sync.id_user = fk_user_sync_id', 'left')
            ->join('user_group', 'user_group.id = fk_user_group_id', 'left')
            ->findAll();

        $data['items'] = array_map(function($eventPlanning) {
            return [
                'id' => $eventPlanning['id'],
                'event_type_name' => $eventPlanning['event_type_name'],
                'event_date' => $eventPlanning['event_date'],
                'start_time' => $eventPlanning['start_time'],
                'end_time' => $eventPlanning['end_time'],
                'is_work_time' => $eventPlanning['is_work_time'] ? lang('common_lang.yes') : lang('common_lang.no'),
                'group_or_user_name' => $eventPlanning['user_group_name'] ?? 
                    "{$eventPlanning['user_firstname']} {$eventPlanning['user_lastname']}",
            ];
        }, $eventPlannings);

        $data['url_create'] = "admin/event-plannings/group/create";
        $data['url_update'] = 'admin/event-plannings/update/';
        $data['url_delete'] = 'admin/event-plannings/delete/serie-or-occurence/';

        return $this->display_view(['Common\Views\items_list'], $data);
    }
    
    /**
     * Display the create form
     *
     * @return string|RedirectResponse
     */
    public function createGroup(?int $userGroupId = null) : string|RedirectResponse {
        $eventTypes = $this->eventTypesModel->where('is_group_event_type', true)->findAll();
        $userGroup = null;

        if (!is_null($userGroupId)) {
            $userGroup = $this->userGroupsModel->find($userGroupId);
        }

        $data = [
            'title' => lang('tim_lang.create_group_event_planning_title'),
            'eventPlanning' => null,
            'sessionEventPlanning' => session()->get('eventPlanningPostData'),
            'eventTypes' => $this->mapForSelectForm($eventTypes),
            'userGroup' => $userGroup
        ];

        session()->remove('eventPlanningPostData');

        if (isset($_POST) && !empty($_POST)) {
            if ($this->checkButtonClicked('select_user_group')) {
                return redirect()->to(base_url('admin/user-groups/select?path=admin/event-plannings/group/create/'));
            }

            $data['errors'] = $this->getPostDataAndSaveEventPlanning();

            if (empty($data['errors'])) {
                return redirect()->to(base_url('admin/event-plannings'));
            }
        }

        return $this->display_view([
            'Timbreuse\Views\eventPlannings\event_tabs',
            'Timbreuse\Views\eventPlannings\group\save_form',
            'Timbreuse\Views\eventPlannings\get_event_series_form'], $data);
    }
    
    /**
     * Display and update a group event
     *
     * @param  int $id
     * @param  int $userGroupId
     * @return string
     */
    public function updateGroup(int $id, ?int $userGroupId = 0) : string|RedirectResponse {
        $route = 'admin/event-plannings';

        $eventPlanning = $this->eventPlanningsModel->find($id);

        if (is_null($eventPlanning)) {
            return redirect()->back();
        }
        $userGroupId = $userGroupId !== 0 ?: $eventPlanning['fk_user_group_id'];
        $eventTypes = $this->eventTypesModel->where('is_group_event_type', true)->findAll();

        $userGroup = $this->userGroupsModel->find($userGroupId);

        $data = [
            'title' => lang('tim_lang.update_group_event_planning_title'),
            'sessionEventPlanning' => session()->get('eventPlanningPostData'),
            'eventPlanning' => $eventPlanning,
            'eventTypes' => $this->mapForSelectForm($eventTypes),
            'userGroup' => $userGroup,
            'route' => $route
        ];

        session()->remove('eventPlanningPostData');

        if (isset($_POST) && !empty($_POST)) {
            if ($this->checkButtonClicked('select_user_group')) {
                return redirect()->to(base_url('admin/user-groups/select?path=admin/event-plannings/group/create/'));
            }

            $data['errors'] = $this->getPostDataAndSaveEventPlanning($id);

            if (empty($data['errors'])) {
                return redirect()->to(base_url('admin/event-plannings'));
            }
        }

        return $this->display_view(['Timbreuse\Views\eventPlannings\group\save_form'], $data);
    }
}
