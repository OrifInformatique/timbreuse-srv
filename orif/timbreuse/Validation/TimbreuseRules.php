<?php

namespace Timbreuse\Validation;
use Timbreuse\Models\BadgesModel;
use Timbreuse\Models\UsersModel;
use Timbreuse\Models\PlanningsModel;

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
        $model = model(badgesModel::class);
        $availableUsers = $model->get_available_users_info();
        $oldUserId = $model->get_user_id($badgeId);
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

    public function cb_available_date(string $newDateBegin,
        string $params, array $data, &$error=null) : bool
    {
        $params = explode(',', $params);
        $timUserId = $params[0];
        $planningId = $params[1];
        $period['date_begin'] = $data['dateBegin'];
        $period['date_end'] = $data['dateEnd'];
        $model = model(PlanningsModel::class);
        if ($model->is_available_period($timUserId, $period, $planningId)) {
            return true;
        }
        $error = lang('tim_lang.dateColide');
        return false;
    }

}
