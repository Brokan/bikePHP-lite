<?php
/**
 * EBFramework 1.x framework system file
 * Using for standart file use
 * 
 * Version history:
 * 1.0.0 (2014-10-25) - Created library
 *
 * @copyright Eduard Brokan, 2014
 * @version 1.0.0 (2014-10-25)
 */
class ebFiles{
    
    /**
     * Read file
     * @param String $filePath Path to file
     * @return String Content of the file
     */
     public static function fileRead($filePath){
         if(!file_exists($filePath)){
             return false;
         }        
         return file_get_contents($filePath);
     }
     
     /**
      * Save to file content. Previos content will be truncate. If the file does not exist, attempt to create it.
      * @param String $filePath Path to file
      * @param String $content Content to save
      */
     public static function fileSave($filePath, $content){
        self::saveToFile($filePath, $content, 'w');        
     }
     
     /**
      * Save to end of the file content. If the file does not exist, attempt to create it.
      * @param String $filePath Path to file
      * @param String $content Content to save
      */
     public static function fileSaveToEnd($filePath, $content){
        self::saveToFile($filePath, $content, 'a');
     }

     /**
      * Open (Create if need) file, and save to this file content.
      * @param String $filePath Path to file
      * @param String $content Content to save
      * @param String $type Type of file open
      */
     private static function saveToFile($filePath, $content, $type){
        if(empty($filePath) || empty($content)) return false;
        $dir=substr($filePath, 0, strrpos($filePath, '/'));
        if(!is_dir($dir)){
             mkdir($dir, 0777, true);
        }
        $file = fopen($filePath,$type);
        if($file){
            fwrite($file,$content); 
            fclose($file);
        }
     }
}