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

use CodeIgniter\I18n\Time;

class Plannings extends BaseController
{
    protected function get_common_rules(): array
    {
        $rules['dateBegin'] = 'required|valid_date';
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
            $rules['dateEnd'] 
                = "cb_available_date[$timUserId, $planningId]";
        }
        $rules = array_merge($rules, $this->get_common_rules());
        return $rules;
    }

    protected function get_create_rules(int $timUserId): array
    {
        $rules['timUserId'] = 'required|integer';
        $rules['dateEnd'] = "cb_available_date[$timUserId, $planningId]";
        $rules = array_merge($rules, $this->get_common_rules());
        return $rules;
    }


    public function initController(RequestInterface $request,
        ResponseInterface $response, LoggerInterface $logger): void
    {
        $this->access_level = config('\User\Config\UserConfig')
             ->access_lvl_registered;
        # $this->access_level = config('\User\Config\UserConfig')
        #      ->access_lvl_admin;
        parent::initController($request, $response, $logger);
        $this->session = \Config\Services::session();
    }


    public function index()
    {
        return redirect()->to(current_url() . '/' . 'edit_planning/');
    }

    private function get_label_for_edit_planning(): array
    {

        $data['monday'] = ucfirst(lang('tim_lang.monday'));
        $data['tuesday'] = ucfirst(lang('tim_lang.tuesday'));
        $data['wednesday'] = ucfirst(lang('tim_lang.wednesday'));
        $data['thursday'] = ucfirst(lang('tim_lang.thursday'));
        $data['friday'] = ucfirst(lang('tim_lang.friday'));
        $data['dueTime'] = ucfirst(lang('tim_lang.dueTime'));
        $data['offeredTime'] = ucfirst(lang('tim_lang.offeredTime'));
        $data['cancel'] = ucfirst(lang('tim_lang.cancel'));
        $data['save'] = ucfirst(lang('common_lang.btn_save'));
        $data['dateBegin'] = ucfirst(lang('tim_lang.dateBegin'));
        $data['dateEnd'] = lang('tim_lang.dateEnd');
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

    protected function get_tim_user_id(): ?int
    {
        helper('UtilityFunctions');
        return get_tim_user_id();
    }

    public function create_planning(?int $timUserId=null)
    {
        # to do
        if (($this->request->getMethod() === 'post')
                and ($this->validate($this->get_create_rules()))) {
            return $this->post_create_planning();
        }
        $timUserId = $timUserId ?? $this->get_tim_user_id(); 

        $model = model(PlanningsModel::class);
        $accessModel = model(AccessTimModel::class);
        $isAccess = $accessModel->is_access($this->get_ci_user_id(),
                $timUserId);
        if ((!$isAccess) and (!$this->is_admin())) {
            return $this->block_ci_user();
        }
        $data = $this->get_data_for_create_planning($timUserId, $model);
        #  test to remove
        $this->display_view(['Timbreuse\Views\planning\edit_planning.php'],
            $data);
    }

    protected function get_data_for_create_planning(int $timUserId, 
        $model): array
    {
        # to do
        $defaultPlanningId = $this ->get_default_planning_id();
        $data = $this->get_planning_hours_minutes_or_old_post(
                $defaultPlanningId, $model);
        $data = array_merge($data, $this->get_begin_end_dates_or_old_post(
                $defaultPlanningId, $model));
        $data['h3title'] = ucfirst(sprintf(lang('tim_lang.titleNewPlanning'),
            $this->get_tim_user_name($timUserId)));
        $data['title'] = $data['h3title'];

        # to do rename get_label_for_edit_planning
        $data['labels'] = $this->get_label_for_edit_planning();
        $data['action'] = '.';
        $data['timUserId'] = $timUserId;
        return $data;
    }

    protected function post_create_planning()
    {
        $model = model(PlanningsModel::class);
        $post = $this->request->getPost();
        $formatedTimeArray = $this->format_form_time_array($post);
        $datesArray = $this->format_form_dates($post);

        #$mergedArray = array_merge($formatedTimeArray, $datesArray);
        if (isset($post['timUserId'])) {
            $model->insert_planning_times_and_dates($post['timUserId'],
                $formatedTimeArray, $datesArray);
        }
        return redirect()->to(current_url() . '/../../../');
    }

    protected function get_tim_user_name(int $timUserId): string
    {
        $userModel = model(UsersModel::class);
        $timUserId = $timUserId ?? $this->get_tim_user_id();
        $data = $userModel->get_names($timUserId);
        return "$data[surname] $data[name]";
    }



    public function edit_user_planning(?int $planningId=null)
    {
        if (($this->request->getMethod() === 'post')
                and ($this->validate($this->get_edit_rules($planningId)))) {
            return $this->post_edit_planning();
        }
        $planningId = $this->request->getPost('planningId') ?? $planningId;
        if (is_null($planningId)) {
            return $this->block_ci_user();
        }
        $model = model(PlanningsModel::class);
        if ((!$model->is_access_ci_user($this->get_ci_user_id(), $planningId))
                and (!$this->is_admin())) {
            return $this->block_ci_user();
        }
        $data = $this->get_data_for_edit_planning($planningId, $model);
        $this->display_view(['Timbreuse\Views\planning\edit_planning.php'],
            $data);
    }

    public function edit_planning(?int $planningId=null)
    {
        if ($planningId == $this->get_default_planning_id()) {

            $url = current_url() . '/../../../DefaultPlannings/edit_planning';
            return redirect()->to($url);
            //return DefaultPlannings->edit_planning();
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
        $data['action'] = '.';
        return $data;
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
        if ($this->request->getMethod() === 'post') {
            return $this->format_post_old_times();
        } else {
            return $model->get_planning_hours_minutes($planningId);
        }
    }

    /**
        * get dates from post or from the model
    */
    protected function get_begin_end_dates_or_old_post(int $planningId,
        Model $model): array
    {
        if ($this->request->getMethod() === 'post') {
            return $this->format_post_old_dates();
        } else {
            return $model->get_begin_end_dates($planningId);
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



    protected function post_edit_planning()
    {
        $model = model(PlanningsModel::class);
        $post = $this->request->getPost();
        $formatedTimeArray = $this->format_form_time_array($post);
        $datesArray = $this->format_form_dates($post);

        #$mergedArray = array_merge($formatedTimeArray, $datesArray);
        if (isset($post['planningId'])) {
            $model->update_planning_times_and_dates($post['planningId'],
                $formatedTimeArray, $datesArray);
        }
        return redirect()->to(current_url() . '/../../../');
    }

    protected function format_form_dates(array $formArray): array
    {
        $formatedArray = array();
        $formatedArray['date_begin'] = $formArray['dateBegin'] ?? null;
        $formatedArray['date_end'] = $formArray['dateEnd'] ?? null;
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

    protected function block_ci_user()
    {
        return $this->display_view('\User\errors\403error');
    }

    public function plannings_list()
    {
        # to do
        $data['list_title'] = ucfirst(lang('tim_lang.titleList'));

    }

    protected function get_default_planning_id(): int
    {
        return config('\Timbreuse\Config\PlanningConfig')->defaultPlanningId;
    }

}