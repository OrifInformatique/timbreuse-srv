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

    public function index()
    {
        return $this->users_list();
    }

    public function users_list()
    {
        $model = model(UsersModel::class);
        $data['title'] = lang('tim_lang.users');

        # Display a test of the generic "items_list" view (defined in common
        # module)
        $data['list_title'] = ucfirst(lang('tim_lang.timUsers'));

        $data['columns'] = [
            'id_user' =>ucfirst(lang('tim_lang.id')),
            'name' =>ucfirst(lang('tim_lang.name')),
            'surname' =>ucfirst(lang('tim_lang.surname'))
        ];
        $data['items'] = $model->get_users();


        $data['primary_key_field']  = 'id_user';
        // $data['btn_create_label']   = 'Add an item';
        #$data['url_detail'] = "PersoLogs/time_list/";
        $data['url_detail'] = "AdminLogs/time_list/";
        $data['url_update'] = 'Users/edit_tim_user/';
        // $data['url_delete'] = "items_list/delete/";
        // $data['url_create'] = "items_list/create/";
        $this->display_view('Common\Views\items_list', $data);
    }

    public function ci_users_list($userId = 92)
    {
        $model = model(AccessTimModel::class);
        $modelCi = model(User_model::class);
        $data['title'] = lang('tim_lang.webUsers');

        $data['list_title'] = sprintf(
            lang('tim_lang.ci_users_list_title'),
            $this->get_username($userId)
        );

        $data['columns'] = [
            'id' => lang('tim_lang.id_site'),
            'username' => ucfirst(lang('tim_lang.username')),
            'access' => ucfirst(lang('tim_lang.access')),
        ];

        $data['items'] = $modelCi->select('id, username')->findall();
        $access = $model->select('id_ci_user')->where('id_user=', $userId)
            ->findall();

        $access = array_map(function ($access) {
            return array_pop($access);
        }, $access);

        $data['items'] = array_map(function (array $item) use ($access) {
            $item['access'] = array_search($item['id'], $access) !== false ?
                lang('tim_lang.yes') : lang('tim_lang.no');
            return $item;
        }, $data['items']);

        $data['primary_key_field']  = 'id';
        $data['url_update'] = 'Users/form_add_access/' . $userId . '/';
        $data['url_delete'] = 'Users/form_delete_access/' . $userId . '/';
        $this->display_view('Common\Views\items_list', $data);
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
        $data['userId'] = $userId;
        $data['ciUserId'] = $ciUserId;
        $data['link'] = '../../post_add_access';
        $data['cancel_link'] = '../../ci_users_list/' . $userId;
        $data['label_button'] = lang('tim_lang.add');
        $data['text'] = sprintf(
            lang('tim_lang.addAccess'),
            $userNames['ciUserName'],
            $userNames['userName']
        );
        $this->display_view('Timbreuse\Views\confirm_form', $data);
    }

    protected function add_access($userId, $ciUserId)
    {
        $model = model(AccessTimModel::class);
        $data = array();
        $data['id_user'] = $userId;
        $data['id_ci_user'] = $ciUserId;
        $model->save($data);
        return redirect()->to('ci_users_list/' . $userId);
    }

    public function post_add_access()
    {
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
        return redirect()->to('ci_users_list/' . $userId);
    }

    public function form_delete_access($userId, $ciUserId)
    {
        $userNames = $this->get_usernames($userId, $ciUserId);
        $data = array();
        $data['userId'] = $userId;
        $data['ciUserId'] = $ciUserId;
        $data['link'] = '../../post_delete_access';
        $data['cancel_link'] = '../../ci_users_list/' . $userId;
        $data['label_button'] = lang('tim_lang.delete');
        $data['text'] = sprintf(
            lang('tim_lang.deleteAccess'),
            $userNames['ciUserName'],
            $userNames['userName']
        );
        $this->display_view('Timbreuse\Views\confirm_form', $data);
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
        $labels['backLabel'] = ucfirst(lang('tim_lang.back'));
        $labels['modifyLabel'] = ucfirst(lang('tim_lang.modify'));
        $labels['deleteLabel'] = ucfirst(lang('tim_lang.delete'));
        $labels['badgeIdLabel'] = ucfirst(lang('tim_lang.badgeId'));
        $labels['eraseLabel'] = ucfirst(lang('tim_lang.erase'));
        return $labels;
    }

    protected function get_url_for_edit_tim_user($timUserId)
    {
        $userModel = model(UsersModel::class);
        $urls = $userModel->select('id_user, name, surname')->find($timUserId);
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
        $data['h3title'] = lang('tim_lang.timUserEdit');
        $labels = $this->get_label_for_edit_tim_user();
        $urls = $this->get_url_for_edit_tim_user($timUserId);
        $badgeIds = $this->get_badge_id_for_edit_tim_user($timUserId);
        $data = array_merge($data, $urls, $labels, $badgeIds);
        return $data;
    }

    public function edit_tim_user($timUserId)
    {
        /**
        * show the edit formulaire, when is valited go to post_edit… method
        */
        if (($this->request->getMethod() === 'post') and $this->validate([
            'name' => 'required',
            'surname' => 'required',
            'timUserId' => 'required|integer',
            'badgeId' => 'regex_match[/^\d*$/]'
        ])) {
            return $this->post_edit_tim_user();
        }
        $data = $this->get_data_for_edit_tim_user($timUserId);
        $this->display_view('Timbreuse\Views\users\edit_tim_user', $data);
    }

    protected function update_user_and_badge($timUserId, $newBadgeId, $name, 
            $surname)
    {
        $userData['name'] = $name;
        $userData['surname'] = $surname;
        $userModel = model(UsersModel::class);
        $badgeModel = model(BadgesModel::class);
        $userModel->db->transBegin();
        $userModel->update($timUserId, $userData);
        
        if ($badgeModel->deallocate_and_reallocate_badge($timUserId,
                $newBadgeId)) {
            $userModel->db->transCommit();
            return true;
        } else {
            $userModel->db->transRollback();
            return false;
        }
    }

    protected function post_edit_tim_user()
    {
        if ($this->request->getMethod() !== 'post') {
            return $this->display_view('\User\errors\403error');
        }
        $name = $this->request->getPost('name');
        $surname = $this->request->getPost('surname');
        $timUserId = $this->request->getPost('timUserId');
        $newBadgeId = $this->request->getPost('badgeId');
        if($this->update_user_and_badge($timUserId, $newBadgeId, $name, 
                $surname)){
            return redirect()->to('../..');
        } else {
            return redirect()->to('..');
        }
    }

    public function delete_tim_user($timUserId)
    {
        $model = model(UsersModel::class);
        $names = $model->select('name, surname')->find($timUserId);
        $data['text'] = sprintf(lang('tim_lang.confirmDeleteTimUser'),
                $names['name'], $names['surname']);
        $data['link'] = '../confirm_delete_tim_user';
        $data['label_button'] = ucfirst(lang('tim_lang.delete')); 
        $data['title'] = lang('tim_lang.delete');
        $data['cancel_link'] = '../edit_tim_user/' . $timUserId;
        $data['userId'] = $timUserId;
        $data['ciUserId'] = null;
        $this->display_view('Timbreuse\Views\confirm_form', $data);
    }

    public function confirm_delete_tim_user()
    {
        if ($this->request->getMethod() !== 'post') {
            return $this->display_view('\User\errors\403error');
        }
        $model = model(UsersModel::class);
        $timUserId = $this->request->getPost('userId');
        if ($timUserId !== null) {
            $model->delete($timUserId);
        }
        return redirect()->to('../..');
    }
}
