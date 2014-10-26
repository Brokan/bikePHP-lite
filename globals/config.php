<?php
/**
 * Set definitions of projects
 */
defined('PATH_THEMES') or define('PATH_THEMES',PATH_GENERAL.'themes/');
defined('PATH_CACHE') or define('PATH_CACHE',PATH_GENERAL.'cache/');

/*Additional configuration*/
defined('EB_BEGIN_TIME') or define('EB_BEGIN_TIME', microtime(true));

/*Debug configuration (For development)*/
defined('EB_DEBUG') or define('EB_DEBUG', true);
defined('EB_DEBUG_TO_FILE') or define('EB_DEBUG_TO_FILE', false);
defined('EB_DEBUG_TO_SCREEN') or define('EB_DEBUG_TO_SCREEN', true);

defined('EB_DEBUG_ERROR') or define('EB_DEBUG_ERROR', true);
defined('EB_DEBUG_DATEBASE') or define('EB_DEBUG_DATEBASE', true);

/**
 * Set project general configuration
 */
return array(
    'project' => array(
        'name' => 'ebFramework-lite',
        'author' => '',
        'authorEmail' => '',
    ),
    //default params for Layouts
    'params' => array(
        'theme' => 'default',
        'layout'=>'default.php',
        'layoutHTML'=>'html.php',
        'css' => array(
            'style.css'=>'1.0.0',
        ),
        'js' => array(
            'main.js'=>'1.0.0',
        ),
        'title' => 'ebFramework-lite',
        'description' => 'Simple PHP framework',
        'keywords' => 'PHP, framework, ebFramework-lite',
    ),
    'themes' => array(
        'default' => array(//Domain
            'default' => 'default',//Subdomain => theme
        ),
    ),
    //use this values, to get the CSS, JS from other domens, if empty, use default, current domain
    'domains'=>array(
        'css'=>'',
        'js'=>'',
    ),
    //connections to databases
    'databases' => array(
        'default' => array(
            'type' => 'mysql',                
            'host' => 'localhost',
            'port' => '3306', 
            'database'=>'database',
            'username' => 'root',
            'password' => '',
            'charset' => 'UTF-8',
        ),
    ),
    'defaultLanguge' => 'en',
    'defaultPages' => array(
        'front' => array('module'=>'default', 'action' => 'front', 'params'=>array()),
        'admin' => array('module'=>'admin', 'action' => 'front', 'params'=>array()),
        '404' => array('module'=>'default', 'action' => '404', 'params'=>array()),
        'noaccess' => array('module'=>'default', 'action' => 'noaccess', 'params'=>array()),
    ),
    //url manager
    'urlManager' => array(
        '<module:\w+>/<action:\w+>' => '<module>/<action>',
        '404' => 'foo/404',
        '' => 'foo/front',
    ),    
);