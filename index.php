<?php

/*Path configuration*/
defined('PATH_GENERAL') or define('PATH_GENERAL',dirname(__FILE__).'/');
defined('PATH_CORE') or define('PATH_CORE',PATH_GENERAL.'core/');

require_once(PATH_CORE.'ebCore.php');
$core = new ebCore();

$core->createWebApplication();