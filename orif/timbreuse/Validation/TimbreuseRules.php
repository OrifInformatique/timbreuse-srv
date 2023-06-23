<?php

namespace Timbreuse\Validation;
use Timbreuse\Models\BadgesModel;
use Timbreuse\Models\UsersModel;
use Timbreuse\Models\PlanningsModel;
use CodeIgniter\I18n\Time;

class TimbreuseRules
{
    public function cb_available_badge(string $newBadgeId, string $userId,
        array $data, &$error=null) : bool
    {
        
        $badgesModel = model(BadgesModel::class);
        $availableBadges = $badgesModel->get_available_badges();
        $oldBadgeId = $badgesModel->get_badges($userId);
        array_push($availableBadges, '');
        if (isset($oldBadgeId[0])) {
            array_push($availableBadges, $oldBadgeId[0]);
        }
        foreach ($availableBadges as $availableBadge) {
            if ($newBadgeId == $availableBadge){
                return true;
            }
        }
        $error = lang('tim_lang.badge_not_available');
        return false;
    }

    public function cb_available_user(string $newUserId, string $badgeId,
        array $data, &$error=null) : bool
    {
        $badgeModel = model(badgesModel::class);
        $userModel = model(UsersModel::class);
        $availableUsers = $userModel->get_available_users_info();
        $oldUserId = $badgeModel->get_user_id($badgeId);
        $empty_user['id_user'] = '';
        array_push($availableUsers, $empty_user);
        if (isset($oldUserId)){
            array_push($availableUsers, $oldUserId);
        }

        foreach ($availableUsers as $availableUser) {
            if ($newUserId == $availableUser['id_user']){
                return true;
            }
        }
        $error = lang('tim_lang.user_not_available');
        return false;
    }

    # public function cb_available_date_end(string $newDateEnd, string $params,
    #     array $data, ?string &$error): bool
    # {
    #     try {
    #         Time::parse($newDateEnd);
    #     } catch (\Exception $e) {
    #         $error = lang('tim_lang.errorDate');
    #         return false;
    #     }
    #     $error = lang('tim_lang.dateColide');
    #     $params = explode(',', $params);
    #     $timUserId = $params[0];
    #     $newDateBegin = $params[1];
    #     $planningId = $params[2] ?? null;
    #     $period['date_begin'] = $newDateBegin;
    #     $period['date_end'] = $newDateEnd
    #     $model = model(PlanningsModel::class);
    #     return $model->is_available_period($timUserId, $period, $planningId);
    # }

    public function cb_available_date_begin(string $newDateBegin,
        string $params, array $data, ?string &$error): bool
    {
        try {
            Time::parse($newDateBegin);
        } catch (\Exception $e) {
            $error = lang('tim_lang.errorDate');
            return false;
        }
        $error = lang('tim_lang.dateColide');
        $params = explode(',', $params);
        $timUserId = $params[0];
        $newDateEnd = $params[1];
        $planningId = $params[2] ?? null;
        $period['date_begin'] = $newDateBegin;
        $period['date_end'] = $newDateEnd;
        $model = model(PlanningsModel::class);
        return $model->is_available_period($timUserId, $period, $planningId);
    }

    public function cb_before_date(string $dateBegin, string $dateEnd, array 
        $data, &$error=null): bool
    {
        if ((!isset($dateEnd)) or ($dateEnd === '')) {
            return true;
        }
        try {
            Time::parse($dateBegin);
            Time::parse($dateEnd);
        } catch (\Exception $e) {
            $error = lang('tim_lang.errorDate');
            return false;
        }
        
        $error = lang('tim_lang.dateNotBefore');
        # to change
        return $dateBegin < $dateEnd;
    }

    public function cb_restore_planning(string $planningId, &$error=null): bool
    {
        $model = model(PlanningsModel::class);
        $period = $model->get_begin_end_dates($planningId, true);
        $timUserId = $model->get_tim_user_id($planningId);
        $error = lang('tim_lang.dateColideRestore');
        return $model->is_available_period($timUserId, $period, $planningId);
    }

}
