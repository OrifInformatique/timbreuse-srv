<?php


namespace Timbreuse\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;
use Timbreuse\Models\BadgesModel;
use Timbreuse\Models\UsersModel;

use CodeIgniter\I18n\Time;

class Badges extends BaseController
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
        return $this->badges_list($with_deleted);
    }

    public function badges_list(bool $with_deleted = false)
    {
        $model = model(badgesModel::class);
        $data['title'] = lang('tim_lang.badges');

        # Display a test of the generic "items_list" view (defined in common 
        # module)

        $data['list_title'] = ucfirst(lang('tim_lang.badges'));

        $data['columns'] = [
            'id_badge' =>ucfirst(lang('tim_lang.badgeId')),
            'surname' =>ucfirst(lang('tim_lang.surname')),
            'name' =>ucfirst(lang('tim_lang.name')),
            'date_delete' =>ucfirst(lang('user_lang.field_user_active')),
        ];
        $data['items'] = $model->get_badges_and_user_info($with_deleted);

        foreach($data['items'] as $i => $item) {
            $data['items'][$i]['date_delete'] = lang($item['date_delete'] || !$item['name'] ? 'common_lang.no' : 'common_lang.yes');
        }

        $data['primary_key_field']  = 'id_badge';
        # $data['btn_create_label']  = lang('common_lang.btn_new_m');
        # $data['url_detail'] = "AdminLogs/time_list/";
        $data['url_update'] = 'Badges/edit_badge_relation/';
        $data['url_delete'] = 'Badges/delete_badge/';
        $data['with_deleted'] = $with_deleted;
        $data['url_getView'] = 'Badges/badges_list/';
        # $data['url_create'] = "Badges/create_badge";

        return $this->display_view('Common\Views\items_list', $data);
    }

    protected function get_data_for_delete_badge($badgeId)
    {
        $badgesModel = model(badgesModel::class);
        $userInfo = $badgesModel->get_user_info($badgeId);
        $data['h3title'] = sprintf(lang('tim_lang.titleConfirmDeleteBadge')
            , $badgeId);
        if (!isset($userInfo['name'], $userInfo['surname'])) {
            $userInfo['name'] = '';
            $userInfo['surname'] = '';
        }
        $data['text'] = sprintf(lang('tim_lang.confirmDeleteBadge'),
            $userInfo['name'], $userInfo['surname']);

        $data['link'] = '';
        $data['cancel_link'] = '..';
        $data['id'] = $badgeId;
        return $data;
    }

    public function delete_badge($badgeId=null)
    {
        if ($this->request->getMethod() === 'post') {
            $badgeId = $this->request->getPost('id');
            return $this->delete_badge_post($badgeId);
        } elseif ($badgeId === null) {
            return $this->display_view('\User\errors\403error');
        }
        $data = $this->get_data_for_delete_badge($badgeId);
        return $this->display_view('Timbreuse\Views\confirm_delete_form',
            $data);
    }

    private function delete_badge_post($badgeId)
    {
        $badgeModel = model(badgesModel::class);
        $badgeData['id_user'] = NULL;
        $badgeModel->transStart();
        if (!is_null($badgeId)) {
            $badgeModel->update($badgeId, $badgeData);
            $badgeModel->delete($badgeId);
        }
        $badgeModel->transComplete();
        return redirect()->to(current_url() . '/../..');
    }

    public function edit_badge_relation($badgeId=null)
    {
        $post = $this->request->getPost(['timUserId', 'badgeId']);
        $badgeId = is_null($badgeId) ? $post['badgeId'] : $badgeId;
        if (is_null($badgeId)) {
            return $this->display_view('\User\errors\403error');
        }
        if (($this->request->getMethod() === 'post') and $this->validate([
            'timUserId' =>
                "regex_match[/^\d*$/]|cb_available_user[$badgeId]",
            'badgeId' => 'required|integer'
        ])) {
            return $this->post_edit_badge_relation($post);
        }
        $data = $this->get_data_for_edit_badge_relation($badgeId);
        return $this->display_view('Timbreuse\Views\badges\edit_badges',
            $data);
    }

    protected function get_empty_user_info()
    {
        $emptyUser['id_user'] = '';
        $emptyUser['name'] = '';
        $emptyUser['surname'] = '';
        return $emptyUser;
    }

    private function post_edit_badge_relation($post)
    {
        if (is_null($post['badgeId'])) {
            return redirect()->to(current_url() . '/..');
        }
        $badgeData['id_user'] = $post['timUserId'] === '' ? null
            : $post['timUserId'];
        $model = model(badgesModel::class);
        $model->update($post['badgeId'], $badgeData);

        return redirect()->to(current_url() . '/../..');
    }

    public function get_data_for_edit_badge_relation($badgeId)
    {
        $data['badgeId'] = $badgeId;
        # $data['postUrl'] = '../post_edit_badge_relation/' . $badgeId;
        $data['postUrl'] = '';
        $data['returnUrl'] = '..';
        $data['deleteUrl'] = '../delete_badge/' . $badgeId;
        $model = model(badgesModel::class);
        $currentUser = $model->get_user_info($badgeId);
        $data['availableUsers'] = array();
        if (is_array($currentUser)) {
            $data['availableUsers'][0] = $currentUser;
        }
        array_push($data['availableUsers'], $this->get_empty_user_info());
        $userModel = model(UsersModel::class);

        $data['availableUsers'] = array_merge($data['availableUsers'],
            $userModel->get_available_users_info());

        $data['labels']['user'] = ucfirst(lang('tim_lang.timUserRelation'));
        $data['labels']['back'] = ucfirst(lang('common_lang.btn_cancel'));
        $data['labels']['modify'] = ucfirst(lang('common_lang.btn_save'));
        $data['labels']['dealloc'] = ucfirst(lang('tim_lang.dealloc'));
        $data['labels']['delete'] = ucfirst(lang('tim_lang.delete'));
        $data['labels']['erase'] = ucfirst(lang('tim_lang.erase'));
        $data['h3title'] = ucfirst(sprintf(lang('tim_lang.edit_badge'),
            $badgeId));
        return $data;
    }
}

