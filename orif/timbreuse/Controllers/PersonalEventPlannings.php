<?php

namespace Timbreuse\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\RedirectResponse;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;
use Timbreuse\Models\EventPlanningsModel;
use Timbreuse\Models\EventTypesModel;
use Timbreuse\Models\UsersModel;
use User\Models\User_model;
use Timbreuse\Controllers\EventSeries;
use Timbreuse\Controllers\PersoLogs;
use CodeIgniter\I18n\Time;

class PersonalEventPlannings extends BaseController
{
    // Class properties
    protected EventSeries $eventSeriesController;
    private PersoLogs $persoLogsController;
    private EventPlanningsModel $eventPlanningsModel;
    private EventTypesModel $eventTypesModel;
    private UsersModel $userSyncModel;
    private User_model $userModel;

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
        $this->access_level = config('\User\Config\UserConfig')->access_lvl_registered;
        parent::initController($request, $response, $logger);

        // Load required helpers
        helper('form');

        // Load required models
        $this->eventPlanningsModel = new EventPlanningsModel();
        $this->eventTypesModel = new EventTypesModel();
        $this->userSyncModel = new UsersModel();
        $this->userModel = new User_model();

        // Load required controllers
        $this->eventSeriesController = new EventSeries();
        $this->persoLogsController = new PersoLogs();
    }

    /**
     * Display the event plannings list
     *
     * @return string
     */
    public function index(?int $timUserId = null) : string|RedirectResponse {
        session()->remove('event_previous_url');
        $isAdminView = $_SESSION['user_access'] === config('\User\Config\UserConfig')->access_lvl_admin;
        $eventPlannigRoute = ($isAdminView ? 'admin/' : '') . 'event-plannings/';

        if (is_null($timUserId)) {
            if (!$isAdminView) {
                $timUserId = $this->getConnectedTimuserId();
            }
        } else if (!$isAdminView) {
            return redirect()->to('event-plannings');
        }  

        $user = $this->userSyncModel->find($timUserId ?? 0);
        if (!empty($user)) {
            $titleParameters = [
                'lastname' => $user['surname'],
                'firstname' => $user['name']
            ];
        } else {
            $titleParameters = [
                'lastname' => ucfirst(lang('tim_lang.unknown_user')),
                'firstname' => ''
            ];
        }

        $data['title'] = lang('tim_lang.personal_event_plannings_list', $titleParameters);
        $data['list_title'] = ucfirst(lang('tim_lang.personal_event_plannings_list', $titleParameters));
        $data['isVisible'] = true;
        $data['route'] = $isAdminView ? "AdminLogs/time_list/{$timUserId}" : 'PersoLogs/perso_time';

        $data['period'] = 'day';
        $data['buttons'] = $this->persoLogsController->get_buttons_for_log_views(Time::today(), $data['period'], $timUserId)['buttons'];

        $data['columns'] = [
            'event_type_name' => ucfirst(lang('tim_lang.event_type')),
            'event_date' => ucfirst(lang('tim_lang.field_event_date')),
            'start_time' => ucfirst(lang('tim_lang.field_start_time')),
            'end_time' => ucfirst(lang('tim_lang.field_end_time')),
            'is_work_time' => ucfirst(lang('tim_lang.field_is_work_time_short')),
        ];

        $eventPlannings = $this->eventPlanningsModel
            ->select('event_planning.id, name, event_date, start_time, end_time, is_work_time')
            ->join('event_type', 'event_type.id = fk_event_type_id', 'left')
            ->where('fk_user_sync_id', $timUserId)
            ->findAll();

        $data['items'] = array_map(function($eventPlanning) {
            return [
                'id' => $eventPlanning['id'],
                'event_type_name' => $eventPlanning['name'],
                'event_date' => $eventPlanning['event_date'],
                'start_time' => $eventPlanning['start_time'],
                'end_time' => $eventPlanning['end_time'],
                'is_work_time' => $eventPlanning['is_work_time'] ? lang('common_lang.yes') : lang('common_lang.no'),
            ];
        }, $eventPlannings);

        $data['url_create'] = $eventPlannigRoute . 'personal/create/' . ($isAdminView ? $timUserId : '');
        $data['url_update'] = $eventPlannigRoute . 'update/';
        $data['url_delete'] = $eventPlannigRoute . 'delete/serie-or-occurence/';

        return $this->display_view([
            'Timbreuse\Views\period_menu',
            'Common\Views\items_list'], $data);
    }

    /**
     * Display the create form
     *
     * @return string|RedirectResponse
     */
    public function createPersonal(?int $userId = null) : string|RedirectResponse {
        $views = [
            'Timbreuse\Views\eventPlannings\personal\save_form',
            'Timbreuse\Views\eventPlannings\get_event_series_form'
        ];
        $isAdminView = url_is('*admin*');
        $route = $this->getPreviousRoute();

        $eventTypes = $this->eventTypesModel->where('is_personal_event_type', true)->findAll();

        if (is_null($userId)) {
            if (!$isAdminView) {
                $userId = $this->getConnectedTimuserId();
            } else {
                $userId = 0;
            }
        } else if (!$isAdminView) {
            // The user can only create an event for himself. Remove the userId from the URL.
            return redirect()->to('event-plannings/personal/create');
        }

        $user = $this->userSyncModel->find($userId);

        $data = [
            'formAction' => $this->getFormAction($isAdminView, "event-plannings/personal/create"),
            'title' => lang('tim_lang.create_personal_event_planning_title'),
            'sessionEventPlanning' => session()->get('eventPlanningPostData'),
            'eventTypes' => $this->mapForSelectForm($eventTypes),
            'user' => $user,
            'route' => $route
        ];

        session()->remove('eventPlanningPostData');

        if (isset($_POST) && !empty($_POST)) {
            if ($this->checkButtonClicked('select_linked_user')) {
                return redirect()->to(base_url('admin/users/select?path=admin/event-plannings/personal/create/'));
            }

            $data['errors'] = $this->getPostDataAndSaveEventPlanning();

            if (empty($data['errors'])) {
                return redirect()->to(base_url($route));
            }
        }

        if ($isAdminView) {
            array_unshift($views, 'Timbreuse\Views\eventPlannings\event_tabs');
        }

        return $this->display_view($views, $data);
    }
    
    /**
     * Redirect to the corresponding route
     *
     * @param  int $id
     * @return RedirectResponse
     */
    private function updateRedirect(array $eventPlanning) : RedirectResponse {
        $route = '';

        if (!is_null($eventPlanning)) {    
            if ($_SESSION['user_access'] >= config('\User\Config\UserConfig')->access_lvl_admin) {
                $route .= is_null($eventPlanning['fk_user_sync_id']) ? 
                    "admin/event-plannings/group/update/{$eventPlanning['id']}" :
                        "admin/event-plannings/personal/update/{$eventPlanning['id']}";
            } else {
                $route .= is_null($eventPlanning['fk_user_sync_id']) ? 
                    "event-plannings/group/update/{$eventPlanning['id']}" :
                        "event-plannings/personal/update/{$eventPlanning['id']}";
            }
        } else {
            $route .= $_SESSION['_ci_previous_url'];
        }

        return redirect()->to(base_url($route));
    }
    
    /**
     * Check if event planning is part of a serie, then redirect on corresponding page
     *
     * @param  mixed $id
     * @return RedirectResponse
     */
    public function updateSerieOrOccurrence(int $id) : RedirectResponse|string {
        $eventPlanning = $this->eventPlanningsModel->find($id);
        $isAdminRoute = url_is('*admin*');
        $askUpdateRoute = ($isAdminRoute ? 'admin/' : '') . "event-plannings/ask-update-type/{$id}";

        if (is_null($eventPlanning)) {
            return redirect()->back();
        }
        
        if (is_null($eventPlanning['fk_event_series_id'])) {
            return $this->updateRedirect($eventPlanning);
        } else {
            return redirect()->to(base_url($askUpdateRoute));
        }
    }
    
    /**
     * Asks to update serie or occurrence then redirect on the corresponding page
     *
     * @param  int $id
     * @return RedirectResponse|string
     */
    public function askUpdateType(int $id) : RedirectResponse|string {
        $eventPlanning = $this->eventPlanningsModel->getWithLinkedData($id);
        $post = $this->request->getPost();
        $isAdminRoute = url_is('*admin*');

        if (is_null($eventPlanning)) {
            return redirect()->back();
        }

        $data = $this->getDataForAskUpdateOrDeleteSerie($eventPlanning, true);

        if ($post) {
            if (isset($post['modify_occurrence'])) {
                return $this->updateRedirect($eventPlanning);
            } else {
                return redirect()->to(base_url(($isAdminRoute ? 'admin/' : '') . "event-series/update/{$eventPlanning['fk_event_series_id']}"));
            }
        }

        return $this->display_view('\Timbreuse\Views\eventPlannings\ask_occurence_or_serie', $data);
    }

    /**
     * Display the create form
     *
     * @return string|RedirectResponse
     */
    public function updatePersonal(int $id, ?int $userId = 0) : string|RedirectResponse {
        $isAdminView = url_is('*admin*');
        $isUserAdmin = $_SESSION['user_access'] >= config('\User\Config\UserConfig')->access_lvl_admin;

        $eventPlanning = $this->eventPlanningsModel->find($id);

        if (is_null($eventPlanning)) {
            return redirect()->back();
        }

        if (!((int)$eventPlanning['fk_user_sync_id'] == $this->getConnectedTimuserId()
            || $isUserAdmin)) {
            return redirect()->back();
        }

        $eventTypes = $this->eventTypesModel->where('is_personal_event_type', true)->findAll();

        if (($userId !== 0 && $userId !== $eventPlanning['fk_user_sync_id']) || !$isUserAdmin) {
            $userId = $this->getConnectedTimuserId() ?? 0;
        } else {
            $userId = $userId !== 0 ?: $eventPlanning['fk_user_sync_id'];
        }

        $user = $this->userSyncModel->find($userId);

        $route = $this->getPreviousRoute();


        $data = [
            'formAction' => $this->getFormAction($isAdminView, "event-plannings/personal/update/{$id}"),
            'title' => lang('tim_lang.update_personal_event_planning_title'),
            'sessionEventPlanning' => session()->get('eventPlanningPostData'),
            'eventPlanning' => $eventPlanning,
            'eventTypes' => $this->mapForSelectForm($eventTypes),
            'user' => $user,
            'route' => $route
        ];

        session()->remove('eventPlanningPostData');

        if (isset($_POST) && !empty($_POST)) {
            if ($this->checkButtonClicked('select_linked_user')) {
                return redirect()->to(base_url('admin/users/select?path=admin/event-plannings/personal/create/'));
            }

            $data['errors'] = $this->getPostDataAndSaveEventPlanning($id);

            if (empty($data['errors'])) {
                return redirect()->to(base_url($route));
            }
        }

        return $this->display_view(['Timbreuse\Views\eventPlannings\personal\save_form'], $data);
    }

    /**
     * Asks to delete serie or occurrence then redirect on the corresponding page
     *
     * @param  int $id
     * @return RedirectResponse|string
     */
    public function askDeleteType(int $id) : RedirectResponse|string {
        $eventPlanning = $this->eventPlanningsModel->getWithLinkedData($id);
        $isAdminView = url_is('*admin*');
        $adminRoute = ($isAdminView ? 'admin/' : '');

        if (is_null($eventPlanning)) {
            return redirect()->back();
        }

        $deleteRoute = 'delete/';

        if (!is_null($_POST) && !empty($_POST)) {
            $redirectRoute = "{$adminRoute}event-series/{$deleteRoute}{$eventPlanning['fk_event_series_id']}";

            if (isset($_POST['delete_occurrence'])) {
                $redirectRoute = "{$adminRoute}event-plannings/{$deleteRoute}{$id}";
            }

            return redirect()->to($redirectRoute);
        }

        $data = $this->getDataForAskUpdateOrDeleteSerie($eventPlanning, false);

        return $this->display_view('\Timbreuse\Views\eventPlannings\ask_occurence_or_serie', $data);
    }
    
    /**
     * Check if event planning is part of a serie, then redirect on corresponding page
     *
     * @param  mixed $id
     * @return RedirectResponse
     */
    public function deleteSerieOrOccurrence(int $id) : RedirectResponse {
        $eventPlanning = $this->eventPlanningsModel->find($id);
        $adminRoute = url_is('*admin*') ? 'admin/' : '';
        $deleteEventPlanningRoute = "{$adminRoute}event-plannings/";
        $askDeleteRoute = "{$deleteEventPlanningRoute}ask-delete-type/{$id}";

        if (is_null($eventPlanning)) {
            return redirect()->back();
        }
        
        if (is_null($eventPlanning['fk_event_series_id'])) {
            $route = "{$deleteEventPlanningRoute}delete/{$id}";
            return redirect()->to($route);
        } else {
            return redirect()->to($askDeleteRoute);
        }
    }

    /**
     * Display the delete form and delete the corresponding event planning
     *
     * @param  int $id
     * @param  int $action
     * @return string|RedirectResponse
     */
    public function delete(int $id, int $action = 0) : string|RedirectResponse {
        $isUserAdmin = $_SESSION['user_access'] >= config('\User\Config\UserConfig')->access_lvl_admin;
        $eventPlanning = $this->eventPlanningsModel->getWithLinkedData($id);

        if (!$eventPlanning) {
            return redirect()->back();
        }

        $route = $this->getPreviousRoute();

        if (!((int)$eventPlanning['fk_user_sync_id'] == $this->getConnectedTimuserId()
            || $isUserAdmin)) {
            return redirect()->to(base_url($route));
        }

        $data = [
            'title' => lang('tim_lang.delete_event_planning'),
            'eventPlanning' => $eventPlanning,
            'route' => $route,
            'titleParameters' => $this->getTitleParameters($eventPlanning)
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

        return redirect()->to($route);
    }

    /**
     * Retrieves post data from the request and saves the event planning information
     *
     * @return array Validation errors encountered during the saving process
     */
    protected function getPostDataAndSaveEventPlanning(?int $id = null) : array {
        $errors = [];
        $eventPlanning = [
            'id' => $id,
            'fk_event_series_id' => null,
            'fk_user_group_id' => $this->request->getPost('linked_user_group_id'),
            'fk_user_sync_id' => $this->request->getPost('linked_user_id'),
            'fk_event_type_id' => $this->request->getPost('fk_event_type_id'),
            'event_date' => $this->request->getPost('event_date'),
            'start_time' => $this->request->getPost('start_time'),
            'end_time' => $this->request->getPost('end_time'),
            'is_work_time' => (bool)$this->request->getPost('is_work_time'),
        ];

        // Check if a serie is created
        if (array_key_exists('start_date', $_POST)) {
            $eventSerie = [
                'start_date' =>$this->request->getPost('start_date'),
                'end_date' => $this->request->getPost('end_date'),
                'recurrence_frequency' => $this->request->getPost('recurrence_frequency'),
                'recurrence_interval' => $this->request->getPost('recurrence_interval'),
                'days_of_week' => $this->request->getPost('days')
            ];
            $errors += $this->eventSeriesController->create($eventSerie, $eventPlanning);
        } else {
            $this->eventPlanningsModel->save($eventPlanning);
            $errors += $this->eventPlanningsModel->errors();
        }

        return $errors;
    }
    
    /**
     * Create an array for select forms
     *
     * @param  mixed $array
     * @return array
     */
    protected function mapForSelectForm($array) : array {
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
    protected function checkButtonClicked(string $buttonName) : bool {
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
    protected function savePostDataToSession(string $key) : void {
        session()->set($key, $this->request->getPost());
    }
    
    /**
     * Get connected tim user's id
     *
     * @return int|null
     */
    protected function getConnectedTimuserId() : int|null {
        $user = $this->userModel
            ->join('access_tim_user', 'id_ci_user = id', 'left')
            ->find($_SESSION['user_id']);

        return $user['id_user'] ?? null;
    }
    
    /**
     * Get route to previous URL
     *
     * @param  bool $isAdminView
     * @param  ?int $userId
     * @return string
     */
    public function getPreviousRoute() : string {
        $isAdminView = url_is('*admin*');

        if (!$isAdminView) {
            return base_url('event-plannings');
        } else if (isset($_SESSION['event_previous_url'])) {
            return $_SESSION['event_previous_url'];
        } else {
            $_SESSION['event_previous_url'] = $_SESSION['_ci_previous_url'];

            return $_SESSION['event_previous_url'];
        }
    }
    
    /**
     * Get action URL for forms
     *
     * @param  bool $isAdminView
     * @param  string $action
     * @return string
     */
    protected function getFormAction(bool $isAdminView, string $action) : string {
        $formAction = '';

        if ($isAdminView) {
            $formAction .= 'admin/';
        }

        $formAction .= $action;


        return $formAction;
    }
    
    /**
     * Get data for update and delete occurrence or serie
     *
     * @param  mixed $eventPlanning
     * @param  bool $update true = update, false = delete
     * @return array
     */
    private function getDataForAskUpdateOrDeleteSerie(array $eventPlanning, bool $update) : array {
        $isAdminView = url_is('*admin*');
        $of_group_or_user = '';

        if (!is_null($eventPlanning['user_group_name'])) {
            $of_group_or_user .= lang('tim_lang.of_group');
        } else {
            $of_group_or_user .= lang('tim_lang.of_user');
        }

        return [
            'eventPlanning' => $eventPlanning,
            'titleParameters' => $this->getTitleParameters($eventPlanning),
            'questionParameter' => [
                'update_or_delete' => $update ? lang('tim_lang.modify') : lang('tim_lang.delete')
            ],
            'btnOccurrence' => ($update ? 'modify' : 'delete') . '_occurrence',
            'btnSerie' => ($update ? 'modify' : 'delete') . '_serie',
            'returnRoute' => $this->getPreviousRoute($isAdminView)
        ];
    }
    
    /**
     * Generate title parameters from the event planning
     *
     * @param  array $eventPlanning
     * @return array
     */
    private function getTitleParameters(array $eventPlanning) : array {
        $of_group_or_user = '';

        if (!is_null($eventPlanning['user_group_name'])) {
            $of_group_or_user .= lang('tim_lang.of_group');
        } else {
            $of_group_or_user .= lang('tim_lang.of_user');
        }

        return [
            'event_type_name' => $eventPlanning['event_type_name'],
            'of_group_or_user' => $of_group_or_user,
            'group_or_user' => $eventPlanning['user_group_name'] ?? 
                "{$eventPlanning['user_firstname']} {$eventPlanning['user_lastname']}",
        ];
    }
}
