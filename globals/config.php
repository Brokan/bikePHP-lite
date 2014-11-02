<?php

/*Set pathes of modules*/
bTheme::setThemesPath(bCore::getBasePath().'themes/');
bTheme::setThemes(array(
    'default' => array(//Domain
        'default' => 'default',//Subdomain => theme
    ),
));

/*Debug configuration (For development)*/
bDebug::setDebug(true);
bDebug::setDebugToFile(true);
bDebug::setDebugToScreen(true);
bDebug::setDebugError(true);
bDebug::setDebugDatabase(true);
bDebug::setDebugLogPath(bCore::getBasePath().'logs/');

/*Set connections to databases*/
bDB::setConnections(array(
    'default' => array(
        'type' => 'mysql',                
        'host' => 'localhost',
        'port' => '3306', 
        'database'=>'database',
        'username' => 'root',
        'password' => '',
        'charset' => 'UTF-8',
    ),
));

/*Set URL rules*/
bURL::setURLRules(array(
    '<module:\w+>/<action:\w+>' => '<module>/<action>',
    '404' => 'foo/404',
    '' => 'foo/front',
));
/*Set default pages*/
bURL::setDefaultPages(array(
    'front' => array('module'=>'foo', 'action' => 'front', 'params'=>array()),
    '404' => array('module'=>'foo', 'action' => '404', 'params'=>array()),
));
/*Set domain*/
bURL::setDomains(array(
    'css'=>'',
    'js'=>'',
));
//defined('PATH_CACHE') or define('PATH_CACHE', bCore::getBasePath().'cache/');

/**
 * Set project general configuration
 */
return array(
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
        'title' => 'bikePHP-lite',
        'description' => 'Simple PHP framework',
        'keywords' => 'PHP, framework, bikePHP-lite',
    ),
);