<?php


namespace Timbreuse\Controllers;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;
use Timbreuse\Models\PlanningsModel;
use Timbreuse\Controllers\Plannings;

use CodeIgniter\I18n\Time;

class DefaultPlannings extends Plannings
{

    # to rename, common it is confus,
    # here common is beetween create and edit
    protected function get_common_rules() : array
    {
        $rules['planningId'] = 'required|integer';
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

    public function initController(RequestInterface $request,
        ResponseInterface $response, LoggerInterface $logger) : void
    {

        $this->access_level = config('\User\Config\UserConfig')
                ->access_lvl_admin;
        get_parent_class(parent::class)::initController($request, $response,
                $logger);
        $this->session = \Config\Services::session();
    }

    public function index()
    {
        return $this->edit_planning();
        # return redirect()->to(current_url() . '/' . 'edit_planning/');
    }

    public function edit_planning($n=null)
    {
        return parent::edit_user_planning($this->get_default_planning_id());

    }

    protected function post_edit_planning()
    {
        $model = model(PlanningsModel::class);
        $post = $this->request->getPost();
        $formatedTimeArray = $this->format_form_time_array($post);

        $model->update($this->get_default_planning_id(), $formatedTimeArray);
        return redirect()->to(current_url() . '/../');
    }

    protected function get_data_for_edit_planning($planningId, $model): array
    {
        $data = $this->get_common_data_for_edit_planning($planningId, $model);
        $data['defaultPlanning'] = true;
        return $data;
    }

    // to rename, is also use after redirect when validate post
    protected function get_cancel_link_for_edit_planning(?int $n=null): string
    {
        return  '..';
    }

}
