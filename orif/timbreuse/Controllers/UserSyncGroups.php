<?php

namespace Timbreuse\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\RedirectResponse;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;
use Timbreuse\Models\UserSyncGroupsModel;
use Timbreuse\Controllers\Users;
use Timbreuse\Models\UserGroupsModel;

class UserSyncGroups extends BaseController
{
    // Class properties
    private UserSyncGroupsModel $userSyncGroupsModel;
    private Users $userSyncController;
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
        $this->userSyncGroupsModel = new UserSyncGroupsModel();
        $this->userGroupsModel = new UserGroupsModel();

        // Load required controllers
        $this->userSyncController = new Users();
    }

    public function linkUserList(int $groupId) : string {
        $groupName = $this->getGroupName($groupId);
        $data['route'] = "admin/user-groups/update/{$groupId}";
        $data['columns'] = [
            'name' => ucfirst(lang('tim_lang.name')),
            'surname' => ucfirst(lang('tim_lang.surname')),
            'link' => ucfirst(lang('tim_lang.linked_to_group')),
        ];

        $users = $this->userSyncController->getUsersAndGroupLink();

        $data['items'] = array_map(function($user) use ($groupName) {
            $linkedGroupNames = explode(',', $user['user_group_name']);
            return [
                'id_user' => $user['id_user'],
                'name' => $user['name'],
                'surname' => $user['surname'],
                'link' => in_array($groupName, $linkedGroupNames) ?
                    lang('common_lang.yes') :
                    lang('common_lang.no'),
            ];
        }, $users);

        $data['primary_key_field']  = 'id_user';
        $data['title'] = lang('tim_lang.title_link_user', ['group_name' => $groupName]);
        $data['list_title'] = ucfirst(lang('tim_lang.title_link_user', ['group_name' => $groupName]));

        $data['url_update'] = "admin/user-groups/{$groupId}/link-user/";
        $data['url_delete'] = "admin/user-groups/{$groupId}/unlink-user/";

        return $this->display_view(['Timbreuse\Views\common\return_button', 'Common\Views\items_list'], $data);
    }
    
    /**
     * Add a link between a group and a user
     *
     * @param  int $groupId
     * @param  int $userId
     * @return RedirectResponse
     */
    public function addLinkUserToGroup(int $groupId, int $userId) : RedirectResponse {
        $this->userSyncGroupsModel->setValidationRule('fk_user_group_id', "cb_is_unique[{$userId}]");
        $this->userSyncGroupsModel->save([
            'fk_user_sync_id' => $userId,
            'fk_user_group_id' => $groupId
        ]);

        return redirect()->to(base_url("admin/user-groups/{$groupId}/link-user"));
    }
    
    /**
     * Delete the link between a group and a user
     *
     * @param  int $groupId
     * @param  int $userId
     * @return RedirectResponse
     */
    public function deleteLinkToGroup(int $groupId, int $userId) : RedirectResponse {
        $this->userSyncGroupsModel->where('fk_user_group_id', $groupId)
            ->where('fk_user_sync_id', $userId)
            ->delete();
        
        return redirect()->to(base_url("admin/user-groups/{$groupId}/link-user"));
    }
    
    /**
     * Retrieve the corresponding group name
     *
     * @param  int $groupId Group ID
     * @return string
     */
    private function getGroupName(int $groupId) : string {
        return $this->userGroupsModel->find($groupId)['name'];
    }
}
