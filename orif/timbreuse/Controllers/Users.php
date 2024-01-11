<?php

namespace Timbreuse\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;
use Timbreuse\Models\UsersModel;
use Timbreuse\Models\AccessTimModel;
use User\Models\User_model;
use Timbreuse\Models\BadgesModel;
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
    }

    public function index(bool $with_deleted = false)
    {
        $model = model(UsersModel::class);
        $data['title'] = lang('tim_lang.users');
        $data['list_title'] = ucfirst(lang('tim_lang.timUsers'));

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
            $data['items'][$i]['archive'] =  lang($item['archive'] ? 'common_lang.no' : 'common_lang.yes');
        }

        $data['primary_key_field']  = 'id_user';
        $data['btn_create_label']  = lang('common_lang.btn_new_m');;
        $data['url_detail'] = "AdminLogs/time_list/";
        $data['url_update'] = 'Users/edit_tim_user/';
        $data['url_delete'] = 'Users/delete_tim_user/';
        $data['with_deleted'] = $with_deleted;
        $data['url_getView'] = 'Users/index/';
        $data['url_create'] = 'user/admin/save_user';

        return $this->display_view('Common\Views\items_list', $data);
    }

    protected function get_data_for_delete_tim_user($timUserId)
    {
        $userModel = model(UsersModel::class);
        $userNames = $userModel->get_names($timUserId);
        if (!isset($userNames['name'], $userNames['surname'])){
            $userNames['name'] = '';
            $userNames['surname'] = '';
        }
        $data['h3title'] = sprintf(lang('tim_lang.titleconfirmDeleteTimUser'),
            $userNames['name'], $userNames['surname']);

        $badgeModel = model(BadgesModel::class);
        $badgeId = $badgeModel->get_badges($timUserId);
        if (!isset($badgeId[0])) {
            $badgeId = '';
        } else {
            $badgeId = $badgeId[0];
        }
        $data['text'] = sprintf(lang('tim_lang.confirmDeleteTimUser'),
            $badgeId, '');
        $data['link'] = '';
        $data['cancel_link'] = '..';
        $data['id'] = $timUserId;
        return $data;
    }

    public function delete_tim_user($timUserId=null)
    {
        if ($this->request->getMethod() === 'post') {
            $timUserId = $this->request->getPost('id');
            return $this->delete_timUser_post($timUserId);
        } elseif ($timUserId === null) {
            return $this->display_view('\User\errors\403error');
        }
        $data = $this->get_data_for_delete_tim_user($timUserId);
        return $this->display_view('Timbreuse\Views\confirm_delete_form',
            $data);
    }

    private function delete_timUser_post($timUserId)
    {
        $timUserModel = model(UsersModel::class);
        $badgeModel = model(BadgesModel::class);
        $timUserModel->db->transStart();
        if (!is_null($timUserId)) {
            $badgeModel->set_user_id_to_null($timUserId);
            $timUserModel->delete($timUserId);
        }
        $timUserModel->db->transComplete();
        return redirect()->to(current_url() . '/../..');
    }

    public function ci_users_list($userId)
    {
        $accessTimModel = model(AccessTimModel::class);
        $userModel = model(User_model::class);

        $data['title'] = lang('tim_lang.webUsers');

        $data['list_title'] = sprintf(lang('tim_lang.ci_users_list_title'), $this->get_username($userId));

        $data['columns'] = [
            'id' => lang('tim_lang.id_site'),
            'username' => ucfirst(lang('tim_lang.username')),
            'access' => ucfirst(lang('tim_lang.access')),
        ];

        $data['items'] = $userModel->select('id, username')->orderBy('username')->findall();
        $access = $accessTimModel->select('id_ci_user')->where('id_user=', $userId)->findall();
        $access = array_map(fn ($access) => array_pop($access), $access);

        $data['items'] = array_map(function (array $item) use ($access) {
            $item['access'] = array_search($item['id'], $access) !== false ?
                lang('tim_lang.yes') : lang('tim_lang.no');
            return $item;
        }, $data['items']);
        
        $data['primary_key_field']  = 'id';
        $data['url_update'] = 'Users/form_add_access/' . $userId . '/';
        $data['url_delete'] = 'Users/form_delete_access/' . $userId . '/';

        return $this->display_view('Common\Views\items_list', $data);
    }
    
    protected function get_usernames($userId, $ciUserId)
    {
        $userName = $this->get_username($userId);

        $ciUserName = $this->get_ci_username($ciUserId);
        $data = array();
        $data['userName'] = $userName;
        $data['ciUserName'] = $ciUserName;
        return $data;
    }

    protected function get_username($userId)
    {
        $model = model(UsersModel::class);
        $userName = $model->select('name, surname')->find($userId);
        $userName = $userName['name'].' '.$userName['surname'];
        return $userName;
    }

    protected function get_ci_username($ciUserId)
    {
        $ciModel = model(User_model::class);
        return $ciModel->select('username')->find($ciUserId)['username'];
    }

    public function form_add_access($userId, $ciUserId)
    {
        $userNames = $this->get_usernames($userId, $ciUserId);
        $data = array();
        $data['ids']['userId'] = $userId;
        $data['ids']['ciUserId'] = $ciUserId;
        $data['link'] = '../../post_add_access';
        $data['cancel_link'] = '../../ci_users_list/' . $userId;
        $data['label_button'] = lang('tim_lang.add');
        $data['text'] = sprintf(
            lang('tim_lang.addAccess'),
            $userNames['ciUserName'],
            $userNames['userName']
        );

        return $this->display_view('Timbreuse\Views\confirm_form', $data);
    }

    protected function add_access($userId, $ciUserId)
    {
        $model = model(AccessTimModel::class);
        $data = array();
        $data['id_user'] = $userId;
        $data['id_ci_user'] = $ciUserId;
        $model->save($data);
        return redirect()->to(current_url() . '/../ci_users_list/' . $userId);
    }

    public function post_add_access()
    {
        // Todo: prevent adding multiple link between one site user and a tim user
        return $this->add_access($this->request->getPostGet('userId'), 
                $this->request ->getPostGet('ciUserId'));
    }

    protected function delete_access($userId, $ciUserId)
    {
        $model = model(AccessTimModel::class);
        $data = array();
        $data['id_user'] = $userId;
        $data['id_ci_user'] = $ciUserId;
        $model->where('id_user=', $userId)->where('id_ci_user=', $ciUserId)
            ->delete();
        return redirect()->to(current_url() . '/../ci_users_list/' . $userId);
    }

    public function form_delete_access($userId, $ciUserId)
    {
        $userNames = $this->get_usernames($userId, $ciUserId);
        $data = array();
        $data['ids']['userId'] = $userId;
        $data['ids']['ciUserId'] = $ciUserId;
        $data['link'] = '../../post_delete_access';
        $data['cancel_link'] = '../../ci_users_list/' . $userId;
        $data['label_button'] = lang('tim_lang.delete');
        $data['text'] = sprintf(
            lang('tim_lang.deleteAccess'),
            $userNames['ciUserName'],
            $userNames['userName']
        );
        return $this->display_view('Timbreuse\Views\confirm_form', $data);
    }

    public function post_delete_access()
    {
        return $this->delete_access($this->request->getPostGet('userId'),
                $this->request ->getPostGet('ciUserId'));
    }

    protected function get_label_for_edit_tim_user()
    {
        $labels['nameLabel'] = ucfirst(lang('tim_lang.name'));
        $labels['surnameLabel'] = ucfirst(lang('tim_lang.surname'));
        $labels['siteAccountLabel'] = ucfirst(lang(
                'tim_lang.siteAccountLabel'));
        $labels['backLabel'] = ucfirst(lang('tim_lang.cancel'));
        $labels['modifyLabel'] = ucfirst(lang('common_lang.btn_save'));
        $labels['deleteLabel'] = ucfirst(lang('tim_lang.delete'));
        $labels['badgeIdLabel'] = ucfirst(lang('tim_lang.badgeId'));
        $labels['eraseLabel'] = ucfirst(lang('tim_lang.erase'));
        return $labels;
    }

    protected function get_url_for_edit_tim_user($timUserId)
    {
        $userModel = model(UsersModel::class);
        $urls = $userModel->select('user_sync.id_user, user_sync.name, user_sync.surname, user.id, user.fk_user_type, user.username, user.email')
            ->join('access_tim_user', 'user_sync.id_user = access_tim_user.id_user', 'left')
            ->join('user', 'user.id = access_tim_user.id_ci_user', 'left')
            ->join('user_type', 'user.fk_user_type = user_type.id', 'left')
            ->withDeleted(true)
            ->find($timUserId);
        $urls['editUrl'] = '../edit_tim_user/' . $timUserId;
        $urls['siteAccountUrl'] = '../ci_users_list/'. $timUserId;
        $urls['returnUrl'] = '..';
        $urls['deleteUrl'] = '../delete_tim_user/' . $timUserId;
        return $urls;
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

    protected function get_data_for_edit_tim_user($timUserId)
    {
        $userTypeModel = model(User_type_model::class);
        $data['userTypes'] = $userTypeModel->findAll();
        $data['h3title'] = lang('tim_lang.timUserEdit');
        $labels = $this->get_label_for_edit_tim_user();
        $urls = $this->get_url_for_edit_tim_user($timUserId);
        $badgeIds = $this->get_badge_id_for_edit_tim_user($timUserId);
        $data = array_merge($data, $urls, $labels, $badgeIds);
        return $data;
    }

    public function edit_tim_user(int $timUserId)
    {
        $data = $this->get_data_for_edit_tim_user($timUserId);
        $data['errors'] = [];

        if ($this->request->getMethod() === 'post') {
            if ($this->validate([
                'badgeId' => "regex_match[/^\d*$/]|cb_available_badge[$timUserId]"
            ])) {
                $userModel = model(User_model::class);
                $userSyncModel = model(UsersModel::class);
                $badgeModel = model(BadgesModel::class);
    
                $userId = intval($this->request->getPost('userId'));
                $badgeId =  $this->request->getPost('badgeId');
                $userType = $this->request->getPost('fk_user_type');
                $password = $this->request->getPost('password');
                $passwordAgain = $this->request->getPost('user_password_again');
    
                $updateTimUser = [
                    'name' => $this->request->getPost('name'),
                    'surname' => $this->request->getPost('surname')
                ];
    
                $updateBadge = [
                    'id_user' => $timUserId
                ];
    
                if (!empty($userId)) {
                    $updateUser = [
                        'id' => $userId,
                        'username' => $this->request->getPost('username'),
                        'email' => $this->request->getPost('email'),
                    ];
        
                    if ($userId !== $_SESSION['user_id']) {
                        $updateUser['fk_user_type'] = $userType;
                    }
        
                    if (!empty($password) || !empty($passwordAgain)) {
                        $updateUser['password'] = $password;
                        $updateUser['password_confirm'] = $passwordAgain;
                    }
    
                    $userModel->save($updateUser);
                }
    
                $badgeModel->set_user_id_to_null($timUserId);
    
                $userSyncModel->update($timUserId, $updateTimUser);
                $badgeModel->update($badgeId, $updateBadge);
    
                if ($userModel->errors() == null && $userSyncModel->errors() == null && $badgeModel->errors() == null) {
                    return redirect()->to(base_url('Users'));
                } else {
                    $allModelsErrors = [...$userModel->errors(), ...$userSyncModel->errors(), ...$badgeModel->errors()];
                    $data['errors'] = $allModelsErrors;
                }
            } else {
                $data['errors'] = service('validation')->getErrors();
            }
        }

        return $this->display_view('Timbreuse\Views\users\edit_tim_user', $data);
    }
}
