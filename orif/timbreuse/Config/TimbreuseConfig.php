<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class TimbreuseConfig extends BaseConfig
{
    // Default values to copy when creating a specific user planning
    public $defaultPlanningId = 1;

    public $userGroupNameMinLength = 3;
    public $userGroupNameMaxLength = 45;
    
    public $eventTypeNameMinLength = 3;
    public $eventTypeNameMaxLength = 45;
}
