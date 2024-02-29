<?php

namespace Timbreuse\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\Database\BaseConnection;
use CodeIgniter\HTTP\RedirectResponse;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;
use \CodeIgniter\Validation\ValidationInterface;
use Timbreuse\Models\UserGroupsModel;

use function PHPSTORM_META\map;

class UserGroups extends BaseController
{
    // Class properties
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
        $this->userGroupsModel = new UserGroupsModel();
    }
    
    /**
     * Display the user group list
     *
     * @return string
     */
    public function index() : string {
        $data['title'] = lang('tim_lang.userGroupList');
        $data['list_title'] = ucfirst(lang('tim_lang.userGroupList'));

        $data['columns'] = [
            'userGroupName' => ucfirst(lang('tim_lang.fieldName')),
            'parentUserGroupName' => ucfirst(lang('tim_lang.fieldGroupParentName')),
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
            'title' => lang('tim_lang.createUserGroupTitle'),
            'userGroup' => null,
            'parentUserGroup' => $parentUserGroup,
        ];

        if (isset($_POST) && !empty($_POST)) {
            if ($this->checkSelectParent()) {
                return redirect()->to(base_url('admin/user-groups/select-parent'));
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

        if (is_null($parentId) && !is_null($userGroup['fk_user_group_id'])) {
            $parentUserGroup = $this->userGroupsModel->find($userGroup['fk_user_group_id']);
        } else if (!is_null($parentId) && is_null($userGroup['fk_user_group_id'])) {
            $parentUserGroup = $this->userGroupsModel->find($parentId);
        } else {
            $parentUserGroup = null;
        }

        $data = [
            'title' => lang('tim_lang.updateUserGroupTitle'),
            'userGroup' => $userGroup,
            'parentUserGroup' => $parentUserGroup,
        ];

        if (isset($_POST) && !empty($_POST)) {
            if ($this->checkSelectParent()) {
                return redirect()->to(base_url('admin/user-groups/select-parent/' . $id ?? ''));
            }

            $data['errors'] = $this->getPostDataAndSaveUserGroup();

            if (empty($data['errors'])) {
                return redirect()->to(base_url('admin/user-groups'));
            }
        }

        return $this->display_view(['Timbreuse\Views\userGroups\save_form'], $data);
    }
    
    /**
     * Display select parent user group page
     *
     * @param  int $id
     * @return string
     */
    public function selectParent(?int $id = null) : string {
        $pathToUserGroupForm = 'admin/user-groups/';

        if (is_null($id)) {
            $pathToUserGroupForm = "{$pathToUserGroupForm}create/";
        } else {
            $pathToUserGroupForm = "{$pathToUserGroupForm}update/{$id}/";
        }

        $data['title'] = lang('tim_lang.userGroupList');
        $data['list_title'] = ucfirst(lang('tim_lang.userGroupList'));

        $data['columns'] = [
            'name' => ucfirst(lang('tim_lang.fieldName')),
        ];

        $data['items'] = $this->userGroupsModel->where('id !=', $id)->findAll();

        $data['url_update'] = base_url($pathToUserGroupForm);

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
            $userGroup['fk_user_group_id'] = $parentUserGroupId;
        } else {
            $userGroup['fk_user_group_id'] = null;
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
