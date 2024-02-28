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
        ['label'=>'tim_lang.webUsersList', 'title'=>
            'tim_lang.webUsersList', 'pageLink'=>'user/admin/list_user'],
        ['label'=>'tim_lang.timUsersList', 'title'=>
            'tim_lang.timUsersList', 'pageLink'=>'Users'],
        ['label'=>'tim_lang.userGroupList', 'title'=>
            'tim_lang.userGroupList', 'pageLink'=>'admin/user-groups'],
        ['label'=>'tim_lang.badgesList', 'title'=>
            'tim_lang.badgesList', 'pageLink'=>'Badges'],
        ['label'=>'tim_lang.Defaultplanning', 'title'=>
            'tim_lang.Defaultplanning', 'pageLink'=>'DefaultPlannings'],
    ];
}
