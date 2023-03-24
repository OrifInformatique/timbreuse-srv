<?php

namespace Timbreuse\Validation;
use Timbreuse\Models\BadgesModel;
use Timbreuse\Models\UsersModel;

class TimbreuseRules
{
    public function cb_available_badge($badgeId) : bool
    {
        $badgesModel = model(BadgesModel::class);
        $availableBadges = $badgesModel->get_available_badges();
        $empty_badge['id_badge'] = '';
        array_push($availableBadges, $empty_badge);

        foreach ($availableBadges as $availableBadge) {
            # reprendre ici lundi 27!!!!
            if ($badgeId == $availableBadge['id_badge']){
                return true;
            }
        }
        return false;
    }

    public function cb_available_user($userId) : bool
    {
        $model = model(badgesModel::class);
        $availableUsers = $model->get_available_users_info();
        $empty_user['id_user'] = '';
        array_push($availableUsers, $empty_user);

        foreach ($availableUsers as $availableUser) {
            if ($userId == $availableUser['id_user']){
                return true;
            }
        }
        return false;
    }
}
