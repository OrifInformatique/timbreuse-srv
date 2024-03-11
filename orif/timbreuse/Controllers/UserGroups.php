<?php

namespace Timbreuse\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\RedirectResponse;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;
use Timbreuse\Models\UserGroupsModel;
use Timbreuse\Models\UserSyncGroupsModel;
use Timbreuse\Models\EventPlanningsModel;
use Timbreuse\Controllers\Users;

class UserGroups extends BaseController
{
    // Class properties
    private UserGroupsModel $userGroupsModel;
    private UserSyncGroupsModel $userSyncGroupsModel;
    private EventPlanningsModel $eventPlanningsModel;
    private Users $userSyncController;

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
        $this->userGroupsModel = new UserGroupsModel();
        $this->userSyncGroupsModel = new UserSyncGroupsModel();
        $this->eventPlanningsModel = new EventPlanningsModel();

        // Load required controllers
        $this->userSyncController = new Users();
    }
    
    /**
     * Display the user group list
     *
     * @return string
     */
    public function index() : string {
        $data['title'] = lang('tim_lang.user_group_list');
        $data['list_title'] = ucfirst(lang('tim_lang.user_group_list'));

        $data['columns'] = [
            'userGroupName' => ucfirst(lang('tim_lang.field_name')),
            'parentUserGroupName' => ucfirst(lang('tim_lang.field_group_parent_name')),
        ];

        $data['items'] = $this->userGroupsModel->getUserGroups();

        $data['url_create'] = "admin/user-groups/create";
        $data['url_update'] = 'admin/user-groups/update/';
        $data['url_delete'] = 'admin/user-groups/delete/';

        return $this->display_view('Common\Views\items_list', $data);
    }
    
    /**
     * Display the create form
     *
     * @return string|RedirectResponse
     */
    public function create(int $parentId = null) : string|RedirectResponse {
        $parentUserGroup = $this->userGroupsModel->find($parentId);

        $data = [
            'title' => lang('tim_lang.create_user_group_title'),
            'userGroup' => null,
            'sessionUserGroup' => session()->get('groupPostData') ?? null,
            'parentUserGroup' => $parentUserGroup,
        ];

        session()->remove('groupPostData');

        if (isset($_POST) && !empty($_POST)) {
            if ($this->checkSelectParent()) {
                return redirect()->to(base_url('admin/user-groups/select-parent?path=admin/user-groups/create/'));
            }

            $data['errors'] = $this->getPostDataAndSaveUserGroup();

            if (empty($data['errors'])) {
                return redirect()->to(base_url('admin/user-groups'));
            }
        }

        return $this->display_view(['Timbreuse\Views\userGroups\save_form'], $data);
    }
    
    /**
     * Retrieves the corresponding user group and display the update form
     *
     * @param  int $id
     * @return string|RedirectResponse
     */
    public function update(int $id, int $parentId = null) : string|RedirectResponse {
        $userGroup = $this->userGroupsModel->find($id);
        $parentUserGroupId = $parentId ?? $userGroup['fk_parent_user_group_id'] ?? null;
        
        if (is_null($userGroup)) {
            return redirect()->to(base_url('admin/user-groups'));
        }

        $userData = $this->userSyncController->getLinkedUserList($id);

        $parentUserGroup = $this->userGroupsModel->find($parentUserGroupId);

        $data = [
            'title' => lang('tim_lang.update_user_group_title'),
            'userGroup' => $userGroup,
            'sessionUserGroup' => session()->get('groupPostData') ?? null,
            'parentUserGroup' => $parentUserGroup,
        ];

        $data += $userData;

        session()->remove('groupPostData');

        if (isset($_POST) && !empty($_POST)) {
            if ($this->checkSelectParent()) {
                return redirect()->to(base_url('admin/user-groups/select-parent/' . $id . '?path=admin/user-groups/update/' . $id . '/'));
            }

            $data['errors'] = $this->getPostDataAndSaveUserGroup();

            if (empty($data['errors'])) {
                return redirect()->to(base_url('admin/user-groups'));
            }
        }

        return $this->display_view(['Timbreuse\Views\userGroups\save_form', 'Common\Views\items_list'], $data);
    }

    /**
     * Display the delete form and delete the corresponding user group
     *
     * @param  int $id
     * @param  int $action
     * @return string|RedirectResponse
     */
    public function delete(int $id, int $action = 0) : string|RedirectResponse {
        $userGroup = $this->userGroupsModel->find($id);

        if (!$userGroup) {
            return redirect()->to(base_url('admin/user-groups'));
        }

        $eventPlanningLinkedCount = $this->eventPlanningsModel->where('fk_user_group_id', $id)->countAllResults();
        $userSyncLinkedCount = $this->userSyncGroupsModel->where('fk_user_group_id', $id)->countAllResults();
        $userGroupLinkedCount = $this->userGroupsModel->where('fk_parent_user_group_id', $id)->countAllResults();
        $canBeDeleted = $eventPlanningLinkedCount === 0 && $userSyncLinkedCount === 0 && $userGroupLinkedCount === 0;

        $data = [
            'title' => lang('tim_lang.delete_event_planning'),
            'userGroup' => $userGroup,
            'canBeDeleted' => $canBeDeleted,
        ];

        switch ($action) {
            case 0:
                return $this->display_view('Timbreuse\Views\userGroups\confirm_delete', $data);

            case 1:
                // In case soft delete is implemented
                break;
            
            case 2:
                if ($canBeDeleted && isset($_POST) && !empty($_POST) && !is_null($_POST['confirmation'])) {
                    $this->userGroupsModel->delete($id, true);
                }
                break;
        }

        return redirect()->to(base_url('admin/user-groups'));
    }
    
    /**
     * Display select user group page
     *
     * @param  int $id
     * @return string
     */
    public function selectUserGroup(?int $id = null) : string {
        $filters = $_GET;

        $data['title'] = lang('tim_lang.user_group_list');
        $data['list_title'] = ucfirst(lang('tim_lang.user_group_list'));

        $data['columns'] = [
            'name' => ucfirst(lang('tim_lang.field_name')),
        ];

        $data['items'] = $this->userGroupsModel->where('id !=', $id)->findAll();

        $data['url_update'] = $filters['path'];

        return $this->display_view('Common\Views\items_list', $data);
    }
    
    /**
     * Retrieves post data from the request and saves the user group information
     *
     * @return array Validation errors encountered during the saving process
     */
    private function getPostDataAndSaveUserGroup() : array {
        $parentUserGroupId = $this->request->getPost('parentUserGroupId');
        $userGroup = [
            'id' => $this->request->getPost('id'),
            'name' => $this->request->getPost('name'),
        ];

        if (!is_null($parentUserGroupId) && !empty($parentUserGroupId)) {
            $userGroup['fk_parent_user_group_id'] = $parentUserGroupId;
        } else {
            $userGroup['fk_parent_user_group_id'] = null;
        }

        $this->userGroupsModel->save($userGroup);
        return $this->userGroupsModel->errors();
    }
    
    /**
     * Check if selectParentButton has been clicked
     * Save post data on true
     *
     * @return bool
     */
    private function checkSelectParent() : bool {
        $selectParentUserGroup = boolval($this->request->getPost('selectParentUserGroupButton'));

        if ($selectParentUserGroup) {
            $this->savePostDataToSession();
        }

        return $selectParentUserGroup;
    }
    
    /**
     * Save post data in the user session
     *
     * @return void
     */
    private function savePostDataToSession() : void {
        session()->set('groupPostData', $this->request->getPost());
    }
}
