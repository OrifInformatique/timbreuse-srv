<?php
/**
 * Config for common module
 *
 * @author      Orif (ViDi,HeMa)
 * @link        https://github.com/OrifInformatique
 * @copyright   Copyright (c), Orif (https://www.orif.ch)
 */

namespace Config;


use User\Controllers\Admin;

class AdminPanelConfig extends \CodeIgniter\Config\BaseConfig
{
    /** Update this array to customize admin pannel tabs for your needs 
     *  Syntax : ['label'=>'tab label','pageLink'=>'tab link']
    */
    public $tabs=[
        ['label'=>'tim_lang.timUsersList', 'title'=>
            'tim_lang.timUsersList', 'pageLink'=>'Users'],
        ['label'=>'tim_lang.user_group_list', 'title'=>
            'tim_lang.user_group_list', 'pageLink'=>'admin/user-groups'],
        ['label'=>'tim_lang.badgesList', 'title'=>
            'tim_lang.badgesList', 'pageLink'=>'Badges'],
        ['label'=>'tim_lang.Defaultplanning', 'title'=>
            'tim_lang.Defaultplanning', 'pageLink'=>'DefaultPlannings'],
        ['label'=>'tim_lang.event_plannings_list', 'title'=>
            'tim_lang.event_plannings_list', 'pageLink'=>'admin/event-plannings'],
        ['label'=>'tim_lang.event_types_list', 'title'=>
            'tim_lang.event_types_list', 'pageLink'=>'admin/event-types'],
    ];
}
