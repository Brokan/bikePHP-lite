<?php

/**
 * Create URL to page
 * @param String $action module/action
 * @param Array $params additional parametrs
 * @return String URL to page 
 */
function setURL($action, $params=array()){    
    return bUrl::createURL($action, $params);     
}

?>