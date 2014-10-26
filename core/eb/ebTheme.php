<?php
/**
 * EBFramework 0.x framework system file
 * Theme using
 * 
 * Version history:
 * 1.0.0 (2014-10-25) - Created library
 *
 * @copyright Eduard Brokan, 2014
 * @version 1.0.0 (2014-10-25)
 */
class ebTheme{
    
    /**
     * Current URL theme to use
     * @var String 
     */
    private static $theme="default";
    
    /**
     * Set theme from config file
     */
    public static function setThemeFromConfig(){
        $domain = ebUrl::getDomain();
        $subdomain = ebUrl::getSubdomain();
        $config = ebConfiguration::getConfiguration();
        if(empty($config['themes'][$domain])){
            $domain = 'default';
        }
        if(empty($config['themes'][$domain][$subdomain])){
            $subdomain = 'default';
        }
        if(!empty($config['themes'][$domain][$subdomain])){
            $theme = $config['themes'][$domain][$subdomain];
            if(self::checkTheme($theme)){
                self::setTheme($theme);
                return $theme;
            }
            ebDebug::debugError("Theme folder not exist");
        }
        ebDebug::debugError("Can't find theme configuration");
        return false;
    }
    
    /**
     * Set theme
     * @param Sring $theme name of theme
     */
    public static function setTheme($theme){
        if(self::checkTheme($theme)){
            self::$theme = $theme;
        }
    }
    
    /**
     * Check is them folder exist
     * @param String $theme  name of theme
     * @return Boolean true if theme file exist or false if not exist
     */
    public static function checkTheme($theme){
        return (is_dir(PATH_THEMES.$theme));
    }
    
    /**
     * Get current theme
     * @return string Theme name
     */
    public static function getTheme(){
        return self::$theme;
    }
}