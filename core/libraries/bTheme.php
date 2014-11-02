<?php
/**
 * bikePHP 0.x framework system file
 * Theme using
 * 
 * Version history:
 * 1.0.0 (2014-10-25) - Created library
 *
 * @copyright Eduard Brokan, 2014
 * @version 1.0.0 (2014-10-25)
 */
class bTheme{
    
    private static $themesPath;
    
    /**
     * Current theme to use
     * @var String 
     */
    private static $theme="default";
    
    /**
     * Themes list depended by domain and subdomains
     * @var Array 
     */
    private static $themes = array();
    
    /**
     * Set path to themes
     * @param String $path Path to themes
     */
    public static function setThemesPath($path){
        self::$themesPath = $path;
    }
    
    /**
     * Get path to themes
     * @return String Path to themes
     */
    public static function getThemesPath(){
        return self::$themesPath;
    }
    
    /**
     * Set $themes
     * @param Array $themes
     */
    public static function setThemes($themes){
        self::$themes = $themes;
    }
    
    /**
     * Get themes
     * @return Array $themes
     */
    public static function getThemes(){
        return self::$themes;
    }
    
    /**
     * Set theme from config file
     */
    public static function setThemeFromConfig(){
        $domain = bUrl::getDomain();
        $subdomain = bUrl::getSubdomain();
        $themes = self::getThemes();
        if(empty($themes[$domain])){
            $domain = 'default';
        }
        if(empty($themes[$domain][$subdomain])){
            $subdomain = 'default';
        }
        if(!empty($themes[$domain][$subdomain])){
            $theme = $themes[$domain][$subdomain];
            if(self::checkTheme($theme)){
                self::setTheme($theme);
                return $theme;
            }
            bDebug::debugError("Theme folder not exist");
        }
        bDebug::debugError("Can't find theme configuration");
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
        return (is_dir(bTheme::getThemesPath().$theme));
    }
    
    /**
     * Get current theme
     * @return string Theme name
     */
    public static function getTheme(){
        return self::$theme;
    }
}