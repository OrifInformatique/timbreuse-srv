<?php

namespace Timbreuse\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\RedirectResponse;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;
use Timbreuse\Models\EventTypesModel;

class EventTypes extends BaseController
{
    // Class properties
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
        $this->eventTypesModel = new EventTypesModel();
    }

    /**
     * Display the event types list
     *
     * @return string
     */
    public function index() : string {
        $data['title'] = lang('tim_lang.event_types_list');
        $data['list_title'] = ucfirst(lang('tim_lang.event_types_list'));

        $data['columns'] = [
            'name' => ucfirst(lang('tim_lang.field_name')),
            'is_group_event_type' => ucfirst(lang('tim_lang.field_is_group_event_type')),
            'is_personal_event_type' => ucfirst(lang('tim_lang.field_is_personal_event_type')),
        ];

        $eventTypes = $this->eventTypesModel->findAll();

        $data['items'] = array_map(function($eventType) {
            return [
                'id' => $eventType['id'],
                'name' => $eventType['name'],
                'is_group_event_type' => $eventType['is_group_event_type'] ? lang('common_lang.yes') : lang('common_lang.no'),
                'is_personal_event_type' => $eventType['is_personal_event_type'] ? lang('common_lang.yes') : lang('common_lang.no'),
            ];
        }, $eventTypes);

        $data['url_create'] = "admin/event-types/create";
        $data['url_update'] = 'admin/event-types/update/';
        $data['url_delete'] = 'admin/event-types/delete/';

        return $this->display_view('Common\Views\items_list', $data);
    }

    /**
     * Display the create form
     *
     * @return string|RedirectResponse
     */
    public function create() : string|RedirectResponse {
        $data = [
            'title' => lang('tim_lang.create_event_type_title'),
            'eventType' => null,
        ];

        if (isset($_POST) && !empty($_POST)) {
            $data['errors'] = $this->getPostDataAndSaveEventType();

            if (empty($data['errors'])) {
                return redirect()->to(base_url('admin/event-types'));
            }
        }

        return $this->display_view(['Timbreuse\Views\eventTypes\save_form'], $data);
    }
    
    /**
     * Retrieves the corresponding event type and display the update form
     *
     * @param  int $id
     * @return string|RedirectResponse
     */
    public function update(int $id) : string|RedirectResponse {
        $eventType = $this->eventTypesModel->find($id);

        if (is_null($eventType)) {
            return redirect()->to(base_url('admin/event-types'));
        }

        $data = [
            'title' => lang('tim_lang.update_event_type_title'),
            'eventType' => $eventType
        ];

        if (isset($_POST) && !empty($_POST)) {
            $data['errors'] = $this->getPostDataAndSaveEventType();

            if (empty($data['errors'])) {
                return redirect()->to(base_url('admin/event-types'));
            }
        }

        return $this->display_view(['Timbreuse\Views\eventTypes\save_form'], $data);
    }
    
    /**
     * Display the delete form and delete the corresponding event type
     *
     * @param  int $id
     * @param  int $action
     * @return string|RedirectResponse
     */
    public function delete(int $id, int $action = 0) : string|RedirectResponse {
        $eventType = $this->eventTypesModel->find($id);

        if (!$eventType) {
            return redirect()->to(base_url('admin/event-types'));
        }

        $data = [
            'title' => lang('tim_lang.delete_event_type'),
            'eventType' => $eventType
        ];

        switch ($action) {
            case 0:
                return $this->display_view('Timbreuse\Views\eventTypes\confirm_delete', $data);

            case 1:
                // In case soft delete is implemented
                break;
            
            case 2:
                if (isset($_POST) && !empty($_POST) && !is_null($_POST['confirmation'])) {
                    $this->eventTypesModel->delete($id, true);
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
    private function getPostDataAndSaveEventType() : array {
        $eventType = [
            'id' => $this->request->getPost('id'),
            'name' => $this->request->getPost('name'),
            'is_group_event_type' => (bool)$this->request->getPost('isGroupEventType'),
            'is_personal_event_type' => (bool)$this->request->getPost('isPersonalEventType'),
        ];

        $this->eventTypesModel->save($eventType);
        return $this->eventTypesModel->errors();
    }
}
