<?php
/**
 * Set theme configuration
 */
return array(
    'project' => array(
        'name' => 'ebFramework-lite',
        'author' => '',
        'authorEmail' => '',
    ),
    'params' => array(
        'layout'=>'default.php',
        'layoutHTML'=>'html.php',
        'css' => array(
            'style.css'=>'1.0',
        ),
        'js' => array(
            'main.js'=>'1.0',
        ),
    ), 
    /*url manager*/
    'urlManager' => array(  
        '<module:\w+>/<action:\w+>' => '<module>/<action>',
        '404' => 'foo/404',
        '' => 'foo/front',
    ),   
);