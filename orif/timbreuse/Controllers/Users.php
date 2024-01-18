<?php

namespace Timbreuse\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\Database\BaseConnection;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\Response;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;
use Timbreuse\Models\UsersModel;
use Timbreuse\Models\AccessTimModel;
use User\Models\User_model;
use Timbreuse\Models\BadgesModel;
use Timbreuse\Models\LogsModel;
use Timbreuse\Models\PlanningsModel;
use Timbreuse\Models\UserPlanningsModel;
use User\Models\User_type_model;

class Users extends BaseController
{
    public function initController(RequestInterface $request,
        ResponseInterface $response, LoggerInterface $logger)
    {
        $this->access_level = config('\User\Config\UserConfig')
             ->access_lvl_admin;
        parent::initController($request, $response, $logger);
        $this->session = \Config\Services::session();

        helper('form');
    }

    public function index(bool $with_deleted = false)
    {
        $model = model(UsersModel::class);
        $data['title'] = lang('tim_lang.users');
        $data['list_title'] = lang('tim_lang.users');

        $data['columns'] = [
            'surname' =>ucfirst(lang('tim_lang.surname')),
            'name' =>ucfirst(lang('tim_lang.name')),
            'username' => ucfirst(lang('user_lang.field_username')),
            'email' => ucfirst(lang('user_lang.field_email')),
            'user_type' => ucfirst(lang('user_lang.field_usertype')),
            'archive' => ucfirst(lang('user_lang.field_user_active')),
        ];
        $data['items'] = $model->get_users($with_deleted);

        foreach($data['items'] as $i => $item) {
            $data['items'][$i]['archive'] =  lang($item['archive'] || $item['date_delete'] ? 'common_lang.no' : 'common_lang.yes');
        }

        $data['primary_key_field']  = 'id_user';
        $data['btn_create_label']  = lang('common_lang.btn_new_m');;
        $data['url_detail'] = "AdminLogs/time_list/";
        $data['url_update'] = 'Users/edit_tim_user/';
        $data['url_delete'] = 'Users/delete_tim_user/';
        $data['with_deleted'] = $with_deleted;
        $data['url_getView'] = 'Users/index/';
        $data['url_create'] = 'Users/create_user';

        return $this->display_view('Common\Views\items_list', $data);
    }

    /**
     * Archive or delete user and tim user
     *
     * @param  mixed $timUserId
     * @param  mixed $action
     * @return void
     */
    public function delete_tim_user($timUserId, $action = 0)
    {
        $userSyncModel = model(UsersModel::class);
        $userModel = model(User_model::class);
        $AccessTimModel = model(AccessTimModel::class);
        $badgeModel = model(BadgesModel::class);
        $logSyncModel = model(LogsModel::class);
        $planningModel = model(PlanningsModel::class);
        $userPlanningModel = model(UserPlanningsModel::class);

        $user = $userSyncModel->get_user($timUserId);

        if (!$user) {
            return redirect()->to(base_url('Users'));
        }

        switch ($action) {
            case 0:
                return $this->display_view('Timbreuse\Views\users\delete_user', $user);
                break;

            case 1:
                is_null($user['id']) ?: $userModel->delete($user['id']);
                $userSyncModel->delete($timUserId);
                break;
            
            case 2:
                $confirmation = $this->request->getPost('confirmation');
                if ($this->request->getMethod() === 'post' && !is_null($confirmation)) {
                    $userPlannings = $userPlanningModel->where('id_user', $timUserId)->findAll();
                    $AccessTimModel->where('id_user', $timUserId)->where('id_ci_user', $user['id'])->delete(null, true);
                    is_null($user['id']) ?: $userModel->delete($user['id'], true);
                    $badgeModel->set_user_id_to_null($timUserId);
                    $logSyncModel->where('id_user', $timUserId)->delete(null, true);
                    $userPlanningModel->where('id_user', $timUserId)->delete(null, true);
                    foreach($userPlannings as $userPlanning) {
                        $planningModel->delete($userPlanning['id_planning'], true);
                    }
                    $userSyncModel->delete($timUserId, true);
                }
                break;
        }

        return redirect()->to(base_url('Users'));
    }
    
    protected function get_badge_id_for_edit_tim_user($timUserId)
    {
        $badgesModel = model(BadgesModel::class);
        $badgeIds['badgeId'] = $badgesModel->get_badges($timUserId);
        if (isset($badgeIds['badgeId'][0]) and is_array($badgeIds['badgeId']))
        {
            $badgeIds['badgeId'] = $badgeIds['badgeId'][0];
            $badgeIds['availableBadges'][1] = '';
        }
        else {
            $badgeIds['badgeId'] = '';
        }
        $badgeIds['availableBadges'][0] = $badgeIds['badgeId'];
        $availableBadges = $badgesModel->get_available_badges();
        if (isset($availableBadges[0]) and is_array($availableBadges)) {
            $badgeIds['availableBadges'] = array_merge(
                    $badgeIds['availableBadges'], $availableBadges);
        }
        return $badgeIds;
    }

    protected function get_user_data_for_edit_tim_user($timUserId)
    {
        $userSyncModel = model(UsersModel::class);

        $userSync = $userSyncModel->get_user($timUserId);

        $badgeIds = $this->get_badge_id_for_edit_tim_user($timUserId);
        $data = array_merge($userSync, $badgeIds);
        return $data;
    }

    /**
     * Display edit user form
     *
     * @param  int $timUserId
     * @return string|Response
     */
    public function edit_tim_user(int $timUserId): string|Response
    {
        $userTypeModel = model(User_type_model::class);

        $data = $this->get_user_data_for_edit_tim_user($timUserId);
        $userTypes = $userTypeModel->orderBy('access_level')->select('id, name')->findAll();
        $data['userTypes'] = array_column($userTypes, 'name', 'id');
        $data['errors'] = [];

        if ($this->request->getMethod() === 'post') {
            $data['errors'] = $this->post_edit_tim_user($timUserId);

            if (empty($data['errors'])) {
                return redirect()->to(base_url('Users'));
            }
        }

        return $this->display_view('Timbreuse\Views\users\edit_tim_user', $data);
    }
    
    /**
     * Get POST data to edit user, user_sync and access_tim_user.
     * Also link a badge if provided in the form
     *
     * @return array
     */
    public function post_edit_tim_user(int $timUserId): array {
        if ($this->validate([
            'badgeId' => "regex_match[/^\d*$/]|cb_available_badge[$timUserId]"
        ])) {
            $userModel = model(User_model::class);
            $userSyncModel = model(UsersModel::class);
            $badgeModel = model(BadgesModel::class);
            $accessTimModel = model(AccessTimModel::class);

            $userId = intval($this->request->getPost('userId'));
            $username = $this->request->getPost('username');
            $email = $this->request->getPost('email');
            $badgeId =  $this->request->getPost('badgeId');
            $userType = $this->request->getPost('fk_user_type');
            $password = $this->request->getPost('password');
            $passwordAgain = $this->request->getPost('password_confirm');

            $updateTimUser = [
                'name' => $this->request->getPost('name'),
                'surname' => $this->request->getPost('surname')
            ];

            $updateBadge = [
                'id_user' => $timUserId
            ];

            if (!empty($username) || !empty($email)) {
                $updateUser = [
                    'username' => $username,
                    'email' => $email,
                ];

                if (!empty($password) || !empty($passwordAgain)) {
                    $updateUser['password'] = $password;
                    $updateUser['password_confirm'] = $passwordAgain;
                }

                if (!empty($userId)) {
                    if ($userId !== $_SESSION['user_id']) {
                        $updateUser['fk_user_type'] = $userType;
                    }
                    $updateUser['id'] = $userId;
    
                    $userModel->save($updateUser);
                } else {
                    $updateUser['fk_user_type'] = $userType;
                    $insertedUserId = $this->create_ci_user($updateUser, $userModel);

                    if ($insertedUserId) {
                        $accessTimModel->add_access($timUserId, $insertedUserId);
                    }
                }
            }

            $badgeModel->set_user_id_to_null($timUserId);

            $userSyncModel->update($timUserId, $updateTimUser);
            $badgeModel->update($badgeId, $updateBadge);

            if ($userModel->errors() == null && $userSyncModel->errors() == null && $badgeModel->errors() == null && $accessTimModel->errors() == null) {
                return [];
            } else {
                $allModelsErrors = [...$userModel->errors(), ...$userSyncModel->errors(), ...$badgeModel->errors()];
                return $allModelsErrors;
            }
        } else {
            return service('validation')->getErrors();
        }
    }
    
    /**
     * Create a website user
     *
     * @param  array $user
     * @param  User_model $userModel
     * @return int|bool
     */
    protected function create_ci_user(array $user, User_model $userModel): int|bool {
        return $userModel->insert($user);
    }
    
    /**
     * Display create user form
     *
     * @return void
     */
    public function create_user() {
        $data = [];
        if ($this->request->getMethod() === 'post') {
            $data['errors'] = $this->post_create_user();

            if (empty($data['errors'])) {
                return redirect()->to(base_url('Users'));
            }
        }

        $userTypeModel = model(User_type_model::class);
        $badgeModel = model(BadgesModel::class);
        $data['availableBadges'] = $badgeModel->get_available_badges();
        $userTypes = $userTypeModel->orderBy('access_level')->select('id, name')->findAll();
        $data['userTypes'] = array_column($userTypes, 'name', 'id');

        return $this->display_view('Timbreuse\Views\users\create_user', $data);
    }
    
    /**
     * Get POST data to create user, user_sync and access_tim_user.
     * Also link a badge if provided in the form
     *
     * @return array
     */
    public function post_create_user(): array {
        $userModel = model(User_model::class);
        $userSyncModel = model(UsersModel::class);
        $badgeModel = model(BadgesModel::class);
        $accessTimModel = model(AccessTimModel::class);

        $timbreuseUser = [
            'name' => $this->request->getPost('name'),
            'surname' => $this->request->getPost('surname'),
        ];

        $badgeId =  $this->request->getPost('badgeId');

        $user = [
            'username' => $this->request->getPost('username'),
            'email' => $this->request->getPost('email'),
            'fk_user_type' => $this->request->getPost('fk_user_type'),
            'password' => $this->request->getPost('password'),
            'password_confirm' => $this->request->getPost('password_confirm'),
        ];

        $isUserValid = $userModel->validate($user);
        $isUserSyncValid = $userSyncModel->validate($timbreuseUser);

        if ($isUserValid && $isUserSyncValid) {
            $userId = $this->create_ci_user($user, $userModel);
            $timbreuseUserId = $userSyncModel->insert($timbreuseUser);

            if ($userId && $timbreuseUserId) {
                $accessTimModel->add_access($timbreuseUserId, $userId);

                if (!empty($badgeId)) {
                    $badgeModel->update($badgeId, ['id_user' => $timbreuseUserId]);
                }
            }
        }

        if ($userModel->errors() == null && $userSyncModel->errors() == null && $badgeModel->errors() == null && $accessTimModel->errors() == null) {
            return [];
        } else {
            $allModelsErrors = [...$userModel->errors(), ...$userSyncModel->errors(), ...$badgeModel->errors(), ...$accessTimModel->errors()];
            return $allModelsErrors;
        }
    }

    /**
     * Reactivate a disabled user.
     *
     * @param int $timUserId = ID of the user to affect
     * @return Response
     */
    public function reactivate_user(int $timUserId): Response
    {
        $userSyncModel = model(UsersModel::class);
        $userModel = model(User_model::class);

        $user = $userSyncModel->get_user($timUserId);

        if (is_null($user)) {
            return redirect()->to(base_url('Users'));
        } else {
            $userSyncModel->update($timUserId, ['date_delete' => null]);
            is_null($user['id']) ?: $userModel->update($user['id'], ['archive' => null]);
            return redirect()->to(base_url('Users/edit_tim_user/' . $timUserId));
        }
    }
}
