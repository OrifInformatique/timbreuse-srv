<?php

namespace Timbreuse\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\RedirectResponse;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;
use Timbreuse\Models\EventPlanningsModel;
use Timbreuse\Models\EventTypesModel;

class EventPlannings extends BaseController
{
    // Class properties
    private EventPlanningsModel $eventPlanningsModel;
    private EventTypesModel $eventTypesModel;

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
            'is_work_time' => ucfirst(lang('tim_lang.field_is_work_time')),
        ];

        $data['items'] = $this->eventPlanningsModel->findAll();

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
    public function createPersonal() : string|RedirectResponse {
        $eventTypes = $this->eventTypesModel->where('is_personal_event_type', true)->findAll();
        $data = [
            'title' => lang('tim_lang.create_event_planning_title'),
            'eventPlanning' => null,
            'eventTypes' => $this->mapForSelectForm($eventTypes)
        ];

        if (isset($_POST) && !empty($_POST)) {
            $data['errors'] = $this->getPostDataAndSaveEventPlanning();

            if (empty($data['errors'])) {
                return redirect()->to(base_url('admin/event-types'));
            }
        }

        return $this->display_view(['Timbreuse\Views\eventPlannings\personal\save_form'], $data);
    }
    
    /**
     * Display the create form
     *
     * @return string|RedirectResponse
     */
    public function createGroup() : string|RedirectResponse {
        $eventTypes = $this->eventTypesModel->where('is_group_event_type', true)->findAll();
        $data = [
            'title' => lang('tim_lang.create_event_planning_title'),
            'eventPlanning' => null,
            'eventTypes' => $this->mapForSelectForm($eventTypes)
        ];

        if (isset($_POST) && !empty($_POST)) {
            $data['errors'] = $this->getPostDataAndSaveEventPlanning();

            if (empty($data['errors'])) {
                return redirect()->to(base_url('admin/event-types'));
            }
        }

        return $this->display_view(['Timbreuse\Views\eventPlannings\group\save_form'], $data);
    }
    
    /**
     * Retrieves the corresponding event type and display the update form
     *
     * @param  int $id
     * @return string|RedirectResponse
     */
    public function update(int $id) : string|RedirectResponse {
        $eventPlanning = $this->eventPlanningsModel->find($id);

        if (is_null($eventPlanning)) {
            return redirect()->to(base_url('admin/event-types'));
        }

        $data = [
            'title' => lang('tim_lang.update_event_planning_title'),
            'eventPlanning' => $eventPlanning
        ];

        if (isset($_POST) && !empty($_POST)) {
            $data['errors'] = $this->getPostDataAndSaveEventPlanning();

            if (empty($data['errors'])) {
                return redirect()->to(base_url('admin/event-types'));
            }
        }

        return $this->display_view(['Timbreuse\Views\eventPlannings\save_form'], $data);
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
            return redirect()->to(base_url('admin/event-types'));
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

        return redirect()->to(base_url('admin/event-types'));
    }

    /**
     * Retrieves post data from the request and saves the event type information
     *
     * @return array Validation errors encountered during the saving process
     */
    private function getPostDataAndSaveEventPlanning() : array {
        $eventPlanning = [
            'id' => $this->request->getPost('id'),
            'name' => $this->request->getPost('name'),
            'is_group_event_planning' => (bool)$this->request->getPost('isGroupEventType'),
            'is_personal_event_planning' => (bool)$this->request->getPost('isPersonalEventType'),
        ];

        $this->eventPlanningsModel->save($eventPlanning);
        return $this->eventPlanningsModel->errors();
    }

    private function mapForSelectForm($array) : array {
        return array_combine(array_column($array, 'id'), array_map(function($row) {
            return $row['name'];
        }, $array));
    }
}
