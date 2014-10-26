<?php
/**
 * EBFramework 0.x framework system file
 * Get module list, data
 * 
 * Version history:
 * 1.0.0 (2014-10-25) - Created library
 *
 * @copyright Eduard Brokan, 2012
 * @version 1.0.0 (2014-10-25)
 */
class ebSystem {

    /**
     * Get module location by module name
     * @param String $module Module name
     * @return String module path
     */
    public static function getModuleLocation($module){
        $dir=PATH_GLOBALS.'modules/'.$module.'/';
        if(!file_exists($dir . '/'.$module.'Controller.php')) {
            $dir=PATH_CORE.'modules/'.$module.'/';
        }
        return $dir;
    }
    
}

?>
