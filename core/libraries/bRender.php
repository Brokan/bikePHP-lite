<?php
/**
 * bikePHP 0.x framework system file
 * Render template with params
 * 
 * Version history:
 * 1.0.0 (2014-10-25) - Created library
 *
 * @copyright Eduard Brokan, 2014
 * @version 1.0.0 (2014-10-25)
 */
class bRender{
    
    /**
     * Additional load files
     * @var Array
     */
    private static $cssFiles = array();
    private static $jsFiles = array();
    private static $jsScript = array();
    
    /**
     * Render template of current module
     */
    public static function render($template, $moduleLocation, $params=array()){
        if(empty($moduleLocation)){
            bDebug::debug("Can't find module path on location: ".$moduleLocation);            
            return false;
        }
        if(!$location = self::getTemplateLocation($template, $moduleLocation)){            
            bDebug::debug("Can't find template - ".$template.' On location: '.$location);
            return false;
        }        
        return self::renderInternal($location,$params);
    }
        
    /**
     * Render setted layout
     */
    public static function renderLayout($layout, $theme, $params=array(), $return = false){
        if(!$location = self::getLayoutLocation($layout, $theme)){
            bDebug::debug("Can't find layout - ".$layout);
            return false;
        }
        return self::renderInternal($location, $params, $return);  
    }
    
    /**
     * Renders a view file.
     * This method includes the view file as a PHP script
     * and captures the display result if required.
     * @param string $_viewFile_ view file
     * @param array $_data_ data to be extracted and made available to the view file
     * @param boolean $_return_ whether the rendering result should be returned as a string
     * @return string the rendering result. Null if the rendering result is not required.
     */
    public static function renderInternal($_viewFile_, $_data_=null, $_return_=true){
        // we use special variable names here to avoid conflict when extracting data
        $data = null;
        if(is_array($_data_)){
            extract($_data_,EXTR_PREFIX_SAME,'data');
        }else{
            $data=$_data_;
        }
        if($_return_){
            ob_start();
            ob_implicit_flush(false);
            require($_viewFile_);
            return ob_get_clean();
        }else{
            require($_viewFile_);
        }
    }
    
    /**
     * Return layout location
     */
    private static function getLayoutLocation($layout, $theme){
        $location =  bTheme::getThemesPath() . $theme. '/layouts/' . $layout ;
        if(file_exists($location)) {
            return $location;               
        }
        $location =  bCore::getGlobalsPath() . 'layouts/' . $layout ;
        if(file_exists($location)) {
            return $location;               
        }
        return false;
    }
    
    /**
     * Return template location
     */
    private static function getTemplateLocation($template, $moduleLocation){
        $location = $moduleLocation . 'templates/'.$template.'.php';        
        if(file_exists($location)) {
            return $location;               
        }        
        return false;
    }
    
    /**
     * Add CSS file
     * $param String $fileName CSS file name
     * $param String $version CSS file version
     * $param Int $weight Order Nr of CSS file in CSS file list
     * $param Bool $external If file is external (http://othersite.com/css.css), the use true
     * $param String $path CSS file path, if empty start search from module path, than project, than global     
     */
    public static function addCSSFile($fileName, $version='', $weight='99', $external=false, $path=''){
        if(!$external){
            if(!empty($path) && !file_exists($path.$fileName)){
                bDebug::debug("Can't find CSS - ".$fileName.' On location: '.$path);
                return false;
            }        
            $path = $this->getFileLocation($fileName, 'css');
            if(empty($path)){
                return false;
            }
            $path = $this->getDomainCSS().str_replace(bCore::getBasePath(), '', $path);            
        }else{
            $path='';
        }
        if(!empty($version)){
            $fileName.='?version='.$version;
        }
        
        /*Check for same*/
        if(!empty($this->cssFilesOrder[$weight])){
            foreach ($this->cssFilesOrder[$weight] as $key => $rec) {
                if($rec==$path.$fileName){
                    return false;
                }
            }
        }
        $this->cssFilesOrder[$weight][]=$path.$fileName;
        return true;
    }
    
    /**
     * Add JS file
     * $param String $fileName JS file name
     * $param String $version JS file version
     * $param Int $weight Order Nr of JS file in JS file list
     * $param String $module module Name
     */
    public static function addJSFile($fileName, $version='', $weight='99', $module=''){
        $path = self::getFileLocation($fileName, 'js', $module);
        if(empty($path)){
            bDebug::debugError("Can't find JS file ".$fileName.".js location for module ".$module);
            return false;
        }
        
        if(!empty($version)){
            $fileName.='?version='.$version;        
        }
        
        /*Check for same*/        
        if(!empty(self::$jsFiles[$weight])){
            self::$jsFiles[$weight] = array();
        }
        self::$jsFiles[$weight][]=$path.$fileName;
        return true;
    }
    
    /**
     * Add Java Script
     * $param String $script Java Script     
     * $param Int $weight Order Nr of JS file in JS file list     
     */
    public static function addJSScripts($script, $weight='99'){           
        if(!empty(self::$jsScript[$weight])){
            self::$jsScript[$weight] = array();
        }
        $this->$jsScript[$weight][]=$script;
        return true;
    }
    
    /**
     * Check and get path to file
     * @param String $fileName
     * @param String $type
     * @param String $module
     * @return string|boolean Path to file or false if not found
     */
    public static function getFileLocation($fileName, $type, $module){
        $modulePath = bSystem::getModuleLocation($module);
        $location =  $modulePath . $type.'/';
        //Check for file in module
        if(file_exists($location.$fileName)) {
            return $location;
        }
        //Check for file in theme
        $location =  bTheme::getThemesPath() . bTheme::getTheme(). '/'.$type.'/' ;
        if(file_exists($location. $fileName)) {
            return $location;               
        }
        //Check for file in globals
        $location =  bCore::getGlobalsPath() . $type.'/' ;
        if(file_exists($location.$fileName)) {
            return $location;               
        }        
        bDebug::debug("Can't find ".$type." file - ".$fileName);
        return false;
    }
}