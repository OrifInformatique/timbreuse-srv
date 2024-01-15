<?php

/*
 |--------------------------------------------------------------------------
 | ERROR DISPLAY
 |--------------------------------------------------------------------------
 | Don't show ANY in production environments. Instead, let the system catch
 | it and display a generic error message.
 */
# ini_set('display_errors', '0');
ini_set('display_errors', 'Off'); # Workarounds for CodeIgniter4 vulnerable to
# information disclosure when detailed error report is displayed in production
# environment 
# https://github.com/OrifInformatique/ci_packbase_v4/security/dependabot/2
# When we update CodeIgniter to 4.4.3 or upper, change Off to 0

error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT & ~E_USER_NOTICE & ~E_USER_DEPRECATED);

/*
 |--------------------------------------------------------------------------
 | DEBUG MODE
 |--------------------------------------------------------------------------
 | Debug mode is an experimental flag that can allow changes throughout
 | the system. It's not widely used currently, and may not survive
 | release of the framework.
 */
defined('CI_DEBUG') || define('CI_DEBUG', false);
