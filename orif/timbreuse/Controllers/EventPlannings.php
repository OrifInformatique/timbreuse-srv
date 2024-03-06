<?php

namespace Timbreuse\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\RedirectResponse;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;
use Timbreuse\Models\EventPlanningsModel;
use Timbreuse\Models\EventTypesModel;
use Timbreuse\Models\UserGroupsModel;
use Timbreuse\Models\UsersModel;

class EventPlannings extends BaseController
{
    // Class properties
    private EventPlanningsModel $eventPlanningsModel;
    private EventTypesModel $eventTypesModel;
    private UserGroupsModel $userGroupsModel;
    private UsersModel $userModel;

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
        $this->userModel = new UsersModel();
    }

    /**
     * Display the event types list
     *
     * @return string
     */
    public function index() : string {
        $data['title'] = lang('tim_lang.event_plannings_list');
        $data['list_title'] = ucfirst(lang('tim_lang.event_plannings_list'));

        $data['columns'] = [
            'event_date' => ucfirst(lang('tim_lang.field_event_date')),
            'start_time' => ucfirst(lang('tim_lang.field_start_time')),
            'end_time' => ucfirst(lang('tim_lang.field_end_time')),
            'is_work_time' => ucfirst(lang('tim_lang.field_is_work_time_short')),
        ];

        $eventPlannings = $this->eventPlanningsModel->findAll();

        $data['items'] = array_map(function($eventPlanning) {
            return [
                'id' => $eventPlanning['id'],
                'event_date' => $eventPlanning['event_date'],
                'start_time' => $eventPlanning['start_time'],
                'end_time' => $eventPlanning['end_time'],
                'is_work_time' => $eventPlanning['is_work_time'] ? lang('common_lang.yes') : lang('common_lang.no'),
            ];
        }, $eventPlannings);

        $data['url_create'] = "admin/event-plannings/group/create";
        $data['url_update'] = 'admin/event-plannings/update/';
        $data['url_delete'] = 'admin/event-plannings/delete/';

        return $this->display_view('Common\Views\items_list', $data);
    }

    /**
     * Display the create form
     *
     * @return string|RedirectResponse
     */
    public function createPersonal(?int $userId = null) : string|RedirectResponse {
        $eventTypes = $this->eventTypesModel->where('is_personal_event_type', true)->findAll();
        $user = null;

        if (!is_null($userId)) {
            $user = $this->userModel->find($userId);
        }

        $data = [
            'title' => lang('tim_lang.create_event_planning_title'),
            'eventPlanning' => null,
            'sessionEventPlanning' => session()->get('eventPlanningPostData'),
            'eventTypes' => $this->mapForSelectForm($eventTypes),
            'user' => $user
        ];

        session()->remove('eventPlanningPostData');

        if (isset($_POST) && !empty($_POST)) {
            if ($this->checkButtonClicked('select_linked_user')) {
                return redirect()->to(base_url('admin/users/select?path=admin/event-plannings/personal/create/'));
            }

            $data['errors'] = $this->getPostDataAndSaveEventPlanning();

            if (empty($data['errors'])) {
                return redirect()->to(base_url('admin/event-plannings'));
            }
        }

        return $this->display_view([
            'Timbreuse\Views\eventPlannings\personal\save_form',
            'Timbreuse\Views\eventPlannings\get_event_series_form'], $data);
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
            'title' => lang('tim_lang.create_event_planning_title'),
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
            'Timbreuse\Views\eventPlannings\group\save_form',
            'Timbreuse\Views\eventPlannings\get_event_series_form'], $data);
    }
    
    /**
     * Display the delete form and delete the corresponding event type
     *
     * @param  int $id
     * @param  int $action
     * @return string|RedirectResponse
     */
    public function delete(int $id, int $action = 0) : string|RedirectResponse {
        $eventPlanning = $this->eventPlanningsModel->find($id);

        if (!$eventPlanning) {
            return redirect()->to(base_url('admin/event-plannings'));
        }

        $data = [
            'title' => lang('tim_lang.delete_event_planning'),
            'eventPlanning' => $eventPlanning
        ];

        switch ($action) {
            case 0:
                return $this->display_view('Timbreuse\Views\eventPlannings\confirm_delete', $data);

            case 1:
                // In case soft delete is implemented
                break;
            
            case 2:
                if (isset($_POST) && !empty($_POST) && !is_null($_POST['confirmation'])) {
                    $this->eventPlanningsModel->delete($id, true);
                }
                break;
        }

        return redirect()->to(base_url('admin/event-plannings'));
    }

    /**
     * Retrieves post data from the request and saves the event type information
     *
     * @return array Validation errors encountered during the saving process
     */
    private function getPostDataAndSaveEventPlanning() : array {
        // todo: Save event serie and get id of saved + errors
        $eventPlanning = [
            'id' => $this->request->getPost('id'),
            'fk_event_series_id' => null,
            'fk_user_group_id' => $this->request->getPost('linked_user_group_id') ?? null,
            'fk_user_sync_id' => $this->request->getPost('linked_user_id') ?? null,
            'fk_event_type_id' => $this->request->getPost('fk_event_type_id'),
            'event_date' => $this->request->getPost('event_date'),
            'start_time' => $this->request->getPost('start_time'),
            'end_time' => $this->request->getPost('end_time'),
            'is_work_time' => (bool)$this->request->getPost('is_work_time'),
        ];

        $this->eventPlanningsModel->save($eventPlanning);
        return $this->eventPlanningsModel->errors();
    }

    private function mapForSelectForm($array) : array {
        return array_combine(array_column($array, 'id'), array_map(function($row) {
            return $row['name'];
        }, $array));
    }

    /**
     * Check if selectParentButton has been clicked
     * Save post data on true
     *
     * @return bool
     */
    private function checkButtonClicked(string $buttonName) : bool {
        $selectParentUserGroup = boolval($this->request->getPost($buttonName));

        if ($selectParentUserGroup) {
            $this->savePostDataToSession('eventPlanningPostData');
        }

        return $selectParentUserGroup;
    }
    
    /**
     * Save post data in the user session
     *
     * @return void
     */
    private function savePostDataToSession(string $key) : void {
        session()->set($key, $this->request->getPost());
    }
}
