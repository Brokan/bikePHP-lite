<?php

bRender::setPageTitle('Title of page');
bRender::setPageDescription('Description of page');
bRender::setPageKeywords('Keywords of page');

/*Set URL rules*/
bURL::setURLRules(array(
    '<module:\w+>/<action:\w+>' => '<module>/<action>',
    '404' => 'foo/404',
    '' => 'foo/front',
));

/**
 * Set page layouts
 */
bRender::setLayout('default.php');
bRender::setLayoutHTML('html.php');
/**
 * Add CSS files to layout render
 */
bRender::addCSSFile('style.css', '1.0.0', '1');
/**
 * Add JS files to layout render
 */
bRender::addJSFile('main.js', '1.0.0', '1');


/**
 * Other configuration
 */
return array();