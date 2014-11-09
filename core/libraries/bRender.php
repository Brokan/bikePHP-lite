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
     * Page parameters
     * @var type 
     */
    private static $pageTitle="";
    private static $pageDescription="";
    private static $pageKeywords="";
    
    /**
     * Layouts
     */
    private static $layout = 'default.php';
    private static $layoutHTML = 'html.php';
 
    /**
     * Additional load files
     * @var Array
     */
    private static $cssFiles = array();
    private static $jsFiles = array();
    private static $jsScript = array();
    
    /**
     * Render setted layout
     * @param Array $params
     * @return String content
     */
    public static function renderLayout($params=array()){
        //render layout
        $layout = self::getLayout();
        if(!$location = self::getLayoutLocation($layout)){
            bDebug::debug("Can't find layout - ".$layout);
            return '';
        }
        $content = self::renderInternal($location, $params);
        
        //render layout HTML
        $layoutHMTL = self::getLayoutHTML();
        if(!$location = self::getLayoutLocation($layoutHMTL)){
            bDebug::debug("Can't find layout HTML - ".$layoutHMTL);
            return $content;
        }
        return self::renderInternal($location, self::getLayoutHTMLParams($content), false);
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
            extract($_data_, EXTR_PREFIX_SAME, 'data');
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
     * @param String $layout Layout file name
     * @return string|boolean Path to layout file or false if not found
     */
    private static function getLayoutLocation($layout){
        $location =  bTheme::getThemesPath() . bTheme::getTheme(). '/layouts/' . $layout ;
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
     * Get params for layout HTML rendering
     * @param String $content
     * @return Array
     */
    private static function getLayoutHTMLParams($content){
        return array(
            'content' => $content,
            'cssFiles' => self::getItemsAsString('css'),
            'jsFiles' => self::getItemsAsString('js'),
            'jsScripts' => self::getItemsAsString('jsscripts'),
            'pageTitle' => self::getPageTitle(),
            'pageDescription' => self::getPageDescription(),
            'pageKeywords' => self::getPageKeywords(),
        );
    }
    
    /**
     * Set page title
     * @param String $pageTitle
     */
    public static function setPageTitle($pageTitle){
        self::$pageTitle = $pageTitle;
    }
    
    /**
     * Get title of the page
     * @return String
     */
    public static function getPageTitle(){
        return self::$pageTitle;
    }
    
    /**
     * Set page description
     * @param String $pageDescription
     */
    public static function setPageDescription($pageDescription){
        self::$pageDescription = $pageDescription;
    }
    
    /**
     * Get description of the page
     * @return String
     */
    public static function getPageDescription(){
        return self::$pageDescription;
    }
    
    /**
     * Set page keywords
     * @param String $pageKeywords
     */
    public static function setPageKeywords($pageKeywords){
        self::$pageKeywords = $pageKeywords;
    }
    
    /**
     * Get keywords of the page
     * @return String
     */
    public static function getPageKeywords(){
        return self::$pageKeywords;
    }
    
    /**
     * Set layout of the page
     * @param String $layout
     */
    public static function setLayout($layout){
        self::$layout = $layout;
    }
    
    /**
     * Get layout of the page
     */
    public static function getLayout(){
        return self::$layout;
    }
    
    /**
     * Set layout of the page
     * @param String $layoutHTML
     */
    public static function setLayoutHTML($layoutHTML){
        self::$layoutHTML = $layoutHTML;
    }
    
    /**
     * Get layout of the page
     */
    public static function getLayoutHTML(){
        return self::$layoutHTML;
    }
    
    /**
     * Add CSS file
     * @param String $fileName CSS file name
     * @param String $version CSS file version
     * @param Int $weight Order Nr of CSS file in CSS file list
     * @param Bool $external If file is external (http://othersite.com/css.css), the use true
     * @return Boolena Success or not
     */
    public static function addCSSFile($fileName, $version='', $weight='99', $module='', $external=false){
        $path = $external ? '' : self::getFileLocation($fileName, 'css', $module);
        if(!$external && empty($path)){
            bDebug::debugError("Can't find CSS file ".$fileName.".css location for module ".$module);
            return false;
        }
        
        if(!empty($version)){
            $fileName.='?ver='.$version;
        }
        $css = '<link type="text/css" rel="stylesheet" href="'.$path.$fileName.'" media="all" />';
        
        return self::addElement(self::$cssFiles, $weight, $css);
    }
    
    /**
     * Add JS file
     * $param String $fileName JS file name
     * $param String $version JS file version
     * $param Int $weight Order Nr of JS file in JS file list
     * $param String $module module Name
     */
    public static function addJSFile($fileName, $version='', $weight='99', $module='', $external=false){
        $path = $external ? '' : self::getFileLocation($fileName, 'js', $module);
        if(!$external && empty($path)){
            bDebug::debugError("Can't find JS file ".$fileName.".js location for module ".$module);
            return false;
        }
        
        if(!empty($version)){
            $fileName.='?ver='.$version;        
        }
        
        $js = '<script type="text/javascript" src="'.$path.$fileName.'" ></script>';
        
        return self::addElement(self::$jsFiles, $weight, $js);
    }
    
    /**
     * Add Java Script
     * $param String $script Java Script     
     * $param Int $weight Order Nr of JS file in JS file list     
     */
    public static function addJSScripts($script, $weight='99'){
        return self::addElement(self::$jsScript, $weight, $script);
    }
    
    /**
     * 
     * @param type $item
     * @param type $weight
     * @param type $content
     * @return boolean
     */
    private static function addElement($item, $weight, $content){
        /*Check for same*/
        if(!empty($item[$weight])){
            foreach ($item[$weight] as $rec) {
                if($rec==$content){
                    return false;
                }
            }
        }else{
            $item[$weight] = array();
        }
        //Add content to item
        $item[$weight][]=$content;
        return true;
    }
    
    /**
     * Get URL path from location path
     * @param String $location
     * @param String $type
     * @return String URL of file location
     */
    private static function getFileURL($location, $type){
        return bURL::getFileTypeDomain($type).str_replace(bCore::getBasePath(), '', $location);
    }
    
    /**
     * Check and get path to file
     * @param String $fileName
     * @param String $type
     * @param String $module
     * @return string|boolean Path to file or false if not found
     */
    private static function getFileLocation($fileName, $type, $module){
        $modulePath = bSystem::getModuleLocation($module);
        $location =  $modulePath . $type.'/';
        //Check for file in module
        if(file_exists($location.$fileName)) {
            return self::getFileURL($location, $type);
        }
        //Check for file in theme
        $location =  bTheme::getThemesPath() . bTheme::getTheme(). '/'.$type.'/' ;
        if(file_exists($location. $fileName)) {
            return self::getFileURL($location, $type);
        }
        //Check for file in globals
        $location =  bCore::getGlobalsPath() . $type.'/' ;
        if(file_exists($location.$fileName)) {
            return self::getFileURL($location, $type);
        }        
        bDebug::debug("Can't find ".$type." file - ".$fileName);
        return false;
    }
    
    /**
     * Get items of type as string for rendering
     * @param String $type on of : js/jsscript/css
     */
    private static function getItemsAsString($type){
        $items = self::getTypeItems($type);
        $content = self::arrayToString($items);
        if($type==='jsscripts'){
            return '<script type="text/javascript">'.$content.'</script>';
        }
        return $content;
    }
    
    /**
     * Get list of (js/jsScripts/css)
     * @param String $type on of : js/jsscript/css
     * @return Array
     */
    private static function getTypeItems($type){
        switch ($type) {
            case 'js':
                return self::$jsFiles;
            case 'jsscripts':
                return self::$jsScript;
            case 'css':
                return self::$cssFiles;
        }
        return array();
    }
    
    /**
     * Convert array to string
     * @param Array $items
     * @return string
     */
    private static function arrayToString($items){
        $content = '';
        foreach ($items as $item) {
            if(is_array($item)){
                $content .='
'.self::arrayToString($item);
            }else{
                $content .='
'.$item;
            }
        }
        return $content;
    }
}