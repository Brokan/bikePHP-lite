<?php
/**
 * bikePHP 0.x framework system file
 * Get module list, data
 * 
 * Version history:
 * 1.0.0 (2014-10-25) - Created library
 *
 * @copyright Eduard Brokan, 2012
 * @version 1.0.0 (2014-10-25)
 */
class bSystem {

    /**
     * Cache of modules locations
     * @var Array 
     */
    private static $modulesLocations = array();

    /**
     * Get module location by module name
     * @param String $module Module name
     * @return String module path
     */
    public static function getModuleLocation($module){
        if(!empty(self::$modulesLocations[$module])){
            return self::$modulesLocations[$module];
        }
        $dir=bCore::getGlobalsPath().'modules/'.$module.'/';
        if(!file_exists($dir . '/'.$module.'Controller.php')) {
            $dir=bCore::getCorePath().'modules/'.$module.'/';
        }
        return self::$modulesLocations[$module] = $dir;
    }
    
}

?>
