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
use Timbreuse\Models\UsersModel;
use Timbreuse\Controllers\PersoLogs;
use CodeIgniter\I18n\Time;

class UserGroups extends BaseController
{
    // todo: manage rights to methods and add link and unlink user from a group
    // Class properties
    private UserGroupsModel $userGroupsModel;
    private UserSyncGroupsModel $userSyncGroupsModel;
    private EventPlanningsModel $eventPlanningsModel;
    private UsersModel $userSyncModel;
    private Users $userSyncController;
    private PersoLogs $persoLogsController;

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
        $this->userGroupsModel = new UserGroupsModel();
        $this->userSyncGroupsModel = new UserSyncGroupsModel();
        $this->eventPlanningsModel = new EventPlanningsModel();
        $this->userSyncModel = new UsersModel();

        // Load required controllers
        $this->userSyncController = new Users();
        $this->persoLogsController = new PersoLogs();
    }
    
    /**
     * Display the user group list
     *
     * @return string
     */
    public function index() : string {
        $data['title'] = lang('tim_lang.user_group_list');

        $userGroups = $this->userGroupsModel->findAll();

        $userGroups = array_map(function($userGroup) {
            if (is_null($userGroup['fk_parent_user_group_id'])) {
                $userGroup['class'] = 'bg-light';
            }

            $userGroup['updateUrl'] = base_url("admin/user-groups/update/{$userGroup['id']}");
            $userGroup['deleteUrl'] = base_url("admin/user-groups/delete/{$userGroup['id']}");

            return $userGroup;
        }, $userGroups);

        $data['userGroups'] = $this->formatForListView($userGroups);

        $data['createUrl'] = base_url('admin/user-groups/create');
        $data['updateUrl'] = base_url('admin/user-groups/update'); 

        return $this->display_view('Timbreuse\Views\userGroups\list', $data);
    }
    
    /**
     * Get all parents recursively
     *
     * @param  mixed $groupId
     * @return array
     */
    private function getParents(?int $groupId): array|null {
        if (is_null($groupId)) {
            return null;
        }

        $allParentGroups = [];

        $parentGroup = $this->userGroupsModel->find($groupId);

        if ($parentGroup && !is_null($parentGroup['fk_parent_user_group_id'])) {
            $parents = $this->getParents($parentGroup['fk_parent_user_group_id']);
            
            foreach($parents as $parent) {
                array_push($allParentGroups, $parent);
            }
        }

        array_push($allParentGroups, $parentGroup);
    
        return $allParentGroups;
    }
    
    /**
     * Format parent and child hierarchy as a breadcrumb
     *
     * @param  array $group
     * @param  array $parentGroups
     * @return string
     */
    private function formatBreadCrumb(array $group, array $parentGroups) : string {
        $groupBreadCrumb = '';
        $arrow = ' <i class="bi bi-arrow-right"></i> ';

        foreach ($parentGroups as $i => $parentGroup) {
            if ($i > 0) {
                $groupBreadCrumb .= $arrow;
            }

            $groupBreadCrumb .= esc($parentGroup['name']);
        }

        $groupBreadCrumb .= $arrow . esc($group['name']);

        return $groupBreadCrumb;
    }
        
    /**
     * Display user groups by corresponding user id
     *
     * @param  mixed $timUserId
     * @return string
     */
    public function displayByUserId(?int $timUserId = null) : string {
        helper('UtilityFunctions');

        if (is_null($timUserId)) {
            $timUserId = get_tim_user_id();
        }

        if (is_admin()) {
            $data['createUrl'] = base_url("user-groups/select/$timUserId");
        }

        $user = $this->userSyncModel->find($timUserId);

        $data['title'] = lang('tim_lang.title_user_group_of', [
            'firstname' => $user['name'],
            'lastname' => $user['surname']
        ]);

        $userGroups = $this->userGroupsModel
            ->select('user_group.id, fk_parent_user_group_id, name')
            ->join('user_sync_group', 'fk_user_group_id = user_group.id')
            ->where('fk_user_sync_id', $timUserId)
            ->findAll();

        $userGroups = array_map(function($userGroup) use ($timUserId) {
            $parentGroups = $this->getParents($userGroup['fk_parent_user_group_id']);

            if (!is_null($parentGroups)) {
                $userGroup['name'] = $this->formatBreadCrumb($userGroup, $parentGroups);
            }

            if (is_admin()) {
                $userGroup['deleteUrl'] = base_url("admin/user-groups/{$userGroup['id']}/unlink-user/$timUserId");
            }

            return $userGroup;
        }, $userGroups);

        $data['userGroups'] = $userGroups;    

        $data['buttons'] = $this->persoLogsController->get_buttons_for_log_views(Time::today(), 'day', $timUserId)['buttons'];

        return $this->display_view([
            'Timbreuse\Views\period_menu',
            'Timbreuse\Views\userGroups\list'
        ], $data);
    }
        
    /**
     * Display page to link groups to a user
     *
     * @param  int $timUserId
     * @return string
     */
    public function selectGroupsLinkToUser(int $timUserId) : string {
        $user = $this->userSyncModel->find($timUserId);
        $data['title'] = lang('tim_lang.title_add_groups_to', [
            'firstname' => $user['name'],
            'lastname' => $user['surname']
        ]);
        $linkedUserGroups = $this->userGroupsModel
            ->select('user_group.id, fk_parent_user_group_id, name')
            ->join('user_sync_group', 'fk_user_group_id = user_group.id')
            ->where('fk_user_sync_id', $timUserId)
            ->findAll();

        $allUserGroups = $this->userGroupsModel->findAll();

        $allUserGroups = array_map(function($userGroup) use ($linkedUserGroups, $timUserId) {
            if (in_array($userGroup, $linkedUserGroups)) {
                $userGroup['class'] = 'bg-secondary';
            } else {
                $userGroup['addUrl'] = base_url("admin/user-groups/{$userGroup['id']}/link-user/$timUserId");
            }

            return $userGroup;
        }, $allUserGroups);

        $data['userGroups'] = $this->formatForListView($allUserGroups);
        $data['buttons'] = $this->persoLogsController->get_buttons_for_log_views(Time::today(), 'day', $timUserId)['buttons'];

        return $this->display_view([
            'Timbreuse\Views\period_menu',
            'Timbreuse\Views\userGroups\list'
        ], $data);
    }

    /**
     * Format and sort user group array for display
     *
     * @param  array $userGroups
     * @return array
     */
    private function formatForListView(array $userGroups) : array {        
        // Sort the new array by parent-child relationship
        usort($userGroups, function($a, $b) {
            if ($a['fk_parent_user_group_id'] == $b['id']) {
                return 1;
            } elseif ($a['id'] == $b['fk_parent_user_group_id']) {
                return -1;
            } else {
                return 0;
            }
        });

        $userGroups = $this->displayHierarchyRecursive($userGroups);

        return $userGroups;
    }
    
    /**
     * Add spaces and chevron to children groups to display hierarchy
     *
     * @param  array $array
     * @param  ?string $parentId
     * @param  int $depth
     * @return array
     */
    private function displayHierarchyRecursive(array $array, ?string $parentId = null, int $depth = 0) : array {
        $result = [];
    
        foreach ($array as $key => $item) {
            if ($item['fk_parent_user_group_id'] === $parentId) {
                $prefix = str_repeat('&nbsp;', $depth * 3);
    
                // Add chevron icon for child items
                if ($depth > 0) {
                    $prefix .= str_repeat('<i class="bi bi-chevron-right"></i>', $depth) . ' ';
                }
    
                $item['name'] = $prefix . esc($item['name']);
                $result[] = $item;
    
                // Recursively process children
                $children = $this->displayHierarchyRecursive($array, $item['id'], $depth + 1);
                $result = array_merge($result, $children);
    
                // Remove processed children from the array
                unset($array[$key]);
            }
        }
    
        return $result;
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

        $data['route'] = $filters['path'];
        $data['title'] = lang('tim_lang.select_user_group');
        $data['list_title'] = ucfirst(lang('tim_lang.select_user_group'));

        $data['columns'] = [
            'name' => ucfirst(lang('tim_lang.field_name')),
        ];

        $data['items'] = $this->userGroupsModel->where('id !=', $id)->findAll();

        $data['url_update'] = $filters['path'];

        return $this->display_view(['Timbreuse\Views\common\return_button', 'Common\Views\items_list'], $data);
    }
    
    /**
     * Retrieves post data from the request and saves the user group information
     *
     * @return array Validation errors encountered during the saving process
     */
    private function getPostDataAndSaveUserGroup() : array {
        $parentUserGroupId = $this->request->getPost('parentUserGroupId');
        $id = $this->request->getPost('id');
        $userGroup = [
            'id' => $id == 0 ? null : $id,
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
