<?php
/**
 * EBFramework 0.x framework system file
 * Render template with params
 * 
 * Version history:
 * 1.0.0 (2014-10-25) - Created library
 *
 * @copyright Eduard Brokan, 2014
 * @version 1.0.0 (2014-10-25)
 */
class ebRender{
    
    /**
     * Render template of current module
     */
    public static function render($template, $moduleLocation, $params=array()){
        if(empty($moduleLocation)){
            ebDebug::debug("Can't find module path on location: ".$moduleLocation);            
            return false;
        }
        if(!$location = self::getTemplateLocation($template, $moduleLocation)){            
            ebDebug::debug("Can't find template - ".$template.' On location: '.$location);
            return false;
        }        
        return self::renderInternal($location,$params);
    }
        
    /**
     * Render setted layout
     */
    public static function renderLayout($layout, $theme, $params=array(), $return = false){
        if(!$location = self::getLayoutLocation($layout, $theme)){
            ebDebug::debug("Can't find layout - ".$layout);
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
        $location =  PATH_THEMES . $theme. '/layouts/' . $layout ;
        if(file_exists($location)) {
            return $location;               
        }
        $location =  PATH_GLOBALS . 'layouts/' . $layout ;
        if(file_exists($location)) {
            return $location;               
        }
        return false;
    }
    
    /**
     * Return template location
     */
    private static function getTemplateLocation($template, $moduleLocation){
        $location = $this->moduleLocation . 'templates/'.$template.'.php';        
        if(file_exists($location)) {
            return $location;               
        }        
        return false;
    }
}