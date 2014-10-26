<?php
/**
 * Set definitions
 */
defined('PATH_GLOBALS') or define('PATH_GLOBALS',PATH_GENERAL.'globals/');

/**
 * Start session
 */
session_start();

/**
 * Core class
 */
class ebCore{
        
        /**
         * Construct CORE
         */
        function __construct(){
            //Set class autoload
            spl_autoload_register(array($this, 'autoLoad'));
            //Get global configuration
            ebConfiguration::setConfiguration();
            //Set theme from configuration
            $theme = ebTheme::setThemeFromConfig();
            //Set theme configuration
            ebConfiguration::setThemeConfiguration($theme);
            //Include global function
            include_once(PATH_GLOBALS . 'global_functions.php');
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
            //Set list of file class
            $listOfClasses = array(
                PATH_GLOBALS . 'modules/' . str_replace('Controller','',$class) . '/' . $class . '.php',
                PATH_GLOBALS. '/modules/' . $class . '/libraries/' . $class . '.php',
                PATH_GLOBALS . 'libraries/' . $class . '.php',
                
                PATH_CORE . 'modules/' . str_replace('Controller','',$class) . '/' . $class . '.php',
                PATH_CORE. '/modules/' . $class . '/libraries/' . $class . '.php',
                PATH_CORE . 'eb/' . $class . '.php',
                PATH_CORE . 'eb/database/' . $class . '.php',
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
         * Create web application
         */
        public function createWebApplication(){            
            $controller = new ebController();
            $content = $controller->run();
            ebDebug::debug('END Web Application');
            echo $content;
            ebDebug::show();
        }                
}