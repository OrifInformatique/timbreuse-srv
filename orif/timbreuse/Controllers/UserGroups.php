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
            'name' => ucfirst(lang('tim_lang.fieldName')),
            'fk_user_group_id' => ucfirst(lang('tim_lang.fieldGroupParentName')),
        ];

        $userGroups = $this->userGroupsModel->findAll();

        //todo: display parent group if has one, also make a page to attribute a parent group

        $data['items'] = array_map(function($userGroup) {
            return [
                'id' => $userGroup['id'],
                'fk_user_group_id' => $userGroup['fk_user_grouop_id'] ?? '-',
                'name' => $userGroup['name']
            ];
        }, $userGroups);

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
    public function create() : string|RedirectResponse {
        $data = [
            'title' => lang('tim_lang.createUserGroupTitle'),
            'userGroup' => null,
        ];

        if (isset($_POST) && !empty($_POST)) {
            $data['errors'] = $this->getPostDataAndSaveUser();

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
    public function update(int $id) : string|RedirectResponse {
        $userGroup = $this->userGroupsModel->find($id);
        
        $data = [
            'title' => lang('tim_lang.updateUserGroupTitle'),
            'userGroup' => $userGroup,
        ];

        if (isset($_POST) && !empty($_POST)) {
            $data['errors'] = $this->getPostDataAndSaveUser();

            if (empty($data['errors'])) {
                return redirect()->to(base_url('admin/user-groups'));
            }
        }

        return $this->display_view(['Timbreuse\Views\userGroups\save_form'], $data);
    }
    
    /**
     * Retrieves post data from the request and saves the user group information
     *
     * @return array Validation errors encountered during the saving process
     */
    private function getPostDataAndSaveUser() : array {
        $userGroup = [
            'id' => $this->request->getPost('id'),
            'name' => $this->request->getPost('name'),
            'fk_user_group_id' => $this->request->getPost('fk_user_group_id') ?? null
        ];

        $this->userGroupsModel->save($userGroup);
        return $this->userGroupsModel->errors();
    }
}
