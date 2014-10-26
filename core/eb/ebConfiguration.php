<?php
/**
 * EBFramework 0.x framework system file
 * Using for configurate module with saving information in files
 * 
 * Version history:
 * 1.0.0 (2014-10-25) - Created library
 *
 * @copyright Eduard Brokan, 2014
 * @version 1.0.0 (2014-10-25)
 */
class ebConfiguration{
    
    /**
     * Project configuration
     * @var Array 
     */
    private static $configuration=array();
    
    /**
     * Get config file and set configuration
     */
    public static function setConfiguration(){
        //Check is configuration empty, can't reset configuration
        if(!empty(self::$configuration)){
            return self::$configuration;
        }
        
        /*set global configuration*/
        $configPath = PATH_GLOBALS . '/config.php';
        if(file_exists($configPath)) {
            $config=require($configPath);
            if(is_array($config)){
                self::$configuration = $config;
            }else{
                ebDebug::debugError('Config file is not valid');
            }
        }else{
            ebDebug::debugError('No config file found');
        }
    }
    
    /**
     * Get theme config file and set to configuration
     * @param string $theme Name of theme
     */
    public static function setThemeConfiguration($theme){
        /*set theme configuration*/
        $themeConfigPath = PATH_THEMES . '/'.$theme.'/config.php';
        if(file_exists($themeConfigPath)) {
            $config=require($themeConfigPath);
            if(is_array($config)){
                self::$configuration = array_replace_recursive(self::$configuration, $config);
            }else{
                ebDebug::debugError('Theme config file is not valid');
            }
        }else{
            ebDebug::debugError('No theme config file found');
        }
    }
    
    /**
     * Get configuration
     * @return Array configuration of project
     */
    public static function getConfiguration(){
        return self::$configuration;
    }
    
}