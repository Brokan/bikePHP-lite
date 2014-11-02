<?php
/**
 * bikePHP 0.x framework system file
 * CORE file
 * 
 * Version history:
 * 1.0.0 (2014-10-25) - Created library
 *
 * @copyright Eduard Brokan, 2012
 * @version 1.0.0 (2014-10-25)
 */
class bCore{
        
    private static $pathBase;
    private static $pathCore;
    private static $pathGlobals;

    /**
     * Construct Core
     * @param String $basePath
     */
    function __construct($basePath){
        
        self::$pathBase = $basePath.'/';
        self::$pathCore = $basePath.'/core/';
        self::$pathGlobals = $basePath.'/globals/';
        
        //Set class autoload
        spl_autoload_register(array($this, 'autoLoad'));
        //Start session
        bSession::start();
        //Get global configuration
        bConfiguration::setConfiguration();
        //Set theme from configuration
        $theme = bTheme::setThemeFromConfig();
        //Set theme configuration
        bConfiguration::setThemeConfiguration($theme);
        //Include global function
        include_once(bCore::getGlobalsPath() . 'global_functions.php');
    }

    /**
     * Auto load class if exist
     * @return framework search and load module
     */
    public static function autoLoad($class){   
        $location = self::checkClassLocation($class);
        if($location){
            include_once($location);
        }            
    }        

    /**
     * Check for class existing in system structure
     * @param string $class Class name
     * @return string Location of class or False if location did'n find
     */        
    public static function checkClassLocation($class){
        $globalsPath = bCore::getGlobalsPath();
        $corePath = bCore::getCorePath();
        //Set list of file class
        $listOfClasses = array(
            $globalsPath . 'modules/' . str_replace('Controller','',$class) . '/' . $class . '.php',
            $globalsPath. 'modules/' . $class . '/libraries/' . $class . '.php',
            $globalsPath . 'libraries/' . $class . '.php',

            $corePath . 'modules/' . str_replace('Controller','',$class) . '/' . $class . '.php',
            $corePath. 'modules/' . $class . '/libraries/' . $class . '.php',
            $corePath . 'libraries/' . $class . '.php',
            $corePath . 'libraries/database/' . $class . '.php',
        );
        
        /*check and return path of module or librarie class*/
        foreach ($listOfClasses as $filePath) {
            if(file_exists($filePath)) {
                return $filePath;
            }
        }
        //No class found
        return false;
    }

    /**
     * Get basic directory path of project
     * @return String base path
     */
    public static function getBasePath(){
        return self::$pathBase;
    }
    
    /**
     * Get core directory path of project
     * @return String core path
     */
    public static function getCorePath(){
        return self::$pathCore;
    }
    
    /**
     * Get globals directory path of project
     * @return String globals directory path
     */
    public static function getGlobalsPath(){
        return self::$pathGlobals;
    }
    
    /**
     * Create web application
     */
    public function createWebApplication(){            
        $controller = new bController();
        $content = $controller->run();
        bDebug::debug('END Web Application');
        echo $content;
        bDebug::show();
    }                
}