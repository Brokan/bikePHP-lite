<?php
/**
 * bikePHP 0.x framework system file
 * Make debugs
 * 
 * Version history:
 * 1.0.0 (2014-10-25) - Created library
 *
 * @copyright Eduard Brokan, 2014
 * @version 1.0.0 (2014-10-25)
 */

class bDebug{
    
    /**
     * Constants
     */
    private static $debug = false;
    private static $debugToFile = false;
    private static $debugToScreen = false;
    private static $debugError = false;
    private static $debugDatabase = false;
    private static $debugLogPath;
    
    private static $beginTime;
    
    private function __construct(){
        self::$beginTime = microtime(true);
    }
    
    /**
     * List of log
     * @var Array 
     */
    private static $log = array();
    
    /**
     * Set debug
     * @param boolean $debug
     */
    public static function setDebug($debug) {
        self::$debug = $debug;
    }
    
    /**
     * Set debug to file
     * @param boolean $debug
     */
    public static function setDebugToFile($debug) {
        self::$debugToFile = $debug;
    }
    
    /**
     * Set debug to screen after page is loaded
     * @param boolean $debug
     */
    public static function setDebugToScreen($debug) {
        self::$debugToScreen = $debug;
    }
    
    /**
     * Set debug of error
     * @param boolean $debug
     */
    public static function setDebugError($debug) {
        self::$debugError = $debug;
    }
    
    /**
     * Set debug of database queries
     * @param boolean $debug
     */
    public static function setDebugDatabase($debug) {
        self::$debugDatabase = $debug;
    }
    
    /**
     * Set debug log path, place where debug log need to save
     * @param String $path Path to logs
     */
    public static function setDebugLogPath($path) {
        self::$debugLogPath = $path;
    }
    
    /**
     * Debug events
     * @param String $text
     */	
    public static function debug($text){
        if(!self::$debug){
            return;
        }
        self::processDebug($text);
    }

    /**
     * Debug events
     * @param String $text
     */
    public static function debugError($text){
        if(!self::$debugError){
            return;
        }
        self::processDebug($text);
    }

    /**
     * Database debug events
     * @param String $text
     */
    public static function debugDatabase($text){
        if(!self::$debugDatabase){
            return;
        }
        self::processDebug($text);
    }

    /**
     * Debug processing
     * @param String $text
     */
    private static function processDebug($text){
        $workTime = round(microtime(true)-self::$beginTime,4);            
        $log = ''.date('H:i:s').' - '.$workTime.' : '.$text. ' (Memory usage - '.memory_get_usage().' Memory pick - '.memory_get_peak_usage().');';
        if(self::$debugToScreen){
            self::$log[]=$log;
        }
        if(self::$debugToFile){
            self::saveLog($log);
        }
    }


    /**
     * Save debug log to file
     */
    private static function saveLog($row){         
        $row='
'.$row;
        bFiles::fileSaveToEnd(self::$debugLogPath.date('Y-m-d').'.txt', $row);            
    }      

    /**
     * Show all debug information
     */
    public static function show(){
        if(self::$debugToScreen && !empty(self::$log)){
            echo join('<br/>',self::$log);
        }
    }
}