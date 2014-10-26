<?php
/**
 * EBFramework 0.x framework system file
 * Make debugs
 * 
 * Version history:
 * 1.0.0 (2014-10-25) - Created library
 *
 * @copyright Eduard Brokan, 2014
 * @version 1.0.0 (2014-10-25)
 */

class ebDebug{
                
    /**
     * Debug events
     * @param String $text
     */	
    public static function debug($text){
        if(!EB_DEBUG){
            return;
        }
        self::processDebug($text);
    }

    /**
     * Debug events
     * @param String $text
     */
    public static function debugError($text){
        if(!EB_DEBUG_ERROR) return;
        self::processDebug($text);
    }

    /**
     * Database debug events
     */
    public static function debugDatabase($text){
        if(!EB_DEBUG_DATEBASE) return;
        self::processDebug($text);
    }

    /**
     * Debug processing
     * @param String $text
     */
    private static function processDebug($text){
        $workTime = round(microtime(true)-EB_BEGIN_TIME,4);            
        $log = ''.date('H:i:s').' - '.$workTime.' : '.$text. ' (Memory usage - '.memory_get_usage().' Memory pick - '.memory_get_peak_usage().');';
        if(EB_DEBUG_TO_SCREEN){
            $GLOBALS['debug'][]=$log;
        }
        if(EB_DEBUG_TO_FILE){
            self::saveLog($log);
        }
    }


    /**
     * Save debug log to file
     */
    private static function saveLog($row){         
        $row='
'.$row;
        ebFiles::fileSaveToEnd(PATH_GENERAL.'debug.txt', $row);            
    }      

    /**
     * Show all debug information
     */
    public static function show(){
        if(EB_DEBUG_TO_SCREEN && !empty($GLOBALS['debug'])){
            echo join('<br/>',$GLOBALS['debug']);
        }
    }
}