<?php

namespace Timbreuse\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;
use Timbreuse\Models\PlanningsModel;
use Timbreuse\Models\AccessTimModel;
use Timbreuse\Models\UsersModel;
use CodeIgniter\Model;
use Timbreuse\Controllers\PersoLogs;

use CodeIgniter\I18n\Time;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\HTTP\Response;

class Plannings extends BaseController
{
    use ResponseTrait; # API Response Trait
    # to rename, common it is confus, 
    # here common is beetween create and edit

    private PersoLogs $persoLogsController;

    protected function get_common_rules(): array
    {
        $rules['dateEnd'] = 'permit_empty';
        $rules['dueHoursMonday'] = 'greater_than_equal_to[0]'
            . '|less_than_equal_to[10]|required';
        $rules['dueMinutesMonday'] = 'greater_than_equal_to[0]'
            . '|less_than_equal_to[59]|required';
        $rules['dueHoursTuesday'] = 'greater_than_equal_to[0]'
            . '|less_than_equal_to[10]|required';
        $rules['dueMinutesTuesday'] = 'greater_than_equal_to[0]'
            . '|less_than_equal_to[59]|required';
        $rules['dueHoursWednesday'] = 'greater_than_equal_to[0]'
            . '|less_than_equal_to[10]|required';
        $rules['dueMinutesWednesday'] = 'greater_than_equal_to[0]'
            . '|less_than_equal_to[59]|required';
        $rules['dueHoursThursday'] = 'greater_than_equal_to[0]'
            . '|less_than_equal_to[10]|required';
        $rules['dueMinutesThursday'] = 'greater_than_equal_to[0]'
            . '|less_than_equal_to[59]|required';
        $rules['dueHoursFriday'] = 'greater_than_equal_to[0]'
            . '|less_than_equal_to[10]|required';
        $rules['dueMinutesFriday'] = 'greater_than_equal_to[0]'
            . '|less_than_equal_to[59]|required';
        $rules['offeredHoursMonday'] = 'greater_than_equal_to[0]'
            . '|less_than_equal_to[10]|required';
        $rules['offeredMinutesMonday'] = 'greater_than_equal_to[0]'
            . '|less_than_equal_to[59]|required';
        $rules['offeredHoursTuesday'] = 'greater_than_equal_to[0]'
            . '|less_than_equal_to[10]|required';
        $rules['offeredMinutesTuesday'] = 'greater_than_equal_to[0]'
            . '|less_than_equal_to[59]|required';
        $rules['offeredHoursWednesday'] = 'greater_than_equal_to[0]'
            . '|less_than_equal_to[10]|required';
        $rules['offeredMinutesWednesday'] = 'greater_than_equal_to[0]'
            . '|less_than_equal_to[59]|required';
        $rules['offeredHoursThursday'] = 'greater_than_equal_to[0]'
            . '|less_than_equal_to[10]|required';
        $rules['offeredMinutesThursday'] = 'greater_than_equal_to[0]'
            . '|less_than_equal_to[59]|required';
        $rules['offeredHoursFriday'] = 'greater_than_equal_to[0]'
            . '|less_than_equal_to[10]|required';
        $rules['offeredMinutesFriday'] = 'greater_than_equal_to[0]'
            . '|less_than_equal_to[59]|required';
        return $rules;
    }

    protected function get_edit_rules(int $planningId): array
    {
        $rules['planningId'] = 'required|integer';
        $model = model(PlanningsModel::class);
        $timUserId = $model->get_tim_user_id($planningId);
        if ($timUserId) {
            $rules['dateBegin'] 
                = 'required|valid_date|cb_before_date[{dateEnd}]|'
                . "cb_available_date_begin[$timUserId, {dateEnd}, "
                . '{planningId}]';
        }
        $rules = array_merge($rules, $this->get_common_rules());
        return $rules;
    }

    protected function get_create_rules(): array
    {
        $rules['timUserId'] = 'required|integer';
        $rules['dateBegin'] = 'required|valid_date|cb_before_date[{dateEnd}]'
            .'|cb_available_date_begin[{timUserId}, {dateEnd}]';
        $rules = array_merge($rules, $this->get_common_rules());
        return $rules;
    }

    protected function get_restore_rules(): array
    {
        $rules['planningId'] = 'required|integer|cb_restore_planning';
        return $rules;
    }

    public function initController(RequestInterface $request,
        ResponseInterface $response, LoggerInterface $logger): void
    {
        $this->access_level = config('\User\Config\UserConfig')
             ->access_lvl_registered;
        parent::initController($request, $response, $logger);
        $this->session = \Config\Services::session();
        $this->persoLogsController = new PersoLogs();
    }


    public function index(): string|Response
    {
        return redirect()->to(current_url() . '/' . 'get_plannings_list/');
    }

    # to rename, is also use to create
    private function get_label_for_edit_planning(): array
    {

        $data['monday'] = ucfirst(lang('tim_lang.monday'));
        $data['tuesday'] = ucfirst(lang('tim_lang.tuesday'));
        $data['wednesday'] = ucfirst(lang('tim_lang.wednesday'));
        $data['thursday'] = ucfirst(lang('tim_lang.thursday'));
        $data['friday'] = ucfirst(lang('tim_lang.friday'));
        $data['dueTime'] = ucfirst(lang('tim_lang.dueTime'));
        $data['offeredTime'] = ucfirst(lang('tim_lang.offeredTime'));
        $data['cancel'] = ucfirst(lang('common_lang.btn_cancel'));
        $data['save'] = ucfirst(lang('common_lang.btn_save'));
        $data['dateBegin'] = ucfirst(lang('tim_lang.dateBegin'));
        $data['dateEnd'] = lang('tim_lang.dateEnd');
        $data['title'] = ucfirst(lang('tim_lang.title'));
        return $data;

    }

    protected function is_admin(): bool
    {
        helper('UtilityFunctions');
        return is_admin();
    }

    protected function get_ci_user_id(): ?int
    {
        helper('UtilityFunctions');
        return get_ci_user_id();
    }

    protected function get_tim_user_id(?int $planningId=null): ?int
    {
        if (is_null($planningId)) {
            helper('UtilityFunctions');
            return get_tim_user_id();
        }
        $model = model(PlanningsModel::class);
        return $model->get_tim_user_id($planningId);
    }

    public function create_planning(?int $timUserId=null): string|Response
    {
        $timUserId = $timUserId ?? $this->get_tim_user_id(); 
        if (($this->request->getMethod() === 'post')
            and ($this->validate($this->get_create_rules()))) {
            return $this->post_create_planning(
                    'get_cancel_link_for_create_planning');
        }
        $model = model(PlanningsModel::class);
        if (!$this->is_access($timUserId)) {
            return $this->display_unauthorize();
        }
        $data = $this->get_data_for_create_planning($timUserId);
        return view('Timbreuse\Views\planning\edit_planning.php', $data);
    }

    protected function get_data_for_copy_planning(int $planningId): array
    {
        $timUserId = $this->get_tim_user_id($planningId); 
        return $this->get_data_for_create_or_copy_planning($planningId,
            $timUserId, 'get_cancel_link_for_copy_planning');
    }

    protected function get_data_for_create_or_copy_planning(int $planningId,
        int $timUserId, string $cancelLinkFunc): array
    {
        $model = model(PlanningsModel::class);
        $data = $this->get_planning_hours_minutes_or_old_post($planningId,
            $model);
        $data = array_merge($data, $this->get_begin_end_dates_or_old_post(
                $planningId, $model));
        // add here merge with title
        $data['planningTitle'] =  $this->get_title_or_old_post($planningId);
        $data['h3title'] = ucfirst(sprintf(lang('tim_lang.titleNewPlanning'),
            $this->get_tim_user_name($timUserId)));
        $data['title'] = $data['h3title'];
        $data['labels'] = $this->get_label_for_edit_planning();
        $data['action'] = '';
        $data['timUserId'] = $timUserId;
        $data['cancelLink'] = call_user_func_array(
                array($this, $cancelLinkFunc), array($timUserId));
        return $data;
    }

    protected function get_data_for_create_planning(int $timUserId): array
    {
        $planningId = $this ->get_default_planning_id();
        return $this->get_data_for_create_or_copy_planning($planningId,
            $timUserId, 'get_cancel_link_for_create_planning');
    }

    protected function post_create_planning(
        string $cancelLinkFunc):string|Response
    {
        $post = $this->request->getPost();
        if (!$this->is_access($post['timUserId'])) {
            return $this->display_unauthorize();
        }
        $model = model(PlanningsModel::class);
        $formatedTimeArray = $this->format_form_time_array($post);
        $datesArray = $this->format_form_dates($post);
        if (isset($post['timUserId'])) {
            $model->insert_planning_times_and_dates($post['timUserId'],
                $formatedTimeArray, $datesArray);
        }
        $url  = call_user_func_array(
                array($this, $cancelLinkFunc), array($post['timUserId']));
        return redirect()->to($url);
    }

    public function copy_planning(?int $planningId=null): string|Response
    {
        if (($this->request->is('post'))
            and ($this->validate($this->get_create_rules())))
        {
            return $this->post_create_planning(
                    'get_cancel_link_for_copy_planning');
        }
        $timUserId = $this->get_tim_user_id($planningId); 
        $model = model(PlanningsModel::class);
        if (!$this->is_access($timUserId)) {
            return $this->display_unauthorize();
        }
        $data = $this->get_data_for_copy_planning($planningId);
        return view('Timbreuse\Views\planning\edit_planning.php', $data);
    }

    protected function get_tim_user_name(int $timUserId): string
    {
        $userModel = model(UsersModel::class);
        $timUserId = $timUserId ?? $this->get_tim_user_id();
        $data = $userModel->get_names($timUserId);
        return "$data[surname] $data[name]";
    }

    public function edit_user_planning(?int $planningId=null): string|Response
    {
        if (($this->request->getMethod() === 'post')
                and ($this->validate($this->get_edit_rules($planningId)))) {
            return $this->post_edit_planning();
        }
        $planningId = $this->request->getPost('planningId') ?? $planningId;
        if (is_null($planningId)) {
            return $this->display_unauthorize();
        }
        $model = model(PlanningsModel::class);
        if (!$this->is_access_planning($planningId)) {
            return $this->display_unauthorize();
        }
        $data = $this->get_data_for_edit_planning($planningId, $model);
        return view('Timbreuse\Views\planning\edit_planning.php', $data);
    }

    public function edit_planning(?int $planningId=null): string|Response
    {
        if ($planningId == $this->get_default_planning_id()) {

            # $url = current_url() . '/../../../DefaultPlannings/edit_planning';
            $url = current_url() . '/../../../DefaultPlannings';
            return redirect()->to($url);
        }
        return $this->edit_user_planning($planningId);
    }

    /**
     * common between planning standard for user and the default planning
    **/
    protected function get_common_data_for_edit_planning(int $planningId,
        Model $model): array
    {
        $data = $this->get_planning_hours_minutes_or_old_post($planningId,
            $model);
        $data['h3title'] = ucfirst(sprintf(lang('tim_lang.titlePlanning'),
            $model->get_tim_user_names($planningId)));
        $data['title'] = $data['h3title'];
        $data['labels'] = $this->get_label_for_edit_planning();
        $data['planningId'] = $planningId;
        $data['action'] = '';
        $data['cancelLink'] = $this->get_cancel_link_for_edit_planning(
                $planningId);
        $data['planningTitle'] = $model->get_title($planningId);
        return $data;
    }

    // to rename, is also use after redirect when validate post
    protected function get_cancel_link_for_create_planning(
            ?int $timUserId=null): string
    {
        if ($timUserId === $this->get_tim_user_id()) {
            return current_url() . '/../get_plannings_list';
        }
        return current_url() . "/../../get_plannings_list/$timUserId";
    }

    protected function get_cancel_link_for_copy_planning(
            ?int $timUserId=null): string
    {
        if ($timUserId === $this->get_tim_user_id()) {
            return current_url() . '/../../get_plannings_list';
        }
        return current_url() . "/../../get_plannings_list/$timUserId";
    }

    # to delete
    protected function get_redirect_link_for_create_planning(
            ?int $timUserId=null): string
    {
        if ($timUserId === $this->get_tim_user_id()) {
            return '../get_plannings_list/';
        }
        return "../../get_plannings_list/$timUserId";
    }

    /**
     * hide the id if the user "check" himself
     **/
    protected function get_link_with_id_or_not(string $txt,
        ?int $timUserId=null): string
    {
        if ($timUserId === $this->get_tim_user_id()) {
            return $txt;
        }
        return  "$txt/$timUserId";
    }

    // to rename, is also use after redirect when validate post
    protected function get_cancel_link_for_edit_planning(
            ?int $planningId=null): string
    {
        if (is_null($planningId)) {
            return  '../get_plannings_list';
        }
        $timUserId = $this->get_tim_user_id($planningId);
        return $this->get_link_with_id_or_not(current_url()
                . '/../../get_plannings_list', $timUserId);
    }

    protected function get_data_for_edit_planning(int $planningId, 
        Model $model): array
    {
        $data = $this->get_common_data_for_edit_planning($planningId, $model);
        $data = array_merge($data, $this->get_begin_end_dates_or_old_post(
            $planningId, $model));
        return $data;
    }

    protected function get_planning_hours_minutes_or_old_post(int $planningId,
        Model $model): array
    {
        if ($this->request->is('post')) {
            return $this->format_post_old_times();
        } else {
            return $model->get_planning_hours_minutes($planningId);
        }
    }

    /**
        * get title planning from post or from the model
    */
    protected function get_title_or_old_post(int $planningId): string
    {
        if ($this->request->is('post')) {
            return $this->request->getPost('title');
        } else {
            $model = model(PlanningsModel::class);
            return $model->get_title($planningId);
        }
    }

    /**
        * get dates from post or from the model
    */
    protected function get_begin_end_dates_or_old_post(int $planningId,
        Model $model): array
    {
        if ($this->request->is('post')) {
            return $this->format_post_old_dates();
        } else {
            return $model->get_begin_end_dates($planningId, true);
        }
    }

    protected function format_post_old_dates(): array
    {
        $post = $this->request->getPost();
        $data = array();
        $data['date_begin'] = $post['dateBegin'];
        $data['date_end'] = $post['dateEnd'];
        return $data;
    }

    protected function format_post_old_times(): array
    {
        $names = $this->get_array_for_format_post_old();
        $post = $this->request->getPost();
        $data = array();
        foreach ($names as $key => $value) {
            if (isset($data[$value]['hour'])) {
                $data[$value]['minute'] = $post[$key];
            } else {
                $data[$value]['hour'] = $post[$key];
            }
        }
        return $data;
    }

    protected function get_array_for_format_post_old(): array
    {
        $names = array();
        $names['dueHoursMonday'] = 'due_time_monday';
        $names['dueMinutesMonday'] = 'due_time_monday';
        $names['dueHoursTuesday'] = 'due_time_tuesday';
        $names['dueMinutesTuesday'] = 'due_time_tuesday';
        $names['dueHoursWednesday'] = 'due_time_wednesday';
        $names['dueMinutesWednesday'] = 'due_time_wednesday';
        $names['dueHoursThursday'] = 'due_time_thursday';
        $names['dueMinutesThursday'] = 'due_time_thursday';
        $names['dueHoursFriday'] = 'due_time_friday';
        $names['dueMinutesFriday'] = 'due_time_friday';
        $names['offeredHoursMonday'] = 'offered_time_monday';
        $names['offeredMinutesMonday'] = 'offered_time_monday';
        $names['offeredHoursTuesday'] = 'offered_time_tuesday';
        $names['offeredMinutesTuesday'] = 'offered_time_tuesday';
        $names['offeredHoursWednesday'] = 'offered_time_wednesday';
        $names['offeredMinutesWednesday'] = 'offered_time_wednesday';
        $names['offeredHoursThursday'] = 'offered_time_thursday';
        $names['offeredMinutesThursday'] = 'offered_time_thursday';
        $names['offeredHoursFriday'] = 'offered_time_friday';
        $names['offeredMinutesFriday'] = 'offered_time_friday';
        return $names;
    }

    protected function get_due_time_key(): array
    {
        $keys[0] = 'due_time_monday';
        $keys[1] = 'due_time_tuesday';
        $keys[2] = 'due_time_wednesday';
        $keys[3] = 'due_time_thursday';
        $keys[4] = 'due_time_friday';
        return $keys;
    }

    protected function filter_by_due_time_key(array $arr): array
    {
        $keys = $this->get_due_time_key();
        $returnArray = array();
        foreach ($keys as $key) {
            $returnArray[$key] = $arr[$key];
        }
        return $returnArray;
    }



    protected function post_edit_planning(): string|Response
    {
        $model = model(PlanningsModel::class);
        $post = $this->request->getPost();
        if (!$this->is_access_planning($post['planningId'])) {
            return $this->display_unauthorize();
        }
        $formatedTimeArray = $this->format_form_time_array($post);
        $datesArray = $this->format_form_dates($post);
        if (isset($post['planningId'])) {
            $model->update_planning_times_and_dates($post['planningId'],
                $formatedTimeArray, $datesArray);
        }
        $url = $this->get_cancel_link_for_edit_planning($post['planningId']);
        return redirect()->to($url);
    }

    protected function format_form_dates(array $formArray): array
    {
        $formatedArray = array();
        $formatedArray['date_begin'] = $formArray['dateBegin'] ?? null;
        $formatedArray['date_end'] = $formArray['dateEnd'] ?? null;
        $formatedArray['title'] = $formArray['title'] ?? null;
        $formatedArray['date_begin'] = $formatedArray['date_begin'] !== '' ?
                $formatedArray['date_begin']: null;
        $formatedArray['date_end'] = $formatedArray['date_end'] !== '' ?
                $formatedArray['date_end']: null;
        $formatedArray['title'] = $formatedArray['title'] !== '' ?
                $formatedArray['title']: null;

        return $formatedArray;
    }


    protected function format_form_time_array(array $formArray): array
    {
        $names = $this->get_array_for_format_post_old();
        $formatedArray = array();
        foreach ($names as $key => $value) {
            if (isset($formatedArray[$value])) {
                $formatedArray[$value] .= ':' . $formArray[$key];
            } else {
                $formatedArray[$value] = $formArray[$key];
            }
        }
        return $formatedArray;

    }

    protected function display_unauthorize(): string
    {
        return $this->display_view('\User\errors\403error');
    }

    protected function get_link_with_id_or_not_withDeleted(int $timUserId,
            string $txt, bool $withDeleted): string
    {
        if ($timUserId === $this->get_tim_user_id()) {
            return "$txt/$withDeleted";
        }
        return  "$txt/$timUserId/$withDeleted";

    }

    protected function get_user_data_for_plannings_list(int $timUserId,
            bool $withDeleted): array
    {
        $data['list_title'] = ucfirst(sprintf(lang('tim_lang.titleList'),
                $this->get_tim_user_name($timUserId)));
        $model = model(PlanningsModel::class);
        $data['items'] = $model->get_data_list_user_planning($timUserId,
                $withDeleted);
        $data['url_create'] = $this->get_link_with_id_or_not(
                'Plannings/create_planning', $timUserId);
        /* $data['buttons'][0]['link'] =
                "../../AdminLogs/time_list/$timUserId";
        $data['buttons'][0]['label'] = ucfirst(lang('tim_lang.back')); */
        $data['url_getView'] =
                "Plannings/get_plannings_list/$timUserId/$withDeleted";
        $data['url_duplicate'] = 'Plannings/copy_planning/';
        return $data;

    }

    protected function get_no_user_data_for_plannings_list(
            bool $withDeleted): array
    {
        $data['columns'] = [
            'date_begin' =>ucfirst(lang('tim_lang.dateBegin')),
            'date_end' =>ucfirst(lang('tim_lang.dateEnd')),
            'title' => ucfirst(lang('tim_lang.title')),
            'due_time' =>ucfirst(lang('tim_lang.planning')),
            'rate' => ucfirst(lang('tim_lang.rate')),
        ];
        $data['url_update'] = 'Plannings/edit_planning/';
        $data['url_delete'] = 'Plannings/delete_planning/';
        $data['url_restore'] = 'Plannings/restore_planning/';
        $data['primary_key_field'] = 'id_planning';
        $data['with_deleted'] = $withDeleted;
        $data['display_deleted_label'] = lang('tim_lang.showDeletedPlanning');
        $data['deleted_field'] = 'date_delete';
        
        return $data;
    }

    protected function get_data_for_plannings_list(int $timUserId,
            bool $withDeleted): array
    {
        $data = $this->get_user_data_for_plannings_list($timUserId,
                $withDeleted);
        $data = array_merge($data,
                $this->get_no_user_data_for_plannings_list($withDeleted));
        return $data;
    }

    protected function is_access(?int $timUserId): bool
    {
        if ($this->is_admin()) {
            return true;
        }
        $accessModel = model(AccessTimModel::class);
        $isAccess = $accessModel->is_access($this->get_ci_user_id(),
                $timUserId);
        return $isAccess;
    }

    protected function is_access_planning(?int $planningId=null): bool
    {
        if ($this->is_admin()) {
            return true;
        } else if (is_null($planningId)) {
            return false;
        }
        $planningModel = model(PlanningsModel::class);
        $isAccess = $planningModel->is_access_tim_user(
                $this->get_tim_user_id(), $planningId);
        return $isAccess;
    }

    public function get_plannings_list(?int $timUserId=null,
            ?bool $withDeleted=false): string
    {
        $timUserId = $timUserId ?? $this->get_tim_user_id();
        $data['period'] = 'day';
        $data['buttons'] = $this->persoLogsController->get_buttons_for_log_views(Time::today(), $data['period'], $timUserId)['buttons'];

        //dd(current_url(), $data['buttons']);

        if (!$this->is_access($timUserId)) {
            return $this->display_unauthorize();
        }

        $data = array_merge($data, $this->get_data_for_plannings_list($timUserId, $withDeleted));

        // check if the user check himself and show return button if not himself
        if ($timUserId === $this->get_tim_user_id()) {
            return $this->display_view(['Timbreuse\Views\period_menu', 'Common\Views\items_list'], $data);
        }

        return $this->display_view(['Timbreuse\Views\period_menu',
                    'Common\Views\items_list'], $data);
    }

    protected function get_default_planning_id(): int
    {
        $model = model(PlanningsModel::class);
        return $model->get_default_planning_id();
    }

    public function delete_planning(?int $planningId=null): string|Response
    {
        $model = model(PlanningsModel::class);
        $purge = $model->is_deleted($planningId);
        if ($this->request->is('post')) {
            return $this->post_delete_planning($purge);
        }
        if (!$this->is_access_planning($planningId)) {
            return $this->display_unauthorize();
        }
        $data['id'] = $planningId;
        $data['h3title'] = ucfirst(sprintf(lang(
                'tim_lang.titleConfirmDeletePlanning'), $planningId));
        $data['title'] = $data['h3title'];
        if (!$purge) {
            $data['text'] = ucfirst(lang('tim_lang.confirmDeletePlanning'));
        } else {
            $data['text'] = ucfirst(
                lang('tim_lang.confirmPurgeDeletePlanning'));
        }
        $data['link'] = '';
        $data['cancel_link'] = $this->get_cancel_link_for_edit_planning(
                $planningId);
        return $this->display_view(['Timbreuse\Views\confirm_delete_form.php'],
                $data);
    }

    public function restore_planning(?int $planningId=null): string|Response
    {
        if (($this->request->is('post')) and ($this->validate(
                $this->get_restore_rules()))) {
            return $this->post_restore_planning();
        }
        $post = $this->request->getPost();
        $planningId = $planningId ?? $post['planningId'];
        if (!$this->is_access_planning($planningId)) {
            return $this->display_unauthorize();
        }
        $data['ids']['planningId'] = $planningId;
        $data['h3title'] = ucfirst(sprintf(lang(
                'tim_lang.titleConfirmRestorePlanning'), $planningId));
        $data['title'] = $data['h3title'];
        $data['text'] = ucfirst(lang('tim_lang.confirmRestorePlanning'));
        $data['link'] = '';
        $data['cancel_link'] = $this->get_cancel_link_for_edit_planning(
                $planningId);
        $data['label_button'] = lang('tim_lang.restore');
        return $this->display_view(['Timbreuse\Views\confirm_form.php'],
                $data);
    }

    protected function post_delete_planning(
        ?bool $purge=false): string|Response
    {
        $post = $this->request->getPost();
        if (!$this->is_access_planning($post['id'])) {
            return $this->display_unauthorize();
        }
        $url = $this->get_cancel_link_for_edit_planning($post['id']);
        $model = model(PlanningsModel::class);
        if (!$purge) {
            $model->delete($post['id'], $purge);
        } else {
            $model->delete_planning_and_user_planning($post['id']);
        }
        return redirect()->to($url);
    }

    protected function post_restore_planning(): string|Response
    {
        $post = $this->request->getPost();
        if (!$this->is_access_planning($post['planningId'])) {
            return $this->display_unauthorize();
        }
        $model = model(PlanningsModel::class);
        $post = $this->request->getPost();
        $updateArray['date_delete'] = null;
        $model->onlyDeleted()->update($post['planningId'], $updateArray);
        $url = $this->get_cancel_link_for_edit_planning($post['planningId']);
        return redirect()->to($url);
    }

    public function get_rate(): string|Response
    {
        if (!$this->request->is('post')) {
            return $this->display_unauthorize();
        }
        $post = $this->request->getPost();
        $planning = $this->format_form_time_array($post);
        $duePlanning = $this->filter_by_due_time_key($planning);
        $model = model(PlanningsModel::class);
        $data['rate'] = $model->get_rate_by_planning_array($duePlanning);
        return $this->respond(json_encode($data));
    }

    public function test()
    {
        # return  view('Timbreuse\Views\planning\edit_planning_style');
        # return $this->setResponseFormat('json')->respond(file_get_contents(ROOTPATH. 'orif/Timbreuse/Views/planning/edit_planning.css'));
        # header('Location:'.ROOTPATH. 'orif/Timbreuse/Views/planning/edit_planning.css'); 
        # header('Content-Type: text/css');
        # return file_get_contents(ROOTPATH. 'orif/Timbreuse/Views/planning/edit_planning.css');
        # readfile
    }





}
