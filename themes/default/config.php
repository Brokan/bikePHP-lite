<?php
/**
 * Set theme configuration
 */

/*Set URL rules*/
bURL::setURLRules(array(
    '<module:\w+>/<action:\w+>' => '<module>/<action>',
    '404' => 'foo/404',
    '' => 'foo/front',
));

return array(
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
);